# Benjamin
[![Build Status](https://img.shields.io/travis/ebanx/benjamin-local/master.svg?style=for-the-badge)](https://travis-ci.com/ebanx/benjamin-local)
[![Latest Stable Version](https://img.shields.io/packagist/v/ebanx/benjamin-local.svg?style=for-the-badge)](https://packagist.org/packages/ebanx/benjamin-local)
[![Total Downloads](https://img.shields.io/packagist/dt/ebanx/benjamin-local.svg?style=for-the-badge)](https://packagist.org/packages/ebanx/benjamin-local)
[![License](https://img.shields.io/packagist/l/ebanx/benjamin-local.svg?style=for-the-badge)](https://packagist.org/packages/ebanx/benjamin-local)


This is the repository for business rules as of implemented by merchant sites for use in e-commerce platform plugins.
The objective is to be a central repository for services and to communicate with the EBANX Local API (also known as "Pay Local").

## Getting Started

It is very simple to use Benjamin. You will only need an instance of `Ebanx\Benjamin\Models\Configs\Config` and an instance of `Ebanx\Benjamin\Models\Payment`:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Payment;

$config = new Config([
    'integrationKey' => 'YOUR_INTEGRATION_KEY',
    'sandboxIntegrationKey' => 'YOUR_SANDBOX_INTEGRATION_KEY'
]);

$payment = new Payment([
    //Payment properties(see wiki)
]);

$result = EBANX($config)->create($payment);
```

If you want more information you can check the [Wiki](https://github.com/ebanx/benjamin-local/wiki/Getting-Started).