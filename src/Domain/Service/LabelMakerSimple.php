<?php

namespace Mikron\json2tex\Domain\Service;

class LabelMakerSimple implements LabelMakerInterface
{
    public function makeLabel(?string $label = null, ?string $caption = null, ?string $prefix = null): string
    {
        if (isset($label)) {
            return empty($label) ? '' : $this->encaseLabel($label);
        }

        return $this->encaseLabel($this->formatPrefixForLabel($prefix) . $this->extractLabelFromCaption($caption));
    }

    public function extractLabelFromCaption(?string $caption): string
    {
        return preg_replace(
            '/-+/',
            '-',
            preg_replace(
                '/\s/u',
                '-',
                strtolower(preg_replace('/[^a-zA-Z\s]/u', '', $caption))
            ));
    }

    public function formatPrefixForLabel($prefix): string
    {
        return (!empty($prefix) ? strtolower($prefix) . ':' : '');
    }

    public function encaseLabel(string $labelContent): string
    {
        return '\\label{' . $labelContent . '}';
    }
}
