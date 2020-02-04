<?php

namespace TooPago\Payu;

use Exception;

class Searchable
{
    /**
     * Search an order using the id asigned by PayU.
     *
     * @param  callback  $onSuccess
     * @param  callback  $onError
     * @return mixed
     *
     */
    public function searchById($sendData , $onSuccess, $onError)
    {
        LaravelPayU::setPayUEnvironment();
        try {
            $params[\PayUParameters::ORDER_ID] = $sendData['ORDER_ID'];
            $response = \PayUReports::getOrderDetail($params);
            if ($response) {
                $onSuccess($response, $this);
            } else {
                $payu = new \stdClass;
                $payu->message = 'Ocurrio un ERROR';
                $onError($payu);
            }
        } catch (\PayUException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\InvalidArgumentException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        }
    }

    /**
     * Search an order using the reference created before attempt the processing.
     *
     * @param  callback  $onSuccess
     * @param  callback  $onError
     * @return mixed
     *
     */
    public function searchByReference($sendData , $onSuccess, $onError)
    {
        LaravelPayU::setPayUEnvironment();
        try {
            $params[\PayUParameters::REFERENCE_CODE] = $sendData['REFERENCE_CODE'];
            $response = \PayUReports::getOrderDetailByReferenceCode($params);
            if ($response) {
                $onSuccess($response, $this);
            } else {
                $payu = new \stdClass;
                $payu->message = 'Ocurrio un ERROR';
                $onError($payu);
            }
        } catch (\PayUException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\InvalidArgumentException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        }
    }

    /**
     * Search an order using the transactionId asigned by PayU.
     *
     * @param  callback  $onSuccess
     * @param  callback  $onError
     * @return mixed
     *
     */
    public function searchByTransaction($sendData , $onSuccess, $onError)
    {
        LaravelPayU::setPayUEnvironment();
        try {
            $params[\PayUParameters::TRANSACTION_ID] = $sendData['TRANSACTION_ID'];
            $response = \PayUReports::getTransactionResponse($params);
            if ($response) {
                $onSuccess($response, $this);
            } else {
                $payu = new \stdClass;
                $payu->message = 'Ocurrio un ERROR';
                $onError($payu);
            }
        } catch (\PayUException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        } catch (\InvalidArgumentException $exc) {
            $payu = new \stdClass;
            $payu->message = $exc->getMessage();
            $onError($payu);
        }
    }
}
