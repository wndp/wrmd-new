<?php

namespace Tests\Unit\Support;

use App\Support\Temperature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesUiBehavior;

final class TemperatureTest extends TestCase
{
    use CreatesUiBehavior;
    use RefreshDatabase;

    #[Test]
    public function itConvertsTemperaturesIntoCelsius(): void
    {
        [$cTemperatureId, $fTemperatureId, $kTemperatureId] = $this->temperatureUnits();

        $this->assertSame(-12.22, Temperature::toCelsius(-12.22, $cTemperatureId));
        $this->assertSame(-12.22, Temperature::toCelsius(10, $fTemperatureId));
        $this->assertSame(-263.15, Temperature::toCelsius(10, $kTemperatureId));
    }

    #[Test]
    public function itConvertsTemperaturesIntoFahrenheit(): void
    {
        [$cTemperatureId, $fTemperatureId, $kTemperatureId] = $this->temperatureUnits();

        $this->assertSame(50.00, Temperature::toFahrenheit(10, $cTemperatureId));
        $this->assertSame(10.00, Temperature::toFahrenheit(10, $fTemperatureId));
        $this->assertSame(-441.67, Temperature::toFahrenheit(10, $kTemperatureId));
    }

    #[Test]
    public function itConvertsTemperaturesIntoKelvin(): void
    {
        [$cTemperatureId, $fTemperatureId, $kTemperatureId] = $this->temperatureUnits();

        $this->assertSame(283.15, Temperature::toKelvin(10, $cTemperatureId));
        $this->assertSame(260.93, Temperature::toKelvin(10, $fTemperatureId));
        $this->assertSame(283.15, Temperature::toKelvin(283.15, $kTemperatureId));
    }
}
