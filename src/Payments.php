<?php

namespace TooPago\Payu;

use InvalidArgumentException;
use TooPago\Payu\LaravelPayU;
use Exception;

class Payments
{





    public function CreatePaymentCash($sendData , $onSuccess, $onError)
    {
        LaravelPayU::setPayUEnvironment();
        try {
            $EXPIRATION_DATE = date("Y-m-d" , strtotime(date("Y-m-d")."+ 2 days"));
            $EXPIRATION_DATE .= "T" . date("H:i:s"); 
            $parameters = array(
                \PayUParameters::ACCOUNT_ID => LaravelPayU::getAccountId(),
                \PayUParameters::REFERENCE_CODE => $sendData['REFERENCE_CODE'],
                \PayUParameters::DESCRIPTION => $sendData['DESCRIPTION'],
                \PayUParameters::VALUE => $sendData['VALUE'],
                \PayUParameters::CURRENCY => $sendData['CURRENCY'],
                \PayUParameters::BUYER_NAME => $sendData['BUYER_NAME'],
                \PayUParameters::PAYER_NAME => $sendData['PAYER_NAME'],
                \PayUParameters::BUYER_EMAIL => LaravelPayU::getBUYER_EMAIL(),
                \PayUParameters::PAYER_DNI=> LaravelPayU::getPAYER_DNI(),
                \PayUParameters::PAYMENT_METHOD => $sendData['PAYMENT_METHOD'],
                \PayUParameters::COUNTRY => $sendData['COUNTRY'],
                \PayUParameters::EXPIRATION_DATE => $EXPIRATION_DATE,
            );
            $response = \PayUPayments::doAuthorizationAndCapture($parameters);
            if($response && $response->code == 'SUCCESS')
            {
                if( $response->transactionResponse && $response->transactionResponse->state && $response->transactionResponse->state == 'PENDING' )
                {
                    if( $response->transactionResponse->responseCode == 'PENDING_TRANSACTION_CONFIRMATION' )
                    {
                        $payu = new \stdClass;
                        $payu->state = $response->transactionResponse->state;
                        $payu->orderId = $response->transactionResponse->orderId;
                        $payu->transactionId = $response->transactionResponse->transactionId;
                        $payu->EXPIRATION_DATE = $response->transactionResponse->extraParameters->EXPIRATION_DATE;
                        $payu->BAR_CODE = $response->transactionResponse->extraParameters->BAR_CODE;
                        $payu->REFERENCE = $response->transactionResponse->extraParameters->REFERENCE;
                        $payu->URL_PAYMENT_RECEIPT_PDF = $response->transactionResponse->extraParameters->URL_PAYMENT_RECEIPT_PDF;
                        $payu->URL_PAYMENT_RECEIPT_HTML = $response->transactionResponse->extraParameters->URL_PAYMENT_RECEIPT_HTML;
                        $payu->PAYMENT_WAY_ID = $response->transactionResponse->extraParameters->PAYMENT_WAY_ID;
                        $payu->BANK_REFERENCED_CODE = $response->transactionResponse->extraParameters->BANK_REFERENCED_CODE;
                        $onSuccess($payu , $this);       
                    } else {
                        $payu = new \stdClass;
                        $payu->message = 'Transaccion Declinada';
                        $onError($payu);
                    }
                } else {
                    $payu = new \stdClass;
                    $payu->message = 'Transaccion Declinada';
                    $onError($payu);
                }
            } else {
                $payu = new \stdClass;
                $payu->message = 'Transaccion Declinada';
                $onError($payu);
            }
        } catch (\PayUException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\ConnectionException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\RuntimeException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\InvalidArgumentException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        }
    }


