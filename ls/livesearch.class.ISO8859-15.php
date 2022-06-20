<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("max_execution_time",900);//15 minutes

class LiveSearch {

  /* V 4.2     *
   * 2020 December 20th */
  
  /****
   * Your settings [+]
   ****/
  //Baselink to search with trailing slash
  var $baseurl = "http://www.yoursite.com/";
  // var $baseurl = "http://www.yoursite.com/aSubDirectory/";

  //Serverpath of your baseurl - including trailing slash (something like /users/mac/www/envato.homac.at/htdocs/demos/LiveSearch/) ... ONLY necessary for thumbnail creation
  var $basepath = "/users/envato/www/yoursite.com/htdocs/";

  //disable indexing of paths above basurl path
  //makes only sense when you are using a subdirectory below your root of the website and only like to stay there
  var $dontIndexUpperDirsOfBaseUrl = false;   //basurl parent paths will be followed
  // var $dontIndexUpperDirsOfBaseUrl = true;   //basurl parent paths won't be followed

  //absolute URL to you ls folder with trailing slash
  var $lsurl = "http://www.yoursite.com/ls/";

  //search results page to prevent from heavy load waitings - within $baseurl
  var $searchresultspage = "search.php";

  //excluding files and directories from indexing under $basedir beginning with:
  //this means the links on these pages won't be followed,
  //works with pdf files too
  //regular expressions can be used
  // var $excl = array(".*-dates/",
  //                   "secret.html",
  //                   "hide",
  //                   );
  var $excl = false;

  //including files and directories under $basedir with:
  //don't forget trailing slashes for directories
  //you can set it to false too
  //these pages will be indexed, the links on it and its content
  //works with pdf files too
  // var $incl = array("hallo3.txt",
  //                   );
  var $incl = false;

  //XML-Sitemap 
  //if you have a XML/RSS sitemap you can point the URL to your sitemap here, if no protocoll (http(s)) is given LiveSearch tries to build url realtive to baseUrl
  //the Sitemap has to be written in a valid format with absolute URLs containing at least
  //  ...
  //    <loc>http://your.website.com/anyFile.html</loc>
  //  ...
  //  OR
  //  ...
  //    <link>http://your.website.com/anyFile.html</link>
  //  ...
  //and only URLs within your baseurl host OR hosts in the additionalHosts array will be followed
  // var $xmlSitemap = "http://www.homac.at/sitemap.xml";
  // var $xmlSitemap = "sitemap.xml";
  // var $xmlSitemap = "/sitemap.xml";
  var $xmlSitemap = false;

  //domains/Hosts of external linked pages or embedded external images or the XML-Sitemaps
  //just the host - no URLs no Protocols
  var $additionalHosts = false;
  // var $additionalHosts = array("www.homac.at","codecanyon.net");

  //method to retrieve the content of urls (curl,url_fopen,auto)
  var $method = "auto"; 
  
  //extensions to check
  var $checkext = array("htm","html","php","txt");
  
  //should URLs be saved next to the indexed content? (intended for debug purposes)
  //example:
  // indexed files are saved as 1.txt 2.txt 3.txt inside the cache directory
  // if enabled, files next to the txt files with the appropriate URL as content will be generated 1.url 2.url 3.url
  // var $saveURLfiles = true;
  var $saveURLfiles = false;

  //should PDF's be indexed and searchable too?
  //Requirements: Linux Webserver, pdftotext binary, ability to run it by the webserver
  var $collect_pdfs = true;
  //extensions for the pdfs (lowercase)
  var $pdfext = array("pdf");
  
  //hours to re-cache files automatically
  //by default it's set to -1 to help you make your search running - afterwards you should either disable auto-caching (0) or increase it to an applicable value
  var $cachetime = -1;    //-1 for cache every time when searching, 0 when caching should not happen
  
  //results per page for the paging function
  var $srch_res_per_page = 10;
  
  //logical combination if searching for multiple words at once
  // OR .... splits to words and shows results containing ANY of the words
  // AND ... splits to words and shows results containing ALL of the words
  // false . doesn't split and shows results for the WHOLE string as it is
  var $srch_logic = "OR";
  // var $srch_logic = "AND";
  // var $srch_logic = false;

  //style for the drawpagination function (default,boxed,bootstrap,bootstrap3,bootstrap4)
  var $pagerstyle = "bootstrap";

  var $highlightClass = "BShighlight";  //highlight is just yellow, BShighlight has a border and another yellow color
  
  //min & max fontsizes for SearchCloud
  var $cloud_min = 10;
  var $cloud_max = 30;

  //maximum number of items in the search cloud (0 shows all items)
  var $maxCloudItems = 50;

  //error message for to small search term
  var $errorToShort = '<div class="alert alert-error alert-danger">You have to enter at least %1$s characters.</div>';   //minlength
  //error message for to small search term
  var $errorNothingFound = '<div class="alert alert-info">No search results for %1$s.</div>';   //%1$s ... searchstring
  //info message for no search term
  var $infoNoSearchTerm = '<div class="alert alert-info">Please type in any term you like to search for.</div>';
  //HTML Tags stripped from search query
  var $errorStrippedTags = '<div class="alert alert-error alert-danger">Tags were removed from your search query<br />HTML-Tags are not allowed to search for.</div>';


  //if you're running into performance-troubles (timeout during caching, memory exhausted ...)
  //var $performance_fix = true;
  var $performance_fix = false;   // true if you've got troubles, false if everything is working fine :) (default)
  
  //if you like to search for images too (filename, alt-tag, title-tag)
  var $collect_images = true;   //true if you like to search for images too, false if not
  var $img_results_headline = '%2$s Images for %1$s';  //used for the imagesearchresults (more images) -> Number of images, Searchstring
  //var $img_results_headline = '%1$s Bilder f&uuml;r %2$s';  //Number of images, Searchstring
  var $img_result_headline = '1 Image for %1$s';  //used for the imagesearchresults (one image) -> Searchstring
  //var $img_results_headline = '1 Bild f&uuml;r %2$s';  //Number of images, Searchstring
  var $thumb_height = 70;
  var $create_thumbs = false; //true if you like to enable the creation of thumbnails (GD-Library required), false if not (CSS resizing instead)

  //if there are any troubles with utf8 content (true/false)
  var $utf8DecodeResults = true;


  //user credentials to access LiveSearch Manager (lsMngr.php) - you have to provide both, user AND pass, to get access
  var $mngrUser = "admin";
  var $mngrPass = "";   //please choose your password

  /****
   * Your settings [-]
   ****/
   
  /****
   * Usage [+]
   ****
   Please check out manual.pdf for usage and examples   
  ****
   * Usage [-]
   ****/
  

  var $version = 4.2;
  var $auth = false;
  var $mngrError = array();
  var $mngrSuccess;
  var $age = 0;
  var $tmpContentUrlKey = 0;

  var $cachedir;
  var $checkedUrls = array();     //checked
  var $collectedUrls = array();   //collected
  var $externalUrls = array();   //external ones
  var $notfoundUrls = array();   //collected
  var $checkUrls = array();       //to check
  var $srch;
  var $searchresults = array();
  var $searchresults_img = array();
  var $searchcount;
  var $searchcountItems;
  var $searchstring;
  var $error = false;
  var $debug;
  var $p;
  var $endset;
  var $offset;
  var $useCurl = false;
  var $useUrlFopen = false;
  var $pdftotext;
  var $minlength = 3;
  var $strippedTagsFromQuery = false;
  var $host;   
  
