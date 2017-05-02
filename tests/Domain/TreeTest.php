<?php

namespace Mikron\json2tex\Tests;

use Mikron\json2tex\Domain\Entity\Tree;
use PHPUnit_Framework_TestCase;

final class TreeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function isTreeCreatedCorrectly()
    {
        $object = new Tree('{}');

        $this->assertInstanceOf('Mikron\json2tex\Domain\Entity\Tree', $object);
    }

    /**
     * @test
     */
    public function isMalformedJSONDetected()
    {
        $this->setExpectedException('Mikron\json2tex\Domain\Exception\MalformedJsonException');
        new Tree('{fail:}');
    }
}
