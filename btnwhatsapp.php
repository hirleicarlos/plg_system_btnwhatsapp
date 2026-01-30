<?php
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class PlgSystemBtnwhatsapp extends CMSPlugin
{
    public function onBeforeCompileHead(): void
    {
        $app = Factory::getApplication();

        if ($app->isClient('site')) {
            $doc = Factory::getDocument();
            $doc->addStyleSheet(Uri::root() . 'media/plg_system_btnwhatsapp/css/btnwhatsapp.css');
        }
    }

    public function onAfterRender()
    {
        $app = Factory::getApplication();

        if ($app->isClient('administrator')) {
            return;
        }

        $paginaAtual = Uri::getInstance()->toString();

        $telefone = $this->params->get('whatsapp_phone');
        $mensagem = $this->params->get('whatsapp_message');
        $urlBase = $this->params->get('whatsapp_url');

        // Substitui {url} pela URL atual
        $mensagem = str_replace('{url}', $paginaAtual, $mensagem);

        // Codifica a mensagem inteira com rawurlencode
        $mensagemCodificada = rawurlencode($mensagem);

        // Monta o link final
        $linkWhatsApp = $urlBase . '/' . $telefone . '?text=' . $mensagemCodificada;

        // HTML final
        $html = <<<HTML
<div id="whatsapp-icon" style="z-index: 999999;">
  <a href="{$linkWhatsApp}" target="_blank" rel="noopener noreferrer">
    <i class="fab fa-whatsapp"></i>
  </a>
  <span class="pulse">1</span>
</div>
HTML;

        $body = $app->getBody();
        $body = str_replace('</body>', $html . '</body>', $body);
        $app->setBody($body);
    }
}