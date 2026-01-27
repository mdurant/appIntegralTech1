<?php

namespace App;

enum WorkOrderStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
}
