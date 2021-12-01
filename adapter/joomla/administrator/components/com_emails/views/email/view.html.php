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
 * View to edit a emails email.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_emails
 * @since       1.6
 */
class EmailsViewEmail extends JViewLegacy
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
		$canDo		= EmailsHelper::getActions();

		JToolbarHelper::title(JText::_('COM_EMAILS_MANAGER_LINK'), 'emails');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::apply('email.apply');
			JToolbarHelper::save('email.save');
		}

		// This component does not support Save as Copy due to uniqueness checks.
		// While it can be done, it causes too much confusion if the user does
		// not change the Old URL.

		if ($canDo->get('core.edit') && $canDo->get('core.create'))
		{
			JToolbarHelper::save2new('email.save2new');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('email.cancel');
		}
		else
		{
			JToolbarHelper::cancel('email.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::help('JHELP_COMPONENTS_EMAILS_MANAGER_EDIT');
	}
}
