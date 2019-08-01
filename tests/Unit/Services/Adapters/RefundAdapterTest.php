<?php
namespace Tests\Unit\Services\Adapters;

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\SplitRule;
use Ebanx\Benjamin\Services\Adapters\RefundAdapter;
use Tests\TestCase;

class RefundAdapterTest extends TestCase
{
    public function testTransformWithHash()
    {
        $adapter = new RefundAdapter(
            [
                'hash' => 'hash_for_test',
                'amount' => '10.00',
                'description' => 'Description for test',

            ],
            new Config()
        );

        $result = $adapter->transform();

        $this->assertEquals('hash_for_test', $result['hash']);
        $this->assertEquals('10.00', $result['amount']);
        $this->assertEquals('Description for test', $result['description']);
        $this->assertArrayNotHasKey('merchant_refund_code', $result);
    }

    public function testTransformWithMerchantPaymentCodeAndMerchantRefundCode()
    {
        $adapter = new RefundAdapter(
            [
                'merchantPaymentCode' => 'merchant_payment_code_for_test',
                'amount' => 10.00,
                'description' => 'Description for test',
                'merchantRefundCode' => 'optional_merchant_refund_code_for_test',

            ],
            new Config()
        );

        $result = $adapter->transform();

        $this->assertEquals('merchant_payment_code_for_test', $result['merchant_payment_code']);
        $this->assertEquals(10.00, $result['amount']);
        $this->assertEquals('Description for test', $result['description']);
        $this->assertEquals('optional_merchant_refund_code_for_test', $result['merchant_refund_code']);
    }

    public function testTransformWithSplitRulesWithAmount()
    {
        $adapter = new RefundAdapter(
            [
                'merchantPaymentCode' => 'merchant_payment_code_for_test',
                'amount' => 10.00,
                'description' => 'Description for test',
                'merchantRefundCode' => 'optional_merchant_refund_code_for_test',
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
            ],
            new Config()
        );

        $result = $adapter->transform();

        $this->assertEquals('merchant_recipient_code_1', $result['split'][0]->recipient_code);
        $this->assertEquals('8', $result['split'][0]->amount);
        $this->assertTrue($result['split'][0]->charge_fee);
        $this->assertTrue($result['split'][0]->liable);

        $this->assertEquals('merchant_recipient_code_2', $result['split'][1]->recipient_code);
        $this->assertEquals('7.5', $result['split'][1]->amount);
        $this->assertTrue($result['split'][1]->charge_fee);
        $this->assertTrue($result['split'][1]->liable);

        $this->assertEquals('merchant_recipient_code_3', $result['split'][2]->recipient_code);
        $this->assertEquals(4.50, $result['split'][2]->amount);
        $this->assertFalse($result['split'][2]->charge_fee);
        $this->assertFalse($result['split'][2]->liable);
    }

    public function testTransformWithSplitRulesWithPercentage()
    {
        $adapter = new RefundAdapter(
            [
                'merchantPaymentCode' => 'merchant_payment_code_for_test',
                'amount' => 10.00,
                'description' => 'Description for test',
                'merchantRefundCode' => 'optional_merchant_refund_code_for_test',
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
            ],
            new Config()
        );

        $result = $adapter->transform();

        $this->assertEquals('merchant_recipient_code_1', $result['split'][0]->recipient_code);
        $this->assertEquals('30.00', $result['split'][0]->percentage);
        $this->assertTrue($result['split'][0]->charge_fee);
        $this->assertFalse($result['split'][0]->liable);

        $this->assertEquals('merchant_recipient_code_2', $result['split'][1]->recipient_code);
        $this->assertEquals('30', $result['split'][1]->percentage);
        $this->assertFalse($result['split'][1]->charge_fee);
        $this->assertTrue($result['split'][1]->liable);

        $this->assertEquals('merchant_recipient_code_3', $result['split'][2]->recipient_code);
        $this->assertEquals(40.00, $result['split'][2]->percentage);
        $this->assertFalse($result['split'][2]->charge_fee);
        $this->assertFalse($result['split'][2]->liable);
    }
}
