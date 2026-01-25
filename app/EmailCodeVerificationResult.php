<?php

namespace App;

enum EmailCodeVerificationResult: string
{
    case Verified = 'verified';
    case Invalid = 'invalid';
    case Expired = 'expired';
    case AlreadyVerified = 'already_verified';
}
