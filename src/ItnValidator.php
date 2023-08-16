<?php

declare(strict_types=1);

namespace TPG\PayFast;

use GuzzleHttp\Client;
use TPG\PayFast\Exceptions\ValidationException;

class ItnValidator
{
    public readonly PayFastResponse $response;

    protected ?string $error = null;

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

            return false;
        }

        return true;
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
            throw new ValidationException('Generated signature does not match response signature.');
        }

        return true;
    }

    protected function validateHost(string $referer): bool
    {
        $ips = $this->getValidIps();

        if (! in_array($referer, $ips, true)) {
            throw new ValidationException('Response is not from a valid PayFast IP address.');
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
        if (str_replace('.', '', (string) $this->response->amountGross) !== (string) $total) {
            throw new ValidationException('The transaction amount does not match the gross amount.');
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
                throw new ValidationException('Unable to confirm order details with PayFast. PayFast Status code '.$response->getStatusCode());
            }

        } catch (\Exception $e) {

            throw new ValidationException('Unable to confirm order details with PayFast. Exception ', $e->getCode(), $e);
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
