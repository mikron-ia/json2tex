<?php

namespace Mikron\json2tex\Infrastructure;

/**
 * Class ConvertTreeCommand
 * @package Mikron\json2tex\Infrastructure
 */
class ConvertLegacyCommand extends ConvertTreeCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('convert:convert')
            ->setDescription('Convert ability tree in JSON to TeX (deprecated, use convert:tree)');
    }
}
