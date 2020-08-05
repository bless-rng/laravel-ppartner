<?php


namespace App\Enums;


class TransactionType extends Enum
{
    public const INCOME = "income";
    public const EXPENSE = "expense";

    protected static $values = [
        "income",
        "expense"
    ];
}
