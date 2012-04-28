<?php
require_once 'Abstract.php';
class Strategy_CleanUp extends Strategy_Abstract
{
    /**
	 * Cleans up the local directory in such a way that if local version of 
     * a file does exist but remote version does not, local version will 
     * be removed.(Please use it carefully). Also it has ability to backup 
     * all the files that should be removed to a directory in your local host.
     * 
     * @param string $localDir 
     * @param string $remoteDir 
     * @param boolean|string $backup backup dir (local)
     * @return void
     */
    public function local($localDir, $remoteDir, $backup=false)
    {
        $localFiles = $this->getFtpSync()->getLocalFilesRecursive($localDir);
		$log = "Local clean up started at ".date("Y-m-d H:i:s").".\n";
        $this->getFtpSync()->getLogger()->dump($log);
		$iFiles = count($localFiles);
		$tDel = 0;
		if ($iFiles > 0) {
			foreach ($localFiles as $file) {
				$file = str_replace("\\", "/", $file);
				$remotePath = str_replace($localDir, $remoteDir, $file);
                //if remote version of a file does not exist, remove the local one
				if($this->getFtpSync()->size($remotePath) == -1){	
					if ($backup) {
                        //manipulate the backup path
						$backupPath = str_replace($localDir, $backup, $file);
                        //get current file's directory
						$backupDir = dirname($backupPath);
						if (!is_dir($backupDir)) {
                            //create backup sub-directories
							@mkdir($backupDir, 0777, true);
						}
                        //copy the file should be removed to the backup directory
						$ok = copy($file, $backupPath);	
					}
					$ok = unlink($file);
					if ($ok) {
						$log = $file . " removed successfully.\n";
						++$tDel;
					}else{
						$log = "Error while deleting file: $file \n";
					}
                    $this->getFtpSync()->getLogger()->dump($log);
				}
			}
			$log = "Local clean up finished at ".date("Y-m-d H:i:s")."\n".
			       "Total Files (local): $iFiles | Total Local Deleted: $tDel\n";
            $this->getFtpSync()->getLogger()->dump($log)->endLog();
		}
    }

    /**
	 * Cleans up the remote directory in such a way that if a file exists in 
     * the remote directory and does not exist in the local directory, 
     * it will be removed from remote directory.(Please use this method carefully).
     * Also it has ability to backup all the files that should be removed 
     * to a directory in the local host.
     * 
     * @param string $remoteDir 
     * @param string $localDir 
     * @param boolean|string $backup backup directory (local)
     * @return void
     */
    public function remote($remoteDir, $localDir, $backup=false)
    {
        $remoteFiles = $this->getFtpSync()->getRemoteFilesRecursive($remoteDir);
		$log = "Remote clean up started at ".date("Y-m-d H:i:s").".\n";
        $this->getFtpSync()->getLogger()->dump($log);
		$iFiles = count($remoteFiles);
		$tDel = 0;
		if($iFiles > 0){
			foreach($remoteFiles as $file){
				$file = str_replace("\\", "/", $file);
				$localPath = str_replace($remoteDir, $localDir, $file);
                //if local version of a file does not exist, remove the remote one
				if (!file_exists($localPath)) {	
					if ($backup) {
						$backupPath = str_replace($remoteDir, $backup, $file);
                        //get current file's directory
						$backupDir = dirname($backupPath);		
						if (!is_dir($backupDir)) {
                            //create backup sub-directories
							@mkdir($backupDir, 0777, true);	
						}
                        //download & backup the file that should be deleted 
						$ok = $this->getFtpSync()->get($backupPath, $file);	
					}
					$ok = $this->getFtpSync()->delete($file);
					if ($ok) {
						++$tDel;
						$log = $file ." removed successfully.\n";
                        $this->getFtpSync()->getLogger()->dump($log);
					}
				}
			}
			$log = "Remote clean up finished at ".date("Y-m-d H:i:s")."\n".
			       "Total Files (remote): $iFiles | Total Remote Deleted: $tDel\n";
            $this->getFtpSync()->getLogger()->dump($log)->endLog();
		}
    }
}
