<?php
namespace Tests\Unit\Services\Adapters;

use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Models\Person;
use Ebanx\Benjamin\Models\SplitRule;
use Ebanx\Benjamin\Services\Adapters\RequestAdapter;
use Tests\Helpers\Builders\BuilderFactory;
use JsonSchema;
use Tests\TestCase;

class RequestAdapterTest extends TestCase
{
    public function testJsonSchema()
    {
        $factory = new BuilderFactory('pt_BR');
        $request = $factory
            ->request()
            ->build();

        $this->assertModelJsonSchemaCompliance($request);
    }

    public function testJsonSchemaWithSubAccount()
    {
        $factory = new BuilderFactory('pt_BR');
        $request = $factory
            ->request()
            ->withSubAccount()
            ->build();

        $this->assertModelJsonSchemaCompliance($request);
    }

    public function testTransformNotificationUrl()
    {
        $expected = md5(rand(1, 999));

        $nullConfig = new Config();
        $goodConfig = new Config(['notificationUrl' => $expected]);

        $factory = new BuilderFactory('pt_BR');
        $request = $factory->request()->build();

        $adapter = new FakeRequestAdapter($request, $nullConfig);
        $result1 = $adapter->transform();

        $adapter = new FakeRequestAdapter($request, $goodConfig);
        $result2 = $adapter->transform();

        $this->assertEmpty(
            $result1->notification_url,
            'Request adapter injected a notification url when it shouldn\'t'
        );

        $this->assertEquals(
            $expected,
            $result2->notification_url,
            'Request adapter failed to inject a notification url'
        );
    }

    public function testTransformRedirectUrl()
    {
        $expected = 'SAMPLE_URL';

        $factory = new BuilderFactory('pt_BR');
        $request = $factory->request()->build();
        $request->redirectUrl = $expected;

        $adapter = new FakeRequestAdapter($request, new Config());
        $result = $adapter->transform();

        $this->assertEquals(
            $expected,
            $result->redirect_url,
            'Request adapter failed to send redirect_url'
        );
    }

    public function testWithManualReview()
    {
        $factory = new BuilderFactory('pt_BR');
        $request = $factory->request()->withManualReview()->build();

        $adapter = new FakeRequestAdapter($request, new Config());
        $result = $adapter->transform();

        $this->assertObjectHasAttribute('manual_review', $result);
        $this->assertTrue($result->manual_review);
    }

    public function testIntegrationKey()
    {
        $factory = new BuilderFactory('pt_BR');
        $request = $factory->request()->build();

        $liveKey = 'testIntegrationKey';
        $sandboxKey = 'testSandboxIntegrationKey';

        $config = new Config([
            'integrationKey' => $liveKey,
            'sandboxIntegrationKey' => $sandboxKey
        ]);

        // Sandbox
        $adapter = new FakeRequestAdapter($request, $config);
        $this->assertEquals($sandboxKey, $adapter->getIntegrationKey());

        // Live
        $config->isSandbox = false;
        $adapter = new FakeRequestAdapter($request, $config);
        $this->assertEquals($liveKey, $adapter->getIntegrationKey());
    }

    public function testUserValues()
    {
        $factory = new BuilderFactory('pt_BR');
        $request = $factory->request()->build();

        $expected = [
            1 => 'from_tests',
            2 => 'DO NOT PAY',
            5 => 'Benjamin',
        ];

        $request->userValues = [
            1 => 'Override me',
            2 => 'DO NOT PAY',
        ];

        $config = new Config([
            'userValues' => [
                1 => 'from_tests',
            ],
        ]);

        $adapter = new FakeRequestAdapter($request, $config);
        $result = $adapter->transform();

        $resultValues = array_filter([
            1 => isset($result->user_value_1) ? $result->user_value_1 : null,
            2 => isset($result->user_value_2) ? $result->user_value_2 : null,
            3 => isset($result->user_value_3) ? $result->user_value_3 : null,
            4 => isset($result->user_value_4) ? $result->user_value_4 : null,
            5 => isset($result->user_value_5) ? $result->user_value_5 : null,
        ]);

        $this->assertEquals($expected, $resultValues);
    }

