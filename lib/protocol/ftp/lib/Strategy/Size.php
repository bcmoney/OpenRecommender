<?php
require_once 'Abstract.php';
class Strategy_Size extends Strategy_Abstract
{
    /**
     * Gets the files that their sizes are not the same.
     * It assumes that local directory is the base.
     * 
     * @param string $localDir 
     * @param string $remoteDir 
     * @return void
     */
    public function local($localDir, $remoteDir, $param=null)
    {
        $localFiles = $this->getFtpSync()->getLocalFilesRecursive($localDir);
		$log = "Size comarision local with remote host started at ".
                date("Y-m-d H:i:s").".(based on local host)\n";
        $this->getFtpSync()->getLogger()->dump($log);
		$tFiles = count($localFiles);
		$tUp = 0;
		if ($tFiles > 0) {
			foreach ($localFiles as $file) {
                $log = '';
				$remotePath = str_replace($localDir, $remoteDir, $file);		
				$remotePath = str_replace("\\", "/", $remotePath);
				$remFileSize = $this->getFtpSync()->size($remotePath);
				$locFileSize = filesize($file);
				if ($remFileSize != $locFileSize) {
					if (!$this->getFtpSync()->isReportOnly()) {
                        $this->getFtpSync()->mkdir(dirname($remotePath));
						if ($this->getFtpSync()->put($remotePath, $file)) {
							$tUp++;
							$log .= "$file is successfully uploaded to $remotePath\n";
						} else {
							$log .= "Error in Uploading $file\n";
						}
					}else {
						if ($remFileSize < 0) {
							$log .= $file." -> remote file does not exist.\n";
						}else {
							$log .= $file." -> local filesize: ".$locFileSize.
                                    " *** remote filesize: ".$remFileSize."\n";
						}
					}
				}
                $this->getFtpSync()->getLogger()->dump($log);
			}
			$log = "Size comparision finished at ".date("Y-m-d H:i:s")."\n".
			       "Total Files (local): $tFiles | Total Uploaded: $tUp \n";
            $this->getFtpSync()->getLogger()->dump($log)->endLog();
		}
    }

    /**
     * Gets the files that their sizes are not the same.
     * It assumes that the remote directory is the base.
     * 
     * @param string $remoteDir 
     * @param string $localDir 
     * @return void
     */
    public function remote($remoteDir, $localDir, $param=null)
    {
        $remoteFiles = $this->getFtpSync()->getRemoteFilesRecursive($remoteDir);
		$log = "Size comarision remote with local host started at ".
                 date("Y-m-d H:i:s").".(based on remote host)\n";
        $this->getFtpSync()->getLogger()->dump($log);
		$tFiles = count($remoteFiles);
		$tDown = 0;
		if ($tFiles > 0) {
			foreach($remoteFiles as $file){
                $log = '';
				$localPath = str_replace($remoteDir, $localDir, $file);
				$localPath = str_replace("\\", "/", $localPath);
				$localFileSize = (file_exists($localPath)) ? filesize($localPath) : 0;
				$remFileSize = $this->getFtpSync()->size($file);
				if($localFileSize != $remFileSize){
					if (!$this->getFtpSync()->isReportOnly()) {
                        if (!file_exists(dirname($localPath))) {
                            mkdir($localPath, 0777, true);
                        }
						if($this->getFtpSync()->get($localPath, $file)){
							$tDown++;
							$log .= "$file is successfully downloaded to $localPath \n";
						} else {
							$log .= "Error while downloading $localPath\n";
						}
					}else{
						if(!$localFileSize){
							$log .= $file." -> local file does not exist.\n";
						}else{
							$log .= $file." -> remote filesize: ".$remFileSize.
                                    " *** local filesize: ".$localFileSize."\n";
						}
					}
				}
                $this->getFtpSync()->getLogger()->dump($log);
			}
			$log = "Size comparision finished at ".date("Y-m-d H:i:s")."\n".
			       "Total Files (remote): $tFiles | Total Downloaded: $tDown \n";
            $this->getFtpSync()->getLogger()->dump($log)->endLog();
		}
    }
}
