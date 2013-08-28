PHP Media Handler Pro
*********************************************************

PHP Media Handler Pro is a utility script & ffmpeg wrapper for publishing videos and audio files, set meta information to make videos streamable on the web, grab single or 

multiple thumbs, post watermark, split videos into smaller clips and perform advance multi level (240p, 360p etc), multi format (flv, mp4, webm, mp3) media processing from 

source video while using few lines of code.

Sample code for performing all these operations 

Publish source video to 360p mp4 video

Publish source video to 720p mp4 video (for HD or paid view)

Publish source video to 480p webm video

Set meta for mp4 videos to make it streamable on web.

Publish source video to mp3 audio

Grab 15 thumbs from start to end of video (for listing preview)

Post watermark on video

Fetch information at the end of operation

in few lines of code

***************************************
Sample Code
***************************************

include_once("encoder.php");

ini_set('max_execution_time', 3600); // set max execution time

$servicePath =  $rootPath . "ffmpeg/"; // ffmpeg path

$mhandler = new mhpEncoder();

$mhandler->rootPath = $rootPath;

$mhandler->servicePath = $servicePath;

$mhandler->sourcePath = $rootPath . "contents/source/";

$mhandler->publishPath = $rootPath . "contents/published/";

$mhandler->thumbsDirectory = $rootPath . "contents/thumbs/";

$mhandler->sourceFileName = "sample.avi";

$mhandler->publishFileName = "sample.mp4";

$mhandler->grabThumbs = true;

$itags = array();

// read more about encoding templates click here

$itags[] = 5; // 360p mp4 encoding

$itags[] = 7; // 720p mp4 encoding

$itags[] = 11; // 480p webm encoding

$itags[] = 14; // mp3 encoding

$mhandler->itags = $itags;

$mhandler->postWatermark = true; // enable post watermark

$mhandler->watermarkPath = "watermark.png";

$mhandler->watermarkLocation = 3; // watermark on bottom right

$info = $mhandler->Process();

if($info->errorcode > 0)

{

	echo "Error Code: " . $vinfo->errorcode . ", Message: " . $vinfo->errorMessage;

	exit;
	
} 

echo returnOutput($info);

For complete documentation and sample codes visit

http://www.mediasoftpro.com/php/media-handler-pro/
