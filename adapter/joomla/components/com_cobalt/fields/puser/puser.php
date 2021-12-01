<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components/com_cobalt/library/php/fields/cobaltfield.php';


require_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_cobalt'. DIRECTORY_SEPARATOR .'api.php';

class JFormFieldCPuser extends CFormField
{

	public function getInput()
	{
		$html = array();
		$groups = $this->getGroups();
		$excluded = $this->getExcluded();

        $link = JURI::root() . "administrator/index.php?option=com_users&view=users&layout=modal&tmpl=component&field=".$this->id;

		// $link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=' . $this->id
		// 	. (isset($groups) ? ('&amp;groups=' . base64_encode(json_encode($groups))) : '')
		// 	. (isset($excluded) ? ('&amp;excluded=' . base64_encode(json_encode($excluded))) : '');

		// Initialize some field attributes.
		$attr = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize JavaScript field attributes.
		$onchange = (string) $this->element['onchange'];

		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal_' . $this->id);
		// Build the script.
		$script = array();
		$script[] = '	function jSelectUser_' . $this->id . '(id, title) {';
		$script[] = '		var old_id = document.getElementById("' . $this->id . '_id").value;';
		$script[] = '		if (old_id != id) {';
		$script[] = '			document.getElementById("' . $this->id . '_id").value = id;';
		$script[] = '			document.getElementById("' . $this->id . '_name").value = title;';
		$script[] = '			' . $onchange;
		$script[] = '		}';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Load the current username if available.
		$table = JTable::getInstance('user');
		if ($this->value)
		{
			$table->load($this->value);
		}
		else
		{
			$table->username = JText::_('JLIB_FORM_SELECT_USER');
		}

		// Create a dummy text field with the user name.
		$html[] = '<div class="input-append">';
		$html[] = '	<input class="input-medium" type="text" id="' . $this->id . '_name" value="' . htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') . '"'
			. ' disabled="disabled"' . $attr . ' />';

		// Create the user select button.
		if ($this->element['readonly'] != 'true')
		{
			$html[] = '		<a class="btn btn-primary modal_' . $this->id . '" title="' . JText::_('JLIB_FORM_CHANGE_USER') . '" href="' . $link . '"'
				. ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = '<i class="icon-user"></i></a>';
		}
		$html[] = '</div>';

		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="' . $this->id . '_id" name="jform[fields]['.$this->id.']" value="' . (int) $this->value . '" />';

		return implode("\n", $html);
	}

	/**
	 * Method to get the filtering groups (null means no filtering)
	 *
	 * @return  mixed  array of filtering groups or null.
	 *
	 * @since   1.6.0
	 */
	protected function getGroups()
	{
		return null;
	}

	/**
	 * Method to get the users to exclude from the list of users
	 *
	 * @return  mixed  Array of users to exclude or null to to not exclude them
	 *
	 * @since   1.6.0
	 */
	protected function getExcluded()
	{
		return null;
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $type, $section);
	}

	private function _render($view, $record, $type, $section)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(TRUE);

		$query->select('username');
		$query->from('#__users');
		$query->where("id =".$this->value);

		$db->setQuery($query);
		$user = $db->loadObject();

		if($user->username){
			$this->value = $user->username;
		}else{
			return '';
		}
		return $this->_display_output($view, $record, $type, $section);
	}
}
