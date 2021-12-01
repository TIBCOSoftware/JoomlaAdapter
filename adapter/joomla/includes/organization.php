<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
?>
<?php

defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.component.modellist');

require_once JPATH_ROOT . '/components/com_cobalt/library/php/helpers/helper.php';
require_once JPATH_ROOT . '/components/com_cobalt/models/record.php';
require_once JPATH_BASE . "/includes/api.php";
require_once  dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'cobaltfield.php';



class CreateOrganizationApi {

      // {
      //   "109":"a5389f85-9dc4-af78-c47a-7701386735d8",
      //   "17":"<p>ACME Product Data Organization<\/p>",
      //   "19":"cloudbus-admin@tibco.com",
      //   "48":null,
      //   "41":null,
      //   "43":null,
      //   "61":null,
      //   "20":{"address":{"country":"US","state":"CA","city":"Palo Alto","zip":"94304","address1":"3303 Hillview Ave","address2":""},
      //   "contacts":{"tel":"650-846-1000"}},
      //   "56":["118","119"],
      //   "74":["121"],
      //   "122":["partner"]
      // }
      
      public static function getUuid($prefix = ''){
          $chars  =  md5(uniqid(mt_rand(), true));
          $uuid   =  substr ( $chars ,0,8).'-';
          $uuid  .=  substr ( $chars ,8,4).'-';
          $uuid  .=  substr ( $chars ,12,4).'-';
          $uuid  .=  substr ( $chars ,16,4).'-';
          $uuid  .=  substr ( $chars ,20,12);
          return $prefix.$uuid;
      }

      public static function insertFields($record_id,$fields){
        $db = JFactory::getDbo();
        $flag = true;
        $db->setQuery('DELETE FROM #__js_res_record_values WHERE record_id = '.$record_id.' AND field_id IN (45,46,47,77,88,101,102,113)');
        $db->execute();

        $zip               = new autoCreationCobaltField($record_id, 20, 'geo', 'Contact Details', $fields->{'20'}->address->zip,'zip');
        $country           = new autoCreationCobaltField($record_id, 20, 'geo', 'Contact Details', $fields->{'20'}->address->country,'country');
        $city              = new autoCreationCobaltField($record_id, 20, 'geo', 'Contact Details', $fields->{'20'}->address->city,'city');
        $state             = new autoCreationCobaltField($record_id, 20, 'geo', 'Contact Details', $fields->{'20'}->address->state,'state');

        $email             = new autoCreationCobaltField($record_id, 19, 'email', 'Email', $fields->{'19'});
        $Description       = new autoCreationCobaltField($record_id, 17, 'html', 'Description', $fields->{'17'});
        $uuid              = new autoCreationCobaltField($record_id, 109, 'uuid', 'Uuid', $fields->{'109'});
        $OrganizationType  = new autoCreationCobaltField($record_id, 122, 'select', 'Organization type', $fields->{'122'}[0]);

        if(!$db->insertObject("#__js_res_record_values",$zip,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$country,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$city,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$state,'id')){$flag = false;}

        if(!$db->insertObject("#__js_res_record_values",$email,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$Description,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$uuid,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$OrganizationType,'id')){$flag = false;}

        return $flag;
      } 

      
      protected static function _getUserGroupId($org_id, $usrtype)
      {
        $db   = JFactory::getDbo(); 
        $query=$db->getQuery(true);
        $query->select("id")->from("#__usergroups")->where('title = "Organization '.$org_id.' '.$usrtype.'"');
        $db->setQuery($query);
        
        $result = $db->loadColumn();
        
        if(!empty($result))
        {
          return $result[0];
        }
        
        return false;
      }
    
      public function insertOrganization($options = array())
      {
        $db = JFactory::getDbo();  
        $organization = new stdClass();
        $app = JFactory::getApplication();
        $contact = new stdClass();
        $contact->address = (object) array("country"=>"US","state"=>$options['state'],"city"=>$options['city'],"zip"=>$options['zipcode'],"address1"=>$options['street'],"address2"=>"");
//         $contact->contacts = (object) array("tel"=>"000-000-0000");

        $fields = new stdClass();

        $user_id = isset($options['user']) ? $options['user']->get("id") : 129;
        $user = JFactory::getUser($user_id);
        $lang   = JFactory::getLanguage();

        $options['title'] = $options['title'] ? $options['title'] : 'Ping Organization'; 
        $options['type'] = $options['type'] ? $options['type'] : 'partner'; 
        
        $fields->{'17'}   =   '';
        $fields->{'19'}    =   $user->get('email');
        $fields->{'20'}    =   $contact;
        $fields->{'41'}    =   null;
        $fields->{'48'}    =   null;
        $fields->{'43'}    =   null;
        $fields->{'56'}    =   null;
        $fields->{'61'}    =   null;
        $fields->{'74'}    =   null;
        $fields->{'109'}   =   self::getUuid('');
        $fields->{'122'}    =  array($options['type']);

        $organization->id          =   null;
        $organization->title       =   trim($options['title']);
        $organization->published   =   1;
        $organization->access      =   2;
        $organization->user_id     =   129;
        $organization->section_id  =   4;
        $organization->ctime       =   JFactory::getDate()->toSql();
        $organization->extime      =   '';
        $organization->mtime       =   JFactory::getDate()->toSql();
        $organization->inittime    =   JFactory::getDate()->toSql();
        $organization->ftime       =   '';
        $organization->type_id     =   5;
        $organization->parent_id   =   0;
        $organization->meta_descr  =   '';
        $organization->meta_key    =   '';
        $organization->meta_index  =   '';
        $organization->alias       =   JApplication::stringURLSafe(strip_tags($organization->title));
        $organization->featured    =   0;
        $organization->archive     =   0;
        $organization->ucatid      =   0;
        $organization->langs       =   $lang->getTag();
        $organization->ip          =   $_SERVER['REMOTE_ADDR'];
        $organization->hidden      =   0;
        $organization->access_key  =   md5(time() . $_SERVER['REMOTE_ADDR'] . $organization->title);
        $organization->fields      =   json_encode($fields);
        $organization->fieldsdata  =   $organization->title . " " . $organization->alias;
        $organization->categories  =   "[]";
        
        
        $record_id                 =   0;
        if($db->insertObject("#__js_res_record",$organization,'id'))
        {
          $record_id = $db->insertid();
          if($record_id && self::insertFields($record_id,$fields)){
              DeveloperPortalApi::createUserGroups($record_id);
          }
        }


        $app->setUserState('com_users.registration.new_org_id', $record_id);

        return $record_id;
      }

}


?>