<?php

namespace App\Intelligence\Fetching;

use Illuminate\Support\Str;

class ContentNormalizerService
{
    /**
     * Normalize raw content into a structured format.
     *
     * @param string $title
     * @param string $content
     * @param string $url
     * @param string|null $publishedAt
     * @return array
     */
    public function normalize(string $title, string $content, string $url, ?string $publishedAt = null): array
    {
        $cleanTitle = $this->cleanString($title);
        $cleanBody = $this->stripHtml($content);
        
        return [
            'title' => $cleanTitle,
            'body' => $cleanBody,
            'url' => $url,
            'fingerprint' => $this->generateFingerprint($url, $cleanTitle),
            'published_at' => $publishedAt ? $this->parseDate($publishedAt) : now(),
        ];
    }

    /**
     * Generate a stable, deterministic fingerprint for a signal.
     * 
     * @param string $url
     * @param string $title
     * @return string
     */
    public function generateFingerprint(string $url, string $title): string
    {
        // Stable fingerprint: lowercased URL + lowercased Title
        return hash('sha256', Str::lower($url) . '|' . Str::lower($title));
    }

    /**
     * Strip HTML and normalize whitespace.
     *
     * @param string $content
     * @return string
     */
    protected function stripHtml(string $content): string
    {
        $text = strip_tags($content);
        // Normalize multiple spaces/newlines to single
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Clean string from excessive whitespace.
     *
     * @param string $str
     * @return string
     */
    protected function cleanString(string $str): string
    {
        return trim(preg_replace('/\s+/', ' ', $str));
    }

    /**
     * Parse date string or return null.
     *
     * @param string $date
     * @return string|null
     */
    protected function parseDate(string $date): ?string
    {
        try {
            return \Illuminate\Support\Carbon::parse($date)->toDateTimeString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
