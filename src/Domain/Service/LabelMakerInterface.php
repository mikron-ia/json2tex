<?php

namespace Mikron\json2tex\Domain\Service;

interface LabelMakerInterface
{
    public function makeLabel(?string $label, ?string $caption, ?string $prefix): string;
}