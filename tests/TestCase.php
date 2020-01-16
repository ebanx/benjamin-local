<?php
namespace Tests;

use Tests\Helpers\Mocks\Http\ClientForTests;
use Tests\Helpers\Mocks\Http\EchoEngine;
use Ebanx\Benjamin\Services\Http\Client;
use JsonSchema;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function getMockedClient($response)
    {
        return new ClientForTests(new EchoEngine(Client::SANDBOX_URL, $response));
    }

    protected function getSchema($schemas)
    {
        if (!is_array($schemas)) {
            $schemas = [$schemas];
        }

        $object = [];
        foreach ($schemas as $schema) {
            $object = array_merge_recursive($object, json_decode(file_get_contents(dirname(__FILE__) . '/Unit/Services/Adapters/Schemas/' . $schema . '.json'), true));
        }

        return json_decode(json_encode($object));
    }

    protected function getJsonMessage(JsonSchema\Validator $validator)
    {
        $message = '';
        $message .= "JSON does not validate. Violations:\n";
        foreach ($validator->getErrors() as $error) {
            $message .= sprintf("[%s] %s\n", $error['property'], $error['message']);
        }
        return $message;
    }
}
