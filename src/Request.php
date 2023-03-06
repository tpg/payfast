<?php

declare(strict_types=1);

namespace TPG\PayFast;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use http\Encoding\Stream;
use TPG\PayFast\Exceptions\PayFastException;

class Request
{
    protected const ENDPOINT = 'https://api.payfast.co.za';

    protected Client $client;
    protected Merchant $merchant;
    protected bool $testing = false;

    public function __construct(Merchant $merchant, Client $client = null)
    {
        $this->merchant = $merchant;
        $this->client = $client ?? new Client();
    }

    public function testing(bool $testing = true): self
    {
        $this->testing = $testing;

        return $this;
    }

    public function make(string $method, string $uri, array $formParams = []): array
    {
        try {
            $response = $this->client->request($method, $this->endpoint($uri), [
                'headers' => $this->headers($formParams),
                'json' => $formParams,
                'query' => [
                    'testing' => $this->testing ? 'true' : 'false',
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new PayFastException('Bad response from PayFast API');
            }

            return $this->extractJson($response->getBody()->getContents());

        } catch (ClientException $exception) {

            throw new PayFastException($this->getErrorMessage($exception->getCode()), $exception->getCode());

        } catch (\JsonException $exception) {

            throw new PayFastException('Invalid JSON response');

        }
    }

    protected function extractJson(string $contents): array
    {
        $json = substr(
            $contents,
            $startPos = strpos($contents, '{'),
            strrpos($contents, '}') - ($startPos - 1)
        );

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    protected function headers(array $body = []): array
    {
        $headers = [
            'merchant-id' => $this->merchant->merchantId(),
            'version' => 'v1',
            'timestamp' => (new \DateTime())->format(DATE_ATOM),
        ];

        $headers['signature'] = (new Signature(array_merge($headers, $body), $this->merchant->passphrase()))->generate(true);

        return $headers;
    }

    protected function endpoint(string $uri = null): string
    {
        if ($uri[0] !== '/') {
            $uri = '/'.$uri;
        }

        return self::ENDPOINT.$uri;
    }

    protected function getErrorMessage(int $code): string
    {
        switch ($code) {
            case 400:
                return 'Invalid request';
            case 401:
                return 'Authorization failed';
            case 404:
                return 'Service not found';
            case 429:
                return 'Rate limited';
            case 500:
                return 'PayFast Application/Communication error';
        }
    }
}
