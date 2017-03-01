<?php

namespace Mikron\json2doc\Domain\Entity;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

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
     * @var PhpWord
     */
    private $document;

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

    public function getDocument():PhpWord
    {
        if (!$this->document) {
            $document = new PhpWord();

            $section = $document->addSection();

            $section->addText("Text");

            $this->document = $document;
        }

        return $this->document;
    }

    public function getString():string
    {
        if (!$this->document) {
            $this->document = $this->getDocument();
        }

        $writer = IOFactory::createWriter($this->document);

        ob_start();
        $writer->save('php://output');
        $output = ob_get_clean();

        return $output;
    }
}
