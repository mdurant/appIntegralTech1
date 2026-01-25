<?php

namespace App;

enum SystemRole: string
{
    case Admin = 'admin';
    case User = 'user';
    case Guest = 'guest';
    case Client = 'client';
}
