<?php

namespace Mikron\json2tex\Tests;

use Mikron\json2tex\Domain\Entity\Document;
use PHPUnit\Framework\TestCase;

final class DocumentTest extends TestCase
{
    /**
     * @test
     */
    public function isDocumentCreatedCorrectly()
    {
        $character = new Document("");

        $this->assertInstanceOf('Mikron\json2tex\Domain\Entity\Document', $character);
    }

    /**
     * @test
     * @dataProvider correctDataProvider
     * @param array $data
     */
    public function isJsonReturnedCorrectly(array $data)
    {
        $json = json_encode($data);

        $character = new Document($json);

        $this->assertEquals($json, $character->getJson());
    }

    /**
     * @test
     * @dataProvider correctDataProvider
     * @param array $data
     */
    public function isArrayReturnedCorrectly(array $data)
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
