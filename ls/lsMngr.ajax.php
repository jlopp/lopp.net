<?php

ini_set("session.gc_maxlifetime",84600);
@session_start();
include ("livesearch.class.php");
$ls = new LiveSearch();

if (!$ls->auth) exit("permission denied, please authenticate");

$action = $_REQUEST["action"];
$error = array();
$success = false;


//clear search strings
if ($action == "clearSrchStr") {

	if (!file_exists($ls->cachedir . "/SearchStrings.php")) $error[] = "Stored search strings are already cleared.";
	if (!$error) {
		$ls->clearSrchStr();
		$success = "Search string removed successfully.";
	}
 
  echo json_encode(array("error" => $error ? implode("<br />",$error) : false, "success" => $success));
  exit;
}


//clear search results
if ($action == "clearSrch") {

	if (!glob($ls->cachedir . "/*.srch")) $error[] = "Stored search results are already removed.";
	if (!$error) {
		$ls->clearSrch();
		$success = "Search results removed successfully.";
	}
 
  echo json_encode(array("error" => $error ? implode("<br />",$error) : false, "success" => $success));
  exit;
}


//clear cache
if ($action == "clearCache") {

	if (!$error) {
		$ls->clearCache();
		$success = "All cached files were removed successfully.";
	}
 
  echo json_encode(array("error" => $error ? implode("<br />",$error) : false, "success" => $success));
  exit;
}

//cache files
if ($action == "cacheFiles") {

	if (!file_exists($ls->cachedir . "/data.php") && file_exists($ls->cachedir . "/tmpContentUrls.php")) {
		$error[] = "It seems that an indexing/caching process is currently running, jumping in ...<br />If you are not sure  if this is okay or caused by something like an interrupted, please try again later or delete cache before trying to cache.";
	}

	if (!$error) {
		ob_end_clean();
		header("Connection: close");
		ignore_user_abort();
		ob_start();
		echo json_encode(array("success" => true));
		$size = ob_get_length();
		header("Content-Length: $size");
		ob_end_flush();
		flush();
		session_write_close();
		$ls->cacheFiles();
		exit;
	}
 
  echo json_encode(array("error" => $error ? implode("<br />",$error) : false, "success" => $success));
  exit;
}


//get cache status
if ($action == "getCacheFilesStatus") {

	$tempFiles = count(glob($ls->cachedir . "/*.tmp"));
	$dataFiles = count(glob($ls->cachedir . "/*.txt"));

	$statusMessage = "Analysing/Indexing Site ...";

	if ($dataFiles) {
		$tempFilesFinished = true;
		$statusMessage = "Caching pages ...";
	}

	//finished???
	if (file_exists($ls->cachedir . "/data.php")) {
		include($ls->cachedir . "/data.php");
		//images
    if ($cache_data_img) $dataImages = count($cache_data_img);
    //PDF
    $pdfSrchRes = array_count_values(array_map(function($value){return $value['isPDF'];}, $cache_data));
    $dataPdfs = intval($pdfSrchRes[1]);
    $dataFiles -= $dataPdfs;

		$success = "Caching finished successfully.";
		$statusMessage = "Caching finished";
		$finished = true;
		$tempFilesFinished = $dataFilesFinished = true;
	}

  echo json_encode(array("error" => $error ? implode("<br />",$error) : false, "success" => $success, 
  	"statusMessage" => $statusMessage, 
  	"finished" => $finished, 
  	"tempFiles" => $tempFiles, 
  	"dataFiles" => $dataFiles,
  	"tempFilesFinished" => $tempFilesFinished,
  	"dataFilesFinished" => $dataFilesFinished,
  	"dataImages" => intval($dataImages),
  	"dataPdfs" => intval($dataPdfs),
 	));
	exit;
}



?>