<?php
// ffmpeg wrapper
require_once "videoinfo.php";

class MediaHandler {
	
	public $servicePath;
	public $inputPath;
	public $outputPath;
	public $fileName;
	public $outputfileName;
	public $parameters; // additional parameters except input and output paths
	public $start_parameters; // parameters before setting input file (before -i command)
	public $command; // direct command to execute
	
	// thumbs related
	public $isMultiple; // grab single image
	public $totalThumbs; // in case of multiple
	public $startPosition; // start grabbing thumbs from 5th seconds
	public $imageFormat; 
	
	function __construct()
	{
		$this->servicePath = "";
		$this->inputPath = "";
		$this->outputPath = "";
		$this->fileName = "";
		$this->outputfileName = "";
		$this->parameters = "";
		$this->start_parameters = "";
		$this->command = "";
		$this->isMultiple = false;
		$this->totalThumbs= 15;
		$this->startPosition = 5;
		$this->imageFormat = ".jpg";
	}
	
	/* get media inforation */
	function getInfo($detailInfo = true)
	{
		$info = new videoInfo();
		$info = $this->isValid($info, false);
		if($info->errorcode > 0)
		  return $info;
		  
		try
		{
			$cmd = '';
			if($this->command != "")
			   $cmd = $this->command;
			else
			{
				$sPath = escapeshellarg($this->servicePath);
			    if(!$this->endsWith($this->inputPath, "/"))
			       $this->inputPath . "/";
			    $iPath = escapeshellarg($this->inputPath . "". $this->fileName);
				$cmd = $sPath . " -i " . $iPath;
			}
			
		    $output = $this->executeCommand($cmd, true);
  
		    $info = $this->parseOutput($output, $detailInfo);
			
			$info->ffmpegOutput = $output;

		}
		catch (Exception $e) 
        {
            $info->errorcode = 200; // uncatch
			$info->errorMessage = $e->getMessage();
        }
		return $info;
	}
	/* core function for media processing */
	function Process($detailInfo = true)
	{
		$info = new videoInfo();
		
		$info = $this->isValid($info);
		if($info->errorcode > 0)
		  return $info;
        
		try
		{
		  $output = $this->executeCommand();
		
		  $info = $this->parseOutput($output, $detailInfo);
       
	      $info->ffmpegOutput = $output;
		}
		catch (Exception $e) 
        {
            $info->errorcode = 200; // uncatch
			$info->errorMessage = $e->getMessage();
        }
		return $info;
	}
	
