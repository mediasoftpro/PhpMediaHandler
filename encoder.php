<?php
// advance media processing
// used to published multi format, multi level video and audio processing, settting meta information, grab thumbs, complex validation in one step.
require_once "mediahandler.php";
require_once "videoinfo.php";
require_once "templates.php";

class mhpEncoder {
	
	public $rootPath = ""; // used for other services
	public $servicePath = "";
	public $sourcePath;
	public $sourceFileName;
	public $publishPath;
	public $publishFileName;
	public $thumbsDirectory;
	
	// other options
	public $postWatermark = false;
	public $watermarkPath = "watermark.png"; // watermark.png must be located in folder where actual .php file exist (php file which call posting watermark command)
    public $watermarkLocation = 0; // 0: top left, 1: top right, 2: bottom left, 3: bottom right	
	public $grabThumbs = true;
	public $publishVideos = true; // if disabled, script will grab thumbs only
	public $isMultipleThumbs = true; // grab single or multiple thumbs
	public $totalThumbs = 15;
	public $allowRollback = true; // remove all published contents in case of error occurs in any stage
	public $deleteSource = false; // delete source video if publishing completed
	public $itags = array();
	// private properties
	private $publishedFiles = array();
	private $mp4boxPath; 
	private $flvtoolPath;
	 
	function __construct()
	{
		$this->mp4boxPath = $this->rootPath . "mp4box/MP4Box.exe"; // adjust with linux path
		$this->flvtoolPath = $this->rootPath . "flvtool/flvtool2.exe"; // adjust in case of linux
	}
	public function Process()
	{		
		$mhandler = new MediaHandler();
		$settings = new MediaTemplates();
		$info = new videoInfo();
		$dinfo = new videoInfo(); // store first 
		
		$outputFileName = "";
        $thumbFileName = "";
		
		try
		{
			$fName = $this->sourceFileName;
			if($this->publishFileName != "")
			  $fName = $this->publishFileName;
			$outputFileName = substr($fName, 0, $mhandler->lastIndexOf($fName, "."));
            if($this->publishVideos)
			{
				$countTags = count($this->itags);
				
				if(count($countTags) == 0)
				{
					$info->errorcode = 203;
					$info->errorMessage = "Please select atleast one output";
					$this->rollbackProcess();
					return $info;
				}
				
				$output = "";
				for ($i = 0; $i <= $countTags - 1; $i++)
				{
					// reset media handler settings
					$mhandler->servicePath = $this->servicePath;
					$mhandler->inputPath = $this->sourcePath;
					$mhandler->fileName = $this->sourceFileName;
					$mhandler->outputPath = $this->publishPath;
					$extension = $settings->returnExtension($this->itags[$i]);
					$mhandler->outputfileName = $outputFileName . "_" . $this->itags[$i] . "" . $extension;
					// load settings from template
					$mhandler->parameters = implode(' ', $settings->returnSettings($this->itags[$i]));
					if($this->postWatermark)
					{
						switch($this->watermarkLocation)
						{
							case 0: // top left
							   $mhandler->parameters .= " -vf \"movie = " . $this->watermarkPath . " [watermark]; [in][watermark] overlay =10:10 [out]\"";
							   break;
							case 1: // top right
							   $mhandler->parameters .= " -vf \"movie = " . $watermarkPath . " [watermark]; [in][watermark] overlay=main_w-overlay_w-10:10 [out]\"";
							   break;
							case 2: // botto left
							   $mhandler->parameters .= " -vf \"movie = " . $watermarkPath . " [watermark]; [in][watermark] overlay=10:main_h-overlay_h-10 [out]\"";
							   break;
							case 3: // bottom right
							   $mhandler->parameters .= " -vf \"movie = " . $watermarkPath . " [watermark]; [in][watermark] overlay=main_w-overlay_w-10:main_h-overlay_h-10 [out]\"";
							  break;
							
						}
					}
					$mhandler->parameters .= " -y"; // owerwrite
					//$output .= "<br />" . $mhandler->parameters . "<br />" . $mhandler->outputfileName;
					$info = $mhandler->Process();
					if($info->errorcode > 0)
					{
						// rollback process
						$this->rollbackProcess();
						$info->errorMessage = $info->errorMessage . " - iTag: " . $this->itags[$i];
						return $info;
					}
					// Validation of published file
					$oFile = $mhandler->outputPath . '/' . $mhandler->outputfileName;
					if(!file_exists($oFile))
					{
						$info->errorcode = 201;
						$info->errorMessage = "Published file not found! - iTag: " . $this->itags[$i];
						$this->rollbackProcess();
						return $info;
					}
					if(filesize($oFile) < 10)
					{
						$info->errorcode = 202;
						$info->errorMessage = "Published file corrupted - 0bytes - iTag: " . $this->itags[$i];
						$this->rollbackProcess();
						return $info;
					}
					// set meta information 
					$output = "no";
					if($extension == ".mp4")
					{						
						$vinfo = $this->setMP4Meta($mhandler->outputfileName);
						if($vinfo->errorcode > 0)
						{
						    $this->rollbackProcess();
							return $vinfo;
						} 
					}
					else if($extension == ".flv")
					{
						$output = $this->setFLVMeta($mhandler->outputfileName);
						/*$vinfo = $this->setFLVMeta($mhandler->outputfileName);
						if($vinfo->errorcode > 0)
						{
						    $this->rollbackProcess();
							return $vinfo;
						} */
					}
					$publishedFiles[] = $oFile;
					if ($i == 0)
					   $dinfo = $info; 
				}
				
				$info->errorcode = 555;
				$info->errorMessage = $output;
			}
			// grab thumbs
			if($this->grabThumbs)
			{
				$ghandler = new MediaHandler();
				$mhandler->servicePath = $this->servicePath;
				$mhandler->inputPath = $this->sourcePath;
				$mhandler->fileName = $this->sourceFileName;
				$mhandler->outputPath = $this->thumbsDirectory;
				$mhandler->parameters = "-f image2 -s 120x100"; // normal options for grabbing thumbs
                $mhandler->imageFormat = ".jpg";
				if($this->isMultipleThumbs)
				{
					// multiple thumb grab option choosen
					$mhandler->isMultiple = true;
                    $mhandler->totalThumbs = $this->totalThumbs; 
					$mhandler->startPosition = 3;
				}
				else
				{
					// single thumb
					$mhandler->isMultiple = false;
					// calculate video mid position
					$startposition = 0;
                    $vinfo = $mhandler->getInfo(false);
                    if($vinfo->duration_sec >0)
                       $startposition = (int)($vinfo->duration_sec/2);
                    else
                       $startposition = 5;
					$mhandler->startPosition = $startposition;
				}
				$ginfo = $mhandler->grabThumbs();
				if($ginfo->errorcode >0)
				{
					$this->rollbackProcess();
					$ginfo->errorMessage = "Thumbs failed to grab properly";
					return $ginfo;
				}
			}
			
			// remove original file
			if($this->deleteSource)
			{
				$sFile = $this->sourcePath . '/' . $this->sourceFileName;
				if(file_exists($sFile))
				  unlink($sFile);
			}
			
		}
		catch (Exception $e) 
        {
            $info->errorcode = 200; // uncatch
			$info->errorMessage = $e->getMessage();
        }
		
		return $info;
		
	}
	
