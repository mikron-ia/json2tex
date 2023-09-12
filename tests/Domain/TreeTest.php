<?php

namespace Mikron\json2tex\Tests;

use Mikron\json2tex\Domain\Entity\Tree;
use PHPUnit\Framework\TestCase;

final class TreeTest extends TestCase
{
    public function testIsTreeCreatedCorrectly()
    {
        $object = new Tree('{}');

        $this->assertInstanceOf('Mikron\json2tex\Domain\Entity\Tree', $object);
    }

    public function testIsMalformedJSONDetected()
    {
        $this->expectException('Mikron\json2tex\Domain\Exception\MalformedJsonException');
        new Tree('{fail:}');
    }
}
