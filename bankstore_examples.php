<?php
/**
* Para incluir los datos necesarios de su producto, es necesario consultar el panel de control del mismo e incluir:
*
*  $merchantCode	= Código de cliente
*  $password		= Contraseña
*  $terminal		= Número de terminal
*
*/
include("class/paytpv_bankstore.php");

$merchantCode	= "";
$password		= "";
$terminal		= "";
$jetid			= NULL; // Opcional si no se utiliza BankStore JET

$paytpv = new Paytpv_Bankstore($merchantCode, $terminal, $password, $jetid);

//------------------------------------------------  métodos por XML  --------------------------------------------------->

$response = $paytpv->AddUserToken("jettoken");
$response = $paytpv->AddUser("credit_card_pan", "credit_card_expdate", "credit_card_cvv");
$response = $paytpv->InfoUser("iduser", "tokenuser");
$response = $paytpv->ExecutePurchase("iduser", "tokenuser", "amount", "operation_reference", "currency");
$response = $paytpv->ExecutePurchaseDcc("iduser", "tokenuser", "amount", "operation_reference");
$response = $paytpv->ConfirmPurchaseDcc("iduser", "dcccurrency", "dccsession");
$response = $paytpv->ExecuteRefund("iduser", "tokenuser", "operation_reference", "currency", "authcode", "amount");
$response = $paytpv->CreateSubscription("credit_card_pan", "credit_card_expdate", "credit_card_cvv", date("Y-m-d"), date("Y-m-d", strtotime("+15 day")), "operation_reference", "periodicity", "amount", "currency");
$response = $paytpv->EditSubscription("iduser", "tokenuser", date("Y-m-d"), date("Y-m-d", strtotime("+15 day")), "periodicity", "amount", "currency", "execute");
$response = $paytpv->RemoveSubscription("iduser", "tokenuser");
$response = $paytpv->CreateSubscriptionToken("iduser", "tokenuser", date("Y-m-d"), date("Y-m-d", strtotime("+15 day")), "operation_reference", "periodicity", "amount", "currency");
$response = $paytpv->CreatePreauthorization("iduser", "tokenuser", "amount", "operation_reference", "currency");
$response = $paytpv->PreauthorizationConfirm("iduser", "tokenuser", "amount", "operation_reference");
$response = $paytpv->PreauthorizationCancel("iduser", "tokenuser", "amount", "operation_reference");
$response = $paytpv->DeferredPreauthorizationConfirm("iduser", "tokenuser", "amount", "operation_reference");
$response = $paytpv->DeferredPreauthorizationCancel("iduser", "tokenuser", "amount", "operation_reference");

//------------------------------------------------  métodos por IFRAME/Fullscreen  ------------------------------------->

$response = $paytpv->ExecutePurchaseUrl("operation_reference", "amount", "currency", "language");
$response = $paytpv->AddUserUrl("operation_reference", "language");
$response = $paytpv->CreateSubscriptionUrl("operation_reference", "amount", "currency", date("Ymd"), date("Ymd", strtotime("+15 day")), "periodicity", "language");
$response = $paytpv->ExecutePurchaseTokenUrl("operation_reference", "amount", "currency", "iduser", "tokenuser", "language");
$response = $paytpv->CreateSubscriptionTokenUrl("operation_reference", "amount", "currency", date("Ymd"), date("Ymd", strtotime("+15 day")), "periodicity", "iduser", "tokenuser", "language");
$response = $paytpv->CreatePreauthorizationUrl("operation_reference", "amount", "currency", "language");
$response = $paytpv->PreauthorizationConfirmUrl("operation_reference", "amount", "currency", "iduser", "tokenuser");
$response = $paytpv->PreauthorizationCancelUrl("operation_reference", "amount", "currency", "iduser", "tokenuser");
$response = $paytpv->ExecutePreauthorizationTokenUrl("operation_reference", "amount", "currency", "iduser", "tokenuser");
$response = $paytpv->DeferredPreauthorizationUrl("operation_reference", "amount", "currency");
$response = $paytpv->DeferredPreauthorizationConfirmUrl("operation_reference", "amount", "currency", "iduser", "tokenuser");
$response = $paytpv->DeferredPreauthorizationCancelUrl("operation_reference", "amount", "currency", "iduser", "tokenuser");

if ($response->RESULT == "KO") {
	var_dump($response);
} else {
	if (isset($response->URL_REDIRECT)) {
		header("Location: ".$response->URL_REDIRECT);
		die();
	}
	var_dump($response);
}
?>