    public function CreatePaymentCard($sendData , $onSuccess, $onError)
    {
        LaravelPayU::setPayUEnvironment();
        try {
            $EXPIRATION_DATE = date("Y-m-d" , strtotime(date("Y-m-d")."+ 2 days"));
            $EXPIRATION_DATE .= "T" . date("H:i:s"); 
            $parameters = array(
                \PayUParameters::ACCOUNT_ID => LaravelPayU::getAccountId(),
                \PayUParameters::REFERENCE_CODE => $sendData['REFERENCE_CODE'],
                \PayUParameters::DESCRIPTION => $sendData['DESCRIPTION'],
                \PayUParameters::VALUE => $sendData['VALUE'],
                \PayUParameters::CURRENCY => $sendData['CURRENCY'],
                \PayUParameters::COUNTRY => $sendData['COUNTRY'],
                \PayUParameters::BUYER_NAME => $sendData['BUYER_NAME'],
                \PayUParameters::PAYER_NAME => $sendData['PAYER_NAME'],
                \PayUParameters::BUYER_EMAIL => LaravelPayU::getBUYER_EMAIL(),
                \PayUParameters::CREDIT_CARD_NUMBER => $sendData['CREDIT_CARD_NUMBER'],
                \PayUParameters::CREDIT_CARD_EXPIRATION_DATE => $sendData['CREDIT_CARD_EXPIRATION_DATE'],
                \PayUParameters::CREDIT_CARD_SECURITY_CODE=> $sendData['CREDIT_CARD_SECURITY_CODE'],
                \PayUParameters::PAYMENT_METHOD => $sendData['PAYMENT_METHOD'],
                \PayUParameters::INSTALLMENTS_NUMBER => $sendData['INSTALLMENTS_NUMBER'],
            );
            $response = \PayUPayments::doAuthorizationAndCapture($parameters);
            if($response && $response->code == 'SUCCESS')
            {
                if( $response->transactionResponse && $response->transactionResponse->state && ($response->transactionResponse->state == 'PENDING' || $response->transactionResponse->state == 'APPROVED') )
                {
                    $payu = new \stdClass;
                    $payu->state = $response->transactionResponse->state;
                    $payu->orderId = $response->transactionResponse->orderId;
                    $payu->transactionId = $response->transactionResponse->transactionId;
                    if( isset($response->transactionResponse->trazabilityCode) )
                    {
                        $payu->trazabilityCode = $response->transactionResponse->trazabilityCode;
                    }
                    if( isset($response->transactionResponse->authorizationCode) )
                    {
                        $payu->authorizationCode = $response->transactionResponse->authorizationCode;
                    }
                    $onSuccess($payu , $this); 
                } else {
                    $payu = new \stdClass;
                    $payu->message = 'Transaccion Declinada';
                    $payu->state = $response->transactionResponse->state;
                    $payu->responseCode = $response->transactionResponse->responseCode;
                    if( isset($response->transactionResponse->responseMessage) )
                    {
                        $payu->responseMessage = $response->transactionResponse->responseMessage;                    
                    }
                    $onError($payu);
                }
            } else {
                $payu = new \stdClass;
                $payu->message = 'Ocurrio un ERROR';
                $onError($payu);
            }
        } catch (\PayUException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\ConnectionException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\RuntimeException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\InvalidArgumentException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        }
    }



