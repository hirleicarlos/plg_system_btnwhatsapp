<?php
defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;   // ✅ ESTE é o correto
use Joomla\Registry\Registry;
use Joomla\Plugin\System\Btnwhatsapp\Extension\Btnwhatsapp;

return new class implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {

                // ✅ Dispatcher correto no Joomla 4/5/6
                $dispatcher = $container->get(DispatcherInterface::class);

                // Pega plugin do banco (params)
                $plugin = PluginHelper::getPlugin('system', 'btnwhatsapp');

                $params = new Registry($plugin->params ?? '');

                $config = [
                    'name'   => 'btnwhatsapp',
                    'type'   => 'system',
                    'params' => $params,
                ];

                return new Btnwhatsapp($dispatcher, $config);
            }
        );
    }
};