	/* set meta information to mp4, flv and other video formats */
	function setMeta()
	{
		$info = new videoInfo();
		$info = $this->isValid($info, false);
		if($info->errorcode > 0)
		  return $info;
		  
		if($this->command == "")
		{
			$info->errorcode = 175;
			$info->errorMessage = "Service command missing";
		}
		try
		{			
			$sPath = escapeshellarg($this->servicePath);
			$cmd = $sPath . ' ' . $this->command;

		    $output = $this->executeCommand($cmd, true);
		
			$info->ffmpegOutput = $output;

		}
		catch (Exception $e) 
        {
            $info->errorcode = 200; // uncatch
			$info->errorMessage = $e->getMessage();
        }
		return $info;
	}
	/* Grab single or multiple thumbes */
	function grabThumbs()
	{
		$info = new videoInfo();
		// validation
		$info = $this->isValid($info);
		if($info->errorcode > 0)
		  return $info;
		  
		try
		{
			
			$fileName = $this->fileName;
			if($this->outputfileName != "")
			  $fileName = $this->outputfileName;
			if (strpos($fileName, ".") !== false)
			     $fileName = substr($fileName, 0, $this->lastIndexOf($fileName, "."));
			if (!$this->startsWith($this->imageFormat, "."))
				 $this->imageFormat = "." . $this->imageFormat;	
			$sPath = escapeshellarg($this->servicePath);
			$iPath = escapeshellarg($this->inputPath . "". $this->fileName);	 
			
			$output = '';
		    if($this->isMultiple)
			{
				// multiple thumbs
				if($this->totalThumbs < 1)
				  $this->totalThumbs = 1;
				
				// retrieve source video duration
				$vinfo = $this->getInfo(false);
				$duration = 0;
				if($vinfo->errorcode == 0)
				  $duration = $vinfo->duration_sec;
				  
				if($duration == 0)
				{
					$info->errorcode = 125;
					$info->errorMessage = "failed to fetch duration from video";
					return $info;
				}
				
				// set transition time between two duration
				$transition = 1; // 1 second
				if($this->startPosition > 0)
				   $duration = $duration - $this->startPosition;
				
				$tthumbs = $this->totalThumbs;
				if($this->totalThumbs ==1)
				   $tthumbs = 2;
				
				$transition = (int)$duration/$tthumbs;
				if($transition == 0)
				  $transition = 1;
				  
				 $counter = 1;
				 $thumb_index = "";
				 $seekpos = $transition;
				 for ($i = 0; $i <= $this->totalThumbs - 1; $i++)
				 {				
					 if ($counter < 10)
						  $thumb_index = "00" . $counter;
					 else if ($counter >= 10 && $counter < 100)
						  $thumb_index = "0" . $counter;
					 else
						  $thumb_index = $counter;
				 
					 $oFile = escapeshellarg($this->outputPath . "" . $fileName . "" . $thumb_index . "" . $this->imageFormat);
					
					 $cmd = $sPath;
					 $cmd .= " -ss " . $seekpos . " -i " . $iPath . " -vframes 1";
					 if($this->parameters != "")
					   $cmd .= " " . trim($this->parameters);
					 $cmd .= " -y " . $oFile;
					 // process command
					 $output = $this->executeCommand($cmd);
					 
					 $seekpos = $seekpos + $transition;

                    if ($seekpos > $duration)
                        $seekpos = $duration;

                     $counter++;
				 }
				 
				 // check for errors 
				 $errors = $this->getErrors($output);
				 if($errors != "")
				 {
					$info->errorcode = 300;
				    $info->errorMessage = $errors;
					return $info;
				 }
			}
			else
			{
				// single thumb
                $seekpos = "5"; // default 5 seconds
				if($this->startPosition > 0)
				  $seekpos = $this->startPosition;
 
				$oFile = escapeshellarg($this->outputPath . "" . $fileName . "" . $this->imageFormat);
				$cmd = $sPath;
				$cmd .= " -ss " . $seekpos . " -i " . $iPath . " -vframes 1";
				if($this->parameters != "")
				   $cmd .= " " . trim($this->parameters);
				$cmd .= " -y " . $oFile; 
				
				// process command
				$output = $this->executeCommand($cmd); 
				$errors = $this->getErrors($output);
				if($errors != "")
				{
					$info->errorcode = 300;
				    $info->errorMessage = $errors;
					return $info;
				}
			}
			
			return $info;
		}
		catch (Exception $e) 
        {
            $info->errorcode = 200; // uncatch
			$info->errorMessage = $e->getMessage();
        }		  
		
	}
	
