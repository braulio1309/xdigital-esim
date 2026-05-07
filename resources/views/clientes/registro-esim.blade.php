@extends('auth-layouts.auth')

@section('title', 'Registro Cliente eSIM - Nomad')

@section('contents')
{{-- Estilos personalizados para esta vista (Brand Colors) --}}
<style>
    :root {
        --xcertus-purple: #623b86;
        --xcertus-yellow: #ffcc00;
        --nomad-blue: #2d9cdb;
        --nomad-navy: #181c36;
    }

    /* --- ESTRUCTURA DE TRIÁNGULO PARA LOGOS --- */
    .brand-alliance-container {
        display: flex;
        flex-direction: column; /* Alineación vertical para crear filas */
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
    }

    .top-row-logos {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        width: 100%;
    }

    .partner-row-logo {
        display: flex;
        justify-content: center;
        width: 100%;
        margin-top: 5px; /* Espacio extra para definir el triángulo */
    }

    .logo-img {
        max-width: 100%; 
        height: auto;
        object-fit: contain;
    }

    .logo-nomad {
        max-height: 42px;
        max-width: 48%;
    }

    .logo-partner {
        max-height: 58px; /* Un poco más grande para que destaque abajo */
        max-width: 58%;
        filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1));
    }

    .top-row-logos.single-brand {
        justify-content: center;
    }

    .brand-footnote {
        margin-top: 24px;
        text-align: center;
        font-size: 0.72rem;
        line-height: 1.5;
        color: rgba(24, 28, 54, 0.58);
    }

    /* --- ESTILOS GENERALES --- */
    .promo-card {
        background: linear-gradient(135deg, #f0f9ff 0%, #e1f5fe 100%);
        border-left: 5px solid var(--nomad-blue);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 25px;
        color: var(--nomad-navy);
    }

    .btn-brand-gradient {
        background: linear-gradient(90deg, var(--nomad-navy) 0%, var(--xcertus-purple) 100%);
        border: none;
        color: white;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-brand-gradient:hover {
        opacity: 0.95;
        transform: scale(1.02);
        box-shadow: 0 4px 10px rgba(98, 59, 134, 0.3);
        color: #fff;
    }

    .form-control:focus {
        border-color: var(--nomad-blue);
        box-shadow: 0 0 0 0.2rem rgba(45, 156, 219, 0.25);
    }

    .country-search-input {
        margin-bottom: 12px;
    }

    .country-autocomplete {
        position: relative;
    }

    .country-suggestions {
        position: absolute;
        top: calc(100% - 10px);
        left: 0;
        right: 0;
        z-index: 30;
        background: #fff;
        border: 1px solid rgba(24, 28, 54, 0.12);
        border-radius: 14px;
        box-shadow: 0 18px 28px rgba(24, 28, 54, 0.12);
        max-height: 260px;
        overflow-y: auto;
        padding: 8px;
    }

    .country-suggestions.d-none {
        display: none;
    }

    .country-suggestion-item {
        width: 100%;
        border: 0;
        background: transparent;
        text-align: left;
        padding: 10px 12px;
        border-radius: 10px;
        color: var(--nomad-navy);
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .country-suggestion-item:hover,
    .country-suggestion-item.is-active {
        background: rgba(45, 156, 219, 0.12);
        color: var(--xcertus-purple);
        outline: none;
    }

    .country-suggestion-empty {
        padding: 10px 12px;
        color: rgba(24, 28, 54, 0.62);
        font-size: 0.92rem;
    }

    .inline-plans-panel {
        margin-top: 28px;
        padding: 22px;
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(45, 156, 219, 0.08) 0%, rgba(98, 59, 134, 0.06) 100%);
        border: 1px solid rgba(45, 156, 219, 0.18);
    }

    .inline-plans-title {
        color: var(--nomad-navy);
        font-weight: 700;
        margin-bottom: 8px;
    }

    .inline-plans-copy {
        color: rgba(24, 28, 54, 0.72);
        font-size: 0.92rem;
        margin-bottom: 18px;
    }

    .inline-plans-status {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 96px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.72);
        color: rgba(24, 28, 54, 0.72);
        text-align: center;
        padding: 18px;
    }

    .inline-plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }

    .inline-plan-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(24, 28, 54, 0.08);
        padding: 18px;
        box-shadow: 0 12px 24px rgba(24, 28, 54, 0.08);
    }

    .inline-plan-meta {
        color: rgba(24, 28, 54, 0.62);
        font-size: 0.85rem;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .inline-plan-data {
        font-size: 1.5rem;
        line-height: 1.1;
        font-weight: 700;
        color: var(--nomad-navy);
        margin-bottom: 8px;
    }

    .inline-plan-price {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--xcertus-purple);
        margin-bottom: 14px;
    }

    .inline-plan-action {
        width: 100%;
    }

    .inline-plan-card.is-selected {
        border-color: rgba(98, 59, 134, 0.35);
        box-shadow: 0 16px 28px rgba(98, 59, 134, 0.14);
    }

    .inline-plans-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .registro-auth-modal .modal-header,
    .registro-payment-modal .modal-header {
        background: linear-gradient(90deg, var(--nomad-navy) 0%, var(--xcertus-purple) 100%);
        color: #fff;
    }

    .registro-auth-modal .nav-tabs .nav-link.active {
        color: var(--xcertus-purple);
        font-weight: 700;
    }

    .registro-success-copy {
        color: rgba(24, 28, 54, 0.7);
    }

    .registro-qr-box {
        display: flex;
        justify-content: center;
        margin-bottom: 18px;
    }

    .registro-manual-data {
        padding: 16px;
        border-radius: 14px;
        background: rgba(24, 28, 54, 0.04);
        text-align: left;
    }

    .registro-plan-summary {
        margin: 0 auto 18px;
        padding: 12px 16px;
        border-radius: 14px;
        background: rgba(45, 156, 219, 0.08);
        color: var(--nomad-navy);
        max-width: 420px;
        text-align: center;
    }

    .registro-plan-summary strong {
        display: block;
        font-size: 1rem;
        margin-bottom: 4px;
    }

    .compatibility-link-wrap {
        margin-top: 14px;
        text-align: center;
    }

    .compatibility-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: var(--xcertus-purple);
        text-decoration: none;
    }

    .compatibility-link:hover,
    .compatibility-link:focus {
        color: var(--nomad-navy);
        text-decoration: none;
    }

    .compatibility-modal .modal-dialog {
        max-width: 860px;
    }

    .compatibility-modal .modal-header {
        background: linear-gradient(90deg, var(--nomad-navy) 0%, var(--xcertus-purple) 100%);
        color: #fff;
    }

    .compatibility-modal .modal-body {
        max-height: 72vh;
        overflow-y: auto;
        background: linear-gradient(180deg, rgba(45, 156, 219, 0.04) 0%, rgba(255, 255, 255, 1) 28%);
    }

    .compatibility-intro {
        color: rgba(24, 28, 54, 0.72);
        font-size: 0.92rem;
        margin-bottom: 18px;
    }

    .compatibility-group {
        border: 1px solid rgba(24, 28, 54, 0.12);
        border-radius: 16px;
        background: #fff;
        margin-bottom: 12px;
        overflow: hidden;
        box-shadow: 0 10px 24px rgba(24, 28, 54, 0.06);
    }

    .compatibility-summary {
        width: 100%;
        border: 0;
        background: transparent;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        font-weight: 700;
        color: var(--nomad-navy);
        text-align: left;
    }

    .compatibility-summary:focus {
        outline: none;
        box-shadow: inset 0 0 0 2px rgba(45, 156, 219, 0.2);
    }

    .compatibility-summary::after {
        content: '+';
        font-size: 1.35rem;
        line-height: 1;
        color: var(--xcertus-purple);
        flex-shrink: 0;
        transition: transform 0.28s ease, color 0.28s ease;
    }

    .compatibility-group.is-open .compatibility-summary::after {
        transform: rotate(45deg);
        color: var(--nomad-navy);
    }

    .compatibility-count {
        font-size: 0.82rem;
        font-weight: 600;
        color: rgba(24, 28, 54, 0.54);
        white-space: nowrap;
    }

    .compatibility-content {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: max-height 0.35s ease, opacity 0.25s ease;
    }

    .compatibility-group.is-open .compatibility-content {
        opacity: 1;
    }

    .compatibility-content-inner {
        padding: 0 18px 18px;
    }

    .compatibility-model-list {
        margin: 0;
        padding-left: 18px;
        color: rgba(24, 28, 54, 0.82);
        columns: 2;
        column-gap: 24px;
    }

    .compatibility-model-list li {
        break-inside: avoid;
        margin-bottom: 6px;
    }

    .compatibility-note {
        margin-top: 14px;
        padding: 14px 16px;
        border-radius: 14px;
        background: rgba(24, 28, 54, 0.05);
    }

    .compatibility-note h6 {
        margin-bottom: 8px;
        font-weight: 700;
        color: var(--nomad-navy);
    }

    .compatibility-note p,
    .compatibility-note li {
        margin-bottom: 6px;
        color: rgba(24, 28, 54, 0.72);
    }

    .compatibility-note ul {
        margin: 0;
        padding-left: 18px;
    }
    
    @media (max-width: 576px) {
        .auth-form-light { padding: 2rem 1.5rem !important; }
        .logo-nomad { max-height: 34px; max-width: 60%; }
        .logo-partner { max-height: 45px; }
        .brand-footnote { font-size: 0.68rem; }
        .inline-plans-panel { padding: 18px; }
        .compatibility-model-list { columns: 1; }
        .compatibility-summary {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

@php
    $displayPartner = $brandPartner ?? $beneficiario ?? $superPartner ?? null;
    $displayPartnerName = $displayPartner->nombre ?? null;
    $displayPartnerLogo = $displayPartner->logo_url ?? null;
    $selectedCountryForPlans = session('selected_country', old('country_code'));
    $showAvailablePlans = session('show_available_plans') && !empty($selectedCountryForPlans);
    $esimData = $esim_data ?? session('esim_data');
    $esimEmailStatus = $esim_email_status ?? session('esim_email_status');
    $showFreeEsimForm = !$showAvailablePlans && empty($esimData);
    $selectedCountryForForm = old('country_code');
    $selectedCountryOption = collect($affordableCountries ?? [])->firstWhere('code', $selectedCountryForForm);
    $selectedCountryAutocompleteValue = $selectedCountryOption['name'] ?? '';
    $partnerAffordableCountryCodes = $partnerAffordableCountryCodes ?? [];
    $registrationCountryOptions = collect($affordableCountries ?? [])->map(function ($country) use ($partnerAffordableCountryCodes) {
        $isAffordableByTariff = isset($country['price']) && (float) $country['price'] <= \App\Helpers\CountryTariffHelper::AFFORDABLE_TARIFF_THRESHOLD;
        $isAffordableForPartner = in_array($country['code'], $partnerAffordableCountryCodes, true);
        return [
            'code' => $country['code'],
            'name' => $country['name'],
            'emoji' => \App\Helpers\CountryTariffHelper::getCountryEmoji($country['code']),
            'is_affordable' => $isAffordableByTariff || $isAffordableForPartner,
            'price' => $country['price'] ?? null,
        ];
    })->values()->all();
    $compatibleDeviceBrands = [
        [
            'brand' => 'Apple',
            'models' => [
                'iPhone Air', 'iPhone 17 Pro Max', 'iPhone 17 Pro', 'iPhone 17', 'iPhone 16e', 'iPhone 16 Pro Max',
                'iPhone 16 Plus', 'iPhone 16 Pro', 'iPhone 16', 'iPhone 15 Pro Max', 'iPhone 15 Plus', 'iPhone 15 Pro',
                'iPhone 15', 'iPhone 14 Pro Max', 'iPhone 14 Plus', 'iPhone 14 Pro', 'iPhone 14', 'iPhone SE 3 (2022)',
                'iPhone 13 Pro Max', 'iPhone 13 Pro', 'iPhone 13 Mini', 'iPhone 13', 'iPhone 12 Pro Max', 'iPhone 12 Pro',
                'iPhone 12 Mini', 'iPhone 12', 'iPhone SE 2 (2020)', 'iPhone 11 Pro Max', 'iPhone 11 Pro', 'iPhone 11',
                'iPhone XS Max', 'iPhone XS', 'iPhone XR',
            ],
            'note' => [
                'title' => 'Importante',
                'paragraphs' => [
                    'Los iPhone vendidos en China continental no son compatibles con eSIM, y solo algunos modelos vendidos en Hong Kong y Macao la admiten. Si compraste tu iPhone en alguno de esos mercados, verifica la compatibilidad antes de instalarla.',
                ],
            ],
        ],
        [
            'brand' => 'Google',
            'models' => [
                'Google Pixel 10 Pro XL', 'Google Pixel 10 Pro', 'Google Pixel 10', 'Google Pixel 9 Pro Fold', 'Google Pixel 9 Pro XL',
                'Google Pixel 9 Pro', 'Google Pixel 9', 'Google Pixel 9a', 'Google Pixel Fold', 'Google Pixel 8 Pro', 'Google Pixel 8',
                'Google Pixel 8a', 'Google Pixel 7 Pro', 'Google Pixel 7', 'Google Pixel 7a', 'Google Pixel 6 Pro', 'Google Pixel 6a',
                'Google Pixel 6', 'Google Pixel 5a', 'Google Pixel 5', 'Google Pixel 4 XL', 'Google Pixel 4a', 'Google Pixel 4',
                'Google Pixel 3a XL', 'Google Pixel 3a', 'Google Pixel 3 XL', 'Google Pixel 3', 'Google Pixel 2 XL', 'Google Pixel 2',
            ],
            'note' => [
                'title' => 'Importante',
                'paragraphs' => ['Los siguientes dispositivos Google Pixel no tienen capacidad eSIM:'],
                'items' => [
                    'Modelos Pixel 3 originarios de Australia, Taiwan y Japon, y aquellos adquiridos con servicio de operadores estadounidenses o canadienses distintos de Sprint y Google Fi.',
                    'Modelos Pixel 3a comprados en el sudeste asiatico y con servicio de Verizon.',
                ],
            ],
        ],
        [
            'brand' => 'Hammer',
            'models' => ['Hammer Explorer PRO', 'Hammer Blade 3', 'Hammer Blade 5G', 'Hammer myPhone NOW eSIM', 'Hammer myPhone Hammer Construction'],
        ],
        [
            'brand' => 'Honor',
            'models' => [
                'Honor 400 Pro', 'Honor 400', 'Honor 400 Lite', 'Honor Magic7 Lite', 'Honor 50', 'Honor X8', 'Honor 90',
                'Honor Magic6 Pro', 'Honor Magic6 Pro RSR', 'Honor Magic5 Pro', 'Honor Magic4 Pro', 'Honor 200 Pro', 'Honor 200',
                'Honor Magic Vs3', 'Honor Magic V2', 'Honor Magic V3',
            ],
        ],
        [
            'brand' => 'Huawei',
            'models' => ['Huawei Mate 40 Pro', 'Huawei P40 Pro', 'Huawei P40'],
            'note' => [
                'title' => 'Importante',
                'paragraphs' => ['Todos los dispositivos Huawei comprados en China continental no son compatibles con eSIM.'],
            ],
        ],
        [
            'brand' => 'Motorola',
            'models' => [
                'Motorola Razr (2025)', 'Motorola Razr+ (2025)', 'Motorola Razr Ultra (2025)', 'Motorola Razr+', 'Motorola G52J 5G',
                'Motorola G52J 5G II', 'Motorola G53J 5G', 'Motorola Moto G (2025)', 'Motorola Moto G34', 'Motorola Moto G35',
                'Motorola Moto G53', 'Motorola Moto G54', 'Motorola Moto G54 Power', 'Motorola Moto G55', 'Motorola Moto G75',
                'Motorola Moto G84', 'Motorola Moto G85', 'Motorola Moto G86', 'Motorola Moto G (2024)', 'Motorola Moto G Power (2024)',
                'Motorola Moto G Stylus 5G (2023)', 'Motorola Moto G Stylus 5G (2024)', 'Motorola Edge Fusion', 'Motorola Edge 60',
                'Motorola Edge 60 Pro', 'Motorola Edge 60 Fusion', 'Motorola Edge 60 Stylus', 'Motorola Edge 50', 'Motorola Edge 50 Fusion',
                'Motorola Edge 50 Pro', 'Motorola Edge 50 Neo', 'Motorola Edge 50 Ultra', 'Motorola Edge 40 Neo', 'Motorola Edge 40 Pro',
                'Motorola Edge 40', 'Motorola Edge+', 'Motorola Edge+ (2023)', 'Motorola Edge (2024)', 'Motorola Edge (2023)',
                'Motorola Edge (2022)', 'Motorola Razr 40', 'Motorola Razr 40 Ultra', 'Motorola Razr 60', 'Motorola Razr 50',
                'Motorola Razr 50 Ultra', 'Motorola Razr 2024', 'Motorola Razr+ 2024', 'Motorola Razr 2022', 'Motorola Razr 2019',
                'Motorola Razr 5G', 'Motorola ThinkPhone 25',
            ],
        ],
        [
            'brand' => 'OnePlus',
            'models' => ['OnePlus Open', 'OnePlus 11', 'OnePlus 12', 'OnePlus 13', 'OnePlus 13R'],
        ],
        [
            'brand' => 'Oppo',
            'models' => [
                'Oppo Find X3 Pro', 'Oppo Find N2 Flip', 'Oppo Find N5', 'Oppo Reno 5A', 'Oppo Reno 6 Pro 5G', 'Oppo Reno 9A',
                'Oppo Find X5', 'Oppo Find X5 Pro', 'Oppo A55s 5G', 'Oppo Find X8 Pro', 'Oppo Find X8', 'Oppo Find X3',
                'Oppo Reno14', 'Oppo Reno14 Pro',
            ],
        ],
        [
            'brand' => 'Others',
            'models' => [
                'Nokia G60 5G', 'Nokia X30', 'Nokia XR21', 'Rakuten Big', 'Rakuten Big-S', 'Rakuten Mini', 'Rakuten Hand',
                'Rakuten Hand 5G', 'Nuu X5', 'Fairphone 4', 'Fairphone 5', 'T-Mobile Revvl 7', 'T-Mobile Revvl 7 Pro', 'Gemini PDA 4G+Wi-Fi',
                'Nothing Phone (3a) Pro', 'Realme 14 Pro+', 'ASUS Zenfone 12 Ultra', 'ZTE nubia Flip2', 'Alcatel V3 Ultra', 'Trump Mobile T1',
            ],
        ],
        [
            'brand' => 'Samsung',
            'models' => [
                'Samsung Galaxy XCover7 Pro', 'Samsung Galaxy A56', 'Samsung Galaxy A55 5G', 'Samsung Galaxy A54 5G', 'Samsung Galaxy A36',
                'Samsung Galaxy A35 5G', 'Samsung Galaxy A23 5G', 'Samsung Galaxy Z Flip5', 'Samsung Galaxy Z Flip6', 'Samsung Galaxy Z Fold',
                'Samsung Galaxy Z Fold3', 'Samsung Galaxy Z Fold5', 'Samsung Galaxy Z Fold6', 'Samsung Galaxy S25', 'Samsung Galaxy S25 Edge',
                'Samsung Galaxy S25+', 'Samsung Galaxy S25 Ultra', 'Samsung Galaxy S25 Slim', 'Samsung Galaxy S24 Ultra', 'Samsung Galaxy S24+',
                'Samsung Galaxy S24 FE', 'Samsung Galaxy S24', 'Samsung Galaxy S23 FE', 'Samsung Galaxy S23 Ultra', 'Samsung Galaxy S23+',
                'Samsung Galaxy S23', 'Samsung Galaxy S22 Ultra', 'Samsung Galaxy S22+', 'Samsung Galaxy S22', 'Samsung Galaxy S21+ Ultra 5G',
                'Samsung Galaxy S21+ 5G', 'Samsung Galaxy S21', 'Samsung Galaxy S20 Ultra 5G', 'Samsung Galaxy S20 Ultra', 'Samsung Galaxy S20+ 5G',
                'Samsung Galaxy S20+', 'Samsung Galaxy S20', 'Samsung Galaxy Z Fold5 5G', 'Samsung Galaxy Z Fold4', 'Samsung Galaxy Z Fold3 5G',
                'Samsung Galaxy Z Fold2 5G', 'Samsung Galaxy Fold', 'Samsung Galaxy Z Flip5 5G', 'Samsung Galaxy Z Flip4', 'Samsung Galaxy Z Flip3 5G',
                'Samsung Galaxy Z Flip', 'Samsung Galaxy A54', 'Samsung Galaxy Note 20 Ultra 5G', 'Samsung Galaxy Note 20',
            ],
            'note' => [
                'title' => 'Importante',
                'paragraphs' => ['Los siguientes dispositivos Samsung Galaxy no son compatibles con eSIM:'],
                'items' => [
                    'Todos los dispositivos Galaxy originarios de China continental, Hong Kong y Taiwan.',
                    'Todos los modelos Galaxy FE, excepto Galaxy S23 FE y S24 FE.',
                    'Modelos estadounidenses de Galaxy S20, S21 y Note 20 Ultra.',
                    'La mayoria de los Samsung Galaxy comprados en Corea del Sur, excepto Galaxy S24, S23, Z Fold 5, Z Fold 4, Z Flip 5, Z Flip 4 y A54 5G.',
                ],
            ],
        ],
        [
            'brand' => 'Sharp',
            'models' => [
                'Sharp AQUOS R10', 'Sharp AQUOS R9 Pro', 'Sharp AQUOS R9', 'Sharp AQUOS R8 Pro', 'Sharp AQUOS R8', 'Sharp AQUOS R7',
                'Sharp Simple Sumaho 6', 'Sharp AQUOS zero6', 'Sharp AQUOS wish3', 'Sharp AQUOS wish 2 SHG08', 'Sharp AQUOS wish',
                'Sharp AQUOS sense7 plus', 'Sharp AQUOS sense7', 'Sharp AQUOS sense6s', 'Sharp AQUOS sense4 lite', 'Sharp AQUOS sense9',
                'Sharp AQUOS sense8',
            ],
        ],
        [
            'brand' => 'Sony',
            'models' => [
                'Sony Xperia 10 III Lite', 'Sony Xperia 10 VI', 'Sony Xperia 10 V', 'Sony Xperia 10 IV', 'Sony Xperia 1 VI', 'Sony Xperia 1 V',
                'Sony Xperia 1 IV', 'Sony Xperia 5 IV', 'Sony Xperia Ace III', 'Sony Xperia 5 V',
            ],
        ],
        [
            'brand' => 'TCL',
            'models' => ['TCL 60', 'TCL 60 XE NxtPaper', 'TCL 50 5G', 'TCL 50 NxtPaper', 'TCL 50 Pro NxtPaper', 'TCL 40 XL'],
        ],
        [
            'brand' => 'Vivo',
            'models' => [
                'Vivo X200 Pro', 'Vivo X200', 'Vivo X200s', 'Vivo X200 FE', 'Vivo X100 Pro', 'Vivo X90 Pro', 'Vivo V29', 'Vivo V29 Lite 5G',
                'Vivo V40', 'Vivo V40 Lite', 'Vivo V50',
            ],
        ],
        [
            'brand' => 'Xiaomi',
            'models' => [
                'Xiaomi 15', 'Xiaomi 15 Ultra', 'Xiaomi 14', 'Xiaomi 14 Pro', 'Xiaomi 14T', 'Xiaomi 14T Pro', 'Xiaomi 13T', 'Xiaomi 13T Pro',
                'Xiaomi 13 Pro', 'Xiaomi 13 Lite', 'Xiaomi 13', 'Xiaomi 12T Pro', 'Xiaomi Poco X7', 'Xiaomi Redmi Note 14 Pro',
                'Xiaomi Redmi Note 14 Pro 5G', 'Xiaomi Redmi Note 14 Pro+', 'Xiaomi Redmi Note 14 Pro+ 5G', 'Xiaomi Redmi Note 13 Pro',
                'Xiaomi Redmi Note 13 Pro+', 'Xiaomi Redmi Note 11 Pro 5G',
            ],
        ],
    ];
    $plansBaseUrl = isset($referralCode)
        ? route('planes.index', ['referralCode' => $referralCode])
        : route('planes.index');

    if (!$displayPartnerLogo && $displayPartner && !empty($displayPartner->logo)) {
        $displayPartnerLogo = asset('storage/' . $displayPartner->logo);
    }
@endphp

<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5 shadow-sm rounded">
                        
                        {{-- 1. HEADER LOGOS (Estructura de Triángulo) --}}
                        <div class="text-center mb-4">
                            <p class="small text-muted text-uppercase mb-3 font-weight-bold" style="letter-spacing: 1px;">Alianza Corporativa</p>
                            
                            <div class="brand-alliance-container">
                                {{-- Fila Superior: Nomad --}}
                                <div class="top-row-logos single-brand">
                                    <img src="{{ asset('images/nomadesim.png') }}" alt="Nomad eSIM" class="logo-img logo-nomad">
                                </div>

                                {{-- Fila Inferior: Partner o Super Partner --}}
                                @if($displayPartner && $displayPartnerLogo)
                                    <div class="partner-row-logo animate__animated animate__zoomIn">
                                        <img src="{{ $displayPartnerLogo }}"
                                             alt="{{ $displayPartnerName }}"
                                             class="logo-img logo-partner">
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- 2. CONTENIDO PRINCIPAL --}}
                        @if($displayPartner)
                            <div class="alert alert-success animate__animated animate__fadeIn mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-account-check mr-2" style="font-size: 1.5rem;"></i>
                                    <div class="text-break">
                                        <strong>Exclusivo para clientes de:</strong> {{ $displayPartnerName }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger mb-3">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0 pl-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($esimData))
                            <div class="text-center mb-4">
                                <h4 class="mb-2 font-weight-bold" style="color: var(--nomad-navy);">Has activado una eSIM</h4>
                                <p class="text-muted mb-0 small">Escanea el QR o usa los datos manuales para terminar la activación.</p>
                            </div>

                            @if(!empty($esimEmailStatus))
                                <div class="alert {{ !empty($esimEmailStatus['sent']) ? 'alert-success' : 'alert-warning' }} mb-4">
                                    {{ $esimEmailStatus['message'] }}
                                </div>
                            @endif

                            <div class="registro-plan-summary">
                                <strong>{{ ($esimData['data_amount'] ?? 'N/A') . ' GB' }}</strong>
                                <span>{{ ($esimData['duration_days'] ?? 'N/A') . ' días de duración' }}</span>
                            </div>

                            <div class="registro-qr-box">{!! $esimData['qr_svg'] ?? '' !!}</div>

                            <div class="registro-manual-data mb-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">SM-DP+ Address</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" readonly id="registro-smdp-input" value="{{ $esimData['smdp'] ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-smdp-input">Copiar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">ICCID</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" readonly id="registro-iccid-input" value="{{ $esimData['iccid'] ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-iccid-input">Copiar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">Código de activación</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" readonly id="registro-code-input" value="{{ $esimData['code'] ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-code-input">Copiar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($showFreeEsimForm)
                            <h4 class="text-center mb-2 font-weight-bold" style="color: var(--nomad-navy);">Activar eSIM gratis</h4>
                            <p class="text-center text-muted mb-4 small">Completa tus datos para validar si tienes habilitada la activación gratuita.</p>

                            <form class="pt-3" method="POST" action="{{ route('registro.esim.store') }}" id="registro-esim-form">
                                @csrf
                                @if(isset($referralCode))
                                    <input type="hidden" name="referralCode" value="{{ $referralCode }}">
                                @endif

                                <div class="form-group">
                                    <label for="identificador" class="font-weight-bold text-small">DNI o Pasaporte</label>
                                    <input type="text" class="form-control form-control-lg" name="identificador" value="{{ old('identificador') }}" placeholder="Ingrese su número de documento o pasaporte" required>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="font-weight-bold text-small">Email</label>
                                    <input type="email" class="form-control form-control-lg" name="email" placeholder="Ingrese su correo electrónico asignado" value="{{ old('email') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="country_code" class="font-weight-bold text-small">Seleccione su País de destino</label>
                                    <div class="country-autocomplete">
                                        <input type="text"
                                               class="form-control form-control-lg country-search-input"
                                               id="registro-country-autocomplete"
                                               value="{{ $selectedCountryAutocompleteValue }}"
                                               placeholder="Escribe y selecciona un país"
                                               autocomplete="off"
                                               required>
                                        <div id="registro-country-suggestions" class="country-suggestions d-none" role="listbox" aria-label="Paises sugeridos"></div>
                                    </div>
                                    <input type="hidden"
                                           name="country_code"
                                           id="registro-country-code"
                                           value="{{ $selectedCountryForForm }}"
                                         data-plans-base-url="{{ $plansBaseUrl }}">
                                     <small class="form-text text-muted mt-2">Selecciona el país de destino. Si no aplica para eSIM gratis, te llevamos directo a sus planes disponibles.</small>
                                     <div class="compatibility-link-wrap">
                                        <a href="#" class="compatibility-link" data-toggle="modal" data-target="#deviceCompatibilityModal">
                                            <i class="mdi mdi-cellphone-link"></i>
                                            <span>Verifica la compatibilidad de tu dispositivo</span>
                                        </a>
                                     </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-block btn-brand-gradient btn-lg font-weight-medium">
                                        Obtener eSIM Gratis
                                    </button>
                                </div>
                            </form>
                        @elseif($showAvailablePlans)
                            <div class="text-center mb-3">
                                <h4 class="mb-2 font-weight-bold" style="color: var(--nomad-navy);">Planes disponibles</h4>
                                <p class="text-muted mb-0 small">Tu eSIM gratis no está habilitada. Puedes continuar comprando un plan para el país que ya seleccionaste.</p>
                            </div>

                            <div id="registro-available-plans-app"
                                 data-country="{{ $selectedCountryForPlans }}"
                                 data-plans-endpoint="{{ route('planes.get') }}"
                                 data-auth-check-endpoint="{{ route('api.auth.check') }}"
                                 data-auth-login-endpoint="{{ route('api.auth.login') }}"
                                 data-auth-register-endpoint="{{ route('api.auth.register') }}"
                                 data-payment-intent-endpoint="{{ route('planes.payment.intent') }}"
                                 data-process-payment-endpoint="{{ route('planes.pago') }}"
                                 data-activate-free-endpoint="{{ route('planes.activar.gratis') }}"
                                 data-stripe-public-key="{{ $stripePublicKey ?? '' }}">
                                <div class="inline-plans-panel">
                                    <h5 class="inline-plans-title">Planes disponibles para tu país</h5>
                                    <p class="inline-plans-copy mb-0">
                                        Ya usamos el país que seleccionaste para que no tengas que elegirlo de nuevo. Te mostramos primero las opciones más económicas.
                                    </p>

                                    <div class="inline-plans-status inline-plans-loading mt-3" id="registro-plans-status">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                        <div>Cargando planes disponibles...</div>
                                    </div>

                                    <div class="inline-plans-grid mt-3 d-none" id="registro-plans-grid"></div>

                                    <div class="alert alert-danger mt-3 mb-0 d-none" role="alert" id="registro-plans-error"></div>
                                </div>
                            </div>

                            <div class="modal fade registro-auth-modal" id="registroAuthModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Iniciar sesión o registrarse</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="nav nav-tabs mb-4" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab" href="#registroLoginTab">Iniciar sesión</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#registroRegisterTab">Registrarse</a>
                                                </li>
                                            </ul>

                                            <div class="tab-content">
                                                <div id="registroLoginTab" class="tab-pane fade show active">
                                                    <form id="registro-login-form">
                                                        <div class="form-group">
                                                            <label>Email</label>
                                                            <input type="email" class="form-control" id="registro-login-email" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Contraseña</label>
                                                            <input type="password" class="form-control" id="registro-login-password" required>
                                                        </div>
                                                        <div class="alert alert-danger d-none" id="registro-auth-error"></div>
                                                        <button type="submit" class="btn btn-brand-gradient btn-block" id="registro-login-submit">
                                                            Iniciar sesión
                                                        </button>
                                                    </form>
                                                </div>

                                                <div id="registroRegisterTab" class="tab-pane fade">
                                                    <form id="registro-register-form">
                                                        <div class="form-group">
                                                            <label>Nombre</label>
                                                            <input type="text" class="form-control" id="registro-register-nombre" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Apellido</label>
                                                            <input type="text" class="form-control" id="registro-register-apellido" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Email</label>
                                                            <input type="email" class="form-control" id="registro-register-email" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Contraseña</label>
                                                            <input type="password" class="form-control" id="registro-register-password" required minlength="6">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Confirmar contraseña</label>
                                                            <input type="password" class="form-control" id="registro-register-password-confirmation" required>
                                                        </div>
                                                        <div class="alert alert-danger d-none" id="registro-register-error"></div>
                                                        <button type="submit" class="btn btn-brand-gradient btn-block" id="registro-register-submit">
                                                            Registrarse
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade registro-payment-modal" id="registroPaymentModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirmar pago</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="registro-payment-summary" class="d-none">
                                                <h5>Plan seleccionado</h5>
                                                <p id="registro-payment-plan-copy"></p>
                                                <h4 class="mb-4" id="registro-payment-total"></h4>

                                                <div id="registro-card-element" class="form-control mb-3" style="padding: 12px;"></div>
                                                <div id="registro-card-errors" class="text-danger mb-3"></div>

                                                <button type="button" class="btn btn-brand-gradient btn-block" id="registro-pay-button">
                                                    Pagar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="registroSuccessModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body p-4 p-sm-5 text-center">
                                            <div class="success-icon mb-3">
                                                <i class="mdi mdi-check-circle text-success" style="font-size: 3rem;"></i>
                                            </div>
                                            <h3>Has activado una eSIM</h3>
                                            <p class="registro-success-copy mb-4">Tu eSIM ya está lista. Aquí mismo tienes el QR y los datos manuales para activarla.</p>

                                            <div id="registro-success-content" class="d-none">
                                                <div class="registro-plan-summary" id="registro-success-plan-summary">
                                                    <strong id="registro-success-plan-amount"></strong>
                                                    <span id="registro-success-plan-duration"></span>
                                                </div>

                                                <div class="registro-qr-box" id="registro-success-qr"></div>

                                                <div class="registro-manual-data">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">SM-DP+ Address</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" readonly id="registro-smdp-modal-input">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-smdp-modal-input">Copiar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">ICCID</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" readonly id="registro-iccid-modal-input">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-iccid-modal-input">Copiar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="font-weight-bold">Código de activación</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" readonly id="registro-code-modal-input">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-code-modal-input">Copiar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="button" class="btn btn-brand-gradient mt-4" data-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="modal fade compatibility-modal" id="deviceCompatibilityModal" tabindex="-1" role="dialog" aria-labelledby="deviceCompatibilityModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deviceCompatibilityModalLabel">Compatibilidad de dispositivos eSIM</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="compatibility-intro mb-3">
                                            Revisa tu marca y despliega la lista para validar si tu equipo aparece entre los dispositivos compatibles con eSIM.
                                        </p>

                                        @foreach($compatibleDeviceBrands as $brandGroup)
                                            <div class="compatibility-group" data-compatibility-group>
                                                <button type="button" class="compatibility-summary" data-compatibility-toggle aria-expanded="false">
                                                    <span>{{ $brandGroup['brand'] }}</span>
                                                    <span class="compatibility-count">{{ count($brandGroup['models']) }} modelos</span>
                                                </button>

                                                <div class="compatibility-content" data-compatibility-content>
                                                    <div class="compatibility-content-inner">
                                                        <ul class="compatibility-model-list">
                                                            @foreach($brandGroup['models'] as $model)
                                                                <li>{{ $model }}</li>
                                                            @endforeach
                                                        </ul>

                                                        @if(!empty($brandGroup['note']))
                                                            <div class="compatibility-note">
                                                                <h6>{{ $brandGroup['note']['title'] ?? 'Importante' }}</h6>

                                                                @foreach($brandGroup['note']['paragraphs'] ?? [] as $paragraph)
                                                                    <p>{{ $paragraph }}</p>
                                                                @endforeach

                                                                @if(!empty($brandGroup['note']['items']))
                                                                    <ul>
                                                                        @foreach($brandGroup['note']['items'] as $item)
                                                                            <li>{{ $item }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="brand-footnote px-4 px-sm-5">
                        Servicio de Nomad eSIM con distribución para Iberoamérica mediante alianza con Xcertus.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
@if($showAvailablePlans)
<script src="https://js.stripe.com/v3/"></script>
@endif

<script type="application/json" id="registro-country-options-json">{!! json_encode($registrationCountryOptions) !!}</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countryAutocomplete = document.getElementById('registro-country-autocomplete');
    const countryCodeInput = document.getElementById('registro-country-code');
    const countryForm = document.getElementById('registro-esim-form');
    const countrySuggestions = document.getElementById('registro-country-suggestions');
    const countryOptionsElement = document.getElementById('registro-country-options-json');
    const availablePlansContainer = document.getElementById('registro-available-plans-app');
    const compatibilityGroups = Array.from(document.querySelectorAll('[data-compatibility-group]'));

    if (compatibilityGroups.length) {
        const setCompatibilityState = function(group, shouldOpen) {
            const toggle = group.querySelector('[data-compatibility-toggle]');
            const content = group.querySelector('[data-compatibility-content]');

            if (!toggle || !content) {
                return;
            }

            group.classList.toggle('is-open', shouldOpen);
            toggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');

            if (shouldOpen) {
                content.style.maxHeight = content.scrollHeight + 'px';
                return;
            }

            content.style.maxHeight = '0px';
        };

        compatibilityGroups.forEach(function(group) {
            const toggle = group.querySelector('[data-compatibility-toggle]');
            const content = group.querySelector('[data-compatibility-content]');

            if (!toggle || !content) {
                return;
            }

            content.style.maxHeight = '0px';

            toggle.addEventListener('click', function() {
                setCompatibilityState(group, !group.classList.contains('is-open'));
            });
        });

        window.addEventListener('resize', function() {
            compatibilityGroups.forEach(function(group) {
                if (!group.classList.contains('is-open')) {
                    return;
                }

                const content = group.querySelector('[data-compatibility-content]');

                if (content) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                }
            });
        });

        $('#deviceCompatibilityModal').on('shown.bs.modal', function() {
            compatibilityGroups.forEach(function(group) {
                setCompatibilityState(group, false);
            });
        });
    }

    if (countryAutocomplete && countryCodeInput && countrySuggestions && countryOptionsElement) {
        const countryOptions = JSON.parse(countryOptionsElement.textContent || '[]');
        const plansBaseUrl = countryCodeInput.dataset.plansBaseUrl || '';
        let activeSuggestionIndex = -1;

        const buildPlansUrl = function(countryCode) {
            if (!plansBaseUrl || !countryCode) {
                return '';
            }

            return plansBaseUrl + (plansBaseUrl.indexOf('?') === -1 ? '?' : '&') + 'country=' + encodeURIComponent(countryCode);
        };

        const hideSuggestions = function() {
            countrySuggestions.classList.add('d-none');
            countrySuggestions.innerHTML = '';
            activeSuggestionIndex = -1;
        };

        const getFilteredCountries = function() {
            const typedValue = countryAutocomplete.value.trim().toLowerCase();

            if (!typedValue) {
                return countryOptions.slice(0, 8);
            }

            return countryOptions.filter(function(option) {
                return (option.name || '').toLowerCase().includes(typedValue)
                    || (option.code || '').toLowerCase().includes(typedValue);
            }).slice(0, 8);
        };

        const applySuggestionSelection = function(option, shouldRedirect) {
            if (!option) {
                return;
            }

            countryAutocomplete.value = option.name || '';
            countryCodeInput.value = option.code || '';
            countryAutocomplete.setCustomValidity('');
            hideSuggestions();

            if (shouldRedirect && option.is_affordable === false) {
                const plansUrl = buildPlansUrl(option.code || '');

                if (plansUrl) {
                    window.location.href = plansUrl;
                }
            }
        };

        const renderSuggestions = function() {
            const filteredCountries = getFilteredCountries();

            if (!filteredCountries.length) {
                countrySuggestions.innerHTML = '<div class="country-suggestion-empty">No encontramos paises con ese criterio.</div>';
                countrySuggestions.classList.remove('d-none');
                activeSuggestionIndex = -1;
                return;
            }

            countrySuggestions.innerHTML = filteredCountries.map(function(option, index) {
                const activeClass = index === activeSuggestionIndex ? ' is-active' : '';
                return '<button type="button" class="country-suggestion-item' + activeClass + '" data-index="' + index + '">' +
                    '<span>' + (option.emoji || '🌍') + '</span>' +
                    '<span>' + (option.name || '') + (option.is_affordable === false ? ' <small style="color:rgba(24,28,54,0.55);">- ver planes</small>' : '') + '</span>' +
                    '</button>';
            }).join('');

            countrySuggestions.classList.remove('d-none');

            Array.from(countrySuggestions.querySelectorAll('.country-suggestion-item')).forEach(function(button, index) {
                button.addEventListener('mousedown', function(event) {
                    event.preventDefault();
                    applySuggestionSelection(filteredCountries[index], true);
                });
            });
        };

        const syncCountryCode = function(shouldRedirect) {
            const typedValue = countryAutocomplete.value.trim().toLowerCase();
            const matchedOption = countryOptions.find(function(option) {
                return (option.name || '').trim().toLowerCase() === typedValue;
            });

            if (!matchedOption) {
                countryCodeInput.value = '';
                countryAutocomplete.setCustomValidity('Selecciona un pais de la lista.');
                return;
            }

            applySuggestionSelection(matchedOption, shouldRedirect);
        };

        countryAutocomplete.addEventListener('input', function() {
            countryCodeInput.value = '';
            countryAutocomplete.setCustomValidity('');
            activeSuggestionIndex = -1;
            renderSuggestions();
        });

        countryAutocomplete.addEventListener('focus', function() {
            activeSuggestionIndex = -1;
            renderSuggestions();
        });

        countryAutocomplete.addEventListener('change', function() {
            syncCountryCode(true);
        });

        countryAutocomplete.addEventListener('keydown', function(event) {
            const filteredCountries = getFilteredCountries();

            if (!filteredCountries.length) {
                return;
            }

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                activeSuggestionIndex = Math.min(activeSuggestionIndex + 1, filteredCountries.length - 1);
                renderSuggestions();
                return;
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                activeSuggestionIndex = Math.max(activeSuggestionIndex - 1, 0);
                renderSuggestions();
                return;
            }

            if (event.key === 'Enter' && activeSuggestionIndex >= 0) {
                event.preventDefault();
                applySuggestionSelection(filteredCountries[activeSuggestionIndex], true);
                return;
            }

            if (event.key === 'Escape') {
                hideSuggestions();
            }
        });

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.country-autocomplete')) {
                hideSuggestions();
            }
        });

        if (countryForm) {
            countryForm.addEventListener('submit', function(event) {
                syncCountryCode(false);

                if (!countryCodeInput.value) {
                    event.preventDefault();
                    countryAutocomplete.reportValidity();
                    return;
                }

                const matchedOption = countryOptions.find(function(option) {
                    return (option.code || '').toUpperCase() === (countryCodeInput.value || '').toUpperCase();
                });

                if (matchedOption && matchedOption.is_affordable === false) {
                    event.preventDefault();
                    const plansUrl = buildPlansUrl(matchedOption.code || '');

                    if (plansUrl) {
                        window.location.href = plansUrl;
                    }
                }
            });
        }
    }

    document.addEventListener('click', function(event) {
        const copyButton = event.target.closest('[data-copy-target]');

        if (!copyButton) {
            return;
        }

        event.preventDefault();
        copyInputValue(copyButton.dataset.copyTarget);
    });

    if (!availablePlansContainer) {
        return;
    }

    const state = {
        selectedCountry: availablePlansContainer.dataset.country || '',
        plansEndpoint: availablePlansContainer.dataset.plansEndpoint,
        authCheckEndpoint: availablePlansContainer.dataset.authCheckEndpoint,
        authLoginEndpoint: availablePlansContainer.dataset.authLoginEndpoint,
        authRegisterEndpoint: availablePlansContainer.dataset.authRegisterEndpoint,
        paymentIntentEndpoint: availablePlansContainer.dataset.paymentIntentEndpoint,
        processPaymentEndpoint: availablePlansContainer.dataset.processPaymentEndpoint,
        activateFreeEndpoint: availablePlansContainer.dataset.activateFreeEndpoint,
        stripePublicKey: availablePlansContainer.dataset.stripePublicKey || '',
        plans: [],
        selectedPlan: null,
        isAuthenticated: false,
        paymentIntentId: null,
        paymentProcessing: false,
        stripe: null,
        cardElement: null,
        cardMounted: false
    };

    const statusNode = document.getElementById('registro-plans-status');
    const gridNode = document.getElementById('registro-plans-grid');
    const errorNode = document.getElementById('registro-plans-error');
    const authErrorNode = document.getElementById('registro-auth-error');
    const registerErrorNode = document.getElementById('registro-register-error');
    const paymentSummaryNode = document.getElementById('registro-payment-summary');
    const paymentPlanCopyNode = document.getElementById('registro-payment-plan-copy');
    const paymentTotalNode = document.getElementById('registro-payment-total');
    const payButton = document.getElementById('registro-pay-button');
    const cardErrorsNode = document.getElementById('registro-card-errors');
    const successContentNode = document.getElementById('registro-success-content');
    const successQrNode = document.getElementById('registro-success-qr');
    const successPlanAmountNode = document.getElementById('registro-success-plan-amount');
    const successPlanDurationNode = document.getElementById('registro-success-plan-duration');
    const smdpInput = document.getElementById('registro-smdp-modal-input');
    const iccidInput = document.getElementById('registro-iccid-modal-input');
    const codeInput = document.getElementById('registro-code-modal-input');
    const loginForm = document.getElementById('registro-login-form');
    const registerForm = document.getElementById('registro-register-form');
    const loginSubmitButton = document.getElementById('registro-login-submit');
    const registerSubmitButton = document.getElementById('registro-register-submit');

    function formatDuration(duration, unit) {
        const labels = {
            DAY: 'días',
            DAYS: 'días',
            MONTH: 'meses',
            MONTHS: 'meses',
            YEAR: 'años',
            YEARS: 'años'
        };

        return duration + ' ' + (labels[unit] || unit || '');
    }

    function formatPrice(price, unit) {
        const numericPrice = Number(price);

        if (!Number.isFinite(numericPrice)) {
            return [price, unit].filter(Boolean).join(' ');
        }

        return numericPrice.toFixed(2) + ' ' + (unit || 'USD');
    }

    function showErrorMessage(message) {
        errorNode.textContent = message;
        errorNode.classList.remove('d-none');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }
    }

    function showSuccessMessage(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: message,
                timer: 1800,
                showConfirmButton: false
            });
        }
    }

    function clearInlineError() {
        errorNode.textContent = '';
        errorNode.classList.add('d-none');
    }

    function setLoadingState(isLoading, message) {
        statusNode.classList.toggle('d-none', !isLoading);
        if (isLoading) {
            statusNode.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><div>' + (message || 'Cargando planes disponibles...') + '</div>';
            gridNode.classList.add('d-none');
        }
    }

    function renderPlans() {
        clearInlineError();

        if (!state.plans.length) {
            statusNode.classList.remove('d-none');
            statusNode.innerHTML = 'No encontramos planes disponibles para este país en este momento.';
            gridNode.innerHTML = '';
            gridNode.classList.add('d-none');
            return;
        }

        statusNode.classList.add('d-none');
        gridNode.classList.remove('d-none');
        gridNode.innerHTML = state.plans.map(function(plan, index) {
            const selectedClass = state.selectedPlan && String(state.selectedPlan.id) === String(plan.id) ? ' is-selected' : '';
            return '<div class="inline-plan-card' + selectedClass + '">' +
                '<div class="inline-plan-meta">' + formatDuration(plan.duration, plan.duration_unit) + '</div>' +
                '<div class="inline-plan-data">' + plan.amount + (plan.amount_unit || '') + '</div>' +
                '<div class="inline-plan-price">' + formatPrice(plan.price, plan.price_unit) + '</div>' +
                '<button type="button" class="btn btn-brand-gradient inline-plan-action" data-plan-index="' + index + '">Comprar ahora</button>' +
                '</div>';
        }).join('');
    }

    function updateSelectedCard() {
        Array.from(gridNode.querySelectorAll('.inline-plan-card')).forEach(function(card, index) {
            const plan = state.plans[index];
            const isSelected = state.selectedPlan && plan && String(plan.id) === String(state.selectedPlan.id);
            card.classList.toggle('is-selected', !!isSelected);
        });
    }

    function setButtonBusy(button, isBusy, busyLabel, idleLabel) {
        if (!button) {
            return;
        }

        button.disabled = isBusy;
        button.textContent = isBusy ? busyLabel : idleLabel;
    }

    function setAuthError(node, message) {
        if (!node) {
            return;
        }

        node.textContent = message || '';
        node.classList.toggle('d-none', !message);
    }

    async function checkAuth() {
        try {
            const response = await axios.get(state.authCheckEndpoint);
            state.isAuthenticated = !!response.data.authenticated;
        } catch (error) {
            state.isAuthenticated = false;
        }
    }

    async function loadPlans() {
        setLoadingState(true, 'Cargando planes disponibles...');
        clearInlineError();

        try {
            const response = await axios.post(state.plansEndpoint, {
                country: state.selectedCountry
            });

            if (response.data && response.data.success) {
                state.plans = response.data.products || [];
                renderPlans();
            } else {
                state.plans = [];
                renderPlans();
                showErrorMessage('No fue posible cargar los planes disponibles en este momento.');
            }
        } catch (error) {
            state.plans = [];
            renderPlans();
            showErrorMessage('No fue posible cargar los planes disponibles en este momento.');
        } finally {
            availablePlansContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function mountStripeCard() {
        if (!state.stripePublicKey || typeof Stripe === 'undefined') {
            return false;
        }

        if (!state.stripe) {
            state.stripe = Stripe(state.stripePublicKey);
        }

        if (!state.cardElement) {
            const elements = state.stripe.elements();
            state.cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#32325d'
                    }
                }
            });
        }

        if (!state.cardMounted) {
            state.cardElement.mount('#registro-card-element');
            state.cardMounted = true;
        }

        return true;
    }

    function showPaymentModal() {
        if (!state.selectedPlan) {
            return;
        }

        clearInlineError();
        paymentSummaryNode.classList.remove('d-none');
        paymentPlanCopyNode.innerHTML = '<strong>' + state.selectedPlan.amount + (state.selectedPlan.amount_unit || '') + '</strong> - ' + formatDuration(state.selectedPlan.duration, state.selectedPlan.duration_unit);
        paymentTotalNode.textContent = 'Total: ' + formatPrice(state.selectedPlan.price, state.selectedPlan.price_unit);
        payButton.textContent = 'Pagar ' + formatPrice(state.selectedPlan.price, state.selectedPlan.price_unit);
        cardErrorsNode.textContent = '';

        if (!mountStripeCard()) {
            showErrorMessage('No fue posible inicializar el pago en este momento.');
            return;
        }

        $('#registroPaymentModal').modal('show');
    }

    function showSuccessModal(esimData) {
        successContentNode.classList.remove('d-none');
        successPlanAmountNode.textContent = (esimData.data_amount || 'N/A') + ' GB';
        successPlanDurationNode.textContent = (esimData.duration_days || 'N/A') + ' días de duración';
        successQrNode.innerHTML = esimData.qr_svg || '';
        smdpInput.value = esimData.smdp || '';
        iccidInput.value = esimData.iccid || '';
        codeInput.value = esimData.code || '';
        $('#registroSuccessModal').modal('show');
    }

    async function processFreeActivation() {
        if (!state.selectedPlan) {
            return;
        }

        if (!state.isAuthenticated) {
            $('#registroAuthModal').modal('show');
            return;
        }

        setLoadingState(true, 'Activando tu eSIM...');
        clearInlineError();

        try {
            const response = await axios.post(state.activateFreeEndpoint, {
                product_id: state.selectedPlan.id,
                plan_name: state.selectedPlan.name,
                data_amount: state.selectedPlan.amount,
                duration: state.selectedPlan.duration,
                original_price: state.selectedPlan.original_price,
            });

            if (!response.data.success) {
                throw new Error(response.data.message || 'No fue posible activar la eSIM');
            }

            renderPlans();
            showSuccessModal(response.data.esim_data || {});
        } catch (error) {
            renderPlans();
            showErrorMessage(error.response?.data?.message || error.message || 'Error al activar el plan.');
        }
    }

    async function processPayment() {
        if (!state.selectedPlan || state.paymentProcessing) {
            return;
        }

        state.paymentProcessing = true;
        clearInlineError();
        setButtonBusy(payButton, true, 'Procesando...', 'Pagar');
        cardErrorsNode.textContent = '';

        try {
            const intentResponse = await axios.post(state.paymentIntentEndpoint, {
                product_id: state.selectedPlan.id,
                amount: state.selectedPlan.price,
                currency: String(state.selectedPlan.price_unit || 'usd').toLowerCase()
            });

            if (!intentResponse.data.success) {
                throw new Error(intentResponse.data.message || 'Error creando el intento de pago');
            }

            state.paymentIntentId = intentResponse.data.payment_intent_id;

            const paymentResult = await state.stripe.confirmCardPayment(intentResponse.data.client_secret, {
                payment_method: {
                    card: state.cardElement
                }
            });

            if (paymentResult.error) {
                throw new Error(paymentResult.error.message);
            }

            const activationResponse = await axios.post(state.processPaymentEndpoint, {
                product_id: state.selectedPlan.id,
                payment_intent_id: state.paymentIntentId,
                plan_name: state.selectedPlan.name,
                data_amount: state.selectedPlan.amount,
                duration: state.selectedPlan.duration,
                purchase_amount: state.selectedPlan.price,
                currency: state.selectedPlan.price_unit
            });

            if (!activationResponse.data.success) {
                throw new Error(activationResponse.data.message || 'No fue posible activar la eSIM');
            }

            $('#registroPaymentModal').modal('hide');
            showSuccessModal(activationResponse.data.esim_data || {});
        } catch (error) {
            const message = error.response?.data?.message || error.message || 'Error procesando el pago.';
            cardErrorsNode.textContent = message;
            showErrorMessage(message);
        } finally {
            state.paymentProcessing = false;
            setButtonBusy(payButton, false, 'Procesando...', 'Pagar ' + formatPrice(state.selectedPlan.price, state.selectedPlan.price_unit));
        }
    }

    function copyInputValue(inputId) {
        const input = document.getElementById(inputId);

        if (!input) {
            return;
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(input.value)
                .then(function() {
                    showSuccessMessage('Copiado al portapapeles');
                })
                .catch(function() {
                    input.select();
                    document.execCommand('copy');
                    showSuccessMessage('Copiado al portapapeles');
                });
            return;
        }

        input.select();
        input.setSelectionRange(0, 99999);

        try {
            document.execCommand('copy');
            showSuccessMessage('Copiado al portapapeles');
        } catch (error) {
            showErrorMessage('No fue posible copiar el valor.');
        }
    }

    gridNode.addEventListener('click', function(event) {
        const actionButton = event.target.closest('[data-plan-index]');

        if (!actionButton) {
            return;
        }

        event.preventDefault();

        const planIndex = Number(actionButton.dataset.planIndex);
        const selectedPlan = state.plans[planIndex];

        if (!selectedPlan) {
            return;
        }

        state.selectedPlan = selectedPlan;
        updateSelectedCard();

        if (selectedPlan.is_free) {
            processFreeActivation();
            return;
        }

        if (!state.isAuthenticated) {
            $('#registroAuthModal').modal('show');
            return;
        }

        showPaymentModal();
    });

    loginForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        setAuthError(authErrorNode, '');
        setButtonBusy(loginSubmitButton, true, 'Iniciando...', 'Iniciar sesión');

        try {
            const response = await axios.post(state.authLoginEndpoint, {
                email: document.getElementById('registro-login-email').value,
                password: document.getElementById('registro-login-password').value
            });

            if (response.data.success) {
                state.isAuthenticated = true;
                $('#registroAuthModal').modal('hide');
                if (state.selectedPlan && state.selectedPlan.is_free) {
                    processFreeActivation();
                } else {
                    showPaymentModal();
                }
            }
        } catch (error) {
            setAuthError(authErrorNode, error.response?.data?.message || 'Error al iniciar sesión');
        } finally {
            setButtonBusy(loginSubmitButton, false, 'Iniciando...', 'Iniciar sesión');
        }
    });

    registerForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        setAuthError(registerErrorNode, '');
        setButtonBusy(registerSubmitButton, true, 'Registrando...', 'Registrarse');

        try {
            const response = await axios.post(state.authRegisterEndpoint, {
                nombre: document.getElementById('registro-register-nombre').value,
                apellido: document.getElementById('registro-register-apellido').value,
                email: document.getElementById('registro-register-email').value,
                password: document.getElementById('registro-register-password').value,
                password_confirmation: document.getElementById('registro-register-password-confirmation').value
            });

            if (response.data.success) {
                state.isAuthenticated = true;
                $('#registroAuthModal').modal('hide');
                if (state.selectedPlan && state.selectedPlan.is_free) {
                    processFreeActivation();
                } else {
                    showPaymentModal();
                }
            }
        } catch (error) {
            const errors = error.response?.data?.errors;
            const message = errors ? Object.values(errors).flat().join('. ') : (error.response?.data?.message || 'Error al registrar usuario');
            setAuthError(registerErrorNode, message);
        } finally {
            setButtonBusy(registerSubmitButton, false, 'Registrando...', 'Registrarse');
        }
    });

    payButton.addEventListener('click', function(event) {
        event.preventDefault();
        processPayment();
    });

    checkAuth();
    loadPlans();
});
</script>
@endpush
@endsection