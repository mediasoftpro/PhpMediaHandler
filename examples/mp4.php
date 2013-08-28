<?php
include_once("../mediahandler.php");

ini_set('max_execution_time', 3600);

$rootPath = $_SERVER['DOCUMENT_ROOT'] . "vsk/";
$servicePath =  $rootPath . "ffmpeg_aug_2013/bin/ffmpeg.exe"; // use proper ffmpeg path in linux
$mp4boxPath =  $rootPath . "mp4box/MP4Box.exe"; // use proper mp4box path in linux
//************************************************
// Multiple Thumbs Grabbing Example
//************************************************
$mhandler = new MediaHandler();
$mhandler->servicePath = $servicePath;
$mhandler->inputPath = $rootPath . "contents/hls/";
$mhandler->outputPath = $rootPath . "contents/hls/stream/";
$mhandler->fileName = "testvideo.mp4";
$mhandler->outputfileName = "sample_meta.mp4";
// preset file

$presetPath = escapeshellarg($rootPath . "ffmpeg_aug_2013/presets/libx264-ipod640.ffpreset");
$mhandler->parameters = "-fpre " . $presetPath . " -s 640x380 -b:v 500k -bufsize 500k -b:a 128k -ar 44100 -c:v libx264 -y";
// post watermark
$watermarkPath = "watermark.png"; // watermark.png must be placed in folder where this page (.php) located
// Post Watermark on Top Left of Video
//$mhandler->parameters .= " -vf \"movie = " . $watermarkPath . " [watermark]; [in][watermark] overlay =10:10 [out]\"";
// Post Watermark on Top Right of Video
//$mhandler->parameters .= " -vf \"movie = " . $watermarkPath . " [watermark]; [in][watermark] overlay=main_w-overlay_w-10:10 [out]\"";
// Post Watermark on Bottom Left of Video
//$mhandler->parameters .= " -vf \"movie = " . $watermarkPath . " [watermark]; [in][watermark] overlay=10:main_h-overlay_h-10 [out]\"";
// Post Watermark on Bottom Right of Video
$mhandler->parameters .= " -vf \"movie = " . $watermarkPath . " [watermark]; [in][watermark] overlay=main_w-overlay_w-10:main_h-overlay_h-10 [out]\"";
$info = $mhandler->Process();
if($info->errorcode > 0)
{
	echo ".............................................<br />";
	echo "Error Code: " . $info->errorcode;
	echo "<br />" . $info->errorMessage;
	echo "<br />.............................................<br />";
	exit;
} 
//*****************************************************
// SET Meta Information for Published MP4 Video
//*****************************************************
$mhandler->servicePath = $mp4boxPath;
$mhandler->inputPath = $mhandler->outputPath; // where published flv located
$mhandler->fileName = $mhandler->outputfileName;
$mhandler->command = '-isma -hint ' .escapeshellarg($mhandler->inputPath . "" . $mhandler->fileName);
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