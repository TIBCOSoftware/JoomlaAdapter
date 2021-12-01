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

class DeveloperPortalApi {
    const TASK_ARCHIVE = "records.sarchive";
    const TASK_EDIT = "form.edit";
    const TASK_DELETE = "records.delete";
    const TASK_PUBLISH = "records.spub";
    const TASK_UNPUBLISH = "records.sunpub";
    const TASK_HIDE = "records.shide";
    const TASK_MARK_FEATURED = "records.sfeatured";
    const USER_TYPE_MEMBER = "Member";
    const USER_TYPE_CONTACT = "Contact";
    const USER_TYPE_MANAGER = "Manager";

    public static function list_controls($ctrls, $tasks_to_hide = array(), $rec_id = 0, $type_id = 0) {
    	$out = "";
      array_push($tasks_to_hide, DeveloperPortalApi::TASK_ARCHIVE, DeveloperPortalApi::TASK_PUBLISH, DeveloperPortalApi::TASK_UNPUBLISH, DeveloperPortalApi::TASK_HIDE);
	  // Change associated records message based on type
      switch ($type_id)
      {
      	case '5':
      		$can_not_delete_msg = 'CAN_NOT_DELETE_ORGANIZATION_HAS_ATTACHED_OBJECTS';
      		break;

      	default:
      		$can_not_delete_msg = 'CAN_NOT_DELETE_OBJECT_HAS_ATTACHED_OBJECTS';
      		break;
      }
        if(isset($ctrls)) {

            foreach($ctrls as $key => $link) {
        		if(is_array($link)) {
        			$out .= "<li class=\"dropdown-submenu\">" . $key;
        			$out .= "<ul class=\"dropdown-menu\">";
        			$out .= DeveloperPortalApi::list_controls($link);
        			$out .= "</ul></li>";
                } else {
        		    if(!DeveloperPortalApi::hide_link($link, $tasks_to_hide)) {
        		        if(strpos($link, DeveloperPortalApi::TASK_DELETE) != FALSE) {
                        if(in_array($type_id, array(5)) && self::getObejectCountsAttachedToCotentType($type_id, $rec_id))
                        {
                           $out .= "<li>" . preg_replace('/href="[^"]+"/', 'href="javascript:void(0)" onclick="Joomla.showError([\''.JText::_($can_not_delete_msg).'\']);return false;"', $link) . "</li>";
                        } else {
        		               $out .= "<li>" . preg_replace('/href="[^"]+"/', 'href="javascript:void(0)" onclick="DeveloperPortal.archiveRecord(' . $rec_id . ', ' . $type_id . ')"', $link) . "</li>";
                        }
        		        } else if(!strpos($link, DeveloperPortalApi::TASK_MARK_FEATURED) || (strpos($link, DeveloperPortalApi::TASK_MARK_FEATURED) && $type_id == 1)) {
        			        $out .= "<li>{$link}</li>";
        		        }
        		    }
        		}
        	}
        }
    	return $out;
    }

    private static function hide_link($link, $tasks_to_hide = array()) {
        $rv = FALSE;
        if(isset($link)) {
            foreach($tasks_to_hide as $task) {
                if(strpos($link, $task) != FALSE) {
                    $rv = TRUE;
                    break;
                }
            }
        }
        return $rv;
    }

    /**
     * Edit by Hank
     * Get a record object using id
     * @param string
     */
    public static function getRecordById($record_id) {
        $record = ItemsStore::getRecord($record_id);
        return $record;
    }

