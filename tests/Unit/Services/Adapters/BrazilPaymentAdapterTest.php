<?php
namespace Tests\Unit\Services\Adapters;

use Ebanx\Benjamin\Services\Adapters\BrazilPaymentAdapter;
use Tests\Helpers\Builders\BuilderFactory;
use JsonSchema;
use Ebanx\Benjamin\Models\Configs\Config;
use Tests\TestCase;

class BrazilPaymentAdapterTest extends TestCase
{
    public function testJsonSchema()
    {
        $config = new Config([
            'sandboxIntegrationKey' => 'testIntegrationKey'
        ]);
        $factory = new BuilderFactory('pt_BR');
        $payment = $factory->payment()->boleto()->businessPerson()->build();

        $adapter = new BrazilFakeAdapter($payment, $config);
        $result = $adapter->transform();

        $validator = new JsonSchema\Validator;
        $validator->validate($result, $this->getSchema(['paymentSchema', 'brazilPaymentSchema']));

        $this->assertTrue($validator->isValid(), $this->getJsonMessage($validator));
    }

    public function testPJPaymentWithoutResponsibleParam()
    {
        $config = new Config([
            'sandboxIntegrationKey' => 'testIntegrationKey'
        ]);
        $factory = new BuilderFactory('pt_BR');
        $payment = $factory->payment()->boleto()->businessPerson()->build();
        unset($payment->responsible);

        $adapter = new BrazilFakeAdapter($payment, $config);
        $result = $adapter->transform();

        $this->assertTrue(!isset($result->payment->responsible));
    }

    public function testPJPaymentWithResponsibleParam()
    {
        $config = new Config([
            'sandboxIntegrationKey' => 'testIntegrationKey'
        ]);
        $factory = new BuilderFactory('pt_BR');
        $payment = $factory->payment()->boleto()->businessPerson()->build();

        $adapter = new BrazilFakeAdapter($payment, $config);
        $result = $adapter->transform();

        $this->assertTrue(isset($result->payment->responsible));
    }

    public function testPJPaymentWithEmptyResponsibleParam()
    {
        $config = new Config([
            'sandboxIntegrationKey' => 'testIntegrationKey'
        ]);
        $factory = new BuilderFactory('pt_BR');
        $payment = $factory->payment()->boleto()->businessPerson()->build();
        $payment->responsible = null;

        $adapter = new BrazilFakeAdapter($payment, $config);
        $result = $adapter->transform();

        $this->assertTrue(!isset($result->payment->responsible));
    }
}

class BrazilFakeAdapter extends BrazilPaymentAdapter
{
}
