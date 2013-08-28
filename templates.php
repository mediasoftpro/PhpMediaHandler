<?php
	// video and audio publishing templates
class MediaTemplates {
	
	// FLV Video Encoding
    // 0: FLV 240p
    // 1: FLV 360p
    // 2: FLV 480p
    // 3: FLV 720p
    // MP4 Video Encoding
    // 4: MP4 240p
    // 5: MP4 360p
    // 6: MP4 480p
    // 7: MP4 720p
    // 8: MP4 1080p
    // WebM Video Encoding
    // 9: WebM 240p
    // 10: WebM 360p
    // 11: WebM 480P
    // 12: WebM 720p
    // 13: WebM 1080p
    // MP3 Encoding
    // 14: mp3 audio
			
	public function returnSettings($id)
	{
		$params = array();
		// optional commands
		//$params[] = "-fs 10000000"; // limit filesize => 10MB
		//$params[] = "-t 10"; // create 10 second clip
		//$params[] = "-ss 10"; // start encoding after 10nth seconds
		//$params[] = "-metadata title='my title' // key=value pair
		
		$rootPath = $_SERVER['DOCUMENT_ROOT'] . "vsk/";
		$presetsFolder = "ffmpeg_aug_2013/presets/";
        switch($id)
		{
			case 0:
			  // 240p flv encoding
			  $params[] = "-s 400x240"; // video size
			  $params[] = "-b:v 256k"; // video bitrate
			  $params[] = "-bufsize 256k";
			  $params[] = "-b:a 64k"; // audio bitrate
			  $params[] = "-ar 22050"; // audio sampling rate
			  $params[] = "-f flv"; // force
			  break;
		   case 1:
		      // 360p flv encoding
		      $params[] = "-s 640x360";
			  $params[] = "-b:v 500k";
			  $params[] = "-bufsize 500k";
			  $params[] = "-b:a 128k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libx264";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libx264-ipod320.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
		      break;
		   case 2:
		      // 480p flv encoding
		      $params[] = "-s 854x480";
			  $params[] = "-b:v 1000k";
			  $params[] = "-bufsize 1000k";
			  $params[] = "-b:a 128k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libx264";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libx264-ipod640.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
		      break;
			case 3:
			  // 720p flv encoding
			  $params[] = "-s 1280x720";
			  $params[] = "-b:v 2000k";
			  $params[] = "-bufsize 2000k";
			  $params[] = "-b:a 128k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libx264";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libx264-ipod640.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
			  break;
			case 4:
			  // 240p mp4 encoding
			  $params[] = "-s 400x240";
			  $params[] = "-b:v 256k";
			  $params[] = "-bufsize 256k";
			  $params[] = "-b:a 64k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libx264";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libx264-ipod320.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
			  break;
			case 5:
			  // 360p mp4 encoding
			  $params[] = "-s 640x380";
			  $params[] = "-b:v 500k";
			  $params[] = "-bufsize 500k";
			  $params[] = "-b:a 128k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libx264";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libx264-ipod320.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
			  break;
			case 6:
			  // 480p mp4 encoding
			  $params[] = "-s 854x480";
			  $params[] = "-b:v 1000k";
			  $params[] = "-bufsize 1000k";
			  $params[] = "-b:a 128k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libx264";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libx264-ipod640.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
			  break;
		   case 7:
			  // 720p mp4 encoding
			  $params[] = "-s 1280x720";
			  $params[] = "-b:v 2200k";
			  $params[] = "-bufsize 2200k";
			  $params[] = "-b:a 128k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libx264";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libx264-ipod640.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
			  break;
		   case 8:
			  // 1080p mp4 encoding
			  $params[] = "-s 1920x1080";
			  $params[] = "-b:v 2900k";
			  $params[] = "-bufsize 2900k";
			  $params[] = "-b:a 152k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libx264";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libx264-ipod640.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
			  break;
		    case 9:
			  // 240p webm encoding
			  $params[] = "-s 400x240";
			  $params[] = "-b:v 256k";
			  $params[] = "-bufsize 256k";
			  $params[] = "-b:a 128k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libvpx";
			  $params[] = "-c:a vorbis";
			  $params[] = "-strict -2"; //for vorbis codec (as its experimental) use libvorbis if your ffmpeg build support it
			  $params[] = "-f webm";
			  $params[] = "-aspect 4:3";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libvpx-360p.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
			  break;
			case 10:
			  // 360p webm encoding
			  $params[] = "-s 640x380";
			  $params[] = "-b:v 500k";
			  $params[] = "-bufsize 500k";
			  $params[] = "-b:a 128k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libvpx";
			  $params[] = "-c:a vorbis";
			   $params[] = "-strict -2"; //for vorbis codec (as its experimental) use libvorbis if your ffmpeg build support it
			  $params[] = "-f webm";
			  $params[] = "-aspect 4:3";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libvpx-360p.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
			  break;
			case 11:
			  // 480p webm encoding
			  $params[] = "-s 854x480";
			  $params[] = "-b:v 1000k";
			  $params[] = "-bufsize 1000k";
			  $params[] = "-b:a 192k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libvpx";
			  $params[] = "-c:a vorbis";
			   $params[] = "-strict -2"; //for vorbis codec (as its experimental) use libvorbis if your ffmpeg build support it
			  $params[] = "-f webm";
			  $params[] = "-aspect 4:3";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libvpx-360p.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
			  break;
			case 12:
			  // 720p webm encoding
			  $params[] = "-s 1280x720";
			  $params[] = "-b:v 2200k";
			  $params[] = "-bufsize 2200k";
			  $params[] = "-b:a 192k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libvpx";
			  $params[] = "-c:a vorbis";
			   $params[] = "-strict -2"; //for vorbis codec (as its experimental) use libvorbis if your ffmpeg build support it
			  $params[] = "-f webm";
			  $params[] = "-aspect 4:3";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libvpx-720p.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
			  break;
			case 13:
			  // 1080p webm encoding
			  $params[] = "-s 1920x1080";
			  $params[] = "-b:v 2900k";
			  $params[] = "-bufsize 2900k";
			  $params[] = "-b:a 192k";
			  $params[] = "-ar 44100";
			  $params[] = "-c:v libvpx";
			  $params[] = "-c:a vorbis";
			   $params[] = "-strict -2"; //for vorbis codec (as its experimental) use libvorbis if your ffmpeg build support it
			  $params[] = "-f webm";
			  $params[] = "-aspect 4:3";
			  $presetpath =  $rootPath . "" . $presetsFolder . "/libvpx-1080p.ffpreset";
			  $params[] = "-fpre " . escapeshellarg($presetpath);
			  break;
			case 14:
			   // mp3 audio encoding
			   $params[] = "-vn";
			   break;
		}
		return $params;
	}
	
	public function returnExtension($id)
	{
		$extension = "";
		switch($id)
		{
			case 0:
			case 1:
			case 2:
			case 3:
			   $extension = ".flv";
			   break;
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			   $extension = ".mp4";
			   break;
			case 9:
			case 10:
			case 11:
			case 12:
			case 13:
			   $extension = ".webm";
			   break;
			case 14:
			   $extension = ".mp3";
			   break;
			 
		}
		return $extension;
	}
}
?>