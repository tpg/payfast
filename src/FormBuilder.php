<?php

declare(strict_types=1);

namespace TPG\PayFast;

class FormBuilder
{
    protected Transaction $transaction;
    protected string $signature;
    protected string $host;
    protected ?int $submitTimeout;

    public function __construct(
        Transaction $transaction,
        string $signature,
        string $host,
        ?int $submitTimeout = null
    ) {
        $this->transaction = $transaction;
        $this->signature = $signature;
        $this->host = $host;
        $this->submitTimeout = $submitTimeout;
    }

    public function build(): string
    {
        $form = implode("\n", [
            '<form method="post" action="'.$this->host.'" id="payfast_form">',
            ...$this->getFormAttributes(),
            '</form>',
        ]);

        return $this->submit($form);
    }

    protected function submit(string $form): string
    {
        if ($this->submitTimeout) {
            $form .= implode("\n", [
                '<script>',
                'setTimeout(() => {',
                'document.querySelector(\'#payfast_form\').submit()',
                '}, '.$this->timeout().')',
                '</script>',
            ]);
        }

        return $form;
    }

    protected function timeout(): int
    {
        return $this->submitTimeout * 1000;
    }

    protected function getFormAttributes(): array
    {
        $attributes = [];

        foreach ($this->attributes() as $key => $value) {
            $attributes[] = '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
        }

        return $attributes;
    }

    protected function attributes(): array
    {
        return array_merge(
            $this->transaction->attributes(),
            [
                'signature' => $this->signature,
            ]
        );
    }
}
