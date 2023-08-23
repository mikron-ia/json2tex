<?php

namespace Mikron\json2tex\Domain\Entity;

use Mikron\json2tex\Domain\Component\CommandSuffix;
use Mikron\json2tex\Domain\Exception\MalformedJsonException;
use Mikron\json2tex\Domain\Exception\MissingComponentException;

/**
 * Class Advantage
 *
 * @package Mikron\json2tex\Domain\Entity
 *
 * @note This should be called 'Trait', but cannot be due to PHP reserved words
 * @note It represents both Advantages and Disadvantages.
 */
class Advantage
{
    private const CREATION_ONLY_TAG = '\characterCreationOnly';

    /**
     * @var string
     */
    private string $json;

    /**
     * @var array|null
     */
    private ?array $array;

    /**
     * @var string
     */
    private string $label;

    /**
     * @var string
     */
    private string $tex;

    /**
     * @var string
     */
    private string $index;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var string
     */
    private string $requirements;

    /**
     * Tree constructor.
     *
     * @param string $json
     * @param string $path
     *
     * @throws MalformedJsonException
     */
    public function __construct(string $json, string $path = "")
    {
        $this->json = $json;
        $this->array = json_decode($json, true);

        if ($this->array === null) {
            throw new MalformedJsonException('Wrong JSON format');
        }

        $this->path = $path;

        $this->label = $this->makeTraitLabel($this->array);
    }

    private function makeTraitLabel(array $array): string
    {
        return $array['label']
            ?? preg_replace(
                '/[\s]/u',
                '',
                ucwords(preg_replace('/[^a-zA-Z\s]/u', '', $array['name']))
            );
    }

    /**
     * @param array $array
     *
     * @return string
     *
     * @throws MissingComponentException
     */
    private function makeTraitName(array $array): string
    {
        if (!isset($array['name'])) {
            throw new MissingComponentException('Trait must have a name.');
        }
        return $array['name'];
    }

    private function makeTraitCost(array $array): string
    {
        return $array['cost'] ?? '0';
    }

    private function makeTraitType(array $array): string
    {
        return ucfirst($array['type'] ?? 'generic') . ' trait';
    }

    private function makeTraitGroup(array $array): string
    {
        return isset($array['group']) ? (' (' . ucfirst($array['group']) . ' group)') : '';
    }

    private function isCreationOnly(array $array): bool
    {
        return $array['creationOnly'] ?? false;
    }

    private function makeTraitTag(array $array): string
    {
        return '\textit{' . $this->makeTraitType($array) . $this->makeTraitGroup($array) . '}';
    }

    /**
     * @param array $array
     * @return string
     */
    private function makeTraitLimit(array $array): string
    {
        $limitList = $this->makeTraitDescribingList($array, 'limitedTo');
        $creationOnly = $this->isCreationOnly($array) ? self::CREATION_ONLY_TAG : '';

        if ($limitList) {
            return '\textit{' . ucfirst(implode(', ', $limitList)) . ' only}'
                . PHP_EOL . PHP_EOL
                . $creationOnly;
        } else {
            return $creationOnly;
        }
    }

    /**
     * @param array $array
     * @param string $key
     * @param string $text
     *
     * @return string
     */
    private function makeTraitCommonality(array $array, string $key, string $text): string
    {
        $list = $this->makeTraitDescribingList($array, $key);
        $commonality = '';

        if ($list) {
            $commonality = '\textit{' . $text . ': ' . implode(', ', $list) . '}';
        }

        return $commonality;
    }

    private function makeTraitCommon(array $array): string
    {
        return $this->makeTraitCommonality($array, 'commonFor', 'Common for');
    }

    private function makeTraitRare(array $array): string
    {
        return $this->makeTraitCommonality($array, 'rareFor', 'Rare for');
    }

    private function makeTraitRequirements(array $array): string
    {
        $this->requirements = '';

        if (empty($this->requirements) && !empty($array['requirements'])) {
            $this->requirements = '\textit{' . ucfirst(implode(', ', $array['requirements'])) . '}';
        }

        return $this->requirements;
    }

    /**
     * @param array $array
     * @return string
     * @throws MissingComponentException
     */
    private function makeTraitContent(array $array): string
    {
        if (!isset($array['content'])) {
            throw new MissingComponentException('Trait must have some content.');
        }
        return $array['content'];
    }

