<?php

declare(strict_types=1);

namespace AppTest\Twig;

use App\Twig\TranslationExtension;
use Laminas\I18n\Translator\TranslatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TranslationExtensionTest extends TestCase
{
    private TranslatorInterface&MockObject $translator;
    private TranslationExtension $extension;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->extension  = new TranslationExtension($this->translator);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertEquals('translate', $functions[0]->getName());
        $this->assertEquals('trans', $functions[1]->getName());
    }

    public function testTranslateWithDefaultParams(): void
    {
        $message    = 'test.message';
        $translated = 'Translated message';

        $this->translator->expects($this->once())
            ->method('translate')
            ->with($message, 'default', null)
            ->willReturn($translated);

        $result = $this->extension->translate($message);
        $this->assertEquals($translated, $result);
    }

    public function testTranslateWithCustomParams(): void
    {
        $message    = 'test.message';
        $params     = ['param1' => 'value1'];
        $textDomain = 'custom';
        $locale     = 'pt_BR';
        $translated = 'Mensagem traduzida';

        $this->translator->expects($this->once())
            ->method('translate')
            ->with($message, $textDomain, $locale)
            ->willReturn($translated);

        $result = $this->extension->translate($message, $params, $textDomain, $locale);
        $this->assertEquals($translated, $result);
    }

    public function testTranslateWhenTranslationNotFound(): void
    {
        $message = 'test.unknown';

        $this->translator->expects($this->once())
            ->method('translate')
            ->with($message, 'default', null)
            ->willReturn($message);

        $result = $this->extension->translate($message);
        $this->assertEquals($message, $result);
    }
}
