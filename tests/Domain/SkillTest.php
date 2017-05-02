<?php

namespace Mikron\json2tex\Tests;

use Mikron\json2tex\Domain\Entity\Skill;
use PHPUnit_Framework_TestCase;

class SkillTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function isSkillCreatedCorrectly()
    {
        $object = new Skill();

        $this->assertInstanceOf('Mikron\json2tex\Domain\Entity\Skill', $object);
    }
}
