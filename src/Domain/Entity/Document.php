<?php

namespace Mikron\json2doc\Domain\Entity;

/**
 * Class Document
 * @package Mikron\json2doc\Domain\Entity
 */
class Document
{
    /**
     * @var string
     */
    private $json;

    /**
     * @var array
     */
    private $array;

    /**
     * Document constructor.
     * @param $json string
     */
    public function __construct($json)
    {
        $this->json = $json;
        $this->array = json_decode($this->json, true);
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->array;
    }
}
