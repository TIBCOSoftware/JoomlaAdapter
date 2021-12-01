<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Library of functions specific to TIBCO Software's OpenAPI
 *
 * @since       1.0
 */
class TibcoAutoSubPlan
{
  /**
     * Check if the product is exist in application
     * @param  [integer] $app_id     id of application
     * @param  [integer] $product_id id of product
     * @return [boolean]             if the product is existing in application, return true, otherwise return false.
     */
    public static function checkProductsForApplication($app_id,$product_id){
      $flag = false;
      $db =JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->select("id")->from("#__js_res_record_values")->where("`field_id`=62 and `field_value`=".$app_id." and `record_id`=".$product_id);
      $db->setQuery($query);
      $result = $db->loadColumn();

      if(!empty($result)){
        $flag = true;
      }
      return $flag;
    }

    /**
     * Bind new subscription to the application
     * 
     * At first we need get the subscriptions which have been bound to the application 
     * and to see whether there is any available subscription, if there is, we delete 
     * application from that subscriptin first.And then bind the new subscription to the application
     *
     * 
     * @param  [integer] $app_id                id of application
     * @param  [integer] $product_id            id of product
     * @param  [integer] $new_subscription_id   id of new created subscription
     * @return [boolean] if action is played successfully, return true, otherwise return false;
     */
    public static function updateSubscriptionForApplication($app_id, $product_id, $new_subscription_id){
    	$flag = array();
    	
      $subscriptionsOfApplication = self::getSubscriptionsByApplicationId($app_id);
      $subscriptionsOfProduct = self::getSubscriptionsByProductId($product_id);

      if(!empty($subscriptionsOfApplication) && !empty($subscriptionsOfProduct)){
        $subscriptions = array_intersect($subscriptionsOfApplication,$subscriptionsOfProduct);

        if(!empty($subscriptions)){
          $subscription_ids = self::getAvailableSubscriptionByIds($subscriptions);
          if(!empty($subscription_ids)){
            self::deleteOldSubscriptions($subscription_ids,$app_id);
          }
          
        }
      }

    if(self::bindNewSubscriptionForApplicationId($app_id,$new_subscription_id))
      {
        $flag = $subscription_ids ? $subscription_ids : array();
      }
      

      return $flag;
    }

    /**
     * Get all of the subscriptions that have been bound to the application
     * @param  [integer] $app_id    id of the application
     * @return [array]   a sort of subscriptions
     */
    public static function getSubscriptionsByApplicationId($app_id){ 
      $db =JFactory::getDbo(); 
      $query= $db->getQuery(true); 

      $query->select("record_id")->from("#__js_res_record_values")
            ->where("`field_id`=116 and`field_value`=".$app_id); 

      $db->setQuery($query); 

      return $db->loadColumn(); 
    }

    /**
     * Get all of the subscriptions that contain the product
     * @param  [integer] $product    id of the product
     * @return [array]   a sort of subscriptions
     */
    public static function getSubscriptionsByProductId($product_id){
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->select("record_id")->from("#__js_res_record_values")->where("`field_id`=114 and `field_value`=".$product_id);
      $db->setQuery($query);
      return $db->loadColumn();
    }

    /**
     * Delete the bound relationship between application and subscription
     * @param  [array]   $subscription_id the sort of subscription's id
     * @param  [integer] $app_id          id of application
     * @return [integer]                  the number of how many reocords have been deleted
     */
    public static function deleteOldSubscriptions($subscription_ids,$app_id){
      $db = JFactory::getDbo(); 
      $query = 'DELETE FROM #__js_res_record_values where field_id=116 and field_value='.$app_id.' and record_id in('.implode(",", $subscription_ids).')';
      $db->setQuery($query);

      $db->loadColumn();
    }


