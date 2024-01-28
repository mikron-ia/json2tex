<?php

namespace Mikron\json2tex\Tests;

use Mikron\json2tex\Domain\Service\LabelMakerSimple;
use PHPUnit\Framework\TestCase;

class LabelMakerSimpleTest extends TestCase
{
    private LabelMakerSimple $labelMaker;

    /**
     * @dataProvider captionProvider
     *
     * @param string $caption Input - freestyle caption
     * @param string $label Output - proper label
     *
     * @return void
     */
    public function testExtractLabelFromCaption(string $caption, string $label): void
    {
        $this->assertEquals($label, $this->labelMaker->extractLabelFromCaption($caption));
    }

    /**
     * @return void
     */
    public function testEncaseLabel(): void
    {
        $this->assertEquals('\\label{label-content}', $this->labelMaker->encaseLabel('label-content'));
    }

    /**
     * @dataProvider prefixProvider
     *
     * @param string|null $prefixRaw
     * @param string $prefixFormatted
     *
     * @return void
     */
    public function testFormatPrefixForLabel(?string $prefixRaw, string $prefixFormatted): void
    {
        $this->assertEquals($prefixFormatted, $this->labelMaker->formatPrefixForLabel($prefixRaw));
    }

    /**
     * @dataProvider labelProvider
     *
     * @param string|null $label
     * @param string|null $caption
     * @param string|null $prefix
     * @param string $result
     *
     * @return void
     */
    public function testMakeLabel(?string $label, ?string $caption, ?string $prefix, string $result): void
    {
        $this->assertEquals(
            $result,
            $this->labelMaker->makeLabel($label, $caption, $prefix)
        );
    }

    public static function captionProvider(): array
    {
        return [
            ['simplest', 'simplest'],
            ['One', 'one'],
            ['More than one', 'more-than-one'],
            ['More than one & has some  weird  spaces', 'more-than-one-has-some-weird-spaces'],
            ['More than one & has some weird stuff', 'more-than-one-has-some-weird-stuff'],
            ["Has \n\n lines", 'has-lines'],
        ];
    }

    public static function prefixProvider(): array
    {
        return [
            ['subsec', 'subsec:'],
            [null, ''],
        ];
    }

    public static function labelProvider(): array
    {
        return [
            ['', '', '', ''],
            ['simple', '', '', '\\label{simple}'],
            ['simple', 'prefix', '', '\\label{simple}'],
            [null, 'simple generated', '', '\\label{simple-generated}'],
            [null, 'simple generated', 'prefixed', '\\label{prefixed:simple-generated}'],
        ];
    }

    protected function setUp(): void
    {
        $this->labelMaker = new LabelMakerSimple();
        parent::setUp();
    }
}