  function __construct($debug=false) {
    $this->initialize();
    $this->url = $this->baseurl;
    $this->debug = $debug;

    $this->auth = $_SESSION["lsAuth"];
  
    if (!file_exists($this->cachedir)) {
      echo "<div class='error'><b>" . $this->cachedir . "</b> not found.<br />Please create your upload-directory <b>\$cachedir</b> or correct path in the site_search.class.php</div>";
      $this->error = true;
    } elseif (!is_writable($this->cachedir)) {
      echo "<div class='error'><b>" . $this->cachedir . "</b> isn't writeable.<br />Please use <b>chmod 757</b> to fix it.</b></div>";
      $this->error = true;
    } elseif ((!ini_get("allow_url_fopen") || strtolower(ini_get("allow_url_fopen") == "off") || strtolower(ini_get("allow_url_fopen")) == "false") && !function_exists("curl_init")) {
      echo "<div class='error'>Neither <b>allow_url_fopen</b> is not activated nor <b>CURL</b> is enabled.<br />Please enable <b>allow_url_fopen</b> or <b>CURL</b> (php.ini)</div>";
      $this->error = true;
    } elseif ((!ini_get("allow_url_fopen") || strtolower(ini_get("allow_url_fopen") == "off") || strtolower(ini_get("allow_url_fopen")) == "false") && $this->method == "url_fopen") {
      echo "<div class='error'><b>allow_url_fopen</b> is not activated.<br />Please use CURL instead, set \$method to curl or auto.</div>";
      $this->error = true;
    } elseif (!function_exists("curl_init") && $this->method == "curl") {
      echo "<div class='error'><b>CURL</b> is not enabled.<br />Please set \$method to url_fopen or auto.</div>";
      $this->error = true;
    } elseif ($this->collect_pdfs) {
      if (!preg_match('/linux/i',PHP_OS)) {
        echo "<div class='error'>For the PDFsearch you need a LINUX Webserver. You're running <i>" . PHP_OS . "</i>. Please move to a Linux WebServer or deactivate \$collect_pdfs.</div>";
        $this->error = true;
      } else {
        //pdftotext installed????
        $pdl = @popen("which pdftotext 2>&1", "r");
        if ($pdl) {
          $this->pdftotext = fread($pdl, 1024); 
          pclose($pdl);
        }
        if (!preg_match('/\/pdftotext$/',$this->pdftotext)) {
          echo "<div class='error'><b>pdftotext</b> is not available on your webserver. " . ($this->pdftotext ? "Server says: <i>" . $this->pdftotext . "</i>. " : "") . "<br />Please ask your hoster to make it available for you or deactivate \$collect_pdfs.</div>";
        $this->error = true;
        }
      }
    } elseif (intval(phpversion()) < 5) {
      echo "<div class='error'>LiveSearch requires as miniumum <b>PHP 5.x</b>.<br />You're running <b>" . phpversion() . "</b></div>";
      $this->error = true;
    } elseif ($this->create_thumbs && (!$this->basepath || !is_dir($this->basepath))) {
      echo "<div class='error'>\$basepath not found or not set<br />You should either disable \$create_thumbs or define or check the \$basepath.</div>";
    }
  }
  
  //preconfigs 
  function initialize() {
    //Path to folder for caching files
    $this->cachedir = realpath(dirname(__FILE__)) . "/cache";
    $this->searchresultspage = $this->baseurl . $this->searchresultspage;
    //set retrieving method
    switch ($this->method) {
      case "curl": $this->useCurl = true; break;
      case "url_fopen": $this->useUrlFopen = true; break;
      default:
        if (function_exists('curl_init')) $this->useCurl = true;
        else $this->useUrlFopen = true;        
        break;
    }
    //get hostname for building absolute urls of links with leading /
    $uData = parse_url($this->baseurl);
    $this->host = $uData["scheme"] . "://" . $uData["host"] . "/";
  }    
  
  //delete cached files
  function clearCache() {
    if ($this->error) return false;
    $handle=opendir($this->cachedir);
    while ($file = readdir($handle)) {
      if ($file != "." && $file != ".." && $file != "SearchStrings.php" && $file != "thumbs") unlink($this->cachedir . "/$file");
    }
    if ($this->debug) echo "CLEAR: files deleted<br />";
    closedir($handle);
    if ($this->collect_images && is_dir($this->cachedir . "/thumbs")) {
      $handle=opendir($this->cachedir . "/thumbs");
      while ($file = readdir($handle)) {
        if ($file != "." && $file != ".." && $file != "index.php") unlink($this->cachedir . "/thumbs/$file");
      }
      if ($this->debug) echo "CLEAR: thumbs deleted<br />";
      closedir($handle);
    }
  }
  
  //delete search results
  function clearSrch() {
    if ($this->error) return false;
    $handle=opendir($this->cachedir);
    while ($file = readdir($handle)) {
      if (preg_match('/\.srch$/',$file)) unlink($this->cachedir . "/" . $file);
    }
    if ($this->debug) echo "clearSrch: files deleted<br />";
    closedir($handle);
  }
  
  //saved searchstrings
  function clearSrchStr() {
    if ($this->error) return false;
    if (file_exists($this->cachedir . "/SearchStrings.php")) unlink($this->cachedir . "/SearchStrings.php");
    if ($this->debug) echo "CLEARSrchStr: files deleted<br />";
  }
  
  // get href links
  function get_href($file) {
    $expr = "/\<a\ [^>]*href=[\"']+(.*)[\"']+[ >]/Uis";
    preg_match_all($expr,$file,$patterns);
    return $patterns[1];
  }
  
  //check time of last caching
  function time2cache() {
    if ($this->error) return false;
    //if cachetime is set to -1 AND we are on a page greater "1" we should avoid recaching
    if ($this->cachetime == -1 && $this->p > 1) return false;
    if (!file_exists($this->cachedir . "/data.php")) return true;
    if ($this->cachetime == -1) return true;
    if (!$this->cachetime) return false;
    $cachetimesec = $this->cachetime * 60 * 60;
    $nowsec = time();
    $lastsec = filectime($this->cachedir . "/data.php");
    $this->age = $nowsec - $lastsec;
    if ($this->age > $cachetimesec) return true;
    else return false;
  }

  //small version to write content into files
  function write($name,$content) {
    if ($this->error) return false;
    $file = $this->cachedir . "/" . $name;
    if (!$handle = fopen($file, "w")) return false;
    fwrite($handle, trim($content));
  }
  
  //check if url should be indexed or not
  function checkUrl($url) {
    $url = trim($url);
    if (!$url) return false;    //no url - don't index
    //$path = strtolower(parse_url($url,PHP_URL_PATH));   //get path
    //prior 5.1.2 fix
    $t = parse_url($url);
    $path = strtolower($t["path"]); 
    
    if (preg_match('/\/$/',$path)) return true;         //trailing slash, directory or root - index
    
    //get file
    $tmpA = explode("/",$path);
    $last = array_pop($tmpA);              
    
    if (strstr($last,".")) $ext = substr($last,strrpos($last,'.')+1);   //if file contains a "." - extract extension
    if (!$ext) return true;                                             //if no extension, url could be a directory - index
    elseif (in_array($ext,$this->checkext)) return true;                //if in defined extensions - index
    
    //PDF
    elseif ($this->collect_pdfs && in_array($ext,$this->pdfext)) return true;
    
    else return false;                                                  //unknown extension - don't index  
  }
  
