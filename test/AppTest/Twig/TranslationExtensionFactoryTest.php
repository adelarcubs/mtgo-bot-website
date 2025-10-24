<?php

declare(strict_types=1);

namespace AppTest\Twig;

use App\Twig\TranslationExtension;
use App\Twig\TranslationExtensionFactory;
use Laminas\I18n\Translator\Translator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use RuntimeException;

class TranslationExtensionFactoryTest extends TestCase
{
    private ContainerInterface&MockObject $container;
    private TranslatorInterface&MockObject $translator;
    private TranslationExtensionFactory $factory;

    protected function setUp(): void
    {
        $this->container  = $this->createMock(ContainerInterface::class);
        $this->translator = $this->createMock(Translator::class);
        $this->factory    = new TranslationExtensionFactory();
    }

    public function testInvokeReturnsTranslationExtension(): void
    {
        // Mock has() to return true for TranslatorInterface::class and false for Translator::class
        $this->container->expects($this->exactly(2))
            ->method('has')
            ->willReturnCallback(function ($service) {
                return $service === TranslatorInterface::class;
            });

        $this->container->expects($this->once())
            ->method('get')
            ->with(TranslatorInterface::class)
            ->willReturn($this->translator);

        $extension = ($this->factory)($this->container);

        $this->assertInstanceOf(TranslationExtension::class, $extension);

        // Use reflection to verify the translator was properly injected
        $reflection = new ReflectionClass(TranslationExtension::class);
        $property   = $reflection->getProperty('translator');
        $property->setAccessible(true);
        $this->assertSame($this->translator, $property->getValue($extension));
    }

    public function testInvokeThrowsExceptionWhenTranslatorNotFound(): void
    {
        // Mock has() to return false for all services
        $this->container->expects($this->exactly(2))
            ->method('has')
            ->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to create ' . TranslationExtension::class . '; could not find ' . TranslatorInterface::class . ' service.');

        ($this->factory)($this->container);
    }
}
