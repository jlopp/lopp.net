<?php

// set the following two values and the rest of the callback script should work
const BTCPAY_IP_ADDRESS = "";
const YOUR_EMAIL_ADDRESS = "";

if ($_SERVER['REQUEST_METHOD'] != 'POST' || $_SERVER['REMOTE_ADDR'] != BTCPAY_IP_ADDRESS) {
  http_response_code(401);
  exit;
}

$invoice = file_get_contents("php://input");

// Send email, otherwise ignore until payment is confirmed
if ($invoice->status == "complete") {
  // read the JSON encoded data from the relevant file
  $orderData = file_get_contents("./messages/" . $invoice->orderId);
  if (!$orderData) {
    $message = "Could not find message file for invoice " . $invoice->id . ", order " . $invoice->orderId;
    $headers = "From: contact@lopp.net\r\nReply-to: contact@lopp.net";
    mail(YOUR_EMAIL_ADDRESS, "Paid Message ERROR", $message, $headers);
  } else {
    $message = json_decode($orderData);
    $headers = "From: " . $message->email . "\r\nReply-to: " . $message->email;
    mail(YOUR_EMAIL_ADDRESS, "Paid Message: " . $message->subject, $message->emailBody, $headers);
  }
}
http_response_code(200);
?>