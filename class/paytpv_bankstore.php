<?php
/**
 * API de PAYTPV para PHP. Métodos BankStore IFRAME/FULLSCREEN/XML/JET
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    PAYTPV
 * @version    1.1.4
 * @author     PAYTPV
 * @license    BSD License (3-clause)
 * @copyright  (c) 2010-2016, PAYTPV
 * @link       https://www.paytpv.com
 */

class Paytpv_Bankstore
{
	private $merchantCode;
	private $terminal;
	private $password;
	private $endpoint;
	private $endpointurl;
	private $jetid;

	public function __construct($arg1, $arg2, $arg3, $arg4 = NULL)
	{
		$this->merchantCode = $arg1;
		$this->terminal = $arg2;
		$this->password = $arg3;
		$this->jetid = $arg4;
		$this->endpoint = 'https://secure.paytpv.com/gateway/xml-bankstore?wsdl';
		$this->endpointurl = 'https://secure.paytpv.com/gateway/ifr-bankstore?';
	}

	/**
	* INTEGRACIÓN BANKSTORE XML --------------------------------------------------->
	*/

	/**
	* Añade una tarjeta a PAYTPV. ¡¡¡ IMPORTANTE !!! Esta entrada directa debe ser activada por PAYTPV.
	* En su defecto el método de entrada de tarjeta para el cumplimiento del PCI-DSS debe ser AddUserUrl o AddUserToken (método utilizado por BankStore JET)
	* @param int $pan Número de tarjeta, sin espacios ni guiones
	* @param string $expdate Fecha de caducidad de la tarjeta, expresada como “mmyy” (mes en dos cifras y año en dos cifras)
	* @param string $cvv Código CVC2 de la tarjeta
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-02
	*/

