<?php

namespace Mikron\json2tex\Domain\Entity;

use Mikron\json2tex\Domain\Exception\MalformedJsonException;
use Mikron\json2tex\Domain\Exception\MissingComponentException;

class Tree
{
    private array $array;
    private string $tex;
    private string $path;
    private string $figureBegin = "\t\\begin{tikzpicture}[scale=1,yscale=-1]
\t\t\\tikzset{
\t\t\tskill/.style={rectangle, rounded corners, draw=black, text centered, text width=5em, minimum height=4em},
\t\t\tarrowreq/.style={->, >=latex', shorten >=1pt, thick}
\t\t}";
    private string $figureEnd = "\t\\end{tikzpicture}";

    /**
     * @param string $json
     * @param string $path
     *
     * @throws MalformedJsonException
     */
    public function __construct(string $json, string $path = "")
    {
        $array = json_decode($json, true);

        if ($array === null) {
            throw new MalformedJsonException('Wrong JSON format');
        }

        $this->array = $array;
        $this->path = $path;
    }

    private function makeCompleteTreeDrawing(string $interior): string
    {
        $caption = $this->array['caption'] ?? '';
        $aura = isset($this->array['aura']) ?
            '\\subsubsection{Aura}' . PHP_EOL . PHP_EOL .
            str_replace('\n', PHP_EOL, implode(PHP_EOL . PHP_EOL, $this->array['aura'])) :
            '';
        $description = isset($this->array['description']) ?
            str_replace('\n', PHP_EOL, implode(PHP_EOL . PHP_EOL, $this->array['description'])) :
            '';

        $begin = <<<TREESTART
\\subsection{{$caption}}

$description

\\begin{figure}[ht]
\t\\centering
$this->figureBegin

TREESTART;

        $end = <<<TREEEND
$this->figureEnd    
\t\\caption{{$caption}}    
\\end{figure}

$aura

\\subsubsection{Powers}

TREEEND;

        return $begin . $interior . $end . implode(PHP_EOL . PHP_EOL, $this->descriptions());
    }

    /**
     * @return string[]
     */
    private function descriptions(): array
    {
        $descriptions = [];

        if (isset($this->array['descriptions'])) {
            $skills = $this->array['descriptions'] ?? [];

            foreach ($skills as $skill) {
                $label = $skill['name'];

                $insides = $this->makeInsides($skill);

                $description = <<<DESCRIPTION

\\power{{$label}}

$insides
DESCRIPTION;

                $descriptions[$label] = $description;

                ksort($descriptions);
            }
        } else {
            $skills = $this->array['skills'] ?? [];

            foreach ($skills as $skill) {
                $label = $skill['name'];
                $rank = $skill['rank'];

                $insides = $this->makeInsides($skill);

                $description = <<<DESCRIPTION

\\power{{$label}}
\\textit{Rank {$rank}}

$insides
DESCRIPTION;

                $descriptions[] = $description;
            }
        }

        return $descriptions;
    }

    /**
     * @param array $skills
     *
     * @return int[]
     */
    private function orderNodesByRank(array $skills): array
    {
        $nodesByRank = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($skills as $skill => $unorderedNode) {
            $node = $unorderedNode;
            $node['label'] = $skill;

            if (!isset($nodesByRank[$unorderedNode['rank']])) {
                $nodesByRank[$unorderedNode['rank']] = 0;
            }
            $nodesByRank[$unorderedNode['rank']] += 1;
        }

        return $nodesByRank;
    }

    /**
     * @param array $skills
     *
     * @return array
     */
    private function prepareSkillsAsNodes(array $skills): array
    {
        $nodes = [];

        foreach ($skills as $skill => $unorderedNode) {
            $node = $unorderedNode;
            $node['label'] = $skill;
            $nodes[] = $node;
        }

        return $nodes;
    }

    /**
     * @param array $nodes
     *
     * @return string
     */
    private function turnSkillsIntoNodes(array $nodes): string
    {
        $content = '';

        foreach ($nodes as $node) {
            $content .= "\t\t\t"
                . '\node[skill] at '
                . '(' . $node['x'] . ', ' . $node['y'] . ') '
                . '(' . $node['label'] . ') '
                . '{' . $node['name'] . '};'
                . PHP_EOL;
        }

        return $content;
    }

    /**
     * @param array $nodes Nodes to add
     * @param array $nodesByLabel List of nodes, keyed by label
     *
     * @return string
     *
     * @throws MissingComponentException
     */
    private function prepareDrawnLines(array $nodes, array $nodesByLabel): string
    {
        $content = '';

        foreach ($nodes as $node) {
            foreach ($node['requires'] ?? [] as $requirementLabel) {
                if (empty($nodesByLabel[$requirementLabel])) {
                    throw new MissingComponentException('Node ' . $requirementLabel . ' not found');
                }
                $content .= "\t\t\t" . '\draw[arrowreq] ('
                    . $nodesByLabel[$requirementLabel]['label'] . '.south) -- ('
                    . $node['label'] . '.north);'
                    . PHP_EOL;
            }
        }

        return $content;
    }

    /**
     * @return string
     *
     * @throws MissingComponentException
     */
    private function makeTreeInterior(): string
    {
        /* Init */
        $content = '';
        $nodesByRankPosition = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $unitByRank = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $nodesByLabel = [];

        $nodes = $this->prepareSkillsAsNodes($this->array['skills'] ?? []);
        $nodesByRank = $this->orderNodesByRank($this->array['skills'] ?? []);

        /* Draw scale on the left */
        $scale = '';
        $content .= $scale;

        /* Calculate tree width */
        $width = $this->array['width'] ?? max($nodesByRank);

        /* Draw nodes */
        for ($i = 1; $i <= 5; $i++) {
            if ($nodesByRank[$i] > 0) {
                $unitByRank[$i] = ceil($width / $nodesByRank[$i]) + 2;
            } else {
                $unitByRank[$i] = null;
            }

            $nodesByRankPosition[$i] = 0;
        }

        $nodeCount = count($nodes);

        for ($i = 0; $i < $nodeCount; $i++) {
            if ($unitByRank[$nodes[$i]['rank']]) {
                $unitX = ceil(20 / $width);

                if (!isset($nodes[$i]['position'])) {
                    $nodes[$i]['position'] = $nodesByRankPosition[$nodes[$i]['rank']];
                }

                $nodes[$i]['x'] = $unitX * $nodes[$i]['position'];
                $nodes[$i]['y'] = ($nodes[$i]['rank'] - 1) * 3;

                $nodesByRankPosition[$nodes[$i]['rank']]++;

                $nodesByLabel[$nodes[$i]['label']] = $nodes[$i];
            }
        }

        $content .= $this->turnSkillsIntoNodes($nodes);

        /* Draw the lines */
        $content .= $this->prepareDrawnLines($nodes, $nodesByLabel);

        return $content;
    }

    private function makeInsides(array $skill): string
    {
        $insides = "[file not found]";

        if (isset($skill['description'])) {
            $insides = str_replace('\n', PHP_EOL, implode(PHP_EOL . PHP_EOL, $skill['description']));
        } elseif (isset($skill['file'])) {
            $path = $this->path . $skill['file'];
            if (file_exists($path)) {
                $insides = file_get_contents($path);
            }
        }

        return $insides;
    }

    /**
     * @return string
     *
     * @throws MissingComponentException
     */
    public function getTex(): string
    {
        if (empty($this->tex)) {
            $interior = $this->makeTreeInterior();
            $this->tex = $this->makeCompleteTreeDrawing($interior);
        }

        return $this->tex;
    }
}
