<?php

namespace Tests\Unit\Helpers;

use App\Helpers\CountryTariffHelper;
use PHPUnit\Framework\TestCase;

class CountryTariffHelperTest extends TestCase
{
    /** @test */
    public function uruguay_and_paraguay_are_available_for_free_esim_activation(): void
    {
        $registrationCountryCodes = array_column(CountryTariffHelper::getAffordableCountriesForRegistration(), 'code');

        foreach (['PY', 'UY'] as $countryCode) {
            $this->assertContains($countryCode, $registrationCountryCodes);
            $this->assertTrue(CountryTariffHelper::isAffordableCountryCode($countryCode));
        }

        $this->assertSame('🇵🇾', CountryTariffHelper::getCountryEmoji('PY'));
        $this->assertSame('🇺🇾', CountryTariffHelper::getCountryEmoji('UY'));
    }
}