  //parse and handle collected urls and arrange them in collectedUrls array
  function collectUrls($urls=array(),$url) {
    if (!$urls) return false;

    //get hostname of current (parent) url to built correct links
    $uData = parse_url($url);
    $thisurlhost = $uData["scheme"] . "://" . $uData["host"] . "/";

    $this->checkUrls = array();   //reset urls to check
    //remove foreign urls
    $cnturls = count($urls);
    for ($i=0;$i<$cnturls;$i++) {
      $thisurl = $urls[$i];
      $thisurl = preg_replace("#^//#","http://",$thisurl);              //considering double slashes
      if (preg_match("/^\//",$thisurl)) {
        //prevent from staying on external websites
        if ($thisurlhost == $this->host) {
          $thisurl = $this->host . $thisurl;    //internal path begins with /
        } else {
          continue;
        }
      }
      if (!preg_match('/^http/i',$thisurl)) {                                             //path doesn't begin with http 
        $thisurl = $this->makeAbsolute($thisurl,$url);
      } elseif ($this->additionalHosts && preg_match('/^https?:\/\/('.implode(")|(",$this->additionalHosts).')/i',$thisurl)) {   //http path is a foreign one - BUT allowed to follow
        $this->externalUrls[] = $thisurl;
      } elseif (!preg_match('/^'.str_replace("/","\/",$this->host).'/i',$thisurl)) {         //http path is a foreign one 
        continue;
      }
      //last check for the validity if the URL
      //stay within baseurl ond don't visit any parent directory
      if ($this->dontIndexUpperDirsOfBaseUrl && stripos($thisurl, $this->host) === 0 && stripos($thisurl, $this->baseurl) !== 0) {
        continue;
      }

      //add slash if slashed $base exists
      /****
       * todo - find out if path is directory */      
      /*if (!preg_match('/\/$/',$thisurl)) {
        $tmp = @fopen($thisurl . "/","r");
        if ($tmp) $thisurl .= "/";
      }*/
      //check if path could be a directory
      /*if (!preg_match('/\/$/',$thisurl)) {
        unset ($ext);
        //extensioncheck
        $last = basename($thisurl); 
        if (strstr($last,".")) $ext = substr($last,strrpos($last,'.')+1);   //if file contains a "." - extract extension
        if (!$ext) {   //if no extension, url could be a directory - add slash
           $thisurl .= "/";
        } elseif (!in_array($ext,$this->checkext)) $thisurl .= "/";  //if in defined extensions - could be a file - leave it
      } */
      $thisurl = preg_replace('/(?<!:)(\/){2,}/', '/', $thisurl);                         //replace wrong double slashes with one slash 
      if ($this->debug) echo "collectUrls: retrieved $thisurl<br />";
      if (!$this->checkUrl($thisurl)) continue;                                           //url shouln't be indexed
      if (preg_match('/^'.str_replace("/","\/",$this->searchresultspage).'/i',$thisurl)) continue;  //prevent from indexing the site search
      //excluding urls part [+]
      $ecount = count($this->excl);
      for ($e=0;$e<$ecount;$e++) {
        $thisexcl = trim($this->excl[$e]);
        if ($thisexcl && preg_match('#^'.$this->baseurl . $thisexcl.'#',$thisurl)) continue 2;
      }
      //excluding urls part [-]
      if (strstr($thisurl,"#")) $thisurl = substr($thisurl,0,strpos($thisurl,"#"));       //remove anchors
      if (strstr($thisurl,"?")) $thisurl = substr($thisurl,0,strpos($thisurl,"?"));       //remove question signs
      $thisurl = trim($thisurl);
      if (!in_array($thisurl,$this->checkedUrls)) $this->checkUrls[] = $thisurl;          //increase checkUrls
      if (!in_array($thisurl,$this->collectedUrls)) $this->collectedUrls[] = $thisurl;    //increase collectedUrls
    }
    $this->checkUrls = array_values(array_unique($this->checkUrls));
    $this->checkedUrls[] = $url;
  }
  
  function getUrlsFromXmlSitemap() {
    $xmlSitemapUrl = trim($this->xmlSitemap);
    $xmlUrls = array();
    if (!$xmlSitemapUrl) return false;
    if (preg_match("/^\//",$xmlSitemapUrl)) {             //path begins with /
      $xmlSitemapUrl = $this->host . $xmlSitemapUrl;
    } elseif (!preg_match('/^http/i',$xmlSitemapUrl)) {            //path doesn't begin with http 
      $xmlSitemapUrl = $this->makeAbsolute($xmlSitemapUrl,$this->baseurl);
    } elseif ($this->additionalHosts && preg_match('/^https?:\/\/('.implode(")|(",$this->additionalHosts).')/i',$xmlSitemapUrl)) {   //http path is a foreign one - BUT allowed to follow
      //it's okay :) we have the correct $xmlSitemapUrl;
    } elseif (!preg_match('/^'.str_replace("/","\/",$this->host).'/i',$thisurl)) {   //http path is a foreign one 
      return false;
    }
    $xmlSitemapUrl = preg_replace('/(?<!:)(\/){2,}/', '/', $xmlSitemapUrl);                         //replace wrong double slashes with one slash 
    $xmlContent = $this->getContent($xmlSitemapUrl,__FUNCTION__  . " " . __LINE__,false);
    if (!$xmlContent) return false;
    //parse xml file
    $xmlP = xml_parser_create();
    xml_parse_into_struct($xmlP, $xmlContent, $xmlDataArr);
    xml_parser_free($xmlP);
    foreach ($xmlDataArr as $xmlData) {
      if (in_array(strtolower($xmlData["tag"]),array("link","loc"))) {
        $xmlUrls[] = $xmlData["value"];
      }
    }
    return $xmlUrls;    
  }


  //response code for Url retrieving before using file_get_contents
  function get_http_response_code($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
  }



  //either CURL or url_fopen to retrieve content of the URLs
  //$url ...the url to get the content from
  //$srcMethod ...for debugging purposes (which function and line is being used)
  //$saveTmpData ...save the temporary data of files, not needed for XML sitemaps
  function getContent($url,$srcMethod=false,$saveTmpData=true) {
    if (preg_match('/^(tel:|javascript:|#)/i',basename($url))) return false;

    //curl
    if ($this->useCurl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $content = curl_exec($ch);
        $headerCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch);
        if ($this->debug) echo "ReturnCode for $url: $headerCode<br />";
        if ($headerCode == 301) {
          $cinfo = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
          if ($this->debug) echo "RedirectInformation <pre>" . print_r($cinfo,true) . "</pre>";
        }
        if ($headerCode > 400) return false;
    }
    //allow_url_fopen
    elseif ($this->useUrlFopen) {
      // I've commented the line below for performance reasons - but it would work
      // if ($this->get_http_response_code($url) > 400) return false;
      $content = @file_get_contents($url);
    }
    if ($saveTmpData) {
      //no need to collect content for URL retrieving AND content grabbing twice, lets save it to temporary files
      $urlKey = $this->tmpContentUrlKey;
      file_put_contents($this->cachedir. "/tmpContentUrls.php","\t\t'" . $this->escapeChar($url,"'") . "' => $urlKey,\n",FILE_APPEND);
      //save content
      file_put_contents($this->cachedir. "/tmpUrlContent.$urlKey.tmp",$content);
      $this->tmpContentUrlKey++;
    }

