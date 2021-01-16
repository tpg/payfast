<?php

declare(strict_types=1);

namespace TPG\PayFast;

class Attributes
{
    public function prep(array $data): array
    {
        $data = array_filter($data, static fn ($value) => ! empty($value));

        return $data;
    }
}
