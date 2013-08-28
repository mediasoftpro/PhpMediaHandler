<?php
include_once("include/config.php");

class encodersettings {

     var $defaultitagvalue = 5; // default encoding id (5: 360p mp4 encoding)
    // itag value is used to point which encoder setting is currently required.
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
	function returnmediasettings($media, $itag)
    {
        //MediaHandler media = new MediaHandler();
       $presetpath = ROOTPATH . "\\ffmpeg\\presets\\libx264-ipod320.ffpreset";
        switch ($itag)
        {
            case 0: // 240p flv video settings
                $media->Width = 400;
                $media->Height = 240;
                $media->Video_Bitrate = 256;
                $media->Audio_SamplingRate = 22050;
                $media->Audio_Bitrate = 64;
                $media->OutputExtension = ".flv";
                $media->Force = "flv";
                break;
            case 1: // 360p flv video settings
                $media->Width = 640;
                $media->Height = 360;
                $media->Video_Bitrate = 500;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 128;
                $media->OutputExtension = ".flv";
                $media->VCodec = "libx264";
                $media->Parameters = " -fpre \"" . $presetpath . "\"";
                break;
            case 2: // 480p flv video settings
                $media->Width = 854;
                $media->Height = 480;
                $media->Video_Bitrate = 1000;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 128;
                $media->OutputExtension = ".flv";
                $media->VCodec = "libx264";
                $media->Parameters = " -fpre \"" . $presetpath . "\"";
                break;
            case 3: // 720p flv video settings
                $media->Width = 1280;
                $media->Height = 720;
                $media->Video_Bitrate = 2000;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 128;
                $media->OutputExtension = ".flv";
                $media->VCodec = "libx264";
                $media->Parameters = " -fpre \"" . $presetpath . "\"";
                break;
            case 4: // 240p mp4 video encoding
                $media->Width = 400;
                $media->Height = 240;
                $media->Video_Bitrate = 256;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 128;
                $media->OutputExtension = ".mp4";
                $media->VCodec = "libx264";
                $media->Parameters = " -fpre \"" . $presetpath . "\"";
                break;
            case 5: // 360p mp4 video encoding
                $media->Width = 640;
                $media->Height = 380;
                $media->Video_Bitrate = 500;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 128;
                $media->OutputExtension = ".mp4";
                $media->VCodec = "libx264";
                $media->Parameters = " -fpre \"" . $presetpath . "\"";
                break;
            case 6: // 480p mp4 video encoding
                $media->Width = 854;
                $media->Height = 480;
                $media->Video_Bitrate = 1000;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 128;
                $media->OutputExtension = ".mp4";
                $media->VCodec = "libx264";
                $media->Parameters = " -fpre \"" . $presetpath . "\"";
                break;
            case 7: // 720p mp4 video encoding
                $media->Width = 1280;
                $media->Height = 720;
                $media->Video_Bitrate = 2200;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 96;
                $media->OutputExtension = ".mp4";
                $media->VCodec = "libx264";
                $media->Parameters = " -fpre \"" . $presetpath . "\"";
                break;
            case 8: // 1080p mp4 video encoding
                $media->Width = 1920;
                $media->Height = 1080;
                $media->Video_Bitrate = 2900;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 152;
                $media->OutputExtension = ".mp4";
                $media->VCodec = "libx264";
                $media->Parameters = " -fpre \"" . $presetpath . "\"";
                break;
            case 9: // 240p webm video encoding
                $media->Width = 400;
                $media->Height = 240;
                $media->Video_Bitrate = 256;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 128;
                $media->OutputExtension = ".webm";
                $media->VCodec = "libx264";
                $presetpath = ROOTPATH . "\\ffmpeg\\presets\\libvpx-360p.ffpreset";
                $media->VCodec = "libvpx";
                $media->ACodec = "libvorbis";
                $media->Parameters = "-f webm -aspect 4:3 -fpre \"" . $presetpath . "\"";
                break;
            case 10: // 360p webm video encoding
                $media->Width = 640;
                $media->Height = 380;
                $media->Video_Bitrate = 500;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 128;
                $media->OutputExtension = ".webm";
                $media->VCodec = "libx264";
                $presetpath = ROOTPATH . "\\ffmpeg\\presets\\libvpx-360p.ffpreset";
                $media->VCodec = "libvpx";
                $media->ACodec = "libvorbis";
                $media->Parameters = "-f webm -aspect 4:3 -fpre \"" . $presetpath . "\"";
                break;
            case 11: // 480p webm video encoding
                $media->Width = 854;
                $media->Height = 480;
                $media->Video_Bitrate = 1000;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 192;
                $media->OutputExtension = ".webm";
                $media->VCodec = "libx264";
                $presetpath = ROOTPATH . "\\ffmpeg\\presets\\libvpx-360p.ffpreset";
                $media->VCodec = "libvpx";
                $media->ACodec = "libvorbis";
                $media->Parameters = "-f webm -aspect 4:3 -fpre \"" . $presetpath . "\"";
                break;
            case 12: // 720p webm video encoding
                $media->Width = 1280;
                $media->Height = 720;
                $media->Video_Bitrate = 2200;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 192;
                $media->OutputExtension = ".webm";
                $media->VCodec = "libx264";
                $presetpath = ROOTPATH . "\\ffmpeg\\presets\\libvpx-360p.ffpreset";
                $media->VCodec = "libvpx";
                $media->ACodec = "libvorbis";
                $media->Parameters = "-f webm -aspect 4:3 -fpre \"" . $presetpath . "\"";
                break;
            case 13: // 1080p webm video encoding
                $media->Width = 1920;
                $media->Height = 1080;
                $media->Video_Bitrate = 2900;
                $media->Audio_SamplingRate = 44100;
                $media->Audio_Bitrate = 192;
                $media->OutputExtension = ".webm";
                $media->VCodec = "libx264";
                $presetpath = ROOTPATH . "\\ffmpeg\\presets\\libvpx-360p.ffpreset";
                $media->VCodec = "libvpx";
                $media->ACodec = "libvorbis";
                $media->Parameters = "-f webm -aspect 4:3 -fpre \"" . $presetpath . "\"";
                break;
            case 14: // mp3 audio encoding
                //media.DisableVideo = true;
                // media.Channel = 2;
                //media.Audio_SamplingRate = 4800;
                //media.Audio_Bitrate = 192;
                $media->OutputExtension = ".mp3";
                break;
        }
        return $media;
    }


    function return_output_extension($itag)
    {
        $extension = "";
        switch ($itag)
        {
            case 0:
                $extension = "flv";
                break;
            case 1:
                $extension = "flv";
                break;
            case 2:
                $extension = "flv";
                break;
            case 3:
                $extension = "flv";
                break;
            case 4:
                $extension = "mp4";
                break;
            case 5:
                $extension = "mp4";
                break;
            case 6:
                $extension = "mp4";
                break;
            case 7:
                $extension = "mp4";
                break;
            case 8:
                $extension = "mp4";
                break;
            case 9:
                $extension = "webm";
                break;
            case 10:
                $extension = "webm";
                break;
            case 11:
                $extension = "webm";
                break;
            case 12:
                $extension = "webm";
                break;
            case 13:
                $extension = "webm";
                break;
            case 14:
                $extension = "mp3";
                break;
        }
        return $extension;
    }
	
}
?>
