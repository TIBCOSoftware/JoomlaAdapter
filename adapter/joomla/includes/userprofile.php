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
require_once  dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'cobaltfield.php';



class CreateUserprofileApi {

      /**
       * field_id 45   => first name  <text>
       * field_id 46   => last name <text>
       * field_id 47   => member of organizations <child>
       * field_id 50   => contact for organizations  <parent>
       * field_id 49   => contact for products  <parent>
       * field_id 51   => contact for apis <parent>
       * field_id 52   => contact for plans <parent>
       * field_id 59   => contact for applications <parent>
       * field_id 68   => contact for subscriptions <parent>
       * field_id 77   => system user <puser>
       * field_id 88   => user type <select>
       * field_id 101  => username <text>
       * field_id 102  => email <email>
       * field_id 113  => uuid
       * field_id 121  => contact phone number <digits>
       */
      
      public function getUserprofile($access_key)
      {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT id from #__js_res_record where user_id=129 AND type_id = 8 AND access_key="'.$access_key.'"');
        return $db->loadObject()->id;
      }

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


        $firstName              = new autoCreationCobaltField($record_id, 45, 'text', 'First Name', $fields->{'45'});
        $lastName               = new autoCreationCobaltField($record_id, 46, 'text', 'Last Name', $fields->{'46'});
        $sysUser                = new autoCreationCobaltField($record_id, 77, 'puser', 'System User', $fields->{'77'});
        $userType               = new autoCreationCobaltField($record_id, 88, 'select', 'User Type', $fields->{'88'});
        $username               = new autoCreationCobaltField($record_id, 101, 'text', 'Username', $fields->{'101'});
        $email                  = new autoCreationCobaltField($record_id, 102, 'email', 'Email', $fields->{'102'});
        $uuid                   = new autoCreationCobaltField($record_id, 113, 'uuid', 'Uuid', $fields->{'113'});
        $org_ids                = $fields->{'47'};


        if(!$db->insertObject("#__js_res_record_values",$firstName,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$lastName,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$meberOfOrganization,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$sysUser,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$userType,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$username,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$email,'id')){$flag = false;}
        if(!$db->insertObject("#__js_res_record_values",$uuid,'id')){$flag = false;}

        foreach ($org_ids as $org_id) 
        {
          $meberOfOrganization   = new autoCreationCobaltField($record_id, 47, 'child', 'Member of Organizations', $org_id);
          if(!$db->insertObject("#__js_res_record_values",$meberOfOrganization,'id'))
          {
            $flag = false;
          }
		 
          self::updateOrganizationMembers($record_id, $org_id);

        }


        return $flag;
      } 

      protected static function updateOrganizationMembers($record_id, $org_id)
      {
        $db             = JFactory::getDbo();
        $query          = $db->getQuery(true);

        $record         = ItemsStore::getRecord($org_id);

        $fields         = json_decode($record->fields);
        $mebers         = ($fields->{'56'} && is_array($fields->{'56'}))?$fields->{'56'}:array();
        $mebers[]       = $record_id;
        $fields->{'56'} = $mebers;
        $fields         = json_encode($fields);
        $record->fields = $fields;
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__js_res_record'))->set($db->quoteName('fields') . "='" . $record->fields ."'")->where($db->quoteName('id') . '=' . $org_id);
        $db->setQuery($query);

        if($db->query())
        {
            return true;
        }

        return false;
      }
	
		
	    protected static function attachUserToUsergroup($org_id, $usrtype='')
      {
        $db   = JFactory::getDbo(); 
    		$user = JFactory::getUser();
        
        $usrtype = $usrtype?$usrtype:'Member';
        $ugroup_id = self::_getUserGroupId($org_id, $usrtype);
        
        if(!$ugroup_id || in_array($ugroup_id, $user->getAuthorisedGroups()))
        {
          return false;
        }
        
        $query = 'INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES ('.$user->id.','.$ugroup_id.')';
        $db->setQuery($query);
        
        if($db->query())
        {
          return true;
        }
        
        return false;
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
      
      
      /**
       * field_id 45   => first name  <text>
       * field_id 46   => last name <text>
       * field_id 47   => member of organizations <child>
       * field_id 50   => contact for organizations  <parent>
       * field_id 49   => contact for products  <parent>
       * field_id 51   => contact for apis <parent>
       * field_id 52   => contact for plans <parent>
       * field_id 59   => contact for applications <parent>
       * field_id 68   => contact for subscriptions <parent>
       * field_id 77   => system user <puser>
       * field_id 88   => user type <select>
       * field_id 101  => username <text>
       * field_id 102  => email <email>
       * field_id 113  => uuid
       * field_id 121  => contact phone number <digits>
       */
      public function insertUserProfile($options = array())
      {
        $db = JFactory::getDbo();
        $userprofile = new stdClass();
        $fields = new stdClass();
        $user = $options['user_id'] ? JFactory::getUser($options['user_id']):JFactory::getUser();
        $lang   = JFactory::getLanguage();

        $options['lastName'] = $options['lastName'] ? $options['lastName']: 'Ping'; 
        $options['firstName'] = $options['firstName'] ? $options['firstName']: 'Ping'; 
        $options['org_id'] = $options['org_id'] ? $options['org_id']: 143; 
        $options['usertype'] = $options['usertype'] ? $options['usertype']:'Member'; 
        
        
        $fields->{'45'}    =   $options['firstName'];
        $fields->{'46'}    =   $options['lastName'];
        $fields->{'47'}    =   array($options['org_id']);
        $fields->{'49'}    =   null;
        $fields->{'50'}    =   null;
        $fields->{'51'}    =   null;
        $fields->{'52'}    =   null;
        $fields->{'59'}    =   null;
        $fields->{'68'}    =   null;
        $fields->{'77'}    =   $user->id;
        $fields->{'88'}    =   $options['usertype'];
        $fields->{'101'}   =   $user->username;
        $fields->{'102'}   =   $user->email;
        $fields->{'113'}   =   self::getUuid('');
        $fields->{'121'}   =   $options['phone-no'] ? $options['phone-no']:'';


        $userprofile->id          =   null;
        $userprofile->title       =   trim($user->name);
        $userprofile->published   =   1;
        $userprofile->access      =   2;
        $userprofile->user_id     =   129;
        $userprofile->section_id  =   4;
        $userprofile->ctime       =   JFactory::getDate()->toSql();
        $userprofile->extime      =   '';
        $userprofile->mtime       =   JFactory::getDate()->toSql();
        $userprofile->inittime    =   JFactory::getDate()->toSql();
        $userprofile->ftime       =   '';
        $userprofile->type_id     =   8;
        $userprofile->meta_descr  =   '';
        $userprofile->meta_key    =   '';
        $userprofile->meta_index  =   '';
        $userprofile->alias       =   JApplication::stringURLSafe(strip_tags($userprofile->title));
        $userprofile->featured    =   0;
        $userprofile->archive     =   0;
        $userprofile->ucatid      =   0;
        $userprofile->langs       =   $lang->getTag();
        $userprofile->ip          =   $_SERVER['REMOTE_ADDR'];
        $userprofile->hidden      =   0;
        $userprofile->access_key  =   md5(time() . $_SERVER['REMOTE_ADDR'] . $userprofile->title);
        $userprofile->fields      =   json_encode($fields);
       	
		    
        if($db->insertObject("#__js_res_record",$userprofile,'id'))
        {
          $record_id = self::getUserprofile($userprofile->access_key);
          if($record_id && self::insertFields($record_id,$fields) && self::attachUserToUsergroup($options['org_id'],$options['usertype'])){
              return true;
          }
        }
        
        return false;
      }



}


?>