	// split video into clips
	public function splitVideo($length, $totalClips)
	{
		$info = new videoInfo();
		$info = $this->isValid($info);
		if($info->errorcode > 0)
		  return $info;
		  
		// retrieve source video duration
		$vinfo = $this->getInfo(false);
		$duration = 0;
		if($vinfo->errorcode == 0)
		  $duration = $vinfo->duration_sec;
		  
		if($duration == 0)
		{
			$info->errorcode = 125;
			$info->errorMessage = "failed to fetch duration from media file";
			return $info;
		}
		if($length < 0)
		   $length = 0;
		if($totalClips < 0)
		   $totalClips = 0;
		if($length == 0 && $totalClips == 0)
		{
			$info->errorcode = 186;
			$info->errorMessage = "must provide length of each clip or total no of clips to be created";
			return $info;
		}
		
		if($length > 0 && $totalClips > 0)
		{
			$total = $length * $totalClips;
			if($total > $duration)
			{
				$info->errorcode = 187;
			    $info->errorMessage = "total length exceeds from actual video length";
			    return $info;
			}
		}
		else if($length > 0 && $totalClips == 0)
		{
			// calculate total no of clips
			$totalClips = (int)(floor($duration / $length));
		}
		else if($length == 0 && $totalClips > 0)
		{
			// calculate total length of each clip
			$length = (int)(floor($duration / $totalClips));
		}
		
		try
		{		
		    $fName = $this->fileName;
			if($this->outputfileName != "")
			  $fName = $this->outputfileName;
			$extension = "";
			$fileName = substr($fName, 0, $this->lastIndexOf($fName, "."));
			$extension = substr($fName, $this->lastIndexOf($fName, "."));
			
		    $index = "0";	
			
			$sPath = escapeshellarg($this->servicePath);
			$iPath = escapeshellarg($this->inputPath . "". $this->fileName);	
			
			$seekpos = 0;
			$commands = "";
			for ($i = 0; $i <= $totalClips - 1; $i++)
			{
				if ($i >= 10)
                   $index = "";
                
				$oPath = escapeshellarg($this->outputPath . "" . $fileName . "" . $index . "" . $i . "" . $extension);
								
				$cmd = $sPath . " -ss " . $seekpos;
			    if($this->start_parameters != "")
			       $cmd .= " " . trim($this->start_parameters);
			    $cmd .= " -i " . $iPath . " -t " . $length;
		   	    if($this->parameters != "")
			       $cmd .= " " . trim($this->parameters);
			    $cmd .= " " . $oPath;
				
				$commands .=  $cmd . "<br />";
				$output = $this->executeCommand($cmd);
		
		        $info = $this->parseOutput($output);
       
	            $info->ffmpegOutput = $output;
				$seekpos = $seekpos + $length;
			}
			//$info->errorcode = 444;
			//$info->errorMessage = $commands;
		}
		catch (Exception $e) 
        {
            $info->errorcode = 200; // uncatch
			$info->errorMessage = $e->getMessage();
        }
		return $info;
	}
	
	private function executeCommand($cmd = '', $fetchinfo = false)
	{	
	    if ($cmd == '')
		   $cmd = $this->prepareCommand();	
		exec($cmd . ' 2>&1', $output, $retVar);
		if($retVar != "" && !$fetchinfo)
		  throw new Exception("Error occured while trying to process your command<br />Command: " . $cmd . "<br />Log: " . implode('<br />', $output));
		else
		  $output = join(PHP_EOL, $output);
	    
		return $output;
	}
	
	private function prepareCommand()
	{
		$cmd = "";
		if($this->command != "")
		  $cmd = $this->command;
		else
		{
			$sPath = escapeshellarg($this->servicePath);
			$iPath = escapeshellarg($this->inputPath . "". $this->fileName);
			$oPath = escapeshellarg($this->outputPath . "" . $this->outputfileName);
			
			$cmd = $sPath;
			if($this->start_parameters != "")
			  $cmd .= " " . trim($this->start_parameters);
			$cmd .= " -i " . $iPath;
			if($this->parameters != "")
			  $cmd .= " " . trim($this->parameters);
			$cmd .= " " . $oPath;
		}
		return $cmd;
	}
	
