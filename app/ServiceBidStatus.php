<?php

namespace App;

enum ServiceBidStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Withdrawn = 'withdrawn';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Expired = 'expired';
}
