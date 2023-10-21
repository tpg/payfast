<?php

namespace TPG\PHPayfast\Enums;

enum PayfastEndpoint: string
{
    case Process = '/eng/process';
    case Onsite = 'onsite/process';

    case Engine = 'onsite/engine.js';

    public function url(bool $sandbox = false): string
    {
        $domain = $sandbox ? 'sandbox.payfast.co.za/' : 'www.payfast.co.za/';

        return 'https://'.$domain.$this->value;
    }
}
