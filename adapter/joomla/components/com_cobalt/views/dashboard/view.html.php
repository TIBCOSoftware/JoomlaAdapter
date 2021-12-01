<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('joomla.application.component.view');
JHtml::addIncludePath(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'php');
require_once JPATH_BASE . "/includes/api.php";
require_once(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'record' . DIRECTORY_SEPARATOR  . 'view.html.' .'php');
class CobaltViewDashboard extends CobaltViewRecord
{
	function display($tpl = NULL)
	{
	  $app   = JFactory::getApplication();
	  $user  = JFactory::getUser();
	  $login = $user->get('guest') ? true : false;
	  $userOrg = DeveloperPortalApi::getUserOrganization();
	  $url = JURI::root();

	  if(is_array($userOrg) && count($userOrg))
	  {
	  	$userOrg = $userOrg[0];
	  }else{
	  	$userOrg = 0;
	  }

	  if($login){
	  	$url .= JRoute::_("index.php?option=com_users&view=login");
	  	$app->redirect($url,JText::_('JLOGINPLEASE'),'error');
	  }
	  else
	  {
	  	if($userOrg)
	  	{
	  		$app->redirect(JRoute::_("index.php?option=com_cobalt&view=record&id=$userOrg"));
	  	}
	  	else
	  	{
	  		$app->redirect($url,JText::_('JNOUSERPROFILE'),'error');
	  	}
	  }
	  return false;
	}
}

?>