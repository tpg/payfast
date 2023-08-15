<?php

declare(strict_types=1);

namespace TPG\PayFast;

readonly class FormBuilder
{
    public function __construct(
        protected Transaction $transaction,
        protected string $signature,
        protected string $host,
        protected ?int $submitTimeout = null
    ) {
    }

    public function build(): string
    {
        $form = implode("\n", [
            '<form method="post" action="'.$this->host.'" id="payfast_form">',
            ...$this->formAttributes(),
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

    protected function formAttributes(): array
    {
        $attributes = [];

        foreach ($this->attributes() as $key => $value) {
            $attributes[] = '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
        }

        return $attributes;
    }

    protected function attributes(): array
    {
        return (new Attributes())->prep(
            array_merge(
                $this->transaction->attributes(),
                [
                    'signature' => $this->signature,
                ]
            )
        );
    }
}
