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
 * Log Table for Logs.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_logs
 * @since       1.6
 */
class LogsTableLog extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object	Database object
	 *
	 * @return  void
	 * @since   1.6
	 */
	public function __construct(&$db)
	{
		parent::__construct('asg_logs', 'id', $db);
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean
	 * @since   1.6
	 */
	public function check()
	{
		// $this->subject = trim($this->subject);
		// $this->alias = trim($this->alias);

		// Check for valid name.
		// if (empty($this->subject))
		// {
		// 	$this->setError(JText::_('COM_LOGS_ERROR_SUBJECT_REQUIRED'));
		// 	return false;
		// }

		// Check for valid name.
		// if (empty($this->alias))
		// {
		// 	$this->setError(JText::_('COM_LOGS_ERROR_ALIAS_REQUIRED'));
		// 	return false;
		// }

		// $db = $this->getDbo();

		// // Check for existing name
		// $query = 'SELECT id FROM asg_logs WHERE subject ='.$db->Quote($this->subject);
		// $db->setQuery($query);

		// $xid = (int) $db->loadResult();

		// if ($xid && $xid != (int) $this->id)
		// {
		// 	$this->setError(JText::_('COM_LOGS_ERROR_DUPLICATE_SUBJECT'));
		// 	return false;
		// }
		// // Check for existing name
		// $query = 'SELECT id FROM asg_logs WHERE alias ='.$db->Quote($this->alias);
		// $db->setQuery($query);

		// $yid = (int) $db->loadResult();

		// if ($yid && $yid != (int) $this->id)
		// {
		// 	$this->setError(JText::_('COM_LOGS_ERROR_DUPLICATE_ALIAS'));
		// 	return false;
		// }
		// pre($this);
		return true;
	}

	/**
	 * Overriden store method to set dates.
	 *
	 * @param   boolean	True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 * @see     JTable::store
	 * @since   1.6
	 */
	public function store($updateNulls = false)
	{
		$date = JFactory::getDate()->toSql();
		$user = JFactory::getUser();

		// print_r($this->type);
		// jexit();
		if (!$this->id)
		{
			// New record.
			$this->create_time = $date;
		}
		return parent::store($updateNulls);
	}
}
