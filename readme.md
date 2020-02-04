# LaravelPayU

[![N|Solid](https://lomeli.io/assets/img/logo.png)](https://lomeli.io)



## Introducción
LaravelPayU provee una interfaz sencilla para utilizar el sdk de PayU en proyectos que tienen como base el framework [*Laravel*](https://laravel.com).

Testeado correctamente en la version 6.14 de [*Laravel*](https://laravel.com).

Este proyecto hace uso del [sdk de Payu](http://developers.payulatam.com/es/sdk/), pero no es un proyecto oficial de PayU.

## Instalación y configuración

Instalar el paquete mediante composer:

```bash
composer require toopago/payu
```

Luego incluir el ServiceProvider en el arreglo de providers en *config/app.php*

```bash
TooPago\Payu\LaravelPayUServiceProvider::class,
```

Publicar la configuración para incluir la informacion de la cuenta de PayU:

```bash
php artisan vendor:publish 
```


Incluir la informacion de la cuenta y ajustes en el archivo *.env* ó directamente en
el archivo de configuración *config/payu.php*

```bash
APP_ENV=local

PAYU_ON_TESTING=true

PAYU_MERCHANT_ID=your-merchant-id

PAYU_API_LOGIN=your-api-login

PAYU_API_KEY=your-api-key

PAYU_ACCOUNT_ID=your-account-id

PAYU_COUNTRY=your-country-ref: AR/BR/CO/CL/MX/PA/PE/US

PSE_REDIRECT_URL=your-pse-redirect-url

PAYU_BUYER_EMAIL="developer@toopago.io"

PAYU_PAYER_NAME="TooPago"

PAYU_PAYER_DNI=xxxxxxxx

```

## Uso del API

Esta versión contiene solo una interfaz para pagos en efectivo , pagos con tarjetas de credito , pagos con tokenización y consultas.
Si necesita usar pagos recurrentes o algo mas debe usar el sdk de PayU directamente.

### Ping

Para consultar la disponibilidad de la plataforma se puede usar el método doPing en el controlador
designado:

```php
<?php

namespace App\Http\Controllers;

use TooPago\Payu\LaravelPayU;

class PaymentsController extends Controller
{
    LaravelPayU::doPing(function($response) {
        $code = $response->code;
        // ... revisar el codigo de respuesta
    }, function($error) {
     // ... Manejo de errores PayUException
    });

```



### Pagos con Tarjetas de Credito

Permite el pago con VISA||MASTERCARD||AMEX de la siguiente manera:

En este metodo se requiere mandar un array con los datos de la tarjeta , cantidad , pais y moneda


```php
<?php

namespace App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TooPago\Payu\Payments;

class MyController extends Controller
{

    public function createChargeCard()
    {
        //PAYER_NAME: APPROVED||PENDING||REJECTED
        //PAYMENT_METHOD : VISA||MASTERCARD||AMEX
        //CREDIT_CARD_EXPIRATION_DATE : YYYY/MM
        //INSTALLMENTS_NUMBER : 1
        $sendData = [
            'REFERENCE_CODE' => 'referencia_' . uniqid(),
            'DESCRIPTION' => 'Deposito de $100',
            'VALUE' => 100,
            'BUYER_NAME' => 'Juan Lopez',
            'PAYER_NAME' => 'PENDING',
            'CREDIT_CARD_NUMBER' => '5491234534511478',
            'CREDIT_CARD_EXPIRATION_DATE' => '2023/05',
            'CREDIT_CARD_SECURITY_CODE' => '897',
            'PAYMENT_METHOD' => 'MASTERCARD',
            'INSTALLMENTS_NUMBER' => '1',
            'CURRENCY' => 'MXN',
            'COUNTRY' => 'MX',
        ];
        $Payu = new Payments();
        $Payu->CreatePaymentCash($sendData , function($response) {
            //**Respuesta Exitosa pero Pendiente**
            //$response->state
            //$response->orderId
            //$response->transactionId
            
            //**Respuesta Exitosa**
            //$response->state
            //$response->orderId
            //$response->transactionId
            //$response->trazabilityCode
            //$response->authorizationCode
        }, function($error) {
            //**Respuesta Erronea**
            //$error->state
            //$error->message
            //$error->responseCode //Opcional
            //$error->responseMessage //Opcional
        });
    }
    
}

```


**Nota:** Todos los campos son obligatorios. El campo PAYER_NAME debe llevar el nombre del pagador en producción , si es pruebas , puede ir cualquier estado de los de arriba

### Pagos en Efectivo

Permite el pago con OXXO||SEVEN_ELEVEN||OTHERS_CASH_MX de la siguiente manera:
En este metodo se requiere mandar un array con los datos del comprador , cantidad , pais y moneda

```php
<?php

namespace App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TooPago\Payu\Payments;


class MyController extends Controller
{

    public function createChargeCash()
    {
        //PAYMENT_METHOD: OXXO||SEVEN_ELEVEN||OTHERS_CASH_MX
        $sendData = [
            'REFERENCE_CODE' => 'referencia_' . uniqid(),
            'DESCRIPTION' => 'Deposito de $100',
            'VALUE' => 100,
            'PAYMENT_METHOD' => 'OXXO',
            'BUYER_NAME' => 'Mi Nombre',
            'PAYER_NAME' => 'Mi Nombre',
            'CURRENCY' => 'MXN',
            'COUNTRY' => 'MX',
        ];
        $Payu = new Payments();
        $Payu->CreatePaymentCard($sendData , function($response) {
            //**Respuesta Exitosa pero Pendiente de Pago**
            //$response->state
            //$response->orderId
            //$response->transactionId
            //$response->EXPIRATION_DATE
            //$response->BAR_CODE
            //$response->REFERENCE
            //$response->URL_PAYMENT_RECEIPT_PDF
            //$response->URL_PAYMENT_RECEIPT_HTML
            //$response->PAYMENT_WAY_ID
            //$response->BANK_REFERENCED_CODE
        }, function($error) {
            //**Respuesta Erronea**
            //$error->message
        });
    }
    
}

```

### Tokenizar una tarjeta de Credito

Permite tokenisar una tarjeta VISA||MASTERCARD||AMEX de la siguiente manera:

En este metodo se requiere mandar un array con los datos de la tarjeta , cantidad , pais y moneda

```php
<?php

namespace App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TooPago\Payu\Payments;


class MyController extends Controller
{

    public function tokenizarCard()
    {
        //PAYER_NAME: APPROVED||PENDING||REJECTED
        //PAYMENT_METHOD : VISA||MASTERCARD||AMEX
        //CREDIT_CARD_EXPIRATION_DATE : YYYY/MM
        $sendData = [
            'PAYER_NAME' => 'APPROVED',
            'PAYER_ID' => '1518', // id del usuario
            'CREDIT_CARD_NUMBER' => '5491234534511478',
            'CREDIT_CARD_EXPIRATION_DATE' => '2023/05',
            'PAYMENT_METHOD' => 'MASTERCARD',
        ];        
        $Payu = new Payments();
        $Payu->tokenizarCard($sendData , function($response) {
            //**Respuesta Exitosa**
            //$response->state
            //$response->creditCardTokenId
            //$response->paymentMethod
            //$response->maskedNumber
        }, function($error) {
            //**Respuesta Erronea**
            //$error->message
        });
    }
    
}

```

**Nota:** Todos los campos son obligatorios. El campo PAYER_NAME debe llevar el nombre del pagador en producción , si es pruebas , puede ir cualquier estado de los de arriba


### Crear un cargo con un token de tarjeta

Permite tokenisar una tarjeta VISA||MASTERCARD||AMEX de la siguiente manera:
En este metodo se requiere mandar un array con los datos del usuario , token , cantidad , pais y moneda

```php
<?php

namespace App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TooPago\Payu\Payments;


class MyController extends Controller
{

    public function changeWithToken()
    {
        //PAYER_NAME: APPROVED||PENDING||REJECTED
        //PAYMENT_METHOD : VISA||MASTERCARD||AMEX
        //CREDIT_CARD_EXPIRATION_DATE : YYYY/MM
        //INSTALLMENTS_NUMBER : 1
        $sendData = [
            'REFERENCE_CODE' => 'referencia_' . uniqid(),
            'DESCRIPTION' => 'Deposito de $100',
            'VALUE' => 100,
            'BUYER_NAME' => 'Juan Lopez',
            'PAYER_NAME' => 'APPROVED',
            'TOKEN_ID' => '759497b1-ab49-4145-b652-0ad581a9f612', // id token tarjeta
            'PAYMENT_METHOD' => 'MASTERCARD',
            'INSTALLMENTS_NUMBER' => '1',
            'CURRENCY' => 'MXN',
            'COUNTRY' => 'MX',
        ];        
        $Payu = new Payments();
        $Payu->tokenizarCard($sendData , function($response) {
            //**Respuesta Exitosa**
            //$response->state
            //$response->orderId
            //$response->transactionId}
            
            //Si el estatus es pendiente
            //$response->pendingReason
            
            //Si el estatus es exitoso
            //$response->trazabilityCode // opcional
            //$response->authorizationCode // opcional
            //$response->responseMessage // opcional
            //$response->responseCode // opcional

        }, function($error) {
            //**Respuesta Erronea**
            //$error->message
        });
    }
    
}

```

**Nota:** Todos los campos son obligatorios. El campo PAYER_NAME debe llevar el nombre del pagador en producción , si es pruebas , puede ir cualquier estado de los de arriba



### Consultas

**Para las consultas por ORDER_ID es de la siguiente manera:** 

```php
<?php

namespace App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TooPago\Payu\Searchable;


class MyController extends Controller
{

    public function search()
    {
        $sendData = [
            'ORDER_ID' => 'xxxxx' //ID de la orden
        ];        
        $Payu = new Searchable();
        $Payu->searchById($sendData , function($response) {
            //**Respuesta Exitosa**
        }, function($error) {
            //**Respuesta Erronea**
            //$error->message
        });
    }
    
}

```

**Para las consultas por REFERENCE_CODE es de la siguiente manera:** 


```php
<?php

namespace App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TooPago\Payu\Searchable;


class MyController extends Controller
{

    public function search()
    {
        $sendData = [
            'REFERENCE_CODE' => 'xxxxx' //REFERENCE_CODE de la orden
        ];        
        $Payu = new Searchable();
        $Payu->searchByReference($sendData , function($response) {
            //**Respuesta Exitosa**
        }, function($error) {
            //**Respuesta Erronea**
            //$error->message
        });
    }
    
}

```

**Para las consultas por TRANSACTION_ID es de la siguiente manera:** 

```php
<?php

namespace App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TooPago\Payu\Searchable;


class MyController extends Controller
{

    public function search()
    {
        $sendData = [
            'TRANSACTION_ID' => 'xxxxx' //TRANSACTION_ID de la orden
        ];        
        $Payu = new Searchable();
        $Payu->searchByTransaction($sendData , function($response) {
            //**Respuesta Exitosa**
        }, function($error) {
            //**Respuesta Erronea**
            //$error->message
        });
    }
    
}

```




## Pruebas
Instalar las dependencias del paquete.

Configurar el archivo phpunit.xml las variables de .env con la configuracion de pruebas de tu cuenta.

Ver información en [sitio de PayU](http://developers.payulatam.com/es/sdk/sandbox.html) y luego si ejecutar las pruebas:

```bash
phpunit
```

## Errores y contribuciones

Para un error escribir directamente el problema en github issues o enviarlo
al correo miguel@lomeli.io. Si desea contribuir con el proyecto por favor enviar los ajustes siguiendo la guía de contribuciones:

- Usar las recomendaciones de estilos [psr-1](http://www.php-fig.org/psr/psr-1/) y [psr-2](http://www.php-fig.org/psr/psr-2/)

- Preferiblemente escribir código que favorezca el uso de Laravel

- Escribir las pruebas y revisar el código antes de hacer un pull request
