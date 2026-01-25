<?php

namespace App;

enum ServiceBidStatus: string
{
    case Submitted = 'submitted';
    case Withdrawn = 'withdrawn';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}
