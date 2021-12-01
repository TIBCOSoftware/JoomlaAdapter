<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die();
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/fields/cobaltfield.php';


class JFormFieldCuuid extends CFormField
{

	public function getInput()
	{
		$this->value = $this->value ? $this->value : $this->uuid('');

		return $this->_display_input();
	}

	private function uuid( $prefix = '')
	{
		$chars = md5(uniqid(mt_rand(), true));
		$uuid = substr ( $chars ,0,8).'-';
		$uuid .= substr ( $chars ,8,4).'-';
		$uuid .= substr ( $chars ,12,4).'-';
		$uuid .= substr ( $chars ,16,4).'-';
		$uuid .= substr ( $chars ,20,12);
		return $prefix.$uuid;
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
		if(!$this->value)
		{
			return;
		}
		if($view == 'list' && $this->params->get('params.length', 0) > 0)
		{
			$this->value = HTMLFormatHelper::substrHTML($this->value, $this->params->get('params.length'));
		}
		$value = $this->value;
		if($this->params->get('params.filter_enable'))
		{
			$tip = ($this->params->get('params.filter_tip') ? JText::sprintf($this->params->get('params.filter_tip'), '<b>' . $this->label . '</b>', '<b>' . $value . '</b>') : NULL);

			switch($this->params->get('params.filter_linkage'))
			{
				case 1 :
					$value = FilterHelper::filterLink('filter_' . $this->id, $value, $value, $this->type_id, $tip, $section);
					break;

				case 2 :
					$value = $value . ' ' . FilterHelper::filterButton('filter_' . $this->id, $value, $this->type_id, $tip, $section, $this->params->get('params.filter_icon', 'funnel-small.png'));
					break;
			}
		}

		$this->value    = $value;

		return $this->_display_output($view, $record, $type, $section);
	}
}
