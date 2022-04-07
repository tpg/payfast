<?php

declare(strict_types=1);

namespace TPG\PayFast\Contracts;

use TPG\PayFast\Customer;
use TPG\PayFast\Merchant;
use TPG\PayFast\Transaction;

interface LaravelPayFastInterface
{
    public function withMerchant(MerchantInterface $merchant): self;
    public function forCustomer(string $name, string $email = null, string $number = null): self;
    public function forItem(string $name, string $description = null, string $paymentId = null): self;
    public function transact(float $price): Transaction;
    public function getMerchant(): Merchant;
    public function getCustomer(): Customer;
}
