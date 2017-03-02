<?php

namespace Mikron\json2doc\Tests;

use Mikron\json2doc\Domain\Entity\Document;
use Mikron\json2doc\Domain\Entity\StylePack;
use PHPUnit_Framework_TestCase;

final class StylePackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function isStylePackCreatedCorrectly()
    {
        $object = new StylePack("");

        $this->assertInstanceOf('Mikron\json2doc\Domain\Entity\StylePack', $object);
    }
}