    /**
     * Bind the new subscription to the application
     * @param  [integer] $app_id                which application will be bound to subscription
     * @param  [integer] $new_subscription_id   id of new subscription
     * @return [boolean]                        the result of excute
     */
    public static function bindNewSubscriptionForApplicationId($app_id,$new_subscription_id){
      $flag = false;
      $db = JFactory::getDbo();

      $field = new stdClass();
      $field->field_id=116;
      $field->field_type='child';
      $field->field_label='Owner application';
      $field->field_key='k'.md5($field->field_label.'-'.$field->field_type);
      $field->field_value=$app_id;
      $field->record_id=$new_subscription_id;
      $field->user_id='129';
      $field->type_id='10';
      $field->section_id='6';
      // $field->category_id='0'; TODO
      $field->params='';
      $field->ip=$_SERVER['REMOTE_ADDR'];
      $field->ctime=JFactory::getDate()->toSql();
      $field->value_index='0';

      $db->insertObject("#__js_res_record_values",$field,'id');

      if($db->insertid()){
        $flag = true;
      }

      return $flag;
    }

    /**
     * Bind the product to the application
     * @param  [integer] $app_id                which application will be bound to subscription
     * @param  [integer] $product               id of product
     * @return [boolean]                        the result of excute
     */
    public static function bindProductForApplicationId($app_id,$product_id){
      $flag = false;
      $db = JFactory::getDbo();

      $field = new stdClass();
      $field->field_id=62;
      $field->field_type='child';
      $field->field_label='Used by applications';
      $field->field_key='k'.md5($field->field_label.'-'.$field->field_type);
      $field->field_value=$app_id;
      $field->record_id=$product_id;
      $field->user_id='129';
      $field->type_id='1';
      $field->section_id='1';
      // $field->category_id='0'; TODO
      $field->params='';
      $field->ip=$_SERVER['REMOTE_ADDR'];
      $field->ctime=JFactory::getDate()->toSql();
      $field->value_index='0';

      $db->insertObject("#__js_res_record_values",$field,'id');

      if($db->insertid()){
        $flag = self::updateJsonFieldsForApplication($app_id,$product_id);
      }

      return $flag;
    }


    public static function updateJsonFieldsForApplication($app_id,$product_id){
      $flag = false;
      $application = self::getApplicationById($app_id);

      $fields = json_decode($application->fields);

      $products = $fields->{'63'};

      if(in_array($product_id, $products)){
        $flag = true;
      }else{
        $products[] = (String)$product_id;

        $fields->{'63'} = $products;

        $application->fields = json_encode($fields);

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__js_res_record'))->set($db->quoteName('fields') . "='" . (string)$application->fields . "'")->where($db->quoteName('id') . '=' . $app_id);
        $db->setQuery($query);

        if($db->query()){
          $flag = true;
        }
      }
      return $flag;
    }

    public static function getApplicationById($app_id){
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->select("*")->from("#__js_res_record")->where("id=".$app_id);
      $db->setQuery($query);
      return $db->loadObject();
    }
    /**
     * filter out all of the available subscriptions from the applications's subscrition
     * @param  [array] $subscriptions  a sort of subscriptions
     * @return [array]                a sort of vailable subscriptions
     */
    public static function getAvailableSubscriptionByIds($subscriptions){
      return array_intersect(self::getAvailableSubscriptionStartedBeforeToday($subscriptions),self::getAvailableSubscriptionEndAfterToday($subscriptions));
    }

    /**
     * Get the subscriptions whose start time before today
     * @param  [array] $subscriptions  a sort of subscriptions
     * @return [array]                 a sort of vailable subscriptions
     */
    public static function getAvailableSubscriptionStartedBeforeToday($subscriptions){
      $db = JFactory::getDbo();
      $query = 'SELECT record_id from #__js_res_record_values where `field_id`=71 and `field_value`<="'.date("Y-m-d",time()).'" and record_id in('.implode(",",$subscriptions).')';
      $db->setQuery($query);
      return $db->loadColumn();
    }

    /**
     * Get the subscriptions whose end time after today
     * @param  [array] $subscriptions  a sort of subscriptions
     * @return [array]                 a sort of vailable subscriptions
     */
    public static function getAvailableSubscriptionEndAfterToday($subscriptions){
      $db = JFactory::getDbo();
      $query = 'SELECT record_id from #__js_res_record_values where `field_id`=72 and `field_value`>="'.date("Y-m-d",time()).'" and record_id in('.implode(",",$subscriptions).')';
      $db->setQuery($query);
      return $db->loadColumn();
    }


}
