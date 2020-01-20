<?php
namespace Tests\Unit\Models;

use Ebanx\Benjamin\Models\Currency;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    public function testInvalidCountry()
    {
        $this->assertNull(Currency::localForCountry('invalidCountry'));
    }

    public function testInvalidCurrencyToCountry()
    {
        $this->assertNull(Currency::currencyToCountry('CHF'));
    }

    public function testValidCurrencyToCountry()
    {
        $this->assertEquals('Brazil', Currency::currencyToCountry('BRL'));
    }
}
