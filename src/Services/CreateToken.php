<?php

namespace Ebanx\Benjamin\Services;

use Ebanx\Benjamin\Models\Card;
use Ebanx\Benjamin\Services\Adapters\CardAdapter;
use Ebanx\Benjamin\Services\Http\HttpService;

class CreateToken extends HttpService
{
    public function forCard(Card $card)
    {
        $cardAdapter = new CardAdapter($this->config, $card);

        return $this->client->createToken($cardAdapter->transform());
    }
}