    public function tokenizarCard($sendData , $onSuccess, $onError)
    {
        LaravelPayU::setPayUEnvironment();
        try {
            $parameters = array(
                \PayUParameters::PAYER_NAME => $sendData['PAYER_NAME'],
                \PayUParameters::PAYER_ID => $sendData['PAYER_ID'],
                \PayUParameters::PAYER_DNI => LaravelPayU::getPAYER_DNI(),
                \PayUParameters::CREDIT_CARD_NUMBER => $sendData['CREDIT_CARD_NUMBER'],
                \PayUParameters::CREDIT_CARD_EXPIRATION_DATE => $sendData['CREDIT_CARD_EXPIRATION_DATE'],
                \PayUParameters::PAYMENT_METHOD => $sendData['PAYMENT_METHOD'],
            );
            $response = \PayUTokens::create($parameters);
            if($response && $response->code == 'SUCCESS')
            {
                if( $response->creditCardToken && $response->creditCardToken->creditCardTokenId )
                {
                    $payu = new \stdClass;
                    $payu->state = $response->code;
                    $payu->creditCardTokenId = $response->creditCardToken->creditCardTokenId;
                    $payu->paymentMethod = $response->creditCardToken->paymentMethod;
                    $payu->maskedNumber = $response->creditCardToken->maskedNumber;
                    $onSuccess($payu , $this);
                } else {
                    $payu = new \stdClass;
                    $payu->message = 'No se pudo tokenizar la tarjeta';
                    $onError($payu);
                }
            } else {
                $payu = new \stdClass;
                $payu->message = 'Ocurrio un ERROR';
                $onError($payu);
            }
        } catch (\PayUException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\ConnectionException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\RuntimeException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\InvalidArgumentException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        }
    }

    public function changeWithToken($sendData , $onSuccess, $onError)
    {
        LaravelPayU::setPayUEnvironment();
        try {
            $parameters = array(
                \PayUParameters::ACCOUNT_ID => LaravelPayU::getAccountId(),
                \PayUParameters::REFERENCE_CODE => $sendData['REFERENCE_CODE'],
                \PayUParameters::DESCRIPTION => $sendData['DESCRIPTION'],
                \PayUParameters::VALUE => $sendData['VALUE'],
                \PayUParameters::CURRENCY => $sendData['CURRENCY'],
                \PayUParameters::CURRENCY => $sendData['CURRENCY'],
                \PayUParameters::COUNTRY => $sendData['COUNTRY'],
                \PayUParameters::BUYER_NAME => $sendData['BUYER_NAME'],
                \PayUParameters::PAYER_NAME => $sendData['PAYER_NAME'],
                \PayUParameters::BUYER_EMAIL => LaravelPayU::getBUYER_EMAIL(),
                \PayUParameters::TOKEN_ID => $sendData['TOKEN_ID'],
                \PayUParameters::PAYMENT_METHOD => $sendData['PAYMENT_METHOD'],
                \PayUParameters::INSTALLMENTS_NUMBER => $sendData['INSTALLMENTS_NUMBER'],
            );
            $response = \PayUPayments::doAuthorizationAndCapture($parameters);
            if($response && $response->code == 'SUCCESS')
            {
                if( $response->transactionResponse && $response->transactionResponse->state && ($response->transactionResponse->state == 'PENDING' || $response->transactionResponse->state == 'APPROVED') )
                {
                    $payu = new \stdClass;
                    $payu->state = $response->transactionResponse->state;
                    $payu->orderId = $response->transactionResponse->orderId;
                    $payu->transactionId = $response->transactionResponse->transactionId;
                    if($response->transactionResponse->state == "PENDING")
                    {
                        $payu->pendingReason = $response->transactionResponse->pendingReason;
                    } else {
                        if( isset($response->transactionResponse->trazabilityCode) )
                        {
                            $payu->trazabilityCode = $response->transactionResponse->trazabilityCode;
                        }
                        if( isset($response->transactionResponse->authorizationCode) )
                        {
                            $payu->authorizationCode = $response->transactionResponse->authorizationCode;
                        }
                        if( isset($response->transactionResponse->responseMessage) )
                        {
                            $payu->responseMessage = $response->transactionResponse->responseMessage;
                        }
                    }
                    $payu->responseCode = $response->transactionResponse->responseCode;
                    $onSuccess($payu , $this); 
                } else {
                    $payu = new \stdClass;
                    $payu->message = 'Transaccion Declinada';
                    $onError($payu);
                }
            } else {
                $payu = new \stdClass;
                $payu->message = 'Ocurrio un ERROR';
                $onError($payu);
            }
        } catch (\PayUException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\ConnectionException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\RuntimeException $exc) {
            $onError($exc);
        } catch (\InvalidArgumentException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        }
    }


}