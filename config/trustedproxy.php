<?php

return [
    'proxies' => env('TRUSTED_PROXIES') ?: null,
    'headers' => env('TRUSTED_PROXY_HEADERS'),
];