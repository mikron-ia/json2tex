<?php

namespace Mikron\json2tex\Domain\Entity;

use Mikron\json2tex\Domain\Exception\MalformedJsonException;

class Tree
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
     * @var Skill[]
     */
    private $skills;
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
    }

    private function makeCompleteTreeDrawing(string $interior)
    {
        $caption = $this->array['caption']??'';
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

\\begin{figure}[h]
    \centering
    \\begin{tikzpicture}[scale=1,yscale=-1]
    	\\tikzset{
    		skill/.style={rectangle, rounded corners, draw=black, text centered, text width=5em, minimum height=3em},
    		arrowreq/.style={->, >=latex', shorten >=1pt, thick},
    	}

TREESTART;

        $end = <<<TREEEND
        
\\end{tikzpicture}
    
    \\caption{{$caption}}
    
\\end{figure}

$aura

\\subsubsection{Powers}

TREEEND;

        return $begin . $interior . $end . implode(PHP_EOL . PHP_EOL, $this->descriptions());
    }

    /**
     * @return string[]
     */
    private function descriptions():array
    {
        $descriptions = [];

        $skills = $this->array['skills']??[];

        foreach ($skills as $skill) {
            $label = $skill['name'];
            $rank = $skill['rank'];

            if (isset($skill['description'])) {
                $insides = str_replace('\n', PHP_EOL, implode(PHP_EOL . PHP_EOL, $skill['description']));
            } elseif (isset($skill['file'])) {
                $path = $this->path . $skill['file'];
                if (file_exists($path)) {
                    $insides = file_get_contents($path);
                } else {
                    $insides = "[file not found]";
                }
            } else {
                $insides = "[no data found]";
            }

            $description = <<<DESCRIPTION

\\power{{$label}}
\\textit{Rank {$rank}}

$insides
DESCRIPTION;

            $descriptions[] = $description;
        }

        return $descriptions;
    }

    private function orderNotesByRank()
    {
        $skills = $this->array['skills']??[];
        $nodesByRank = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($skills as $skill => $unorderedNode) {
            $node = $unorderedNode;
            $node['label'] = $skill;
            $nodes[] = $node;

            if (!isset($nodesByRank[$unorderedNode['rank']])) {
                $nodesByRank[$unorderedNode['rank']] = 0;
            }
            $nodesByRank[$unorderedNode['rank']] += 1;
        }

        return $nodesByRank;
    }

    private function makeTreeInterior()
    {
        /* Init */
        $content = '';
        $nodesByRankPosition = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $unitByRank = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $nodes = [];
        $nodesByLabel = [];

        $skills = $this->array['skills']??[];

        $nodesByRank = $this->orderNotesByRank();

        foreach ($skills as $skill => $unorderedNode) {
            $node = $unorderedNode;
            $node['label'] = $skill;
            $nodes[] = $node;
        }

        /* Calculate tree width */
        $width = max($nodesByRank);

        for ($i = 1; $i <= 5; $i++) {
            //$unitByRank[$i] = ceil($width * 3 / ($nodesByRank[$i] + 1)) * 3;
            if ($nodesByRank[$i] > 0) {
                $unitByRank[$i] = ceil($width / $nodesByRank[$i]);
            } else {
                $unitByRank[$i] = null;
            }

            $nodesByRankPosition[$i] = 0;
        }

        /* Draw scale on the left */
        $scale = '';
        $content .= $scale;

        /* Draw nodes */

        $nodeCount = count($nodes);

        for ($i = 0; $i < $nodeCount; $i++) {
            if ($unitByRank[$nodes[$i]['rank']]) {
                $unitX = $unitByRank[$nodes[$i]['rank']] * 4;

                $nodes[$i]['x'] = $unitX * $nodesByRankPosition[$nodes[$i]['rank']];
                $nodes[$i]['y'] = ($nodes[$i]['rank'] - 1) * 3;

                $nodesByRankPosition[$nodes[$i]['rank']]++;

                $nodesByLabel[$nodes[$i]['label']] = $nodes[$i];
            }
        }

        foreach ($nodes as $node) {
            $content .= '\node[skill] at (' . $node['x'] . ', ' . $node['y'] . ') (' . $node['label'] . ') {' . $node['name'] . '};' . PHP_EOL;
        }

        /* Draw lines */

        foreach ($nodes as $node) {
            if (!empty($node['requires'])) {
                foreach ($node['requires'] as $requirementLabel) {
                    $requiredNode = $nodesByLabel[$requirementLabel];
                    //$meanY = ceil(($requiredNode['y'] + $node['y']) / 2);
                    $meanY = ceil($requiredNode['y'] + 1);
                    $content .= '\draw[arrowreq] ('
                        . $requiredNode['label'] . '.south) -- ('
                        . $requiredNode['x'] . ', ' . $meanY . ') -- ('
                        . $node['x'] . ',' . $meanY . ') -- ('
                        . $node['label'] . '.north);'
                        . PHP_EOL;
                }
            }
        }
        return $content;
    }

    /**
     * @return string
     */
    public function getTex()
    {
        if (empty($this->tex)) {
            $interior = $this->makeTreeInterior();
            $this->tex = $this->makeCompleteTreeDrawing($interior);
        }

        return $this->tex;
    }
}
