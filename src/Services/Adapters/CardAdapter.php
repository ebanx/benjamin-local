<?php

namespace Ebanx\Benjamin\Services\Adapters;

use Ebanx\Benjamin\Models\Card;
use Ebanx\Benjamin\Models\Configs\Config;

class CardAdapter extends BaseAdapter
{
    private $card;

    public function __construct(Config $config, Card $card)
    {
        $this->card = $card;
        parent::__construct($config);
    }

    public function transform()
    {
        return (object) [
            'integration_key' => $this->getIntegrationKey(),
            'country' => 'br',
            'payment_type_code' => $this->card->type,
            'creditcard' => [
                'card_number' => $this->card->number,
                'card_name' => $this->card->name,
                'card_due_date' => $this->card->dueDate,
                'card_cvv' => $this->card->cvv,
            ]
        ];
    }


}