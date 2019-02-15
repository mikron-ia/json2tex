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
        return $array['label'] ?? str_replace(' ', '', ucwords($array['name']));
    }

    private function makeTraitName(array $array): string
    {
        if (!isset($array['name'])) {
            throw new MissingComponentException('Trait must have a name.');
        }
        return $array['name'] . ' [' . ($array['cost'] ?? '0') . ']';
    }

    /**
     * @param array $array
     * @return string
     * @todo Consider AND/OR
     */
    private function makeTraitLimit(array $array): string
    {
        $limitList = [];
        foreach ($array['allowedFor'] ?? [] as $limit) {
            if (is_array($limit)) {
                $base = $limit['value'];
                if ($limit['ref']) {
                    $suffix = ' (p. \pageref{' . $limit['ref'] . '})';
                } else {
                    $suffix = '';
                }
            } else {
                $base = $limit;
                $suffix = '';
            }
            $limitList[] = "{$base}{$suffix}";
        }

        if ($limitList) {
            return '\textit{' . implode(', ', $limitList) . ' only}';
        } else {
            return '';
        }
    }

    private function makeTraitRequirements(array $array): string
    {
        return implode(', ', $array['requirements'] ?? []);
    }

    private function makeTraitContent(array $array): string
    {
        if (!isset($array['content'])) {
            throw new MissingComponentException('Trait must have some content.');
        }
        return $array['content'];
    }

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

    private function makeCommandRequirements(): string
    {
        return $this->makeCommand('Requirements');
    }

    private function makeCommandContent(): string
    {
        return $this->makeCommand('Content');
    }

    private function dressStringInCommand(string $command, string $content): string
    {
        return "\\newcommand{$command}{{$content}}";
    }

    private function makeEntry(): string
    {
        $subtitle = $this->makeCommandLimit(); // @todo Add racial/culture/class limit, creation only tag

        return '\\subsubsection{' . $this->makeCommandName() . '}' . PHP_EOL
            . $subtitle . PHP_EOL
            . PHP_EOL
            . $this->makeCommandRequirements() . PHP_EOL
            . PHP_EOL
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
            $this->dressStringInCommand($this->makeCommandRequirements(), $this->makeTraitRequirements($array)),
            $this->dressStringInCommand($this->makeCommandContent(), $this->makeTraitContent($array))
        ]);
    }

    /**
     * @return string
     */
    public function getIndex(): string
    {
        if (empty($this->index)) {
            $this->index = $this->makeEntry();
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
