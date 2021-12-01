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

defined('JPATH_BASE') or die;

/**
 * Supports an HTML select list of categories
 *
 * @package     Joomla.Administrator
 * @subpackage  com_emails
 * @since       1.6
 */
class JFormFieldResync extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.6
	 */
	protected $type = 'resync';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 * @since   1.6
	 */
	protected function getInput()
	{
	  //Set some default js values, as same as what we set in template "index.php" file.
	  $str = '<script type="text/javascript">';
	  $str.= 'var _SESSION_ID = "'.JSession::getInstance(null,null)->getId().'", ';
	  $str.= '_USER_ID="'.JFactory::getUser()->id.'",';
	  $str.= 'GLOBAL_CONTEXT_PATH="'.JURI::root().'",';
	  $str.= 'PORTAL_UNREACHABLE_ERROR_MESSAGE = "'.JText::_("PORTAL_UNREACHABLE_ERROR_MESSAGE").'",';
	  $str.= 'PORTAL_TIMEOUT_ERROR_MESSAGE = "'.JText::_("PORTAL_TIMEOUT_ERROR_MESSAGE").'";'."\n";
	  $str.= '</script>';
	  
	  //get front-end template folder name
	  $db = JFactory::getDBO();
    $query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
    $db->setQuery($query);
    $defaultemplate = $db->loadResult();
    
	  $html = $str.'<script type="text/javascript" src="'.JURI::root().'templates/'.$defaultemplate.'/js/developer_portal.js"></script>';
	  $html.= '<button class="btn" type="'.$this->id.'" id="'.$this->id.'" name="'.$this->name.'" onclick="javascript: DeveloperPortal.resync(\'0\', \'APIExchange\');return false;">';
	  $html.= JText::_('COM_EMAILS_XML_RESYNC_BTN');
	  $html.= '</button>';
		return $html;
	}
}