    private function makeTraitDescribingList(array $array, string $key): array
    {
        $list = [];

        foreach ($array[$key] ?? [] as $limit) {
            $suffix = '';
            if (is_array($limit)) {
                $base = $limit['name'];
                if ($limit['label']) {
                    $suffix = ' (p. \pageref{' . $limit['label'] . '})';
                }
            } else {
                $base = $limit;
            }
            $list[] = "{$base}{$suffix}";
        }

        return $list;
    }

    /**
     * @param CommandSuffix $code
     *
     * @return string
     */
    private function makeCommand(CommandSuffix $code): string
    {
        return "\\trait{$this->label}{$code->value}";
    }

    private function makeLabel(): string
    {
        return "\\label{trait{$this->label}}";
    }

    private function dressInCommand(string $command, string $content): string
    {
        return "\\newcommand{{$command}}{{$content}}";
    }

    private function makeEntryFull(): string
    {
        $requirements = !empty($this->requirements) ? ($this->makeCommand(CommandSuffix::Requirements)) : '';

        return '\subsubsection{'
            . '{' . $this->makeCommand(CommandSuffix::Name) . '} '
            . '[' . $this->makeCommand(CommandSuffix::Cost) . ']'
            . '}'
            . $this->makeLabel()
            . PHP_EOL . PHP_EOL
            . $this->makeCommand(CommandSuffix::Tag)
            . PHP_EOL . PHP_EOL
            . $this->makeCommand(CommandSuffix::Limit)
            . PHP_EOL . PHP_EOL
            . $requirements
            . PHP_EOL . PHP_EOL
            . $this->makeCommand(CommandSuffix::Common)
            . PHP_EOL . PHP_EOL
            . $this->makeCommand(CommandSuffix::Rare)
            . PHP_EOL . PHP_EOL
            . $this->makeCommand(CommandSuffix::Content);
    }

    private function makeEntryLite(): string
    {
        return '\subsubsection{' .
            '{' . $this->makeCommand(CommandSuffix::Name) . '} '
            . '[' . $this->makeCommand(CommandSuffix::Cost) . ']'
            . '}'
            . PHP_EOL . PHP_EOL
            . $this->makeCommand(CommandSuffix::Tag)
            . PHP_EOL . PHP_EOL
            . $this->makeCommand(CommandSuffix::Requirements)
            . PHP_EOL . PHP_EOL
            . $this->makeCommand(CommandSuffix::Common)
            . PHP_EOL . PHP_EOL
            . $this->makeCommand(CommandSuffix::Rare)
            . PHP_EOL . PHP_EOL
            . $this->makeCommand(CommandSuffix::Content);
    }

    /**
     * @param array $array
     * @return string
     * @throws MissingComponentException
     */
    public function makeCompleteTex(array $array): string
    {
        return implode(PHP_EOL, [
            $this->dressInCommand($this->makeCommand(CommandSuffix::Name), $this->makeTraitName($array)),
            $this->dressInCommand($this->makeCommand(CommandSuffix::Cost), $this->makeTraitCost($array)),
            $this->dressInCommand($this->makeCommand(CommandSuffix::Tag), $this->makeTraitTag($array)),
            $this->dressInCommand($this->makeCommand(CommandSuffix::Limit), $this->makeTraitLimit($array)),
            $this->dressInCommand($this->makeCommand(CommandSuffix::Common), $this->makeTraitCommon($array)),
            $this->dressInCommand($this->makeCommand(CommandSuffix::Rare), $this->makeTraitRare($array)),
            $this->dressInCommand($this->makeCommand(CommandSuffix::Requirements),
                $this->makeTraitRequirements($array)),
            $this->dressInCommand($this->makeCommand(CommandSuffix::Content), $this->makeTraitContent($array)),
            PHP_EOL,
            $this->dressInCommand($this->makeCommand(CommandSuffix::PackLite), $this->makeEntryLite())
        ]);
    }

    /**
     * @return string
     */
    public function getIndex(): string
    {
        if (empty($this->index)) {
            $this->index = $this->makeEntryFull();
        }

        return $this->index;
    }

    /**
     * @return string
     *
     * @throws MissingComponentException
     */
    public function getTex(): string
    {
        if (empty($this->tex)) {
            $this->tex = $this->makeCompleteTex($this->array);
        }

        return $this->tex;
    }
}
