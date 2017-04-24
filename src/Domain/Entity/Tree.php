<?php

namespace Mikron\json2tex\Domain\Entity;


use Mikron\json2tex\Domain\Exception\MalformedJsonException;

class Tree
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
     * Tree constructor.
     * @param $json
     * @throws MalformedJsonException
     */
    public function __construct($json)
    {
        $this->json = $json;
        $this->array = json_decode($json);

        if ($this->array === null) {
            throw new MalformedJsonException();
        }
    }
}
