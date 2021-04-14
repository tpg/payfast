<?php

declare(strict_types=1);

namespace TPG\PayFast;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;
use TPG\PayFast\Exceptions\PayfastException;

class Subscription
{
    protected const ENDPOINT = 'https://api.payfast.co.za/subscriptions';

    protected const STATUS_ACTIVE = 1;
    protected const STATUS_CANCELLED = 2;
    protected const STATUS_PAUSED = 3;

    protected Merchant $merchant;
    protected string $token;
    protected bool $testing = false;
    protected array $data = [];
    protected Client $client;
    protected ?bool $paused = null;
    protected ?bool $cancelled = null;

    public function __construct(Merchant $merchant, string $token, Client $client = null)
    {
        $this->merchant = $merchant;
        $this->token = $token;
        $this->client = $client ?? new Client();
    }

    public function testing(bool $testing = true): self
    {
        $this->testing = $testing;
        return $this;
    }

    public function fetch(): self
    {
        $data = $this->request('get', 'fetch');

        $this->setData(Arr::get($data, 'data.response', []));

        return $this;
    }

    public function pause(int $cycles = 1): array
    {
        $response = $this->request('put', 'pause', [
//            'cycles' => $cycles,
        ]);

        if (Arr::get($response, 'status') === 'success') {
            $this->paused = true;
        }

        return $response;
    }

    public function unpause(): array
    {
        if ($this->paused === false) {
            throw new PayfastException('Subscription is not paused', 6501);
        }

        $response = $this->request('put', 'unpause');
        if (Arr::get($response, 'status') === 'success') {
            $this->paused = false;
        }

        return $response;
    }

    public function cancel(): array
    {
        $response = $this->request('put', 'cancel');

        if (Arr::get($response, 'status') === 'success') {
            $this->cancelled = true;
        }

        return $response;
    }

    public function update(array $data): array
    {
        $response = $this->request('patch', 'update', $data);

        if (Arr::get($response, 'status') === 'success') {
            $this->setData(Arr::get($response, 'data.response'));
        }

        return $response;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    protected function setData(array $data): void
    {
        $this->data = $data;
        if (Arr::get($data, 'status') === self::STATUS_CANCELLED) {
            $this->cancelled = true;
        }
        if (Arr::get($data, 'status') === self::STATUS_PAUSED) {
            $this->paused = true;
        }
    }

    protected function request(string $method, string $uri = null, array $body = []): array
    {
        $headers = [
            'merchant-id' => $this->merchant->merchantId(),
            'version' => 'v1',
            'timestamp' => (new \DateTime())->format(DATE_ATOM),
        ];

        $headers['signature'] = (new Signature(array_merge($headers, $body), $this->merchant->passphrase()))->generate(true);

        try {
            $response = $this->client->request($method, self::ENDPOINT . '/' . $this->token . '/' . $uri, [
                'headers' => $headers,
                'form_params' => $body,
                'query' => [
                    'testing' => $this->testing ? 'true' : 'false',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            if ($response->getStatusCode() !== 200) {
                throw new PayfastException($data['status'], $data['code']);
            }

            return $data;
        } catch (ClientException $exception) {
            throw new PayfastException('Unable to communicate with PayFast');
        }
    }

    public function amount(): ?int
    {
        return Arr::get($this->data, 'amount', null);
    }

    public function cycles(): ?int
    {
        return Arr::get($this->data, 'cycles', null);
    }

    public function cyclesComplete(): ?int
    {
        return Arr::get($this->data, 'cycles_complete', null);
    }

    public function frequency(): ?int
    {
        return Arr::get($this->data, 'frequency', null);
    }

    public function runDate(): ?\DateTime
    {
        return Arr::has($this->data, 'run_date')
            ? new \DateTime($this->data['run_date'])
            : null;
    }

    public function status(): ?int
    {
        return Arr::get($this->data, 'status', null);
    }

    public function paused(): bool
    {
        return $this->status() === self::STATUS_PAUSED;
    }

    public function cancelled(): bool
    {
        return $this->status() === self::STATUS_CANCELLED;
    }

    public function statusReason(): ?string
    {
        return Arr::get($this->data, 'status_reason', null);
    }

    public function statusText(): ?string
    {
        return Arr::get($this->data, 'status_text', null);
    }
}
