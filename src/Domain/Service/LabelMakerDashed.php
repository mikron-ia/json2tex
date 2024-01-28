<?php

namespace Mikron\json2tex\Domain\Service;

class LabelMakerDashed implements LabelMakerInterface
{
    public function makeLabel(?string $label = null, ?string $caption = null, ?string $prefix = null): string
    {
        if (!empty($label)) {
            return $this->encaseLabel($label);
        }

        return $this->encaseLabel($this->formatPrefixForLabel($prefix) . $this->extractLabelFromCaption($caption));
    }

    private function extractLabelFromCaption(?string $caption): string
    {
        return str_replace([' ', "\t"], '-', str_replace(['`', '\'', '"'], '', strtolower($caption)));
    }

    private function formatPrefixForLabel($prefix): string
    {
        return (isset($prefix) ? strtolower($prefix) . ':' : '');
    }

    private function encaseLabel(string $labelContent): string
    {
        return '\\label{' . $labelContent . '}';
    }
}
