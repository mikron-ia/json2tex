<?php

namespace Mikron\json2doc\Domain\Entity;

/**
 * Class StylePack
 * Loads and stores styles
 *
 * @package Mikron\json2doc\Domain\Entity
 */
class StylePack
{
    /**
     * @var array
     */
    private $style;

    /**
     * @param string $json
     */
    public function __construct(string $json)
    {
        $this->style = json_decode($json);
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->style;
    }
}
