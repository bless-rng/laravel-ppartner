<?php


namespace App\Enums;


class Currency extends Enum
{
    public const AUD = "AUD";
    public const GPB = "GBP";
    public const BYR = "BYR";
    public const DKK = "DKK";
    public const USD = "USD";
    public const EUR = "EUR";
    public const ISK = "ISK";
    public const KZT = "KZT";
    public const RUB = "RUB";

    protected static $values = [
        "AUD",
        "GBP",
        "BYR",
        "DKK",
        "USD",
        "EUR",
        "ISK",
        "KZT",
        "RUB"
    ];
}
