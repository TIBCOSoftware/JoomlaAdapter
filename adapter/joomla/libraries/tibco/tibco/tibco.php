<?php

/* @copyright Copyright © 2013-2015, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die;
require_once JPATH_BASE . "/includes/api.php";

/**
 * Library of functions specific to TIBCO Software's OpenAPI
 *
 * @since       1.0
 */
class TibcoTibco {

    /**
     * create ping user profile
     * @param array $options: lastName, firstName, org_id, usertype
     */
    public function createUserProfile($options = array()) {
        include_once JPATH_BASE . "/includes/userprofile.php";
        CreateUserprofileApi::insertUserProfile($options);
    }

    /**
     * Creat an organization and get the organization's id
     * @param  array  $options['title'] title of the new created organization
     * @return           [description]
     */
    public function forceGetOrganizationId($options = array()) {
        include_once JPATH_BASE . "/includes/organization.php";
        return CreateOrganizationApi::insertOrganization($options);
    }

    public static function updateApplicationForPlan($apps, $product, $new_subscription) {
        include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autosubplan.php';
        $oldSubscriptions = array();

        foreach ($apps as $app_id) {
            if (TibcoAutoSubPlan::checkProductsForApplication($app_id, $product)) {
                $oldSubscriptions[$app_id] = TibcoAutoSubPlan::updateSubscriptionForApplication($app_id, $product, $new_subscription);
            } else {
                if (TibcoAutoSubPlan::bindProductForApplicationId($app_id, $product) && TibcoAutoSubPlan::bindNewSubscriptionForApplicationId($app_id, $new_subscription)) {
                    $oldSubscriptions[$app_id] = array();
                }
            }
        }
        return $oldSubscriptions;
    }

    /**
     * get organization detail by organization id.
     * @param string $org_id
     * @return array it include three values: API limit usage percentage(field_id=130)
     */
    public function getOrganizationDetailById($org_id) {
        $rv = array();
        $db = JFactory::getDbo();
        $sql = "SELECT field_value FROM #__js_res_record_values WHERE  record_id=" . $org_id . " AND field_id in (130) ORDER BY id";
        $db->setQuery($sql);
        if ($result = $db->loadObjectList()) {
            foreach ($result as $record) {
                array_push($rv, $record->field_value);
            }
        }
        return $rv;
    }

