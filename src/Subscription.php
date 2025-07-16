<?php

declare(strict_types=1);

namespace TPG\PayFast;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;
use TPG\PayFast\Exceptions\PayFastException;

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

    public function __construct(Merchant $merchant, string $token, ?Client $client = null)
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
            'cycles' => $cycles,
        ]);

        if (Arr::get($response, 'status') === 'success') {
            $this->paused = true;
        }

        return $response;
    }

    public function unpause(): array
    {
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
            $this->setData(array_merge($this->data, Arr::get($response, 'data.response')));
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

    protected function request(string $method, ?string $uri = null, array $body = []): array
    {
        try {

            return (new Request($this->merchant, $this->client))->testing($this->testing)
                ->make($method, 'subscriptions/'.$this->token.'/'.$uri, $body);

        } catch (ClientException $exception) {
            throw new PayFastException(
                'Unable to communicate with PayFast',
                $exception->getCode(),
                $exception
            );
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
        return $this->paused;
    }

    public function cancelled(): bool
    {
        return $this->cancelled;
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
