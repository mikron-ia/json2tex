<?php

namespace Mikron\json2tex\Tests;

use Mikron\json2tex\Domain\Entity\Document;
use Mikron\json2tex\Domain\Entity\StylePack;
use PHPUnit\Framework\TestCase;

final class StylePackTest extends TestCase
{
    public function testIsStylePackCreatedCorrectly()
    {
        $object = new StylePack("");

        $this->assertInstanceOf('Mikron\json2tex\Domain\Entity\StylePack', $object);
    }

    public function testIsTexReturnedCorrectly()
    {
        $string = '\renewCommand{A}{B}';
        $tex = new StylePack($string);

        $this->assertEquals($string, $tex->getTeX());
    }

    public function testIsToStringCorrect()
    {
        $string = '\renewCommand{A}{B}';
        $tex = new StylePack($string);

        $this->assertEquals($string, $tex);
    }
}