    public function testAddress()
    {
        $factory = new BuilderFactory('pt_BR');
        $request = $factory->request()->build();

        $expected = 'Rua Marechal Deodoro';
        $request->address = new Address([
            'address' => $expected,
            'country' => Country::BRAZIL
        ]);

        $adapter = new FakeRequestAdapter($request, new Config());
        $result = $adapter->transform();

        $this->assertEquals($expected, $result->address);
    }

    private function assertModelJsonSchemaCompliance($model)
    {
        $config = new Config([
            'sandboxIntegrationKey' => 'testIntegrationKey'
        ]);

        $adapter = new FakeRequestAdapter($model, $config);
        $result = $adapter->transform();

        $validator = new JsonSchema\Validator();
        $validator->validate($result, $this->getSchema('requestSchema'));

        $this->assertTrue($validator->isValid(), $this->getJsonMessage($validator));
    }

    public function testTransformWithSplitRulesWithAmount()
    {
        $adapter = new FakeAdapter(new Payment([
            'person' => new Person(),
            'address' => new Address(),
            'split' => [
                new SplitRule([
                    'recipientCode' => 'merchant_recipient_code_1',
                    'amount' => '8',
                    'chargeFee' => true,
                    'liable' => true,
                ]),
                new SplitRule([
                    'recipientCode' => 'merchant_recipient_code_2',
                    'amount' => '7.5',
                    'chargeFee' => true,
                    'liable' => true,
                ]),
                new SplitRule([
                    'recipientCode' => 'merchant_recipient_code_3',
                    'amount' => 4.50,
                    'chargeFee' => false,
                    'liable' => false,
                ]),
            ]
        ]), new Config());

        $result = $adapter->transform();

        $this->assertEquals('merchant_recipient_code_1', $result->payment->split[0]->recipient_code);
        $this->assertEquals('8', $result->payment->split[0]->amount);
        $this->assertTrue($result->payment->split[0]->charge_fee);
        $this->assertTrue($result->payment->split[0]->liable);

        $this->assertEquals('merchant_recipient_code_2', $result->payment->split[1]->recipient_code);
        $this->assertEquals('7.5', $result->payment->split[1]->amount);
        $this->assertTrue($result->payment->split[1]->charge_fee);
        $this->assertTrue($result->payment->split[1]->liable);

        $this->assertEquals('merchant_recipient_code_3', $result->payment->split[2]->recipient_code);
        $this->assertEquals(4.50, $result->payment->split[2]->amount);
        $this->assertFalse($result->payment->split[2]->charge_fee);
        $this->assertFalse($result->payment->split[2]->liable);
    }

    public function testTransformWithSplitRulesWithPercentage()
    {
        $adapter = new FakeAdapter(new Payment([
            'person' => new Person(),
            'address' => new Address(),
            'split' => [
                new SplitRule([
                    'recipientCode' => 'merchant_recipient_code_1',
                    'percentage' => '30.00',
                    'chargeFee' => true,
                    'liable' => false,
                ]),
                new SplitRule([
                    'recipientCode' => 'merchant_recipient_code_2',
                    'percentage' => '30',
                    'chargeFee' => false,
                    'liable' => true,
                ]),
                new SplitRule([
                    'recipientCode' => 'merchant_recipient_code_3',
                    'percentage' => 40.00,
                    'chargeFee' => false,
                    'liable' => false,
                ]),
            ]
        ]), new Config());

        $result = $adapter->transform();

        $this->assertEquals('merchant_recipient_code_1', $result->payment->split[0]->recipient_code);
        $this->assertEquals('30.00', $result->payment->split[0]->percentage);
        $this->assertTrue($result->payment->split[0]->charge_fee);
        $this->assertFalse($result->payment->split[0]->liable);

        $this->assertEquals('merchant_recipient_code_2', $result->payment->split[1]->recipient_code);
        $this->assertEquals('30', $result->payment->split[1]->percentage);
        $this->assertFalse($result->payment->split[1]->charge_fee);
        $this->assertTrue($result->payment->split[1]->liable);

        $this->assertEquals('merchant_recipient_code_3', $result->payment->split[2]->recipient_code);
        $this->assertEquals(40.00, $result->payment->split[2]->percentage);
        $this->assertFalse($result->payment->split[2]->charge_fee);
        $this->assertFalse($result->payment->split[2]->liable);
    }
}

class FakeRequestAdapter extends RequestAdapter
{
    public function getIntegrationKey()
    {
        return parent::getIntegrationKey();
    }
}
