<?php

declare(strict_types=1);

return [
    'base_url' => env('NECTARCRM_BASE_URL', 'https://app.nectarcrm.com.br/crm/api/1/'),
    'access_token' => env('NECTARCRM_ACCESS_TOKEN', ''),
    'error_email' => env('NECTARCRM_ERROR_EMAIL', 'joao.paulo@fmd.ag'),
];
