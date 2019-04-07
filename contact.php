<?php include('contact_secrets.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Jameson Lopp :: Contact</title>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="google-site-verification" content="R0dzeFRj3Xe_EeN3ckdmdqfm7tbk27eyWcfmTvWtfzE" />
  <meta name="keywords" content="jameson, lopp, bitcoin, crypto, cypherpunk" />
  <meta name="Robots" content="index,follow" />
  <meta name="description" content="Contact form for reaching Jameson Lopp." />

  <link rel="shortcut icon" href="/favicon.ico" />
  <link rel="stylesheet" href="css/bootstrap.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/overpassmono.css" />

  <script src="js/jquery-1.11.3.min.js"></script>
  <!-- PoW CAPTCHA -->
  <link type="text/css" rel="stylesheet" href="css/jquery.hashcash.io.min.css" media="all" />
  <script src="js/jquery.hashcash.io.min.js"></script>

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
  <script>
  function MM_swapImgRestore() { //v3.0
    var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
  }
  function MM_preloadImages() { //v3.0
    var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
      var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
      if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
  }

  function MM_findObj(n, d) { //v4.01
    var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
      d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
    if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
    for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
    if(!x && d.getElementById) x=d.getElementById(n); return x;
  }

  function MM_swapImage() { //v3.0
    var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
     if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
  }

  function HideContent(d) {
    document.getElementById(d).style.display = "none";
  }
  function ShowContent(d) {
    document.getElementById(d).style.display = "block";
  }
  function ReverseDisplay(d) {
    if(document.getElementById(d).style.display == "none") { document.getElementById(d).style.display = "block"; }
    else { document.getElementById(d).style.display = "none"; }
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

<body onLoad="MM_preloadImages('images/twitter-roll.webp','images/github-roll.webp','images/earn-roll.webp','images/keybase-roll.webp','images/pgp-roll.webp')">
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
        <p>Jameson's goal is to spread knowledge of crypto systems and liberating technologies. If you can offer an opportunity to reach a large audience, please use the form below. If you're seeking specific answers for your own understanding, please check the <a href="bitcoin.html">educational resources page.</a> For business opportunities or other requests, please use the contact form hosted at <a href="https://earn.com/lopp/" target="_blank">earn.com</a></p>
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
      <div class="col-xs-12 text-left col-lg-offset-0 col-lg-12"> <br><br>
      </div>
      <div class="col-xs-12 col-lg-offset-1 col-lg-10 col-md-offset-1 col-md-10 col-sm-10 col-sm-offset-1">
        <h3>Presentation/Interview Request Form</h3>
        <p><span class="error">Please do not contact me inquiring about paid promotions / press releases / reviews / social media marketing. My reputation is not for sale. Messages sent via this form are heavily filtered and may not be read, much less responded to - use the earn.com link if you want a guaranteed response.</span></p>

        <form action="contact.php" method="post">    
           <label for="name">Name </label><span class="error"> <?= $nameErr; ?></span>

            <input type="text" id="name" name="name" placeholder="Your name..." value="<?=$_POST["name"]?>">

            <label for="email">Email</label><span class="error"> <?= $emailErr; ?></span>
            <input type="text" id="email" name="email" placeholder="Your email..." value="<?=$_POST["email"]?>">

            <label for="subject">Subject</label><span class="error"> <?= $subjectErr; ?></span>
            <input type="text" id="subject" name="subject" placeholder="Subject..." value="<?=$_POST["subject"]?>">
            
            <label for="emailBody">Message</label><span class="error"> <?= $messageErr; ?></span>
            <textarea id="emailBody" name="emailBody" placeholder="Write something..." style="height:200px"><?=$_POST["emailBody"]?></textarea>

            <input type="submit" name="submit" value="Submit"><span class="error"> <?= $captchaErr; ?></span>
            <script>
              $("form input[type=submit]").hashcash({
                key: "97517ff2-9167-4af1-afb0-779a67e395b3",
                complexity: 0.1
              });
            </script>
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
        <div class="row">
          <div class="text-left col-lg-offset-1 col-lg-5 col-md-5 col-md-offset-1 col-sm-offset-1 col-sm-6 col-xs-12">
            <h3>Open Source Projects</h3>
            <h4><a href="https://github.com/jlopp/bitcoin-core-config-generator/" target="_blank">Bitcoin Core Config Generator</a></h4>
            <h4><a href="https://github.com/jlopp/statoshi/" target="_blank">Statoshi</a></h4>
            <h4><a href="https://github.com/jlopp/physical-bitcoin-attacks/blob/master/README.md" target="_blank">Physical Attack Log</a></h4>
            <h4><a href="https://github.com/jlopp/xpub-converter" target="_blank">Bitcoin xpub Converter</a></h4>
            <br>
            <br>
          </div>
          <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12">
            <h3>Want a Guaranteed Response?</h3>
            <p>
              <button type="button" class="btn btn-success" onclick="location.href='https://earn.com/lopp/'">Get in touch on earn.com</button>
            </p>
            <br>
            <br>
          </div>
        </div>
  </div>
</section>
<hr>
<div class="section well">
  <div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-10 col-lg-10 col-xs-offset-0 col-xs-12">
          <p class="text-right">
            <a href="pgpkey" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('PGP','','images/pgp-roll.webp',1)" target="_blank" rel="nofollow"><img src="images/pgp.webp" alt="PGP" width="20" height="20" id="PGP">
            </a>
            <a href="https://twitter.com/lopp?ref_src=twsrc%5Etfw&ref_url=http%3A%2F%2Flopp.net%2F" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Twitter','','images/twitter-roll.webp',1)" target="_blank" rel="nofollow"><img src="images/twitter.webp" alt="Twitter" width="24" height="20" id="Twitter">
            </a>
            <a href="https://github.com/jlopp" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Github','','images/github-roll.webp',1)" target="_blank" rel="nofollow"><img src="images/github.webp" alt="Github" width="24" height="20" id="Github">
            </a>
            <a href="https://earn.com/lopp/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('earn','','images/earn-roll.webp',1)" target="_blank" rel="nofollow"><img src="images/earn.webp" alt="earn" width="20" height="20" id="earn">
            </a>
            <a href="https://keybase.io/lopp" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Keybase','','images/keybase-roll.webp',1)" target="_blank" rel="nofollow"><img src="images/keybase.webp" alt="Keybase" width="24" height="20" id="Keybase">
            </a>
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
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="js/jquery-1.11.3.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.js"></script>
</body>
</html>