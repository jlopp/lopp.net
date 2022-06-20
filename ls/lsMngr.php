<?php
  ini_set("session.gc_maxlifetime",84600);
  @session_start();
  include ("livesearch.class.php");
  $ls = new LiveSearch();

  if ($_REQUEST["action"] == "login") $ls->authenticate();
  if ($_REQUEST["action"] == "logout") $ls->logout();

  $section = $_REQUEST["s"];

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LiveSearch Dashboard</title>
    <link rel="stylesheet" href="lsMngr.css">
    <script src="lsMngr.min.js"></script>

</head>
<body>

<div class='container'>
  <div class='row'>
    <div class='col-xs-12'>


  <div class="page-header">
    <h1>LiveSearch<sup class='small'><?php echo $ls->version; ?></sup> Manager</h1>
  </div>


  <?php
  if ($ls->mngrError) {
    echo "<div class='alert alert-danger'>" . implode("<br />",$ls->mngrError) . "</div>";
  }
  if ($ls->mngrSuccess) {
    echo "<div class='alert alert-success'>" . $ls->mngrSuccess . "</div>";
  }
  ?>

<?php
  //missing credentials
  if (!$ls->mngrUser || !$ls->mngrPass) {
    echo "<div class='alert alert-warning'>Please provide user credentials to get access to the LiveSearch Manager by filling the variables <code>\$mngrUser</code> <strong>and</strong> <code>\$mngrPass</code> in the <code>livesearch.class.php</code></div>";
  }
  //not authenticated, login
  elseif (!$ls->auth) {
?>
  <div class='row'>
    <div class='col-xs-12 col-xs-offset-0 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4'>
      <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" class='form-horizontal' id="loginF">
        <input type="hidden" name="action" value="login" />
        <div class='panel panel-primary'>
          <div class='panel-heading'><h3 class='panel-title'>Login</h3></div>
          <div class='panel-body'>
            <div class='form-group'>
              <div class='col-xs-12'>
                <input type="text" name="user" value="<?php echo $_REQUEST["user"]; ?>" class="form-control" placeholder="Username" required="required" />
              </div>
            </div>
            <div class='form-group'>
              <div class='col-xs-12'>
                <input type="password" name="pass" class="form-control" placeholder="Password" required />
              </div>
            </div>
          </div>
          <div class='panel-footer'>
            <button class="btn btn-default btn-block" type="submit"><i class='fa fa-sign-in fa-fw'></i>Login</button>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php
  }

