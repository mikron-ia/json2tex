<?php

namespace Mikron\json2tex\Domain\Component;

enum CommandSuffix: string
{
    case Name = 'Name';
    case Common = 'Common';
    case Content = 'Content';
    case Cost = 'Cost';
    case Limit = 'Limit';
    case PackLite = 'PackLite';
    case Rare = 'Rare';
    case Requirements = 'Requirements';
    case Tag = 'Tag';
}
