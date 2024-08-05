<?php

namespace App\Enums;

enum SpecialAccount: int
{
    case WRMD = 1;
    case WRMD_TRIAL = 16;
    case OWCN = 38;
    case WRMD_SURVEILLANCE = 416;
    case OWCN_TEST = 600;
    case OWCN_IOA = 915;
}
