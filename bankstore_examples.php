<?php
/**
* Para incluir los datos necesarios de su producto, es necesario consultar el panel de control del mismo e incluir:
*
*  $merchantCode	= Código de cliente
*  $password		= Contraseña
*  $terminal		= Número de terminal
*
*/
include("class/paycomet_bankstore.php");

$merchantCode	= "";
$password		= "";
$terminal		= "";
$jetid			= NULL; // Opcional si no se utiliza BankStore JET

$paycomet = new Paycomet_Bankstore($merchantCode, $terminal, $password, $jetid);

//------------------------------------------------  métodos por XML  --------------------------------------------------->

$response = $paycomet->AddUserToken("jettoken");
$response = $paycomet->AddUser("credit_card_pan", "credit_card_expdate", "credit_card_cvv");
$response = $paycomet->InfoUser("iduser", "tokenuser");
$response = $paycomet->ExecutePurchase("iduser", "tokenuser", "amount", "operation_reference", "currency");
$response = $paycomet->ExecutePurchaseDcc("iduser", "tokenuser", "amount", "operation_reference");
$response = $paycomet->ConfirmPurchaseDcc("iduser", "dcccurrency", "dccsession");
$response = $paycomet->ExecuteRefund("iduser", "tokenuser", "operation_reference", "currency", "authcode", "amount");
$response = $paycomet->CreateSubscription("credit_card_pan", "credit_card_expdate", "credit_card_cvv", date("Y-m-d"), date("Y-m-d", strtotime("+15 day")), "operation_reference", "periodicity", "amount", "currency");
$response = $paycomet->EditSubscription("iduser", "tokenuser", date("Y-m-d"), date("Y-m-d", strtotime("+15 day")), "periodicity", "amount", "currency", "execute");
$response = $paycomet->RemoveSubscription("iduser", "tokenuser");
$response = $paycomet->CreateSubscriptionToken("iduser", "tokenuser", date("Y-m-d"), date("Y-m-d", strtotime("+15 day")), "operation_reference", "periodicity", "amount", "currency");
$response = $paycomet->CreatePreauthorization("iduser", "tokenuser", "amount", "operation_reference", "currency");
$response = $paycomet->PreauthorizationConfirm("iduser", "tokenuser", "amount", "operation_reference");
$response = $paycomet->PreauthorizationCancel("iduser", "tokenuser", "amount", "operation_reference");
$response = $paycomet->DeferredPreauthorizationConfirm("iduser", "tokenuser", "amount", "operation_reference");
$response = $paycomet->DeferredPreauthorizationCancel("iduser", "tokenuser", "amount", "operation_reference");
$response = $paycomet->ExecutePurchaseRToken("amount", "operation_reference", "reference_token", "currency");
$response = $paycomet->UpdateExpiryDate("iduser", "tokenuser", "expirydate");

//------------------------------------------------  métodos por IFRAME/Fullscreen  ------------------------------------->

$response = $paycomet->ExecutePurchaseUrl("operation_reference", "amount", "currency", "language");
$response = $paycomet->AddUserUrl("operation_reference", "language");
$response = $paycomet->CreateSubscriptionUrl("operation_reference", "amount", "currency", date("Ymd"), date("Ymd", strtotime("+15 day")), "periodicity", "language");
$response = $paycomet->ExecutePurchaseTokenUrl("operation_reference", "amount", "currency", "iduser", "tokenuser", "language");
$response = $paycomet->CreateSubscriptionTokenUrl("operation_reference", "amount", "currency", date("Ymd"), date("Ymd", strtotime("+15 day")), "periodicity", "iduser", "tokenuser", "language");
$response = $paycomet->CreatePreauthorizationUrl("operation_reference", "amount", "currency", "language");
$response = $paycomet->PreauthorizationConfirmUrl("operation_reference", "amount", "currency", "iduser", "tokenuser");
$response = $paycomet->PreauthorizationCancelUrl("operation_reference", "amount", "currency", "iduser", "tokenuser");
$response = $paycomet->ExecutePreauthorizationTokenUrl("operation_reference", "amount", "currency", "iduser", "tokenuser");
$response = $paycomet->DeferredPreauthorizationUrl("operation_reference", "amount", "currency");
$response = $paycomet->DeferredPreauthorizationConfirmUrl("operation_reference", "amount", "currency", "iduser", "tokenuser");
$response = $paycomet->DeferredPreauthorizationCancelUrl("operation_reference", "amount", "currency", "iduser", "tokenuser");
$response = $paycomet->ExecutePurchaseRTokenUrl("operation_reference", "amount", "currency", "identifier", "group");

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