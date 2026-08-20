<?php

return [
    'mobile_token_ttl_days' => (int) env('API_MOBILE_TOKEN_TTL_DAYS', 30),
    'integration_token_ttl_days' => (int) env('API_DEFAULT_INTEGRATION_TOKEN_TTL_DAYS', 90),
];
