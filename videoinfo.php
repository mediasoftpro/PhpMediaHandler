<?php

class videoInfo {
   
    public $processid ;
	public $processsize;
	public $ProcessedTime;
	public $TotalSize;
	public $ProcessingLeft;
	public $ProcessingCompleted;
    public $errorcode;
    public $duration;
	public $duration_sec;
	public $sourceFileName;
	public $fileName;
	public $thumbFileName;
	public $thumbstartindex; 
	public $isenabled ; // 0: disabled, 1: enabled
	public $errordescription;
	
	public $hours;
	public $minutes;
	public $seconds;
	public $start;
	public $errorMessage;
	public $ffmpegOutput;
	public $bitrate;
	// output parameters
	public $aCodec;
	public $vCodec;
	public $samplingRate;
	public $channel;
	public $aBitrate;
	public $vBitrate;
	public $width;
	public $height;
	public $frameRate;
	public $hasaudio;
	public $hasvideo;
	// inputer parameters
	public $i_aCodec;
	public $i_vCodec;
	public $i_SamplingRate;
	public $i_channel;
	public $i_aBitrate;
	public $i_vBitrate;
	public $i_width;
	public $i_height;
	public $i_hasaudio;
	public $i_hasvideo;
	// other parameters
	public $artist;
	public $title;
	public $copyright;
	public $genre;
	public $tracknumber;
	public $pixelformat;
	
	function __construct()
	{
		$this->processid = 0;
		$this->processsize = "";
		$this->ProcessedTime = "";
		$this->TotalSize = "";
		$this->ProcessingLeft = 0;
		$this->ProcessingCompleted = 0;
		
		$this->errorcode = 0;
		$this->duration = "";
		$this->duration_sec = 0;
		$this->sourceFileName = "";
		$this->fileName = "";
		$this->thumbFileName = "";
		$this->thumbstartindex = "";
		$this->isenabled = 1;  
		$this->errordescription = "";
		
		$this->hours = "";
	    $this->minutes = "";
	    $this->seconds = "";
	    $this->start = "";
	    $this->errorMessage = "";
	    $this->ffmpegOutput = "";
		$this->bitrate = "";
	    // output parameters
	    $this->aCodec = "";
	    $this->vCodec = "";
	    $this->samplingRate = "";
	    $this->channel = "";
	    $this->aBitrate = "";
	    $this->vBitrate = "";
	    $this->width = "";
	    $this->height = "";
	    $this->frameRate = "";
	    $this->hasaudio = false;
		$this->hasvideo = false;
	    // inputer parameters
	    $this->i_aCodec = "";
	    $this->i_vCodec = "";
	    $this->i_SamplingRate = "";
	    $this->i_channel = "";
	    $this->i_aBitrate = "";
	    $this->i_vBitrate = "";
	    $this->i_width = "";
	    $this->i_height = "";
		$this->i_hasaudio = false;
	    $this->i_hasvideo = false;
	    // other parameters
	    $this->artist = "";
	    $this->title = "";
		$this->copyright = "";
		$this->genre = "";
		$this->tracknumber = 0;
		$this->pixelformat = "";
	}
}
?>
