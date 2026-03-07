<?php

namespace App\Enums;

enum SplitFocus: string
{
    case Balanced = 'balanced';
    case UpperFocus = 'upper_focus';
    case LowerFocus = 'lower_focus';
}
