<?php

namespace Mikron\json2tex\Infrastructure;

use Mikron\json2tex\Domain\Entity\AdvantagePack;
use Mikron\json2tex\Domain\Exception\MalformedJsonException;
use Mikron\json2tex\Domain\Exception\MissingComponentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConvertTreeCommand
 *
 * @package Mikron\json2tex\Infrastructure
 */
class ConvertTraitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('convert:trait')
            ->setDescription('Convert traits description in JSON to TeX')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'JSON file'
            )
            ->addArgument(
                'target',
                InputArgument::REQUIRED,
                'Target TeX file for \\newcommand output'
            )
            ->addArgument(
                'index',
                InputArgument::OPTIONAL,
                'Target TeX file for complete list of traits'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     *
     * @throws MalformedJsonException
     * @throws MissingComponentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $source = $input->getArgument('source');
        $target = $input->getArgument('target');
        $index = $input->getArgument('index');

        $json = file_get_contents($source);

        if (!$json) {
            $output->writeln('Unable to read source file.');
        } else {
            $document = new AdvantagePack($json);
            if (file_put_contents($target, $document->getContent()) === false) {
                $output->writeln('Unable to write target file.');
            }
            if (file_put_contents($index, $document->getIndex()) === false) {
                $output->writeln('Unable to write target file.');
            }
        }
    }
}
