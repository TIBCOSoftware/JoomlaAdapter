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
 * View to edit a logs log.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_logs
 * @since       1.6
 */
class LogsViewLog extends JViewLegacy
{
	protected $item;

	protected $form;

	protected $state;

	/**
	 * Display the view
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$canDo		= LogsHelper::getActions();

		JToolbarHelper::title(JText::_('COM_LOGS_MANAGER_LINK'), 'logs');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::apply('log.apply');
			JToolbarHelper::save('log.save');
		}

		// This component does not support Save as Copy due to uniqueness checks.
		// While it can be done, it causes too much confusion if the user does
		// not change the Old URL.

		if ($canDo->get('core.edit') && $canDo->get('core.create'))
		{
			JToolbarHelper::save2new('log.save2new');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('log.cancel');
		}
		else
		{
			JToolbarHelper::cancel('log.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::help('JHELP_COMPONENTS_LOGS_MANAGER_EDIT');
	}
}
