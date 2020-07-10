<?php

declare(strict_types=1);

namespace IonBazan\Bundle\GuzzleBundleAliyunSignerPlugin\Tests;

use EightPoints\Bundle\GuzzleBundle\PluginInterface;
use Generator;
use IonBazan\AliyunSigner\Guzzle\RequestSignerMiddleware;
use IonBazan\AliyunSigner\Key;
use IonBazan\AliyunSigner\RequestSigner;
use IonBazan\Bundle\GuzzleBundleAliyunSignerPlugin\AliyunRequestSignerPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AliyunRequestSignerPluginTest extends TestCase
{
    /**
     * @var AliyunRequestSignerPlugin
     */
    private $plugin;

    protected function setUp(): void
    {
        $this->plugin = new AliyunRequestSignerPlugin();
    }

    public function testSubClassesOfPlugin(): void
    {
        self::assertInstanceOf(PluginInterface::class, $this->plugin);
        self::assertInstanceOf(Bundle::class, $this->plugin);
    }

    public function testLoad(): void
    {
        $this->plugin->load([], new ContainerBuilder());
        self::assertTrue(true);
    }

    public function testAddConfiguration(): void
    {
        $arrayNode = new ArrayNodeDefinition('node');
        $this->plugin->addConfiguration($arrayNode);
        $node = $arrayNode->getNode();

        self::assertFalse($node->isRequired());
        self::assertTrue($node->hasDefaultValue());
        self::assertSame(['app_id' => null, 'secret' => null], $node->getDefaultValue());
    }

    public function testLoadForClientWithConfig(): void
    {
        $handler = new Definition();
        $container = new ContainerBuilder();
        $config = ['app_id' => '123', 'secret' => 'test_secret'];
        $this->plugin->loadForClient($config, $container, 'test_client', $handler);

        $middlewareDefinition = $container->getDefinition('guzzle_bundle_aliyun_signer_plugin.middleware.test_client');
        self::assertSame(RequestSignerMiddleware::class, $middlewareDefinition->getClass());
        self::assertEquals(
            [new Reference('guzzle_bundle_aliyun_signer_plugin.signer.test_client')],
            $middlewareDefinition->getArguments()
        );

        $signerDefinition = $container->getDefinition('guzzle_bundle_aliyun_signer_plugin.signer.test_client');
        self::assertSame(RequestSigner::class, $signerDefinition->getClass());
        self::assertEquals(
            [new Reference('guzzle_bundle_aliyun_signer_plugin.key.test_client')],
            $signerDefinition->getArguments()
        );

        $keyDefinition = $container->getDefinition('guzzle_bundle_aliyun_signer_plugin.key.test_client');
        self::assertSame(Key::class, $keyDefinition->getClass());
        self::assertEquals(['123', 'test_secret'], $keyDefinition->getArguments());
        self::assertEquals(
            [['unshift', [new Reference('guzzle_bundle_aliyun_signer_plugin.middleware.test_client'), 'aliyun_signer']]],
            $handler->getMethodCalls()
        );
    }

    /**
     * @dataProvider emptyConfigDataProvider
     */
    public function testLoadForClientWithoutConfig(array $config): void
    {
        $handler = new Definition();
        $container = new ContainerBuilder();
        $this->plugin->loadForClient($config, $container, 'test_client', $handler);

        self::assertFalse($container->hasDefinition('guzzle_bundle_aliyun_signer_plugin.key.test_client'));
        self::assertFalse($container->hasDefinition('guzzle_bundle_aliyun_signer_plugin.signer.test_client'));
        self::assertFalse($container->hasDefinition('guzzle_bundle_aliyun_signer_plugin.middleware.test_client'));
    }

    public function emptyConfigDataProvider(): Generator
    {
        yield [['app_id' => '', 'secret' => '']];
        yield [['app_id' => null, 'secret' => 'test']];
        yield [['app_id' => 'test', 'secret' => '']];
    }
}
