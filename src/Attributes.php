<?php

declare(strict_types=1);

namespace TPG\PHPayfast;

class Attributes
{
    public function prep(array $data): array
    {
        return array_filter($data, static fn ($value) => ! is_null($value));
    }
}
