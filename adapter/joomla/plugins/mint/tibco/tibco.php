<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  mint.tibco
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Finder Content Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.mint
 * @since       2.5
 */
class PlgMintTibco extends JPlugin
{
	/**
	 * Finder after save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param   string  The context of the content passed to the plugin (added in 1.6)
	 * @param   object		A JTableContent object
	 * @param   bool		If the content has just been created
	 * @since	2.5
	 */
	public function onAfterArticleSaved($isnew, $record, $fields, $section, $type)
	{
		$flag = true;
		if($isnew && $section->id == 1 && $type->id == 1){
      $entry = new stdClass();
      $entry->product_id = $record->id;
			$db = JFactory::getDbo();
      $flag = $db->insertObject("asg_product_show_map",$entry,'product_id');

		}
		
		if($isnew && $section->id == 6 && $type->id == 10){
			$product_id = JRequest::getVar("sub_product_id",0);
			if($product_id)
			{
				require_once JPATH_BASE ."/components/com_cobalt/library/php/helpers/itemsstore.php";
				require_once JPATH_BASE ."/components/com_cobalt/api.php";
				$app = JFactory::getApplication();
				$product = ItemsStore::getRecord($product_id);
				$product_url = JRoute::_(Url::record($product));
				/** intentionally comment out this line to prevent a pre-matured redirection which will
				 * prevent the DeveloperPortal.submitForm() method in the developer_portal.js file gets
				 * a wrong record id
				 */
				//		$app->redirect($product_url);
			}
		}

        if(!$isnew && $section->id == 4 && $type->id == 8)
        {
            require_once JPATH_BASE ."/includes/api.php";

            $uid_of_profile = (int) DeveloperPortalApi::getUserIdByProfileId($record->id);
            if($uid_of_profile)
            {
                $user = JFactory::getUser($uid_of_profile);
                if($user->id)
                {
                    $user->name = $record->title;
                }
            }
        }

		return $flag;
	}
}
