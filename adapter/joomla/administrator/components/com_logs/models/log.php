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
 * Logs log model.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_logs
 * @since       1.6
 */
class LogsModelLog extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_LOGS';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	$record	A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since   1.6
	 */
	protected function canDelete($record)
	{

			if ($record->published != -2)
			{
				return false;
			}
			$user = JFactory::getUser();
			return $user->authorise('core.admin', 'com_logs');

	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object	$record	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check the component since there are no categories or other assets.
			return $user->authorise('core.admin', 'com_logs');

	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 * @since   1.6
	*/
	public function getTable($type = 'Log', $prefix = 'LogsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array  $data		Data for the form.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return  JForm	A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_logs.log', 'log', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		// if ($this->canEditState((object) $data) != true)
		// {
		// 	// Disable fields for display.
		// 	$form->setFieldAttribute('created_by', 'disabled', 'true');

		// 	// Disable fields while saving.
		// 	// The controller has already verified this is a record you can edit.
		// 	$form->setFieldAttribute('created_by', 'filter', 'unset');
		// }

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_logs.edit.log.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

}
