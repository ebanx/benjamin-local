<?php
namespace Ebanx\Benjamin\Models;

class SplitRule extends BaseModel
{
    /**
     * Unique Merchant Recipient Code (max 128 characters).
     *
     * @var string
     */
    public $recipientCode;

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
     * @var string
     */
    public $liable = false;

    /**
     * If you will be charged for a fee.
     *
     * @var string
     */
    public $chargeFee = false;
}
