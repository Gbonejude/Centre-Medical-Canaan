<?php

namespace App\Enums;

enum QuestionType: string
{
    case MULTIPLE_CHOICE = 'multiple_choice';
    case TEXT = 'text';
    case TRUE_FALSE = 'true_false';
    case SHORT_ANSWER = 'short_answer';
}
