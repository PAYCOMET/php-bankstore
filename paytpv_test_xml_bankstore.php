<?php
/* Tracking ID:	XB9-2TE-PRGE */

date_default_timezone_set("Europe/Madrid");

/**
* Para incluir los datos necesarios de su producto, es necesario consultar el panel de control del mismo e incluir:
*
*  $merchantCode	= Código de cliente
*  $password		= Contraseña
*  $terminal		= Número de terminal
*
* En este test se incluye la generación de una referencia aleatoria. Deberá cambiarse por la referencia interna correspondiente. Debe ser única.
*
* Documentación en : XML_BankStore_vX.X_Integration_es.pdf
*
*/

	$endPoint		= 'https://secure.paytpv.com/gateway/xml_bankstore.php?wsdl';

	//-------------- Datos de integración del terminal ------------------>
	//
	$merchantCode	= '';  // Incluir Código de cliente
	$terminal		= '';  // Incluir Terminal
	$password		= '';  // Incluir Password


//-----------------------------------------------------------------------------------------------------------------  add_user  ------------------------------------->

	$creditCard	= '';  // Incluir una tarjeta válida
	$expiryDate	= '';  // Incluir una fecha de caducidad válida (mmyy)
	$cvv2		= '';  // Incluir un CVC2 válido

	$signature	= sha1($merchantCode.$creditCard.$cvv2.$terminal.$password);

	$ip			= $_SERVER['REMOTE_ADDR'];	// Incluir IP desde donde se realiza la compra

	try{
		$clientSOAP = new SoapClient($endPoint);

		$addUserResponse = $clientSOAP->add_user($merchantCode, $terminal, $creditCard, $expiryDate, $cvv2, $signature, $ip);

		var_dump($addUserResponse);

	// La respuesta obtenida sin error (DS_ERROR_ID=0), devuelve un DS_USER y un DS_TOKEN_USER que debe utilizarse para el resto de métodos.
	//array (size=3)
	//	'DS_IDUSER' => string '99999' (length=4)
	//	'DS_TOKEN_USER' => string '_TokendePrueba_' (length=15)
	//	'DS_ERROR_ID' => string '0' (length=1)
	//

	} catch(SoapFault $e){
		var_dump($e);
	}

	die();


//-----------------------------------------------------------------------------------------------------------------  info_user  ------------------------------------->

	/*
	$addUserResponseIdUser		= '';  // Incluir un DS_IDUSER obtenido mediante add_user
	$addUserResponseTokenUser	= '';  // Incluir un DS_TOKEN_USER obtenido mediante add_user


	$idUser						= $addUserResponseIdUser;
	$tokenUser					= $addUserResponseTokenUser;
	$signature					= sha1($merchantCode.$idUser.$tokenUser.$terminal.$password);
	$ip							= $_SERVER['REMOTE_ADDR'];	// Incluir IP desde donde se realiza la compra

	try{
		$clientSOAP = new SoapClient($endPoint);

		$infoUserResponse = $clientSOAP->info_user($merchantCode, $terminal, $idUser, $tokenUser, $signature, $ip);

		var_dump($infoUserResponse);

		// Si el usuario sigue activo y la tarjeta se encuentra, se obtiene la siguiente información
		//array (size=5)
		//	'DS_MERCHANT_PAN' => string 'XXXX-XXXX-XXXX-9227' (length=19)
		//	'DS_ERROR_ID' => null
		//	'DS_CARD_BRAND' => string 'DISCOVER' (length=8)
		//	'DS_CARD_TYPE' => string 'CREDIT' (length=6)
		//	'DS_CARD_I_COUNTRY_ISO3' => string 'USA' (length=3)

	} catch(SoapFault $e){
		var_dump($e);
	}

	die();
	*/

//-----------------------------------------------------------------------------------------------------------------  execute_purchase  ------------------------------------->

	/*
	$addUserResponseIdUser		= '';  // Incluir un DS_IDUSER obtenido mediante add_user
	$addUserResponseTokenUser	= '';  // Incluir un DS_TOKEN_USER obtenido mediante add_user


	$idUser						= $addUserResponseIdUser;
	$tokenUser					= $addUserResponseTokenUser;
	$amount						= '';  // Incluir Cantidad de la transacción. Formato entero 2 decimales: 4,50 EUROS = 450
	$order						= date("YmdHis").rand(20, 40); // Incluir Referencia. Debe ser única
	$currency					= "EUR";
	$signature					= sha1($merchantCode.$idUser.$tokenUser.$terminal.$amount.$order.$password);
	$ip							= $_SERVER['REMOTE_ADDR'];	// Incluir IP desde donde se realiza la compra
	$productDescription			= "XML_BANKSTORE TEST productDescription";
	$owner						= "XML_BANKSTORE TEST owner";

	try{
		$clientSOAP = new SoapClient($endPoint);

		$executePurchaseResponse = $clientSOAP->execute_purchase($merchantCode, $terminal, $idUser, $tokenUser, $amount, $order, $currency, $signature, $ip, $productDescription, $owner);

		var_dump($executePurchaseResponse);

		// Ejemplo respuesta obtenida sin error (DS_RESPONSE=1), devuelve un DS_MERCHANT_AUTHCODE que es el código único de identificación de la autorización
		//array (size=7)
		//	'DS_MERCHANT_AMOUNT' => int 202
		//	'DS_MERCHANT_ORDER' => string '2014070313454732' (length=16)
		//	'DS_MERCHANT_CURRENCY' => string 'EUR' (length=3)
		//	'DS_MERCHANT_AUTHCODE' => string '277315/495415196568105148576714985885' (length=37)
		//	'DS_MERCHANT_CARDCOUNTRY' => null
		//	'DS_RESPONSE' => int 1
		//	'DS_ERROR_ID' => null

	} catch(SoapFault $e){
		var_dump($e);
	}

	die();
	*/

//-----------------------------------------------------------------------------------------------------------------  remove_user  ------------------------------------->

	/*
	$addUserResponseIdUser		= '';  // Incluir un DS_IDUSER obtenido mediante add_user
	$addUserResponseTokenUser	= '';  // Incluir un DS_TOKEN_USER obtenido mediante add_user


	$idUser						= $addUserResponseIdUser;
	$tokenUser					= $addUserResponseTokenUser;
	$signature					= sha1($merchantCode.$idUser.$tokenUser.$terminal.$password);
	$ip							= $_SERVER['REMOTE_ADDR'];	// Incluir IP desde donde se realiza la compra

	try{
		$clientSOAP = new SoapClient($endPoint);

		$removeUserResponse = $clientSOAP->remove_user($merchantCode, $terminal, $idUser, $tokenUser, $signature, $ip);

		var_dump($removeUserResponse);

		// Si el usuario ha sido eliminado correctamente se obtiene la siguiente respuesta
		//array (size=2)
		//	'DS_RESPONSE' => int 1
		//	'DS_ERROR_ID' => null

	} catch(SoapFault $e){
		var_dump($e);
	}

	die();
	*/



?>