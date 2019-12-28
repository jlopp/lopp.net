<?php include('contact_secrets.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Jameson Lopp :: Contact</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="keywords" content="jameson, lopp, bitcoin, crypto, cypherpunk" />
  <meta name="Robots" content="index,follow" />
  <meta name="description" content="Contact form for reaching Jameson Lopp." />

  <meta name="twitter:card" content="summary" />
  <meta name="twitter:description" content="Contact Jameson Lopp" />
  <meta name="twitter:title" content="Jameson Lopp's Contact Information" />
  <meta name="twitter:site" content="@lopp" />
  <meta name="twitter:creator" content="@lopp" />
  <meta name="twitter:image" content="https://www.lopp.net/images/avatar.jpg" />

  <meta property="og:type" content="website" />
  <meta property="og:description" content="Contact Jameson Lopp" />
  <meta property="og:image" content="https://www.lopp.net/images/avatar.jpg" />
  <meta property="og:url" content="https://www.lopp.net/contact.php" />
  <meta property="og:title" content="Contact Jameson Lopp" />

  <link rel="shortcut icon" href="/favicon.ico" />
  <link rel="apple-touch-icon" href="/images/apple-touch-icon.png" />

  <link rel="stylesheet" href="css/bootstrap.css" />
  <link id="mystyle" rel="stylesheet" href="css/style.css" />

  <link rel="preload" href="/fonts/overpassmono/_Xmq-H86tzKDdAPa-KPQZ-AC1i-0tg.woff2" as="font" type="font/woff2" crossorigin="anonymous">
  <link rel="preload" href="/fonts/overpassmono/_Xm3-H86tzKDdAPa-KPQZ-AC3pSRo_CXBg.woff2" as="font" type="font/woff2" crossorigin="anonymous">
  <link rel="preload" href="/fonts/overpassmono/_Xm3-H86tzKDdAPa-KPQZ-AC3vCQo_CXBg.woff2" as="font" type="font/woff2" crossorigin="anonymous">

  <script src="js/jquery-3.4.1.slim.min.js"></script>
  <!-- PoW CAPTCHA -->
  <link type="text/css" rel="stylesheet" href="css/jquery.hashcash.io.min.css" media="all" />
  <!-- PGP encryption -->
  <script src="js/openpgp.min.js" async></script>
  <script>
    function showFreeForm() {
      document.getElementById("freeform").style.display = "block";
      document.getElementById("paidform").style.display = "none";
    }

    function showPaidForm() {
      document.getElementById("freeform").style.display = "none";
      document.getElementById("paidform").style.display = "block";
    }

    async function encrypt(elementId) {
      if (window.crypto.getRandomValues) {
        // don't allow message to be encrypted multiple times
        if (document.getElementById(elementId).value.startsWith("-----BEGIN PGP MESSAGE-----")) {
          return true;
        }
        var pub_key = await openpgp.key.readArmored(
`-----BEGIN PGP PUBLIC KEY BLOCK-----

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
NjT4rMUesCnjTVHVM9KXvMemwAhhYbM=
=QGZJ
-----END PGP PUBLIC KEY BLOCK-----`
          );
        var options = {
            message: openpgp.message.fromText(document.getElementById(elementId).value),       // input as Message object
            publicKeys: pub_key.keys
        }
        var pgp_message = openpgp.encrypt(options).then(ciphertext => {
          $('#'+elementId).val(ciphertext.data);
          return true;
        });
      } else {
        $("#freeencryptbutton").val("Error");
        $("#paidencryptbutton").val("Error");
        window.alert("This browser isn't supported!");
        return false;
      }
    }
  </script>

  <!-- Google Analytics -->
  <script>
    (function(i, s, o, g, r, a, m) {
      i['GoogleAnalyticsObject'] = r;
      i[r] = i[r] || function() {
        (i[r].q = i[r].q || []).push(arguments)
      }, i[r].l = 1 * new Date();
      a = s.createElement(o),
        m = s.getElementsByTagName(o)[0];
      a.async = 1;
      a.src = g;
      m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'js/gastats.js', 'ga');

    ga('create', 'UA-2071667-1', 'auto');
    ga('send', 'pageview');
  </script>

</head>

<body>
<nav class="navbar navbar-default col-sm-offset-0 col-sm-12 col-md-offset-0 col-md-12 col-lg-offset-0 col-lg-12">
  <div class="container"> 
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#myDefaultNavbar1" aria-expanded="false"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a class="navbar-brand" href="/">Jameson Lopp</a> </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="myDefaultNavbar1">
      <ul class="nav navbar-nav">
      <li><a href="articles">Articles</a></li>
      <li><a href="presentations">Presentations</a></li>
      <li><a href="interviews">Interviews</a></li>
      <li><a href="bitcoin-information">Bitcoin Resources</a></li>
      <li><a href="lightning-information">Lightning Resources</a></li>
      <li class="active"><a href="#">Contact<span class="sr-only">(current)</span></a></li>
      </ul>
    </div>
    <!-- /.navbar-collapse --> 
  </div>
  <!-- /.container-fluid --> 
</nav>
<section class="inner-background">
<div class="container">
    <div class="row">
      <div class="col-xs-12 text-left col-lg-offset-0 col-lg-12"> <br>
      </div>
    </div>
  </div>
  <div class="container" style="background-color: #ffffff">
    <div class="row">
      <div class="col-xs-12 text-left col-lg-offset-1 col-lg-8 col-md-offset-1 col-md-10 col-sm-offset-1 col-sm-10">
        <h1>Contact</h1>
        <p>Jameson's goal is to spread knowledge of crypto systems and liberating technologies. If you can offer an opportunity to reach a large audience, please use the low priority form. If you're seeking specific answers for your own understanding, please check the <a href="/bitcoin-information">educational resources page.</a> For consulting questions or high priority requests, please use the paid form.</p>
        <br>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-xs-12 text-left col-lg-offset-0 col-lg-12"> <br><br>
      </div>
    </div>
  </div>
</section>
<section>
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-lg-offset-1 col-lg-10 col-md-offset-1 col-md-10 col-sm-10 col-sm-offset-1">
        <div style="margin: 20px;text-align:center"><button type="button" class="btn btn-success" onclick="showFreeForm()">Send Low Priority Message (Free)</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-success" onclick="showPaidForm()">Send High Priority Message ($100)</button></div>

        <form id="freeform" action="contact.php" method="post" <? if(!isset($_POST["formType"]) || $_POST["formType"] == "paid") echo 'style="display:none"';?>>
          <p><span class="error">Please do not contact me inquiring about paid promotions / press releases / reviews / social media marketing. My reputation is not for sale. Messages sent via this form are heavily filtered and may not be read, much less responded to - use the paid form if you want a guaranteed response.</span></p>
          <label for="freeName">Name </label><span class="error"> <?= $nameErr; ?></span>
          <input type="text" id="freeName" name="name" placeholder="Your name..." value="<?=$_POST["name"]?>">
          <label for="freeEmail">Email</label><span class="error"> <?= $emailErr; ?></span>
          <input type="text" id="freeEmail" name="email" placeholder="Your email address..." value="<?=$_POST["email"]?>">
          <label for="freeSubject">Subject</label><span class="error"> <?= $subjectErr; ?></span>
          <input type="text" id="freeSubject" name="subject" placeholder="Subject..." value="<?=$_POST["subject"]?>">
          <label for="freeEmailBody">Message</label><span class="error"> <?= $messageErr; ?></span>
          <textarea id="freeEmailBody" name="emailBody" placeholder="Write your message here. If it contains sensitive information, click the Encrypt Message button before submitting." style="height:200px"><?=$_POST["emailBody"]?></textarea>
          <input type="hidden" name="formType" value="free" />
          <button type="button" class="btn btn-success" id="freeencryptbutton" onClick="encrypt('freeEmailBody')">Encrypt Message</button>
          <input type="submit" name="submit" value="Submit"><span class="error"> <?= $captchaErr; ?></span>
          <!-- PoW CAPTCHA makes calls to body, so must be loaded after body exists in DOM -->
          <script src="js/jquery.hashcash.io.min.js"></script>
          <script>
            $("form input[type=submit]").hashcash({
              key: "97517ff2-9167-4af1-afb0-779a67e395b3",
              complexity: 0.1
            });
          </script>
        </form>

        <form id="paidform" method="POST" action="contact.php" <? if(!isset($_POST["formType"]) || $_POST["formType"] == "free") echo 'style="display:none"';?>>
          <p><span class="error">Upon submitting this form you will be directed to pay an invoice for $100 USD in BTC. <b>I will not receive your message until after the payment has been confirmed</b>. After receiving your message I will strive to respond to it within 24 hours.</span></p>
          <label for="paidName">Name </label><span class="error"> <?= $nameErr; ?></span>
          <input type="text" id="paidName" name="name" placeholder="Your name..." value="<?=$_POST["name"]?>">
          <label for="paidEmail">Email</label><span class="error"> <?= $emailErr; ?></span>
          <input type="text" id="paidEmail" name="email" placeholder="Your email address..." value="<?=$_POST["email"]?>">
          <label for="paidSubject">Subject</label><span class="error"> <?= $subjectErr; ?></span>
          <input type="text" id="paidSubject" name="subject" placeholder="Subject..." value="<?=$_POST["subject"]?>">
          <label for="paidEmailBody">Message</label><span class="error"> <?= $messageErr; ?></span>
          <textarea id="paidEmailBody" name="emailBody" placeholder="Write your message here. If it contains sensitive information, click the Encrypt Message button before submitting." style="height:200px"><?=$_POST["emailBody"]?></textarea>
          <input type="hidden" name="formType" value="paid" />
          <button type="button" class="btn btn-success" id="paidencryptbutton" onClick="encrypt('paidEmailBody')">Encrypt Message</button>
          <input type="submit" name="submit" value="Submit">
        </form>
        <br>
        <br>
        </div>
        </div>
        <div class="row">
          <div class="col-lg-offset-0 col-lg-4">

          </div>
          <div class="col-lg-4">&nbsp;</div>
        </div>
  </div>
</section>
<hr>
<div class="section well">
  <div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-10 col-lg-10 col-xs-offset-0 col-xs-12">
          <p class="text-right">
            <a href="/pgpkey" class="pgp" target="_blank" aria-label="Jameson's PGP Key"></a>
            <a href="https://twitter.com/lopp" class="twitter" target="_blank" rel="noopener" aria-label="Jameson's Twitter Account"></a>
            <a href="https://github.com/jlopp" class="github" target="_blank" rel="noopener" aria-label="Jameson's Github Account"></a>
            <a href="https://earn.com/lopp/" class="earn" target="_blank" rel="noopener" aria-label="Jameson's Earn Account"></a>
            <a href="https://keybase.io/lopp" class="keybase" target="_blank" rel="noopener" aria-label="Jameson's Keybase Account"></a>
          </p>
        </div>
    </div>
  </div>
</div>
<footer class="text-center">
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <p class="footer">No copyright ever. No rights reserved.</p>
      </div>
    </div>
  </div>
</footer>
<script src="js/bootstrap.min.js"></script>
<script src="js/lopp.js"></script>
</body>
</html>