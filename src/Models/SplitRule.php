<?php
namespace Ebanx\Benjamin\Models;

class SplitRule extends BaseModel
{
    /**
     * Unique Merchant Recipient Code (max 128 characters).
     *
     * @var string|null
     */
    public $recipientCode = null;

    /**
     * A percentage of amount total value part. E.g.,: 25
     *
     * @var float|null
     */
    public $percentage = null;

    /**
     * A amount to send to the recipient. Maximum two decimal places. E.g.,: 55.90
     *
     * @var float|null
     */
    public $amount = null;

    /**
     * If is responsible for chargebacks fees.
     *
     * @var bool|null
     */
    public $liable = null;

    /**
     * If you will be charged for a fee.
     *
     * @var bool|null
     */
    public $chargeFee = null;

    public static function transformSplitRules(array $split)
    {
        $splitRules = [];

        foreach ($split as $splitRule) {
            $properties = [
                'recipient_code' => $splitRule->recipientCode,
            ];

            if (property_exists($splitRule, 'percentage')) {
                $properties['percentage'] = $splitRule->percentage;
            }

            if (property_exists($splitRule, 'amount')) {
                $properties['amount'] = $splitRule->amount;
            }

            if (property_exists($splitRule, 'liable')) {
                $properties['liable'] = $splitRule->liable;
            }

            if (property_exists($splitRule, 'chargeFee')) {
                $properties['charge_fee'] = $splitRule->chargeFee;
            }

            $splitRules[] = (object) $properties;
        }

        return $splitRules;
    }
}
