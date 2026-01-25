<?php

namespace App;

enum ServiceFormFieldType: string
{
    case Select = 'select';
    case Text = 'text';
    case Number = 'number';
    case Textarea = 'textarea';
    case Date = 'date';
}
