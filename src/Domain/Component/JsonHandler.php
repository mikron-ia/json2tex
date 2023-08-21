<?php

namespace Mikron\json2tex\Domain\Component;

use Mikron\json2tex\Domain\Exception\MalformedJsonException;

trait JsonHandler
{
    /**
     * @param string $json
     *
     * @return array
     *
     * @throws MalformedJsonException
     */
    private function decodeJson(string $json): array
    {
        $result = json_decode($json, true);

        if ($result === null) {
            throw new MalformedJsonException('Cannot decode JSON.');
        }

        return $result;
    }
}
