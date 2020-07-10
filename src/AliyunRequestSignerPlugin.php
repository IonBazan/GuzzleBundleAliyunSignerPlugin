<?php

declare(strict_types=1);

namespace IonBazan\Bundle\GuzzleBundleAliyunSignerPlugin;

use EightPoints\Bundle\GuzzleBundle\PluginInterface;
use IonBazan\AliyunSigner\Guzzle\RequestSignerMiddleware;
use IonBazan\AliyunSigner\Key;
use IonBazan\AliyunSigner\RequestSigner;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AliyunRequestSignerPlugin extends Bundle implements PluginInterface
{
    public function getPluginName(): string
    {
        return 'aliyun_signer';
    }

    public function addConfiguration(ArrayNodeDefinition $pluginNode): void
    {
        $pluginNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('app_id')->defaultNull()->end()
                ->scalarNode('secret')->defaultNull()->end()
            ->end();
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
    }

    public function loadForClient(
        array $config,
        ContainerBuilder $container,
        string $clientName,
        Definition $handler
    ): void {
        if ($config['app_id'] && $config['secret']) {
            $key = new Definition(Key::class);
            $key->setArguments([$config['app_id'], $config['secret']]);
            $keyId = sprintf('guzzle_bundle_aliyun_signer_plugin.key.%s', $clientName);
            $container->setDefinition($keyId, $key);

            $signer = new Definition(RequestSigner::class);
            $signer->setArguments([new Reference($keyId)]);
            $signerId = sprintf('guzzle_bundle_aliyun_signer_plugin.signer.%s', $clientName);
            $container->setDefinition($signerId, $signer);

            $middleware = new Definition(RequestSignerMiddleware::class);
            $middleware->setArguments([new Reference($signerId)]);
            $middlewareId = sprintf('guzzle_bundle_aliyun_signer_plugin.middleware.%s', $clientName);
            $container->setDefinition($middlewareId, $middleware);

            $handler->addMethodCall('unshift', [new Reference($middlewareId), $this->getPluginName()]);
        }
    }
}
