<?php
namespace Tests\Unit\Services\Adapters;

use Ebanx\Benjamin\Services\Adapters\CardPaymentAdapter;
use Tests\Helpers\Builders\BuilderFactory;
use JsonSchema;
use Ebanx\Benjamin\Models\Configs\Config;
use Tests\TestCase;

class CardPaymentAdapterTest extends TestCase
{
    public function testJsonSchema()
    {
        $config = $this->generateConfig();
        $factory = new BuilderFactory('pt_BR');
        $payment = $factory->payment()->creditCard()->businessPerson()->build();

        $adapter = new CardPaymentAdapter($payment, $config);
        $result = $adapter->transform();

        $validator = new JsonSchema\Validator;
        $validator->validate($result, $this->getSchema(['paymentSchema', 'brazilPaymentSchema', 'cardPaymentSchema']));

        $this->assertTrue($validator->isValid(), $this->getJsonMessage($validator));
    }

    public function testAdaptEmptyCard()
    {
        $config = $this->generateConfig();
        $factory = new BuilderFactory('pt_BR');
        $payment = $factory->payment()->emptyCreditCard()->businessPerson()->build();

        $adapter = new CardPaymentAdapter($payment, $config);
        $result = $adapter->transform();

        $this->assertObjectHasAttribute('payment', $result);
    }

    public function testWithManualReview()
    {
        $config = $this->generateConfig();
        $factory = new BuilderFactory('pt_BR');
        $payment = $factory->payment()->creditCard()->businessPerson()->manualReview(true)->build();

        $adapter = new CardPaymentAdapter($payment, $config);
        $result = $adapter->transform();

        $this->assertObjectHasAttribute('payment', $result);
        $this->assertObjectHasAttribute('manual_review', $result->payment);
        $this->assertEquals(true, $result->payment->manual_review);
    }

    public function testWithoutManualReview()
    {
        $config = $this->generateConfig();
        $factory = new BuilderFactory('pt_BR');
        $payment = $factory->payment()->creditCard()->businessPerson()->build();

        $adapter = new CardPaymentAdapter($payment, $config);
        $result = $adapter->transform();

        $this->assertObjectHasAttribute('payment', $result);
        $this->assertObjectHasAttribute('manual_review', $result->payment);
        $this->assertEquals(null, $result->payment->manual_review);
    }

    public function testRequestAttributeNumber()
    {
        $config = $this->generateConfig();
        $factory = new BuilderFactory('pt_BR');
        $payment = $factory->payment()->emptyCreditCard()->businessPerson()->build();

        $adapter = new CardPaymentAdapter($payment, $config);
        $result = $adapter->transform();

        $numberOfKeys = count((array) $result);
        $this->assertEquals(5, $numberOfKeys);
        $this->assertObjectHasAttribute('integration_key', $result);
        $this->assertObjectHasAttribute('operation', $result);
        $this->assertObjectHasAttribute('mode', $result);
        $this->assertObjectHasAttribute('metadata', $result);
        $this->assertObjectHasAttribute('payment', $result);
    }

    public function testCardPaymentTransformWithCreateToken_shouldHaveTokenAndCreditCardInPayment()
    {
        $config = $this->generateConfig();
        $payment = $this->generateCreditCardPayment();
        $payment->card->createToken = true;
        $payment->card->token = md5($payment->card->number);
        $adapter = new CardPaymentAdapter($payment, $config);
        $result = $adapter->transform();

        $this->assertObjectHasAttribute('token', $result->payment);
        $this->assertObjectHasAttribute('create_token', $result->payment);
        $this->assertObjectHasAttribute('card_number', $result->payment->creditcard);
        $this->assertObjectHasAttribute('card_name', $result->payment->creditcard);
        $this->assertObjectHasAttribute('card_due_date', $result->payment->creditcard);
        $this->assertObjectHasAttribute('card_cvv', $result->payment->creditcard);
        $this->assertObjectNotHasAttribute('token', $result->payment->creditcard);
    }

    public function testCardPaymentTransformWithTokenButWithoutCreateToken_shouldHaveTokenInCard()
    {
        $config = $this->generateConfig();
        $payment = $this->generateCreditCardPayment();
        $payment->card->token = md5($payment->card->number);
        $adapter = new CardPaymentAdapter($payment, $config);
        $result = $adapter->transform();

        $this->assertObjectNotHasAttribute('token', $result->payment);
        $this->assertObjectNotHasAttribute('create_token', $result->payment);
        $this->assertObjectNotHasAttribute('card_number', $result->payment->creditcard);
        $this->assertObjectNotHasAttribute('card_name', $result->payment->creditcard);
        $this->assertObjectNotHasAttribute('card_due_date', $result->payment->creditcard);
        $this->assertObjectHasAttribute('card_cvv', $result->payment->creditcard);
        $this->assertObjectHasAttribute('token', $result->payment->creditcard);
    }

    public function testCardPaymentTransformWithoutTokenAndCreateToken_shouldHaveOnlyCardInfo()
    {
        $config = $this->generateConfig();
        $payment = $this->generateCreditCardPayment();
        $adapter = new CardPaymentAdapter($payment, $config);
        $result = $adapter->transform();

        $this->assertObjectNotHasAttribute('token', $result->payment);
        $this->assertObjectNotHasAttribute('create_token', $result->payment);
        $this->assertObjectHasAttribute('card_number', $result->payment->creditcard);
        $this->assertObjectHasAttribute('card_name', $result->payment->creditcard);
        $this->assertObjectHasAttribute('card_due_date', $result->payment->creditcard);
        $this->assertObjectHasAttribute('card_cvv', $result->payment->creditcard);
        $this->assertObjectNotHasAttribute('token', $result->payment->creditcard);
    }

    public function testCardPaymentTransformWithoutCardInfo_shouldNotHaveCreditCardInfo()
    {
        $config = $this->generateConfig();
        $payment = $this->generateCreditCardPayment();
        $payment->card = null;
        $adapter = new CardPaymentAdapter($payment, $config);
        $result = $adapter->transform();

        $this->assertObjectNotHasAttribute('token', $result->payment);
        $this->assertObjectNotHasAttribute('create_token', $result->payment);
        $this->assertObjectNotHasAttribute('creditcard', $result->payment);
    }

    private function generateConfig()
    {
        return new Config([
            'sandboxIntegrationKey' => 'testIntegrationKey'
        ]);
    }

    private function generateCreditCardPayment()
    {
        $factory = new BuilderFactory('pt_BR');

        return $factory->payment()
            ->creditCard()
            ->build();
    }
}
