<?php



use Orchestra\Testbench\TestCase;
use TooPago\Payu\Payments;



class PaymentsTest extends TestCase
{



    private function getUser()
    {
        return (object) [
            'id' => 1,
            'name' => 'Taylor Otwell Lopez',
            'email' => 'user@tests.com',
            'identification' => '',
            'CURRENCY' => 'MXN',
            'COUNTRY' => 'MX',
        ];
    }

    private function getOrder()
    {
        //PAYMENT_METHOD: OXXO||SEVEN_ELEVEN||OTHERS_CASH_MX
        return (object) [
            'payu_order_id' => null,
            'transaction_id' => null,
            'state' => 1,
            'reference' => uniqid(time()),
            'REFERENCE_CODE' => 'toopago_' . uniqid(),
            'value' => 600,
            'user_id' => 1,
            'PAYMENT_METHOD' => 'OXXO'
        ];
    }
    
    private function getCard()
    {
        //PAYER_NAME: APPROVED||PENDING||REJECTED
        //PAYMENT_METHOD : VISA||MASTERCARD||AMEX
        //CREDIT_CARD_EXPIRATION_DATE : YYYY/MM
        return (object) [
            'PAYER_NAME' => 'PENDING',
            'CREDIT_CARD_NUMBER' => '5499490537511933',
            'CREDIT_CARD_EXPIRATION_DATE' => '2023/02',
            'PAYMENT_METHOD' => 'MASTERCARD',
            'CREDIT_CARD_SECURITY_CODE' => '897',
            'INSTALLMENTS_NUMBER' => '1',
            'TOKEN_ID' => '759497b1-ab49-4145-b652-0ad581a9f612',
        ];
    }

    /** @test */
    function cards()
    {
        $user = $this->getUser();
        $order = $this->getOrder();
        $card = $this->getCard();
        $sendData = [
            'REFERENCE_CODE' => $order->REFERENCE_CODE,
            'DESCRIPTION' => 'Payment cc test',
            'VALUE' => $order->value,
            'BUYER_NAME' => $user->name,
            'PAYER_NAME' => $card->PAYER_NAME,        
            'CREDIT_CARD_NUMBER' => $card->CREDIT_CARD_NUMBER,
            'CREDIT_CARD_EXPIRATION_DATE' => $card->CREDIT_CARD_EXPIRATION_DATE,
            'CREDIT_CARD_SECURITY_CODE' => $card->CREDIT_CARD_SECURITY_CODE,
            'PAYMENT_METHOD' => $card->PAYMENT_METHOD,
            'INSTALLMENTS_NUMBER' => $card->INSTALLMENTS_NUMBER,
            'CURRENCY' => $user->CURRENCY,
            'COUNTRY' => $user->COUNTRY,
        ];
        $payu = new Payments();
        $payu->CreatePaymentCard($sendData, function($response) {
            $this->assertTrue(true);
        }, function($error) {
            $this->assertFalse(true);
        });
    }

    
    /** @test */
    function cash()
    {
        $user = $this->getUser();
        $order = $this->getOrder();
        $sendData = [
            'REFERENCE_CODE' => $order->REFERENCE_CODE,
            'DESCRIPTION' => 'Payment Cash Test',
            'VALUE' => $order->value,
            'PAYMENT_METHOD' => $order->PAYMENT_METHOD,
            'BUYER_NAME' => $user->name,
            'PAYER_NAME' => $user->name,
            'CURRENCY' => $user->CURRENCY,
            'COUNTRY' => $user->COUNTRY,
        ];
        $payu = new Payments();
        $payu->CreatePaymentCash($sendData, function($response) {
            $this->assertTrue(true);
        }, function($error) {
            $this->assertFalse(true);
        });
    }



    /** @test */
    function tokenizarCard()
    {
        $card = $this->getCard();
        $sendData = [
            'PAYER_NAME' => $card->PAYER_NAME,
            'PAYER_ID' => '1' . uniqid(),
            'CREDIT_CARD_NUMBER' => $card->CREDIT_CARD_NUMBER,
            'CREDIT_CARD_EXPIRATION_DATE' => $card->CREDIT_CARD_EXPIRATION_DATE,
            'PAYMENT_METHOD' => $card->PAYMENT_METHOD,
        ];
        $payu = new Payments();
        $payu->tokenizarCard($sendData, function($response) {
            $this->assertTrue(true);
        }, function($error) {
            $this->assertFalse(true);
        });
    }




    /** @test */
    function changeWithToken()
    {
        $user = $this->getUser();
        $order = $this->getOrder();
        $card = $this->getCard();
        $sendData = [
            'REFERENCE_CODE' => $order->REFERENCE_CODE,
            'DESCRIPTION' => 'Payment Token test',
            'VALUE' => $order->value,
            'BUYER_NAME' => $user->name,
            'PAYER_NAME' => $card->PAYER_NAME, 
            'TOKEN_ID' => $card->TOKEN_ID,
            'PAYMENT_METHOD' => $card->PAYMENT_METHOD,
            'INSTALLMENTS_NUMBER' => $card->INSTALLMENTS_NUMBER,
            'CURRENCY' => $user->CURRENCY,
            'COUNTRY' => $user->COUNTRY,
        ];
        $payu = new Payments();
        $payu->changeWithToken($sendData, function($response) {
            $this->assertTrue(true);
        }, function($error) {
            $this->assertFalse(true);
        });
    }


}
