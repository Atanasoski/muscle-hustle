<?php

namespace App\Enums;

enum PlanType: string
{
    case Library = 'library';
    case Custom = 'custom';
    case Program = 'program';
}
