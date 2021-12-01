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
 * Logs email list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_logs
 * @since       1.6
 */
class LogsControllerLogs extends JControllerAdmin
{
	/**
	 * Method to update a record.
	 * @since   1.6
	 */
	public function activate()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$ids     = $this->input->get('cid', array(), 'array');
		$alias  = $this->input->getString('alias');
		// $subject = $this->input->getString('subject');

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('COM_LOGS_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			JArrayHelper::toInteger($ids);

			// // Remove the items.
			if (!$model->activate($ids, $alias, $subject))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else {
				$this->setMessage(JText::plural('COM_LOGS_N_LINKS_UPDATED', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_logs&view=logs');
	}

	/**
	 * Proxy for getModel.
	 * @since   1.6
	 */
	public function getModel($name = 'Log', $prefix = 'LogsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