	private function parseOutput($output, $detailInfo = true)
	{
	   $info = new videoInfo();
	   // check for errors
	   $errors = $this->getErrors($output);
	   if($errors != "")
	   {
		   $info->errorcode = 300;
		   $info->errorMessage = $errors;
		   return $info;
	   }
	   
	   // capture duration
	   $info->duration = $this->getDuration($output, false);
	   $info->duration_sec = $this->getDuration($output);
	   
	   $inputsource = $this->getInput($output);
	   $outputsource = $this->getOutput($output);
	  	   
	   // input width x height
	   $isize = $this->getSize($inputsource);
	   $info->width = (int)$isize['width'];
	   $info->height = (int)$isize['height'];
	   // output width x height
	   if($outputsource != "")
	   {
		   $osize = $this->getSize($outputsource);
		   $info->i_width = (int)$osize['width'];
		   $info->i_height = (int)$osize['height'];
	   }
	   
	   if($detailInfo)
	   {
		   // framerate
		   $info->frameRate = $this->getFrameRate($output);
		  
		   // title
		   $info->title = $this->getTitle($output);
		   
		   // artist
		   $info->artist = $this->getArtist($output);
		   
		   // copy right
		   $info->copyright = $this->getCopyright($output);
		   
		   // genre
		   $info->genre = $this->getGenre($output);
		   
		   // track number
		   $info->tracknumber = $this->getTrackNumber($output);
		   
		   // pixel format
		   $info->pixelformat = $this->getPixelFormat($output);
		   
		   // birate
		   $info->bitrate = $this->getBitrate($output);
		   
		   // video bitrate
		   $info->vBitrate = $this->getVideoBitrate($inputsource);
 	       $info->i_vBitrate = $this->getVideoBitrate($outputsource);
		   
		   // audio bitrate
		   $info->aBitrate = $this->getAudioBitrate($inputsource);
		   $info->i_aBitrate = $this->getAudioBitrate($outputsource);
		   
		   // audio sampling rate
		   $info->samplingRate = $this->getAudioSamplingRate($inputsource);
		   $info->i_SamplingRate = $this->getAudioSamplingRate($outputsource);
		   
		   // video codec
		   $info->vCodec = $this->getVideoCodec($inputsource);
		   $info->i_vCodec = $this->getVideoCodec($outputsource);
		   
		   // audio codec
		   $info->aCodec = $this->getAudioCodec($inputsource);
		   $info->i_aCodec = $this->getAudioCodec($outputsource);
		   
		   // channel
		   $info->channel = $this->getAudioChannel($inputsource);
		   $info->i_channel = $this->getAudioChannel($outputsource);
	   }
	   // has audio
	   $info->hasaudio = $this->hasAudio($inputsource);
	   $info->i_hasaudio = $this->hasAudio($outputsource);
	   $info->hasvideo = $this->hasVideo($inputsource);
	   $info->i_hasvideo = $this->hasVideo($outputsource);
	  	  
	   return $info;
	}
	
	private function hasVideo($output)
	{	
	    if($output != "")	
		   return (boolean) preg_match('/Stream.+Video/', $output);
		else
		   return "";
	}
	private function hasAudio($output)
	{	
	    if($output != "")
		   return (boolean) preg_match('/Stream.+Audio/', $output);
		else 
		   return "";
	}
	private function getAudioChannel($output)
	{				
	    $channel = 0;
		if($output != "")
		{
			$match = array();
			preg_match('/Audio:\s[^,]+,[^,]+,([^,]+)/', $output, $match);
			if (array_key_exists(1, $match)) {
				switch (trim($match[1])) {
					case 'mono':
						$channel = 1; break;
					case 'stereo':
						$channel = 2; break;
					case '5.1':
						$channel = 6; break;
					case '5:1':
						$channel = 6; break;
					default: 
						$channel = (int) $match[1];
				}                 
			} else {
				$channel = 0;
			}
		}
		return $channel;
	}
	private function getAudioCodec($output)
	{		
	    if($output != "")
		{
			preg_match('/Audio:\s([^,]+),/', $output, $match);
			return (array_key_exists(1, $match)) ? trim($match[1]) : '';
		}
		else
		{
			return "";
		}
	}
	private function getVideoCodec($output)
	{	
	    if($output != "")
		{	
			preg_match('/Video:\s([^,]+),/', $output, $match);
			return (array_key_exists(1, $match)) ? trim($match[1]) : '';
		}
		else
		{
			return "";
		}
	}
	private function getAudioSamplingRate($output)
	{
		if($output != "")
		{
			preg_match('/Audio:.+?([0-9]+) Hz/', $output, $match);
			return  (int) ((array_key_exists(1, $match)) ? $match[1] : 0);
		}
		else
		{
			return "";
		}
	}
	private function getAudioBitrate($output)
	{	
	   if($output != "")	
	   {
			preg_match('/Audio:.+?([0-9]+) kb\/s/', $output, $match);
			return (int) ((array_key_exists(1, $match)) ? ($match[1] * 1000) : 0);
	   }
	   else
	   {
		   return "";
	   }
	}
	private function getVideoBitrate($output)
	{	
	    if($output != "")
		{	
			preg_match('/Video:.+?([0-9]+) kb\/s/', $output, $match);
			return (int) ((array_key_exists(1, $match)) ? ($match[1] * 1000) : 0);
		}
		else
		{
			return "";
		}
	}
	private function getBitrate($output)
	{		
	    if($output != "")
		{
			preg_match('/bitrate: ([0-9]+) kb\/s/', $output, $match);
			return (int) ((array_key_exists(1, $match)) ? ($match[1] * 1000) : 0);
		}
		else
		{
			return "";
		}
	}
	private function getPixelFormat($output)
	{		
	    if($output != "")
		{
			preg_match('/Video: [^,]+, ([^,]+)/', $output, $match);
			return (array_key_exists(1, $match)) ? trim($match[1]) : '';
		}
		else
		{
			return "";
		}
	}
	private function getTrackNumber($output)
	{
		if($output != "")
		{
			preg_match('/track\s*(:|=)\s*(.+)/i', $output, $match);
			return (int)((array_key_exists(2, $match)) ? $match[2] : 0);
		}
		else
		{
			return "";
		}
	}
	private function getGenre($output)
	{	
	   if($output != "")
	   {
			preg_match('/genre\s*(:|=)\s*(.+)/i', $output, $match);
			return  (array_key_exists(2, $match)) ? trim($match[2]) : '';
	   }
	   else
	   {
		   return "";
	   }
	}
	private function getCopyright($output)
	{		
	    if($output != "")
		{
			preg_match('/copyright\s*(:|=)\s*(.+)/i', $output, $match);
			return  (array_key_exists(2, $match)) ? trim($match[2]) : '';
		}
		else
		{
			return "";
		}
	}
	private function getArtist($output)
	{		
	    if($output != "")
		{
			preg_match('/(artist|author)\s*(:|=)\s*(.+)/i', $output, $match);
			return (array_key_exists(3, $match)) ? trim($match[3]) : '';
		}
		else
		{
			return "";
		}
	}
	private function getTitle($output)
	{	
	   if($output != "")
	   {	
		preg_match('/(\s+|)title\s*(:|=)\s*(.+)/i', $output, $match);
        return (array_key_exists(2, $match)) ? trim($match[2]) : '';
	   }
	   else
	   {
		   return "";
	   }
	}
	private function getFrameRate($output)
	{	
	    $frate = "";
		if($output != "")
		{
			$spattern = '/(?<fps>([0-9\.]+)\sfps,\s)?(?<stbr>([0-9\.]+))\stbr/';
			preg_match_all($spattern,$output, $matches);
			if (array_key_exists(0, $matches["fps"]))
			  $frate = $matches["fps"][0];
			if($frate != "")
			  return $frate;
			  
			if (array_key_exists(0, $matches["stbr"]))
			   $frate = $matches["stbr"][0];
		}
	    return $frate;
	}
		
