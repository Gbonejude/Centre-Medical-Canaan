<?php

namespace App\Enums;

enum ConversationStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
