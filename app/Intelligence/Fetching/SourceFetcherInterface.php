<?php

namespace App\Intelligence\Fetching;

interface SourceFetcherInterface
{
    /**
     * Fetch content from a given source.
     *
     * @param \App\Models\Source $source
     * @return array List of normalized items
     */
    public function fetch(\App\Models\Source $source): array;
}
