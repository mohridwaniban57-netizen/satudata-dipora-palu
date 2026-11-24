<?php

namespace App\Services\Analytics;

class LinearRegression
{
    private float $slope = 0.0;
    private float $intercept = 0.0;
    private float $standardError = 0.0;
    private float $rSquared = 0.0;
    private int $n = 0;
    private bool $isFitted = false;

    /**
     * Fit ordinary least squares (OLS) regression on arrays of x and y.
     *
     * @param array<int|float> $xs
     * @param array<int|float> $ys
     */
    public function fit(array $xs, array $ys): self
    {
        $xs = array_values($xs);
        $ys = array_values($ys);
        $n = min(count($xs), count($ys));
        $this->n = $n;

        if ($n < 2) {
            $this->slope = 0.0;
            $this->intercept = $n === 1 ? (float) $ys[0] : 0.0;
            $this->standardError = 0.0;
            $this->rSquared = 0.0;
            $this->isFitted = true;
            return $this;
        }

        $meanX = array_sum($xs) / $n;
        $meanY = array_sum($ys) / $n;

        $ssXx = 0.0;
        $ssYy = 0.0;
        $ssXy = 0.0;

        for ($i = 0; $i < $n; $i++) {
            $diffX = (float) $xs[$i] - $meanX;
            $diffY = (float) $ys[$i] - $meanY;
            $ssXx += $diffX * $diffX;
            $ssYy += $diffY * $diffY;
            $ssXy += $diffX * $diffY;
        }

        if ($ssXx == 0.0) {
            $this->slope = 0.0;
            $this->intercept = $meanY;
            $this->standardError = 0.0;
            $this->rSquared = 0.0;
            $this->isFitted = true;
            return $this;
        }

        $this->slope = $ssXy / $ssXx;
        $this->intercept = $meanY - ($this->slope * $meanX);

        // Calculate residual sum of squares (SSres)
        $ssRes = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $predicted = $this->intercept + ($this->slope * (float) $xs[$i]);
            $residual = (float) $ys[$i] - $predicted;
            $ssRes += $residual * $residual;
        }

        if ($n > 2) {
            $this->standardError = sqrt($ssRes / ($n - 2));
        } else {
            $this->standardError = 0.0;
        }

        if ($ssYy == 0.0) {
            $this->rSquared = 1.0;
        } else {
            $this->rSquared = max(0.0, min(1.0, 1.0 - ($ssRes / $ssYy)));
        }

        $this->isFitted = true;
        return $this;
    }

    public function predict(float|int $x): float
    {
        if (!$this->isFitted) {
            return 0.0;
        }

        return $this->intercept + ($this->slope * (float) $x);
    }

    public function standardError(): float
    {
        return $this->standardError;
    }

    public function rSquared(): float
    {
        return $this->rSquared;
    }

    public function slope(): float
    {
        return $this->slope;
    }

    public function intercept(): float
    {
        return $this->intercept;
    }

    public function sampleSize(): int
    {
        return $this->n;
    }
}
