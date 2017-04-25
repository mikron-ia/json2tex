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
     * Tree constructor.
     * @param $json
     * @throws MalformedJsonException
     */
    public function __construct($json)
    {
        $this->json = $json;
        $this->array = json_decode($json, true);

        if ($this->array === null) {
            throw new MalformedJsonException('Wrong JSON format');
        }
    }

    private function makeCompleteTree(string $interior)
    {
        $caption = $this->array['caption']??'';

        $begin = <<<'TREESTART'
\begin{figure}[h]
    \centering
    \begin{tikzpicture}[scale=1,yscale=-1]
    	\tikzset{
    		skill/.style={rectangle, rounded corners, draw=black, text centered, text width=8em, minimum height=3em},
    		arrowreq/.style={->, >=latex', shorten >=1pt, thick},
    	}

TREESTART;

        $end = <<<TREEEND
        
\\end{tikzpicture}
    
    \\caption{{$caption}}
\\end{figure}
TREEEND;

        return $begin . $interior . $end;
    }

    private function makeTreeInterior()
    {
        /* Init */
        $content = '';
        $nodesByRank = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $nodesByRankPosition = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $unitByRank = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $nodes = [];
        $nodesByLabel = [];

        foreach ($this->array['skills'] as $skill => $unorderedNode) {
            $node = $unorderedNode;
            $node['label'] = $skill;
            $nodes[] = $node;

            if (!isset($nodesByRank[$unorderedNode['rank']])) {
                $nodesByRank[$unorderedNode['rank']] = 0;
            }
            $nodesByRank[$unorderedNode['rank']] += 1;
        }

        /* Calculate tree width */
        $width = max($nodesByRank) * 4;

        for ($i = 1; $i <= 5; $i++) {
            $unitByRank[$i] = ceil($width / ($nodesByRank[$i] + 1)) * 4;
            $nodesByRankPosition[$i] = 0;
        }

        /* Draw scale on the left */

        /* Draw nodes */

        $nodeCount = count($nodes);

        for ($i = 0; $i < $nodeCount; $i++) {
            $unitX = ceil($unitByRank[$nodes[$i]['rank']] / 2);

            $nodes[$i]['x'] = $unitX * ($nodesByRankPosition[$nodes[$i]['rank']] + 1);
            $nodes[$i]['y'] = ($nodes[$i]['rank'] - 1) * 2;

            $nodesByRankPosition[$nodes[$i]['rank']]++;

            $nodesByLabel[$nodes[$i]['label']] = $nodes[$i];
        }

        foreach ($nodes as $node) {
            $content .= '\node[skill] at (' . $node['x'] . ', ' . $node['y'] . ') (' . $node['label'] . ') {' . $node['name'] . '};' . PHP_EOL;
        }

        /* Draw lines */

        foreach ($nodes as $node) {
            if (!empty($node['requires'])) {
                foreach ($node['requires'] as $requirementLabel) {
                    $requiredNode = $nodesByLabel[$requirementLabel];
                    $meanY = ceil(($requiredNode['y'] + $node['y']) / 2);
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
            $this->tex = $this->makeCompleteTree($interior);
        }

        return $this->tex;
    }
}
