<?php

declare(strict_types=1);

namespace App\Services\Bins;

interface BinInterface
{
    public function getCountryCode(int $bin): ?string;

    public function fetchFromAPI(int $bin): ?array;

    public function extractCountryCode(array $binData): ?string;
}