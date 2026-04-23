<?php

namespace App\Enums;

enum UserModuleType: string
{
    case VIDEO = 'video';
    case ARTICLE = 'article';
    case PDF = 'pdf';
}
