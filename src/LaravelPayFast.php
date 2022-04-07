<?php

declare(strict_types=1);

namespace TPG\PayFast;

use TPG\PayFast\Contracts\LaravelPayFastInterface;
use TPG\PayFast\Contracts\MerchantInterface;

class LaravelPayFast implements LaravelPayFastInterface
{
    protected MerchantInterface $merchant;
    protected ?Customer $customer = null;
    protected string $itemName;
    protected ?string $itemDescription = null;
    protected ?string $paymentId = null;

    public function __construct(protected readonly array $config = [])
    {
        $this->merchant = new Merchant(
            $config['id'],
            $config['key'],
            $config['passphrase']
        );
    }

    public function withMerchant(MerchantInterface $merchant): self
    {
        $this->merchant = $merchant;
        return $this;
    }

    public function forCustomer(string $name, string $email = null, string $number = null): self
    {
        $this->customer = (new Customer())
            ->setName($name)
            ->setEmail($email)
            ->setCellNumber($number);

        return $this;
    }

    public function forItem(string $name, string $description = null, string $paymentId = null): self
    {
        $this->itemName = $name;
        $this->itemDescription = $description;
        $this->paymentId = $paymentId;

        return $this;
    }

    public function transact(float $price): Transaction
    {
        return new Transaction($this->merchant, (int)$price * 100, $this->itemName);
    } 

    public function getMerchant(): Merchant
    {
        return $this->merchant;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}

/**
 * $form = PayFast::forCustomer('Bob', 'bob@example.com', '0827352002')
 *  ->forItem('Shoes', 'Nice shoes to buy', 'SE454112')
 *  ->pay(199.95);
 *
 * $payfast = PayFast::forCustomer('Bob', '
 */
