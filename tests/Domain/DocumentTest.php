<?php

namespace Mikron\json2doc\Tests;

use Mikron\json2doc\Domain\Entity\Document;
use PHPUnit_Framework_TestCase;

final class DocumentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function isDocumentCreatedCorrectly()
    {
        $character = new Document("");

        $this->assertInstanceOf('Mikron\json2doc\Domain\Entity\Document', $character);
    }


    public function correctDataProvider()
    {
        return [
            [
                json_encode([
                    "data" => "data",
                ])
            ],
            [
                json_encode([
                    "name" => "name",
                    "data" => "data",
                ])
            ],
        ];
    }
}
