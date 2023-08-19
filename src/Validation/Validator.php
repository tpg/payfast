<?php

declare(strict_types=1);

namespace TPG\PayFast\Validation;

use Respect\Validation\Rules\AllOf;
use Respect\Validation\Validator as BaseValidator;
use TPG\PayFast\Exceptions\ValidationException;

abstract class Validator
{
    protected BaseValidator $respect;

    public function __construct()
    {
        $this->respect = new BaseValidator();
    }

    abstract public function rules(): array;

    /**
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        $validator = new BaseValidator(...$this->getRules());

        try {
            $validator->check($data);
        } catch (\Respect\Validation\Exceptions\ValidationException $exception) {
            throw new ValidationException($exception->getMessage());
        }
    }

    protected function getRules(): array
    {
        return collect($this->rules())->map(function ($rules, $key) {

            $prepared = collect(array_map(fn ($rule) => match ($rule) {
                'required' => $this->respect::notEmpty(),
                'string' => $this->respect::stringType(),
                'email' => $this->respect::email(),
                'numeric' => $this->respect::number(),
                'url' => $this->respect::url(),
                default => null,
            }, $rules))->whereNotNull()->toArray();

            $validation = $this->respect::key($key, new AllOf(...$prepared));

            if (in_array('nullable', $rules, true)) {
                $validation = $this->respect::key($key, $this->respect::nullable(...$prepared));
            }

            return $validation;

        })->toArray();
    }
}
