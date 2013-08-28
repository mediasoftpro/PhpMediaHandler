<?php
include_once("../mediahandler.php");

$rootPath = $_SERVER['DOCUMENT_ROOT'] . "vsk/";
$servicePath =  $rootPath . "ffmpeg/bin/ffmpeg.exe";
// Get Media Info
$mhandler = new MediaHandler();
$mhandler->servicePath = $servicePath;
$mhandler->inputPath = $rootPath . "contents/hls/";
$mhandler->fileName = "testvideo.mp4";
$info = $mhandler->getInfo();
if($info->errorcode > 0)
{
	echo ".............................................<br />";
	echo "Error Code: " . $info->errorcode;
	echo "<br />: " . $info->errorMessage;
	echo "<br />.............................................<br />";
	exit;
} 
// fetch information
$output = "";
$output .= "......................................................<br />";
$output .= "<strong>Duration:</strong> " . $info->duration . "<br />";
$output .= "<strong>Duration Seconds:</strong> " . $info->duration_sec . "<br />";
$output .= "<strong>Size:</strong> " . $info->width . 'x' . $info->height . "<br />";
$output .= "......................................................<br />";
$output .= "Detail Information<br />";
$output .= "......................................................<br />";
$output .= "<strong>Frame Rate:</strong> " . $info->frameRate . "<br />";
if($info->title != "")
   $output .= "<strong>Title:</strong> " . $info->title . "<br />";
if($info->artist != "")
   $output .= "<strong>Artist:</strong> " . $info->artist . "<br />";
if($info->copyright != "")
   $output .= "<strong>Copyright:</strong> " . $info->copyright . "<br />";
if($info->genre != "")
   $output .= "<strong>Genre:</strong> " . $info->genre . "<br />";
if($info->tracknumber != "")
   $output .= "<strong>Track Number:</strong> " . $info->tracknumber . "<br />";   
if($info->pixelformat != "")
   $output .= "<strong>Pixel Format:</strong> " . $info->pixelformat . "<br />";  
if($info->bitrate != "")
   $output .= "<strong>Bitrate:</strong> " . $info->bitrate . "<br />";  
if($info->vBitrate != "")
   $output .= "<strong>Video Bitrate:</strong> " . $info->vBitrate . "<br />"; 
if($info->aBitrate != "")
   $output .= "<strong>Audio Bitrate:</strong> " . $info->aBitrate . "<br />";
if($info->samplingRate != "")
   $output .= "<strong>Sampling Rate:</strong> " . $info->samplingRate . "<br />";
if($info->vCodec != "")
   $output .= "<strong>Video Codec:</strong> " . $info->vCodec . "<br />";  
if($info->aCodec != "")
   $output .= "<strong>Audio Codec:</strong> " . $info->aCodec . "<br />";  
if($info->channel != "")
   $output .= "<strong>Channel:</strong> " . $info->channel . "<br />";

$output .= "<strong>Has Video:</strong> " . (boolean)$info->hasaudio . "<br />";
$output .= "<strong>Has Audio:</strong> " . (boolean)$info->hasaudio . "<br />";
echo $output;
?>