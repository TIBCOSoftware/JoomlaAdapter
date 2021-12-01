<?php
/**
 * @version     1.0.0
 * @package     com_request
 * @copyright   
 * @license     
 * @author      burtyu <ybt7755221@sohu.com> - http://burtyu.com
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_request')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Request');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
