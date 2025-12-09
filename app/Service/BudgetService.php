<?php
namespace App\Service;

use App\Models\Budget;

class BudgetService
{
    public function getTotalBudget()
    {   
        $lastAmount = Budget::latest()->value('amount');  // amount terakhir

        return $lastAmount ?? 0;
    }
}