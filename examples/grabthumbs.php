<?php
include_once("../mediahandler.php");

$rootPath = $_SERVER['DOCUMENT_ROOT'] . "vsk/";
$servicePath =  $rootPath . "ffmpeg/bin/ffmpeg.exe";
//************************************************
// Multiple Thumbs Grabbing Example
//************************************************
$mhandler = new MediaHandler();
$mhandler->servicePath = $servicePath;
$mhandler->inputPath = $rootPath . "contents/hls/";
$mhandler->outputPath = $rootPath . "contents/hls/stream/";
$mhandler->fileName = "testvideo.mp4";
$mhandler->outputfileName = "sample";
$mhandler->parameters = "-f image2 -s 120x100";
// thumb options
$mhandler->isMultiple = true;
$mhandler->totalThumbs = 34; 
$mhandler->startPosition = 5;
$mhandler->imageFormat = ".jpg";
$info = $mhandler->grabThumbs();
if($info->errorcode > 0)
{
	echo ".............................................<br />";
	echo "Error Code: " . $info->errorcode;
	echo "<br />: " . $info->errorMessage;
	echo "<br />.............................................<br />";
	exit;
} 
//************************************************
// Single Thumb Grab Example
//************************************************
/*$mhandler = new MediaHandler();
$mhandler->servicePath = $servicePath;
$mhandler->inputPath = $rootPath . "contents/hls/";
$mhandler->outputPath = $rootPath . "contents/hls/stream/";
$mhandler->fileName = "testvideo.mp4";
$mhandler->outputfileName = "sample";
$mhandler->parameters = "-f image2 -s 120x100";
// thumb options
$mhandler->isMultiple = false;
// calculate mid duration of video
$startposition = 0;
$vinfo = $mhandler->getInfo(false);
if($vinfo->duration_sec >0)
  $startposition = (int)($vinfo->duration_sec/2);
else
  $startposition = 5;
$mhandler->startPosition = $startposition;
$mhandler->imageFormat = ".jpg";
$info = $mhandler->grabThumbs();
if($info->errorcode > 0)
{
	echo ".............................................<br />";
	echo "Error Code: " . $info->errorcode;
	echo "<br />: " . $info->errorMessage;
	echo "<br />.............................................<br />";
	exit;
} */
// fetch information
echo "thumbs grabbed";
?>