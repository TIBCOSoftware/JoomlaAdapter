<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/fields/cobaltselectable.php';

class JFormFieldCPlevel extends CFormFieldSelectable
{

	public function getInput()
	{
		$params = $this->params;
		$doc = JFactory::getDocument();
		$this->user = JFactory::getUser();
		
		$values = array();
		if ($params->get('params.values'))
		{
			$values = explode("\n", $params->get('params.values'));
			ArrayHelper::clean_r($values);
			if (is_array($this->value))
			{
				$this->value = trim(@$this->value[0]);
			}
			if (!in_array($this->value, $values))
			{
				$values[] = $this->value;
			}
			ArrayHelper::clean_r($values);
			
			if ($params->get('params.sort') == 2)
				asort($values);
			if ($params->get('params.sort') == 3)
				rsort($values);
		}
		
		$this->values = $values;
		
		if($this->isnew && $this->params->get('params.default_val'))
		{
			$this->value = $this->params->get('params.default_val');
		}

		return $this->_display_input();
		
	}

	public function onJSValidate()
	{
		$js = "\n\t\tvar chb{$this->id} = jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[$this->id\\\\]\"]').val();";
		if($this->required)
		{
			$js .= "\n\t\tif(!chb{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('".addslashes(JText::sprintf("CFIELDREQUIRED", $this->label))."');}";
		}	
		return $js;
	}

}
