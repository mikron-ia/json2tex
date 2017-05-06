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
                InputArgument::REQUIRED,
                'Target TeX file'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $target = $input->getArgument('target');

        $json = file_get_contents($source);
        $document = new Document($json);

        file_put_contents($target, $document->getContent());

        $output->writeln("Done" . PHP_EOL);
    }
}
