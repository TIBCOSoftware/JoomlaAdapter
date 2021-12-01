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
 * Emails master display controller.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_emails
 * @since       1.6
 */
class EmailsController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 * @since   1.6
	 */
	protected $default_view = 'emails';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@email JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/emails.php';

		// Load the submenu.
		EmailsHelper::addSubmenu($this->input->get('view', 'emails'));

		$view   = $this->input->get('view', 'emails');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'email' && $layout == 'edit' && !$this->checkEditId('com_emails.edit.email', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_emails&view=emails', false));

			return false;
		}

		parent::display();
	}
}
