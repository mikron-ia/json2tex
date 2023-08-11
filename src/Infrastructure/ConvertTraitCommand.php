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
    private const EXIT_WITH_SUCCESS = 0;
    private const EXIT_WITH_ERROR = 1;

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
     * @return int
     *
     * @throws MalformedJsonException
     * @throws MissingComponentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
        $target = $input->getArgument('target');
        $index = $input->getArgument('index');

        $json = file_get_contents($source);

        if (!$json) {
            $output->writeln('Unable to read source file.');
            return self::EXIT_WITH_ERROR;
        }

        $document = new AdvantagePack($json);

        if (
            file_put_contents($target, $document->getContent()) === false ||
            file_put_contents($index, $document->getIndex()) === false
        ) {
            $output->writeln('Unable to write target file.');
            return self::EXIT_WITH_ERROR;
        }

        return self::EXIT_WITH_SUCCESS;
    }
}
