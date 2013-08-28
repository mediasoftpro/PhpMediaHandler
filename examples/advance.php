<?php
include_once("../encoder.php");

ini_set('max_execution_time', 3600);

$rootPath = $_SERVER['DOCUMENT_ROOT'] . "vsk/";
$servicePath =  $rootPath . "ffmpeg_aug_2013/bin/ffmpeg.exe";
//************************************************
// Multiple Thumbs Grabbing Example
//************************************************
$mhandler = new mhpEncoder();
$mhandler->rootPath = $rootPath;
$mhandler->servicePath = $servicePath;
$mhandler->sourcePath = $rootPath . "contents/hls/";
$mhandler->publishPath = $rootPath . "contents/hls/stream/";
$mhandler->thumbsDirectory = $rootPath . "contents/hls/stream/";
$mhandler->sourceFileName = "testvideo.mp4";
$mhandler->publishFileName = "split.mp4";
$mhandler->grabThumbs = false;
$itags = array();
//$itags[] = 0; // 240p flv encoding
$itags[] = 0; // 480p flv encoding

//$itags[] = 5; // 360p mp4 encoding
//$itags[] = 6; // 480 mp4 encoding
//$itags[] = 14; // mp3 encoding
//$itags[] = 13; // webm encoding
$mhandler->itags = $itags;

$info = $mhandler->Process();
if($info->errorcode > 0)
{
	echo ".............................................<br />";
	echo "Error Code: " . $info->errorcode;
	echo "<br />" . $info->errorMessage;
	echo "<br />.............................................<br />";
	exit;
}
// preset file
/*$presetPath = escapeshellarg($rootPath . "ffmpeg_july_2012/presets/libx264-ipod640.ffpreset");
$mhandler->parameters = "-fpre " . $presetPath . " -vcodec libx264 -s 640x380 -b 500k -ab 128k -ar 44100 -y";
$totalclips = 5;
$length = 10; // length of each clip in seconds
$info = $mhandler->splitVideo($length, $totalclips);
if($info->errorcode > 0)
{
	echo ".............................................<br />";
	echo "Error Code: " . $info->errorcode;
	echo "<br />" . $info->errorMessage;
	echo "<br />.............................................<br />";
	exit;
} */
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
$output .= "......................................................<br />";
$output .= "Output Information<br />";
$output .= "......................................................<br />";
$output .= "<strong>Size:</strong> " . $info->i_width . 'x' . $info->i_height . "<br />";
if($info->i_vBitrate != "")
   $output .= "<strong>Video Bitrate:</strong> " . $info->i_vBitrate . "<br />"; 
if($info->i_aBitrate != "")
   $output .= "<strong>Audio Bitrate:</strong> " . $info->i_aBitrate . "<br />";
if($info->i_SamplingRate != "")
   $output .= "<strong>Sampling Rate:</strong> " . $info->i_SamplingRate . "<br />";
if($info->i_vCodec != "")
   $output .= "<strong>Video Codec:</strong> " . $info->i_vCodec . "<br />";  
if($info->i_aCodec != "")
   $output .= "<strong>Audio Codec:</strong> " . $info->i_aCodec . "<br />";  
if($info->i_channel != "")
   $output .= "<strong>Channel:</strong> " . $info->i_channel . "<br />";

$output .= "<strong>Has Video:</strong> " . (boolean)$info->i_hasaudio . "<br />";
$output .= "<strong>Has Audio:</strong> " . (boolean)$info->i_hasaudio . "<br />";
echo $output;
?>