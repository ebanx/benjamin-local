[![Stories in Ready](https://badge.waffle.io/ebanx/benjamin.svg?label=ready&title=Ready)](http://waffle.io/ebanx/benjamin)
[![StyleCI](https://styleci.io/repos/89406660/shield?branch=feature/readme-tracking)](https://styleci.io/repos/89406660)
[![Build Status](https://travis-ci.org/ebanx/benjamin.svg?branch=master)](https://travis-ci.org/ebanx/benjamin)

# Benjamin

This is the repository for business rules as of implemented by merchant sites for use in e-commerce platform plugins.
The objective is to be a central repository for services and to communicate with the EBANX API (also known as "Pay").

## Contributing

* TDD
* PSR-2

## Checklist for implementations needed

- [ ] Payment
	- [ ] Brasil
		- [ ] Boleto
		- [ ] Credit Card
		- [ ] TEF
		- [ ] EBANX Account
	- [ ] Mexico
		- [ ] Credit Card
		- [ ] Debit Card
		- [ ] OXXO
	- [ ] Chile
		- [ ] Sencillito
		- [ ] Servipag
	- [ ] Colombia
		- [ ] PSE
		- [ ] Baloto
		- [ ] Credit Card
	- [ ] Peru
		- [ ] SafetyPay
		- [ ] PagoEfectivo
- [ ] Refund
- [ ] Payment Capture
- [ ] Payment by link
- [ ] Validator
- [ ] Notifications
- [ ] Interest Rates
- [ ] Taxes
