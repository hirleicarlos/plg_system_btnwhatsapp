<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.btnwhatsapp
 *
 * @copyright   (C) 2026 Hirlei Carlos Pereira de Araújo
 * @license     GNU General Public License version 2 or later
 */
defined('_JEXEC') or die;

$link           = $displayData['link']            ?? '#';
$position       = $displayData['position']        ?? 'end';
$isOpen         = $displayData['is_open']         ?? true;
$layoutMode     = $displayData['layout_mode']     ?? 'icon_text';
$buttonText     = trim((string) ($displayData['button_text'] ?? 'WhatsApp'));
$shape          = $displayData['shape']           ?? 'pill';
$size           = $displayData['size']            ?? 'md';

$iconImage      = trim((string) ($displayData['icon_image'] ?? ''));
$iconSvg        = trim((string) ($displayData['icon_svg']   ?? ''));
$iconClass      = trim((string) ($displayData['icon_class'] ?? ''));

$bgColor        = (string) ($displayData['bg_color']     ?? '#25D366');
$textColor      = (string) ($displayData['text_color']   ?? '#FFFFFF');
$borderColor    = (string) ($displayData['border_color'] ?? 'transparent');
$bgHover        = (string) ($displayData['bg_hover']     ?? '#1ebe5d');
$textHover      = (string) ($displayData['text_hover']   ?? '#FFFFFF');
$borderHover    = (string) ($displayData['border_hover'] ?? 'transparent');
$shadow         = (int)    ($displayData['shadow']       ?? 1);
$offsetBottom   = (int)    ($displayData['offset_bottom'] ?? 20);
$offsetInline   = (int)    ($displayData['offset_inline'] ?? 20);
$zIndex         = (int)    ($displayData['zindex']        ?? 999999);

$animation      = (string) ($displayData['animation']       ?? 'slide');
$animDelay      = (int)    ($displayData['anim_delay']       ?? 1000);
$tooltipEnabled = (int)    ($displayData['tooltip_enabled']  ?? 0);
$tooltipText    = (string) ($displayData['tooltip_text']     ?? 'Fale conosco!');
$tooltipDelay   = (int)    ($displayData['tooltip_delay']    ?? 3000);
$tooltipBg      = (string) ($displayData['tooltip_bg']       ?? '#075E54');
$tooltipColor   = (string) ($displayData['tooltip_color']    ?? '#FFFFFF');
$scheduleEnabled = (int)   ($displayData['schedule_enabled'] ?? 0);
$offlineMsg     = (string) ($displayData['offline_msg']      ?? '');

// Fora do horário: mostrar estado desabilitado visualmente (sem link)
$isDisabled = ($scheduleEnabled && !$isOpen);

// CSS variables
$styleVars = [
    '--btnwa-bg:'           . $bgColor,
    '--btnwa-color:'        . $textColor,
    '--btnwa-border:'       . $borderColor,
    '--btnwa-bg-hover:'     . $bgHover,
    '--btnwa-color-hover:'  . $textHover,
    '--btnwa-border-hover:' . $borderHover,
    '--btnwa-offset-bottom:'. max(0, $offsetBottom) . 'px',
    '--btnwa-offset-inline:'. max(0, $offsetInline) . 'px',
    '--btnwa-zindex:'       . max(1, $zIndex),
    '--btnwa-shadow:'       . ($shadow ? '1' : '0'),
    '--btnwa-tooltip-bg:'   . $tooltipBg,
    '--btnwa-tooltip-color:'. $tooltipColor,
];

$styleAttr = implode(';', $styleVars);

// Data-attributes para JS
$dataAttrs = 'data-animation="' . htmlspecialchars($animation, ENT_QUOTES, 'UTF-8') . '"'
    . ' data-anim-delay="'      . (int) $animDelay . '"'
    . ' data-tooltip="'         . ($tooltipEnabled ? '1' : '0') . '"'
    . ' data-tooltip-delay="'   . (int) $tooltipDelay . '"';

