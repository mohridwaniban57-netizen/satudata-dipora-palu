<?php

namespace Tests\Unit;

use App\Services\Analytics\LinearRegression;
use PHPUnit\Framework\TestCase;

class LinearRegressionTest extends TestCase
{
    public function test_perfect_linear_data(): void
    {
        $regression = new LinearRegression();
        $xs = [1, 2, 3, 4, 5];
        $ys = [2, 4, 6, 8, 10]; // y = 2x

        $regression->fit($xs, $ys);

        $this->assertEqualsWithDelta(2.0, $regression->slope(), 0.001);
        $this->assertEqualsWithDelta(0.0, $regression->intercept(), 0.001);
        $this->assertEqualsWithDelta(12.0, $regression->predict(6), 0.001);
        $this->assertEqualsWithDelta(1.0, $regression->rSquared(), 0.001);
        $this->assertEqualsWithDelta(0.0, $regression->standardError(), 0.001);
    }

    public function test_fallback_on_single_point(): void
    {
        $regression = new LinearRegression();
        $xs = [2024];
        $ys = [25];

        $regression->fit($xs, $ys);

        $this->assertEquals(0.0, $regression->slope());
        $this->assertEquals(25.0, $regression->intercept());
        $this->assertEquals(25.0, $regression->predict(2025));
        $this->assertEquals(0.0, $regression->standardError());
    }

    public function test_noisy_data_predict_and_error(): void
    {
        $regression = new LinearRegression();
        $xs = [1, 2, 3, 4, 5];
        $ys = [2.1, 3.9, 6.2, 7.8, 10.1];

        $regression->fit($xs, $ys);

        $this->assertGreaterThan(1.8, $regression->slope());
        $this->assertLessThan(2.2, $regression->slope());
        $this->assertGreaterThan(0.95, $regression->rSquared());
        $this->assertGreaterThan(0.0, $regression->standardError());
    }
}
