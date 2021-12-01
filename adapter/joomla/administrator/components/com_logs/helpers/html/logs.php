<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_logs
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Utility class for creating HTML Grids
 *
 * @static
 * @package     Joomla.Administrator
 * @subpackage  com_logs
 * @since       1.6
 */
class JHtmlLogs
{
	/**
	 * @param   int $value	The state value.
	 * @param   int $i
	 * @param   string  An optional prefix for the task.
	 * @param   boolean		An optional setting for access control on the action.
	 */
	public static function create_time($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			-2	=> array('trash.png',		'logs.publish',		'JTRASHED',	'COM_LOGS_ENABLE_LINK'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[0]);
		$html	= JHtml::_('image', 'admin/'.$state[0], JText::_($state[2]), null, true);
		if ($canChange)
		{
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					. $html.'</a>';
		}

		return $html;
	}
}
