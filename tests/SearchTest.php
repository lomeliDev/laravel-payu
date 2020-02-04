<?php



use Orchestra\Testbench\TestCase;
use TooPago\Payu\Searchable;


class SearchTest extends TestCase
{





    private function getOrder()
    {
        return (object) [
            'ORDER_ID' => '848363665',
            'REFERENCE_CODE' => 'toopago_1_5e39b92b6f448',
            'TRANSACTION_ID' => '0f0de284-0a12-48dc-8476-d185db306d58'
        ];
    }
    


    /** @test */
    function searchById()
    {
        $order = $this->getOrder();
        $sendData = [
            'ORDER_ID' => $order->ORDER_ID,
        ];
        $payu = new Searchable();
        $payu->searchById($sendData, function($response) {
            $this->assertTrue(true);
        }, function($error) {
            $this->assertFalse(true);
        });
    }

    /** @test */
    function searchByReference()
    {
        $order = $this->getOrder();
        $sendData = [
            'REFERENCE_CODE' => $order->REFERENCE_CODE,
        ];
        $payu = new Searchable();
        $payu->searchByReference($sendData, function($response) {
            $this->assertTrue(true);
        }, function($error) {
            $this->assertFalse(true);
        });
    }

    /** @test */
    function searchByTransaction()
    {
        $order = $this->getOrder();
        $sendData = [
            'TRANSACTION_ID' => $order->TRANSACTION_ID,
        ];
        $payu = new Searchable();
        $payu->searchByTransaction($sendData, function($response) {
            $this->assertTrue(true);
        }, function($error) {
            $this->assertFalse(true);
        });
    }


}
