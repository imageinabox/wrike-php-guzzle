<?php

/*
 * This file is part of the zibios/wrike-php-guzzle package.
 *
 * (c) Zbigniew Ślązak
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zibios\WrikePhpGuzzle\Tests\Client;

use GuzzleHttp\ClientInterface;
use Zibios\WrikePhpGuzzle\Client\GuzzleClient;
use Zibios\WrikePhpGuzzle\Tests\TestCase;
use Zibios\WrikePhpLibrary\Enum\Api\RequestMethodEnum;

/**
 * Guzzle Client Test.
 */
class GuzzleClientTest extends TestCase
{
    /**
     * Test exception inheritance.
     */
    public function test_ExtendProperClasses()
    {
        $client = new GuzzleClient();
        self::assertInstanceOf(GuzzleClient::class, $client);
        self::assertInstanceOf(ClientInterface::class, $client);
    }

    /**
     * @return array
     */
    public function paramsProvider()
    {
        $accessToken = 'testBearerToken';
        $testUri = '/test/uri';
        $baseOptions['base_uri'] = 'https://www.wrike.com/api/v3/';
        $baseOptions['headers']['Authorization'] = sprintf('Bearer %s', $accessToken);

        return [
            // [accessToken, requestMethod, path, params, options]
            [$accessToken, RequestMethodEnum::GET, $testUri, [], $baseOptions],
            [$accessToken, RequestMethodEnum::GET, $testUri, ['test' => 'query'], ['query' => ['test' => 'query']] + $baseOptions],
            [$accessToken, RequestMethodEnum::DELETE, $testUri, [], $baseOptions],
            [$accessToken, RequestMethodEnum::DELETE, $testUri, ['test' => 'query'], ['query' => ['test' => 'query']] + $baseOptions],
            [$accessToken, RequestMethodEnum::PUT, $testUri, [], $baseOptions],
            [$accessToken, RequestMethodEnum::PUT, $testUri, ['test' => 'query'], ['form_params' => ['test' => 'query']] + $baseOptions],
            [$accessToken, RequestMethodEnum::POST, $testUri, [], $baseOptions],
            [$accessToken, RequestMethodEnum::POST, $testUri, ['test' => 'query'], ['form_params' => ['test' => 'query']] + $baseOptions],
        ];
    }

    /**
     * @param string $accessToken
     * @param string $requestMethod
     * @param string $path
     * @param array  $params
     * @param array  $options
     *
     * @dataProvider paramsProvider
     */
    public function test_executeRequestForParams($accessToken, $requestMethod, $path, $params, $options)
    {
        /** @var GuzzleClient $clientMock */
        $clientMock = self::getMock(GuzzleClient::class, ['request']);
        $clientMock->expects(self::any())
            ->method('request')
            ->with(self::equalTo($requestMethod), self::equalTo($path), self::equalTo($options));

        $clientMock->executeRequestForParams($requestMethod, $path, $params, $accessToken);
    }

    /**
     * @return array
     */
    public function wrongParamsProvider()
    {
        $accessToken = 'testBearerToken';
        $testUri = '/test/uri';
        $baseOptions['base_uri'] = 'https://www.wrike.com/api/v3/';
        $baseOptions['headers']['Authorization'] = sprintf('Bearer %s', $accessToken);

        return [
            // [accessToken, requestMethod, path, params, options]
            ['', RequestMethodEnum::GET, $testUri, [], $baseOptions],
            [null, RequestMethodEnum::GET, $testUri, [], $baseOptions],
            [$accessToken, 'WRONG_METHOD', $testUri, [], $baseOptions],
        ];
    }

    /**
     * @param string $accessToken
     * @param string $requestMethod
     * @param string $path
     * @param array  $params
     * @param array  $options
     *
     * @dataProvider wrongParamsProvider
     */
    public function test_executeRequestForWrongParams($accessToken, $requestMethod, $path, $params, $options)
    {
        self::setExpectedException(\InvalidArgumentException::class);
        /** @var GuzzleClient $clientMock */
        $clientMock = self::getMock(GuzzleClient::class, ['request']);
        $clientMock->expects(self::any())
            ->method('request')
            ->with(self::equalTo($requestMethod), self::equalTo($path), self::equalTo($options));

        $clientMock->executeRequestForParams($requestMethod, $path, $params, $accessToken);
    }
}
