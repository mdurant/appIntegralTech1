<?php

namespace App;

enum ServiceRequestStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Closed = 'closed';
    case Awarded = 'awarded';
    case Cancelled = 'cancelled';
}
