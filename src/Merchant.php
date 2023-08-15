<?php

declare(strict_types=1);

namespace TPG\PayFast;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;
use TPG\PayFast\Exceptions\PayFastException;

readonly class Merchant
{
    public function __construct(
        public string $merchantId,
        public string $merchantKey,
        public string $passphrase,
    )
    {
    }

    public function ping(bool $testing = false, Client $client = null): bool
    {

        try {
            $response = (new Request($this))->testing($testing)->make('get', 'ping');

            if (Arr::get($response, 'status') === 'failed') {
                // todo: There's a bug in the response from PayFast at the moment.
                // todo: The `data.message` appears to be boolean, while `data.response` appears to be a string.
                // todo: Also, the response from a live ping always returns a 500 error.
                throw new PayFastException(Arr::get($response, 'data.message'), $response['code']);
            }

            return true;

        } catch (ClientException $exception) {
            throw new PayFastException($exception->getMessage(), $exception->getCode());
        }
    }
    public function attributes(): array
    {
        return (new Attributes())->prep([
            $this->merchantId,
            $this->merchantKey,
            $this->passphrase,
        ]);
    }
}