    public static function getFlagForShow($id = 0) {
        if ($id) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select("is_show")->from("asg_product_show_map")->where("product_id=" . $id);
            $db->setQuery($query);
            if ($result = $db->loadColumn()) {
                return $result[0];
            }
        }
    }

    /**
     * Get the id of a user group which belongs to an organization and denotes a special type of users, e.g. a developer, a contact or an administrator.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $org_id The id of the organization whose groups are to be queried.
     * @param string $user_type The user type that the group denotes. This is optional. The default value is "Member".
     * @return int The id the group which matches the criteria or 0 if no group's been found.
     */
    public static function getGroupByOrgAndType($org_id, $user_type = DeveloperPortalApi::USER_TYPE_MEMBER) {
        $ret = 0;
        if ($org_id) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)->select("id")->from("#__usergroups")->where("title like 'Organization " . $org_id . " " . $user_type . "'");
            $db->setQuery($query);
            if ($result = $db->loadAssoc()) {
                $ret = $result["id"];
            }
        }
        return $ret;
    }

    /**
     * Associate the given user groups to a view access level. If anyone of the user groups has already been associated
     * to the view access level it'll be left intact.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $view_level The id of the view access level to be associated to.
     * @param array $groups An array of ids of some user groups to be associated to a view access level.
     */
    public static function linkGroupWithViewLevel($view_level, $groups = array()) {
        if ($view_level) {
            $table_view_level = JTable::getInstance("ViewLevel");
            if ($table_view_level->load($view_level)) {
                $rules = (array) json_decode($table_view_level->rules);
                $rules = array_merge($rules, $groups);
                $rules = array_unique($rules);
                $table_view_level->rules = json_encode($rules);
                $table_view_level->store();
            }
        }
    }

    /**
     * Get the id of the organization to which a user profile that's specified by the $user_profile_id belongs.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $user_profile_id The id of the user profile for which the organization is to be retrieved.
     * @return int A positive integer which denotes the id of of the organization or 0 if no organization was found.
     */
    public static function getOrgIdByUserProfileId($user_profile_id) {
        $ret = 0;
        if ($user_profile_id) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)->select("field_value")->from("#__js_res_record_values")->where("type_id=8")->where("field_id=47")->where("record_id=" . $user_profile_id);
            $db->setQuery($query);
            if ($result = $db->loadAssoc()) {
                $ret = $result["field_value"];
            }
        }
        return $ret;
    }

    /**
     * Get the id of the organization of the user who's currently logged in.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @return int The non-zero id of the organization if there's a logged-in user. 0 otherwise.
     */
    public static function getCurrentUserOrgId() {
        $ret = 0;
        $user = JFactory::getUser();
        $db = JFactory::getDbo();
        $sql = "SELECT id FROM #__js_res_record WHERE id IN ";
        $sql .= "(SELECT field_value FROM `#__js_res_record_values` WHERE type_id=8 AND field_id=47 AND record_id IN ";
        $sql .= "(SELECT record_id FROM #__js_res_record_values WHERE type_id=8 AND field_id=77 AND field_value=" . $user->id . "))";
        $db->setQuery($sql);
        if ($result = $db->loadAssoc()) {
            $ret = $result["id"];
        }
        return $ret;
    }

    /**
     * Get the id of the organization which has the subscription with the id $sub_id.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $sub_id The id of the subscription whose subscribing organization are to be looked for.
     * @return int The non-zero id of the organization if any. 0 otherwise.
     */
    public static function getSubOrgId($sub_id) {
        $ret = 0;
        $db = JFactory::getDbo();
        $sql = "SELECT field_value FROM #__js_res_record_values WHERE field_id=73 AND type_id=10 AND record_id=" . $sub_id;
        $db->setQuery($sql);
        if ($result = $db->loadAssoc()) {
            $ret = $result["field_value"];
        }
        return $ret;
    }
    
    public static function getRequestList( $status = 1 ) {
        $user = JFactory::getUser();
        $result = '';
        if (isset($user->id) && !empty($user->id)) {
            $db = JFactory::getDbo();
            $sql = 'SELECT * FROM `#__request_list` WHERE `created_by` = ' . $user->id;
            if (isset($status) && $status != 0) {
                $sql .= ' AND `status` = ' . $status * 1;
            }
            $sql .= ' ORDER BY id DESC';
            $db->setQuery($sql);
            $result = $db->loadObjectList();
        } 
        return $result;
    }
    
    public static function getAppName( $apps = 0 ) {
        $result = '';
        if ( $apps != 0 ) {
            $db = JFactory::getDbo();
            $sql = 'SELECT title FROM `#__js_res_record` WHERE id IN('.$apps.')';
            $db->setQuery($sql);
            $res = $db->loadObjectList();
            foreach( $res as $v ) {
                $result .= $v->title.',';
            }
        } else {
            $result = 'None,';
        }
        return substr( $result, 0, -1 );
    }

    /**
     * Get the value of the field with id 145 of API.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param number $api_id The id of the api of which the field with id 145 is to be read.
     * @return string The value of the field with id 145 of API.
     */
    public static function getAPICreateProxyById($api_id = 0) {
    	$ret = 0;
        if($api_id > 0) {
            $db = JFactory::getDbo();
            $sql = "SELECT * FROM `openapi_js_res_record_values` WHERE type_id=2 AND field_id=145 AND record_id=" . $api_id;
            $db->setQuery($sql);
            if($result = $db->loadAssoc()) {
                $ret = $result["field_value"];
            }
    	}
    	return $ret;
    }
    /**
     * Get Joomla default validate rule to validate password
     * errno: 1 length, 2 int,3 sym,4 upp;
     */
	public static function validatePassword($password,$format=''){
		$resArr = array( 'success'=>1,'errno'=>array() );
		$ruleArr = self::getPasswordRule();
		$min_length = $ruleArr['minimum_length'];
		$min_int = $ruleArr['minimum_integers'];
		$min_sym = $ruleArr['minimum_symbols'];
		$min_upp = $ruleArr['minimum_uppercase'];
		if ( strlen($password) < $min_length ) {
				$resArr['success'] = 0;
				$resArr['errno']['len'] = $min_length;
		}
		preg_match_all('/[0-9]/',$password,$intArr);
		$int_num = count($intArr[0]);
		if ( $int_num < $min_int ) {
			$resArr['success'] = 0;
			$resArr['errno']['int'] = $min_int;
		}
		preg_match_all('/[A-Z]/',$password,$uppArr);
		$upp_num = count($uppArr[0]);
		if ( $upp_num < $min_upp ) {
			$resArr['success'] = 0;
			$resArr['errno']['upp'] = $min_upp;
		}
		preg_match_all('/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/',$password,$symArr);
		$sym_num = count($symArr[0]);
		if ( $sym_num < $min_sym ) {
			$resArr['success'] = 0;
			$resArr['errno']['sym'] = $min_sym;
		}
		return $resArr;		
	}
	
	public static function getProductImage($id) {
    	$db = JFactory::getDbo();
    	$sql = "SELECT `fields` FROM `#__js_res_record` WHERE `id` = ".($id*1)." LIMIT 1";
    	$db->setQuery($sql);
    	$fieldsJson = $db->loadObjectList();
    	$fieldArr = json_decode($fieldsJson[0]->fields,true);
    	return $fieldArr[3][image];
    }
    /**
     * Get password validate rule.
     * @return mixed
     */
    private static function getPasswordRule(){
    	$db = JFactory::getDbo();
    	$sql = 'SELECT `params` FROM `openapi_extensions` WHERE `name` = \'com_users\' LIMIT 1';
    	$db->setQuery($sql);
    	$ruleJson = $db->loadObject();
    	return json_decode($ruleJson->params,true);
    }

    /**
     * Get the id of the environment to which the gateway is attached.
     *  
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $gateway_id The id of the gateway of which the parent environment is to be retrieved.
     * @return int The id of the environment to which the gateway is attached.
     */
    public static function getEnvIdByGatewayId($gateway_id) {
    	$ret = 0;
    	$db = JFactory::getDbo();
    	$sql = "SELECT * FROM `#__js_res_record_values` WHERE field_id=16 AND record_id=" . $gateway_id;
    	$db->setQuery($sql);
    	if($result = $db->loadAssoc()) {
    		$ret = $result["field_value"];
    	}
    	return $ret;
    }
    
    /**
     * Tell whether an environment is managed by gateway.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param integer $env_id The id of the environment.
     * @return boolean True if the environment is managed by gateway. False otherwise.
     */
    public static function isEnvManaged($env_id) {
        $ret = -1;
        $db = JFactory::getDbo();
        $sql = "SELECT field_value FROM #__js_res_record_values WHERE field_id=132 AND record_id=" . $env_id;
        $db->setQuery($sql);
        if($result = $db->loadAssoc()) {
            if($result["field_value"] == 1) {
                $ret = 1;
            }
        }
        return $ret;
    }

    /**
     * Delete all records when the related record is being delete
     * 
     * @author Vivian Ma<xima@tibco-support.com>
     * @param integer $n_parent_type_id The type id of related record.
     * @param integer $n_parent_record_id The type value of the field.
     * @return boolean True if the records are deleted. False otherwise.
     */
    public static function deleteRelatedRecords($n_parent_record_id, $n_parent_type_id, $user_id) {
        require_once JPATH_COMPONENT . "/controllers/records.php";
        $ret = TRUE;
        switch ($n_parent_type_id) {
            case 1:
                $n_type_id = 7;
                $n_field_id = 53;
                break;
            case 2:
                $n_type_id = 6;
                $n_field_id = 30;
                break;
            case 4:
                $n_type_id = 3;
                $n_field_id = 16;
                break;
            case 5:
                $n_type_id = 8;
                $n_field_id = 47;
                break;
            case 8:
                if(isset($user_id) && !empty($user_id)){
                    $ret = TibcoTibco::deleteUser($user_id);
                }
                break;
        }
        if(isset($n_type_id) && isset($n_field_id)){      
            $app  = JFactory::getApplication();
            $controller = new CobaltControllerRecords();
            $db = JFactory::getDbo();              
            $sql = "SELECT record_id FROM `#__js_res_record_values` WHERE field_id= ". $n_field_id ." AND field_value=" . $n_parent_record_id ." AND type_id=" . $n_type_id ;
            $db->setQuery($sql);
            if($result = $db->loadObjectList()) {
                foreach ($result as $record) {
                    $app->input->set('id', $record->record_id);
                    $ret = $ret && $controller->delete();
                }
            }
        }
        return $ret;
    }

    /**
     * Used for deleting user.
     * 
     * @author Vivian Ma<xima@tibco-support.com>
     * @param integer $user_id The id of the user.
     * @return boolean True if the user are deleted. False otherwise.
     */
    public static function deleteUser($user_id){
        $ret = FALSE;
        $db = JFactory::getDbo();
        if(!empty($user_id)){

            $sql1 = 'DELETE FROM `#__users` WHERE `id` = '.$user_id;
            $db->setQuery($sql1);
            $db->execute();

            $sql2 = 'DELETE FROM `#__user_profiles` WHERE `user_id` = '.$user_id;
            $db->setQuery($sql2);
            $db->execute();

            $sql3 = 'DELETE FROM `#__user_usergroup_map` WHERE `user_id` = '.$user_id;
            $db->setQuery($sql3);
            $db->execute();            

            $sql4 = 'DELETE FROM `#__user_keys` WHERE `user_id` = '.$user_id;
            $db->setQuery($sql4);
            $db->execute();

            $sql5 = 'DELETE FROM `#__user_notes` WHERE `user_id` = '.$user_id;
            $db->setQuery($sql5);
            $db->execute();
            $ret = TRUE;
        }
        return $ret;
    }

    /**
     * Used for deleting a record.
     * 
     * @author Vivian Ma<xima@tibco-support.com>
     * @param integer $id The id of the record.
     * @param integer $type_id The type id of the record.
     * @return boolean True if the user are deleted. False otherwise.
     */
    public static function deleteRecord($id, $type_id){
        $result = FALSE;
        require_once JPATH_COMPONENT . "/controllers/records.php";
        if($type_id==8){
            $user_id = DeveloperPortalApi::getUserIdByProfileId($id);
        }
        $controller = new CobaltControllerRecords();
        $result = $controller->delete();
        TibcoTibco::deleteRelatedRecords($id,$type_id,$user_id);
        return $result;

    }

    public static function getWSDLSubfolder() {

        $ret = '';
        $db = JFactory::getDbo();
        $sql = 'SELECT params FROM #__js_res_fields WHERE id=127';
        $db->setQuery($sql);

        if($result = $db->loadObject()) {

            $wsdl_params = json_decode($result->params);
            $ret = $wsdl_params->params->subfolder;
        }

        return $ret;
    }

    /**
     * Find the WSDL file with the given file id and replace the value of the "location" properties of all "address"
     * elements with the base path of the facade environment of that API. Then return the modified content.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param mixed $file_record An object containing the information of the file record.
     * @param string $subfolder The value of the "subfoler" property set in the WSDL file field setting of the Cobalt
     * back-end.
     * @return string The modified content of the WSDL file or empty string if the file doesn't exist or doesn't
     * readable.
     */
    public static function replaceLocationsInWSDL($file_record) {

        $ret = "";

        if($file_record != null) {

            $subfolder = TibcoTibco::getWSDLSubfolder();
            $file_path = JPATH_BASE . "/uploads/" . $subfolder . "/" . $file_record->fullpath;
            $regexp = '/(location=")[^\/]*\/\/[^\/]+(\/)/i';

            if (is_readable($file_path)) {

                $file = fopen($file_path, "r");
                $contents = fread($file, filesize($file_path));
                fclose($file);

                $facade_basepath = TibcoTibco::getFacadeBasePath($file_record->record_id);

                $ret = preg_replace($regexp, '${1}' . $facade_basepath . '${2}', $contents);
            }
        }

        return $ret;
    }

    /**
     * Get the file record with the provided id in the database.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $file_id The id of the file to be found.
     * @return mixed An object containing the information about the file record or an empty object if nothing
     * was found.
     */
    public static function getFileById($file_id) {

        $db = JFactory::getDbo();
        $sql = "SELECT * FROM #__js_res_files WHERE id=" . $file_id;
        $query = $db->setQuery($sql);

        if($result = $query->loadObject()) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Insert the string "facade" in the given file name.
     *
     * @param string $filename The file name to be inserted.
     * @return string A new file name with the string "facade" inserted right before the extension and delimited by dots.
     */
    public static function insertFacadeInFileName($filename) {

        $array_filename = explode('.', $filename);
        $extension = array_pop($array_filename);
        array_push($array_filename, 'facade');
        array_push($array_filename, $extension);
        return implode('.', $array_filename);
    }

    /**
     * Get the base base path of the facade environment of an API.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param int $api_id The id of the API.
     * @return string The value of the base path of the facade environment of the API whose id equals the one in the
     * argument or an empty string if nothing was found.
     */
    private static function getFacadeBasePath($api_id) {

        $ret = '';

        $db = JFactory::getDbo();
        $sql = 'SELECT * FROM openapi_js_res_record_values WHERE field_id=14 AND record_id IN (SELECT record_id FROM openapi_js_res_record_values WHERE field_id=25 AND field_value=' . $api_id . ')';
        $db->setQuery($sql);

        if($result = $db->loadObject()) {
            $ret = $result->field_value;
        }

        return $ret;
    }

    /**
     * @author Crystal Liu<yunliu@tibco-support.com>
     * @return The fields.
     */
    public static function getFields($id) {

        $db = JFactory::getDbo();
        $sql = 'SELECT fields FROM #__js_res_record WHERE id=' . $id . '';
        $db->setQuery($sql);
        $result = $db->loadObject();
        return $result;
    }



}