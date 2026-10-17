<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;

class TimezoneWibTest extends TestCase
{
    public function test_application_timezone_is_wib_asia_jakarta(): void
    {
        $this->assertEquals('Asia/Jakarta', config('app.timezone'));
        $this->assertEquals('Asia/Jakarta', date_default_timezone_get());

        // Carbon now should have +07:00 offset (WIB)
        $now = Carbon::now();
        $this->assertEquals(7 * 3600, $now->offset);
    }

    public function test_carbon_locale_is_indonesian(): void
    {
        $date = Carbon::create(2026, 10, 17, 12, 0, 0, 'Asia/Jakarta');
        $formatted = $date->translatedFormat('l, d F Y');

        $this->assertStringContainsString('Sabtu', $formatted);
        $this->assertStringContainsString('Oktober', $formatted);
    }
}
