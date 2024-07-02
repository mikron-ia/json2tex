<?php

namespace Mikron\json2tex\Domain\Entity;

use Mikron\json2tex\Domain\Exception\MalformedJsonException;
use Mikron\json2tex\Domain\Exception\MissingComponentException;

class AdvantagePack
{
    /**
     * @var string
     */
    private string $json;

    /**
     * @var array
     */
    private mixed $array;

    /**
     * @var string
     */
    private string $document;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var string
     */
    private string $index;

    /**
     * @var string
     */
    private string $content;

    /**
     * Document constructor.
     * @param string $json
     * @param string $path
     */
    public function __construct($json, $path = "")
    {
        $this->json = $json;
        $this->array = json_decode($this->json, true);
        $this->path = $path;
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

    /**
     * @return string
     */
    public function getDocument(): string
    {
        return $this->document;
    }

    /**
     * @throws MalformedJsonException
     * @throws MissingComponentException
     */
    private function makeContentAndIndex(): void
    {
        $traitTexes = [];
        $traitIndex = [];

        if ($this->array === null) {
            throw new MalformedJsonException('Invalid trait structure. Cannot generate document.');
        }

        foreach ($this->array as $traitLabel => $trait) {
            $treeObject = new Advantage(json_encode($trait), $this->path);
            $traitTexes[$traitLabel] = $treeObject->getTex();
            $traitIndex[$traitLabel] = $treeObject->getIndex();
        }

        $this->content = implode(PHP_EOL . PHP_EOL . PHP_EOL, $traitTexes);
        $this->index = implode(PHP_EOL . PHP_EOL . PHP_EOL, $traitIndex);
    }

    /**
     * @return string
     *
     * @throws MalformedJsonException
     * @throws MissingComponentException
     */
    public function getIndex(): string
    {
        if (empty($this->index)) {
            $this->makeContentAndIndex();
        }

        return $this->index;
    }

    /**
     * @return string
     * @throws MalformedJsonException
     * @throws \Exception
     */
    public function getContent(): string
    {
        if (empty($this->content)) {
            $this->makeContentAndIndex();
        }

        return $this->content;
    }
}
