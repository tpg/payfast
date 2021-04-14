<?php

declare(strict_types=1);

namespace TPG\PayFast;

class Signature
{
    protected array $attributes;
    protected ?string $passphrase;

    public function __construct(array $attributes, ?string $passphrase = null)
    {
        $this->attributes = $attributes;
        $this->passphrase = $passphrase;
    }

    public function generate(bool $sort = false): string
    {
        return md5($this->attributes($sort));
    }

    protected function attributes(bool $sort = false): string
    {
        $attributes = $this->attributes;

        $attributes['passphrase'] = $this->passphrase;

        array_walk($attributes, static function (&$value, $key) {
            $value = $key.'='.urlencode(trim((string)$value));
        });

        if ($sort) {
            ksort($attributes);
        }

        return implode(
            '&',
            array_values($attributes)
        );
    }
}
