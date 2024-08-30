<?php

namespace App\Services\Rates;

interface RatesInterface
{
    public function setRates(): void;

    public function fetchFromAPI(): ?array;

    public function find(string $currency): ?float;
}