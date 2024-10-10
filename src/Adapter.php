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
use Wlfpanda1012\CommonSts\Contract\StoragePolicyGenerator;
use Wlfpanda1012\CommonSts\Contract\StsAdapter;
use Wlfpanda1012\CommonSts\Response\StsTokenResponse;
use Wlfpanda1012\CtyunOosSdkPlus\Core\OosException;
use Wlfpanda1012\CtyunOosSdkPlus\Model\SessionToken;
use Wlfpanda1012\CtyunOosSdkPlus\OosClient;
use Wlfpanda1012\CtyunSts\Exception\InvalidArgumentException;

class Adapter implements StsAdapter, StoragePolicyGenerator
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

    public function getToken(array $policy, array $config = []): StsTokenResponse
    {
        $data = [
            'PolicyDocument' => json_encode($policy),
            'DurationSeconds' => $config['durationSeconds'] ?? 3600,
        ];
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

    public function storagePolicy(string $effect, array $actions, array|string $path, array $config = []): array
    {
        $resource = [];
        if (is_array($path)) {
            foreach ($path as $item) {
                $resource[] = $this->assembleResource($item, $config);
            }
        } else {
            $resource[] = $this->assembleResource($path, $config);
        }
        return $this->generatePolicy([$this->generateStatement($effect, $actions, $resource)]);
    }

    public function generateStatement(string $effect, array $action, array $resource, ?array $condition = null): array
    {
        $statement = [
            'Effect' => $effect,
            'Action' => $action,
            'Resource' => $resource,
        ];
        $condition && $statement = array_merge($statement, ['Condition' => $condition]);
        return $statement;
    }

    public function generatePolicy(array $statement): array
    {
        return [
            'Version' => '2012-10-17',
            'Statement' => $statement,
        ];
    }

    protected function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $this->rejectFunkyWhiteSpace($path);
        return $this->normalizeRelativePath($path);
    }

    private function assembleResource(string $path, array $config): string
    {
        /**
         * 默认设置最大范围,通过 RAM 用户 基础范围 + 文件url 控制.
         */
        return sprintf('arn:ctyun:oos::%s:%s/%s', $config['account_uid'] ?? '*', $config['bucket'] ?? '*', $this->normalizePath($path));
    }

    private function rejectFunkyWhiteSpace(string $path): void
    {
        if (preg_match('#\p{C}+#u', $path)) {
            throw new InvalidArgumentException('Invalid characters in path: ' . $path);
        }
    }

    private function normalizeRelativePath(string $path): string
    {
        $parts = [];

        foreach (explode('/', $path) as $part) {
            switch ($part) {
                case '':
                case '.':
                    break;
                case '..':
                    if (empty($parts)) {
                        throw new InvalidArgumentException('Invalid path: ' . $path);
                    }
                    array_pop($parts);
                    break;
                default:
                    $parts[] = $part;
                    break;
            }
        }

        return implode('/', $parts);
    }
}
