<?php

namespace Mikron\json2tex\Infrastructure;

use Mikron\json2tex\Domain\Entity\Document;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConvertCommand
 * @package Mikron\json2tex\Infrastructure
 */
class ConvertCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('convert:convert')
            ->setDescription('Convert JSON to TeX')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'JSON file'
            )
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Target TeX file'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $target = $input->getArgument('target');

        $json = file_get_contents(__DIR__ . '/' . $source);
        $document = new Document($json);

        $output->writeln($document->getContent());
    }
}
