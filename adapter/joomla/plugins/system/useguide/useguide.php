<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.useguide
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 *
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.useguide
 * @since       1.6
 */
class PlgSystemUseguide extends JPlugin
{

	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since 1.5
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$lang = JFactory::getLanguage();
		$lang->load('plg_system_useguide',JPATH_ADMINISTRATOR);
	}

	/**
	 * Plugin that loads module positions within content
	 *
	 * @param   string	The context of the content being passed to the plugin.
	 * @param   object	The article object.  Note $article->text is also available
	 * @param   object	The article params
	 * @param   integer  The 'page' number
	 */
	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$doc = JFactory::getDocument();
		$maxStep = 7;

		if ($app->isSite())
		{
			// $app->redirect(JRoute::_("index.php?option=com_users&view=profile&layout=edit"));

			if (!$user->guest)
			{
				if ($this->isToShowGuide($user->id))
				{
					JText::Script("PLG_SYSTEM_USEGUIDE_GUIDE_CONTENT_STEP1",false,false);
					JText::Script("PLG_SYSTEM_USEGUIDE_GUIDE_CONTENT_STEP2",false,false);
					JText::Script("PLG_SYSTEM_USEGUIDE_GUIDE_CONTENT_STEP3",false,false);
					JText::Script("PLG_SYSTEM_USEGUIDE_GUIDE_CONTENT_STEP4",false,false);
					JText::Script("PLG_SYSTEM_USEGUIDE_GUIDE_CONTENT_STEP5",false,false);
					JText::Script("PLG_SYSTEM_USEGUIDE_GUIDE_CONTENT_STEP6",false,false);
					JText::Script("PLG_SYSTEM_USEGUIDE_GUIDE_CONTENT_STEP7",false,false);

					$storedStep = $this->getCurrentGuideStep($user->id);
					$cookie_name = md5(strtotime($user->registerDate).$user->id."_guide_step");
					$stepNumber = $app->input->cookie->getInt( $cookie_name, 0 );


					$doc->addScriptDeclaration('var guide_cookie_name="'.$cookie_name.'";');
					$doc->addScript(JURI::root(true) . '/templates/' . $app->getTemplate() . '/js/vendor/jqueryui/jquery-ui-1.10.3.custom.min.js', 'text/javascript');
					$doc->addScript(JURI::root(true) . '/templates/' . $app->getTemplate() . '/js/guide.js', 'text/javascript','foot');
					$doc->addStyleSheet(JURI::root(true) . '/templates/' . $app->getTemplate() . '/css/guide.css');

					if ($stepNumber < $storedStep)
					{
						$config = JFactory::getConfig();
						$cookie_domain = $config->get('cookie_domain', '');
						$cookie_path = $config->get('cookie_path', '/');
						setcookie($cookie_name, $storedStep, time() + 365 * 86400, $cookie_path, $cookie_domain);

					} else if ($stepNumber <= $maxStep && $stepNumber > $storedStep) {

						$this->saveCurrentGuideStep($user->id, $stepNumber);

					} else if ($stepNumber > $maxStep) {

						$this->guideIsDone($user->id);
					}
				}
			}
		}
	}

	private function isToShowGuide($user_id=0){
	  if($user_id)
	  {
	    $db = JFactory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select("*")->from("#__user_profiles")->where($db->quoteName('user_id') . '=' . $user_id . ' and ' . $db->quoteName('profile_key') . '="guide.show" and ' . $db->quoteName('profile_value') . '=1');
	    $db->setQuery($query);
	    return count($db->loadColumn());
	  }
	  return false;
	}

	private function getCurrentGuideStep($user_id=0){
		if(!$user_id)
		{
		  return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('profile_value'))->from("#__user_profiles")
					->where($db->quoteName('user_id') . '=' . $user_id . ' and ' . $db->quoteName('profile_key') . '="guide.step"');

		$db->setQuery($query);

		$result = $db->loadColumn();

		if(count($result) === 1)
		{
			return $result[0];
		}

		return 0;
	}

	private function saveCurrentGuideStep($user_id=0,$stepNember=1){
		if(!$user_id)
		{
		  return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__user_profiles'))
					->set($db->quoteName('profile_value') . "=" . $stepNember)
					->where($db->quoteName('user_id') . '=' . $user_id)
					->where($db->quoteName('profile_key') . '="guide.step"');
		$db->setQuery($query);

		return $db->query();
	}

	private function guideIsDone($user_id=0){
		if(!$user_id)
		{
		  return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->delete("#__user_profiles")->where($db->quoteName('user_id') . '=' . $user_id)
					->where($db->quoteName('profile_key') . ' in ("guide.step","guide.show")');

		$db->setQuery($query);

		return $db->query();
	}
}
