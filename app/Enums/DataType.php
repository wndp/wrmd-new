<?php

namespace App\Enums;

enum DataType: string
{
    case STRING = 'STRING';
    case INT = 'INT';
    case DOUBLE = 'DOUBLE';
    case BOOLEAN = 'BOOLEAN';
    case DATE = 'DATE';
}
