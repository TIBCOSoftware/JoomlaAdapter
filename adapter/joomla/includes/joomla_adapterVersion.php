<?php
/*
 * $HeadURL: http://svn.tibco.com/asg/branches/1.0/runtime/functions/asg_functions/src/main/java/com/tibco/asg/asg_functionsVersion.tag $ $Revision: 47285 $ $Date: 2012-01-20 15:54:53 -0800 (Fri, 20 Jan 2012) $
 *
 * Copyright (c) 2013-2014, TIBCO Software Inc. All rights reserved.
 *
 * GNU General Public License version 2; see LICENSE.txt
 *
 * asg-functions.jar Version Information
 *
 */

/*

        AUTOMATICALLY GENERATED AT BUILD TIME !!!!

        DO NOT EDIT !!!

 * "joomla_adapterVersion.php" is automatically generated at
 * build time from "joomla_adapterVersion.tag"
 *
 * Any maintenance changes MUST be applied to "joomla_adapterVersion.tag"
 * and an official build triggered to propagate such changes to
 * "joomla_adapterVersion.php"
 *
 * If maintenance changes must be applied immediately without going
 * through an official build, then they MUST be applied to *BOTH*
 * "joomla_adapterVersion.tag" *AND* "joomla_adapterVersion.php"
 *
 */

defined('_JEXEC') or die;
class joomla_adapterVersion
{
	static $asterisks       = "**********************************************************************";
	static $copyright       = "Copyright(c) 2004-2019 TIBCO Software Inc. All rights reserved.";
	static $line_separator  = "\n"; //In Java we use: System.getProperty("line.separator");
	static $version = "2.3.4";
	static $build = "285";
	static $buildDate = "2021-07-20";
	static $company = "TIBCO Software Inc.";
	static $component = "Adapter Code for TIBCO API Exchange and Joomla!";
	static $license = "";
	
	public static function getVersion() {
		$retVersion = "Version " . joomla_adapterVersion::$version . "." . joomla_adapterVersion::$build . ", " . joomla_adapterVersion::$buildDate;
		return $retVersion;
	}

	public static function getCompany() {
			return joomla_adapterVersion::$company;
	}

	public static function getComponent() {
			return joomla_adapterVersion::$component;
	}

	/*
	static public void main(String[] args) {
			System.out.println(getCompany() + " - " + getComponent() + " " + getVersion() + " " + getLicense());
	}
	*/

	public static function getLicense() {
			return joomla_adapterVersion::$license;
	}
	 
}

?>

