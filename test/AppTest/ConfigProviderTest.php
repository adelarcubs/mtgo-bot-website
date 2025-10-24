<?php

declare(strict_types=1);

namespace AppTest;

use App\ConfigProvider;
use App\Handler\HomePageHandler;
use App\Handler\PingHandler;
use App\Twig\TranslationExtension;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    private ConfigProvider $configProvider;

    protected function setUp(): void
    {
        $this->configProvider = new ConfigProvider();
    }

    public function testInvokeReturnsExpectedKeys(): void
    {
        $config = ($this->configProvider)();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey('templates', $config);
        $this->assertArrayHasKey('twig', $config);
    }

    public function testGetDependenciesReturnsExpectedStructure(): void
    {
        $dependencies = $this->configProvider->getDependencies();

        $this->assertIsArray($dependencies);
        $this->assertArrayHasKey('invokables', $dependencies);
        $this->assertArrayHasKey('factories', $dependencies);

        // Test specific invokables
        $this->assertArrayHasKey(
            PingHandler::class,
            $dependencies['invokables']
        );

        // Test specific factories
        $this->assertArrayHasKey(
            HomePageHandler::class,
            $dependencies['factories']
        );
    }

    public function testGetTemplatesReturnsExpectedStructure(): void
    {
        $templates = $this->configProvider->getTemplates();

        $this->assertIsArray($templates);
        $this->assertArrayHasKey('paths', $templates);
        $this->assertArrayHasKey('app', $templates['paths']);
        $this->assertArrayHasKey('error', $templates['paths']);
        $this->assertArrayHasKey('layout', $templates['paths']);

        // Verify template paths are arrays
        $this->assertIsArray($templates['paths']['app']);
        $this->assertIsArray($templates['paths']['error']);
        $this->assertIsArray($templates['paths']['layout']);

        // Verify template paths are not empty
        $this->assertNotEmpty($templates['paths']['app']);
        $this->assertNotEmpty($templates['paths']['error']);
        $this->assertNotEmpty($templates['paths']['layout']);
    }

    public function testGetTwigConfigReturnsExpectedStructure(): void
    {
        $twigConfig = $this->configProvider->getTwigConfig();

        $this->assertIsArray($twigConfig);
        $this->assertArrayHasKey('extensions', $twigConfig);
        $this->assertIsArray($twigConfig['extensions']);

        // Verify TranslationExtension is in the extensions array
        $this->assertContains(
            TranslationExtension::class,
            $twigConfig['extensions']
        );
    }
}
