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
use Wlfpanda1012\CommonSts\Contract\StsAdapter;
use Wlfpanda1012\CommonSts\Response\StsTokenResponse;
use Wlfpanda1012\CtyunOosSdkPlus\Core\OosException;
use Wlfpanda1012\CtyunOosSdkPlus\Model\SessionToken;
use Wlfpanda1012\CtyunOosSdkPlus\OosClient;

class Adapter implements StsAdapter
{
    private OosClient $client;

    /**
     * @throws OosException
     */
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
        return new StsTokenResponse(
            $sessionToken->getAccessKeyId(),
            $sessionToken->getSecretAccessKey(),
            new DateTime($sessionToken->getExpiration()),
            $sessionToken->getSessionToken()
        );
    }
}
