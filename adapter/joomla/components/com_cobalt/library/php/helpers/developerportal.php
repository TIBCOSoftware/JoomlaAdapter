<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
?>
<?php

defined('_JEXEC') or die();

class DeveloperPortalFormatHelper {
    
    /*
     * Edit by Hank 2013.09.04
     * Used for specified 'list' style in some template
     */
    public static function bookmark_list($record, $type, $params)
    {
        $user = JFactory::getUser();

        if(! $user->get('id'))
        {
            return NULL;
        }

        if(! in_array($type->params->get('properties.item_can_favorite'), $user->getAuthorisedViewLevels()))
        {
            return NULL;
        }

        $file = JURI::root() . 'media/mint/icons/bookmarks/' . $params->get('tmpl_core.bookmark_icons', 'star') . '/state' . (int)($record->bookmarked > 0) . '.png';
        $alt = ($record->bookmarked ? JText::_('CMSG_REMOVEBOOKMARK') : JText::_('CMSG_ADDBOOKMARK'));
        $attr = array('data-original-title' => $alt, 'rel' => 'tooltip', 'id' => 'bookmark_' . $record->id);
        $out = JHtml::image($file, $alt, $attr);

        return sprintf('<a onclick="Cobalt.bookmarkRecord(%d, \'%s\', %d);">%s Bookmark</a>',
            $record->id, $params->get('tmpl_core.bookmark_icons', 'star'), JRequest::getInt('section_id'), $out);
    }
    

    /*
     * Edit by Hank 2013.09.04
     * Used for specified 'list' style in some template
     */
    public static function approve_publish($record, $is_to_show)
    {
        $user = JFactory::getUser();

        if(! $user->get('id'))
        {
            return NULL;
        }


        $file = JURI::root() . 'media/mint/icons/16/auction-hammer.png';
        $alt = ($record->bookmarked ? JText::_('CMSG_REMOVEBOOKMARK') : JText::_('CMSG_ADDBOOKMARK'));
        $attr = array('data-original-title' => $alt, 'rel' => 'tooltip', 'id' => 'bookmark_' . $record->id);
        $out = JHtml::image($file, $alt, $attr);
        $result = '';
        if($is_to_show){
            $result = sprintf('<a href="javascript:void(0)" onclick="DeveloperPortal.approvePublish('.$record->id.',1)">%s Show</a>', $out);
        }else{
            $result = sprintf('<a href="javascript:void(0)" onclick="DeveloperPortal.approvePublish('.$record->id.',0)">%s Hide</a>', $out);
        }
        return $result;
    }

    public static function follow_list($record, $section)
    {
        $user = JFactory::getUser();

        if(! $user->get('id'))
        {
            return NULL;
        }

        if(! in_array($section->params->get('events.subscribe_record'), $user->getAuthorisedViewLevels()))
        {
            return NULL;
        }

        $file = JURI::root() . 'media/mint/icons/16/follow' . (int)($record->subscribed > 0) . '.png';
        $alt = ($record->subscribed ? JText::_('CMSG_CLICKTOUNFOLLOW') : JText::_('CMSG_CLICKTOFOLLOW'));
        $attr = array('data-original-title' => $alt, 'rel' => 'tooltip', 'id' => 'follow_record_' . $record->id);
        $out = JHtml::image($file, $alt, $attr);

        return sprintf('<a onclick="Cobalt.followRecord(%d, %d);">%s Follow</a>',
            $record->id, JRequest::getInt('section_id'), $out);
    }

}

?>