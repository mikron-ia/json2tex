<?php

namespace Mikron\json2tex\Tests;

use Mikron\json2tex\Domain\Entity\Document;
use Mikron\json2tex\Domain\Exception\MalformedJsonException;
use PHPUnit\Framework\TestCase;

final class DocumentTest extends TestCase
{
    public function testIsDocumentCreatedOnEmpty()
    {
        $this->expectException(MalformedJsonException::class);
        new Document('');
    }

    public function testIsDocumentCreatedAtAll()
    {
        $document = new Document('{}');

        $this->assertInstanceOf('Mikron\json2tex\Domain\Entity\Document', $document);
    }

    public function testIsDocumentCreatedCorrectly()
    {
        $document = new Document('{"trees": []}');

        $expected = '\documentclass[a4paper,final,12pt]{memoir}
\providecommand\locale{english}

\usepackage{tikz}
\usetikzlibrary{arrows,positioning}' . PHP_EOL . PHP_EOL
            . '\begin{document}' . PHP_EOL . PHP_EOL
            . PHP_EOL
            . '\end{document}' . PHP_EOL;

        $this->assertEquals($expected, $document->getDocument());
    }

    /**
     * @dataProvider correctDataProvider
     * @param array $data
     *
     * @throws MalformedJsonException
     */
    public function testIsJsonReturnedCorrectly(array $data)
    {
        $json = json_encode($data);

        $character = new Document($json);

        $this->assertEquals($json, $character->getJson());
    }

    /**
     * @dataProvider correctDataProvider
     * @param array $data
     *
     * @throws MalformedJsonException
     */
    public function testIsArrayReturnedCorrectly(array $data)
    {
        $json = json_encode($data);

        $character = new Document($json);

        $this->assertEquals($data, $character->getArray());
    }

    public static function correctDataProvider(): array
    {
        return [
            [
                [
                    "data" => "data",
                ]
            ],
            [
                [
                    "name" => "name",
                    "data" => "data",
                ]
            ],
        ];
    }
}
