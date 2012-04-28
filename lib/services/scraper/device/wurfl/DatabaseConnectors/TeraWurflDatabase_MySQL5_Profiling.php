<?php
/**
 * Copyright (c) 2011 ScientiaMobile, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the COPYING file distributed with this package.
 *
 * @package    WURFL_Database
 * @copyright  ScientiaMobile, Inc.
 * @author     Steve Kamerman <steve AT scientiamobile.com>
 * @license    GNU Affero General Public License
 * @version    $id$
 */
/**
 * Include required files
 */
require_once realpath(dirname(__FILE__).'/TeraWurflDatabase_MySQL5.php');
/**
 * Include required files
 */
require_once realpath(dirname(__FILE__).'/phpMyProfiler.php');
/**
 * Provides connectivity from Tera-WURFL to MySQL 5
 * This "Profiling" connector logs profile data from MySQL during its queries
 * @package TeraWurflDatabase
 */
class TeraWurflDatabase_MySQL5_Profiling extends TeraWurflDatabase_MySQL5{
	protected $profiler;
	/**
	 * The path and file prefix to use for storing MySQL Query Profiles
	 * @var string
	 */
	protected $profile_log = "/tmp/TeraWurflProfile-";
	/**
	 * Establishes connection to database (does not check for DB sanity)
	 */
	public function connect(){
		parent::connect();
		$this->profiler = new phpMyProfiler($this->dbcon,$this->profile_log);
	}
	public function getDeviceFromUA_RIS($userAgent,$tolerance,UserAgentMatcher &$matcher){
		$return = parent::getDeviceFromUA_RIS($userAgent,$tolerance,$matcher);
		$this->profiler->log();
		return $return;
	}
}
