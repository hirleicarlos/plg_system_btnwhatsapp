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

    public function onBeforeCompileHead(): void
    {
        $app = Factory::getApplication();

        if (!$this->params) {
            return;
        }

        if (!$app->isClient('site')) {
            return;
        }

        if (!(int) $this->params->get('enabled', 1)) {
            return;
        }

        $doc = Factory::getDocument();

        if (!$doc instanceof HtmlDocument) {
            return;
        }

        $wa = $doc->getWebAssetManager();

        if (!$wa->assetExists('style', 'plg.system.btnwhatsapp')) {
            $wa->registerStyle(
                'plg.system.btnwhatsapp',
                'media/plg_system_btnwhatsapp/css/btnwhatsapp.css',
                [],
                ['version' => '2.0.0']
            );
        }

        $wa->useStyle('plg.system.btnwhatsapp');
    }

    public function onAfterRender(): void
    {
        $app = Factory::getApplication();

        if (!$this->params) {
            return;
        }

        if (!$app->isClient('site')) {
            return;
        }

        if (!(int) $this->params->get('enabled', 1)) {
            return;
        }

        // ===============================
        // USER AGENT / MOBILE (calcula 1x)
        // ===============================
        $ua = (string) $app->getInput()->server->getString('HTTP_USER_AGENT', '');
        $isMobile = (bool) preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $ua);

        // ===============================
        // CONTROLE DE DISPOSITIVO
        // ===============================
        $device = (string) $this->params->get('device', 'all');

        if ($device === 'mobile' && !$isMobile) {
            return;
        }

        if ($device === 'desktop' && $isMobile) {
            return;
        }

        // ===============================
        // CONTROLE DE MENU
        // ===============================
        $displayMode = (string) $this->params->get('display_mode', 'all');
        $menuItems   = (array) $this->params->get('menu_items', []);

        $menu = $app->getMenu();
        $active = $menu->getActive();
        $currentItemId = $active ? (int) $active->id : 0;

        if ($displayMode === 'only') {
            if (!in_array($currentItemId, $menuItems)) {
                return;
            }
        }

        if ($displayMode === 'exclude') {
            if (in_array($currentItemId, $menuItems)) {
                return;
            }
        }

        // ===============================
        // TELEFONE
        // ===============================
        $phone = preg_replace('/\D+/', '', (string) $this->params->get('phone', ''));

        if ($phone === '') {
            return;
        }

        // ===============================
        // MENSAGEM + VARIÁVEIS (3)
        // ===============================
        $doc = Factory::getDocument();

        $message  = (string) $this->params->get('message', 'Olá! {url}');
        $url      = Uri::getInstance()->toString();
        $title    = method_exists($doc, 'getTitle') ? (string) $doc->getTitle() : '';
        $sitename = (string) $app->get('sitename');

        $message = strtr($message, [
            '{url}'      => $url,
            '{title}'    => $title,
            '{sitename}' => $sitename,
        ]);

        // ===============================
        // LINK (mobile vs desktop)
        // ===============================
        if ($isMobile) {
            $link = 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
        } else {
            $link = 'https://api.whatsapp.com/send?phone=' . $phone . '&text=' . rawurlencode($message);
        }

        // ===============================
        // PARAMS (LAYOUT + DESIGN)
        // ===============================
        $layoutMode  = (string) $this->params->get('layout_mode', 'icon_text'); // icon | text | icon_text
        $buttonText  = (string) $this->params->get('button_text', 'WhatsApp');
        $shape       = (string) $this->params->get('shape', 'pill');           // circle | pill | rounded | square
        $size        = (string) $this->params->get('size', 'md');              // sm | md | lg

        // Cores (aceita #hex e nomes tipo "transparent")
        $bgColor     = trim((string) $this->params->get('bg_color', '#25D366'));
        $textColor   = trim((string) $this->params->get('text_color', '#FFFFFF'));
        $borderColor = trim((string) $this->params->get('border_color', 'transparent'));

        $bgHover     = trim((string) $this->params->get('bg_hover', '#1ebe5d'));
        $textHover   = trim((string) $this->params->get('text_hover', '#FFFFFF'));
        $borderHover = trim((string) $this->params->get('border_hover', 'transparent'));

        $shadow      = (int) $this->params->get('shadow', 1);

        $offsetBottom = (int) $this->params->get('offset_bottom', 20);
        $offsetInline = (int) $this->params->get('offset_inline', 20);
        $zIndex       = (int) $this->params->get('zindex', 999999);

        // sane defaults
        $offsetBottom = max(0, $offsetBottom);
        $offsetInline = max(0, $offsetInline);
        $zIndex       = max(1, $zIndex);

        // ===============================
        // RENDER
        // ===============================
        $layout = new FileLayout('default', __DIR__ . '/../../layouts');

        $html = $layout->render([
            'link'         => $link,
            'position'     => (string) $this->params->get('position', 'end'),
            'layout_mode'  => $layoutMode,
            'button_text'  => $buttonText,
            'shape'        => $shape,
            'size'         => $size,

            'bg_color'     => $bgColor,
            'text_color'   => $textColor,
            'border_color' => $borderColor,

            'bg_hover'     => $bgHover,
            'text_hover'   => $textHover,
            'border_hover' => $borderHover,

            'shadow'       => $shadow,

            'offset_bottom'=> $offsetBottom,
            'offset_inline'=> $offsetInline,
            'zindex'       => $zIndex,
        ]);

        $body = $app->getBody();

        // evita injetar 2x
        if (strpos($body, 'class="plg-btnwa"') !== false) {
            return;
        }

        $pos = strripos($body, '</body>');

        if ($pos !== false) {
            $body = substr_replace($body, $html . "\n", $pos, 0);
            $app->setBody($body);
        }
    }
}