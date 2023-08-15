<?php

namespace Mikron\json2tex\Domain\Entity;

class Skill
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var int
     */
    private int $rank;

    /**
     * @var string[]
     */
    private array $parameters;
}
