<?php

namespace Mikron\json2tex\Domain\Entity;


use Mikron\json2tex\Domain\Exception\MalformedJsonException;
use Mikron\json2tex\Domain\Exception\MissingComponentException;

/**
 * Class Advantage
 * @package Mikron\json2tex\Domain\Entity
 * @note This should be called 'Trait', but cannot be. It represents both Advantages and Disadvantages.
 */
class Advantage
{
    const CREATION_ONLY_TAG = '\characterCreationOnly';

    /**
     * @var string
     */
    private $json;

    /**
     * @var array
     */
    private $array;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $tex;

    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $requirements;

    /**
     * Tree constructor.
     * @param $json
     * @param $path
     * @throws MalformedJsonException
     */
    public function __construct($json, $path = "")
    {
        $this->json = $json;
        $this->array = json_decode($json, true);

        if ($this->array === null) {
            throw new MalformedJsonException('Wrong JSON format');
        }

        $this->path = $path;

        $this->label = $this->makeTraitLabel($this->array);
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->array;
    }

    private function makeTraitLabel(array $array): string
    {
        return $array['label'] ?? preg_replace(
                '/[\s]/u',
                '',
                ucwords(preg_replace('/[^a-zA-Z\s]/u', '', $array['name']))
            );
    }

    private function makeTraitName(array $array): string
    {
        if (!isset($array['name'])) {
            throw new MissingComponentException('Trait must have a name.');
        }
        return $array['name'] . ' [' . ($array['cost'] ?? '0') . ']';
    }

    private function makeTraitType(array $array): string
    {
        return ucfirst($array['type'] ?? 'generic') . ' trait';
    }

    private function isCreationOnly(array $array)
    {
        return $array['creationOnly'] ?? false;
    }

    /**
     * @param array $array
     * @return string
     * @todo Consider AND/OR
     */
    private function makeTraitLimit(array $array): string
    {
        $limitList = [];
        foreach ($array['limitedTo'] ?? [] as $limit) {
            if (is_array($limit)) {
                $base = $limit['name'];
                if ($limit['label']) {
                    $suffix = ' (p. \pageref{' . $limit['label'] . '})';
                } else {
                    $suffix = '';
                }
            } else {
                $base = $limit;
                $suffix = '';
            }
            $limitList[] = "{$base}{$suffix}";
        }

        $traitType = $this->makeTraitType($this->array);
        $creationOnly = $this->isCreationOnly($array) ? self::CREATION_ONLY_TAG : '';

        if ($limitList) {
            return '\textit{' . $traitType . '}' . PHP_EOL . PHP_EOL
                . '\textit{' . ucfirst(implode(', ', $limitList)) . ' only}' . PHP_EOL . PHP_EOL
                . $creationOnly;
        } else {
            return '\textit{' . $traitType . '}' . PHP_EOL . PHP_EOL
                . $creationOnly;
        }
    }

    /**
     * @param array $array
     * @return string
     */
    private function makeTraitCommonality(array $array, string $key, string $text): string
    {
        $list = [];
        foreach ($array[$key] ?? [] as $limit) {
            if (is_array($limit)) {
                $base = $limit['name'];
                if ($limit['label']) {
                    $suffix = ' (p. \pageref{' . $limit['label'] . '})';
                } else {
                    $suffix = '';
                }
            } else {
                $base = $limit;
                $suffix = '';
            }
            $list[] = "{$base}{$suffix}";
        }

        if ($list) {
            return '\textit{' . $text . ': ' . implode(', ', $list) . '}';
        } else {
            return '';
        }
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
        if (empty($this->requirements) && !empty($array['requirements'])) {
            $this->requirements = '\textit{' . ucfirst(implode(', ', $array['requirements'])) . '}';
        } else {
            $this->requirements = '';
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

    /**
     * @param string $code
     * @return string
     */
    private function makeCommand(string $code): string
    {
        return "\\trait{$this->label}{$code}";
    }

    private function makeCommandName(): string
    {
        return $this->makeCommand('Name');
    }

    private function makeCommandLimit(): string
    {
        return $this->makeCommand('Limit');
    }

    private function makeCommandCommon(): string
    {
        return $this->makeCommand('Common');
    }

    private function makeCommandRare(): string
    {
        return $this->makeCommand('Rare');
    }

    private function makeCommandRequirements(): string
    {
        return $this->makeCommand('Requirements');
    }

    private function makeCommandContent(): string
    {
        return $this->makeCommand('Content');
    }

    private function makeCommandPackLite(): string
    {
        return $this->makeCommand('PackLite');
    }

    private function makeLabel(): string
    {
        return "\\label{trait{$this->label}}";
    }

    private function dressStringInCommand(string $command, string $content): string
    {
        return "\\newcommand{{$command}}{{$content}}";
    }

    private function makeEntryFull(): string
    {
        $subtitle = $this->makeCommandLimit();
        $requirements = !empty($this->requirements) ? ($this->makeCommandRequirements()) : '';

        return '\subsubsection{' . $this->makeCommandName() . '}' . $this->makeLabel()
            . PHP_EOL . PHP_EOL
            . $subtitle
            . PHP_EOL . PHP_EOL
            . $requirements
            . PHP_EOL . PHP_EOL
            . ucfirst($this->makeCommandCommon())
            . PHP_EOL . PHP_EOL
            . ucfirst($this->makeCommandRare())
            . PHP_EOL . PHP_EOL
            . $this->makeCommandContent();
    }

    private function makeEntryLite(): string
    {
        return '\subsubsection{' . $this->makeCommandName() . '}'
            . PHP_EOL . PHP_EOL
            . $this->makeCommandRequirements()
            . PHP_EOL . PHP_EOL
            . $this->makeCommandCommon()
            . PHP_EOL . PHP_EOL
            . $this->makeCommandRare()
            . PHP_EOL . PHP_EOL
            . $this->makeCommandContent();
    }

    /**
     * @param array $array
     * @return string
     * @throws MissingComponentException
     */
    public function makeCompleteTex(array $array): string
    {
        return implode(PHP_EOL, [
            $this->dressStringInCommand($this->makeCommandName(), $this->makeTraitName($array)),
            $this->dressStringInCommand($this->makeCommandLimit(), $this->makeTraitLimit($array)),
            $this->dressStringInCommand($this->makeCommandCommon(), $this->makeTraitCommon($array)),
            $this->dressStringInCommand($this->makeCommandRare(), $this->makeTraitRare($array)),
            $this->dressStringInCommand($this->makeCommandRequirements(), $this->makeTraitRequirements($array)),
            $this->dressStringInCommand($this->makeCommandContent(), $this->makeTraitContent($array)),
            PHP_EOL,
            $this->dressStringInCommand($this->makeCommandPackLite(), $this->makeEntryLite())
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