// ── Ícone: prioridade → class CSS > SVG personalizado > imagem > padrão WhatsApp ──
// SVG padrão WhatsApp (fallback)
$defaultIconSvg = '<svg class="plg-btnwa__icon" viewBox="0 0 32 32" aria-hidden="true" focusable="false">'
    . '<path fill="currentColor" d="M19.11 17.46c-.28-.14-1.65-.81-1.9-.9-.26-.09-.45-.14-.64.14-.19.28-.73.9-.9 1.08-.16.19-.32.21-.6.07-.28-.14-1.17-.43-2.24-1.37-.83-.74-1.39-1.66-1.55-1.94-.16-.28-.02-.43.12-.57.12-.12.28-.32.42-.48.14-.16.19-.28.28-.46.09-.19.05-.35-.02-.5-.07-.14-.64-1.54-.88-2.1-.23-.55-.47-.48-.64-.49l-.55-.01c-.19 0-.5.07-.76.35-.26.28-1 1-1 2.44 0 1.44 1.03 2.83 1.17 3.03.14.19 2.03 3.09 4.93 4.33.69.3 1.23.48 1.65.62.69.22 1.31.19 1.8.12.55-.08 1.65-.67 1.88-1.31.23-.64.23-1.19.16-1.31-.07-.12-.26-.19-.55-.33z"/>'
    . '<path fill="currentColor" d="M16 3C8.83 3 3 8.66 3 15.62c0 2.73.9 5.24 2.43 7.28L4 29l6.3-1.95c1.95 1.07 4.19 1.68 6.7 1.68 7.17 0 13-5.66 13-12.62C30 8.66 23.17 3 16 3zm0 23.04c-2.33 0-4.44-.69-6.2-1.85l-.44-.28-3.74 1.16 1.22-3.54-.3-.55c-1.2-1.9-1.89-4.13-1.89-6.48C4.65 9.88 9.77 4.96 16 4.96c6.23 0 11.35 4.92 11.35 10.66 0 5.74-5.12 10.42-11.35 10.42z"/>'
    . '</svg>';

if ($iconClass !== '') {
    // Prioridade 1: classe CSS (ex: fa fa-whatsapp, bi bi-whatsapp, material-icons)
    $resolvedIcon = '<i class="plg-btnwa__icon ' . htmlspecialchars($iconClass, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true"></i>';
} elseif ($iconSvg !== '') {
    // Prioridade 2: SVG personalizado (já sanitizado no Extension/Btnwhatsapp.php)
    // Garante que a classe plg-btnwa__icon esteja presente no elemento <svg>
    if (preg_match('/class\s*=/i', $iconSvg)) {
        $resolvedIcon = preg_replace('/(<svg\b[^>]*class\s*=\s*["\'])([^"\']*)/i', '$1plg-btnwa__icon $2', $iconSvg, 1);
    } else {
        $resolvedIcon = preg_replace('/<svg\b/i', '<svg class="plg-btnwa__icon"', $iconSvg, 1);
    }
} elseif ($iconImage !== '') {
    // Prioridade 3: imagem (URL ou path relativo)
    $resolvedIcon = '<img'
        . ' class="plg-btnwa__icon"'
        . ' src="' . htmlspecialchars($iconImage, ENT_QUOTES, 'UTF-8') . '"'
        . ' alt=""'
        . ' aria-hidden="true"'
        . ' loading="lazy"'
        . '>';
} else {
    // Fallback: SVG padrão do WhatsApp
    $resolvedIcon = $defaultIconSvg;
}
?>

<div
    id="plg-btnwa"
    class="plg-btnwa<?php echo $isDisabled ? ' plg-btnwa--offline' : ''; ?>"
    data-pos="<?php echo htmlspecialchars($position,   ENT_QUOTES, 'UTF-8'); ?>"
    data-shape="<?php echo htmlspecialchars($shape,    ENT_QUOTES, 'UTF-8'); ?>"
    data-size="<?php echo htmlspecialchars($size,      ENT_QUOTES, 'UTF-8'); ?>"
    data-mode="<?php echo htmlspecialchars($layoutMode,ENT_QUOTES, 'UTF-8'); ?>"
    <?php echo $dataAttrs; ?>
    style="<?php echo htmlspecialchars($styleAttr, ENT_QUOTES, 'UTF-8'); ?>"
    aria-live="polite"
>
    <?php if ($tooltipEnabled && !$isDisabled): ?>
    <div class="plg-btnwa__tooltip" role="tooltip" aria-hidden="true">
        <?php echo htmlspecialchars($tooltipText, ENT_QUOTES, 'UTF-8'); ?>
        <span class="plg-btnwa__tooltip-arrow"></span>
    </div>
    <?php endif; ?>

    <?php if ($isDisabled): ?>
    <div class="plg-btnwa__btn plg-btnwa__btn--disabled" aria-label="<?php echo htmlspecialchars($offlineMsg, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($offlineMsg, ENT_QUOTES, 'UTF-8'); ?>">
        <?php echo $resolvedIcon; ?>
        <?php if ($layoutMode !== 'icon'): ?>
        <span class="plg-btnwa__text"><?php echo htmlspecialchars($offlineMsg ?: $buttonText, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <a
        class="plg-btnwa__btn"
        href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="WhatsApp"
    >
        <?php if ($layoutMode === 'icon' || $layoutMode === 'icon_text'): ?>
            <?php echo $resolvedIcon; ?>
        <?php endif; ?>

        <?php if (($layoutMode === 'text' || $layoutMode === 'icon_text') && $buttonText !== ''): ?>
            <span class="plg-btnwa__text"><?php echo htmlspecialchars($buttonText, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php endif; ?>
    </a>
    <?php endif; ?>
</div>
