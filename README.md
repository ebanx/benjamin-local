# Benjamin
[![Build Status](https://img.shields.io/travis/ebanx/benjamin/master.svg?style=for-the-badge)](https://travis-ci.com/ebanx/benjamin)
[![codecov](https://img.shields.io/codecov/c/github/ebanx/benjamin/master.svg?style=for-the-badge)](https://codecov.io/gh/ebanx/benjamin)
[![Latest Stable Version](https://img.shields.io/packagist/v/ebanx/benjamin.svg?style=for-the-badge)](https://packagist.org/packages/ebanx/benjamin)
[![Total Downloads](https://img.shields.io/packagist/dt/ebanx/benjamin.svg?style=for-the-badge)](https://packagist.org/packages/ebanx/benjamin)
[![License](https://img.shields.io/packagist/l/ebanx/benjamin.svg?style=for-the-badge)](https://packagist.org/packages/ebanx/benjamin)


This is the repository for business rules as of implemented by merchant sites for use in e-commerce platform plugins.
The objective is to be a central repository for services and to communicate with the EBANX Local API (also known as "Pay Local").

## Getting Started

It is very simple to use Benjamin. You will only need an instance of `Ebanx\Benjamin\Models\Configs\Config` and an instance of `Ebanx\Benjamin\Models\Payment`:

```php
<?php
$config = new Config([
    'integrationKey' => 'YOUR_INTEGRATION_KEY',
    'sandboxIntegrationKey' => 'YOUR_SANDBOX_INTEGRATION_KEY'
]);

$payment = new Payment([
    //Payment properties(see wiki)
]);

$result = EBANX($config)->create($payment);
```

If you want more information you can check the [Wiki](https://github.com/ebanx/benjamin/wiki/Using-Benjamin).

## Contributing

Check the [Wiki](https://github.com/ebanx/benjamin/wiki/Contributing).
