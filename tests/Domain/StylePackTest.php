<?php

namespace Mikron\json2tex\Tests;

use Mikron\json2tex\Domain\Entity\Document;
use Mikron\json2tex\Domain\Entity\StylePack;
use PHPUnit_Framework_TestCase;

final class StylePackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function isStylePackCreatedCorrectly()
    {
        $object = new StylePack("");

        $this->assertInstanceOf('Mikron\json2tex\Domain\Entity\StylePack', $object);
    }
}
