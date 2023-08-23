<?php

declare(strict_types=1);

namespace TPG\PHPayfast;

readonly class Merchant
{
    public function __construct(
        public string $id,
        public string $secret,
        public string $passphrase,
        public bool $testing = false,
    ) {
    }

    public function toArray(): array
    {
        return (new Attributes())->prep([
            'merchant_id' => $this->id,
            'merchant_key' => $this->secret,
        ]);
    }
}