	public function AddUser($pan, $expdate, $cvv)
	{
		$pan = preg_replace('/\s+/', '', $pan);
		$expdate = preg_replace('/\s+/', '', $expdate);
		$cvv = preg_replace('/\s+/', '', $cvv);
		$signature = sha1($this->merchantCode.$pan.$cvv.$this->terminal.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->add_user($this->merchantCode, $this->terminal, $pan, $expdate, $cvv, $signature, $ip);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Elimina un usuario de PAYTPV mediante llamada soap
	* @param int $idpayuser Id de usuario en PAYTPV
	* @param string $tokenpayuser Token de usuario en PAYTPV
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-02
	*/
	public function RemoveUser($idpayuser, $tokenpayuser)
	{

		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try {
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->remove_user($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $signature, $ip);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Devuelve la información de un usuario almacenada en PAYTPV mediante llamada soap
	* @param int $idpayuser Id del usuario en PAYTPV
	* @param string $tokenpayuser Token del usuario en PAYTPV
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-02
	*/
	public function InfoUser($idpayuser, $tokenpayuser)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->info_user($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $signature, $ip);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Ejecuta un pago por web service
	* @param int $idpayuser Id del usuario en PAYTPV
	* @param string $tokenpayuser Token del usuario en PAYTPV
	* @param string $amount Importe del pago 1€ = 100
	* @param string $transreference Identificador único del pago
	* @param string $currency Identificador de la moneda de la operación
	* @param string $productdescription Descripción del producto
	* @param string $owner Titular de la tarjeta
	* @param integer $scoring (optional) Valor de scoring de riesgo de la transacción
	* @param string $merchant_data (optional) Datos del Comercio
	* @param string $merchant_description (optional) Descriptor del Comercio
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-02
	*/

	public function ExecutePurchase($idpayuser, $tokenpayuser, $amount, $transreference, $currency, $productdescription, $owner, $scoring = null, $merchant_data = null, $merchant_description = null)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$amount.$transreference.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->execute_purchase($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $amount, $transreference, $currency, $signature, $ip, $productdescription, $owner, $scoring, $merchant_data, $merchant_description);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Ejecuta un pago por web service con la operativa DCC
	* @param int $idpayuser Id del usuario en PAYTPV
	* @param string $tokenpayuser Token del usuario en PAYTPV
	* @param string $amount Importe del pago 1€ = 100
	* @param string $transreference Identificador único del pago
	* @param string $productdescription Descripción del producto
	* @param string $owner Titular de la tarjeta
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-07
	*/

	public function ExecutePurchaseDcc($idpayuser, $tokenpayuser, $amount, $transreference, $productdescription = false, $owner = false, $merchant_description = null)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$amount.$transreference.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->execute_purchase_dcc($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $amount, $transreference, $signature, $ip, $productdescription, $owner, $merchant_description);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Confirma un pago por web service con la operativa DCC
	* @param string $transreference Identificador único del pago
	* @param string $dcccurrency Moneda de la transacción elegida. Puede ser la del producto PAYTPV o la nativa seleccionada por el usuario final. El importe será el enviado en execute_purchase_dcc si es el mismo del producto y el convertido en caso de ser diferente.
	* @param string $dccsession Misma sesión enviada en el proceso de execute_purchase_dcc.
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-07
	*/

	public function ConfirmPurchaseDcc($transreference, $dcccurrency, $dccsession)
	{
		$signature = sha1($this->merchantCode.$this->terminal.$transreference.$dcccurrency.$dccsession.$this->password);

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->confirm_purchase_dcc($this->merchantCode, $this->terminal, $transreference, $dcccurrency, $dccsession, $signature);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Ejecuta una devolución de un pago por web service
	* @param int $idpayuser Id del usuario en PAYTPV
	* @param string $tokenpayuser Token del usuario en PAYTPV
	* @param string $transreference Identificador único del pago
	* @param string $currency Identificador de la moneda de la operación
	* @param string $authcode AuthCode de la operación original a devolver
	* @param string $amount Importe del pago 1€ = 100
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-02
	*/

	public function ExecuteRefund($idpayuser, $tokenpayuser, $transreference, $currency, $authcode, $amount = NULL, $merchant_description = NULL)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$authcode.$transreference.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
				$ans = $clientSOAP->execute_refund($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $authcode, $transreference, $currency, $signature, $ip, $amount, $merchant_description);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Crea una suscripción en PAYTPV sobre una tarjeta. ¡¡¡ IMPORTANTE !!! Esta entrada directa debe ser activada por PAYTPV.
	* En su defecto el método de entrada de tarjeta para el cumplimiento del PCI-DSS debe ser CreateSubscriptionUrl o CreateSubscriptionToken
	* @param int $pan Número de tarjeta, sin espacios ni guiones
	* @param string $expdate Fecha de caducidad de la tarjeta, expresada como “mmyy” (mes en dos cifras y año en dos cifras)
	* @param string $cvv Código CVC2 de la tarjeta
	* @param string $startdate Fecha de inicio de la suscripción yyyy-mm-dd
	* @param string $enddate Fecha de fin de la suscripción yyyy-mm-dd
	* @param string $transreference Identificador único del pago
	* @param string $periodicity Periodicidad de la suscripción. Expresado en días.
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $ownerName (optional) Titular de la tarjeta
	* @param integer $scoring (optional) Valor de scoring de riesgo de la transacción
	* @param string $merchant_data (optional) Datos del Comercio
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-07
	*/

	public function CreateSubscription($pan, $expdate, $cvv, $startdate, $enddate, $transreference, $periodicity, $amount, $currency, $ownerName = null, $scoring = null, $merchant_data = null)
	{
		$pan = preg_replace('/\s+/', '', $pan);
		$expdate = preg_replace('/\s+/', '', $expdate);
		$cvv = preg_replace('/\s+/', '', $cvv);
		$signature = sha1($this->merchantCode.$pan.$cvv.$this->terminal.$amount.$currency.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->create_subscription($this->merchantCode, $this->terminal, $pan, $expdate, $cvv, $startdate, $enddate, $transreference, $periodicity, $amount, $currency, $signature, $ip, 1, $ownerName, $scoring, $merchant_data);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Modifica una suscripción en PAYTPV sobre una tarjeta.
	* @param string $idpayuser Identificador único del usuario registrado en el sistema.
	* @param string $tokenpayuser Código token asociado al IDUSER.
	* @param string $startdate Fecha de inicio de la suscripción yyyy-mm-dd
	* @param string $enddate Fecha de fin de la suscripción yyyy-mm-dd
	* @param string $periodicity Periodicidad de la suscripción. Expresado en días.
	* @param string $amount Importe del pago 1€ = 100
	* @param string $execute Si el proceso de alta implica el cobro de la primera cuota el valor de DS_EXECUTE debe ser 1. Si sólo se desea el alta de la subscripción sin el cobro de la primera cuota (se ejecutará con los parámetros enviados) su valor debe ser 0.
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-07
	*/

	public function EditSubscription($idpayuser, $tokenpayuser, $startdate, $enddate, $periodicity, $amount, $execute)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$amount.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->edit_subscription($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $startdate, $enddate, $periodicity, $amount, $signature, $execute, $ip);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Elimina una suscripción en PAYTPV sobre una tarjeta.
	* @param string $idpayuser Identificador único del usuario registrado en el sistema.
	* @param string $tokenpayuser Código token asociado al IDUSER.
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-07
	*/

	public function RemoveSubscription($idpayuser, $tokenpayuser)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->remove_subscription($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $signature, $ip);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Crea una suscripción en PAYTPV sobre una tarjeta tokenizada previamente.
	* @param string $idpayuser Identificador único del usuario registrado en el sistema.
	* @param string $tokenpayuser Código token asociado al IDUSER.
	* @param string $startdate Fecha de inicio de la suscripción yyyy-mm-dd
	* @param string $enddate Fecha de fin de la suscripción yyyy-mm-dd
	* @param string $transreference Identificador único del pago
	* @param string $periodicity Periodicidad de la suscripción. Expresado en días.
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param integer $scoring (optional) Valor de scoring de riesgo de la transacción
	* @param string $merchant_data (optional) Datos del Comercio
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-07
	*/

	public function CreateSubscriptionToken($idpayuser, $tokenpayuser, $startdate, $enddate, $transreference, $periodicity, $amount, $currency, $scoring = null, $merchant_data = null)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$amount.$currency.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->create_subscription_token($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $startdate, $enddate, $transreference, $periodicity, $amount, $currency, $signature, $ip, $scoring, $merchant_data);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Crea una preautorización por web service
	* @param int $idpayuser Id del usuario en PAYTPV
	* @param string $tokenpayuser Token del usuario en PAYTPV
	* @param string $amount Importe del pago 1€ = 100
	* @param string $transreference Identificador único del pago
	* @param string $currency Identificador de la moneda de la operación
	* @param string $productdescription Descripción del producto
	* @param string $owner Titular de la tarjeta
	* @param integer $scoring (optional) Valor de scoring de riesgo de la transacción
	* @param string $merchant_data (optional) Datos del Comercio
	* @param string $merchant_description (optional) Descriptor del Comercio
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-02
	*/

	public function CreatePreauthorization($idpayuser, $tokenpayuser, $amount, $transreference, $currency, $productdescription = false, $owner = false, $scoring = null, $merchant_data = null, $merchant_description = null)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$amount.$transreference.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->create_preauthorization($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $amount, $transreference, $currency, $signature, $ip, $productdescription, $owner, $scoring, $merchant_data, $merchant_description);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Confirma una preautorización por web service previamente enviada
	* @param int $idpayuser Id del usuario en PAYTPV
	* @param string $tokenpayuser Token del usuario en PAYTPV
	* @param string $amount Importe del pago 1€ = 100
	* @param string $transreference Identificador único del pago
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-02
	*/

	public function PreauthorizationConfirm($idpayuser, $tokenpayuser, $amount, $transreference, $merchant_description = null)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$transreference.$amount.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->preauthorization_confirm($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $amount, $transreference, $signature, $ip, $merchant_description);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Cancela una preautorización por web service previamente enviada
	* @param int $idpayuser Id del usuario en PAYTPV
	* @param string $tokenpayuser Token del usuario en PAYTPV
	* @param string $amount Importe del pago 1€ = 100
	* @param string $transreference Identificador único del pago
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-02
	*/

	public function PreauthorizationCancel($idpayuser, $tokenpayuser, $amount, $transreference)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$transreference.$amount.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->preauthorization_cancel($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $amount, $transreference, $signature, $ip);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Confirma una preautorización diferida por web service. Una vez realizada y autorizada una operación de preautorización diferida, puede confirmarse para realizar el cobro efectivo dentro de las 72 horas siguientes; pasada esa fecha, las preautorizaciones diferidas pierden su validez.
	* @param int $idpayuser Id del usuario en PAYTPV
	* @param string $tokenpayuser Token del usuario en PAYTPV
	* @param string $amount Importe del pago 1€ = 100
	* @param string $transreference Identificador único del pago
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-07
	*/

	public function DeferredPreauthorizationConfirm($idpayuser, $tokenpayuser, $amount, $transreference)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$transreference.$amount.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->deferred_preauthorization_confirm($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $amount, $transreference, $signature, $ip);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Cancela una preautorización diferida por web service.
	* @param int $idpayuser Id del usuario en PAYTPV
	* @param string $tokenpayuser Token del usuario en PAYTPV
	* @param string $amount Importe del pago 1€ = 100
	* @param string $transreference Identificador único del pago
	* @return object Objeto de respuesta de la operación
	* @version 2.0 2016-06-07
	*/

	public function DeferredPreauthorizationCancel($idpayuser, $tokenpayuser, $amount, $transreference)
	{
		$signature = sha1($this->merchantCode.$idpayuser.$tokenpayuser.$this->terminal.$transreference.$amount.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->deferred_preauthorization_cancel($this->merchantCode, $this->terminal, $idpayuser, $tokenpayuser, $amount, $transreference, $signature, $ip);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* Ejecuta un pago por web service con el "pago por referencia" de cara a la migración de sistemas a PAYTPV.
	* @param string $amount Importe del pago 1€ = 100
	* @param string $transreference Identificador único del pago
	* @param string $rtoken Referencia original de la tarjeta almacenada en sistema antiguo.
	* @param string $currency Identificador de la moneda de la operación
	* @param string $productdescription Descripción del producto
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-07
	*/

	public function ExecutePurchaseRToken($amount, $transreference, $rtoken, $currency, $productdescription = false, $merchant_description = null)
	{
		$signature = sha1($this->merchantCode.$this->terminal.$amount.$transreference.$rtoken.$this->password);

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->execute_purchase_rtoken($this->merchantCode, $this->terminal, $amount, $transreference, $rtoken, $currency, $signature, $productdescription, $merchant_description);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}


	/**
	* INTEGRACIÓN BANKSTORE JET --------------------------------------------------->
	*/

	/**
	* Añade un usuario por BankStore JET mediante web service
	* @param int $jettoken Token temporal del usuario en PAYTPV
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-02
	*/

	public function AddUserToken($jettoken)
	{
		$signature = sha1($this->merchantCode.$jettoken.$this->jetid.$this->terminal.$this->password);
		$ip	= $_SERVER['REMOTE_ADDR'];

		try{
			$clientSOAP = new SoapClient($this->endpoint);
			$ans = $clientSOAP->add_user_token($this->merchantCode, $this->terminal, $jettoken, $this->jetid, $signature, $ip);
		} catch(SoapFault $e){
			return $this->SendResponse();
		}

		return $this->SendResponse($ans);
	}

	/**
	* INTEGRACIÓN BANKSTORE IFRAME/Fullscreen --------------------------------------------------->
	*/

	/**
	* Devuelve la URL para lanzar un execute_purchase bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único del pago
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $lang Idioma de los literales de la transacción
	* @param string $description Descripción de la operación
	* @param string $secure3d Forzar la operación por 0 = No segura y 1 = Segura mediante 3DSecure
	* @param integer $scoring (optional) Valor de scoring de riesgo de la transacción
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @param string $merchant_data (optional) Datos del Comercio
	* @param string $merchant_description (optional) Descriptor del Comercio
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function ExecutePurchaseUrl($transreference, $amount, $currency, $lang = "ES", $description = false, $secure3d = false, $scoring = null, $urlOk = null, $urlKo = null, $merchant_data = null, $merchant_description = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 1;
		$operation->Reference = $transreference;
		$operation->Amount = $amount;
		$operation->Currency = $currency;
		$operation->Language = $lang;
		$operation->Concept = $description;
		if ($secure3d != false) {
			$operation->Secure3D = $secure3d;
		}
		if ($scoring) {
			$operation->Scoring = (int)$scoring;
		}

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		if ($merchant_data){
			$operation->Merchant_data = $merchant_data;
		}

		if ($merchant_description){
			$operation->Merchant_description = $merchant_description;
		}

		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Devuelve la URL para lanzar un execute_purchase_token bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único del pago
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $iduser Identificador único del usuario registrado en el sistema.
	* @param string $tokenuser Código token asociado al IDUSER.
	* @param string $lang Idioma de los literales de la transacción
	* @param string $description Descripción de la operación
	* @param string $secure3d Forzar la operación por 0 = No segura y 1 = Segura mediante 3DSecure
	* @param integer $scoring (optional) Valor de scoring de riesgo de la transacción
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @param string $merchant_data (optional) Datos del Comercio
	* @param string $merchant_description (optional) Descriptor del Comercio
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function ExecutePurchaseTokenUrl($transreference, $amount, $currency, $iduser, $tokenuser, $lang = "ES", $description = false, $secure3d = false, $scoring = null, $urlOk = null, $urlKo = null, $merchant_data = null, $merchant_description = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 109;
		$operation->Reference = $transreference;
		$operation->Amount = $amount;
		$operation->Currency = $currency;
		$operation->IdUser = $iduser;
		$operation->TokenUser = $tokenuser;
		$operation->Language = $lang;
		$operation->Concept = $description;
		if ($secure3d != false) {
			$operation->Secure3D = $secure3d;
		}
		if ($scoring) {
			$operation->Scoring = (int)$scoring;
		}

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		if ($merchant_data){
			$operation->Merchant_data = $merchant_data;
		}

		if ($merchant_description){
			$operation->Merchant_description = $merchant_description;
		}

		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Devuelve la URL para lanzar un add_user bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único de la transacción
	* @param string $lang Idioma de los literales de la transacción
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function AddUserUrl($transreference, $lang = "ES", $urlOk = null, $urlKo = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 107;
		$operation->Reference = $transreference;
		$operation->Language = $lang;

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Devuelve la URL para lanzar un create_subscription bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único del pago
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $startdate Fecha de inicio de la suscripción yyyymmdd
	* @param string $enddate Fecha de fin de la suscripción yyyymmdd
	* @param string $periodicity Periodicidad de la suscripción. Expresado en días.
	* @param string $lang Idioma de los literales de la transacción
	* @param string $description Descripción de la operación
	* @param string $secure3d Forzar la operación por 0 = No segura y 1 = Segura mediante 3DSecure
	* @param integer $scoring (optional) Valor de scoring de riesgo de la transacción
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @param string $merchant_data (optional) Datos del Comercio
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function CreateSubscriptionUrl($transreference, $amount, $currency, $startdate, $enddate, $periodicity, $lang = "ES", $secure3d = false, $scoring = null, $urlOk = null, $urlKo = null, $merchant_data = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 9;
		$operation->Reference = $transreference;
		$operation->Amount = $amount;
		$operation->Currency = $currency;
		$operation->Language = $lang;
		$operation->StartDate = $startdate;
		$operation->EndDate = $enddate;
		$operation->Periodicity = $periodicity;
		if ($secure3d != false) {
			$operation->Secure3D = $secure3d;
		}
		if ($scoring) {
			$operation->Scoring = (int)$scoring;
		}

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		if ($merchant_data){
			$operation->Merchant_data = $merchant_data;
		}

		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Devuelve la URL para lanzar un create_subscription_token bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único del pago
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $startdate Fecha de inicio de la suscripción yyyymmdd
	* @param string $enddate Fecha de fin de la suscripción yyyymmdd
	* @param string $periodicity Periodicidad de la suscripción. Expresado en días.
	* @param string $iduser Identificador único del usuario registrado en el sistema.
	* @param string $tokenuser Código token asociado al IDUSER.
	* @param string $lang Idioma de los literales de la transacción
	* @param string $secure3d Forzar la operación por 0 = No segura y 1 = Segura mediante 3DSecure
	* @param integer $scoring (optional) Valor de scoring de riesgo de la transacción
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @param string $merchant_data (optional) Datos del Comercio
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function CreateSubscriptionTokenUrl($transreference, $amount, $currency, $startdate, $enddate, $periodicity, $iduser, $tokenuser, $lang = "ES", $secure3d = false, $scoring = null, $urlOk = null, $urlKo = null, $merchant_data = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 110;
		$operation->Reference = $transreference;
		$operation->Amount = $amount;
		$operation->Currency = $currency;
		$operation->Language = $lang;
		$operation->StartDate = $startdate;
		$operation->EndDate = $enddate;
		$operation->Periodicity = $periodicity;
		$operation->IdUser = $iduser;
		$operation->TokenUser = $tokenuser;
		if ($secure3d != false) {
			$operation->Secure3D = $secure3d;
		}
		if ($scoring) {
			$operation->Scoring = (int)$scoring;
		}

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		if ($merchant_data){
			$operation->Merchant_data = $merchant_data;
		}

		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Devuelve la URL para lanzar un create_preauthorization bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único del pago
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $lang Idioma de los literales de la transacción
	* @param string $description Descripción de la operación
	* @param string $secure3d Forzar la operación por 0 = No segura y 1 = Segura mediante 3DSecure
	* @param integer $scoring (optional) Valor de scoring de riesgo de la transacción
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @param string $merchant_data (optional) Datos del Comercio
	* @param string $merchant_description (optional) Descriptor del Comercio
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function CreatePreauthorizationUrl($transreference, $amount, $currency, $lang = "ES", $description = false, $secure3d = false, $scoring = null, $urlOk = null, $urlKo = null, $merchant_data = null, $merchant_description = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 3;
		$operation->Reference = $transreference;
		$operation->Amount = $amount;
		$operation->Currency = $currency;
		$operation->Language = $lang;
		$operation->Concept = $description;
		if ($secure3d != false) {
			$operation->Secure3D = $secure3d;
		}
		if ($scoring) {
			$operation->Scoring = (int)$scoring;
		}

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		if ($merchant_data){
			$operation->Merchant_data = $merchant_data;
		}

		if ($merchant_description){
			$operation->Merchant_description = $merchant_description;
		}


		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Devuelve la URL para lanzar un preauthorization_confirm bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único del pago
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $iduser Identificador único del usuario registrado en el sistema.
	* @param string $tokenuser Código token asociado al IDUSER.
	* @param string $lang Idioma de los literales de la transacción
	* @param string $description Descripción de la operación
	* @param string $secure3d Forzar la operación por 0 = No segura y 1 = Segura mediante 3DSecure
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function PreauthorizationConfirmUrl($transreference, $amount, $currency, $iduser, $tokenuser, $lang = "ES", $description = false, $secure3d = false, $urlOk = null, $urlKo = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 6;
		$operation->Reference = $transreference;
		$operation->Amount = $amount;
		$operation->Currency = $currency;
		$operation->Language = $lang;
		$operation->Concept = $description;
		$operation->IdUser = $iduser;
		$operation->TokenUser = $tokenuser;
		if ($secure3d != false) {
			$operation->Secure3D = $secure3d;
		}

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		$check_user_exist = $this->InfoUser($operation->IdUser, $operation->TokenUser);
		if ($check_user_exist->DS_ERROR_ID != 0) {
			return $this->SendResponse(array("DS_ERROR_ID" => $check_user_exist->DS_ERROR_ID));
		}

		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Devuelve la URL para lanzar un preauthorization_cancel bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único del pago
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $iduser Identificador único del usuario registrado en el sistema.
	* @param string $tokenuser Código token asociado al IDUSER.
	* @param string $lang Idioma de los literales de la transacción
	* @param string $description Descripción de la operación
	* @param string $secure3d Forzar la operación por 0 = No segura y 1 = Segura mediante 3DSecure
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function PreauthorizationCancelUrl($transreference, $amount, $currency, $iduser, $tokenuser, $lang = "ES", $description = false, $secure3d = false, $urlOk = null, $urlKo = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 4;
		$operation->Reference = $transreference;
		$operation->Amount = $amount;
		$operation->Currency = $currency;
		$operation->Language = $lang;
		$operation->Concept = $description;
		$operation->IdUser = $iduser;
		$operation->TokenUser = $tokenuser;
		if ($secure3d != false) {
			$operation->Secure3D = $secure3d;
		}

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		$check_user_exist = $this->InfoUser($operation->IdUser, $operation->TokenUser);
		if ($check_user_exist->DS_ERROR_ID != 0) {
			return $this->SendResponse(array("DS_ERROR_ID" => $check_user_exist->DS_ERROR_ID));
		}

		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Devuelve la URL para lanzar un execute_preauthorization_token bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único del pago
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $iduser Identificador único del usuario registrado en el sistema.
	* @param string $tokenuser Código token asociado al IDUSER.
	* @param string $lang Idioma de los literales de la transacción
	* @param string $description Descripción de la operación
	* @param string $secure3d Forzar la operación por 0 = No segura y 1 = Segura mediante 3DSecure
	* @param integer $scoring (optional) Valor de scoring de riesgo de la transacción
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @param string $merchant_data (optional) Datos del Comercio
	* @param string $merchant_description (optional) Descriptor del Comercio
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function ExecutePreauthorizationTokenUrl($transreference, $amount, $currency, $iduser, $tokenuser, $lang = "ES", $description = false, $secure3d = false, $scoring = null, $urlOk = null, $urlKo = null, $merchant_data = null, $merchant_description = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 111;
		$operation->Reference = $transreference;
		$operation->Amount = $amount;
		$operation->Currency = $currency;
		$operation->Language = $lang;
		$operation->Concept = $description;
		$operation->IdUser = $iduser;
		$operation->TokenUser = $tokenuser;
		if ($secure3d != false) {
			$operation->Secure3D = $secure3d;
		}
		if ($scoring) {
			$operation->Scoring = (int)$scoring;
		}

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		if ($merchant_data){
			$operation->Merchant_data = $merchant_data;
		}

		if ($merchant_description){
			$operation->Merchant_description = $merchant_description;
		}

		$check_user_exist = $this->InfoUser($operation->IdUser, $operation->TokenUser);
		if ($check_user_exist->DS_ERROR_ID != 0) {
			return $this->SendResponse(array("DS_ERROR_ID" => $check_user_exist->DS_ERROR_ID));
		}

		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Devuelve la URL para lanzar un deferred_preauthorization bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único del pago
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $lang Idioma de los literales de la transacción
	* @param string $description Descripción de la operación
	* @param string $secure3d Forzar la operación por 0 = No segura y 1 = Segura mediante 3DSecure
	* @param integer $scoring (optional) Valor de scoring de riesgo de la transacción
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @param string $merchant_data (optional) Datos del Comercio
	* @param string $merchant_description (optional) Descriptor del Comercio
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function DeferredPreauthorizationUrl($transreference, $amount, $currency, $lang = "ES", $description = false, $secure3d = false, $scoring = null, $urlOk = null, $urlKo = null, $merchant_data = null, $merchant_description = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 13;
		$operation->Reference = $transreference;
		$operation->Amount = $amount;
		$operation->Currency = $currency;
		$operation->Language = $lang;
		$operation->Concept = $description;
		if ($secure3d != false) {
			$operation->Secure3D = $secure3d;
		}
		if ($scoring) {
			$operation->Scoring = (int)$scoring;
		}

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		if ($merchant_data){
			$operation->Merchant_data = $merchant_data;
		}

		if ($merchant_description){
			$operation->Merchant_description = $merchant_description;
		}

		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Devuelve la URL para lanzar un deferred_preauthorization_confirm bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único del pago
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $iduser Identificador único del usuario registrado en el sistema.
	* @param string $tokenuser Código token asociado al IDUSER.
	* @param string $lang Idioma de los literales de la transacción
	* @param string $description Descripción de la operación
	* @param string $secure3d Forzar la operación por 0 = No segura y 1 = Segura mediante 3DSecure
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function DeferredPreauthorizationConfirmUrl($transreference, $amount, $currency, $iduser, $tokenuser, $lang = "ES", $description = false, $secure3d = false, $urlOk = null, $urlKo = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 16;
		$operation->Reference = $transreference;
		$operation->Amount = $amount;
		$operation->Currency = $currency;
		$operation->Language = $lang;
		$operation->Concept = $description;
		$operation->IdUser = $iduser;
		$operation->TokenUser = $tokenuser;
		if ($secure3d != false) {
			$operation->Secure3D = $secure3d;
		}

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		$check_user_exist = $this->InfoUser($operation->IdUser, $operation->TokenUser);
		if ($check_user_exist->DS_ERROR_ID != 0) {
			return $this->SendResponse(array("DS_ERROR_ID" => $check_user_exist->DS_ERROR_ID));
		}

		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Devuelve la URL para lanzar un deferred_preauthorization_cancel bajo IFRAME/Fullscreen
	* @param string $transreference Identificador único del pago
	* @param string $amount Importe del pago 1€ = 100
	* @param string $currency Identificador de la moneda de la operación
	* @param string $iduser Identificador único del usuario registrado en el sistema.
	* @param string $tokenuser Código token asociado al IDUSER.
	* @param string $lang Idioma de los literales de la transacción
	* @param string $description Descripción de la operación
	* @param string $secure3d Forzar la operación por 0 = No segura y 1 = Segura mediante 3DSecure
	* @param string $urlOk URL a la que redirigir en caso de éxito.
	* @param string $urlKo URL a la que redirigir en caso de error.
	* @return object Objeto de respuesta de la operación
	* @version 1.0 2016-06-06
	* @version 1.1 2017-11-22 Añadimos parámetros urlOk y urlKo
	*/

	public function DeferredPreauthorizationCancelUrl($transreference, $amount, $currency, $iduser, $tokenuser, $lang = "ES", $description = false, $secure3d = false, $urlOk = null, $urlKo = null)
	{
		$pretest = array();

		$operation = new stdClass();
		$operation->Type = 14;
		$operation->Reference = $transreference;
		$operation->Amount = $amount;
		$operation->Currency = $currency;
		$operation->Language = $lang;
		$operation->Concept = $description;
		$operation->IdUser = $iduser;
		$operation->TokenUser = $tokenuser;
		if ($secure3d != false) {
			$operation->Secure3D = $secure3d;
		}

		if ($urlOk) {
			$operation->UrlOk = $urlOk;
		}

		if ($urlKo) {
			$operation->UrlKo = $urlKo;
		}

		$check_user_exist = $this->InfoUser($operation->IdUser, $operation->TokenUser);
		if ($check_user_exist->DS_ERROR_ID != 0) {
			return $this->SendResponse(array("DS_ERROR_ID" => $check_user_exist->DS_ERROR_ID));
		}

		$operation->Hash = $this->GenerateHash($operation, $operation->Type);
		$lastrequest = $this->ComposeURLParams($operation, $operation->Type);

		$pretest = $this->CheckUrlError($lastrequest);
		$pretest["URL_REDIRECT"] = ($this->endpointurl.$lastrequest);

		return $this->SendResponse($pretest);
	}

	/**
	* Crea una respuesta del servicio PAYTPV BankStore en objeto
	* @param array $respuesta Array de la respuesta a ser convertida a objeto
	* @return object Objeto de respuesta. Se incluye el valor RESULT (OK para correcto y KO incorrecto)
	* @version 1.0 2016-06-03
	*/
	private function SendResponse($respuesta = false)
	{
		$result = new stdClass();
		if (!is_array($respuesta)) {
			$result->RESULT = "KO";
			$result->DS_ERROR_ID = 1011; // No se pudo conectar con el host
		} else {
			$result = (object)$respuesta;
			if ($respuesta["DS_ERROR_ID"] != "" && $respuesta["DS_ERROR_ID"] != 0) {
				$result->RESULT = "KO";
			} else {
				$result->RESULT = "OK";
			}
		}

		return $result;
	}

	/**
	* Genera la firma en función al tipo de operación para BankStore IFRAME/Fullscreen
	* @param object $operationdata Objeto con los datos de la operación para calcular su firma
	* @param int $operationtype Tipo de operación para generar la firma
	* @return string Hash de la firma calculado
	* @version 1.0 2016-06-06
	*/
	private function GenerateHash($operationdata, $operationtype)
	{
		$hash = false;


		if ((int)$operationtype == 1) {				// Authorization (execute_purchase)
			$hash = md5($this->merchantCode.$this->terminal.$operationtype.$operationdata->Reference.$operationdata->Amount.$operationdata->Currency.md5($this->password));
		} elseif ((int)$operationtype == 3) {		// Preauthorization
			$hash = md5($this->merchantCode.$this->terminal.$operationtype.$operationdata->Reference.$operationdata->Amount.$operationdata->Currency.md5($this->password));
		} elseif ((int)$operationtype == 6) {		// Confirmación de Preauthorization
			$hash = md5($this->merchantCode.$operationdata->IdUser.$operationdata->TokenUser.$this->terminal.$operationtype.$operationdata->Reference.$operationdata->Amount.md5($this->password));
		} elseif ((int)$operationtype == 4) {		// Cancelación de Preauthorization
			$hash = md5($this->merchantCode.$operationdata->IdUser.$operationdata->TokenUser.$this->terminal.$operationtype.$operationdata->Reference.$operationdata->Amount.md5($this->password));
		} elseif ((int)$operationtype == 9) {		// Subscription
			$hash = md5($this->merchantCode.$this->terminal.$operationtype.$operationdata->Reference.$operationdata->Amount.$operationdata->Currency.md5($this->password));
		} elseif ((int)$operationtype == 107) {		// Add_user
			$hash = md5($this->merchantCode.$this->terminal.$operationtype.$operationdata->Reference.md5($this->password));
		} elseif ((int)$operationtype == 109) {		// execute_purchase_token
			$hash = md5($this->merchantCode.$operationdata->IdUser.$operationdata->TokenUser.$this->terminal.$operationtype.$operationdata->Reference.$operationdata->Amount.$operationdata->Currency.md5($this->password));
		} elseif ((int)$operationtype == 110) {		// create_subscription_token
			$hash = md5($this->merchantCode.$operationdata->IdUser.$operationdata->TokenUser.$this->terminal.$operationtype.$operationdata->Reference.$operationdata->Amount.$operationdata->Currency.md5($this->password));
		} elseif ((int)$operationtype == 111) {		// create_preauthorization_token
			$hash = md5($this->merchantCode.$operationdata->IdUser.$operationdata->TokenUser.$this->terminal.$operationtype.$operationdata->Reference.$operationdata->Amount.$operationdata->Currency.md5($this->password));
		} elseif ((int)$operationtype == 13) {		// Preauthorization Diferida
			$hash = md5($this->merchantCode.$this->terminal.$operationtype.$operationdata->Reference.$operationdata->Amount.$operationdata->Currency.md5($this->password));
		} elseif ((int)$operationtype == 16) {		// Confirmación de Preauthorization Diferida
			$hash = md5($this->merchantCode.$operationdata->IdUser.$operationdata->TokenUser.$this->terminal.$operationtype.$operationdata->Reference.$operationdata->Amount.md5($this->password));
		} elseif ((int)$operationtype == 14) {		// Cancelación de Preauthorization Diferida
			$hash = md5($this->merchantCode.$operationdata->IdUser.$operationdata->TokenUser.$this->terminal.$operationtype.$operationdata->Reference.$operationdata->Amount.md5($this->password));
		}

		return $hash;
	}

	/**
	* Recibe toda la operación completa y la genera para que llegue por GET en la ENDPOINTURL
	* @param object $operationdata Objeto con los datos de la operación para calcular y generar la URL
	* @param int $operationtype Tipo de operación para generar la petición
	* @return string URL para enviar al ENDPOINTURL
	* @version 1.0 2016-06-06
	* @version 1.1.3 2017-12-20
	*/
	private function ComposeURLParams($operationdata, $operationtype)
	{
		$secureurlhash = false;
		$data = array();

		$data["MERCHANT_MERCHANTCODE"] = $this->merchantCode;
		$data["MERCHANT_TERMINAL"] = $this->terminal;
		$data["OPERATION"] = $operationtype;
		$data["LANGUAGE"] = $operationdata->Language;
		$data["MERCHANT_MERCHANTSIGNATURE"] = $operationdata->Hash;

		if (isset($operationdata->UrlOk)) {
			$data["URLOK"] = $operationdata->UrlOk;
		}

		if (isset($operationdata->UrlKo)) {
			$data["URLKO"] = $operationdata->UrlKo;
		}

		$data["MERCHANT_ORDER"] = $operationdata->Reference;

		if (isset($operationdata->Secure3D) && $operationdata->Secure3D != false) {
			$data["3DSECURE"] = $operationdata->Secure3D;
		}

		if (isset($operationdata->Amount)) {
			$data["MERCHANT_AMOUNT"] = $operationdata->Amount;
		}

		if (isset($operationdata->Concept) && $operationdata->Concept != "") {
			$data["MERCHANT_PRODUCTDESCRIPTION"] = $operationdata->Concept;
		}

		if (isset($operationdata->Merchant_data)) {
			$data["MERCHANT_DATA"] = $operationdata->Merchant_data;
		}

		if (isset($operationdata->Merchant_description)) {
			$data["MERCHANT_MERCHANTDESCRIPTOR"] = $operationdata->Merchant_description;
		}

		if ((int)$operationtype == 1) { // Authorization (execute_purchase)
			$data["MERCHANT_CURRENCY"] = $operationdata->Currency;
			if (isset($operationdata->Scoring)) {
				$data["MERCHANT_SCORING"] = $operationdata->Scoring;
			}
		} elseif ((int)$operationtype == 3) { // Preauthorization
			$data["MERCHANT_CURRENCY"] = $operationdata->Currency;
			if (isset($operationdata->Scoring)) {
				$data["MERCHANT_SCORING"] = $operationdata->Scoring;
			}
		} elseif ((int)$operationtype == 6) { // Confirmación de Preauthorization
			$data["IDUSER"] = $operationdata->IdUser;
			$data["TOKEN_USER"] = $operationdata->TokenUser;
		} elseif ((int)$operationtype == 4) { // Cancelación de Preauthorization
			$data["IDUSER"] = $operationdata->IdUser;
			$data["TOKEN_USER"] = $operationdata->TokenUser;
		} elseif ((int)$operationtype == 9) { // Subscription
			$data["MERCHANT_CURRENCY"] = $operationdata->Currency;
			$data["SUBSCRIPTION_STARTDATE"] = $operationdata->StartDate;
			$data["SUBSCRIPTION_ENDDATE"] = $operationdata->EndDate;
			$data["SUBSCRIPTION_PERIODICITY"] = $operationdata->Periodicity;
			if (isset($operationdata->Scoring)) {
				$data["MERCHANT_SCORING"] = $operationdata->Scoring;
			}
		} elseif ((int)$operationtype == 109) { // execute_purchase_token
			$data["IDUSER"] = $operationdata->IdUser;
			$data["TOKEN_USER"] = $operationdata->TokenUser;
			$data["MERCHANT_CURRENCY"] = $operationdata->Currency;
			if (isset($operationdata->Scoring)) {
				$data["MERCHANT_SCORING"] = $operationdata->Scoring;
			}
		} elseif ((int)$operationtype == 110) { // create_subscription_token
			$data["IDUSER"] = $operationdata->IdUser;
			$data["TOKEN_USER"] = $operationdata->TokenUser;
			$data["MERCHANT_CURRENCY"] = $operationdata->Currency;
			$data["SUBSCRIPTION_STARTDATE"] = $operationdata->StartDate;
			$data["SUBSCRIPTION_ENDDATE"] = $operationdata->EndDate;
			$data["SUBSCRIPTION_PERIODICITY"] = $operationdata->Periodicity;
			if (isset($operationdata->Scoring)) {
				$data["MERCHANT_SCORING"] = $operationdata->Scoring;
			}
		} elseif ((int)$operationtype == 111) { // create_preauthorization_token
			$data["IDUSER"] = $operationdata->IdUser;
			$data["TOKEN_USER"] = $operationdata->TokenUser;
			$data["MERCHANT_CURRENCY"] = $operationdata->Currency;
			if (isset($operationdata->Scoring)) {
				$data["MERCHANT_SCORING"] = $operationdata->Scoring;
			}
		} elseif ((int)$operationtype == 13) { // Deferred Preauthorization
			$data["MERCHANT_CURRENCY"] = $operationdata->Currency;
			if (isset($operationdata->Scoring)) {
				$data["MERCHANT_SCORING"] = $operationdata->Scoring;
			}
		} elseif ((int)$operationtype == 16) { // Deferred Confirmación de Preauthorization
			$data["IDUSER"] = $operationdata->IdUser;
			$data["TOKEN_USER"] = $operationdata->TokenUser;
		} elseif ((int)$operationtype == 14) { // Deferred  Cancelación de Preauthorization
			$data["IDUSER"] = $operationdata->IdUser;
			$data["TOKEN_USER"] = $operationdata->TokenUser;
		}

		$content = "";
		foreach ($data as $key => $value) {
			if ($content != "") {
				$content .= "&";
			}

			$content .= urlencode($key) . "=" . urlencode($value);
		}

		$data["VHASH"] = hash('sha512', md5($content.md5($this->password)));

		$secureurlhash = "";
		foreach ($data as $key => $value) {
			if ($secureurlhash != "") {
				$secureurlhash .= "&";
			}

			$secureurlhash .= urlencode($key) . "=" . urlencode($value);
		}

		return $secureurlhash;
	}

	/**
	* Comprueba si la URL generada con la operativa deseada genera un error
	* @param string $peticion La URL con la petición a PAYTPV.
	* @return array $response Array con la respuesta. Si hay un error devuelve el error que ha generado, si es OK el value DS_ERROR_ID irá a 0.
	* @version 1.0 2016-06-06
	*/
	private function CheckUrlError($urlgen)
	{
		$response = array("DS_ERROR_ID" => 1023);

		if ($urlgen != "") {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->endpointurl.$urlgen);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);

			if($errno = curl_errno($ch)) {
				$response = array("DS_ERROR_ID" => 1021);
			} else {
				if ((strpos($output, "Error: ") == 0 && strpos($output, "Error: ") !== false) || (strpos($output, "<!-- Error: ") == 0 && strpos($output, "<!-- Error: ") !== false)) {
					$response = array("DS_ERROR_ID" => (int)str_replace(array("<!-- Error: ", "Error: ", " -->"), "", $output));
				} else {
					$response = array("DS_ERROR_ID" => 0);
				}
			}

			curl_close($ch);
		}

		return $response;
	}
}
?>