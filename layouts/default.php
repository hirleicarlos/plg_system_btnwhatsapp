<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.btnwhatsapp
 *
 * @copyright   (C) 2026 Hirlei Carlos Pereira de Araújo
 * @license     GNU General Public License version 2 or later
 */
defined('_JEXEC') or die;

$link        = $displayData['link'] ?? '#';
$position    = $displayData['position'] ?? 'end';

$layoutMode  = $displayData['layout_mode'] ?? 'icon_text'; // icon | text | icon_text
$buttonText  = trim((string) ($displayData['button_text'] ?? 'WhatsApp'));

$shape       = $displayData['shape'] ?? 'pill'; // circle | pill | rounded | square
$size        = $displayData['size'] ?? 'md';    // sm | md | lg

$bgColor     = (string) ($displayData['bg_color'] ?? '#25D366');
$textColor   = (string) ($displayData['text_color'] ?? '#FFFFFF');
$borderColor = (string) ($displayData['border_color'] ?? 'transparent');

$bgHover     = (string) ($displayData['bg_hover'] ?? '#1ebe5d');
$textHover   = (string) ($displayData['text_hover'] ?? '#FFFFFF');
$borderHover = (string) ($displayData['border_hover'] ?? 'transparent');

$shadow      = (int) ($displayData['shadow'] ?? 1);

$offsetBottom = (int) ($displayData['offset_bottom'] ?? 20);
$offsetInline = (int) ($displayData['offset_inline'] ?? 20);
$zIndex       = (int) ($displayData['zindex'] ?? 999999);

// CSS variables inline (produto: configurável sem depender de template)
$styleVars = [
        '--btnwa-bg:' . $bgColor,
        '--btnwa-color:' . $textColor,
        '--btnwa-border:' . $borderColor,
        '--btnwa-bg-hover:' . $bgHover,
        '--btnwa-color-hover:' . $textHover,
        '--btnwa-border-hover:' . $borderHover,
        '--btnwa-offset-bottom:' . max(0, $offsetBottom) . 'px',
        '--btnwa-offset-inline:' . max(0, $offsetInline) . 'px',
        '--btnwa-zindex:' . max(1, $zIndex),
        '--btnwa-shadow:' . ($shadow ? '1' : '0'),
];

$styleAttr = implode(';', $styleVars);

// SVG WhatsApp (sem dependência externa)
$iconSvg = '<svg class="plg-btnwa__icon" width="22" height="22" viewBox="0 0 32 32" aria-hidden="true" focusable="false">'
        . '<path fill="currentColor" d="M19.11 17.46c-.28-.14-1.65-.81-1.9-.9-.26-.09-.45-.14-.64.14-.19.28-.73.9-.9 1.08-.16.19-.32.21-.6.07-.28-.14-1.17-.43-2.24-1.37-.83-.74-1.39-1.66-1.55-1.94-.16-.28-.02-.43.12-.57.12-.12.28-.32.42-.48.14-.16.19-.28.28-.46.09-.19.05-.35-.02-.5-.07-.14-.64-1.54-.88-2.1-.23-.55-.47-.48-.64-.49l-.55-.01c-.19 0-.5.07-.76.35-.26.28-1 1-1 2.44 0 1.44 1.03 2.83 1.17 3.03.14.19 2.03 3.09 4.93 4.33.69.3 1.23.48 1.65.62.69.22 1.31.19 1.8.12.55-.08 1.65-.67 1.88-1.31.23-.64.23-1.19.16-1.31-.07-.12-.26-.19-.55-.33z"/>'
        . '<path fill="currentColor" d="M16 3C8.83 3 3 8.66 3 15.62c0 2.73.9 5.24 2.43 7.28L4 29l6.3-1.95c1.95 1.07 4.19 1.68 6.7 1.68 7.17 0 13-5.66 13-12.62C30 8.66 23.17 3 16 3zm0 23.04c-2.33 0-4.44-.69-6.2-1.85l-.44-.28-3.74 1.16 1.22-3.54-.3-.55c-1.2-1.9-1.89-4.13-1.89-6.48C4.65 9.88 9.77 4.96 16 4.96c6.23 0 11.35 4.92 11.35 10.66 0 5.74-5.12 10.42-11.35 10.42z"/>'
        . '</svg>';
?>

<div
        class="plg-btnwa"
        data-pos="<?php echo htmlspecialchars($position, ENT_QUOTES, 'UTF-8'); ?>"
        data-shape="<?php echo htmlspecialchars($shape, ENT_QUOTES, 'UTF-8'); ?>"
        data-size="<?php echo htmlspecialchars($size, ENT_QUOTES, 'UTF-8'); ?>"
        data-mode="<?php echo htmlspecialchars($layoutMode, ENT_QUOTES, 'UTF-8'); ?>"
        style="<?php echo htmlspecialchars($styleAttr, ENT_QUOTES, 'UTF-8'); ?>"
>
    <a class="plg-btnwa__btn"
       href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="WhatsApp"
    >
        <?php if ($layoutMode === 'icon' || $layoutMode === 'icon_text'): ?>
            <?php echo $iconSvg; ?>
        <?php endif; ?>

        <?php if (($layoutMode === 'text' || $layoutMode === 'icon_text') && $buttonText !== ''): ?>
            <span class="plg-btnwa__text"><?php echo htmlspecialchars($buttonText, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php endif; ?>
    </a>
</div>