<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.btnwhatsapp
 *
 * @copyright   (C) 2026 Hirlei Carlos Pereira de Araújo
 * @license     GNU General Public License version 2 or later
 */
namespace Joomla\Plugin\System\Btnwhatsapp\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Document\HtmlDocument;

final class Btnwhatsapp extends CMSPlugin
{
    protected $autoloadLanguage = true;

    // =========================================================
    // CSS + JS via WebAssetManager
    // =========================================================
    public function onBeforeCompileHead(): void
    {
        $app = Factory::getApplication();

        if (!$app->isClient('site')) {
            return;
        }

        $doc = Factory::getDocument();

        if (!$doc instanceof HtmlDocument) {
            return;
        }

        $wa = $doc->getWebAssetManager();

        // CSS
        if (!$wa->assetExists('style', 'plg.system.btnwhatsapp')) {
            $wa->registerStyle(
                'plg.system.btnwhatsapp',
                'media/plg_system_btnwhatsapp/css/btnwhatsapp.css',
                [],
                ['version' => '3.1.0']
            );
        }
        $wa->useStyle('plg.system.btnwhatsapp');

        // JS
        if (!$wa->assetExists('script', 'plg.system.btnwhatsapp')) {
            $wa->registerScript(
                'plg.system.btnwhatsapp',
                'media/plg_system_btnwhatsapp/js/btnwhatsapp.js',
                [],
                ['version' => '3.1.0', 'defer' => true],
                []
            );
        }
        $wa->useScript('plg.system.btnwhatsapp');
    }

    // =========================================================
    // Render
    // =========================================================
    public function onAfterRender(): void
    {
        $app = Factory::getApplication();

        if (!$app->isClient('site')) {
            return;
        }

        // ── Dispositivo ──────────────────────────────────────
        $ua       = (string) $app->getInput()->server->getString('HTTP_USER_AGENT', '');
        $isMobile = (bool) preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $ua);

        $device = (string) $this->params->get('device', 'all');

        if ($device === 'mobile'  && !$isMobile) return;
        if ($device === 'desktop' &&  $isMobile) return;

        // ── Menu ─────────────────────────────────────────────
        $displayMode   = (string) $this->params->get('display_mode', 'all');
        $menuItems     = (array)  $this->params->get('menu_items', []);
        $menu          = $app->getMenu();
        $active        = $menu->getActive();
        $currentItemId = $active ? (int) $active->id : 0;

        if ($displayMode === 'only'    && !in_array($currentItemId, $menuItems)) return;
        if ($displayMode === 'exclude' &&  in_array($currentItemId, $menuItems)) return;

        // ── Telefone ─────────────────────────────────────────
        $phone = preg_replace('/\D+/', '', (string) $this->params->get('phone', ''));

        if ($phone === '') return;

        // ── Horário de atendimento ────────────────────────────
        $scheduleEnabled = (int) $this->params->get('schedule_enabled', 0);
        $isOpen          = true;
        $offlineMsg      = '';

        if ($scheduleEnabled) {
            $timezone = (string) $this->params->get('schedule_timezone', 'America/Sao_Paulo');

            try {
                $tz  = new \DateTimeZone($timezone);
                $now = new \DateTime('now', $tz);
            } catch (\Exception $e) {
                $now = new \DateTime('now');
            }

            // 0=Sun, 1=Mon … 6=Sat  (PHP w)
            $dayMap = [
                0 => 'sun',
                1 => 'mon',
                2 => 'tue',
                3 => 'wed',
                4 => 'thu',
                5 => 'fri',
                6 => 'sat',
            ];

            $dayKey     = $dayMap[(int) $now->format('w')];
            $dayEnabled = (int) $this->params->get($dayKey . '_enabled', 0);

            if (!$dayEnabled) {
                $isOpen = false;
            } else {
                $openStr  = (string) $this->params->get($dayKey . '_open',  '08:00');
                $closeStr = (string) $this->params->get($dayKey . '_close', '18:00');

                $currentTime = (int) $now->format('Hi'); // ex: 0830
                $openTime    = (int) str_replace(':', '', $openStr);
                $closeTime   = (int) str_replace(':', '', $closeStr);

                if ($currentTime < $openTime || $currentTime >= $closeTime) {
                    $isOpen = false;
                }
            }

            if (!$isOpen) {
                $offlineMsg = (string) $this->params->get('schedule_offline_msg', '');
            }
        }

        // ── Mensagem + variáveis ─────────────────────────────
        $doc      = Factory::getDocument();
        $message  = $isOpen
            ? (string) $this->params->get('message', 'Olá! {url}')
            : $offlineMsg;

        $url      = Uri::getInstance()->toString();
        $title    = method_exists($doc, 'getTitle') ? (string) $doc->getTitle() : '';
        $sitename = (string) $app->get('sitename');

        $message = strtr($message, [
            '{url}'      => $url,
            '{title}'    => $title,
            '{sitename}' => $sitename,
        ]);

        // ── Link ─────────────────────────────────────────────
        if ($isMobile) {
            $link = 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
        } else {
            $link = 'https://api.whatsapp.com/send?phone=' . $phone . '&text=' . rawurlencode($message);
        }

