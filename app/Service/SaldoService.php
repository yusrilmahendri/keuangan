<?php
namespace App\Service;

use App\Models\Saldo;


class SaldoService
{
    public function getTotalSaldo()
    {
        return Saldo::sum('amount');
    }
}