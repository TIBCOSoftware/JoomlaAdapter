<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
?>
<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.selcobalttype
 * @author Jacky love0.0chen@gmail.com
 */
defined('_JEXEC') or die;

/**
 * Joomla! SELCOBALTTYPE Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  System.Selcobalttype
 * @since       3.1
 */
class PlgSystemSelcobalttype extends JPlugin
{
	/**
	 * Add a default filter for cobalt section
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	public function onAfterRoute()
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		if ($app->getName() != 'site' || $doc->getType() !== 'html' || JRequest::getVar('mode') == 'form' || JRequest::getvar('view') == 'elements')
		{
			return true;
		}

		$uri     = JUri::getInstance();
		$router = $app->getRouter();

		if($section_id = $app->input->get('section_id'))
		{
			// echo 
			switch ($section_id) {
				case 1:
					$needType = 1;//prod
					break;
				case 2:
					$needType = 2;//api
					break;
				case 3:
					$needType = 4;//env
					break;
				case 4:
					$needType = 5;//org
					break;
				case 5:
					$needType = 9;//app
					break;
				case 6:
					$needType = 10;//sub
					break;
				case 7:
					$needType = 12;//scope
					break;
				default:
					$needType = 0;
					break;
			}
			$app->input->set('filter_type',$needType);
		}
	}
}
