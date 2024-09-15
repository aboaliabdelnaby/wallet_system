<?php

namespace App\Http\Enum;

enum TransactionTypeEnum: string
{
   case RECIEVING = 'recieving';
   case SENDING = 'sending';
   case TOPUP = 'topup';
}
