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

namespace Wlfpanda1012\CtyunSts;

use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Wlfpanda1012\CommonSts\Contract\StsAdapter;
use Wlfpanda1012\CommonSts\Response\StsTokenResponse;
use Wlfpanda1012\CtyunOosSdkPlus\Core\OosException;
use Wlfpanda1012\CtyunOosSdkPlus\Model\SessionToken;
use Wlfpanda1012\CtyunOosSdkPlus\OosClient;

use function Hyperf\Support\make;

class Adapter implements StsAdapter
{
    private OosClient $client;

    /**
     * @throws OosException
     */
    #[ArrayShape([
        'accessKeyId' => 'string',
        'accessKeySecret' => 'string',
        'endpoint' => 'string',
    ])]
    public function __construct(array $config = [])
    {
        $accessKeyId = $config['accessKeyId'];
        $accessKeySecret = $config['accessKeySecret'];
        $endpoint = $config['endpoint'] ?? 'oos-cn-iam.ctyunapi.cn';
        $this->client = new OosClient($accessKeyId, $accessKeySecret, $endpoint);
    }

    public function getToken(mixed $data, array $config = []): StsTokenResponse
    {
        $sessionToken = $this->client->GetSessionToken($data);
        /**
         * @var SessionToken $sessionToken
         */
        return make(StsTokenResponse::class, [
            'accessKeyId' => $sessionToken->getAccessKeyId(),
            'accessKeySecret' => $sessionToken->getSecretAccessKey(),
            'expireTime' => strtotime($sessionToken->getExpiration()),
            'sessionToken' => $sessionToken->getSessionToken(),
        ]);
    }
}