if ($ls->auth) {

  if (!$section) $section = "dashboard";
  $actMen[$section] = " class='active'";


?>


  <ul class="nav nav-pills nav-mngr mb20">
    <li<?php echo $actMen["dashboard"]; ?>><a href="<?php echo $_SERVER["PHP_SELF"]; ?>"><i class='fa fa-tachometer'></i> <span>Dashboard</span></a></li>
    <li<?php echo $actMen["tools"]; ?>><a href="<?php echo $_SERVER["PHP_SELF"]; ?>?s=tools"><i class='fa fa-cogs'></i> <span>Tools</span></a></li>
    <li class='navbar-right'><a href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=logout"><i class='fa fa-power-off'></i> <span>Logout</span></a></li>
  </ul>

<?php

  echo "<div class='row'>";

  if ($section == "dashboard") {
    switch ($ls->cachetime) {
      case -1: $cacheTimeInfo = "no caching"; break;
      case 0: $cacheTimeInfo = "no re-indexing"; break;
      default: $cacheTimeInfo = "each " . $ls->cachetime . " hours"; break;
    }
    echo "<div class='col-xs-12 col-sm-6'>
      <table class='table table-bordered table-hover table-striped'><tbody>
          <tr><td><strong>Base-Url</strong></td><td>" . $ls->baseurl . "</td></tr>
          <tr><td><strong>Extensions</strong></td><td>" . implode(", ",$ls->checkext) . "</td></tr>
          <tr><td><strong>Method</strong></td><td>" . $ls->method . ($ls->method == "auto" ? " (" . ($ls->useCurl ? "curl" : "url_fopen") . ")" : "") . "</td></tr>
          <tr><td><strong>Collect Images</strong></td><td><i class='fa fa-fw " . ($ls->collect_images?"fa-check text-success":"fa-times text-danger") . "'></i></td></tr>
          <tr><td><strong>Collect PDFs</strong></td><td><i class='fa fa-fw " . ($ls->collect_pdfs?"fa-check text-success":"fa-times text-danger") . "'></i></td></tr>
          <tr><td><strong>Performance Fix</strong></td><td><i class='fa fa-fw " . ($ls->performance_fix?"fa-check text-success":"fa-times text-danger") . "'></i></td></tr>
          <tr><td><strong>Cache Hours</strong></td><td>$cacheTimeInfo</td></tr>
        </tbody></table>
      </div>";

    if (file_exists($ls->cachedir . "/data.php")) {
      $dataCdate = date("M d<\s\u\p>S</\s\u\p> Y G:i",filectime($ls->cachedir . "/data.php"));
      include($ls->cachedir . "/data.php");
      if ($cache_data_img) $dataImagesCnt = count($cache_data_img);
      else $dataImagesCnt = 0;
      $dataItemsCnt = count($cache_data) + $dataImagesCnt;
      //PDF
      $pdfSrchRes = array_count_values(array_map(function($value){return $value['isPDF'];}, $cache_data));
      $dataPdfsCnt = intval($pdfSrchRes[1]);
      $dataUrlsCnt = $dataItemsCnt - $dataImagesCnt - $dataPdfsCnt;
      $lsState = "<span class='text-success'><i class='fa fa-fw fa-check'></i> indexed</span>";
    } else {
      $dataCdate = "<i class='fa fa-fw fa-times text-danger'></i>";
      $dataUrlsCnt = $dataItemsCnt = $dataImagesCnt = $dataPdfsCnt = 0;
      if (file_exists($ls->cachedir . "/tmpContentUrls.php")) {
        $lsState = "<span class='text-warning'><i class='fa fa-fw fa-clock-o'></i> seems to be indexing</span>";
      } else {
        $lsState = "<span class='text-danger'><i class='fa fa-fw fa-exclamation-triangle'></i> never/not indexed</span>";
      }
    }
    if (file_exists($ls->cachedir . "/SearchStrings.php")) {
      $srchLast = date("M d<\s\u\p>S</\s\u\p> Y G:i",filectime($ls->cachedir . "/SearchStrings.php"));
      include($ls->cachedir . "/SearchStrings.php");
      $srchQCnt = count($q);
      $srcQSum = array_sum($q);
    } else {
      $srchLast = "<i class='fa fa-fw fa-times text-danger'></i>";
      $srchQCnt = $srcQSum = 0;
    }

    if ($ls->cachetime == -1) {
      $reCacheTimeInfo = "on <span class='text-primary'>every</span> search";
    } elseif ($ls->cachetime == 0) {
      $reCacheTimeInfo = "never";
    } else {
      $time2cache = $ls->time2cache();
      if ($time2cache) $reCacheTimeInfo = "on <span class='text-primary'>next</span> search";
      else {
        $reCacheTimeInfo = date("M d<\s\u\p>S</\s\u\p> Y G:i", time() + $ls->cachetime * 60 * 60 - $ls->age);
      }
    }

    echo "<div class='col-xs-12 col-sm-6'>
      <table class='table table-bordered table-hover table-striped'><tbody>
          <tr><td><strong>LiveSearch Status</strong></td><td>$lsState</td></tr>
          <tr><td><strong>Last time indexed</strong></td><td>$dataCdate</td></tr>
          <tr><td><strong>Items indexed</strong></td><td>$dataItemsCnt <div class='pull-right'>
            <span class='label label-primary'><i class='fa fa-link'></i> $dataUrlsCnt</span> 
            <span class='label label-primary'><i class='fa fa-image'></i> $dataImagesCnt</span> 
            <span class='label label-primary'><i class='fa fa-file-pdf-o'></i> $dataPdfsCnt</span>
          </div></td></tr>
          <tr><td><strong>Last performed search</strong></td><td>$srchLast</td></tr>
          <tr><td><strong>Different search terms</strong></td><td>$srchQCnt</td></tr>
          <tr><td><strong>Performed searches</strong></td><td>$srcQSum</td></tr>
          <tr><td><strong>Next possible auto-indexing</strong></td><td>$reCacheTimeInfo</td></tr>
        </tbody></table>
      </div>";


  } elseif ($section == "tools") {
    echo "<div class='toolboxes'>";
    echo "<div class='col-xs-12 col-sm-6 col-md-4 toolbox'>
      <div class='panel panel-info'>
        <div class='panel-heading'><h3 class='panel-title'>Clear Stored Search Counts</h3></div>
        <div class='panel-body'>
          Function to remove stored searchstrings (used by the Search Word Cloud).<br /><br />
          <code>\$LiveSearch->clearSrchStr();</code>
        </div>
        <div class='panel-footer'>";
    if (file_exists($ls->cachedir . "/SearchStrings.php")) {
      echo "<button class='btn btn-primary btn-block js-bn-clearSrchStr' type='button'>Clear Stored Search Counts</button>";
    } else {
      echo "<div class='tttop' title='no Search Strings stored'><button class='btn btn-primary btn-block' type='button' disabled='disabled'>Clear Stored Search Counts</button></div>";
    }
    echo "</div>
      </div>
    </div>";

    echo "<div class='col-xs-12 col-sm-6 col-md-4 toolbox'>
      <div class='panel panel-info'>
        <div class='panel-heading'><h3 class='panel-title'>Clear Search Results</h3></div>
        <div class='panel-body'>
          The stored (cached) results of previous made searches can be removed with this method. It will be used on re-indexing, re-caching or when using clearCache too.<br /><br />
          <code>\$LiveSearch->clearSrch();</code>
        </div>
        <div class='panel-footer'>";
    if (glob($ls->cachedir . "/*.srch")) {
      echo "<button class='btn btn-primary btn-block js-bn-clearSrch' type='button'>Clear Search Results</button>";
    } else {
      echo "<div class='tttop' title='no search results stored'><button class='btn btn-primary btn-block' type='button' disabled='disabled'>Clear Search Results</button></div>";
    }
    echo "</div>
      </div>
    </div>";

    echo "<div class='col-xs-12 col-sm-6 col-md-4 toolbox'>
      <div class='panel panel-info'>
        <div class='panel-heading'><h3 class='panel-title'>Clear Cache</h3></div>
        <div class='panel-body'>
          Method to delete all cached files (except the SearchStrings). <strong class='bg-warning text-warning'>Note:</strong> Cached files will be deleted automatically if they are too old (<code>\$cachetime</code> exceeded) or on every other caching process (-1) <br /><br />
          <code>\$LiveSearch->clearCache();</code>
        </div>
        <div class='panel-footer'>";
    $cachedFiles = glob($ls->cachedir . "/*.*");
    $files2remove = count($cachedFiles); 
    // if (in_array($ls->cachedir . "/tmpContentUrls.php",$cachedFiles)) $files2remove--;
    if (in_array($ls->cachedir . "/thumbs",$cachedFiles)) $files2remove--;
    if (in_array($ls->cachedir . "/SearchStrings.php",$cachedFiles)) $files2remove--;
    if ($files2remove) {
      echo "<button class='btn btn-primary btn-block js-bn-clearCache' type='button'>Clear Cache <span class='badge'>$files2remove</span></button>";
    } else {
      echo "<div class='tttop' title='no cached files to remove'><button class='btn btn-primary btn-block' type='button' disabled='disabled'>Clear Cache <span class='badge'>$files2remove</span></button></div>";
    }
    echo "</div>
      </div>
    </div>";

    echo "<div class='col-xs-12 col-sm-6 col-md-4 toolbox'>
      <div class='panel panel-info'>
        <div class='panel-heading'><h3 class='panel-title'>Cache Files</h3></div>
        <div class='panel-body'>
          Just caching the files without searching - this action could take a while an will be called automatically while search process if no files are cached or the age of the cached files is older than the defined <code>\$cachetime</code>. <strong class='bg-info text-info'>This could take a while.</strong><br /><br />
          <code>\$LiveSearch->cacheFiles();</code>
        </div>
        <div class='panel-footer'>
          <div><button class='btn btn-primary btn-block js-btn-cacheFiles' type='button'>Cache Files</button></div>
        </div>
      </div>
    </div>";

    echo "</div>";

  }



      echo "</div>";
} //auth
?>
    </div>  <!-- col-xs-12 -->
  </div>  <!-- row -->
</div> <!-- container -->
</body>
</html>