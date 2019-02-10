<?php

namespace Mikron\json2tex\Infrastructure;

use Mikron\json2tex\Domain\Entity\Document;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConvertTreeCommand
 * @package Mikron\json2tex\Infrastructure
 */
class ConvertTreeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('convert:tree')
            ->setDescription('Convert ability tree in JSON to TeX')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'JSON file'
            )
            ->addArgument(
                'target',
                InputArgument::REQUIRED,
                'Target TeX file'
            )
            ->addArgument(
                'filePath',
                InputArgument::OPTIONAL,
                'Path to auxiliary files'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Mikron\json2tex\Domain\Exception\MalformedJsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $target = $input->getArgument('target');
        $path = $input->getArgument('filePath');

        $json = file_get_contents($source);

        if (!$json) {
            $output->writeln('Unable to read source file.');
        } else {
            $document = new Document($json, $path);
            if (file_put_contents($target, $document->getContent()) === false) {
                $output->writeln('Unable to write target file.');
            }
        }
    }
}