    return $content;
  }
  
  //retrieve urls
  function checkUrls($urls=array()) {
    if ($this->checkUrls) {
      $cntu = count($this->checkUrls);
      $tcheckUrls = $this->checkUrls;
      for ($i=0;$i<$cntu;$i++) {
        //continue if already indexed
        if (in_array($tcheckUrls[$i], $this->checkedUrls)) continue;

        $mycontent = $this->getContent($tcheckUrls[$i],__FUNCTION__  . " " . __LINE__);
        if (!$mycontent) { 
          $this->checkedUrls[] = $tcheckUrls[$i];
          $this->checkUrls = array_diff($this->checkUrls, array($tcheckUrls[$i]));
          $this->checkUrls = array_values($this->checkUrls);
          continue;
        }
        if (in_array($tcheckUrls[$i],$this->externalUrls)) continue;
        $mylinks = $this->get_href($mycontent);
        $this->checkedUrls[] = $tcheckUrls[$i];
        if ($this->debug) echo "checkUrls: analyzed " . $tcheckUrls[$i] . "<br />";
        $this->collectUrls($mylinks, $tcheckUrls[$i]);
        $this->checkUrls = array_diff($this->checkUrls, array($tcheckUrls[$i]));
        $this->checkUrls = array_values($this->checkUrls);
        if ($this->performance_fix) break;
        else {
          if ($this->checkUrls) $this->checkUrls();
          else break;
        }
      }
    }
  }
  
  //collect links and generate cache files
  function cacheFiles() {
    if ($this->error) return false;
    $this->clearCache();
    $mylinks = array();
    file_put_contents($this->cachedir. "/tmpContentUrls.php","<?php\n\$tmpContentUrls = array(\n");
    $mycontent = $this->getContent($this->url, __FUNCTION__  . " " . __LINE__);
    $mylinks = $this->get_href($mycontent);
    //expanding with the incl array
    if ($this->incl) $mylinks = array_merge($mylinks,$this->incl);

    //add current base URL - just to avoid single page websites from not being indexed - useless but why not (why searching single page webprojects) ??!?!?!!
    array_push($mylinks,$this->url);

    //xml sitemap URLs here
    $xmlUrls = $this->getUrlsFromXmlSitemap();
    if ($xmlUrls) $mylinks = array_merge($mylinks,$xmlUrls);

    $this->collectUrls($mylinks, $this->url);
    $this->checkUrls($this->checkUrls);
    file_put_contents($this->cachedir. "/tmpContentUrls.php",");\n?>\n",FILE_APPEND);

    //get temporary collected urls for content checking :)
    include($this->cachedir. "/tmpContentUrls.php");

    $this->checkedUrls = array_values(array_unique($this->checkedUrls));
    $cnturls = count($this->collectedUrls);   
    $y = $z = 0; 

    for ($i=0;$i<$cnturls;$i++) {
      $isPDF = $meta_tags = false;


      $urlInfos = pathinfo($this->collectedUrls[$i]);
      
      $isTXT = strtolower($urlInfos["extension"]) == "txt";

      if ($this->collect_pdfs && in_array(strtolower($urlInfos["extension"]),$this->pdfext)) {
        $title = $urlInfos["basename"];
        $isPDF = true;
      }
      
      if ($isPDF) $mycontent = true;
      else {
        $tmpFname =  $this->cachedir. "/tmpUrlContent." . $tmpContentUrls[$this->collectedUrls[$i]] . ".tmp";
        if (file_exists($tmpFname)) {
          $mycontent = file_get_contents($tmpFname);
          unlink($tmpFname);
        } else {
          $mycontent = $this->getContent($this->collectedUrls[$i], __FUNCTION__  . " " . __LINE__);
        }
      }

      if ($this->saveURLfiles) {
        file_put_contents($this->cachedir. "/" . $y.".url",$this->collectedUrls[$i]);
      }

      if ($mycontent) {
        
        //it's a PDF
        if ($isPDF) {
          $this->PDFwrite($y.".txt",$this->collectedUrls[$i]);
        }
        //it's a TXT file - let's just copy the file
        elseif ($isTXT) {
          $this->write($y.".txt",$mycontent);
        }
        //no PDF just a plain HTML,php,asp...
        else {
          //find page title
          preg_match_all("/<title(.*)>(.*)<\/title>/siU", $mycontent,$tarr);
          $title = $tarr[2][0];
          //cut till body if body :)
          if (stristr($mycontent,"<body")) {
            $body = substr($mycontent,stripos($mycontent,'<body'));
            //get meta tags
            $meta_tags = get_meta_tags($this->collectedUrls[$i]);
          } else $body = $mycontent;
          
          $body = preg_replace('/<\!--LSHIDE-->.*<\!--\/LSHIDE-->/Us', "", $body);
          $body = preg_replace('/<script.*<\/script>/Us', "", $body); 
          $body = $this->br2nl($body);
          
          if ($this->collect_images) {
            $res = $img = array();
            preg_match_all('/<img[^>]+>/i',$body, $res);        
            foreach ($res as $img_tags) {
              foreach ($img_tags as $img_tag) {
                preg_match_all('/(src|alt|title|class)=((\'|")[^(\'|")]*(\'|"))/i',$img_tag, $img);
                if ($img) {
                  $imgAttributes = array_flip($img[1]);
                  $imgSrc = preg_replace("#^.?//#","http://",$img[2][$imgAttributes["src"]]);
                  $imgClass = $img[2][$imgAttributes["class"]];
                  $imgAlt = $img[2][$imgAttributes["alt"]];
                  $imgTitle = $img[2][$imgAttributes["title"]];
                  $imgExternal = 0;

                  $img_path = str_replace(array("'",'"'),"",$imgSrc);
                  
                  if (!in_array($this->collectedUrls[$i],$this->externalUrls)) {

                    if (preg_match("/^\//",$img_path)) $img_path = preg_replace('/\/$/','',$this->baseurl) . $img_path;             //path begins with /
                    if (!preg_match('/^http/i',$img_path)) {                                             //path doesn't begin with http 
                      $img_path = $this->makeAbsolute($img_path,$this->collectedUrls[$i]);
                    } elseif ($this->additionalHosts && preg_match('/^https?:\/\/('.implode(")|(",$this->additionalHosts).')/i',$img_path)) {   //http path is a foreign one - BUT allowed to follow
                      $imgExternal = 1;
                    } elseif (!preg_match('/^'.str_replace("/","\/",$this->baseurl).'/i',$img_path)) {   //http path is a foreign one 
                      continue;
                    }
                  } else {
                     $img_path = $this->makeAbsolute($img_path,$this->collectedUrls[$i]);             //path begins with /
                     $imgExternal = 1;
                  }

                  if (preg_match('/lshide/i',$imgClass)) continue;
                  $cache_data .= '$cache_data_img[' . $z . '] = array("src" => "' . addslashes($img_path) . '",
                    "filename" => "' . basename($img_path) . '",
                    "class" => "' . $this->escapeChar($imgClass) . '",
                    "alt" => "' . $this->escapeChar($imgAlt) . '",
                    "title" => "' . $this->escapeChar($imgTitle) . '",
                    "external" => ' . $imgExternal . ',
                    "GDThumb" => "' . md5($img_path) . '.jpg",
                    "parenturl" => "' . addslashes($this->collectedUrls[$i]) . '");' . "\n";
                  $z++;
                }   
              }
            }   
          }        
          $body = strip_tags($body);
          $body = preg_replace('/\n/', " ", $body);
          $body = preg_replace('/\s{2,}/', " ", $body);
          $this->write($y.".txt",$body);
        } //no PDF - just HTML and PHP and other plain files
     
        $uData = parse_url($this->collectedUrls[$i]);
        $host = preg_replace('/^www\./','',$uData["host"]);

        $cache_data .= '$cache_data[' . $y . '] = array("url" => "' . addslashes($this->collectedUrls[$i]) . '",
          "description" => "' . $this->escapeChar($meta_tags["description"]) . '",
          "keywords" => "' . $this->escapeChar($meta_tags["keywords"]) . '",
          "title" => "' . $this->escapeChar($title) . '",
          "host" => "' . $this->escapeChar($host) . '",
          "isPDF" => ' . ($isPDF?1:0) . ');' . "\n";
        $y++;
      }
    }

    if ($cache_data) $this->write("data.php",'<?php' . "\n$cache_data\n?".'>');
  }
  
  //write content of PDF into txt file
  function PDFwrite($name,$url) {    
    //transform misplaced Spaces into URL equivalences
    $url = preg_replace('/\s/','%20',$url);
    //temporary PDF file
    $tmpfile = $this->cachedir . "/" . time() . ".pdf";
    //curl
    if ($this->useCurl) {
      $fhdl = fopen($tmpfile, 'w');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_FILE, $fhdl);
      $headerCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
      curl_exec($ch);
      curl_close($ch);
      fclose($fhdl);
      if ($headerCode > 400) return false;
      else $pdfwrite = true;
    }
    //allow_url_fopen
    elseif ($this->useUrlFopen) {
      $content = file_get_contents($url);
      if ($content) {
        file_put_contents($tmpfile, $content);
        $pdfwrite = true;
      } else return false;
    }
    
    //write PDFContent and delete temporary pdf
    if ($pdfwrite) {
      $pdl = popen("pdftotext" . " '" . $tmpfile . "' '" . $this->cachedir . "/" . $name . "'", 'r');
      if ($pdl) {
        pclose($pdl);
        unlink($tmpfile);
      }
    }
  }
  
  
  
  //searching the cached files for $str
  //second argument is the current page for the paging function
  function search($str,$p=1) {
    if (get_magic_quotes_gpc()) $str = stripslashes($str);
    //trim it
    $str = trim($str);
    //small precheck for html tags stripping
    $lenA = strlen($str); //length before stripping
    if (!$str || $lenA < $this->minlength) return false;  //minlength
    $str = strip_tags($str);
    $lenB = strlen($str); //length after stripping
    $this->strippedTagsFromQuery = $lenB < $lenA; //was it stripped by tags????
    if (!$str || $lenB < $this->minlength) return false;  //minlength after stripping


    $this->p = intval($p);
    $this->searchstring = $str;
    //collect searchstring
    $this->collectSearchStrings($str);
    $str = preg_quote($str,"/");

    //caching ....
    if ($this->time2cache()) $this->cacheFiles();
    
    $session_file_name = md5($str) . ".srch";                  //temporary search files for paging function
    $session_file = $this->cachedir . "/$session_file_name";
    if (preg_match('/[äöüßÄÖÜ]/',$str)) $str2 = str_replace(array("ä","ö","ü","ß","Ä","Ö","Ü"),array("&auml;","&ouml;","&uuml;","&szlig;","&Auml;","&Ouml;","&Uuml;"),$str);
    if (!file_exists($session_file)) {
      //go through files, search and collect links  
      if (file_exists($this->cachedir. "/data.php")) include($this->cachedir. "/data.php");

      $srchStrArr = array();
      $srchStrArr[] = $str;
      
      if (preg_match('/\w\s+\w/',$str)) {
        switch ($this->srch_logic) {
          case "OR":
            $srchStrArr = array_merge($srchStrArr, preg_split('/\s+/',$str));
            if ($str2) {
              $srchStrArr[] = $str2;
              $srchStrArr = array_merge($srchStrArr, preg_split('/\s+/',$str2));
            }
            break;
          case "AND":
            $srchStrArr = array_merge($srchStrArr, preg_split('/\s+/',$str));
            array_shift($srchStrArr);
            break;
          default: 
            $this->srch_logic = false;
            if ($str2) $srchStrArr[] = $str2;
            break;
        }
      } else {
        if ($str2) $srchStrArr[] = $str2;
        $this->srch_logic = false;
      }
      $srchStrArr = array_values(array_unique($srchStrArr));

      for ($i=0;$i<count($cache_data);$i++) {
        if (!file_exists($this->cachedir . "/$i.txt")) continue;
        $content = file_get_contents($this->cachedir . "/$i.txt");
        list($cTitle,$cKeywords,$cDescription) = array($cache_data[$i]["title"],$cache_data[$i]["keywords"],$cache_data[$i]["description"]);
        if ($this->utf8DecodeResults) {
          $content = utf8_decode($content);
          $cTitle = utf8_decode($cTitle);
          $cKeywords = utf8_decode($cKeywords);
          $cDescription = utf8_decode($cDescription);
        }

        if (
          // OR or false (not AND to prevent "false" = non-boolean settings)
          ($this->srch_logic != "AND" && (preg_match("/" . implode("|",$srchStrArr) . "/i", $content) || preg_match("/" . implode("|",$srchStrArr) . "/i", $cTitle) || preg_match("/" . implode("|",$srchStrArr) . "/i", $cKeywords) || preg_match("/" . implode("|",$srchStrArr) . "/i", $cDescription))) ||
          // AND
          ($this->srch_logic == "AND" && (preg_match("/(?=.*" . implode(")(?=.*",$srchStrArr) . ")/i", $content) || preg_match("/(?=.*" . implode(")(?=.*",$srchStrArr) . ")/i", $cTitle) || preg_match("/(?=.*" . implode(")(?=.*",$srchStrArr) . ")/i", $cKeywords) || preg_match("/(?=.*" . implode(")(?=.*",$srchStrArr) . ")/i", $cDescription)))
          ) {

          $title = $this->str_highlight($cTitle,$str);
          $url = $cache_data[$i]["url"];
          $content = $this->highlight($content, $srchStrArr);
          if (!$title) $title = "no title";
          $this->searchresults[] = array("url" => $url,
                                         "title" => $title,
                                         "content" => $content,
                                         "isPDF" => $cache_data[$i]["isPDF"],
                                         "host" => $cache_data[$i]["host"]);
          $srchcontent .= '$searchresults[] = array("url" => "' . addslashes($url) . '",
            "title" => "' . $this->escapeChar($title) . '",
            "isPDF" => "' . $cache_data[$i]["isPDF"] . '",
            "host" => "' . $cache_data[$i]["host"] . '",
            "content" => "' . $this->escapeChar($content) . '");' . "\n";
        } 
      }
      
      if ($this->collect_images) {  
        $img_collected = array();
        $img_cnt = count($cache_data_img);
        for ($i=0;$i<$img_cnt;$i++) {
          $thisimg = $cache_data_img[$i];
          if (preg_match("/$str".($str2?"|$str2":"")."/i", $thisimg["filename"]) || preg_match("/$str".($str2?"|$str2":"")."/i", $thisimg["title"]) || preg_match("/$str".($str2?"|$str2":"")."/i", $thisimg["alt"])) {
            if (!in_array($thisimg["src"],$img_collected)) {
              $this->searchresults_img[] = array("src" => $thisimg["src"],
                                                 "title" => $thisimg["title"],
                                                 "alt" => $thisimg["alt"],
                                                 "parenturl" => $thisimg["parenturl"],
                                                 "GDThumb" =>  $thisimg["GDThumb"]);
              $srchcontent .= '$searchresults_img[] = array("src" => "' . addslashes($thisimg["src"]) . '",
                "title" => "' . $this->escapeChar($thisimg["title"]) . '",
                "alt" => "' . $this->escapeChar($thisimg["alt"]) . '",
                "parenturl" => "' . addslashes($thisimg["parenturl"]) . '",
                "GDThumb" => "' . addslashes($thisimg["GDThumb"]) . '");' . "\n";
              $img_collected[] = $thisimg["src"];
              
              if ($this->create_thumbs) $md5img = $this->thumb($thisimg["src"]);
              $searchresults_img_content .= "<a href='" . $thisimg["src"] . "'><img src='" . ($md5img ? $this->lsurl . "cache/thumbs/$md5img" : $thisimg["src"]) . "' alt='" . $thisimg["alt"] . "' title='" . $thisimg["title"] . "' /></a>\n";
              
            }
          }
        }
      }
      
      if ($srchcontent) {
        $this->write($session_file_name,"<?php\n".$srchcontent."?".">");
      }
    } else {
      include($session_file);
      $this->searchresults = $searchresults;
      $this->searchresults_img = $searchresults_img;
    }
    
    $this->searchcount = count($this->searchresults) + count($this->searchresults_img);
    //for the pager
    $this->searchcountItems = count($this->searchresults) + (count($this->searchresults_img)?1:0);
    
    if ($this->collect_images && $this->searchresults_img) {
      $imgCNT = count($this->searchresults_img);
      $this->searchresults_img["title"] = sprintf($imgCNT > 1 ? $this->img_results_headline : $this->img_result_headline, $str, $imgCNT);
      $this->searchresults_img["url"] = "#";
      if (!$searchresults_img_content) {
        for ($i=0;$i<$imgCNT;$i++) {
          $thisimg = $this->searchresults_img[$i];
          $searchresults_img_content .= "<a href='" . $thisimg["src"] . "'><img src='" . ($this->create_thumbs ? $this->lsurl . "cache/thumbs/" . $thisimg["GDThumb"] : $thisimg["src"]) . "' alt='" . $thisimg["alt"] . "$i' title='" . $thisimg["title"] . "' /></a>\n";
        }
      }
      $this->searchresults_img["content"] = $searchresults_img_content;      
    }
    if ($this->searchresults_img) {
      if ($this->searchresults) array_unshift($this->searchresults,$this->searchresults_img);
      else $this->searchresults[] = $this->searchresults_img;
    }
    if (!$this->searchcount) return false; //nothing found
    elseif ($this->searchcountItems <= $this->srch_res_per_page) {  //paging isn't necessary
      return $this->searchresults;
    } else {
      //some pagecalculations
      if (!$this->p) $this->p = 1;
      if ((($this->p-1) * $this->srch_res_per_page) > $this->searchcountItems) $this->p = 1;       //if page is too high the page will be set to 1
      $this->offset = ($this->p * $this->srch_res_per_page) - $this->srch_res_per_page;
      $this->endset = ($this->offset+$this->srch_res_per_page <= $this->searchcountItems ? $this->offset+$this->srch_res_per_page : $this->searchcountItems);
      $this->pages = ceil($this->searchcountItems/$this->srch_res_per_page);
      
      $output = array();
      //page output
      
      if ($this->collect_images && $this->searchresults_img) {
        $output[] = array("images" => $this->searchresults_img);
      }
      
      for ($i=$this->offset;$i<$this->endset;$i++) {
        if (!$this->searchresults[$i]["url"]) continue;
        $output[] = array("url" => $this->searchresults[$i]["url"],
                          "title" => $this->searchresults[$i]["title"],
                          "content" => $this->searchresults[$i]["content"],
                          "isPDF" => $this->searchresults[$i]["isPDF"],
                          "host" => $this->searchresults[$i]["host"]
                         );
      }
      $this->searchresults = $output;
      return $output;
    }
  }

  
  //collect and count search strings
  function collectSearchStrings($str) {
    if (!$str) return false;
    $str = mb_strtolower(str_replace("\\","",$str));
    $srchfilename = "SearchStrings.php";
    $srchfile = $this->cachedir . "/" . $srchfilename;
    if (file_exists($srchfile)) include($srchfile);
    $q["$str"]++;
    //stats output
    foreach ($q as $key => $value) {
      $output .= '$q["' . $this->escape($key) . '"] = ' . intval($value) . ';'."\n";
    }
    if ($output) $this->write($srchfilename,"<?php\n".$output."?".">");
  }
  
  //remove backslashes and escape just doublequotes
  function escape($str) {
    if (!$str) return false;
    else {
      $str = str_replace("\\","",$str);
      $str = str_replace("\"","\\\"",$str);
      return $str;
    }
  }

  //escape (add backslash) for specific charcters
  //addslahes willadd slashes to single and double quotes, that's the reason for this method
  function escapeChar($str, $char='"') {
    if (!$char) return $str;
    return str_replace($char,'\\' . $char,$str);
  }

  
  //just return some paging infos
  function pager() {
    if ($this->pages < 2) return false;   //nothing to page
    $output = array("current" => $this->p,
                    "total" => $this->pages);
    return $output;
  }
  
  //to create the thumbs
  function thumb($img) {
    if (!$img || !$this->create_thumbs) return false;
    $img_name = md5($img) . ".jpg";
    $img = $this->basepath . str_replace($this->baseurl,"",$img);
    if (!file_exists($img)) return false;
    if (!is_dir($this->cachedir . "/thumbs")) mkdir($this->cachedir . "/thumbs");
    list($w,$h) = getimagesize($img); 
    $hmulti = $this->thumb_height/$h;
    $modh = $this->thumb_height;
    $modw = round($w*$hmulti);
    $tn = imagecreatetruecolor($modw, $modh);
    switch (strtolower(substr(strrchr($img,"."),1))) {
      case "gif": $image = imagecreatefromgif($img); break;
      case "png": $image = imagecreatefrompng($img); break;
      default: $image = imagecreatefromjpeg($img); break;
    }    
    imagecopyresampled($tn, $image, 0, 0, 0, 0, $modw, $modh, $w, $h); 
    imagejpeg($tn, $this->cachedir . "/thumbs/$img_name", 80);
    return $img_name;
  }
  
  //Example how to handle pages
  //$pagevarname is the name of the variable to set page
  //$searchstringvarname is the name of the searchstring variable
  function drawPagination($pagevarname = "p", $searchstringvarname = "q", $add2query = false, $pagerstyle = false) {

  
    $pagerstyle = $pagerstyle?$pagerstyle:$this->pagerstyle;
  
    if ($this->pages < 2) return false;   //nothing to page
    if ($this->pages > 8) {
      //interpage calc
      $pstart = $this->p - 2;
      if ($pstart <= 1) $pstart = 2;
      $pend = $this->p + 2;
      if ($pend > $this->pages) $pend = $this->pages;
      $pages_after_last_interpage = ($pend+1 >= $this->pages ? true : false);
      $pages_before_first_interpage = ($pstart-2 >= 1 ? true : false);
    
      $NAV_PAGES[] = array("P" => 1,
                           "PCLASS" => ($this->p == 1 ? "psel" : ""),
                           "SEP" => ($pages_before_first_interpage ? " ... " : ""));
                           
      for ($i=$pstart;$i<=$pend;$i++) {
        $NAV_PAGES[] = array("P" => $i,
                             "PCLASS" => ($this->p == $i ? "psel" : ""),
                             "SEP" => ($i == $pend && !$pages_after_last_interpage ? " ... " : "" ));
      }
      if ($pend < $this->pages) {
      $NAV_PAGES[] = array("P" => $this->pages,
                           "PCLASS" => "",
                           "SEP" => "");
      }
    } else {
      for ($i=1;$i<=$this->pages;$i++) {
        $NAV_PAGES[] = array("P" => $i,
                             "PCLASS" => ($this->p == $i ? "psel" : ""),
                             "SEP" => "");
      }
    }
    //make the ouput
    if ($NAV_PAGES) {

      $navPageCount = count($NAV_PAGES);
      $lastPage = $NAV_PAGES[$navPageCount-1]["P"];
    
      switch ($pagerstyle) {
      
        case "boxed":  //inspired by Dave Reedijk
          for ($i=0;$i<$navPageCount;$i++) {
            if ($NAV_PAGES[$i]["PCLASS"] == "psel") {
              $activepagenumber = $NAV_PAGES[$i]["P"];
              break; 
            }
          }
          $pager = "<div class='ls-boxedpager ls-boxedpager-right'><ul>";
          if ($activepagenumber > 1) {
            $pager .= "<li><a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . ($activepagenumber - 1) . "$add2query'>&laquo;</a></li>\n";
          } else {
            $pager .= "<li class=\"disabled\"><a>&laquo;</a></li>\n";
          }
          for ($i=0;$i<count($NAV_PAGES);$i++) {
              $pager .= "<li class='" . $NAV_PAGES[$i]["PCLASS"] . "'>" . "<a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . $NAV_PAGES[$i]["P"] . "$add2query'>" . $NAV_PAGES[$i]["P"] . "</a>" . $NAV_PAGES[$i]["SEP"] . "</li>\n";
          }
          if ($activepagenumber == $lastPage) {
            $pager .= "<li class=\"disabled\"><a>&raquo;</a></li>\n";
          } else {
            $pager .= "<li><a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . ($activepagenumber + 1) . "$add2query'>&raquo;</a></li>\n";
          } //eenleveninbalans
          $pager .= "</ul></div>";
        break;
      
        case "bootstrap":  //bootstrap standard style (2.3.2)
          for ($i=0;$i<$navPageCount;$i++) {
            if ($NAV_PAGES[$i]["PCLASS"] == "psel") {
              $activepagenumber = $NAV_PAGES[$i]["P"];
              break; 
            }
          }
          $pager = "<div class='pagination pagination-small pagination-centered'><ul>";
          if ($activepagenumber > 1) {
            $pager .= "<li><a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . ($activepagenumber - 1) . "$add2query'>&laquo;</a></li>\n";
          } else {
            $pager .= "<li class='disabled'><span>&laquo;</span></li>\n";
          }
          for ($i=0;$i<count($NAV_PAGES);$i++) {
              $pager .= "<li" . ($NAV_PAGES[$i]["PCLASS"] ? " class='active'" : "") . "><a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . $NAV_PAGES[$i]["P"] . "$add2query'>" . $NAV_PAGES[$i]["P"] . "</a></li>\n";
              if ($NAV_PAGES[$i]["SEP"]) {
                $pager .= "<li class='disabled'><span>...</span></li>\n";
              }
          }
          if ($activepagenumber == $lastPage) {
            $pager .= "<li class='disabled'><span>&raquo;</span></li>\n";
          } else {
            $pager .= "<li><a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . ($activepagenumber + 1) . "$add2query'>&raquo;</a></li>\n";
          }
          $pager .= "</ul></div>";
        break;
      
        case "bootstrap3":  //bootstrap standard style (3.x)
          for ($i=0;$i<$navPageCount;$i++) {
            if ($NAV_PAGES[$i]["PCLASS"] == "psel") {
              $activepagenumber = $NAV_PAGES[$i]["P"];
              break; 
            }
          }
          $pager = "<div class='text-center'><ul class='pagination pagination-sm'>";
          if ($activepagenumber > 1) {
            $pager .= "<li><a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . ($activepagenumber - 1) . "$add2query'>&laquo;</a></li>\n";
          } else {
            $pager .= "<li class='disabled'><span>&laquo;</span></li>\n";
          }
          for ($i=0;$i<count($NAV_PAGES);$i++) {
              $pager .= "<li" . ($NAV_PAGES[$i]["PCLASS"] ? " class='active'" : "") . "><a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . $NAV_PAGES[$i]["P"] . "$add2query'>" . $NAV_PAGES[$i]["P"] . "</a></li>\n";
              if ($NAV_PAGES[$i]["SEP"]) {
                $pager .= "<li class='disabled'><span>...</span></li>\n";
              }
          }
          if ($activepagenumber == $lastPage) {
            $pager .= "<li class='disabled'><span>&raquo;</span></li>\n";
          } else {
            $pager .= "<li><a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . ($activepagenumber + 1) . "$add2query'>&raquo;</a></li>\n";
          }
          $pager .= "</ul></div>";
        break;
      
        case "bootstrap4":  //bootstrap standard style (4.2)
          for ($i=0;$i<$navPageCount;$i++) {
            if ($NAV_PAGES[$i]["PCLASS"] == "psel") {
              $activepagenumber = $NAV_PAGES[$i]["P"];
              break; 
            }
          }
          $pager = "<div class='container'><div class='row'><div class='col'><nav aria-label='Search results pages'><ul class='pagination justify-content-center'>";
          if ($activepagenumber > 1) {
            $pager .= "<li class='page-item'><a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . ($activepagenumber - 1) . "$add2query' class='page-link'>&laquo;</a></li>\n";
          } else {
            $pager .= "<li class='page-item disabled'><span class='page-link'>&laquo;</span></li>\n";
          }
          for ($i=0;$i<count($NAV_PAGES);$i++) {
              $pager .= "<li class='page-item" . ($NAV_PAGES[$i]["PCLASS"] ? " active" : "") . "'><a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . $NAV_PAGES[$i]["P"] . "$add2query' class='page-link'>" . $NAV_PAGES[$i]["P"] . "</a></li>\n";
              if ($NAV_PAGES[$i]["SEP"]) {
                $pager .= "<li class='page-item disabled'><span class='page-link'>...</span></li>\n";
              }
          }
          if ($activepagenumber == $lastPage) {
            $pager .= "<li class='page-item disabled'><span class='page-link'>&raquo;</span></li>\n";
          } else {
            $pager .= "<li><a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . ($activepagenumber + 1) . "$add2query' class='page-link'>&raquo;</a></li>\n";
          }
          $pager .= "</ul></nav>";
          $pager = "<div class='container'><div class='row'>$pager</div></div></div>";
        break;
        
        default: //the origin LiveSearch Pager
          $pager = "<p>";
          for ($i=0;$i<$navPageCount;$i++) {
            $pager .= "<a href='?$searchstringvarname=" . $this->he($this->searchstring) . "&amp;$pagevarname=" . $NAV_PAGES[$i]["P"] . "$add2query' class='" . $NAV_PAGES[$i]["PCLASS"] . "'>" . $NAV_PAGES[$i]["P"] . "</a>" . $NAV_PAGES[$i]["SEP"];
          }
          $pager .= "</p>";
        break;
      }
    }
    return $pager;
  }
  
  //Example how to handle searchresults
  //$pagevarname is the name of the variable to set page
  //$searchstringvarname is the name of the searchstring variable
  function drawSearchresults($pagevarname = "p", $searchstringvarname = "q", $add2query = false) {
    if (!$this->searchstring) {
      //stripped Tags???
      if ($this->strippedTagsFromQuery) $output = $this->errorStrippedTags;
      //otherwise
      else $output = sprintf($this->infoNoSearchTerm);
    } elseif ($this->searchstring && strlen($this->searchstring) < $this->minlength) {
      $output = sprintf($this->errorToShort,$this->minlength);
    } elseif (!$this->searchcount) {
      $output = sprintf($this->errorNothingFound,$this->searchstring);
    } else {
      $output = "<b>" . $this->searchcount . " " . ($this->searchcount == 1 ? "Result" : "Results") . " for " . $this->searchstring . "</b>";
      
      $output .= $this->drawPagination($pagevarname, $searchstringvarname, $add2query);
      $output .=  "<hr />";
      for ($i=0;$i<count($this->searchresults);$i++) {
        $thisres = $this->searchresults[$i];
        $output .= ($thisres["isPDF"] ? "<img src='" . $this->lsurl . "icon-pdf.png' alt='[PDF]' class='lsico' /> " : "") . 
          "<a href='" . $thisres["url"] . "'><b>" . $thisres["title"] . "</b></a> <i>" . $thisres["host"] . "</i><div class='lssrchres'>" . $thisres["content"] . "</div><br />";
      }
      $output .= "<hr />";  
      $output .= $this->drawPagination($pagevarname, $searchstringvarname, $add2query);
    }
    return $output;
  }
 
  //display collected urls
  function showUrls() {
    if (!file_exists($this->cachedir. "/data.php")) return false;
    $output = array();
    include($this->cachedir. "/data.php");
    $cnt = count($cache_data);
    for ($i=0;$i<$cnt;$i++) {
      $output[] = $cache_data[$i]["url"];
    }
    return $output;
  }
  
  //turn breaks into \n
  function br2nl($text) {
    $text = str_replace(array("<br />","<br>","<br/>","<p>","</p>"),"\n",$text);
    return $text;
  }
  
  //short htmlentities checker - converts all quotes
  function he($str) {
    $str = htmlentities($str, ENT_QUOTES);
    return $str;
  }
  
  //search in string, highlight string and cut off section 
  function highlight($txt,$srch) {
    $srchArr = $srch;
    unset($srch);
    if (mb_stripos($txt,$this->searchstring)) {
      $srch = $this->searchstring;
      $foundCompleteSearchString = true;
    } else {
      foreach ($srchArr as $srchItem) {
        if (mb_stripos($txt,$srchItem)) {
          $srch = $srchItem;
          break;
        }
      }
    }
    if (!$srch) $srch = strtok($srchArr[0]," ");
    //highlighting and cutting
    $max_len = 250;
    if (!$txt || !$srch) return false;
    $txt = strip_tags($txt);

    //check position of searchstring in txt and cutting the correct part out
    $srch_pos = mb_stripos($txt,$srch);
    //maybe not found because of umlauts
    if ($srch_pos === false) $srch_pos = mb_stripos($txt,str_replace(array("ä","ö","ü","ß","Ä","Ö","Ü"),array("&auml;","&ouml;","&uuml;","&szlig;","&Auml;","&Ouml;","&Uuml;"),$srch));
    $txt_len = strlen($txt);
    if ($srch_pos) {
      if ($txt_len > $max_len) {
        //startposition for our cut
        if ($srch_pos > 120) {
          $cut_start = $srch_pos - 100;
          //search for whitespace
          $cut_start = strpos($txt," ",$cut_start);
        } else $cut_start = 0;
        //search for endposition
        if (($cut_start + $max_len + 20) < $txt_len) {
          $cut_end = strpos($txt," ",$cut_start+$max_len);
          $cut_length = $cut_end-$cut_start;
        } else {
          $cut_end = $cut_length = $max_len;
        }
        $txt = ($cut_start > 0 ? "..." : "") . (strlen($txt)<$max_len ? $txt : substr($txt,$cut_start,$cut_length) . "...");
      }
    } else {
      if (strlen($txt) >= $max_len) {
        $text_pos = strpos($txt," ",$max_len) + 1;
        $txt = ($txt_start_pos > 0 ? "..." : "") . (strlen($txt)<$max_len ? $txt : substr($txt,0,$text_pos) . "...");
      }
    }
    if (count($srchArr) > 1 && !$foundCompleteSearchString) {
      //don't replace each string at once, but all in just one step :)
      $txt = $this->str_highlight_multi($txt,$srchArr);
      return $txt;
    } else {
      return $this->str_highlight($txt,$srch);
    }
  }
  
  //short highlighter function
  function str_highlight($text, $needle) {    
    $needle = preg_quote($needle,"/");
    return preg_replace("/($needle)/i", "<strong class=\"" . $this->highlightClass . "\">$1</strong>",$text);
  }

  //multi  highlighter function
  function str_highlight_multi($text, $needles) {    
    $needle = preg_quote($needle,"/");
    return preg_replace("/(" . implode("|",$needles) . ")/i", "<strong class=\"" . $this->highlightClass . "\">$1</strong>",$text);
  }
  
  
  function makeAbsolute($rel,$base) {
    /* return if already absolute URL */
    /*if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;*/
    //prior 5.1.2 fix
    $t = parse_url($rel);
    if ($t["scheme"] != '') return $rel; 
    /* queries and anchors */
    if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;
    /* parse base URL and convert to local variables: $scheme, $host, $path */
    extract(parse_url($base));
    //add slash if slashed $base exists
    if (!preg_match('/\/$/',$path) && !preg_match('/\.(.*){3,4}$/i',$path) && !in_array("$scheme://$host$path/",$this->notfoundUrls)) {
      //curl
      if ($this->useCurl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$scheme://$host$path/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        $headerCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch);
        if ($headerCode < 400) $path .= "/";
        else $this->notfoundUrls[] = "$scheme://$host$path/";
      }
      //allow_url_fopen
      elseif ($this->useUrlFopen) {
        $tmp = @fopen("$scheme://$host$path/","r");
        if ($tmp) $path .= "/";
        else $this->notfoundUrls[] = "$scheme://$host$path/";
        fclose($tmp);
      }
    }
    /* remove non-directory element from path */
    $path = preg_replace('#/[^/]*$#', '', $path);
    /* destroy path if relative url points to root */
    if ($rel[0] == '/') $path = '';
    /* dirty absolute URL */
    $abs = "$host$path/$rel";
    /* replace '//' or '/./' or '/foo/../' with '/' */
    $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
    for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}
    /* absolute URL is ready! */
    return $scheme.'://'.$abs;
  }
    
  //$searchstringvarname is the name of the searchstring variable
  function printSrchCloud($searchstringvarname = "q", $add2query = false) {
    $srchfilename = "SearchStrings.php";
    $srchfile = $this->cachedir . "/" . $srchfilename;
    if (!file_exists($srchfile)) return false;
    else include($srchfile);  
    if (!$q) return false;
    arsort($q);
    //ghet max and min occurences
    $max = max(array_values($q));
    $min = min(array_values($q));
    //define range of values
    $gap = $max - $min;
    if (!$gap) $gap = 1; // prevent of division by zero
   
    // set the font-size increment
    $step = ($this->cloud_max - $this->cloud_min)/$gap;
   
    // loop through the tag array
    $i = 0;
    foreach ($q as $key => $val) {
      if ($this->maxCloudItems && $i++ > $this->maxCloudItems) break;
      // calculate font-size
      // find the $value in excess of $min_qty
      // multiply by the font-size increment ($size)
      // and add the $min_size set above
      $size = round($this->cloud_min + (($val - $min) * $step));
      $output .= "<a href='" . $this->searchresultspage . "?$searchstringvarname=" . $this->he($key) . "$add2query' style='font-size: ". $size . "px;' title='" . strip_tags($key) . " searched $val times'>" . strip_tags($key) . "</a> ";
    }
    return $output;
  }

    
  //authentication to the LiveSearch Manager
  function authenticate() {
    $user = $_REQUEST["user"];
    $pass = $_REQUEST["pass"];
    if (!$user) $this->mngrError[] = "Please type in your user name.";
    if (!$pass) $this->mngrError[] = "Please type in the password.";
    if (!$mngrError) {
      if ($user != $this->mngrUser || $pass != $this->mngrPass) {
        $this->mngrError[] = "Username and/or password are incorrect.";
      } else {
        $this->auth = $_SESSION["lsAuth"] = true;
      }
    }
  }
    
  //logout from the LiveSearch Manager
  function logout() {
    $this->auth = $_SESSION["lsAuth"] = false;
    $this->mngrSuccess = "You've been logged out successfully.";
  }


  
  
}
 
?>