<?php

namespace App\Helpers;

class CountryTariffHelper
{
    /**
     * Maximum tariff threshold for affordable countries
     */
    const AFFORDABLE_TARIFF_THRESHOLD = 0.67;

    /**
     * Get all countries with their tariff information
     * Returns array with country code, name, region, tier, and price
     */
    public static function getAllCountries()
    {
        return [
            ['code' => 'AW', 'name' => 'Aruba', 'region' => 'Latin America', 'tier' => 'Neon', 'price' => 3.90],
            ['code' => 'AF', 'name' => 'Afghanistan', 'region' => 'Asia Pacific', 'tier' => 'Neon', 'price' => 4.93],
            ['code' => 'AO', 'name' => 'Angola', 'region' => 'Africa', 'tier' => 'Blue', 'price' => 12.17],
            ['code' => 'AI', 'name' => 'Anguilla', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'AX', 'name' => 'Åland Islands', 'region' => 'Europe', 'tier' => 'Green', 'price' => 0.82],
            ['code' => 'AL', 'name' => 'Albania', 'region' => 'Europe', 'tier' => 'Neon', 'price' => 1.00],
            ['code' => 'AD', 'name' => 'Andorra', 'region' => 'Europe', 'tier' => 'Blue', 'price' => 3.05],
            ['code' => 'AN', 'name' => 'Netherlands Antilles', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'region' => 'Middle East', 'tier' => 'Green', 'price' => 1.88],
            ['code' => 'AR', 'name' => 'Argentina', 'region' => 'Latin America', 'tier' => 'Blue', 'price' => 2.34],
            ['code' => 'AM', 'name' => 'Armenia', 'region' => 'Europe', 'tier' => 'Neon', 'price' => 1.35],
            ['code' => 'AG', 'name' => 'Antigua & Barbuda', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'AU', 'name' => 'Australia', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 0.75],
            ['code' => 'AT', 'name' => 'Austria', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'AZ', 'name' => 'Azerbaijan', 'region' => 'Asia Pacific', 'tier' => 'Neon', 'price' => 2.24],
            ['code' => 'BI', 'name' => 'Burundi', 'region' => 'Africa', 'tier' => 'Blue', 'price' => 15.37],
            ['code' => 'BE', 'name' => 'Belgium', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'BJ', 'name' => 'Benin', 'region' => 'Africa', 'tier' => 'Neon', 'price' => 4.04],
            ['code' => 'BQ', 'name' => 'Bonaire', 'region' => 'Latin America', 'tier' => 'Green', 'price' => 7.13],
            ['code' => 'BF', 'name' => 'Burkina Faso', 'region' => 'Africa', 'tier' => 'Lilac', 'price' => 4.98],
            ['code' => 'BD', 'name' => 'Bangladesh', 'region' => 'Asia Pacific', 'tier' => 'Neon', 'price' => 1.12],
            ['code' => 'BG', 'name' => 'Bulgaria', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'BH', 'name' => 'Bahrain', 'region' => 'Middle East', 'tier' => 'Green', 'price' => 2.13],
            ['code' => 'BS', 'name' => 'Bahamas', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'BA', 'name' => 'Bosnia & Herzegovina', 'region' => 'Europe', 'tier' => 'Neon', 'price' => 0.88],
            ['code' => 'BL', 'name' => 'Saint Barthelemy', 'region' => 'Latin America', 'tier' => 'Blue', 'price' => 1.64],
            ['code' => 'BY', 'name' => 'Belarus', 'region' => 'Europe', 'tier' => 'Neon', 'price' => 1.57],
            ['code' => 'BZ', 'name' => 'Belize', 'region' => 'Latin America', 'tier' => 'Neon', 'price' => 10.55],
            ['code' => 'BM', 'name' => 'Bermuda', 'region' => 'Latin America', 'tier' => 'Lilac', 'price' => 4.70],
            ['code' => 'BO', 'name' => 'Bolivia', 'region' => 'Latin America', 'tier' => 'Neon', 'price' => 3.20],
            ['code' => 'BR', 'name' => 'Brazil', 'region' => 'Latin America', 'tier' => 'Blue', 'price' => 2.34],
            ['code' => 'BB', 'name' => 'Barbados', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'BN', 'name' => 'Brunei', 'region' => 'Asia Pacific', 'tier' => 'Blue', 'price' => 3.02],
            ['code' => 'BW', 'name' => 'Botswana', 'region' => 'Africa', 'tier' => 'Lilac', 'price' => 4.98],
            ['code' => 'CA', 'name' => 'Canada', 'region' => 'North America', 'tier' => 'Green', 'price' => 1.75],
            ['code' => 'CH', 'name' => 'Switzerland', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.75],
            ['code' => 'CL', 'name' => 'Chile', 'region' => 'Latin America', 'tier' => 'Green', 'price' => 2.38],
            ['code' => 'CN', 'name' => 'China', 'region' => 'Asia Pacific', 'tier' => 'Jade', 'price' => 0.94],
            ['code' => 'CI', 'name' => "Cote d'Ivoire", 'region' => 'Africa', 'tier' => 'Lilac', 'price' => 4.64],
            ['code' => 'CM', 'name' => 'Cameroon', 'region' => 'Africa', 'tier' => 'Lilac', 'price' => 4.64],
            ['code' => 'CD', 'name' => 'Congo, (DRC)', 'region' => 'Africa', 'tier' => 'Sapphire', 'price' => 3.50],
            ['code' => 'CG', 'name' => 'Congo, Republic', 'region' => 'Africa', 'tier' => 'Lilac', 'price' => 4.64],
            ['code' => 'CO', 'name' => 'Colombia', 'region' => 'Latin America', 'tier' => 'Green', 'price' => 2.50],
            ['code' => 'CV', 'name' => 'Cape Verde', 'region' => 'Africa', 'tier' => 'Green', 'price' => 5.63],
            ['code' => 'CR', 'name' => 'Costa Rica', 'region' => 'Latin America', 'tier' => 'Green', 'price' => 1.63],
            ['code' => 'CU', 'name' => 'Cuba', 'region' => 'Latin America', 'tier' => 'Lilac', 'price' => 16.34],
            ['code' => 'CW', 'name' => 'Curacao', 'region' => 'Latin America', 'tier' => 'Green', 'price' => 7.13],
            ['code' => 'KY', 'name' => 'Cayman Islands', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'CY', 'name' => 'Cyprus', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'CZ', 'name' => 'Czech Republic', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'DE', 'name' => 'Germany', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'DM', 'name' => 'Dominica', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'DK', 'name' => 'Denmark', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'DO', 'name' => 'Dominican Republic', 'region' => 'Latin America', 'tier' => 'Lilac', 'price' => 0.1],
            ['code' => 'DZ', 'name' => 'Algeria', 'region' => 'Middle East', 'tier' => 'Neon', 'price' => 0.82],
            ['code' => 'EC', 'name' => 'Ecuador', 'region' => 'Latin America', 'tier' => 'Blue', 'price' => 2.95],
            ['code' => 'EG', 'name' => 'Egypt', 'region' => 'Middle East', 'tier' => 'Neon', 'price' => 1.32],
            ['code' => 'ES', 'name' => 'Spain', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'EE', 'name' => 'Estonia', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'ET', 'name' => 'Ethiopia', 'region' => 'Africa', 'tier' => 'Blue', 'price' => 6.65],
            ['code' => 'FI', 'name' => 'Finland', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'FJ', 'name' => 'Fiji', 'region' => 'Asia Pacific', 'tier' => 'Lilac', 'price' => 5.92],
            ['code' => 'FR', 'name' => 'France', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'FO', 'name' => 'Faroe Islands', 'region' => 'Europe', 'tier' => 'Neon', 'price' => 1.35],
            ['code' => 'GA', 'name' => 'Gabon', 'region' => 'Africa', 'tier' => 'Sapphire', 'price' => 3.07],
            ['code' => 'GB', 'name' => 'United Kingdom', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'GE', 'name' => 'Georgia', 'region' => 'Europe', 'tier' => 'Neon', 'price' => 0.84],
            ['code' => 'GG', 'name' => 'Guernsey', 'region' => 'Europe', 'tier' => 'Green', 'price' => 2.98],
            ['code' => 'GH', 'name' => 'Ghana', 'region' => 'Africa', 'tier' => 'Neon', 'price' => 2.34],
            ['code' => 'GI', 'name' => 'Gibraltar', 'region' => 'Europe', 'tier' => 'Lilac', 'price' => 0.67],
            ['code' => 'GN', 'name' => 'Guinea', 'region' => 'Africa', 'tier' => 'Lilac', 'price' => 4.98],
            ['code' => 'GP', 'name' => 'Guadeloupe', 'region' => 'Latin America', 'tier' => 'Blue', 'price' => 0.75],
            ['code' => 'GM', 'name' => 'Gambia', 'region' => 'Africa', 'tier' => 'Lilac', 'price' => 6.64],
            ['code' => 'GW', 'name' => 'Guinea-Bissau', 'region' => 'Africa', 'tier' => 'Lilac', 'price' => 4.98],
            ['code' => 'GR', 'name' => 'Greece', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'GD', 'name' => 'Grenada', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'GL', 'name' => 'Greenland', 'region' => 'Europe', 'tier' => 'Lilac', 'price' => 4.64],
            ['code' => 'GT', 'name' => 'Guatemala', 'region' => 'Latin America', 'tier' => 'Lilac', 'price' => 1.60],
            ['code' => 'GF', 'name' => 'French Guiana', 'region' => 'Latin America', 'tier' => 'Blue', 'price' => 0.75],
            ['code' => 'GU', 'name' => 'Guam', 'region' => 'Asia Pacific', 'tier' => 'Green', 'price' => 6.88],
            ['code' => 'GY', 'name' => 'Guyana', 'region' => 'Latin America', 'tier' => 'Lilac', 'price' => 5.14],
            ['code' => 'HK', 'name' => 'Hong Kong', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 0.83],
            ['code' => 'HN', 'name' => 'Honduras', 'region' => 'Latin America', 'tier' => 'Lilac', 'price' => 1.65],
            ['code' => 'HR', 'name' => 'Croatia', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'HT', 'name' => 'Haiti', 'region' => 'Latin America', 'tier' => 'Green', 'price' => 7.13],
            ['code' => 'HU', 'name' => 'Hungary', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'ID', 'name' => 'Indonesia', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 0.74],
            ['code' => 'IM', 'name' => 'Isle of Man', 'region' => 'Europe', 'tier' => 'Green', 'price' => 2.98],
            ['code' => 'IN', 'name' => 'India', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 1.25],
            ['code' => 'IE', 'name' => 'Ireland', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'IQ', 'name' => 'Iraq', 'region' => 'Middle East', 'tier' => 'Neon', 'price' => 2.13],
            ['code' => 'IS', 'name' => 'Iceland', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'IL', 'name' => 'Israel', 'region' => 'Middle East', 'tier' => 'Blue', 'price' => 1.15],
            ['code' => 'IT', 'name' => 'Italy', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'JM', 'name' => 'Jamaica', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'JE', 'name' => 'Jersey', 'region' => 'Europe', 'tier' => 'Green', 'price' => 2.98],
            ['code' => 'JO', 'name' => 'Jordan', 'region' => 'Middle East', 'tier' => 'Green', 'price' => 1.50],
            ['code' => 'JP', 'name' => 'Japan', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 1.00],
            ['code' => 'KZ', 'name' => 'Kazakhstan', 'region' => 'Asia Pacific', 'tier' => 'Neon', 'price' => 0.68],
            ['code' => 'KE', 'name' => 'Kenya', 'region' => 'Africa', 'tier' => 'Sapphire', 'price' => 3.07],
            ['code' => 'KG', 'name' => 'Kyrgyzstan', 'region' => 'Asia Pacific', 'tier' => 'Neon', 'price' => 0.82],
            ['code' => 'KH', 'name' => 'Cambodia', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 1.25],
            ['code' => 'KN', 'name' => 'St Kitts & Nevis', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'KR', 'name' => 'Korea, Republic of', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 0.82],
            ['code' => 'KW', 'name' => 'Kuwait', 'region' => 'Middle East', 'tier' => 'Neon', 'price' => 1.52],
            ['code' => 'LA', 'name' => 'Laos', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 1.63],
            ['code' => 'LK', 'name' => 'Sri Lanka', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 1.65],
            ['code' => 'LT', 'name' => 'Lithuania', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'LU', 'name' => 'Luxembourg', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'LV', 'name' => 'Latvia', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'MO', 'name' => 'Macau', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 0.83],
            ['code' => 'MF', 'name' => 'Saint Martin (French Part)', 'region' => 'Latin America', 'tier' => 'Blue', 'price' => 1.64],
            ['code' => 'MA', 'name' => 'Morocco', 'region' => 'Middle East', 'tier' => 'Blue', 'price' => 1.22],
            ['code' => 'MC', 'name' => 'Monaco', 'region' => 'Europe', 'tier' => 'Lilac', 'price' => 5.05],
            ['code' => 'MD', 'name' => 'Moldova', 'region' => 'Europe', 'tier' => 'Neon', 'price' => 2.18],
            ['code' => 'MG', 'name' => 'Madagascar', 'region' => 'Africa', 'tier' => 'Neon', 'price' => 2.72],
            ['code' => 'MV', 'name' => 'Maldives', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 6.94],
            ['code' => 'MX', 'name' => 'Mexico', 'region' => 'Latin America', 'tier' => 'Green', 'price' => 1.75],
            ['code' => 'MK', 'name' => 'Macedonia', 'region' => 'Europe', 'tier' => 'Lilac', 'price' => 1.78],
            ['code' => 'MT', 'name' => 'Malta', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'MM', 'name' => 'Myanmar', 'region' => 'Asia Pacific', 'tier' => 'Jade', 'price' => 2.64],
            ['code' => 'ME', 'name' => 'Montenegro', 'region' => 'Europe', 'tier' => 'Neon', 'price' => 1.08],
            ['code' => 'MN', 'name' => 'Mongolia', 'region' => 'Asia Pacific', 'tier' => 'Jade', 'price' => 3.47],
            ['code' => 'MS', 'name' => 'Montserrat', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'MQ', 'name' => 'Martinique', 'region' => 'Latin America', 'tier' => 'Blue', 'price' => 0.75],
            ['code' => 'MU', 'name' => 'Mauritius', 'region' => 'Africa', 'tier' => 'Sapphire', 'price' => 2.50],
            ['code' => 'MW', 'name' => 'Malawi', 'region' => 'Africa', 'tier' => 'Sapphire', 'price' => 3.07],
            ['code' => 'MY', 'name' => 'Malaysia', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 0.63],
            ['code' => 'YT', 'name' => 'Mayotte', 'region' => 'Africa', 'tier' => 'Blue', 'price' => 0.75],
            ['code' => 'NZ', 'name' => 'New Zealand', 'region' => 'Asia Pacific', 'tier' => 'Green', 'price' => 1.00],
            ['code' => 'OM', 'name' => 'Oman', 'region' => 'Middle East', 'tier' => 'Blue', 'price' => 2.57],
            ['code' => 'PK', 'name' => 'Pakistan', 'region' => 'Asia Pacific', 'tier' => 'Neon', 'price' => 0.89],
            ['code' => 'PA', 'name' => 'Panama', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 1.75],
            ['code' => 'PE', 'name' => 'Peru', 'region' => 'Latin America', 'tier' => 'Green', 'price' => 1.25],
            ['code' => 'PH', 'name' => 'Philippines', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 1.00],
            ['code' => 'PL', 'name' => 'Poland', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'PR', 'name' => 'Puerto Rico', 'region' => 'North America', 'tier' => 'Amber', 'price' => 1.00],
            ['code' => 'PT', 'name' => 'Portugal', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'PS', 'name' => 'Palestine', 'region' => 'Middle East', 'tier' => 'Blue', 'price' => 1.93],
            ['code' => 'QA', 'name' => 'Qatar', 'region' => 'Middle East', 'tier' => 'Neon', 'price' => 1.38],
            ['code' => 'RE', 'name' => 'Reunion', 'region' => 'Africa', 'tier' => 'Lilac', 'price' => 0.67],
            ['code' => 'RO', 'name' => 'Romania', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'NI', 'name' => 'Nicaragua', 'region' => 'Latin America', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'RU', 'name' => 'Russia', 'region' => 'Europe', 'tier' => 'Neon', 'price' => 1.57],
            ['code' => 'RW', 'name' => 'Rwanda', 'region' => 'Africa', 'tier' => 'Sapphire', 'price' => 3.07],
            ['code' => 'SA', 'name' => 'Saudi Arabia', 'region' => 'Middle East', 'tier' => 'Neon', 'price' => 1.58],
            ['code' => 'SN', 'name' => 'Senegal', 'region' => 'Africa', 'tier' => 'Neon', 'price' => 2.82],
            ['code' => 'SG', 'name' => 'Singapore', 'region' => 'Asia Pacific', 'tier' => 'Neon', 'price' => 0.68],
            ['code' => 'SV', 'name' => 'El Salvador', 'region' => 'Latin America', 'tier' => 'Lilac', 'price' => 1.63],
            ['code' => 'RS', 'name' => 'Serbia', 'region' => 'Europe', 'tier' => 'Green', 'price' => 1.68],
            ['code' => 'SR', 'name' => 'Suriname', 'region' => 'Latin America', 'tier' => 'Green', 'price' => 7.13],
            ['code' => 'SK', 'name' => 'Slovakia', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'SI', 'name' => 'Slovenia', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'SE', 'name' => 'Sweden', 'region' => 'EU28', 'tier' => 'Blue', 'price' => 0.67],
            ['code' => 'SX', 'name' => 'Sint Maarten', 'region' => 'Latin America', 'tier' => 'Green', 'price' => 7.13],
            ['code' => 'SC', 'name' => 'Seychelles', 'region' => 'Africa', 'tier' => 'Sapphire', 'price' => 3.50],
            ['code' => 'SZ', 'name' => 'Eswatini', 'region' => 'Africa', 'tier' => 'Blue', 'price' => 6.28],
            ['code' => 'TC', 'name' => 'Turks & Caicos Islands', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'TD', 'name' => 'Chad', 'region' => 'Africa', 'tier' => 'Sapphire', 'price' => 3.07],
            ['code' => 'TH', 'name' => 'Thailand', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 0.88],
            ['code' => 'TJ', 'name' => 'Tajikistan', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 8.75],
            ['code' => 'TT', 'name' => 'Trinidad & Tobago', 'region' => 'Latin America', 'tier' => 'Green', 'price' => 7.13],
            ['code' => 'TN', 'name' => 'Tunisia', 'region' => 'Middle East', 'tier' => 'Blue', 'price' => 1.15],
            ['code' => 'TR', 'name' => 'Turkey', 'region' => 'Middle East', 'tier' => 'Neon', 'price' => 0.49],
            ['code' => 'TW', 'name' => 'Taiwan', 'region' => 'Asia Pacific', 'tier' => 'Jade', 'price' => 0.92],
            ['code' => 'TZ', 'name' => 'Tanzania', 'region' => 'Africa', 'tier' => 'Sapphire', 'price' => 3.50],
            ['code' => 'UG', 'name' => 'Uganda', 'region' => 'Africa', 'tier' => 'Sapphire', 'price' => 3.07],
            ['code' => 'UA', 'name' => 'Ukraine', 'region' => 'Europe', 'tier' => 'Neon', 'price' => 0.63],
            ['code' => 'US', 'name' => 'United States', 'region' => 'North America', 'tier' => 'Amber', 'price' => 0.63],
            ['code' => 'UZ', 'name' => 'Uzbekistan', 'region' => 'Asia Pacific', 'tier' => 'Neon', 'price' => 0.89],
            ['code' => 'VC', 'name' => 'St Vincent & the Grenadines', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'VG', 'name' => 'British Virgin Islands', 'region' => 'Latin America', 'tier' => 'Amber', 'price' => 3.67],
            ['code' => 'VI', 'name' => 'United States Virgin Islands', 'region' => 'North America', 'tier' => 'Amber', 'price' => 1.00],
            ['code' => 'VN', 'name' => 'Vietnam', 'region' => 'Asia Pacific', 'tier' => 'Sapphire', 'price' => 1.03],
            ['code' => 'XK', 'name' => 'Kosovo', 'region' => 'Europe', 'tier' => 'Blue', 'price' => 10.24],
            ['code' => 'ZA', 'name' => 'South Africa', 'region' => 'Africa', 'tier' => 'Neon', 'price' => 2.02],
            ['code' => 'ZM', 'name' => 'Zambia', 'region' => 'Africa', 'tier' => 'Sapphire', 'price' => 3.07],
            ['code' => 'VE', 'name' => 'Venezuela', 'region' => 'Latin America', 'tier' => 'Sapphire', 'price' => 0.1],

        ];
    }

