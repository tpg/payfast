<?php

declare(strict_types=1);

namespace TPG\PayFast;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;
use TPG\PayFast\Exceptions\PayFastException;

class Merchant
{
    protected $attributes = [
        'merchant_id' => null,
        'merchant_key' => null,
        'return_url' => null,
        'cancel_url' => null,
        'notify_url' => null,
    ];
    protected string $passphrase;

    public function __construct(string $merchantId, string $merchantKey, string $passphrase)
    {
        $this->attributes['merchant_id'] = $merchantId;
        $this->attributes['merchant_key'] = $merchantKey;
        $this->passphrase = $passphrase;
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

    public function merchantId(): string
    {
        return $this->attributes['merchant_id'];
    }

    public function merchantKey(): string
    {
        return $this->attributes['merchant_key'];
    }

    public function passphrase(): string
    {
        return $this->passphrase;
    }

    public function setReturnUrl(string $returnUrl): self
    {
        $this->attributes['return_url'] = $returnUrl;
        return $this;
    }

    public function setCancelUrl(string $cancelUrl): self
    {
        $this->attributes['cancel_url'] = $cancelUrl;
        return $this;
    }

    public function setNotifyUrl(string $notifyUrl): self
    {
        $this->attributes['notify_url'] = $notifyUrl;
        return $this;
    }

    public function attributes(): array
    {
        return (new Attributes())->prep($this->attributes);
    }
}
