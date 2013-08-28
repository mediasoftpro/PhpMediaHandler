<?php
include_once("../mediahandler.php");

$rootPath = $_SERVER['DOCUMENT_ROOT'] . "vsk/";
$servicePath =  $rootPath . "ffmpeg/bin/ffmpeg.exe";
$flvtoolPath = $rootPath . "flvtool/flvtool2.exe";
//************************************************
// Multiple Thumbs Grabbing Example
//************************************************
$mhandler = new MediaHandler();
$mhandler->servicePath = $servicePath;
$mhandler->inputPath = $rootPath . "contents/hls/";
$mhandler->outputPath = $rootPath . "contents/hls/stream/";
$mhandler->fileName = "testvideo.mp4";
$mhandler->outputfileName = "sample_meta02.flv";
$mhandler->parameters = "-ab 64k -ar 22050 -s 320x240 -f flv -y";
$info = $mhandler->Process();
if($info->errorcode > 0)
{
	echo ".............................................<br />";
	echo "Error Code: " . $info->errorcode;
	echo "<br />: " . $info->errorMessage;
	echo "<br />.............................................<br />";
	exit;
} 
//*****************************************************
// SET Meta Information for Published FLV Video
//*****************************************************
$mhandler->servicePath = $flvtoolPath;
$mhandler->inputPath = $mhandler->outputPath; // where published flv located
$mhandler->fileName = $mhandler->outputfileName;
$mhandler->command ='-U ' . escapeshellarg($mhandler->inputPath . "" . $mhandler->fileName);
$vinfo = $mhandler->setMeta();
if($vinfo->errorcode > 0)
{
	echo "Meta Error Code: " . $vinfo->errorcode;
	echo "<br />Error: " . $vinfo->errorMessage;
}
echo "Meta Output: " . $vinfo->ffmpegOutput;
// fetch information
$output = "";
$output .= "<br />......................................................<br />";
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