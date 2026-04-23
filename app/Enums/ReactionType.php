<?php

namespace App\Enums;

enum ReactionType: string
{
    case LIKE = 'like';
    case DISLIKE = 'dislike';
    case LOVE = 'love';
    case HAHA = 'haha';
    case WOW = 'wow';
    case SAD = 'sad';
    case ANGRY = 'angry';
}