	private function getSize($output)
	{
		if($output != "")
		{
		 $arr = array();
		 $spattern = '/Video:.+?(?<size>(?<wd>([1-9][0-9]*))x(?<ht>([1-9][0-9]*)))/';
		 preg_match_all($spattern,$output, $matches);
		 if(array_key_exists(0, $matches["size"]))
		    $arr['size'] = $matches["size"][0];
	     else
		    $arr['size'] = 0;
		 if(array_key_exists(0, $matches["wd"]))
		    $arr['width'] = $matches["wd"][0];
		 else
		    $arr['width'] = 0;
		 if(array_key_exists(0, $matches["ht"]))
		    $arr['height'] = $matches["ht"][0];
		 else
		    $arr['height'] = 0;
		 return $arr;
		}
		else
		{
			return "";
		}
	}
	private function getDuration($output, $isSec = true)
	{
		if($output == "")
		  return "";
		$duration = 0.0;
		preg_match('/Duration: ([0-9]{2}):([0-9]{2}):([0-9]{2})(\.([0-9]+))?/', $output, $match);
         if (array_key_exists(1, $match) && array_key_exists(2, $match) && array_key_exists(3, $match)) {                
                $hours     = (int)    $match[1];
                $minutes   = (int)    $match[2];
                $seconds   = (int)    $match[3];                        
                $fractions = (float)  ((array_key_exists(5, $match)) ? "0.$match[5]" : 0.0);
                
				if($isSec)
				  $duration = (($hours * (3600)) + ($minutes * 60) + $seconds);        
				else
				{
				  $duration = "";
				  if ($hours > 0)
				  {
					  if($hours <= 9)
					    $duration .= "0";
					  $duration .= $hours . ":";
				  }
				  if ($minutes > 0)
				  {
					  if($minutes <= 9)
					    $duration .= "0";
					  $duration .= $minutes . ":";
				  }
				  if($seconds <= 9)
				    $duration .= "0";
				  $duration .= $seconds;
				}
           }  
	   return $duration;
	}
	