        // ── Layout params ─────────────────────────────────────
        $layoutMode  = (string) $this->params->get('layout_mode',  'icon_text');
        $buttonText  = (string) $this->params->get('button_text',  'WhatsApp');
        $shape       = (string) $this->params->get('shape',        'pill');
        $size        = (string) $this->params->get('size',         'md');

        // ── Ícone personalizado ────────────────────────────────
        // Campo media do Joomla salva JSON: {"imagefile":"images/...","width":...,"height":...,"alt":"..."}
        $iconImageRaw = $this->params->get('icon_image', '');
        $iconImage    = '';
        if (!empty($iconImageRaw)) {
            if (is_object($iconImageRaw)) {
                $iconImage = trim((string) ($iconImageRaw->imagefile ?? ''));
            } elseif (is_string($iconImageRaw)) {
                $decoded = json_decode($iconImageRaw, true);
                $iconImage = is_array($decoded)
                    ? trim((string) ($decoded['imagefile'] ?? ''))
                    : trim($iconImageRaw);
            }
        }

        $iconClass = trim((string) $this->params->get('icon_class', ''));

        // Sanitiza SVG: aceita apenas conteúdo que comece com <svg (ignora espaços/BOM)
        $iconSvgRaw = trim((string) $this->params->get('icon_svg', ''));
        $iconSvg    = '';
        if ($iconSvgRaw !== '' && preg_match('/^\s*<svg[\s>]/i', $iconSvgRaw)) {
            // Remove atributos perigosos: on*, javascript:, <script>
            $iconSvg = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $iconSvgRaw);
            $iconSvg = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $iconSvg);
            $iconSvg = preg_replace('/javascript\s*:/i', '', $iconSvg);
            $iconSvg = trim($iconSvg);
        }

        // ── Design params ─────────────────────────────────────
        $bgColor     = trim((string) $this->params->get('bg_color',     '#25D366'));
        $textColor   = trim((string) $this->params->get('text_color',   '#FFFFFF'));
        $borderColor = trim((string) $this->params->get('border_color', 'transparent'));
        $bgHover     = trim((string) $this->params->get('bg_hover',     '#1ebe5d'));
        $textHover   = trim((string) $this->params->get('text_hover',   '#FFFFFF'));
        $borderHover = trim((string) $this->params->get('border_hover', 'transparent'));
        $shadow      = (int)  $this->params->get('shadow',        1);
        $offsetBottom = max(0, (int) $this->params->get('offset_bottom', 20));
        $offsetInline = max(0, (int) $this->params->get('offset_inline', 20));
        $zIndex       = max(1, (int) $this->params->get('zindex',       999999));

        // ── Behaviour params ──────────────────────────────────
        $animation      = (string) $this->params->get('animation',       'slide');
        $animDelay      = max(0, (int) $this->params->get('animation_delay', 1000));
        $tooltipEnabled = (int)  $this->params->get('tooltip_enabled',  0);
        $tooltipText    = (string) $this->params->get('tooltip_text',   'Fale conosco!');
        $tooltipDelay   = max(0, (int) $this->params->get('tooltip_delay',  3000));
        $tooltipBg      = trim((string) $this->params->get('tooltip_bg',    '#075E54'));
        $tooltipColor   = trim((string) $this->params->get('tooltip_color', '#FFFFFF'));

        // ── Render layout ─────────────────────────────────────
        $layout = new FileLayout('default', __DIR__ . '/../../layouts');

        $html = $layout->render([
            'link'           => $link,
            'position'       => (string) $this->params->get('position', 'end'),
            'is_open'        => $isOpen,
            'layout_mode'    => $layoutMode,
            'button_text'    => $buttonText,
            'shape'          => $shape,
            'size'           => $size,
            'icon_image'     => $iconImage,
            'icon_svg'       => $iconSvg,
            'icon_class'     => $iconClass,
            'bg_color'       => $bgColor,
            'text_color'     => $textColor,
            'border_color'   => $borderColor,
            'bg_hover'       => $bgHover,
            'text_hover'     => $textHover,
            'border_hover'   => $borderHover,
            'shadow'         => $shadow,
            'offset_bottom'  => $offsetBottom,
            'offset_inline'  => $offsetInline,
            'zindex'         => $zIndex,
            'animation'      => $animation,
            'anim_delay'     => $animDelay,
            'tooltip_enabled'=> $tooltipEnabled,
            'tooltip_text'   => $tooltipText,
            'tooltip_delay'  => $tooltipDelay,
            'tooltip_bg'     => $tooltipBg,
            'tooltip_color'  => $tooltipColor,
            'schedule_enabled' => $scheduleEnabled,
            'offline_msg'    => $offlineMsg,
        ]);

        $body = $app->getBody();

        // Evita injetar 2x
        if (strpos($body, 'id="plg-btnwa"') !== false) {
            return;
        }

        $pos = strripos($body, '</body>');

        if ($pos !== false) {
            $body = substr_replace($body, $html . "\n", $pos, 0);
            $app->setBody($body);
        }
    }
}
