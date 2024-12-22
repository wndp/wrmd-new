<?php

namespace Tests\Unit\Support;

use App\Support\Temperature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesUiBehavior;

final class TemperatureTest extends TestCase
{
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_it_converts_temperatures_into_celsius(): void
    {
        [$cTemperatureId, $fTemperatureId, $kTemperatureId] = $this->temperatureUnits();

        $this->assertSame(-12.22, Temperature::toCelsius(-12.22, $cTemperatureId));
        $this->assertSame(-12.22, Temperature::toCelsius(10, $fTemperatureId));
        $this->assertSame(-263.15, Temperature::toCelsius(10, $kTemperatureId));
    }

    public function test_it_converts_temperatures_into_fahrenheit(): void
    {
        [$cTemperatureId, $fTemperatureId, $kTemperatureId] = $this->temperatureUnits();

        $this->assertSame(50.00, Temperature::toFahrenheit(10, $cTemperatureId));
        $this->assertSame(10.00, Temperature::toFahrenheit(10, $fTemperatureId));
        $this->assertSame(-441.67, Temperature::toFahrenheit(10, $kTemperatureId));
    }

    public function test_it_converts_temperatures_into_kelvin(): void
    {
        [$cTemperatureId, $fTemperatureId, $kTemperatureId] = $this->temperatureUnits();

        $this->assertSame(283.15, Temperature::toKelvin(10, $cTemperatureId));
        $this->assertSame(260.93, Temperature::toKelvin(10, $fTemperatureId));
        $this->assertSame(283.15, Temperature::toKelvin(283.15, $kTemperatureId));
    }
}
