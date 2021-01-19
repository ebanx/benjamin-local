<?php
namespace Ebanx\Benjamin\Services\Adapters;

class CardPaymentAdapter extends BrazilPaymentAdapter
{
    protected function transformPayment()
    {
        $transformed = parent::transformPayment();

        if (empty($this->payment->card)) {
            return $transformed;
        }

        $transformed->payment_type_code = $this->payment->card->type;
        $transformed->instalments = $this->payment->instalments;
        $transformed->creditcard = $this->transformCard();
        $transformed->device_id = $this->payment->deviceId;
        $transformed->manual_review = $this->payment->manualReview;

        if (property_exists($this->payment->card, 'createToken') && !empty($this->payment->card->createToken)) {
            $transformed->create_token = $this->payment->card->createToken;
            $transformed->token = $this->payment->card->token;
        }

        return $transformed;
    }

    private function transformCard()
    {
        $cardObject =  (object) [
            'card_number' => $this->payment->card->number,
            'card_name' => $this->payment->card->name,
            'card_due_date' => $this->payment->card->dueDate ? $this->payment->card->dueDate->format('m/Y') : null,
            'card_cvv' => $this->payment->card->cvv,
            'auto_capture' => $this->payment->card->autoCapture,
        ];

        if (property_exists($this->payment->card, 'createToken') && !empty($this->payment->card->createToken)) {
            return $cardObject;
        }

        if (property_exists($this->payment->card, 'token') && !empty($this->payment->card->token)) {
            return (object) [
                'token' => $this->payment->card->token
            ];
        }

        return $cardObject;
    }
}
