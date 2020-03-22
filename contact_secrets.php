<?php

// set the following two values and the rest of the contact script should work
const HASHCASH_PRIVATE_KEY = "";
const YOUR_EMAIL_ADDRESS = "";
const BTCPAY_STORE_ID = "";
const BTCPAY_CALLBACK_URL = "";
const SUCCESS_URL = "";
const BTCPAY_INVOICE_API_URL = "";

$nameErr = $emailErr = $subjectErr = $messageErr = "";
$name = $email = $subject = $emailBody = "";
$formType = $_POST["formType"];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
  } else {
    $name = test_input($_POST["name"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
      $nameErr = "Only letters and white space allowed";
    }
  }
  
  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $email = test_input($_POST["email"]);
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format";
    }
  }
    
  if (empty($_POST["subject"])) {
    $subjectErr = "Subject is required";
  } else {
    $subject = test_input($_POST["subject"]);
  }
  
  if (empty($_POST["emailBody"])) {
    $messageErr = "Message is required";
  } else {
    $emailBody = test_input($_POST["emailBody"]);
  }

  if ($formType == "free" && !$_REQUEST['hashcashid']) {
    $captchaErr = 'Please unlock the submit button!';
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function generateOrderId() {
  return substr(str_shuffle(str_repeat($x='abcdefghjkmnpqrstuvwxyz23456789', ceil(20/strlen($x)) )),1,20);
}

// Check conditions for submission of free form
if($formType == "free" && isset($_POST['submit']) && isset($_POST['hashcashid']) && (!empty($_POST["name"])) && (preg_match("/^[a-zA-Z ]*$/",$name)) && (!empty($_POST["email"])) &&  (filter_var($email, FILTER_VALIDATE_EMAIL)) && (!empty($_POST["subject"])) && (!empty($_POST["emailBody"]))) {
    // validate that the client performed the proof of work for the captcha
    $url = 'https://hashcash.io/api/checkwork/' . $_POST['hashcashid'] . '?apikey=' . HASHCASH_PRIVATE_KEY;
    $response = json_decode(file_get_contents($url));

    if (!$response) {
      $captchaErr = 'Something went wrong; please try again.';
    } else if ($response->verified) {
      $captchaErr = 'This proof of work was already used. Nice try!';
    } else if ($response->totalDone < 0.1) {
      $captchaErr = 'Failed to complete enough proof of work for form CAPTCHA. Nice try!';
    } else {
      // All good
      $name = $_POST['name'];
      $subject = $_POST['subject'];
      $emailBody = $name . " from email: " . $email . " wrote the following:" . "\n\n" . $_POST['emailBody'];
      $headers = "From: $email\r\nReply-to: $email";
      mail(YOUR_EMAIL_ADDRESS, $subject, $emailBody, $headers);
      echo '<META HTTP-EQUIV="Refresh" Content="0; URL=thank_you.html">';
      exit;
    }
  }

// Check conditions for submission of paid form
if($formType == "paid" && isset($_POST['submit']) && (!empty($_POST["name"])) && (preg_match("/^[a-zA-Z ]*$/",$name)) && (!empty($_POST["email"])) &&  (filter_var($email, FILTER_VALIDATE_EMAIL)) && (!empty($_POST["subject"])) && (!empty($_POST["emailBody"]))) {
    $orderId = generateOrderId();
    $price = "100";
    $currency = "USD";
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $emailBody = $name . " from email: " . $email . " wrote the following:" . "\n\n" . $_POST['emailBody'];

    // Store data as JSON in a text file named by order ID
    $orderData = array(
      "email" => $email,
      "subject" => $subject,
      "emailBody" => $emailBody
    );
    file_put_contents("./messages/" . $orderId, json_encode($orderData));

    // POST the data to BTCPay
    $params = [
              'orderId' => $orderId,
              'storeId' => BTCPAY_STORE_ID,
              'serverIpn' => BTCPAY_CALLBACK_URL,
              'browserRedirect' => SUCCESS_URL,
              'price' => $price,
              'currency' => $currency,
              'buyerName' => $name,
              'buyerEmail' => $email,
              'notifyEmail' => $email,
              'submit' => "Submit"
            ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, BTCPAY_INVOICE_API_URL);
    curl_setopt($ch, CURLOPT_PORT, 443);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/x-www-form-urlencoded', 'content-length: ' . strlen(http_build_query($params)), 'accept: text/html'));

    curl_exec($ch);
    $redirect = curl_getinfo($ch)['redirect_url'];

    // Redirect the user to the invoice payment page
    echo '<META HTTP-EQUIV="Refresh" Content="0; URL=' . $redirect . '">';
  }

?>