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

class CreateSubscriptionApi {

      public function getSubscription($access_key)
      {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT id from #__js_res_record where user_id=129 AND access_key="'.$access_key.'"');
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
        $db->setQuery('DELETE FROM #__js_res_record_values WHERE record_id = '.$record_id.' AND field_id IN (66,114,69,71,72,73,78,112)');
        $db->execute();
        if(!self::insertDescription($record_id, $fields->{'66'})){$flag = false;}
        if(!self::insertProduct($record_id, $fields->{'114'})){$flag = false;}
        if(!self::insertPlan($record_id, $fields->{'69'})){$flag = false;}
        if(!self::insertStartDate($record_id, $fields->{'71'}[0])){$flag = false;}
        if(!self::insertEndDate($record_id, $fields->{'72'}[0])){$flag = false;}
        if(!self::insertSubscribingOrganization($record_id, $fields->{'73'})){$flag = false;}
        if(!self::inserStatus($record_id)){$flag = false;}
        if(!self::inserUuid($record_id, $fields->{'112'})){$flag = false;}

        return true;
      } 

      public function insertDescription($record_id=0, $value=''){
        $db = JFactory::getDbo();

        $field = new stdClass();
        $field->field_id=66;
        $field->field_type='html';
        $field->field_label='Description';
        $field->field_key='k'.md5($field->field_label.'-'.$field->field_type);
        $field->field_value=$value;
        $field->record_id=$record_id;
        $field->user_id='129';
        $field->type_id='10';
        $field->section_id='6';
        // $field->category_id='0'; TODO
        $field->params='';
        $field->ip=$_SERVER['REMOTE_ADDR'];
        $field->ctime=JFactory::getDate()->toSql();
        $field->value_index='0';

        $db->insertObject("#__js_res_record_values",$field,'id');
      }
      public function insertProduct($record_id=0, $value=''){
        $db = JFactory::getDbo();

        $field = new stdClass();
        $field->field_id=114;
        $field->field_type='child';
        $field->field_label='Product';
        $field->field_key='k'.md5($field->field_label.'-'.$field->field_type);
        $field->field_value=$value;
        $field->record_id=$record_id;
        $field->user_id='129';
        $field->type_id='10';
        $field->section_id='6';
        // $field->category_id='0';TODO
        $field->params='';
        $field->ip=$_SERVER['REMOTE_ADDR'];
        $field->ctime=JFactory::getDate()->toSql();
        $field->value_index='0';

        $db->insertObject("#__js_res_record_values",$field,'id');
      }
      public function insertPlan($record_id=0, $value=''){
        $db = JFactory::getDbo();

        $field = new stdClass();
        $field->field_id=69;
        $field->field_type='child';
        $field->field_label='Plan';
        $field->field_key='k'.md5($field->field_label.'-'.$field->field_type);
        $field->field_value=$value;
        $field->record_id=$record_id;
        $field->user_id='129';
        $field->type_id='10';
        $field->section_id='6';
        // $field->category_id='0'; TODO
        $field->params='';
        $field->ip=$_SERVER['REMOTE_ADDR'];
        $field->ctime=JFactory::getDate()->toSql();
        $field->value_index='0';

        $db->insertObject("#__js_res_record_values",$field,'id');
      }
      public function insertStartDate($record_id=0, $value=''){
        $db = JFactory::getDbo();

        $field = new stdClass();
        $field->field_id=71;
        $field->field_type='datetime';
        $field->field_label='Start Date';
        $field->field_key='k'.md5($field->field_label.'-'.$field->field_type);
        $field->field_value=$value;
        $field->record_id=$record_id;
        $field->user_id='129';
        $field->type_id='10';
        $field->section_id='6';
        // $field->category_id='0'; TODO
        $field->params='';
        $field->ip=$_SERVER['REMOTE_ADDR'];
        $field->ctime=JFactory::getDate()->toSql();
        $field->value_index='0';

        $db->insertObject("#__js_res_record_values",$field,'id');
      }
      public function insertEndDate($record_id=0, $value=''){
        $db = JFactory::getDbo();

        $field = new stdClass();
        $field->field_id=72;
        $field->field_type='html';
        $field->field_type='datetime';
        $field->field_label='End Date';
        $field->field_key='k'.md5($field->field_label.'-'.$field->field_type);
        $field->field_value=$value;
        $field->record_id=$record_id;
        $field->user_id='129';
        $field->type_id='10';
        $field->section_id='6';
        // $field->category_id='0'; TODO
        $field->params='';
        $field->ip=$_SERVER['REMOTE_ADDR'];
        $field->ctime=JFactory::getDate()->toSql();
        $field->value_index='0';

        $db->insertObject("#__js_res_record_values",$field,'id');
      }
      public function insertSubscribingOrganization($record_id=0, $value=''){
        $db = JFactory::getDbo();

        $field = new stdClass();
        $field->field_id=73;
        $field->field_type='child';
        $field->field_label='Subscribing Organization';
        $field->field_key='k'.md5($field->field_label.'-'.$field->field_type);
        $field->field_value=$value;
        $field->record_id=$record_id;
        $field->user_id='129';
        $field->type_id='10';
        $field->section_id='6';
        // $field->category_id='0'; TODO
        $field->params='';
        $field->ip=$_SERVER['REMOTE_ADDR'];
        $field->ctime=JFactory::getDate()->toSql();
        $field->value_index='0';

        $db->insertObject("#__js_res_record_values",$field,'id');
      }
      public function inserStatus($record_id=0, $value='Active'){
        $db = JFactory::getDbo();

        $field = new stdClass();
        $field->field_id=78;
        $field->field_type='radio';
        $field->field_label='Status';
        $field->field_key='k'.md5($field->field_label.'-'.$field->field_type);
        $field->field_value=$value;
        $field->record_id=$record_id;
        $field->user_id='129';
        $field->type_id='10';
        $field->section_id='6';
        // $field->category_id='0'; TODO
        $field->params='';
        $field->ip=$_SERVER['REMOTE_ADDR'];
        $field->ctime=JFactory::getDate()->toSql();
        $field->value_index='0';

        $db->insertObject("#__js_res_record_values",$field,'id');
      }