    /**
     * Get countries with tariff <= AFFORDABLE_TARIFF_THRESHOLD
     */
    public static function getAffordableCountries()
    {
        return array_filter(self::getAllCountries(), function($country) {
            return $country['price'] <= self::AFFORDABLE_TARIFF_THRESHOLD;
        });
    }

    /**
     * Get country by code
     */
    public static function getCountryByCode($code)
    {
        $countries = self::getAllCountries();
        foreach ($countries as $country) {
            if ($country['code'] === strtoupper($code)) {
                return $country;
            }
        }
        return null;
    }

    /**
     * Get country emoji flag
     * Comprehensive mapping for all countries in the tariff list
     */
    public static function getCountryEmoji($code)
    {
        $emojis = [
            'AD' => '🇦🇩', 'AE' => '🇦🇪', 'AF' => '🇦🇫', 'AG' => '🇦🇬', 'AI' => '🇦🇮',
            'AL' => '🇦🇱', 'AM' => '🇦🇲', 'AN' => '🇦🇳', 'AO' => '🇦🇴', 'AR' => '🇦🇷',
            'AT' => '🇦🇹', 'AU' => '🇦🇺', 'AW' => '🇦🇼', 'AX' => '🇦🇽', 'AZ' => '🇦🇿',
            'BA' => '🇧🇦', 'BB' => '🇧🇧', 'BD' => '🇧🇩', 'BE' => '🇧🇪', 'BF' => '🇧🇫',
            'BG' => '🇧🇬', 'BH' => '🇧🇭', 'BI' => '🇧🇮', 'BJ' => '🇧🇯', 'BL' => '🇧🇱',
            'BM' => '🇧🇲', 'BN' => '🇧🇳', 'BO' => '🇧🇴', 'BQ' => '🇧🇶', 'BR' => '🇧🇷',
            'BS' => '🇧🇸', 'BW' => '🇧🇼', 'BY' => '🇧🇾', 'BZ' => '🇧🇿', 'CA' => '🇨🇦',
            'CD' => '🇨🇩', 'CG' => '🇨🇬', 'CH' => '🇨🇭', 'CI' => '🇨🇮', 'CL' => '🇨🇱',
            'CM' => '🇨🇲', 'CN' => '🇨🇳', 'CO' => '🇨🇴', 'CR' => '🇨🇷', 'CU' => '🇨🇺',
            'CV' => '🇨🇻', 'CW' => '🇨🇼', 'CY' => '🇨🇾', 'CZ' => '🇨🇿', 'DE' => '🇩🇪',
            'DK' => '🇩🇰', 'DM' => '🇩🇲', 'DO' => '🇩🇴', 'DZ' => '🇩🇿', 'EC' => '🇪🇨',
            'EE' => '🇪🇪', 'EG' => '🇪🇬', 'ES' => '🇪🇸', 'ET' => '🇪🇹', 'FI' => '🇫🇮',
            'FJ' => '🇫🇯', 'FO' => '🇫🇴', 'FR' => '🇫🇷', 'GA' => '🇬🇦', 'GB' => '🇬🇧',
            'GD' => '🇬🇩', 'GE' => '🇬🇪', 'GF' => '🇬🇫', 'GG' => '🇬🇬', 'GH' => '🇬🇭',
            'GI' => '🇬🇮', 'GL' => '🇬🇱', 'GM' => '🇬🇲', 'GN' => '🇬🇳', 'GP' => '🇬🇵',
            'GR' => '🇬🇷', 'GT' => '🇬🇹', 'GU' => '🇬🇺', 'GW' => '🇬🇼', 'GY' => '🇬🇾',
            'HK' => '🇭🇰', 'HN' => '🇭🇳', 'HR' => '🇭🇷', 'HT' => '🇭🇹', 'HU' => '🇭🇺',
            'ID' => '🇮🇩', 'IE' => '🇮🇪', 'IL' => '🇮🇱', 'IM' => '🇮🇲', 'IN' => '🇮🇳',
            'IQ' => '🇮🇶', 'IS' => '🇮🇸', 'IT' => '🇮🇹', 'JE' => '🇯🇪', 'JM' => '🇯🇲',
            'JO' => '🇯🇴', 'JP' => '🇯🇵', 'KE' => '🇰🇪', 'KG' => '🇰🇬', 'KH' => '🇰🇭',
            'KN' => '🇰🇳', 'KR' => '🇰🇷', 'KW' => '🇰🇼', 'KY' => '🇰🇾', 'KZ' => '🇰🇿',
            'LA' => '🇱🇦', 'LK' => '🇱🇰', 'LT' => '🇱🇹', 'LU' => '🇱🇺', 'LV' => '🇱🇻',
            'MA' => '🇲🇦', 'MC' => '🇲🇨', 'MD' => '🇲🇩', 'ME' => '🇲🇪', 'MF' => '🇲🇫',
            'MG' => '🇲🇬', 'MK' => '🇲🇰', 'MM' => '🇲🇲', 'MN' => '🇲🇳', 'MO' => '🇲🇴',
            'MQ' => '🇲🇶', 'MS' => '🇲🇸', 'MT' => '🇲🇹', 'MU' => '🇲🇺', 'MV' => '🇲🇻',
            'MW' => '🇲🇼', 'MX' => '🇲🇽', 'MY' => '🇲🇾', 'NZ' => '🇳🇿', 'OM' => '🇴🇲',
            'PA' => '🇵🇦', 'PE' => '🇵🇪', 'PH' => '🇵🇭', 'PK' => '🇵🇰', 'PL' => '🇵🇱',
            'PR' => '🇵🇷', 'PS' => '🇵🇸', 'PT' => '🇵🇹', 'QA' => '🇶🇦', 'RE' => '🇷🇪',
            'RO' => '🇷🇴', 'RS' => '🇷🇸', 'RU' => '🇷🇺', 'RW' => '🇷🇼', 'SA' => '🇸🇦',
            'SC' => '🇸🇨', 'SE' => '🇸🇪', 'SG' => '🇸🇬', 'SI' => '🇸🇮', 'SK' => '🇸🇰',
            'SN' => '🇸🇳', 'SR' => '🇸🇷', 'SV' => '🇸🇻', 'SX' => '🇸🇽', 'SZ' => '🇸🇿',
            'TC' => '🇹🇨', 'TD' => '🇹🇩', 'TH' => '🇹🇭', 'TJ' => '🇹🇯', 'TN' => '🇹🇳',
            'TR' => '🇹🇷', 'TT' => '🇹🇹', 'TW' => '🇹🇼', 'TZ' => '🇹🇿', 'UA' => '🇺🇦',
            'UG' => '🇺🇬', 'US' => '🇺🇸', 'UZ' => '🇺🇿', 'VC' => '🇻🇨', 'VG' => '🇻🇬',
            'VI' => '🇻🇮', 'VN' => '🇻🇳', 'XK' => '🇽🇰', 'YT' => '🇾🇹', 'ZA' => '🇿🇦',
            'ZM' => '🇿🇲',
        ];
        return $emojis[strtoupper($code)] ?? '🌍';
    }
}