	function setFLVMeta($fileName)
	{
		$mhandler = new MediaHandler();
		$mhandler->servicePath = $this->flvtoolPath;
        $mhandler->fileName = $fileName;
        $mhandler->command = '-U ' .escapeshellarg($this->publishPath . "" . $mhandler->fileName);
        $info = $mhandler->setMeta();
		return $info->errorcode . "<br />" . $info->ffmpegOutput . "<br />" . $mhandler->command;
	}
	
	function setMP4Meta($fileName)
	{
		$mhandler = new MediaHandler();
		$mhandler->servicePath = $this->mp4boxPath;
        $mhandler->fileName = $fileName;
        $mhandler->command = '-isma -hint ' .escapeshellarg($this->publishPath . "" . $mhandler->fileName);
        return $mhandler->setMeta();
	}
	
	// rollback all successuful stats in case of errors
	function rollbackProcess() 
	{
		 if(!$this->allowRollback)
		    return;
	     // remove all published files
		 if(count($publishedFiles) > 0)
		 {
			 foreach($publishedFiles as $itm)
			 {
			     if(file_exists($itm))
				    unlink($itm);
			 }
		 }
		 // remove all thumbs if grabbed
		 $fName = $this->sourceFileName;
		 if($this->publishFileName != "")
		   $fName = $this->publishFileName;
		   
		 $counter = 1;
		 $thumb_index = "";
		 for ($i = 0; $i <= $this->totalThumbs - 1; $i++)
		 {				
			 if ($counter < 10)
				  $thumb_index = "00" . $counter;
			 else if ($counter >= 10 && $counter < 100)
				  $thumb_index = "0" . $counter;
			 else
				  $thumb_index = $counter;
		 }
		 $outputFileName = substr($fName, 0, $mhandler->lastIndexOf($fName, "."));
		 		 
		 $oFile = $this->thumbsDirectory . "" . $outputFileName . "" . $thumb_index . ".jpg";
		 if(file_exists($oFile))
		   unlink($oFile);
	}
}
?>