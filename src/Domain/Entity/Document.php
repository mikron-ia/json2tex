<?php

namespace Mikron\json2tex\Domain\Entity;

use Mikron\json2tex\Domain\Exception\MalformedJsonException;
use Mikron\json2tex\Domain\Exception\MissingComponentException;

/**
 * Class Document
 * @package Mikron\json2tex\Domain\Entity
 */
class Document
{
    /**
     * @var string
     */
    private string $json;

    /**
     * @var array
     */
    private array $array;

    /**
     * @var string
     */
    private string $document;

    /**
     * @var string
     */
    private string $header = '';

    /**
     * @var string
     */
    private string $footer = '';

    /**
     * @var string
     */
    private string $path;

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
     * @throws MalformedJsonException
     * @throws MissingComponentException
     */
    public function getDocument(): string
    {
        if (!$this->document) {
            $top = '\documentclass[a4paper,final,12pt]{memoir}
\providecommand\locale{english}

\usepackage{tikz}
\usetikzlibrary{arrows,positioning}';
            $start = '\begin{document}';
            $end = '\end{document}';

            $document = $top . PHP_EOL .
                $this->header . PHP_EOL .
                $start . PHP_EOL .
                $this->getContent() . PHP_EOL .
                $this->footer . PHP_EOL .
                $end . PHP_EOL;

            $this->document = $document;
        }

        return $this->document;
    }

    /**
     * @return string
     *
     * @throws MalformedJsonException
     * @throws MissingComponentException
     */
    public function getContent(): string
    {
        $trees = $this->array['trees'];
        $treeTexes = [];

        if ($trees === null) {
            throw new MalformedJsonException('Invalid tree structure. Cannot generate document.');
        }

        foreach ($trees as $treeLabel => $tree) {
            $treeObject = new Tree(json_encode($tree), $this->path);
            $treeTexes[$treeLabel] = $treeObject->getTex();
        }

        return implode(PHP_EOL . PHP_EOL . PHP_EOL, $treeTexes);
    }
}
