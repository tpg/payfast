<?php

declare(strict_types=1);

namespace TPG\PHPayfast\Validation;

use Respect\Validation\Rules\AllOf;
use Respect\Validation\Rules\Email;
use Respect\Validation\Rules\Key;
use Respect\Validation\Rules\NotEmpty;
use Respect\Validation\Rules\Nullable;
use Respect\Validation\Rules\NumericVal;
use Respect\Validation\Rules\StringType;
use Respect\Validation\Rules\Url;
use Respect\Validation\Validator as BaseValidator;
use TPG\PHPayfast\Exceptions\ValidationException;

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
        try {
                $this->getRules()->check($data);
        } catch (\Respect\Validation\Exceptions\ValidationException $exception) {
            throw new ValidationException($exception->getMessage());
        }
    }

    protected function getRules(): AllOf
    {
        $rules = collect($this->rules())->map(function ($rules, $key) {

            $prepared = collect(array_map(fn ($rule) => match ($rule) {
                'required' => new NotEmpty(),
                'string' => new StringType(),
                'email' => new Email(),
                'numeric' => new NumericVal(),
                'url' => new Url(),
                default => null,
            }, $rules))->whereNotNull()->toArray();


            $validation = new Key($key, new AllOf(...$prepared));

            if (in_array('nullable', $rules, true)) {
                $validation = new Key($key, new Nullable(new AllOf(...$prepared)));
            }

            return $validation;

        })->toArray();

        return new AllOf(...$rules);
    }
}
