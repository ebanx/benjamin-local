<?php
namespace Ebanx\Benjamin\Services;

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Services\Adapters\RefundAdapter;
use Ebanx\Benjamin\Services\Http\HttpService;

class Refund extends HttpService
{
    /**
     * @param string    $hash                The payment hash.
     * @param float     $amount              The amount to be refunded; expressed in the original payment currency.
     * @param string    $description         Description of the refund reason.
     * @param string    $merchantRefundCode  An optional merchant refund code.
     * @return array
     */
    public function requestByHash($hash, $amount, $description, $merchantRefundCode = '')
    {
        $data = self::handleRequestData([
            'hash' => $hash,
            'amount' => $amount,
            'description' => $description,
            'merchantRefundCode' => $merchantRefundCode,
        ]);

        return $this->request($data);
    }

    /**
     * @param string    $merchantPaymentCode    The merchant payment code
     * @param float     $amount                 The amount to be refunded; expressed in the original payment currency.
     * @param string    $description            Description of the refund reason.
     * @param string    $merchantRefundCode  An optional merchant refund code.
     * @return array
     */
    public function requestByMerchantPaymentCode($merchantPaymentCode, $amount, $description, $merchantRefundCode = '')
    {
        $data = self::handleRequestData([
            'merchantPaymentCode' => $merchantPaymentCode,
            'amount' => $amount,
            'description' => $description,
            'merchantRefundCode' => $merchantRefundCode,
        ]);

        return $this->request($data);
    }

    /**
     * @param string    $hash                The payment hash.
     * @param float     $amount              The amount to be refunded; expressed in the original payment currency.
     * @param string    $description         Description of the refund reason.
     * @param string    $merchantRefundCode  An optional merchant refund code.
     * @param array     $split               An optional Split array.
     *
     * @return array
     */
    public function requestByHashWithSplit($hash, $amount, $description, $merchantRefundCode = '', array $split = [])
    {
        $data = self::handleRequestData([
            'hash' => $hash,
            'amount' => $amount,
            'description' => $description,
            'merchantRefundCode' => $merchantRefundCode,
            'split' => $split,
        ]);

        return $this->requestWithSplit($data);
    }

    /**
     * @param string    $merchantPaymentCode  The merchant payment code
     * @param float     $amount               The amount to be refunded; expressed in the original payment currency.
     * @param string    $description          Description of the refund reason.
     * @param string    $merchantRefundCode   An optional merchant refund code.
     * @param array     $split                An optional Split array.
     * @return array
     */
    public function requestByMerchantPaymentCodeWithSplit($merchantPaymentCode, $amount, $description, $merchantRefundCode = '', array $split = [])
    {
        $data = self::handleRequestData([
            'merchantPaymentCode' => $merchantPaymentCode,
            'amount' => $amount,
            'description' => $description,
            'merchantRefundCode' => $merchantRefundCode,
            'split' => $split,
        ]);

        return $this->requestWithSplit($data);
    }

    /**
     * @param $refundId
     *
     * @return array
     */
    public function cancel($refundId)
    {
        $adapter = new RefundAdapter(['refundId' => $refundId], $this->config);
        $response = $this->client->refund($adapter->transformCancel());

        return $response;
    }

    /**
     * @param $data
     * @return array
     */
    private function request($data)
    {
        $adapter = new RefundAdapter($data, $this->config);
        $response = $this->client->refund($adapter->transform());

        return $response;
    }

    /**
     * @param $data
     * @return array
     */
    private function requestWithSplit($data)
    {
        $adapter = new RefundAdapter($data, $this->config);
        $response = $this->client->refundWithSplit($adapter->transform());

        return $response;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private static function handleRequestData(array $data)
    {
        $requestData = [
            'amount' => $data['amount'],
            'description' => $data['description'],
        ];

        if (!empty($data['hash'])) {
            $requestData['hash'] = $data['hash'];
        }

        if (!empty($data['merchantPaymentCode'])) {
            $requestData['merchantPaymentCode'] = $data['merchantPaymentCode'];
        }

        if (!empty($data['merchantRefundCode'])) {
            $requestData['merchantRefundCode'] = $data['merchantRefundCode'];
        }

        if (!empty($data['split'])) {
            $requestData['split'] = $data['split'];
        }

        return $requestData;
    }
}
