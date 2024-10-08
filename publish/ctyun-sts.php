<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use function Hyperf\Support\env;

return [
    /**
     * STS相关.
     */
    'oss' => [
        'access_key_id' => env('OOS_ACCESS_KEY_ID'),
        'access_key_secret' => env('OOS_ACCESS_KEY_SECRET'),
        'endpoint' => env('OOS_ENDPOINT'),
    ],
];
