<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_emails
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Emails component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_emails
 * @since       1.6
 */
class EmailsHelper
{
	public static $extension = 'com_emails';

	/**
	 * Configure the Emailbar.
	 *
	 * @param   string	The name of the active view.
	 */
	public static function addSubmenu($vName)
	{
		// No submenu for this component.
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  JObject
	 */
	public static function getActions()
	{
		$user		= JFactory::getUser();
		$result		= new JObject;
		$assetName	= 'com_emails';

		$actions = JAccess::getActions($assetName);

		foreach ($actions as $action)
		{
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	 * Returns an array of standard created_by state filter options.
	 *
	 * @return  string  	The HTML code for the select tag
	 */
	public static function created_byOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '*', 'JALL');
		$options[]	= JHtml::_('select.option', '-2', 'JTRASHED');

		return $options;
	}

	/**
	 * Determines if the plugin for Emails to work is enabled.
	 *
	 * @return  boolean
	 */
	public static function isEnabled()
	{
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT enabled' .
			' FROM #__extensions' .
			' WHERE folder = '.$db->quote('system').
			'  AND element = '.$db->quote('emails')
		);

		try
		{
			$result = (boolean) $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $result;
	}
}