	private function getInput($output)
	{
		if($output == "") 
		  return $output;
		$str = "";
		//$inputPattern = '/Input #(?<data>.+)/s';
		//if (strpos($output, "Output") != false)
		$inputPattern = '/Input #(?<data>.+)?Output/s';
	    preg_match_all($inputPattern,$output, $matches);
	    foreach($matches["data"] as $matche) 
	    $str = $matche;
		if($str == "") 
		{
			// input doesn't match with output try again without output
			$inputPattern = '/Input #(?<data>.+)/s';
			preg_match_all($inputPattern,$output, $matches);
			foreach($matches["data"] as $matche) 
			$str = $matche;
		}
		return $str;
	}
	
	private function getOutput($output)
	{
		if($output == "") 
		  return $output;
		$str = "";
		$inputPattern = '/Output #(?<data>.+)/s';
	    preg_match_all($inputPattern,$output, $matches);
	    foreach($matches["data"] as $matche) 
	    $str = $matche;
		return $str;
	}
	private function getErrors($output)
	{
		if($output == "") 
		  return $output;
	   $err = "";
	   preg_match('/.*(Error|Permission denied|could not seek to position|Invalid pixel format|Unknown encoder|could not find codec|does not contain any stream).*/i', $output, $errors);
	   if ($errors) {
		   $err =  $errors[0];
	   }
	   return $err;
	}
	
	private function isValid($info, $validateoutput = true)
	{
		if(!$this->isServicePath())
		{
			$info->errorcode = 100;
			$info->errorMessage = "Service path invalid";
			return $info;
		}
		if(!$this->isValidInputPath())
		{
			$info->errorcode = 101;
			$info->errorMessage = "Input file path invalid";
			return $info;
		}
		if($validateoutput)
		{
			if(!$this->isValidOutputPath())
			{
				$info->errorcode = 102;
				$info->errorMessage = "Output directory path invalid";
				return $info;
			}
			
			if (!$this->isValidOutputFileName())
			{
				$info->errorcode = 141;
				$info->errorMessage = "Output File Name must specifiy, e.g sample.avi or sample, extension will skip if specified.";
				return $info;
			}
		}
		
		// validation success
		if(!$this->endsWith($this->outputPath, "/"))
			$this->outputPath = $this->outputPath . "/"; 
    
		if(!$this->endsWith($this->inputPath, "/"))
		    $this->inputPath = $this->inputPath . "/";
			
		return $info;
	}
	
	private function isServicePath()
	{
		if($this->command != "") 
		  return true;
		else if($this->servicePath == "" || !file_exists($this->servicePath))
		   return false;
		else
		   return true;
	}
	
	private function isValidInputPath()
	{
		if($this->command != "") 
		  return true;
		else if($this->startsWith($this->fileName, "http") || strpos($this->fileName, "%") !== false)
		  return true;
		else if($this->fileName == "" || !file_exists($this->inputPath . '/' . $this->fileName))
		   return false;
		else
		   return true;
	}
	
	private function isValidOutputPath()
	{
		if($this->command != "") 
		  return true;
		else if(!file_exists($this->outputPath) || !is_dir($this->outputPath))
		   return false;
		else
		   return true;
	}
	
	private function isValidOutputFileName()
	{
		if($this->command != "") 
		  return true;
		else if($this->outputfileName == "")
		   return false;
		else
		   return true;
	}
	
	private function startsWith($text, $value)
    {
       return !strncmp($text, $value, strlen($value));
    }
	private function endsWith($text, $value)
    {
        $length = strlen($value);
        if ($length == 0) {
           return true;
        }

      return (substr($text, -$length) === $value);
    }
	
	public function lastIndexOf($string,$item)
	{  
		$index=strpos(strrev($string),strrev($item));  
		if ($index){  
			$index=strlen($string)-strlen($item)-$index;  
			return $index;  
		}  
			else  
			return -1;  
	}  
}
?>