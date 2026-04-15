/**
 * PLG_SYSTEM_BTNWHATSAPP — v3.0.0
 * Animação de entrada + Tooltip automático
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        var wrap = document.getElementById('plg-btnwa');
        if (!wrap) return;

        var animation  = wrap.dataset.animation  || 'slide';
        var animDelay  = parseInt(wrap.dataset.animDelay,  10) || 0;
        var hasTooltip = wrap.dataset.tooltip === '1';
        var tipDelay   = parseInt(wrap.dataset.tooltipDelay, 10) || 3000;
        var tooltip    = wrap.querySelector('.plg-btnwa__tooltip');

        // ── Animação de entrada ──────────────────────────────
        function showButton() {
            if (animation !== 'none') {
                wrap.classList.add('anim-' + animation);
            }
            wrap.classList.add('is-visible');

            // Tooltip auto-show depois de tipDelay
            if (hasTooltip && tooltip) {
                setTimeout(showTooltip, tipDelay);
            }
        }

        setTimeout(showButton, animDelay);

        // ── Tooltip ───────────────────────────────────────────
        function showTooltip() {
            if (!tooltip) return;
            tooltip.setAttribute('aria-hidden', 'false');
            tooltip.classList.add('is-visible');
            wrap.classList.add('tooltip-shown');

            // Esconde após 4s automaticamente
            setTimeout(hideTooltip, 4000);
        }

        function hideTooltip() {
            if (!tooltip) return;
            tooltip.setAttribute('aria-hidden', 'true');
            tooltip.classList.remove('is-visible');
            // Remove pulse
            wrap.classList.remove('tooltip-shown');
        }

        // Hover re-mostra tooltip no desktop
        var btn = wrap.querySelector('.plg-btnwa__btn');
        if (btn && hasTooltip && tooltip) {
            btn.addEventListener('mouseenter', function () {
                tooltip.setAttribute('aria-hidden', 'false');
                tooltip.classList.add('is-visible');
            });
            btn.addEventListener('mouseleave', function () {
                tooltip.setAttribute('aria-hidden', 'true');
                tooltip.classList.remove('is-visible');
            });
            // Toque em mobile
            btn.addEventListener('focus', function () {
                tooltip.setAttribute('aria-hidden', 'false');
                tooltip.classList.add('is-visible');
            });
            btn.addEventListener('blur', function () {
                tooltip.setAttribute('aria-hidden', 'true');
                tooltip.classList.remove('is-visible');
            });
        }
    });

}());
