<?php

const YOUR_EMAIL_ADDRESS = "";
const BTCPAY_STORE_ID = "";
const BTCPAY_CALLBACK_URL = "";
const SUCCESS_URL = "";
const BTCPAY_INVOICE_API_URL = "";
// set this to a path and file name to store recently submitted stamps
const STAMP_LOG = "";

// user-configurable random string
$hc_salt = "";

// number of bits to collide
$hc_difficulty = 17;

// tolerance, in minutes, between stamp generation and expiration
// allows time for clock drift and for filling out form
$hc_tolerance = 10;

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
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}


// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function generateOrderId() {
  return substr(str_shuffle(str_repeat($x='abcdefghjkmnpqrstuvwxyz23456789', ceil(20/strlen($x)) )),1,20);
}

// Check conditions for submission of free form
if($formType == "free" && (!empty($_POST["name"])) && (preg_match("/^[a-zA-Z ]*$/",$name)) && (!empty($_POST["email"])) &&  (filter_var($email, FILTER_VALIDATE_EMAIL)) && (!empty($_POST["subject"])) && (!empty($_POST["emailBody"]))) {
    if (!hc_CheckStamp()) {
      $captchaErr = 'Invalid proof of work submitted! Please try again.';
      return;
    }
    // log that this puzzle has been used
    file_put_contents(STAMP_LOG, $_POST['hc_stamp'] . "\n", FILE_APPEND | LOCK_EX);

    // All good
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $emailBody = $name . " from email: " . $email . " wrote the following:" . "\n\n" . $_POST['emailBody'];
    $headers = "From: $email\r\nReply-to: $email";
    mail(YOUR_EMAIL_ADDRESS, $subject, $emailBody, $headers);
    echo '<META HTTP-EQUIV="Refresh" Content="0; URL=thank_you.html">';
    exit;
  }

// Check conditions for submission of paid form
if($formType == "paid" && (!empty($_POST["name"])) && (preg_match("/^[a-zA-Z ]*$/",$name)) && (!empty($_POST["email"])) &&  (filter_var($email, FILTER_VALIDATE_EMAIL)) && (!empty($_POST["subject"])) && (!empty($_POST["emailBody"]))) {
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

    // Something went wrong and BTCPay didn't generate an invoice
    if (empty($redirect)) {
      $emailErr = "Something went wrong generating the bitcoin invoice. Please try again later.";
    } else { // Redirect the user to the invoice payment page
      echo '<META HTTP-EQUIV="Refresh" Content="0; URL=' . $redirect . '">';
    }
  }

  /////////////////////////////
 //       hashcash          //
/////////////////////////////

// define generic hash function (currently md5)
function hc_HashFunc($x) {
  return hash('sha256', $x);
}

function DEBUG_OUT($x) {
  //echo "<pre>$x</pre>\n";
}

// convert hex numbers to decimal
function hc_HexInDec($x)
{
  if(($x >= "0") && ($x <= "9"))
    $ret = $x * 1;
  else
    switch($x)
  {
    case "A": $ret = 10;
    case "B": $ret = 11;
    case "C": $ret = 12;
    case "D": $ret = 13;
    case "E": $ret = 14;
    case "F": $ret = 15;
    default: $ret = 0;
  }
}

// convert hex numbers to binary strings
function hc_HexInBin($x)
{
  switch($x)
  {
    case '0': $ret = '0000'; break;
    case '1': $ret = '0001'; break;
    case '2': $ret = '0010'; break;
    case '3': $ret = '0011'; break;
    case '4': $ret = '0100'; break;
    case '5': $ret = '0101'; break;
    case '6': $ret = '0110'; break;
    case '7': $ret = '0111'; break;
    case '8': $ret = '1000'; break;
    case '9': $ret = '1001'; break;
    case 'A': $ret = '1010'; break;
    case 'B': $ret = '1011'; break;
    case 'C': $ret = '1100'; break;
    case 'D': $ret = '1101'; break;
    case 'E': $ret = '1110'; break;
    case 'F': $ret = '1111'; break;
    default: $ret = '0000';
  }
  DEBUG_OUT("ret = " . $ret);
  return $ret;
}

