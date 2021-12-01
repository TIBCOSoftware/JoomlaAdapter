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

require_once JPATH_BASE . "/../includes/joomla_adapterVersion.php";


/**
 * View class for a list of redirection emails.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_emails
 * @since       1.6
 */
class EmailsViewEmails extends JViewLegacy
{
	protected $enabled;

	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		$this->enabled		= EmailsHelper::isEnabled();
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
        echo joomla_adapterVersion::getComponent() . "<br/>";
        echo joomla_adapterVersion::getVersion() . "<br/><br/>";
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$state	= $this->get('State');
		$canDo	= EmailsHelper::getActions();

		JToolbarHelper::title(JText::_('COM_EMAILS_MANAGER_LINKS'), 'emails');
		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('email.add');
		}
		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('email.edit');
		}
		if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'emails.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolbarHelper::divider();
		} elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('emails.trash');
			JToolbarHelper::divider();
		}
		JToolbarHelper::help('JHELP_COMPONENTS_EMAILS_MANAGER');


		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', EmailsHelper::created_byOptions(), 'value', 'text', $this->state->get('filter.state'), true)
		);
	}
}
