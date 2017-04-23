<?php

namespace Mikron\json2tex\Domain\Entity;

/**
 * Class Document
 * @package Mikron\json2tex\Domain\Entity
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
     * @var string
     */
    private $document;

    /**
     * @var string
     */
    private $header = '';

    private $footer = '';

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

    private function getContent():string
    {
        return "Text";
    }

    public function getDocument():string
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
}
