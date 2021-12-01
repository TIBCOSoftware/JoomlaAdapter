<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.resetpassword
 *
 */

defined('_JEXEC') or die;

/**
 *
 * @package     Joomla.Plugin
 * @subpackage  System.resetpassword
 * @since       1.6
 */
class PlgSystemResetpassword extends JPlugin
{

	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$doc = JFactory::getDocument();
		$maxStep = 10;
		if (array_key_exists("isSuccessFromPing",$_SESSION)){
           $isSuccessFromPing = $_SESSION["isSuccessFromPing"];
        }

		if ($app->isSite())
		{
			if (!$user->guest)
			{
				if ($this->getResetPasword($user->id) && !$isSuccessFromPing)
				{
					$this->deleteResetPassword($user->id);
					$app->redirect(JRoute::_("index.php?option=com_users&view=profile&layout=edit"));
				}
			}
		}
		
	}

	private function getResetPasword($user_id = 0){
	  if(!$user_id)
	  {
	    return false;
	  }

	  $db = JFactory::getDbo();
	  $query = $db->getQuery(true);
	  $query->select("*")->from("#__user_profiles")->where($db->quoteName('user_id') . '=' . $user_id . ' and ' . $db->quoteName('profile_key') . '="reset.password"');

	  $db->setQuery($query);

	  $result = $db->loadColumn();

	  if(count($result))
	  {
	    return true;
	  }

	  return false;
	}


	private function deleteResetPassword($user_id = 0){
	  if(!$user_id)
	  {
	    return false;
	  }

	  $db = JFactory::getDbo();
	  $query = $db->getQuery(true);

	  $query->delete("#__user_profiles")->where($db->quoteName('user_id') . '=' . $user_id)
	        ->where($db->quoteName('profile_key') . '="reset.password"');

	  $db->setQuery($query);

	  $db->query();
	}
}
