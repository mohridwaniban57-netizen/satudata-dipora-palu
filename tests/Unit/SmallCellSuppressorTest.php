<?php

namespace Tests\Unit;

use App\Services\Privacy\SmallCellSuppressor;
use PHPUnit\Framework\TestCase;

class SmallCellSuppressorTest extends TestCase
{
    public function test_suppresses_numbers_below_k(): void
    {
        $suppressor = new SmallCellSuppressor(5);

        $this->assertEquals('< 5', $suppressor->suppress(1));
        $this->assertEquals('< 5', $suppressor->suppress(2));
        $this->assertEquals('< 5', $suppressor->suppress(4));
        $this->assertTrue($suppressor->isSuppressed(3));
    }

    public function test_preserves_zero_and_numbers_at_or_above_k(): void
    {
        $suppressor = new SmallCellSuppressor(5);

        $this->assertEquals(0, $suppressor->suppress(0));
        $this->assertFalse($suppressor->isSuppressed(0));

        $this->assertEquals(5, $suppressor->suppress(5));
        $this->assertFalse($suppressor->isSuppressed(5));

        $this->assertEquals(42, $suppressor->suppress(42));
        $this->assertFalse($suppressor->isSuppressed(42));
    }

    public function test_suppress_array(): void
    {
        $suppressor = new SmallCellSuppressor(5);

        $data = [
            ['sport' => 'Panahan', 'count' => 3],
            ['sport' => 'Sepak Bola', 'count' => 25],
        ];

        $suppressed = $suppressor->suppressArray($data, ['count']);

        $this->assertEquals('< 5', $suppressed[0]['count']);
        $this->assertTrue($suppressed[0]['is_suppressed']);
        $this->assertEquals(3, $suppressed[0]['raw_count']);

        $this->assertEquals(25, $suppressed[1]['count']);
        $this->assertFalse($suppressed[1]['is_suppressed']);
        $this->assertEquals(25, $suppressed[1]['raw_count']);
    }
}
