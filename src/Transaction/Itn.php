<?php

declare(strict_types=1);

namespace TPG\PHPayfast\Transaction;

use GuzzleHttp\Client;
use TPG\PHPayfast\Exceptions\ValidationException;
use TPG\PHPayfast\Response\PayFastResponse;

class Itn
{
    public readonly PayFastResponse $response;

    protected ?string $error = null;

    public readonly ?bool $success;

    public function __construct(public readonly array $data, protected bool $testing = false)
    {
        $this->response = PayFastResponse::createFromResponse($data);
        $this->flush();
    }

    protected function flush(): void
    {
        header('HTTP/1.0 200 OK');
        flush();
    }

    public function validate(int $amount, string $passphrase, string $referer): bool
    {
        $paramString = $this->paramString($passphrase);

        try {
            $this->validateSignature($paramString);
            $this->validateHost($referer);
            $this->validateAmount($amount);
            $this->confirm($paramString, $this->testing);
        } catch (ValidationException $e) {
            $this->error = $e->getMessage();

            return $this->success = false;
        }

        return $this->success = true;
    }

    public function paramString(string $passphrase): string
    {
        $data = $this->data;

        $params = [];

        foreach ($data as $key => $value) {
            if ($key !== 'signature') {
                $params[] = $key.'='.($value ? urlencode(stripslashes($value)) : null);
            }
        }

        return implode('&', $params).'&passphrase='.$passphrase;

    }

    protected function validateSignature(string $params): bool
    {
        $signature = md5($params);

        if ($signature !== $this->response->signature) {
            $this->success = false;
            $this->error = 'Invalid signature';

            return false;
        }

        return true;
    }

    protected function validateHost(string $referer): bool
    {
        $ips = $this->getValidIps();

        if (! in_array($referer, $ips, true)) {
            $this->success = false;
            $this->error = 'Invalid host';

            return false;
        }

        return true;
    }

    protected function getValidIps(): array
    {
        $validHosts = [
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
        ];

        $ips = [];

        foreach ($validHosts as $host) {
            $hostIp = gethostbynamel($host);

            if ($hostIp) {
                $ips = [...$ips, ...$hostIp, '127.0.0.1'];
            }
        }

        return array_unique($ips);
    }

    protected function validateAmount(int $total): bool
    {
        if (str_replace('.', '', (string) $this->response->gross) !== (string) $total) {
            $this->success = false;
            $this->error = 'Invalid amount.';

            return false;
        }

        return true;
    }

    protected function confirm(string $params, bool $testing = false): bool
    {
        try {
            $client = new Client();

            $response = $client->post($this->payfastHost($testing).'/eng/query/validate', [
                'query' => $params,
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->success = false;
                $this->error = 'Unable to confirm. Status '.$response->getStatusCode();

                return false;
            }

            $this->success = true;

        } catch (\Throwable $e) {
            $this->success = false;
            $this->error = $e->getMessage();

            return false;
        }

        return true;
    }

    protected function payfastHost(bool $testing = false): string
    {
        return implode('', [
            'https://',
            $testing ? 'sandbox.' : 'www.',
            'payfast.co.za',
        ]);
    }

    public function error(): ?string
    {
        return $this->error;
    }
}
