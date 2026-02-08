<?php

namespace App\Intelligence\Contracts;

interface TrustStrategyInterface
{
    /**
     * Calculate the trust weight for a given URL or source metadata.
     *
     * @param string $url
     * @param array $metadata
     * @return float
     */
    public function calculate(string $url, array $metadata = []): float;
}