    public static function productsForSelect() {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $db -> setQuery('select * from #__js_res_record as a WHERE a.id in
(SELECT record_id FROM #__js_res_record_values
where type_id=10)');
      
        if ($result = $db -> loadObjectList()) {
            return $result;
        }
        return array();
    }

    public static function productIdForSubscription($field_id, $subscriptionID) {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $db -> setQuery('select field_value from #__js_res_record_values as a WHERE a.field_id=' . $field_id . ' and a.record_id=' . $subscriptionID);
        if ($result = $db -> loadObjectList()) {
            return $result;
        }
        return array();
    }

    public static function valueForKeyFromJson($jsonStr, $destKey, $recordId=0) {
        unset($destValue);
        $destValue = null;

		if ($recordId>0) {
			$db = JFactory::getDbo();
			$db -> setQuery('select field_value from #__js_res_record_values as a WHERE a.field_id=' . $destKey . ' and a.record_id=' . $recordId);
			$results = array();
			foreach ($db -> loadObjectList() as $value) {
				$results[] = $value->field_value;
			}

			if (count($results)==1) {
				return $results[0];
			}else {
				return $results;
			}
		}else{
	        $fieldsArray = json_decode($jsonStr);
	        foreach ($fieldsArray as $k => $v) {
	            if ($k == $destKey) {
	                $destValue = $v;
	                break;
	            }
	        }
	        return $destValue;
		}
    }

	public static function classify_subscriptions($subscriptions){
		$arr = array();
		if (!empty($subscriptions)) {
			foreach ($subscriptions as $subscription) {
				$product = DeveloperPortalApi::valueForKeyFromJson("",114,$subscription->id);
				$arr[$product][] = $subscription;
			}
		}

		return $arr;
	}

	public static function list_plan_controls($originControls,$planId=0) {
		if(! $originControls) return;
		$out = '';
		$db = JFactory::getDbo();
		foreach($originControls as $key => $link){
			if(is_array($link)){
				$out .= '<li class="dropdown-submenu">' . $key;
				$out .= '<ul class="dropdown-menu">';
				$out .= DeveloperPortalApi::list_plan_controls($link);
				$out .= "</ul></li>";
			} else{
				if ($planId && strpos($link,"task=records.sunpub")) {
					$db -> setQuery("SELECT id FROM #__js_res_record WHERE id in (SELECT record_id FROM #__js_res_record_values WHERE record_id IN (SELECT record_id FROM #__js_res_record_values WHERE field_id =69 AND field_value =".$planId.") AND field_id =78 AND field_value =  'Active') and published <> 2");
					$subscriptions = $db -> loadObjectList();
					if (count($subscriptions)>0) {
						continue;
					}
				}else if($planId && strpos($link,"task=records.delete")){
					$db -> setQuery("SELECT id FROM #__js_res_record WHERE id in (SELECT record_id FROM #__js_res_record_values WHERE field_id=69 and field_value=".$planId.") and published<>2");
					$subscriptions = $db -> loadObjectList();
					if (count($subscriptions)>0) {
						continue;
					}
				}
				$out .= "<li>{$link}</li>";
			}
		}
		return $out;
	}

    public static function subscriptionsOfOrgnazions($orgnazionId) {
        $db = JFactory::getDbo();
        $db -> setQuery('select * from #__js_res_record WHERE id in (select record_id from #__js_res_record_values where field_id=73 and field_value='.$orgnazionId.') and published <> 2');
        return $db -> loadObjectList();
    }

	public static function subscriptionsInApplication($appId){
        $db = JFactory::getDbo();
	      $db -> setQuery('select * from #__js_res_record WHERE id in (select record_id from #__js_res_record_values where field_id=116 and field_value='.$appId.') and published <> 2');
		    $results = array();
    		foreach ($db -> loadObjectList() as $value) {
    			$results[] = $value->id;
    		}
        return $results;
	}

    /**
     * Get all applications for the current user.
     *
     * @return array An array of application ids if any, otherwise an empty array will be returned.
     */
    public static function getApplicationsForUser() {
        $rv = array();
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $sql = "SELECT * FROM #__js_res_record WHERE
              section_id=5 AND archive=0 AND published=1 AND hidden=0 AND id in
          (SELECT record_id FROM `#__js_res_record_values`
                WHERE field_id=60 and type_id=9 and section_id=5 and
              field_value in (SELECT field_value from #__js_res_record_values
                WHERE field_id=47 and type_id=8 and section_id=4 and
                  record_id in (SELECT id FROM #__js_res_record
                      WHERE id in (SELECT record_id FROM #__js_res_record_values
                        WHERE field_id=77 and type_id=8 and section_id=4 and field_value =" . $user->id . "))))
                          OR id in (SELECT id FROM #__js_res_subscribe WHERE user_id = " . $user->id . " AND `type` = 'record' AND section_id = 5)
                            GROUP BY id ORDER BY ctime DESC";
        $db -> setQuery($sql);
        if ($result = $db -> loadObjectList()) {
            foreach($result as $record) {
                array_push($rv, $record->id);
            }
        }
        return $rv;
    }
    // Get the application's active keys of current organization by using product id
    public static function getActiveKeysOfCurOrgByProdId($productId) {
      $ret = array();
      $db = JFactory::getDbo();
      $user = JFactory::getUser();
      $sql = "SELECT field_value FROM #__js_res_record_values WHERE record_id IN (SELECT record_id FROM #__js_res_record_values WHERE field_id=77 AND field_value=" . $user->id . ") and field_id=47";
      $db->setQuery($sql);
      if ($orgId = $db -> loadObject()->field_value) {
        $sql = "select t.application_id, t.title, t.user_id from #__js_res_record_values rv, (select r.id as application_id, r.title, r.user_id from #__js_res_record r, #__js_res_record_values rv where rv.field_id=60 and rv.field_value=".$orgId." and rv.record_id=r.id and r.archive=0 and r.published<>2) t where rv.record_id=".$productId." and rv.field_id=62 and rv.field_value=t.application_id";
        $db->setQuery($sql);
        if($result = $db->loadObjectList()) {
          foreach($result as $object) {
            $object->active_key = DeveloperPortalApi::getActiveKeyOfApplication($object->application_id);
            $ret[] = $object;
          }
        }
      }
      return $ret;
    }
    /**
     * Get the ids of all plans along with ids of the products to which they belong.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @return array An associative array with plan id as the key and product id as the value.
     */
    public static function getPlansWithProducts() {
        $rv = array();
        $db = JFactory::getDbo();
        $sql = "SELECT r.id AS plan_id, rv.field_value AS product_id FROM #__js_res_record AS r, #__js_res_record_values AS rv WHERE rv.field_id=53 AND rv.type_id=7 AND rv.record_id=r.id";
        $db->setQuery($sql);
        if($result = $db->loadObjectList()) {
            foreach($result as $object) {
                $rv[$object->plan_id] = $object->product_id;
            }
        }
        return $rv;
    }

     /**
     * Get some of the Fields that related to Plans, Almost consists of a kind of Model.
     *
     * @author Dyami Delia ddella@tibco.com
     * @return an Array of Plan Deatils based of the DB Query
     */
    public static function getPlanModel() {
        $rv = array();
        $db = JFactory::getDbo();
        $sql = "SELECT  r.fields AS plan_fields, r.title AS plan_title, rv.field_value AS product_id, r.id AS id FROM #__js_res_record AS r, #__js_res_record_values AS rv WHERE rv.field_id=53 AND rv.type_id=7 AND rv.record_id=r.id";
        $db->setQuery($sql);
        $result = $db->loadObjectList();

        return $result;
    }

    /**
     * Get all the plans that the organization(s) to which the user specified by $user_profile_id belongs has subscribed to.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @return array An array of plans objects if any, otherwise an empty array will be returned.
     */
    public static function getPlansForUser() {

    }

    /**
     * Get all the products that the organization(s) to which the user specified by $user_profile_id belongs has subscribed to.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @return array An array of product objects if any, otherwise an empty array will be returned.
     */
    public static function getProductsForUser() {
        $rv = array();
            $db = JFactory::getDbo();
            $user_profile_id = DeveloperPortalApi::getUserProfileId();
//             $sql = "SELECT * FROM #__js_res_record WHERE id IN ";
//             $sql .= "(SELECT field_value FROM #__js_res_record_values WHERE record_id IN ";
//             $sql .= "(SELECT record_id FROM #__js_res_record_values WHERE type_id=10 AND IF(field_id=72, DATEDIFF(field_value, CURDATE()) >= 0, 1=1) AND record_id IN ";
//             $sql .= "(SELECT record_id FROM #__js_res_record_values WHERE type_id=10 AND field_id=71 AND DATEDIFF(CURDATE(), field_value) >= 0 AND record_id IN ";
//             $sql .= "(SELECT record_id FROM #__js_res_record_values WHERE type_id=10 AND field_id=73 AND field_value IN ";
//             $sql .= "(SELECT field_value FROM #__js_res_record_values WHERE record_id=" . $user_profile_id . " AND field_id=47 AND type_id=8)))) ";
//             $sql .= "AND field_id=114 AND type_id=10)";

            $sql= "SELECT * FROM #__js_res_record as productRecord, #__js_res_record_values AS product, #__js_res_record_values AS activeSubsEnd, #__js_res_record_values AS activeSubsStart, #__js_res_record_values AS subs, #__js_res_record_values AS org ";
            $sql.= " WHERE productRecord.id = product.field_value AND product.field_id=114 AND product.record_id = activeSubsEnd.record_id AND activeSubsEnd.type_id=10 AND activeSubsEnd.field_id=72 AND DATEDIFF(activeSubsEnd.field_value, CURDATE()) >= 0 AND activeSubsEnd.record_id = activeSubsStart.record_id ";
            $sql.= " AND activeSubsStart.type_id=10 AND activeSubsStart.field_id=71 AND DATEDIFF(CURDATE(), activeSubsStart.field_value) >= 0 AND activeSubsStart.record_id = subs.record_id ";
            $sql.=" AND subs.type_id=10 AND subs.field_id=73 AND subs.field_value = org.field_value AND  org.record_id=".$user_profile_id." AND org.field_id=47 AND org.type_id=8";
            $db -> setQuery($sql);
            if ($result = $db -> loadObjectList()) {
                return $result;
            }
        return $rv;
    }

    /**
     * Get the ids of the products that are associated to an application to which the $app_id belongs.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $app_id The id of the application to which the products are associated.
     * @return array An array of ids of products if any, otherwise an empty array will be returned.
     */
    public static function getProductsInApplication($app_id) {
        if (isset($app_id)) {
            $db = JFactory::getDbo();
            $db -> setQuery("SELECT * FROM #__js_res_record WHERE id in (SELECT record_id FROM #__js_res_record_values WHERE type_id=1 AND field_id=62 AND field_value=" . $app_id . ") ORDER BY id ASC");
            if ($result = $db -> loadObjectList()) {
                return $result;
            }
        }
        return array();
    }

    /**
     * Get the ids of the products that are associated to an application to which the $app_id belongs.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $app_id The id of the application to which the products are associated.
     * @return array An array of ids of products if any, otherwise an empty array will be returned.
     */
    public static function getProductIdsInApplication($app_id) {
        $rv = array();
        if (isset($app_id)) {
            $db = JFactory::getDbo();
            $db -> setQuery("SELECT id FROM #__js_res_record WHERE id in (SELECT record_id FROM #__js_res_record_values WHERE type_id=1 AND field_id=62 AND field_value=" . $app_id . ") ORDER BY id ASC");
            if ($result = $db -> loadObjectList()) {
                foreach ($result as $product) {
                    array_push($rv, $product -> id);
                }
            }
        }
        return $rv;
    }

    /**
     * Get all the keys including active and inactive ones of the application to which the $app_id belongs.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $app_id The id of the application of which the keys are to be retrieved.
     * @return array An array of the keys if any, otherwise an empty array will be returned.
     */
    public static function getKeysOfApplication($app_id) {
        $rv = array();
        if (isset($app_id)) {
            $db = JFactory::getDbo();
            $db -> setQuery("SELECT record.id FROM #__js_res_record AS record WHERE record.id in (SELECT record_id FROM #__js_res_record_values WHERE type_id=11 AND field_id=86 AND field_value=" . $app_id . ") ORDER BY mtime DESC");
            if ($result = $db -> loadObjectList()) {
                foreach ($result as $key) {
                    array_push($rv, $key -> id);
                }
            }
        }
        return $rv;
    }

    /**
     * Get the currently active key of the application to which the $app_id belongs.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $app_id The id of the application of which the active key is to be retrieved.
     * @return array An array of the active key if any, otherwise an empty array will be returned.
     */
    public static function getActiveKeyOfApplication($app_id) {
        if (isset($app_id)) {
            $db = JFactory::getDbo();
            $db -> setQuery("SELECT record_id, field_value AS apiKey FROM #__js_res_record_values WHERE record_id IN (  SELECT record_id FROM #__js_res_record_values WHERE record_id IN (SELECT record_id FROM #__js_res_record_values WHERE type_id=11 AND field_id=86 AND field_value=" . $app_id . ") AND field_id=85 AND field_value='Active') AND field_id=82");
            if ($result = $db -> loadObjectList()) {
                return $result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * Get the currently active OAuth key of the application to which the $app_id belongs.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $app_id The id of the application of which the active key is to be retrieved.
     * @return array An array of the active key if any, otherwise an empty array will be returned.
     */
    public static function getActiveOAuthKeyOfApp($app_id) {
        if (isset($app_id)) {
            $db = JFactory::getDbo();
            $db -> setQuery("SELECT rv1.record_id, rv1.field_value AS apiKey, rv2.field_value AS secret FROM #__js_res_record_values AS rv1, #__js_res_record_values AS rv2 WHERE rv1.record_id IN (  SELECT record_id FROM #__js_res_record_values WHERE record_id IN (SELECT record_id FROM #__js_res_record_values WHERE type_id=11 AND field_id=86 AND field_value=" . $app_id . ") AND field_id=85 AND field_value='Active') AND rv1.field_id=82 AND rv2.record_id=rv1.record_id AND rv2.field_id=83");
            if ($result = $db -> loadObjectList()) {
                return $result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * Get the organization of the user profile to which the $user_profile_id belongs.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @return array An array of organization ids if any, otherwise an empty array will be returned.
     */
    public static function getUserOrganization() {
        $rv = array();
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $db -> setQuery("SELECT field_value FROM #__js_res_record_values WHERE record_id IN (SELECT record_id FROM #__js_res_record_values WHERE field_id=77 AND field_value=" . $user->id . ") and field_id=47");
        if ($result = $db -> loadObjectList()) {
            foreach ($result as $record) {
                array_push($rv, $record -> field_value);
            }
        }
        return $rv;
    }

    /**
     * Get all public environments of a product.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $product_id The id of the product of which all public environments are to be selected.
     * @return array The public environments of a product.
     */
    public static function getPublicEnvironments($product_id) {
        if(isset($product_id)) {
            $db = JFactory::getDbo();
            $sql = "SELECT r.id AS id, r.title AS title, rv.field_value AS base_path FROM #__js_res_record as r, #__js_res_record_values as rv WHERE r.id=rv.record_id AND r.published=1 AND rv.field_id=14 AND rv.record_id IN ";
            $sql .= "(SELECT record_id FROM #__js_res_record_values WHERE type_id=4 AND field_id=34 AND field_value=" . $product_id . ")";
            $db->setQuery($sql);
            if($result = $db->loadObjectList()) {
                return $result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * Get all private environments of a product.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $product_id The id of the product of which all private environments are to be selected.
     * @return array The private environments of a product.
     */
    public static function getPrivateEnvironments($product_id) {
        if(isset($product_id)) {
            $db = JFactory::getDbo();
            $sql = "SELECT r.id AS id, r.title AS title, rv.field_value AS base_path FROM #__js_res_record as r, #__js_res_record_values as rv WHERE r.id=rv.record_id AND r.published=1 AND rv.field_id=14 AND rv.record_id IN ";
            $sql .= "(SELECT record_id FROM #__js_res_record_values WHERE type_id=4 AND field_id=33 AND field_value=" . $product_id . ")";
            $db->setQuery($sql);
            if($result = $db->loadObjectList()) {
                return $result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * Get a user profile by both the id of the Joomla user that belongs to the user profile and the id of a product. If the product belongs to an organization to which the user profile belongs the user profile will be returned. Otherwise an empty array will be returned.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $product_id The id of a product. If this parameter is omitted an empty array will be returned.
     * @return array An array of user profiles if the user profile and the product belong to the same organization. Otherwise an empty array will be returned.
     */
    public static function getUserByProductOrganization($product_id) {
        if(isset($product_id)) {
            $db = JFactory::getDbo();
            $user = JFactory::getUser();
            $sql = "SELECT * FROM #__js_res_record_values WHERE type_id=8 AND field_id=77 AND field_value=" . $user->id . " AND record_id IN ";
            $sql .= "(SELECT record_id FROM #__js_res_record_values WHERE type_id=8 AND field_id=47 AND field_value IN ";
            $sql .= "(SELECT field_value FROM  #__js_res_record_values WHERE field_id=42 AND record_id=" . $product_id . " AND type_id=1))";
            $db->setQuery($sql);
            if($result = $db->loadObjectList()) {
                return $result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
    //Get the products which are referencing current API.
    public static function getProductsByAPIId($api_id) {
      if(isset($api_id)) {
        $db = JFactory::getDbo();
        $query = "SELECT field_value FROM #__js_res_record_values WHERE field_id=6 AND type_id=2 AND record_id=" . $api_id;
        $query = "SELECT id FROM #__js_res_record WHERE published!=2 AND id IN (" . $query . ")";
        $db->setQuery($query);
        if($result = $db->loadObjectList()) {
          return $result;
        } else {
          return array();
        }
      } else {
        return array();
      }
    }
    //Get the environments which are referencing current gateway.
    public static function getEnvironmentsByGatewayId($gateway_id) {
      if(isset($gateway_id)) {
        $db = JFactory::getDbo();
        $query = "SELECT id FROM #__js_res_record WHERE published!=2 AND id in (";
        $query .= "SELECT field_value FROM #__js_res_record_values WHERE field_id=16 AND type_id=3 AND record_id=" . $gateway_id . ")";
        $db->setQuery($query);
        if($result = $db->loadObjectList()) {
          return $result;
        } else {
          return array();
        }
      } else {
        return array();
      }
    }

    //Get the plans which are referecing current plan
    public static function getPlanByProductId($product_id) {
      if(isset($product_id)) {
        $db = JFactory::getDbo();
        $query = "SELECT id FROM #__js_res_record WHERE published!=2 AND id in (";
        $query .= "SELECT record_id FROM #__js_res_record_values WHERE field_id=53 AND field_value=" . $product_id . ")";
        $db->setQuery($query);
        if($result = $db->loadObjectList()) {
          return $result;
        } else {
          return array();
        }
      } else {
        return array();
      }
    }
    //Get the subscriptions which are referencing current plan.
    public static function getSubscriptionByPlanId($plan_id) {
      if(isset($plan_id)) {
        $db = JFactory::getDbo();
        $query = "SELECT id FROM #__js_res_record WHERE published!=2 AND id in (";
        $query .= "SELECT record_id FROM #__js_res_record_values WHERE field_id=69 AND field_value=" . $plan_id . ")";
        $db->setQuery($query);
        if($result = $db->loadObjectList()) {
          return $result;
        } else {
          return array();
        }
      } else {
        return array();
      }
    }

        //Get the subscriptions which are attached to an APP
    public static function getApplicationSubscriptions($app_id) {
     
        $db = JFactory::getDbo();
        $query = 'SELECT record_id FROM #__js_res_record_values WHERE field_label= "Owner application" and field_value =' . $app_id;
        $query = 'SELECT field_value as plan_ids FROM #__js_res_record_values WHERE field_label= "Plan" and record_id IN (' . $query . ')';
        $db->setQuery($query);
        if($result = $db->loadObjectList()) {
          return $result;
        } else {
          return array();
        }
      
    }

    //Get the applications which are referencing current subscription.
    public static function getApplicationBySubscriptionId($subscription_id) {
      if(isset($subscription_id)) {
        $db = JFactory::getDbo();
        $query = "SELECT id FROM #__js_res_record WHERE published!=2 AND id in (";
        $query .= "SELECT field_value FROM #__js_res_record_values WHERE field_id=116 AND record_id=" . $subscription_id . ")";
        $db->setQuery($query);
        if($result = $db->loadObjectList()) {
          return $result;
        } else {
          return array();
        }
      } else {
        return array();
      }
    }
    //Get the gateways which are referenced by current environment.
    public static function getGatewaysByEnvironmentId($environment_id) {
      if(isset($environment_id)) {
        $db = JFactory::getDbo();
        $query = "SELECT id as record_id,title FROM #__js_res_record WHERE published!=2 and id in (SELECT record_id FROM #__js_res_record_values WHERE type_id=3 AND field_value=" . $environment_id . ")";
        $db->setQuery($query);
        if($result = $db->loadObjectList()) {
          return $result;
        } else {
          return array();
        }
      } else {
        return array();
      }
    }
    //Get the published/unpublished APIs which are referencing current environment.
    public static function getAPIsByEnvironmentId($environment_id) {
      if(isset($environment_id)) {
        $db = JFactory::getDbo();
        $query = "SELECT id FROM #__js_res_record WHERE published!=2 AND id in (";
        $query .= "SELECT field_value FROM #__js_res_record_values WHERE field_id=25 AND record_id=" . $environment_id . ")";
        $db->setQuery($query);
        if($result = $db->loadObjectList()) {
          return $result;
        } else {
          return array();
        }
      } else {
        return array();
      }
    }
    public static function isReferencedByDownstreamSubs($id, $field_type) {
      if (isset($id)) {
        $query = "SELECT id FROM #__js_res_record WHERE published!=2 AND id IN
                     (SELECT record_id FROM #__js_res_record_values WHERE type_id=10 AND IF(field_id=72, DATEDIFF(field_value, CURDATE()) >= 0, 1=1) AND record_id IN
                     (SELECT record_id FROM #__js_res_record_values WHERE type_id=10 AND field_id=71 AND DATEDIFF(CURDATE(), field_value) >= 0 AND record_id IN
                     (SELECT record_id FROM #__js_res_record_values WHERE field_id=78 AND type_id=10 AND field_value='Active' AND record_id IN ";
        switch($field_type) {
          case "gateway":
            $query .= "(SELECT record_id FROM `#__js_res_record_values` WHERE field_id=69 AND type_id=10 AND field_value IN
                     (SELECT record_id FROM `#__js_res_record_values` WHERE field_id=53 AND type_id=7 AND field_value IN
                     (SELECT field_value FROM `#__js_res_record_values` WHERE field_id=6 AND type_id=2 AND record_id IN
                     (SELECT field_value FROM `#__js_res_record_values` WHERE field_id=25 AND type_id=4 AND record_id IN
                     (SELECT field_value FROM `#__js_res_record_values` WHERE field_id=16 AND type_id=3 AND record_id=" . $id . "))))))))";
            break;
          case "environment":
            $query .= "(SELECT record_id FROM `#__js_res_record_values` WHERE field_id=69 AND type_id=10 AND field_value IN
                     (SELECT record_id FROM `#__js_res_record_values` WHERE field_id=53 AND type_id=7 AND field_value IN
                     (SELECT field_value FROM `#__js_res_record_values` WHERE field_id=6 AND type_id=2 AND record_id IN
                     (SELECT field_value FROM `#__js_res_record_values` WHERE field_id=25 AND type_id=4 AND record_id=" . $id . ")))))))";
            break;
          case "api":
            $query .= "(SELECT record_id FROM `#__js_res_record_values` WHERE field_id=69 AND type_id=10 AND field_value IN
                     (SELECT record_id FROM `#__js_res_record_values` WHERE field_id=53 AND type_id=7 AND field_value IN
                     (SELECT field_value FROM `#__js_res_record_values` WHERE field_id=6 AND type_id=2 AND record_id=" . $id . "))))))";
            break;
          case "product":
            $query .= "(SELECT record_id FROM `#__js_res_record_values` WHERE field_id=69 AND type_id=10 AND field_value IN
                     (SELECT record_id FROM `#__js_res_record_values` WHERE field_id=53 AND type_id=7 AND field_value=" . $id . ")))))";
            break;
          case "plan":
            $query .= "(SELECT record_id FROM `#__js_res_record_values` WHERE field_id=69 AND type_id=10 AND field_value=" . $id . "))))";
            break;
          default:
            $query .= "(0))))";
            break;
        }
        $db = JFactory::getDbo();
        $db->setQuery($query);
        if($result = $db->loadObjectList()) {
            return count($result) > 0;
        } else {
            return false;
        }
      } else {
        return false;
      }
    }
    /**
     * Get the description, contact's name and the owner organization's name of an application.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $app_id The id of the application of which the information is to be retrieved.
     * @return array An array of the information of the application.
     */
    public static function getApplicationInformation($app_id) {
        if(isset($app_id)) {
            $db = JFactory::getDbo();
            $sql = "SELECT DISTINCT rv1.record_id AS app_id, rv1.field_value AS description, r1.title AS contact, r2.title AS org FROM #__js_res_record AS r1, #__js_res_record AS r2, #__js_res_record_values AS rv1, #__js_res_record_values AS rv2, #__js_res_record_values AS rv3, #__js_res_record_values AS rv4 WHERE rv1.record_id=rv2.record_id AND rv2.record_id=rv3.record_id AND rv1.type_id=9 AND rv2.type_id=9 AND rv3.type_id=9 AND rv1.field_id=57 AND rv2.field_id=58 AND rv3.field_id=60 AND rv2.field_value=r1.id AND rv3.field_value=r2.id AND rv1.record_id=" . $app_id;
            $db->setQuery($sql);
            if($result = $db->loadObjectList()) {
                return $result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * Get the information of an application about whether the OAuth is used.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $app_id The id of the application of which the information about whether OAuth is used is to be retrieved.
     * @return array An array of the information of whether the application is using OAuth.
     */
    public static function getAppOAuth($app_id) {
        if(isset($app_id)) {
            $db = JFactory::getDbo();
            $sql = "SELECT field_value AS use_oauth FROM #__js_res_record_values WHERE field_id=64 AND type_id=9 AND record_id=" . $app_id;
            $db->setQuery($sql);
            if($result = $db->loadObjectList()) {
                return $result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * Get all subscriptions of a product.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $prod_id The id of the product.
     * @return array An array of the subscriptions of the product whose id equals $prod_id.
     */
    public static function getSubscriptions4Prod($prod_id) {
        if(isset($prod_id)) {
            $db = JFactory::getDbo();
            $sql = "SELECT * FROM #__js_res_record AS r, #__js_res_record_values AS rv WHERE rv.field_id=114 AND rv.field_value=" . $prod_id . " AND rv.type_id=10 AND r.id=rv.record_id AND r.published=1";
            $db->setQuery($sql);
            if($result = $db->loadObjectList()) {
                return $result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * Archive a record by setting the "published" field to 2.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int rec_id The id of the record to be archived.
     * @return boolean True if the operation is successful. False otherwise.
     */
    public static function archiveRecord($rec_id,$type_id) {
        if(isset($rec_id)) {
            switch ($type_id) {
                case 1:
                    $field_id = 53;
                    break;
                case 2:
                    $field_id = 30;
                    break;
                case 4:
                    $field_id = 16;
                    break;
                case 5:
                    $field_id = 47;
                    break;
                default:
                    $field_id = 0;
                    break;
            }

            $db = JFactory::getDbo();

            if($type_id == "11" || $type_id == "12" || $type_id == "8"){
                return TibcoTibco::deleteRecord($rec_id,$type_id);
            }else{
                $sql = "UPDATE #__js_res_record SET published=2,title= concat(title,'_',UNIX_TIMESTAMP(now())) WHERE id=" . $rec_id ." or id in (select record_id from `#__js_res_record_values` where field_value=" . $rec_id  . " and field_id=". $field_id .")";
                $db->setQuery($sql);

                if(!$db->execute()) {
                    return false;
                } else {
                    return true;
                }
            }

        } else {
            return false;
        }
    }

    /**
     * Get the emails of admins
     *
     * @Author Jacky
     * @Created 2013-10-15
     *
     * @return array  emails of admins
     */
    public static function getEmailsOfJoomlaAdmins()
    {
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);

        $query->select($db->quoteName("user_id"))->from("#__user_usergroup_map")->where("group_id IN (7,8)");
        $db->setQuery($query);
        $user_ids = $db->loadColumn();
        $emails_admins = array();

        if(count($user_ids))
        {
          $user_ids = JArrayHelper::arrayUnique($user_ids);
          foreach ($user_ids as $user_id) {
            $emails_admins[] = JFactory::getUser($user_id)->email;
          }
        }
        return $emails_admins;
    }

    /**
     * Get the emails of organization's admins
     *
     * @Author Jacky
     * @Created 2013-10-15
     * @param  string $org_id which organization we want to get its administrator's email
     * @return Array  if no result is found, return empty array, otherwise, return a array containing emails of amdins
     */
    public static function getEmailsOfOrganizationAdmin($org_id = '')
    {
        $result = array();


        if(!$org_id)
        {
          $org_id     = DeveloperPortalApi::getUserOrganization();

          if(empty($org_id)){
            return $result;
          }else{
            $org_id   = $org_id[0];
          }
        }

        $user_ids = self::getIdsOfOrganizationAdmin($org_id);

        if(count($user_ids))
        {
          $user_ids = JArrayHelper::arrayUnique($user_ids);
          foreach ($user_ids as $user_id) {
            $result[] = JFactory::getUser($user_id)->email;
          }
        }

        return $result;
    }

    /**
     * Get the ids of organization's admins
     *
     * @Author Jacky
     * @Created 2013-10-15
     * @param  string $org_id which organization we want to get its administrator's email
     * @return Array  if no result is found, return empty array, otherwise, return a array containing ids of amdins
     */

    public static function getIdsOfOrganizationAdmin($org_id = '')
    {
      $db     = JFactory::getDbo();
      $query  = "SELECT user_id from #__user_usergroup_map where group_id in (SELECT id from #__usergroups where title ='Organization ".$org_id." Manager')";
      $db->setQuery($query);
      return $db->loadColumn();
    }
    /**
     * Get the emails of organization's contacts
     *
     * @Author Jacky
     * @Created 2013-10-15
     * @param  string $org_id which organization we want to get its contacts's email
     * @return Array  if no result is found, return empty array, otherwise, return a array containing emails of amdins
     */
    public static function getEmailsOfOrganizationContact($org_id = '')
    {
        $result = array();

        if(!$org_id)
        {
          $org_id     = DeveloperPortalApi::getUserOrganization();

          if(empty($org_id)){
            return $result;
          }else{
            $org_id   = $org_id[0];
          }
        }

        $db     = JFactory::getDbo();
        $query  = "SELECT user_id from #__user_usergroup_map where group_id in (SELECT id from #__usergroups where title ='Organization ".$org_id." Contact')";
        $db->setQuery($query);
        $user_ids = $db->loadColumn();
        if(count($user_ids))
        {
          $user_ids = JArrayHelper::arrayUnique($user_ids);
          foreach ($user_ids as $user_id) {
            $result[] = JFactory::getUser($user_id)->email;
          }
        }
        return $result;
    }
    /**
     * Get the id of the user profile object of the current user.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @return integer The id of the profile of the current user or 0 if nothing found.
     */
    public static function getUserProfileId($user_id="") {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $user_id = $user_id ? $user_id : $user->id;
        $query = $db->getQuery(true);
        $query->select($db->quoteName("record_id"))->from("#__js_res_record_values")->where("field_id=77 AND field_value=" . $user_id);
        $db->setQuery($query);
        $result = $db->loadColumn();
        return !empty($result) ? $result[0] : 0;
    }


    /**
     * Get the id of the user object of the related user profile.
     *
     * @author Jacky <jihgao@tibco-support.com>
     * @return integer The id of the profile of the current user or 0 if nothing found.
     */
    public static function getUserIdByProfileId($profile_id="") {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $user_id = $user_id ? $user_id : $user->id;
        $query = $db->getQuery(true);
        $query->select($db->quoteName("field_value"))->from("#__js_res_record_values")->where("field_id=77 AND record_id=" . $profile_id);
        $db->setQuery($query);
        $result = $db->loadColumn();
        return is_array($result) ? $result[0] : 0;
    }

    public static function createUserGroups($org_id) {
        $mid=DeveloperPortalApi::_createUserGroup($org_id,12);
        if(!empty($mid)) {
            $cid=DeveloperPortalApi::_createUserGroup($org_id, $mid[0], DeveloperPortalApi::USER_TYPE_CONTACT);
            if(!empty($cid)) {
                $gid = DeveloperPortalApi::_createUserGroup($org_id, $cid[0], DeveloperPortalApi::USER_TYPE_MANAGER);
                if(!empty($gid)) {
                    DeveloperPortalApi::_rebuildUserGroup();
                    TibcoTibco::linkGroupWithViewLevel(39, $gid);
                }
            }
        }
    }

    /**
     * AUtomactically creating user group for organization
     * @param int $org_id
     * @param int $parent_id
     * @param string $gtype
     * @return Ambigous <mixed, NULL, multitype:mixed >
     */
    private static function _createUserGroup($org_id=0, $parent_id=12, $gtype = DeveloperPortalApi::USER_TYPE_MEMBER)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->insert('#__usergroups');
        $query->columns(array($db->quoteName('parent_id'), $db->quoteName('title')));
        $query->values($parent_id . ', '.'"Organization '.$org_id.' '.$gtype.'"');
        $db->setQuery($query);

        if($db->execute()){
            $ids = DeveloperPortalApi::_getNewUserGroupId($org_id, $parent_id);
            DeveloperPortalApi::_createUserACL($ids,$org_id, $gtype);
            return $ids;
        };

    }

    private static function _createUserACL($rule=array(),$org_id=0, $gtype = DeveloperPortalApi::USER_TYPE_MEMBER)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->insert('#__viewlevels');
        $query->columns(array($db->quoteName('title'),$db->quoteName('rules')));
        $query->values('"Organization '.$org_id.' '.$gtype.'", "['.($rule[0] ? $rule[0] : "").']"');
        $db->setQuery($query);

        $db->execute();
    }

    private static function _getNewUserGroupId($org_id=0,$parent_id=12)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName('id'));
        $query->from('#__usergroups AS a');
        $query->where('a.parent_id = ' . (int)$parent_id);
        $query->where('a.title LIKE ' . '"Organization '.$org_id.'%"');

        $db->setQuery($query);
        if($db->execute())
        {
            return $db->loadColumn();;
        };
    }

    private static function _rebuildUserGroup($parent_id = 0, $left = 0)
    {
        // Get the database object
        $db = JFactory::getDbo();

        // Get all children of this node
        $db->setQuery('SELECT `id` FROM ' . $db->quoteName('#__usergroups') . ' WHERE parent_id=' . (int) $parent_id . ' ORDER BY parent_id, title');
        $children = $db->loadColumn();

        // The right value of this node is the left value + 1
        $right = $left + 1;

        // Execute this function recursively over all children
        for ($i = 0, $n = count($children); $i < $n; $i++)
        {
          // $right is the current right value, which is incremented on recursion return
          $right = DeveloperPortalApi::_rebuildUserGroup($children[$i], $right);

          // If there is an update failure, return false to break out of the recursion
          if ($right === false)
          {
            return false;
          }
        }

        // We've got the left value, and now that we've processed
        // the children of this node we also know the right value
        $db->setQuery('UPDATE ' . $db->quoteName('#__usergroups') . ' SET lft=' . (int) $left . ', rgt=' . (int) $right . ' WHERE id=' . (int) $parent_id);

        // If there is an update failure, return false to break out of the recursion
        if (!$db->execute())
        {
          return false;
        }

        // Return the right value of this node + 1
        return $right + 1;
    }

    function wantToSubscribePlan() {
        $user = JFactory::getUser();
        $user_id = $user -> id;
        $plan_id = $_GET["plan_id"];
        if ($user_id && $plan_id) {
            $db = JFactory::getDbo();
            $db -> setQuery('DELETE FROM #__user_profiles ' . ' WHERE user_id=' . $user_id . ' AND profile_key = "plan.subscribe" LIMIT 1');
            $db -> execute();
            $map = new JObject;
            $map -> user_id = $user_id;
            $map -> profile_key = "plan.subscribe";
            $map -> profile_value = $plan_id;

            $db -> insertObject('#__user_profiles', $map);
            if ($db -> getAffectedRows()) {
                $arr = array("success" => "1");
                $user = JUserHelper::getProfile($this -> user_id);
                $db -> setQuery('SELECT * FROM #__email_templates WHERE alias="plan.subscribe" AND published = 1 LIMIT 1');
                $results = $db -> loadObject();
                if ($results -> subject && $results -> content) {
                    $config = &JFactory::getConfig();
                    $title = $results -> subject;
                    $content = str_replace("{USER}", $user -> profiletap["firstName"] . " " . $user -> profiletap["lastName"], $results -> content);
                    $content = str_replace("{EMAIL}", $this -> user_email, $content);
                    $content = str_replace("{PHONE}", $user -> profiletap["phoneNumber"], $content);
                    $content = str_replace("{COMPANY}", $user -> profiletap["company"], $content);
                    send_email($config -> get('mailfrom'), $title, $content, $results -> isHTML);
                }
                echo $this -> pushSuccessMessage($arr);
            } else {
                echo $this -> pushErrorMessage(3);
            }
        } else {
            echo $this -> pushErrorMessage(4);
        }
    }

    public function resendActiveEmail($user_id, $active_url) {
      $user = JFactory::getUser($user_id);
      $user_id = $user->id;
      if ($user_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select("*")->from("#__email_templates")
              ->where($db->quoteName('alias') . "=". $db->quote('resend_active_email'))
              ->where($db->quoteName('published') . "=1  LIMIT 1");
        
        $db->setQuery($query);

        $results = $db->loadObject();
        if ($results->subject && $results->content) {
          $config = &JFactory::getConfig();
          $title   = $results->subject;
          $content = str_replace("{USER}", $user->name, $results->content);
          $content = str_replace("{ACTIVE_URL}", $active_url, $content);
          $content = str_replace("{USER_NAME}", $user->username, $content);
          $content = str_replace("Password : {PASSWORD}", JText::_('ACTIVE_EMAIL_PROMPT_MESSAGE'), $content);
          return DeveloperPortalApi::send_email($user->email, $title, $content, $results->isHTML);
        }
        return false;
      }
      return false;
    }

    //-------------- private function
    function pushSuccessMessage($data = array()) {
        $arr = array();
        $arr['data'] = $data;
        $arr['result'] = $this -> _getResultMessage(1);
        return json_encode($arr);
    }

    function pushErrorMessage($code) {
        $arr = array("result" => $this -> _getResultMessage($code));
        return json_encode($arr);
    }

    function pushCustomMessage($error_code, $error_msg) {
        $arr = array("result" => array("code" => (String)$error_code, "message" => $error_msg, ));
        return json_encode($arr);
    }

    function _getResultMessage($code) {
        $this -> arr_message = array('0' => "Fail!", '1' => "Successful.", '2' => "Parameter error.", '3' => "Database error.", '4' => "No permission, please login first.", '5' => "Data does not exist.", '6' => "No permission, paid users only.");
        $arr = array("code" => (String)$code, "message" => $this -> arr_message[$code], );
        return $arr;
    }

    public static function getUserOrganizationId($user_id) {
        if (!$user_id)
            return '';

        $db = JFactory::getDbo();
        $db -> setQuery('SELECT `title` FROM #__usergroups WHERE `title` LIKE "Organization %" AND `id` IN (SELECT `group_id` FROM #__user_usergroup_map WHERE `user_id`=' . $user_id . ')');
        $result = $db -> loadColumn();

        if (empty($result))
        {  
          return false;
        } 
        preg_match('/^Organization\s+([0-9]+)/i', $result[0], $result);

        if (!$result)
        {   
          return false;
        }

        return $result[1];
    }
    
    public static function fromSameOrg($current_user,$target_user){
      if(empty($current_user)||empty($target_user)){
        return false;
      }
      $org_current_user = DeveloperPortalApi::getUserOrganizationId($current_user);
      $org_target_user =  DeveloperPortalApi::getUserOrganizationId($target_user);
      if($org_current_user!=$org_target_user){
        return false;
      }
      return true;
    }

    function getApiFieldsForProduct($api_id) {
        $item = $this -> getRecordById($api_id);
        if (!$item) {
            return '';
        }
        $result = array();
        $result['title'] = $item -> title;
        $result['description'] = $item -> fields_by_id[5];
        $result['mtime'] = $item -> mtime;
        $result['author'] = JFactory::getUser($item -> user_id) -> username;
        return $result;
    }

    function send_email($mail_list, $title, $content, $isHTML=false, $bcc=false){
      $mailer =& JFactory::getMailer();
      $config =& JFactory::getConfig();
      $sender = array(
          $config->get( 'mailfrom' ),
          $config->get( 'fromname' )
      );

      if ($isHTML) {
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
      }

      $mailer->setSender($sender);
      $mail_list = is_array($mail_list) ? $mail_list : array($mail_list);
      $mailer->addRecipient($mail_list);
      $body   = $content;
      $mailer->setSubject($title);
      $mailer->setBody($body);
      if ($bcc && $config->get( 'mailbcc' )) {
        $mailer->addBCC($config->get( 'mailbcc' ));
      }
      $send =& $mailer->Send();
      return $send;
    }
    public static function getHostUrl(){
      $_url = parse_url(JURI::root());
      return $_url["scheme"]."://".$_url["host"].($_url["port"] ? ":".$_url["port"] : "");
    }


    /**
     * Get the right records which we need
     * @param array element $item
     * @return boolean
     */
    public static function getOrganizationRecords($item){
        return (isset($item->type_id) && $item->type_id==5);
    }

    /**
     * Update the message record for the current user.
     *
     * @param  boolean $ajax    if $ajax is true,it stand for the front-end hava already got
     *                          the message from protal engine after sending notification to portal engine.
     *
     * @param  array   $options parameters for message log, including entity_type=>content_type, entity_id=>record_id
     * @return boolean if the action is successfully action, return true.
     */
    public static function updateMessageForUser($ajax = false,$options = array()){
        $user       = JFactory::getUser();
        $db         = JFactory::getDbo();
        $query      = $db->getQuery(true);
        $condition  = $db->quoteName('uid') . '=' . $user->id .' AND '.$db->quoteName('is_show') . '=' . '1';
        $query->update($db->quoteName('asg_logs'))->set($db->quoteName('is_show') . "=0");

        if($ajax){

            if (func_num_args() > 1 && !is_array($options))
            {
                $args = func_get_args();
                $options = array();
                $options['entity_type'] = (isset($args[1])) ? $args[1] : null;
                $options['entity_id'] = (isset($args[2])) ? $args[2] : null;
            }


            if ($options['entity_type'])
            {
                $condition .= ' AND '.$db->quoteName('entity_type') . '=' . $options['entity_type'];
            }


            if ($options['entity_id'])
            {
                $condition .= ' AND '.$db->quoteName('entity_id') . '=' . $options['entity_id'];
            }
        }


        $query->where($condition);
        $db->setQuery($query);


        if($db->query())
        {
            return true;
        }

        return false;

    }
    /**
     * Get the message for the current user, once the message is shown for user, hide it from database.
     * @return Joomla Message
     */
    public static function protalEngineMessage()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $user = JFactory::getUser();

        if(!$user->id){return false;}

        $query->select('*')->from($db->quoteName('asg_logs'))->where($db->quoteName('uid') . '=' . $user->id .' AND '.$db->quoteName('is_show') . '=' . '1');

        $db->setQuery($query);
        $results = $db->loadObjectList();
        if($results)
        {
            foreach ($results as $result) {
                if(strtolower($result->event_status) === 'error'){
                   JLog::add(sprintf(JText::_('PORTAL_ENGINE_RUN_ERROR'), $result->entity_type,$result->event,$result->entity_id), JLog::ERROR, 'jerror');
                }
            }
            DeveloperPortalApi::updateMessageForUser(false);
        }
    }


    /**
     * Get the Object number of how many object have been attached to the organization
     * @param  string $org_id the organization's id
     * @return integer the numer of how many object have been attached to the organization
     */
    function getObjectAttachedToOrganization($org_id = '')
    {
        if(!$org_id)
        {
            return 0;
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("id")->from("#__js_res_record_values")->where('(field_id=48 and record_id='.$org_id.') or (field_id in (40,42,60,47,73) and field_value='.$org_id.')');
        $db->setQuery($query);
        $attached_objects = $db->loadColumn();

        return count($attached_objects);
    }

    /**
     * Get the count of objects attached to one particular content type
     */

    public function getObejectCountsAttachedToCotentType($type = 0, $id = 0)
    {
    	$count = 0;
    	if($type && $id)
    	{
    		switch ((int) $type) {
    			case 1:
    				$condition = '(field_id=48 and record_id='.$id.') or (field_id in (114,53) and field_value='.$id.')';
    				break;
    			case 5:
    				$condition = '(v.field_id IN (40,42,60,47,73) AND v.field_value='.$id.') AND r.published = 1';
    				break;
    			default:
    				$condition = '';
    				break;
    		}

    		if ($condition)
    		{
    			$db    = JFactory::getDbo();
    			$query = $db->getQuery(true);

    			$query->select("v.id")->from("#__js_res_record_values AS v LEFT JOIN #__js_res_record AS r ON v.record_id = r.id");
    			$query->where($condition);
    			$db->setQuery($query);
    			$count= count($db->loadColumn());
    		}
    	}
    	return $count;
    }


    public static function getOrganizationIdByName ($org_name = ''){
      $result = 0;
      if($org_name)
      {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select("id")->from("#__usergroups")->where('title="'.$org_name.'"');

        $db->setQuery($query);
        $result = $db->loadColumn();
        if(count($result))
        {
          $result = $result[0];
        }else{
            $result = 0;
        }
      }
      return $result;
    }


    public static function getUserOrganizationGroupIdByOrganizationsGroupIds($org_groups = array())
    {
        $user = JFactory::getUser();
        $result = 0;
        if(!empty($org_groups))
        {
            foreach ($org_groups as $org_group_id) {
               if(in_array($org_group_id, $user->getAuthorisedGroups()))
               {
                $result = $org_group_id;
                break;
               }
            }
        }

        return $result;
    }

    public static function getUserAccessGroupId()
    {
        $org_groups = self::getUserOrganizationGroupsIds();
        $user = JFactory::getUser();
        $user_group = self::getUserOrganizationGroupIdByOrganizationsGroupIds($org_groups);
        $acccess_name = self::getUserOrganizationAccessLevelNameById($user_group);
        $acccess_id = self::getUserOrganizationAccessIdByName($acccess_name);

        return $acccess_id;
    }

    /**
     * Get organization groups which belongs to the user's organization
     * @return [type] [description]
     */
    public static function getUserOrganizationGroupsIds()
    {
        $orgs = self::getUserOrganization();
        $result = array();
        if(!empty($orgs))
        {
            foreach ($orgs as $org){
                if($member_group = self::getOrganizationIdByName('Organization '.$org.' '.DeveloperPortalApi::USER_TYPE_MEMBER)){
                    $result[] = $member_group;
                }
                if($contact_group = self::getOrganizationIdByName('Organization '.$org.' '.DeveloperPortalApi::USER_TYPE_CONTACT)){
                    $result[] = $contact_group;
                }
                if($manage_group = self::getOrganizationIdByName('Organization '.$org.' '.DeveloperPortalApi::USER_TYPE_MANAGER)){
                    $result[] = $manage_group;
                }
            }
        }
        return $result;
    }

    /**
     * Get the organization's id which the application belong to.
     * @param  integer $application_id  application id
     * @return [integer]                the organization's id
     */
    public static function getOranizationIdOfApplication($application_id = 0)
    {
       $org_id = 0;
       if($application_id)
       {
            $application =  ItemsStore::getRecord($application_id);
            // pr($application);
            if($application->fields)
            {
                 $fields = json_decode($application->fields);

                 if($organizations = $fields->{'60'})
                 {
                     $org_id = $organizations;
                 }
            }
       }

       return $org_id;
    }

    public static function getOranizationIdOfSubscription($subscription_id = 0)
    {
       $org_id = 0;
       if($subscription_id)
       {
            $subscription =  ItemsStore::getRecord($subscription_id);
            // pr($application);
            if($subscription->fields)
            {
                 $fields = json_decode($subscription->fields);

                 if($organizations = $fields->{'73'})
                 {
                     $org_id = $organizations;
                 }
            }
       }

       return $org_id;
    }
    /**
     * Get user's organization access level name by user group's id
     * @param  integer $org_group_id  Id of the user group
     * @return [string]     return name of the
     */
    public static function getUserOrganizationAccessLevelNameById($org_group_id = 0)
    {
       $result = '';
       if($org_group_id)
       {
         $db = JFactory::getDbo();
         $query = $db->getQuery(true);

         $query->select("title")->from("#__usergroups")->where('id="'.$org_group_id.'"');

         $db->setQuery($query);
         $result = $db->loadColumn();
         if(count($result))
         {
           $result = $result[0];
         }else{
           $result = '';
         }
       }
       return $result;
    }

    public static function getUserOrganizationAccessIdByName($access_name='')
    {
       $result = 0;
       if($access_name)
       {
         $db = JFactory::getDbo();
         $query = $db->getQuery(true);

         $query->select("id")->from("#__viewlevels")->where('title="'.$access_name.'"');

         $db->setQuery($query);
         $result = $db->loadColumn();
         if(count($result))
         {
           $result = $result[0];
         }else{
           $result = 0;
         }
       }
       return $result;
    }

    public static function getDocType($fileName) {
        $doctype = 'unknown';
        if(isset($fileName)) {
            if(strripos($fileName,".pdf") === (strlen($fileName) - 4)) {
                $doctype = "document";
            }
            elseif(strripos($fileName,".doc") === (strlen($fileName) - 4)) {
                $doctype = "document";
            }
            elseif(strripos($fileName,".txt") === (strlen($fileName) - 4)) {
                $doctype = "document";
            }
            elseif(strripos($fileName,".rtf") === (strlen($fileName) - 4)) {
                $doctype = "document";
            }
            elseif(strripos($fileName,".docx") === (strlen($fileName) - 5)) {
                $doctype = "document";
            }
            elseif(strripos($fileName,".jpg") === (strlen($fileName) - 4)) {
                $doctype = "image";
            }
            elseif(strripos($fileName,".jpeg") === (strlen($fileName) - 5)) {
                $doctype = "image";
            }
            elseif(strripos($fileName,".gif") === (strlen($fileName) - 4)) {
                $doctype = "image";
            }
            elseif(strripos($fileName,".zip") === (strlen($fileName) - 4)) {
                $doctype = "compressed";
            }
            elseif(strripos($fileName,".rar") === (strlen($fileName) - 4)) {
                $doctype = "compressed";
            }
            elseif(strripos($fileName,".7z") === (strlen($fileName) - 3)) {
                $doctype = "compressed";
            }
            elseif(strripos($fileName,".tar") === (strlen($fileName) - 4)) {
                $doctype = "compressed";
            }
            elseif(strripos($fileName,".gz") === (strlen($fileName) - 3)) {
                $doctype = "compressed";
            }
        }
        return $doctype;
    }


    /*
     * Get the old operations list of  api and this function is developed for send event of operation
     * $api_id is id of api
     */

    public static function getOperationsOfApiByApiId($api_id=0)
    {
        $result = array();
        if($api_id){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select("a.record_id")->from("#__js_res_record_values AS a,#__js_res_record AS b")->where('(a.record_id=b.id and b.published=1 and a.field_id=30 and a.field_value='.$api_id.')');
            $db->setQuery($query);

            $result = $db->loadColumn();
        }

        return $result;

    }
    public static function getApis($apis) {

        $apisArr = array();
        $db = JFactory::getDbo();
        foreach( $apis as $idx => $value ) {
          $sql = 'SELECT title FROM `#__js_res_record` WHERE `published` =1 AND `id` = '.$value;
          $db->setQuery($sql);
          $idsArr = $db->loadObject();
            $apisArr[$idx]['title'] = $idsArr->title;
           $apisArr[$idx]['operations'] = self::getOperationsForApi($value);
        }
        return $apisArr;
    }

    private static function getOperationsForApi($api_id=0)
    {
        $returnArr = array();
        if($ids = self::getOperationsOfApiByApiId($api_id)){
          $ids = json_encode($ids);
          $ids = str_replace( '[', '(',$ids);
          $ids = str_replace( ']', ')', $ids );
          $ids = str_replace( '"', '', $ids );
          $ids = str_replace( '\'', '', $ids );
          $db = JFactory::getDbo();
          $sql = 'SELECT id, title ,fieldsdata FROM `#__js_res_record` WHERE `id` in '.$ids;
          $db->setQuery($sql);
          $idsArr = $db->loadObjectList();
          foreach( $idsArr as $idx => $val ) {
              $returnArr[$idx]['title'] = $val->title;
              $sql2 = 'SELECT field_label, field_value FROM `#__js_res_record_values` WHERE `record_id` = '. $val->id;
              $db->setQuery($sql2);
              $ops = $db->loadObjectList();
              foreach ( $ops as $value ) {
                  $returnArr[$idx][$value->field_label] = $value->field_value;
              }
          }
        }
        return $returnArr;

    }
    
    /**
     * Get all applications for the current user's Organization
     *
     * @return array An array of application ids if any, otherwise an empty array will be returned.
     */
    public static function getApplicationsForUserOrganization() {
    	$rv = array();
    	$db = JFactory::getDbo();
    	$user = JFactory::getUser();

    	$access = self::getUserOrganizationAccessLevel();

    	$sql = "SELECT * FROM #__js_res_record
    				WHERE published=1
    				AND archive=0
    				AND hidden=0
    				AND section_id=5
    				AND access=" .$access . " GROUP BY id ORDER BY ctime DESC";

    	$db -> setQuery($sql);
    	if ($result = $db-> loadObjectList()) {
    		foreach($result as $record) {
    			array_push($rv, $record->id);
    		}
    	}
    	return $rv;
    }

    public static function getUserOrganizationAccessLevel()
    {
    	$rv = array();
    	$db = JFactory::getDbo();
    	$user = JFactory::getUser();

    	$sql = "SELECT title
			FROM #__usergroups
			WHERE id
			IN (
    			SELECT group_id
				FROM #__user_usergroup_map
				WHERE user_id =".$user->id."
				AND `group_id` NOT LIKE '2'
    		)";

    	$db->setQuery($sql);
    	$org_name = explode(' ',$db->loadResult());
    	$access = self::getUserAccessGroupId('Organization '.$org_name[1].' Member');
    	return $access;
    }
}
?>
