<?php

namespace Mikron\json2tex\Tests;

use Mikron\json2tex\Domain\Entity\Skill;
use PHPUnit\Framework\TestCase;

class SkillTest extends TestCase
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
