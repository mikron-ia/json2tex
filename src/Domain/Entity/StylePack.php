<?php

namespace Mikron\json2tex\Domain\Entity;

/**
 * Simple wrapper on styles
 *
 * @todo Add loading from file
 * 
 * @package Mikron\json2tex\Domain\Entity
 */
class StylePack
{
    /**
     * @var string
     */
    private string $tex;

    public function __construct(string $tex)
    {
        $this->tex = $tex;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getTeX();
    }

    /**
     * @return string
     */
    public function getTeX(): string
    {
        return $this->tex;
    }
}
