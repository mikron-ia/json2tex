<?php

namespace Mikron\json2tex\Tests;

use Mikron\json2tex\Domain\Entity\Document;
use Mikron\json2tex\Domain\Entity\StylePack;
use PHPUnit\Framework\TestCase;

final class StylePackTest extends TestCase
{
    /**
     * @test
     */
    public function isStylePackCreatedCorrectly()
    {
        $object = new StylePack("");

        $this->assertInstanceOf('Mikron\json2tex\Domain\Entity\StylePack', $object);
    }

    /**
     * @test
     */
    public function isTexReturnedCorrectly()
    {
        $string = '\renewCommand{A}{B}';
        $tex = new StylePack($string);

        $this->assertEquals($string, $tex->getTeX());
    }

    /**
     * @test
     */
    public function isToStringCorrect()
    {
        $string = '\renewCommand{A}{B}';
        $tex = new StylePack($string);

        $this->assertEquals($string, $tex);
    }
}
