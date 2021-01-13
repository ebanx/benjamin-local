<?php
namespace Ebanx\Benjamin\Services\Adapters;

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\SplitRule;

class RefundAdapter extends BaseAdapter
{
    /**
     * @var array
     */
    private $data;

    /**
     * RefundAdapter constructor.
     *
     * @param array $data
     * @param Config $config
     */
    public function __construct($data, Config $config)
    {
        $this->data = $data;
        parent::__construct($config);
    }

    public function transform()
    {
        $transformed = [
            'integration_key' => $this->getIntegrationKey(),
            'operation' => 'request',
            'amount' => $this->data['amount'],
            'description' => $this->data['description'],
        ];
        if (isset($this->data['hash'])) {
            $transformed['hash'] = $this->data['hash'];
        }

        if (isset($this->data['merchantPaymentCode'])) {
            $transformed['merchant_payment_code'] = $this->data['merchantPaymentCode'];
        }

        if (isset($this->data['merchantRefundCode'])) {
            $transformed['merchant_refund_code'] = $this->data['merchantRefundCode'];
        }

        if (isset($this->data['requester'])) {
            $transformed['requester'] = $this->data['requester'];
        }

        if (!empty($this->data['split']) && is_array($this->data['split'])) {
            $transformed['split'] = SplitRule::transformSplitRules($this->data['split']);
        }

        return $transformed;
    }

    public function transformCancel()
    {
        return [
            'integration_key' => $this->getIntegrationKey(),
            'operation' => 'cancel',
            'refund_id' => $this->data['refundId'],
        ];
    }
}
