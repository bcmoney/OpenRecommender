<?php
require_once 'Abstract.php';
class Strategy_MTime extends Strategy_Abstract
{
    /**
     * Synchronizes remote directory with local directory based on 
     * file's modified time. Files on the local host will be used as base.
     * 
     * @param string $localDir 
     * @param string $remoteDir 
     * @param int $param time offset
     * @return void
     */
    public function local($localDir, $remoteDir, $timeOffset=600)
    {
        $this->getFtpSync()->mkdir($remoteDir);
		$localFiles = $this->getFtpSync()->getLocalFilesRecursive($localDir);
		$log = "Synchronizing local with remote host started at ".
                date("Y-m-d H:i:s")."\n"."Time Offset : $timeOffset Seconds \n";
        $this->getFtpSync()->getLogger()->dump($log);
		$totalFiles = count($localFiles);
		$tUp = 0;
		$tDown = 0;
		if ($totalFiles > 0) {
			foreach ($localFiles as $file) {
                $log = '';
				$remotePath = str_replace($localDir, $remoteDir, $file);		
				$remotePath = str_replace("\\", "/", $remotePath);
				$remMtime = $this->getFtpSync()->mtime($remotePath);
				$locMTime = filemtime($file);
				if ($remMtime < $locMTime-$timeOffset) {
                    $this->getFtpSync()->mkdir(dirname($remotePath));
					if ($this->getFtpSync()->put($remotePath, $file)) {
						$tUp++;
						$log .= "$file is successfully uploaded to $remotePath \n";
					} else {
						$log .= "Error while Uploading $file\n";
					}
				}elseif ( !$this->getFtpSync()->isReportOnly() && $remMtime > $locMTime+$timeOffset ) {
					if ($this->getFtpSync()->get($file, $remotePath)) {
						$tDown++;
						$log .= "$remotePath is successfully downloaded to $file \n";
					}
				}
                $this->getFtpSync()->getLogger()->dump($log);
			}
			$log = "Synchronization finished at ".date("Y-m-d H:i:s")."\n".
			       "Total Files (local): $totalFiles | Total Uploaded: $tUp | ".
                   "Total Downloaded: $tDown \n";
            $this->getFtpSync()->getLogger()->dump($log)->endLog();
		}
    }

    /**
     * Synchronizes local directory with remote directory based on file's 
     * modified time. Files in the remote host will be used as base.
     * 
     * @param string $remoteDir 
     * @param string $localDir 
     * @param int $timeOffset time offset
     * @return void
     */
    public function remote($remoteDir, $localDir, $timeOffset=600)
    {
        $remoteFiles = $this->getFtpSync()->getRemoteFilesRecursive($remoteDir);
		$log = "Synchronizing remote with local host started at ".
                date("Y-m-d H:i:s")."\n"."Time Offset : $timeOffset Seconds \n";
        $this->getFtpSync()->getLogger()->dump($log);
		$totalFiles = count($remoteFiles);
		$tUp = 0;
		$tDown = 0;
		if($totalFiles > 0){
			foreach($remoteFiles as $file){
                $log = '';
				$localPath = str_replace($remoteDir, $localDir, $file);
				$localPath = str_replace("\\", "/", $localPath);
				$locMTime = (file_exists($localPath)) ? filemtime($localPath) : 0;
				$remMtime = $this->getFtpSync()->mtime($file);
				if ($remMtime < $locMTime-$timeOffset && !$this->getFtpSync()->isReportOnly()) {
					if($this->getFtpSync()->put($file, $localPath)){
						$tUp++;
						$log .= "$localPath is successfully uploaded to $file\n";
					} else {
						$log .= "Error while Uploading $file\n";
					}
				}elseif($remMtime > $locMTime+$timeOffset) {
                    if (!file_exists(dirname($localPath))) {
                        mkdir($localPath, 0777, true);
                    }
					if ($this->getFtpSync()->get($localPath, $file)) {
						$tDown++;
						$log .= "$file is successfully downloaded to $localPath\n";
					}
				}
                $this->getFtpSync()->getLogger()->dump($log);
			}
			$log = "Synchronization finished at ".date("Y-m-d H:i:s")."\n".
			       "Total Files (local): $totalFiles | Total Uploaded: $tUp | ".
                   "Total Downloaded: $tDown \n";
            $this->getFtpSync()->getLogger()->dump($log)->endLog();
		}
    }
}