// Get the first num_bits of data from this string
function hc_ExtractBits($hex_string, $num_bits)
{
  $bit_string = "";
  $num_chars = ceil($num_bits / 4);
  for($i = 0; $i < $num_chars; $i++)
    $bit_string .= hc_HexInBin(substr($hex_string, $i, 1));

  DEBUG_OUT("requested $num_bits bits from $hex_string, returned $bit_string as " . substr($bit_string, 0, $num_bits));
  return substr($bit_string, 0, $num_bits);
}


  /////////////////////////////
 // stamp creation function //
/////////////////////////////

// generate a stamp
function hc_CreateStamp()
{
  global $hc_salt, $hc_difficulty;
  $ip = get_client_ip();
  $now = intval(time() / 60);

  // create stamp
  // stamp = hash of time (in minutes) . user ip . salt value
  $stamp = hc_HashFunc($now . $ip . $hc_salt);
  $nonce = $_POST['hc_nonce'];

  //embed stamp in page
  echo "<input type=\"hidden\" name=\"hc_stamp\" id=\"hc_stamp\" value=\"" . $stamp . "\">\n";
  echo "<input type=\"hidden\" name=\"hc_difficulty\" id=\"hc_difficulty\" value=\"" . $hc_difficulty . "\">\n";
  echo "<input type=\"hidden\" name=\"hc_nonce\" id=\"hc_nonce\" value=\"" . $hc_nonce . "\">\n";
}

  //////////////////////////////
 // stamp checking functions //
//////////////////////////////

// hc_CheckExpiration - true = valid, false = expired
// this function also implicitly validates the IP address and salt match
function hc_CheckExpiration($a_stamp)
{
  global $hc_salt, $hc_tolerance;

  $tempnow = intval(time() / 60);
  $ip = get_client_ip();

  // gen hashes for $tempnow ... $tempnow - $tolerance
  for($i = -1*$hc_tolerance; $i < $hc_tolerance; $i++)
  {
    DEBUG_OUT("checking $a_stamp versus " . hc_HashFunc(($tempnow - $i) . $ip . $hc_salt));
    if($a_stamp === hc_HashFunc(($tempnow - $i) . $ip . $hc_salt))
    {
      DEBUG_OUT("stamp matched at T-Minus-" . $i);
      return true;
    }
  }

  DEBUG_OUT("stamp expired");
  return false;
}

// check for nonce of $stamp_contract bits for $stamp and $nonce
function hc_CheckProofOfWork($hc_difficulty, $stamp, $nonce)
{

  // get hash of $stamp & $nonce
  DEBUG_OUT("checking difficulty bits of work");
  $work = hc_HashFunc($stamp . $nonce);

  $leadingBits = hc_ExtractBits($work, $hc_difficulty);

  DEBUG_OUT("checking leading bits of $work for difficulty match: $leadingBits");

  // if the leading bits are all 0, the difficulty target was met
  return (strlen($leadingBits) > 0 && intval($leadingBits) == 0);
}

// check a stamp
// checks validity, expiration, and difficulty obligations for a stamp
function hc_CheckStamp()
{
  global $hc_difficulty;

  // get stamp from input
  $stamp = $_POST['hc_stamp'];
  $client_difficulty = $_POST['hc_difficulty'];
  $nonce = $_POST['hc_nonce'];

  DEBUG_OUT("got variables!");
  DEBUG_OUT("stamp: $stamp");
  DEBUG_OUT("hc_difficulty: $client_difficulty");
  DEBUG_OUT("nonce: $nonce");

  // optimized, fastest-test-first order

  if ($client_difficulty != $hc_difficulty) return false;
  DEBUG_OUT("difficulty comparison: $client_difficulty and $hc_difficulty");

  $expectedLength = strlen(hc_HashFunc("randoinput"));
  if(strlen($stamp) != $expectedLength) return false;
  DEBUG_OUT("stamp size: " . strlen($stamp) . " expected: $expectedLength");

  if(!hc_CheckExpiration($stamp)) return false;
  DEBUG_OUT("PoW puzzle has not expired");

  // check the actual PoW
  if(!hc_CheckProofOfWork($hc_difficulty, $stamp, $nonce)) return false;
  DEBUG_OUT("Difficulty threshold met.");

  // check if this puzzle has already been used to submit a message
  $savedStamps = 0;
  if (($handle = fopen(STAMP_LOG, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, "\n")) !== FALSE) {
      if ($data === $stamp)
        return false;
      $savedStamps++;
    }
    fclose($handle);
  }

  // truncate the log if it starts getting long
  if ($savedStamps > 1000) {
    file_put_contents(STAMP_LOG, "$stamp\n");
  }
  return true;
}
?>