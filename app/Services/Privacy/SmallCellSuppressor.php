<?php

namespace App\Services\Privacy;

class SmallCellSuppressor
{
    protected int $k;

    public function __construct(?int $k = null)
    {
        $this->k = $k ?? (int) config('analytics.k_anonymity_min', 5);
    }

    /**
     * Suppress count if strictly between 1 and K-1 (or < K).
     * Returns string "< 5" if suppressed, or integer count.
     * Note: 0 is safe to show as 0 (no individual to identify).
     */
    public function suppress(int $count, ?int $k = null): int|string
    {
        $threshold = $k ?? $this->k;

        if ($count > 0 && $count < $threshold) {
            return "< {$threshold}";
        }

        return $count;
    }

    public function isSuppressed(int $count, ?int $k = null): bool
    {
        $threshold = $k ?? $this->k;
        return $count > 0 && $count < $threshold;
    }

    /**
     * Apply suppression to an associative or multidimensional array on specified keys.
     */
    public function suppressArray(array $items, array $keys = ['count', 'total'], ?int $k = null): array
    {
        $threshold = $k ?? $this->k;

        foreach ($items as &$item) {
            if (is_array($item)) {
                foreach ($keys as $key) {
                    if (isset($item[$key]) && is_numeric($item[$key])) {
                        $raw = (int) $item[$key];
                        $item["raw_{$key}"] = $raw;
                        $item[$key] = $this->suppress($raw, $threshold);
                        $item["is_suppressed"] = $this->isSuppressed($raw, $threshold);
                    }
                }
            }
        }

        return $items;
    }
}
