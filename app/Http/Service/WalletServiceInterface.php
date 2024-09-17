<?php

namespace App\Http\Service;

interface WalletServiceInterface
{
    public function topUp(array $data): bool;
    public function transfer(array $data): bool;
    public function history();
}
