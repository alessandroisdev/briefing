<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case Answered = 'answered';
    case WaitingClient = 'waiting_client';
    case Closed = 'closed';
}