      public function inserUuid($record_id, $value=''){
        $db = JFactory::getDbo();

        $field = new stdClass();
        $field->field_id=112;
        $field->field_type='uuid';
        $field->field_label='uuid';
        $field->field_key='k'.md5($field->field_label.'-'.$field->field_type);
        $field->field_value=$value;
        $field->record_id=$record_id;
        $field->user_id='129';
        $field->type_id='10';
        $field->section_id='6';
        // $field->category_id='0'; TODO
        $field->params='';
        $field->ip=$_SERVER['REMOTE_ADDR'];
        $field->ctime=JFactory::getDate()->toSql();
        $field->value_index='0';

        $db->insertObject("#__js_res_record_values",$field,'id');
      }




      // public static function getGatewayForEnvironment()
      // {   
      //     $rid = JRequest::getInt('record_id');
      //     $exclude = explode(",", JRequest::getVar('exclude'));
      //     $iterm_ids = array();
      //     $db = JFactory::getDbo();
      //     $query = "select `id`,`fields` from #__js_res_record where section_id=3 and type_id=3";
      //     $db->setQuery($query);
      //     $gateways = $db->loadObjectList();
      //     foreach ($gateways as $key => $env_gate) {
      //       $fields = json_decode($env_gate->fields);
      //       if($fields->{"16"} && in_array($rid, $fields->{"16"})){
      //           $iterm_ids[] = $env_gate->id;
      //       }
      //     }
      //     $iterm_ids = JArrayHelper::arrayUnique($iterm_ids);
      //     pre($iterm_ids);
      //     return $iterm_ids;
      // }

}


?>
