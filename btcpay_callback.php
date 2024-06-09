<?php

// set the following values and the rest of the callback script should work
const BTCPAY_IP_ADDRESS = ""; // external IP of your BTCPay server, used as sanity check / spam prevention
const YOUR_EMAIL_ADDRESS = "";
const FROM_EMAIL_ADDRESS = "paidform@yourdomain.com";
$headers = "From: " . FROM_EMAIL_ADDRESS . "\r\nReply-to: " . FROM_EMAIL_ADDRESS;

if ($_SERVER['REQUEST_METHOD'] != 'POST' || $_SERVER['REMOTE_ADDR'] != BTCPAY_IP_ADDRESS) {
  http_response_code(401);
  exit;
}

$invoice = json_decode(file_get_contents("php://input"));
// read the JSON encoded data from the relevant file
$orderData = file_get_contents("./messages/" . $invoice->orderId);

// ignore expired and unconfirmed invoices
if ($invoice->status == "expired" || $invoice->status == "paid") {
  http_response_code(200);
  exit;
}

// Send email, otherwise ignore until payment is confirmed
if ($invoice->status == "complete" || $invoice->status == "confirmed") {
  if (!$orderData) {
    $message = "Could not find message file for invoice " . $invoice->id . ", order " . $invoice->orderId;
    mail(YOUR_EMAIL_ADDRESS, "Paid Message ERROR", $message, $headers);
  } else {
    $message = json_decode($orderData);
    $headers = "From: " . FROM_EMAIL_ADDRESS . "\r\nReply-to: " . $message->email;
    mail(YOUR_EMAIL_ADDRESS, "Paid Message: " . $message->subject, $message->emailBody, $headers);
  }
} else if (!$orderData) {
  $message = "Could not find message file for invoice " . print_r($invoice, true);
  mail(YOUR_EMAIL_ADDRESS, "Paid Message ERROR. invoice status: " . $invoice->status, $message, $headers);
} else {
  mail(YOUR_EMAIL_ADDRESS, "Paid Message ERROR status ($invoice->status)", $message, $headers);
}
http_response_code(200);
?>