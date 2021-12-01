<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
?>
<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.controlleradmin');
jimport('joomla.application.component.controllerusers');
require_once JPATH_BASE . "/includes/api.php";
require_once JPATH_ROOT . '/components/com_cobalt/api.php';
require_once JPATH_BASE . "/includes/subcreate.php";
require_once JPATH_BASE . "/administrator/components/com_cobalt/models/record.php";
use Joomla\Utilities\ArrayHelper;

class CobaltControllerAjaxMore extends JControllerAdmin {
    
    public function returnData() {
        AjaxHelper::send('admin');
    }

    public function updateStatusOfKey() {
        $ids = $_REQUEST['keyList'];
        ArrayHelper::toInteger($ids);

        $ids = implode(',', $ids);
        if (empty($ids)) {
            AjaxHelper::send('Related key has updated', $key = 'result');
        }
        $db = JFactory::getDbo();

        $db -> setQuery('UPDATE #__js_res_record_values SET field_value = "Inactive" WHERE field_id = 85 AND section_id = 5 AND record_id IN (' . $ids . ')');
        $db -> query();
        AjaxHelper::send('Related key has updated', $key = 'result');
    }

    public function getEnvironmentByApis() {

        if (isset($_REQUEST['apis'])) {
            $apis = $_REQUEST['apis'];
        }
        if (!$apis) {
            AjaxHelper::error("You didn't select any apis");
        }
        $db = JFactory::getDbo();
        $db -> setQuery('SELECT record_id,count(record_id) as record_num FROM `#__js_res_record_values` where field_id=25  and field_value in (' . (int) $apis . ') group by record_id order by record_num desc');
        $db -> query();
        $result = array();
        $api_count = count(explode(",", $apis));

        foreach ($db->loadObjectList() as $key => $val) {
            if ($val -> record_num >= $api_count) {
                $result[] = $val -> record_id;
            }
        }
        if (!empty($result)) {
            AjaxHelper::send($result, "result");
        } else {
            AjaxHelper::error("Don't have any public environment!");
        }
    }

    public function getApiRecordsById() {

        if (isset($_REQUEST['ids'])) {
            $ids = $_REQUEST['ids'];
        }

        if (!$ids) {
            AjaxHelper::error("You didn't select any apis");
        }

        $ids = explode(',', $ids);
        $api = new DeveloperPortalApi();
        $result = array();

        foreach ($ids as $key => $id) {
            if ($api -> getApiFieldsForProduct($id)) {
                $result[] = $api -> getApiFieldsForProduct($id);
            }
        }

        if (!empty($result)) {
            AjaxHelper::send($result, "result");
        } else {
            AjaxHelper::error("Don't have any public environment!");
        }
    }

    public function archiveOperationsInSpec() {
        if (isset($_REQUEST['specPath'])) {
            $specPath = $_REQUEST['specPath'];
            $apiID = $_REQUEST['apiID'];
            if (!empty($specPath) && !empty($apiID)) {
                $file = file_get_contents($specPath);
                $spec = json_decode($file);
                $titlesArray = array();
                foreach ($spec->apis as $key => $api) {
                    foreach ($api->operations as $operation) {
                        $titlesArray[] = '"'.$operation->nickname.'"';
                    }
                }

                if (count($titlesArray)>0) {
                    ArrayHelper::toString($titlesArray);
                    $titles = implode(",",$titlesArray);
                    $db = JFactory::getDbo();
                    $sql = 'Select id from `#__js_res_record` where id in (select record_id from `#__js_res_record_values` where field_id=30 and field_value='.(int) $apiID.') and title in ('.$titles.') and published <> 2';
                    $db->setQuery($sql);
                    $result = array();
                    foreach ($db->loadObjectList() as $key => $value) {
                        $result[] = $value->id;
                    }
                    if (count($result)>0) {
                        ArrayHelper::toInteger($result);
                        $ids = implode(",",$result);
                        $sql = 'Update `#__js_res_record` set published=2 where id in ('. $ids .')';
                        $db->setQuery($sql);
                        if ($db -> query()) {
                            AjaxHelper::send($result);
                        }else{
                            AjaxHelper::error("");
                        }
                    }else{
                        AjaxHelper::send("");
                    }
                }
            }else{
                AjaxHelper::error("");
            }
        }
    }

// Uses the Cobalt API to find the Parent / Child Relationships and Set the Operations published to 2.
	public function deleteOperationsAPI() {
		
	
		//Insantiate the Cobalt API
		$api = new CobaltApi();
		
		//Get the API ID
        if (isset($_REQUEST['apiID'])) {
            $apiID = $_REQUEST['apiID'];
            $result = true;
			
		//Test out ID, make sure it is only numbers // Validation
      
        $contentdata = $api->records(
		$section_id = 2, 
		$view_what, 
		$orderby, 
		$type_ids = 6,
		$user_id,
		$category_id, 
		$limit = $usersetlimit, 
		$template,
		$client,
		$client_id,
		$lang,
		$ids
            );
			
		$response = $this->getJsonFromObject($contentdata['list']);
		$db = JFactory::getDbo();
		
        foreach ($response as $key => $value) {

        	if ($response[$key]['fields'][30] == $apiID)
			{
				$sql = "Update #__js_res_record SET published=2 WHERE id = " . (int) $response[$key]['id'];
				$results = $db->setQuery($sql);
				$db->query();
			}
        }     

        }
	}
    
	// Use this Function to turn some of the PHP Objects into JSON DATA - For REST APIS -
    private function getJsonFromObject($data){
        $resultArr = array();
        foreach ( $data as $obj ) {
            $array = get_object_vars($obj);
            $newArr = array();
            foreach ( $array as $key => $val ){
                if ( $key === 'params' ) {
                    $tmp = get_object_vars($val);
                    foreach( $tmp as $pk => $pv ){
                        $newArr[$key][$pk] = (array)$pv;
                    }

                }elseif( $key === 'ctime' || $key === 'mtime' ){
                    $tmp = get_object_vars($val);
                    foreach( $tmp as $tk => $tv ){
                        $newArr[$key][$tk] = (array)$tv;
                    }
                }elseif( $key !== 'fields_by_id' && $key !== 'fields_by_groups' && $key !== 'fields_by_key' ){
                    $newArr[$key] = $val;
                }
            }
            $resultArr[]=$newArr;
        }
        return $resultArr;
    }
	
    public function _getEmailTemplateByAlias($alias){
      $db = JFactory::getDbo();
      $db -> setQuery('SELECT * FROM #__email_templates WHERE alias="'. $db->quote($alias) .'" AND published = 1 LIMIT 1');
      return $db -> loadObject();
    }
    /**
     * user request custom plan
     *   1. send email to administrator of joomla back-end
     *   2. send email to administrator and contacter organization
     *   3. send email to requester
     * @author Owen
     */
    public function requestNormalPlan() {
      $user = JFactory::getUser();
      $user_id = $user -> id;
      
      $plan_id = $_POST["planId"];
      $product_id = $_POST["productId"];
      $_domain = DeveloperPortalApi::getHostUrl();
      
      $config = JFactory::getConfig();
      $admin_email = $config -> get('mailfrom');
      if ($user_id && $plan_id && $product_id) {
        $plan_url         = $_domain.JRoute::_(Url::record($plan_id));
        $product_url      = $_domain.JRoute::_(Url::record($product_id));
        $organization     = DeveloperPortalApi::getUserOrganization();
        $organization_url = $_domain.JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id='.$organization[0]);
        $user_url         = $_domain.JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id='.DeveloperPortalApi::getUserProfileId($user_id));
        
        //send email to administrator of joomla back-end
        $results = $this->_getEmailTemplateByAlias("request_normal_plan_notify_admin_of_joomla");
        if ($results -> subject && $results -> content) {
          $create_sub_url = JURI::root()."index.php/subscriptions/submit/6-subscriptions/10-subscription?sub_product_id=".$product_id."&sub_plan_id=".$plan_id."&sub_uid=".$user_id.'&organization_id='.$organization[0];
          $title = $results -> subject;
          $content = $results -> content;
          $content = str_replace("{CREATE_SUB_URL}", $create_sub_url, $content);
          $content = str_replace("{PLAN_URL}", $plan_url, $content);
          $content = str_replace("{PRODUCT_URL}", $product_url, $content);
          $content = str_replace("{ORGANIZATION_URL}", $organization_url, $content);
          $content = str_replace("{USER_URL}", $user_url, $content);
          
          $admin_email_group = DeveloperPortalApi::getEmailsOfJoomlaAdmins();
          $is_send_email_to_admin_joomla = DeveloperPortalApi::send_email($admin_email_group, $title, $content, $results -> isHTML);
        }
        
        //send email to administrator and contacter organization
        $results = $this->_getEmailTemplateByAlias("request_normal_plan_notify_admin_contacter_of_organization");
        if ($results -> subject && $results -> content) {
          $title = $results -> subject;
          $content = $results -> content;
          $content = str_replace("{PLAN_URL}", $plan_url, $content);
          $content = str_replace("{PRODUCT_URL}", $product_url, $content);
          $content = str_replace("{ORGANIZATION_URL}", $organization_url, $content);
          $content = str_replace("{USER_URL}", $user_url, $content);
        
          $admin_email_group = array_merge(DeveloperPortalApi::getEmailsOfOrganizationAdmin(), DeveloperPortalApi::getEmailsOfOrganizationContact());
          DeveloperPortalApi::send_email($admin_email_group, $title, $content, $results -> isHTML);
        }
        
        if ($is_send_email_to_admin_joomla == "1") {
          //send email to requester
          $results = $this->_getEmailTemplateByAlias("request_plan_notify_requester");
          if ($results -> subject && $results -> content) {
            $title = $results -> subject;
            $content = $results -> content;
            $content = str_replace("{PLAN_URL}", $plan_url, $content);
            $content = str_replace("{PRODUCT_URL}", $product_url, $content);
            $content = str_replace("{ORGANIZATION_URL}", $organization_url, $content);
            
            DeveloperPortalApi::send_email($user->email, $title, $content, $results -> isHTML);
          }
          $result=array();
          $result['msg']=JText::_('PLAN_REQUEST_RESULT_SUCCESS');
          AjaxHelper::send($result,'result');
        }else{
          AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_2'));
        }
      }else{
        AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_4'));
      }
    }
    /**
     * user request custom plan
     *   1. send email to administrator of joomla back-end
     *   2. send email to administrator and contacter organization
     * @author Owen
     */
    public function requestCustomPlan() {
      $user = JFactory::getUser();
      $user_id = $user -> id;
      
      $product_id = $_POST["productId"];
      $_domain = DeveloperPortalApi::getHostUrl();
      
      $config = JFactory::getConfig();
      $admin_email = $config -> get('mailfrom');
      if ($user_id && $product_id) {
        $product_url      = $_domain.JRoute::_(Url::record($product_id));
        $organization     = DeveloperPortalApi::getUserOrganization();
        $organization_url = $_domain.JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id='.$organization[0]);
        $user_url         = $_domain.JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id='.DeveloperPortalApi::getUserProfileId($user_id));
        
        //send email to administrator of joomla back-end
        //send email to administrator and contacter organization
        $results = $this->_getEmailTemplateByAlias("request_custom_plan_notify_admin_contacter");
        if ($results -> subject && $results -> content) {
          $rate_limit  = $_POST["rate_limit"];
          $quota_limit = $_POST["quota_limit"];
          $additional_request = $_POST["additional_request"];
        
          $title = $results -> subject;
          $content = $results -> content;
          $content = str_replace("{PRODUCT_URL}", $product_url, $content);
          $content = str_replace("{ORGANIZATION_URL}", $organization_url, $content);
          $content = str_replace("{RATE_LIMIT}", $rate_limit, $content);
          $content = str_replace("{QUOTA_LIMIT}", $quota_limit, $content);
          $content = str_replace("{ADDITIONAL_REQUEST}", $additional_request, $content);
          $content = str_replace("{USER_URL}", $user_url, $content);
          
          $admin_email_group = DeveloperPortalApi::getEmailsOfJoomlaAdmins();
          $is_send_email_to_admin_joomla = DeveloperPortalApi::send_email($admin_email_group, $title, $content, $results -> isHTML);
        }
        
        if ($is_send_email_to_admin_joomla == "1") {
          //send email to requester
          $results = $this->_getEmailTemplateByAlias("request_plan_notify_requester");
          if ($results -> subject && $results -> content) {
            $title = $results -> subject;
            $content = $results -> content;
            $content = str_replace("{PRODUCT_URL}", $product_url, $content);
            $content = str_replace("{ORGANIZATION_URL}", $organization_url, $content);
          
            DeveloperPortalApi::send_email($user->email, $title, $content, $results -> isHTML);
          }
          $result=array();
          $result['msg']=JText::_('PLAN_REQUEST_RESULT_SUCCESS');
          AjaxHelper::send($result,'result');
        }else{
          AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_2'));
        }
      }else{
        AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_4'));
      }
    }
    /**
     * verify if there is same plan title existed in same product. 
     */
    public function validatePlanTitle() {
      $plan_title = urlencode($_POST["plan_title"]);
      $product_id = $_POST["product_id"];
      $plan_id = $_POST["plan_id"];
      if ($plan_title && $product_id) {
        $db = JFactory::getDbo();
        $sql = "SELECT COUNT(r.id) AS sum  FROM openapi_js_res_record AS r, openapi_js_res_record_values AS rv ";
        $sql.= " WHERE rv.field_id=53 AND rv.type_id=7 AND rv.record_id=r.id";
        $sql.= " AND rv.field_value=". (int) $product_id." AND LOWER(r.title)='".strtolower($plan_title)."'";
        $sql.= $plan_id ? " AND r.id!=". (int) $plan_id : "";
        $db->setQuery($sql);
        $result = $db->loadObject();
        if ($result->sum==0) {
          AjaxHelper::send("");
        }else{
          AjaxHelper::error(JText::_('DUPLICATE_PLAN_TITLE_IN_PRODUCT'));
        }
      }else{
        AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_4'));
      }
    }
    /**
     * verify if there is same gateway title existed in same environments.
     */
    public function validateGatewayTitle() {
      $gateway_title  = urlencode($_POST["gateway_title"]);

      $environment_id = $_POST["environment_id"];
      $gateway_id = $_POST["gateway_id"];
      if ($gateway_title && $environment_id) {
        $db = JFactory::getDbo();
        $sql = "SELECT COUNT(r.id) AS sum  FROM openapi_js_res_record AS r, openapi_js_res_record_values AS rv ";
        $sql.= " WHERE rv.field_id=16 AND rv.type_id=3 AND rv.record_id=r.id";
        $sql.= " AND rv.field_value=". (int) $environment_id." AND LOWER(r.title)='".strtolower($gateway_title)."'";
        $sql.= $gateway_id ? " AND r.id!=". (int) $gateway_id : "";
        $db->setQuery($sql);
        $result = $db->loadObject();
        if ($result->sum==0) {
          AjaxHelper::send("");
        }else{
          AjaxHelper::error(JText::_('DUPLICATE_GATEWAY_TITLE_IN_ENVIRONMENT'));
        }
      }else{
        AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_4'));
      }
    }
    /**
     * verify if there is same operation title existed in same api.
     */
    public function validateOperationTitle() {
      $operation_title  = $_POST["operation_title"];
      $api_id = $_POST["api_id"];
      $operation_id = $_POST["operation_id"];
      if ($operation_title && $api_id) {
        $db = JFactory::getDbo();
        $sql = "SELECT COUNT(r.id) AS sum  FROM openapi_js_res_record AS r, openapi_js_res_record_values AS rv ";
        $sql.= " WHERE rv.field_id=30 AND rv.type_id=6 AND rv.record_id=r.id";
        $sql.= " AND rv.field_value=". (int) $api_id." AND LOWER(r.title)='".strtolower($operation_title)."'";
        $sql.= $operation_id ? " AND r.id!=". (int) $operation_id : "";
        $db->setQuery($sql);
        $result = $db->loadObject();
        if ($result->sum==0) {
          AjaxHelper::send("");
        }else{
          AjaxHelper::error(JText::_('DUPLICATE_OPERATION_TITLE_IN_API'));
        }
      }else{
        AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_4'));
      }
    }
    /**
     * validate gateway's management URL. 
     */
    public function validateGatewaysManagementURLs() {
      $urls = explode(",", $_POST["urls"]);
      $duplicates = array();
      if (count($urls)>0) {
        $db = JFactory::getDbo();
        for($i=0;$i<count($urls);$i++) {
          $sql = "select count(id) as sum from #__js_res_record where published!=2 and id in (select record_id from #__js_res_record_values where field_id=89 and type_id=3 and field_value=' . $db->quote($urls[$i]) . ')";
          $db->setQuery($sql);
          $result = $db->loadObject();
          if ($result->sum!=0) {
            $duplicates[] = $urls[$i];
          }
        }
        if (count($duplicates) == 0) {
          AjaxHelper::send("");
        } else {
          AjaxHelper::error(JText::_('DUPLICATE_MANAGEMENT_URL_IN_GATEWAYS') . ": " . join(",", $duplicates) . ".");
        }
      }else{
        AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_4'));
      }
    } 
  
    /**
     * invoke when user click send in support page. 
     */

    public function requestSupportInit() {
       $plugin = JPluginHelper::getPlugin('captcha','recaptcha');
        // Check if plugin is enabled
        if ($plugin){
          // Get plugin params
          $pluginParams = new JRegistry($plugin->params);
          $sitekey = $pluginParams->get('public_key');
          if($sitekey){
            AjaxHelper::send($sitekey,"sitekey");
          }
        }
        AjaxHelper::error('Captcha plugin not set or not found. Please contact a site administrator.');
    }

    function _verifyCaptcha($captcha){
      $dispatcher = JEventDispatcher::getInstance();
      JPluginHelper::importPlugin('captcha');
        try{
          $res = $dispatcher->trigger('onCheckAnswer',$captcha);
        }
        catch(exception $e){
          AjaxHelper::error($e->getMessage());
          return false;
        }
        if(!$res[0]){
            AjaxHelper::error('Invalid Captcha..');
        }
    }

    public function requestSupport() {

        $input = JFactory::getApplication()->input;
        $captcha=$input->getString("g-recaptcha-response",'');
        if($captcha===''){
          AjaxHelper::error("Captcha field not set, Please validate yourself as a human being.");
        }
        $this->_verifyCaptcha($captcha);
        $user_id = JFactory::getUser()->id;
        $name =  $input->getString("fname",'');
        if ( $user_id == 0 )
        $name = $input->getString("fname",'');
        $email = $input->getString("email",'');
        $user_content =$input->getString("content",'');

        $db = JFactory::getDbo();
        $db -> setQuery('SELECT * FROM #__email_templates WHERE alias="request_email" AND published = 1 LIMIT 1');
        $results = $db -> loadObject();
        if ($results -> subject && $results -> content) {
          $config = &JFactory::getConfig();
          $title = str_replace("{USER}", $name, $results -> subject);

          $content = str_replace("{USER}", $name, $results -> content);
          $content = str_replace("{EMAIL}", $email, $content);
          $content = str_replace("{USER_CONTENT}", $user_content, $content);

          $config = JFactory::getConfig();
          $admin_email = $config -> get('mailfrom');

          if (DeveloperPortalApi::send_email($admin_email, $title, $content, $results -> isHTML)) {
            AjaxHelper::send("success");
          }else{
            AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_2'));
          }
        }else{
          AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_6'));
        }
      }
  
    /**
     * get enabled subscriptions' product&plan in specific application. 
     */
  public function subscriptionsInApp() {
    $app_ids = $_POST["app_ids"];
    $sub_ids = $_POST["sub_ids"];
    $plan_ids = $_POST["plan_ids"];
    $counter = array();
    if (!empty($app_ids)) {
      $counter = $app_ids;
    }else if(!empty($sub_ids)){
      $counter = array(1);
    }else{
      AjaxHelper::error("");
    }
    $returnValue = array();
    foreach ($counter as $app_id) {
      if (!empty($app_ids)) {
        $records = DeveloperPortalApi::subscriptionsInApplication($app_id);
      }else{
        $records = $sub_ids;
      }
      
          if (count($records)>0) {
        $results = array();
        $db = JFactory::getDbo();
        ArrayHelper::toInteger($records);
        $sql = 'select title as productName from `#__js_res_record` where id in (SELECT field_value FROM `#__js_res_record_values` WHERE field_id=114 and record_id in ('.implode(',',$records).'))';
        $db -> setQuery($sql);
        $results['products'] = $db->loadObjectList();
        $sql = 'select fields as planDetail from `#__js_res_record` where id in (SELECT field_value FROM `#__js_res_record_values` WHERE field_id=69 and record_id in ('.implode(',',$records).'))';
        if (!empty($plan_ids)) {
            ArrayHelper::toInteger($plan_ids);
          $sql = 'select id,title,fields as planDetail from `#__js_res_record` where id in ('.implode(',',$plan_ids).')';
        }
        $db -> setQuery($sql);
        $results['plans'] = $db->loadObjectList();
        $sql = 'select id,pct from (select id from #__js_res_record where id in ('.implode(',',$records).')) a left join (SELECT subscription_id, pct FROM  `asg_subscription_usage` WHERE subscription_id in ('.implode(',',$records).')) b on a.id=b.subscription_id';
        $db -> setQuery($sql);
        $results['usage'] = $db->loadObjectList();
        $returnValue[] = $results;
          }else {
            $returnValue[] = array();
          }
    }
    
    AjaxHelper::send($returnValue);
  }
  
    /**
     * get alert data from table asg_log 
     */
  public function alertMessages(){
    $app = JFactory::getApplication();
    $input= $app->input;
    $orgID = $input->get('org_id', null, 'INT');
      if(!$orgID) {
        AjaxHelper::error( JText::_('Org_id not set'));
        }
    $comEmail = JComponentHelper::getComponent('com_emails');
    $limitCount = $comEmail->params->get('show_alerts_count');
    $limitCount = (empty($limitCount)||$limitCount<1)?0:$limitCount;
    $curUser = JFactory::getUser();

    $db = JFactory::getDbo();
    $db -> setQuery('SELECT event, http_status_text, log_type, summary,event_status,entity_type, create_time FROM  `asg_logs` WHERE org_id ='.$orgID.' or (uid = '.$curUser->id.' and event_status in ("Error","Partially Completed","Request")) ORDER BY id desc LIMIT 0,'.$limitCount);
    $result = $db->loadObjectList();
    AjaxHelper::send($result);

  }
  
    /**
     * send email to related people when a subscription created. 
     */
  public function subscriptionDidCreate() {
    $requester_uid   = $_POST["requester_uid"];
    $product_id      = $_POST["product_id"];
    $subscription_id = $_POST["subscription_id"];
    $_domain = DeveloperPortalApi::getHostUrl();
    $product_url      = $_domain.JRoute::_(Url::record($product_id));
    $subscription_url = $_domain.JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id='.$subscription_id);


    
    //send email to joomla admin/organization admin,contacter/requester
    if ($requester_uid && $product_id && $subscription_id) {
      $config = JFactory::getConfig();
      $admin_email = $config -> get('mailfrom');
      
      $user = JFactory::getUser($requester_uid);
      $user_email = $user->email;
      $email_group = array_merge(
         DeveloperPortalApi::getEmailsOfJoomlaAdmins(),
         DeveloperPortalApi::getEmailsOfOrganizationAdmin(), 
         DeveloperPortalApi::getEmailsOfOrganizationContact(),
         array($user_email)
      );
      $email_group = array_unique($email_group);
      $results = $this->_getEmailTemplateByAlias("notification_of_create_subscription");
      if ($results -> subject && $results -> content) {
        $title = $results -> subject;
        $content = $results -> content;
        $content = str_replace("{PRODUCT_URL}", $product_url, $content);
        $content = str_replace("{SUBSCRIPTION_URL}", $subscription_url, $content);
    
        DeveloperPortalApi::send_email($email_group, $title, $content, $results -> isHTML);
      }
      AjaxHelper::send(JText::_('SUBSCRIPTION_CREATE_SUCCESS'),"msg");
    }else{
      AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_4'));
    }
  }
    
    
    public function createUserGroups() {
        $app = JFactory::getApplication();
        $input= $app->input;
        $org_id = $input->get('org_id', null, 'INT');
          if(!$org_id) {
            AjaxHelper::error( JText::_('Org_id not set'));
             return "Please set Org_id";
            }
        DeveloperPortalApi::createUserGroups($org_id);
    }

    public function getUserByUid(){      
      $uid  = intval($_GET["uid"]);
      $current_user = JFactory::getUser();
      if ( !$this->userState()) {
          AjaxHelper::error( JText::_('USER_NO_LOGIN') );
      }
      elseif(!in_array(8, $current_user->getAuthorisedGroups())&&!DeveloperPortalApi::fromSameOrg($current_user->id,$uid)){
          AjaxHelper::error(JText::_('NOPERMISSION'));
      }
      if ($uid) {
        $user = &JFactory::getUser($uid);
        if ($user) {
          $result = array(
              "id"   => $user->id,              
              "name" => $user->name,              
              "username" => $user->username,              
              "email"    => $user->email              
          );
          AjaxHelper::send($result, "result");
        }else{
          AjaxHelper::error("This user does not exist.");
        }
      }else{
        AjaxHelper::error("Miss parameters in the request.");
      }
    }

    /**
     * Used for disadbling user when the related userprofile is being archived.
     * @return Boolean if unactive 
     */
    public static function disabledUser(){
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $userprofile_id = $_REQUEST['userfile_id'];
      $user_id = DeveloperPortalApi::getUserIdByProfileId($userprofile_id);
      $user = &JFactory::getUser($user_id);

      $query->update($db->quoteName('#__users'))->set($db->quoteName('block') . "=1")->where($db->quoteName('id') . '=' . $user_id);
      $db->setQuery($query);

      if($db->query()){
          AjaxHelper::send(JText::_('ENVIRONMENT_REMOVE_FROM_PRODUCT_SUCCESS'));
      }
      else
      {
          AjaxHelper::error(JText::_('ENVIRONMENT_REMOVE_FROM_PRODUCT_FAILED'));
      }
    }

    public function resendActiveEmail(){
      JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
      $app = JFactory::getApplication();
      $url = '';
      $url .= JURI::root().'index.php/component/users/?task=registration.activate&token=';
      $retunrUrl = JURI::root().'index.php/organizations';

      $res_id = $_REQUEST['id'];
      $db = JFactory::getDbo();
      $db->setQuery('select `field_value` from #__js_res_record_values where `field_id` in (77,47) and `record_id`='. (int) $res_id);
      $result = $db->loadColumn();
      if(empty($result))
    {
      $app->redirect( $retunrUrl, $msg=JText::_("RESEND_ACTIVATION_EMAIL_FAIL_NO_USER_ATTACHED"), $msgType='message');
    }
    $user_org_id=$result[1];
    $retunrUrl = JUri::base(true).'/index.php/organizations/item/'.$user_org_id;
    $db->setQuery('select `id` from #__users where `id`="'. (int) ($result[0]?$result[0]:0).'"');
    $user_id = $db->loadColumn();
    if(empty($user_id))
    {
      $app->redirect( $retunrUrl, $msg=JText::_("RESEND_ACTIVATION_EMAIL_FAIL_NO_USER_FOUND"), $msgType='message');
    }

    $user = &JFactory::getUser($user_id[0]);
    $url .= $user->get('activation');

    if(DeveloperPortalApi::resendActiveEmail($user_id[0], $url)){
        $query = $db->getQuery(true);
        $query->select($db->quoteName('params'))
            ->from($db->quoteName('#__users'))
            ->where($db->quoteName('id') . ' = ' . $result[0]);
        $db->setQuery($query);
        $userParams = json_decode($db->loadResult());
        $userParams->activation_time = date('Y-m-d H:i:s');
        $params = json_encode($userParams);
        $db->setQuery('UPDATE `#__users` SET `params` = \'' . $params . '\' WHERE `id` = ' . (int) $result[0]);
        $result = $db->execute();
        $app->redirect( $retunrUrl, $msg=JText::_("RESEND_ACTIVATION_EMAIL_SUCCESS"), $msgType='message');
    }
    else
    {
      $app->redirect( $retunrUrl, $msg=JText::_("RESEND_ACTIVATION_EMAIL_FAIL_TECHNICAL"), $msgType='message');
    }
  }
  
    public function asgLogs(){

      $db = JFactory::getDbo();
      $user = JFactory::getUser();
      $log_item = new stdClass();

      $log_item->log_type             = filter_var($_POST['log_type'], FILTER_SANITIZE_STRIPPED);
      $log_item->is_show              = 0;
      $log_item->org_id               = 0;
      $log_item->http_status          = filter_var($_POST['status'], FILTER_SANITIZE_STRIPPED);
      $log_item->http_status_text     = addslashes(filter_var($_POST['statusText'], FILTER_SANITIZE_STRIPPED));
      $log_item->http_response_text   = addslashes('');
      $log_item->summary              = addslashes(filter_var($_POST['summary'], FILTER_SANITIZE_STRIPPED));
      $log_item->content              = addslashes(filter_var($_POST['content'], FILTER_SANITIZE_STRIPPED));
      $log_item->entity_type          = filter_var($_POST['entity_type'], FILTER_SANITIZE_STRIPPED);
      $log_item->entity_id            = filter_var($_POST['entity_id'], FILTER_SANITIZE_NUMBER_INT);
      $log_item->event                = filter_var($_POST['event'], FILTER_SANITIZE_STRIPPED);
      $log_item->event_status         = filter_var($_POST['event_status'], FILTER_SANITIZE_STRIPPED);
      $log_item->uid                  = $user->id ? $user->id : 0;
      $log_item->uuid                 = filter_var($_POST['uuid'], FILTER_SANITIZE_STRIPPED);


      $db->insertObject("asg_logs",$log_item,'id') ? AjaxHelper::send("") : AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_4'));

    }

    /**
     * Archive an object by setting the "published" column to 2.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @return string A JSON string
     * update 13/11/2013 by Jacky
     */
    public function archiveRecord() {
      $flag       = true;
      $type_id    =  JRequest::getInt("type_id",0);
      $object_id  =  JRequest::getInt("rec_id",0);

      $archive_ids = $object_id?array($object_id):array();

      if($type_id == 4 && $object_id)
      {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select("record_id")->from("#__js_res_record_values")->where('field_id=16 and field_value='. (int) $object_id);
        $db->setQuery($query);
        $result = $db->loadColumn();

        $archive_ids = array_merge($archive_ids, $result);
      }

      foreach ($archive_ids as $archive_id)
      {
        if(!DeveloperPortalApi::archiveRecord($archive_id, $type_id))
        {
          $flag = false;
          break;
        }
      }

      if (!$object_id)
      {
        AjaxHelper::error("The parameter rec_id is missing.");
      }else if ($flag){
        AjaxHelper::send("success");
      }else{
        AjaxHelper::error("Record update failed.");
      }

    }

    /**
     * @Author Jacky love0.0chen@gmail.com
     * @Created 2013-10-10 17:04
     * @Updated 2013-10-10 17:04
     * Remove environment from product
     * @Params record_id the product id which we need to make sure which record we need to update
     * @Params return where we need to get back when action is failed or success
     * @Return refresh page
     */
    public function removeEnvFromProduct(){
      $app            =       JFactory::getApplication();
      $db             =       JFactory::getDbo();
      $user           =       JFactory::getUser();
      $query          =       $db->getQuery(true);

      $return_link    =       base64_decode(JRequest::getVar("return"));
      $field_id       =       JRequest::getVar("field_id");
      $record_id      =       $_REQUEST['record_id'];
      $record         =       ItemsStore::getRecord($record_id);


      if(!in_array(8, $user->getAuthorisedGroups()))
      {
          AjaxHelper::error(JText::_('ENVIRONMENT_REMOVE_FROM_PRODUCT_NOPERMISSION'));
      }

      //Change the fields value for record
      $fields         =       json_decode($record->fields);
      $fields->{'35'} =       null;
      $fields         =       json_encode($fields);
      $res->fields    =       $fields;


      //Delete attached environment from databse for the record
      $query->delete("#__js_res_record_values")->where('field_id = 34 AND field_value = '. (int) $record_id);
      $db->setQuery($query);

      //if delete failed, refesh page
      if(!$db->query())
      {
          AjaxHelper::error(JText::_('ENVIRONMENT_REMOVE_FROM_PRODUCT_FAILED'));
      }

      //Update the record fields
    $fieldsValue = str_replace('\\','\\\\',$fields);
    $fieldsValue = str_replace("'","\'",$fieldsValue);
      $query = $db->getQuery(true);
    $query->update($db->quoteName('#__js_res_record'))->set($db->quoteName('fields') . "='" .  $fieldsValue ."'")->where($db->quoteName('id') . '=' . (int) $record_id);
      $db->setQuery($query);

      if($db->query()){
          AjaxHelper::send(JText::_('ENVIRONMENT_REMOVE_FROM_PRODUCT_SUCCESS'));
      }
      else
      {
          AjaxHelper::error(JText::_('ENVIRONMENT_REMOVE_FROM_PRODUCT_FAILED'));
      }
      return false;
    }


    /**
     * field_id 66  => description
     * field_id 114 => product 179
     * field_id 69  => plan 186
     * field_id 71  => startd date
     * field_id 72  => enddate
     * field_id 73  => organization
     * field_id 78  => status
     * field_id 112 => uuid
     */
    public function insertSub()
    {

      $applications = JRequest::getVar("selected_apps","");
      if($applications){
        $applications = explode(",", $applications);
      }

      $db = JFactory::getDbo();
      $sub = new stdClass();
      $fields = new stdClass();
      $user = JFactory::getUser();
      $lang   = JFactory::getLanguage();
      


      $config =& JFactory::getConfig();
      $offset = $config->get('offset');
      
      $start_date = new JDate("now", $offset);
      $start_date = $start_date->format("Y-m-d",true);

      $end_date = new JDate("2038-01-01", $offset);
      $end_date = $end_date->format("Y-m-d",true);

      $fields->{'66'}   =   "";
      $fields->{'114'}  =   JRequest::getVar('product_id');
      $fields->{'69'}   =   JRequest::getVar('plan_id');
      $fields->{'71'}   =   array($start_date);
      $fields->{'72'}   =   array($end_date);
      $fields->{'73'}   =   DeveloperPortalApi::getUserOrganization();
      $fields->{'73'}   =   $fields->{'73'}[0];
      $fields->{'78'}   =   array("Active");
      $fields->{'112'}  =   CreateSubscriptionApi::getUuid('');

      $org_title        =   ItemsStore::getRecord($fields->{'73'})->title;
      $product_title    =   ItemsStore::getRecord($fields->{'114'})->title;
      $plan_title       =   ItemsStore::getRecord($fields->{'69'})->title;
      $title            =   $org_title.'-'.$product_title.'-'.$plan_title;

      $sub->id          =   null;
      $sub->title       =   trim($title);
      $sub->published   =   1;
      $sub->access      =   8;
      $sub->user_id     =   129;
      $sub->section_id  =   6;
      $sub->parent_id   =   0;
      $sub->ctime       =   JFactory::getDate()->toSql();
      $sub->extime      =   '';
      $sub->mtime       =   JFactory::getDate()->toSql();
      $sub->inittime    =   JFactory::getDate()->toSql();
      $sub->ftime       =   '';
      $sub->type_id     =   10;
      $sub->meta_descr  =   '';
      $sub->meta_key    =   '';
      $sub->meta_index  =   '';
      $sub->alias       =   JApplication::stringURLSafe(strip_tags($sub->title));
      $sub->featured    =   0;
      $sub->archive     =   0;
      $sub->ucatid      =   0;
      $sub->langs       =   $lang->getTag();
      $sub->ip          =   $_SERVER['REMOTE_ADDR'];
      $sub->hidden      =   0;
      $sub->access_key  =   md5(time() . $_SERVER['REMOTE_ADDR'] . $sub->title);
      $sub->fields      =   json_encode($fields);
      $sub->fieldsdata  =   $sub->title . " " . $sub->alias;
      $sub->categories  =   "[]";

     
      
      if($db->insertObject("#__js_res_record",$sub,'id'))
      {
        $record_id = CreateSubscriptionApi::getSubscription($sub->access_key);
        if($record_id){
          if(CreateSubscriptionApi::insertFields($record_id,$fields))
          {
            
            if($applications){
              if($old_subscriptions = TibcoTibco::updateApplicationForPlan($applications, $fields->{'114'}, $record_id)){
                $result = array("record_id"=>$record_id,"appIds"=>$applications,"app_old_subscriptions"=>$old_subscriptions,"msg"=>JText::_('AUTO_CREATE_SUBSCRIPTION_SUCCESS'));
              
                AjaxHelper::send($result,"result");

              }else{
                AjaxHelper::error(JText::_('AUTO_CREATE_SUBSCRIPTION_FAILED'));
              }

            }else{
              $result = array("record_id"=>$record_id, "msg"=>JText::_('AUTO_CREATE_SUBSCRIPTION_SUCCESS'));
              AjaxHelper::send($result,"result");
            }
          }
          else
          {
            AjaxHelper::error(JText::_('AUTO_CREATE_SUBSCRIPTION_FAILED'));
          }
        }
      }
    }
     /**
     * if the user login from Ping system, will build a form this user login to joomla with out password.
     * @author Owen
     */
    function answerPing(){
      $isSuccessFromPing = $_SESSION["isSuccessFromPing"];
      if ($isSuccessFromPing) {
        $email      = $_REQUEST["email"];
        $org_name   = $_REQUEST["org_name"];
        $loginBackUrl = JURI::root() . 'index.php?option=com_cobalt&task=ajaxmore.createPingUserProfile&org_name='.$org_name;
        
        $str = '<form action="'.JURI::root().'" method="post" style="display:none;">'."\n";
        $str.= '<input type="text" name="username" value="'.$email.'" />'."\n";
        $str.= '<input type="password" name="password" value="'.$email.'" />'."\n";
        $str.= '<input type="hidden" name="option" value="com_users" />'."\n";
        $str.= '<input type="hidden" name="task" value="user.login" />'."\n";
        $str.= '<input type="hidden" name="isSuccessFromPing" value="'.$isSuccessFromPing.'" />'."\n";
        $str.= '<input type="hidden" value="'.base64_encode($loginBackUrl).'" name="return">'."\n";
        $str.= '<button id="ping_login_submit" type="submit" name="Submit">Sign in</button>'."\n";
        $str.= JHtml::_('form.token')."\n";  
        $str.= '</form>'."\n";
        $str.= '<script type="text/javascript">'."\n";
        $str.= "window.onload=function(){ document.getElementById('ping_login_submit').click(); } \n";
        $str.= '</script>'."\n";
        echo $str;
        exit;
      }else{
        $app = JFactory::getApplication();
        $app->redirect('index.php/component/users/?view=login', JText::_("JGLOBAL_AUTH_FAIL"));
      }
    }
    /**
     * create user profile for Ping user.
     * if user does not have cobalt user profile
     *   1. will get or create an organization for this user.
     *   2. will create cobalt user profile and bind to the created organization.
     *   3. re-login. Because of the user need to active the organization permission in browser session.
     * @author Owen
     */
    function createPingUserProfile(){
      $org_name   = $_REQUEST["org_name"];
      $user = JFactory::getUser();
      $user_id = $user->get('id');
      
      if (!DeveloperPortalApi::getUserProfileId()) {
        $options = array(
            "title" => $org_name,
        );
        $org_id = TibcoTibco::forceGetOrganizationId($options);
        $options = array(
            "org_id" => $org_id,
        );
        TibcoTibco::createUserProfile($options);
        
        $app = JFactory::getApplication();
        $app->logout($user_id, array());
        
        header("Location:".JURI::root().'index.php?option=com_cobalt&task=ajaxmore.answerPing&email='.$user->email);exit;
      }
      header("Location:".JURI::root());exit;
    }

    function attachUserToGroup()
    {
      
      $user_id = JRequest::getVar("userId",0);


      $org_name = JRequest::getVar("jform",array());
      $org_name = $org_name['user_group_name'];
      if(!$user_id || !$org_name)
      {
        AjaxHelper::error(JText::_('ATTACH_USER_TO_ORGANIZATION_NO_USER_ORGANIZATION'));
      }

      $user = JFactory::getUser($user_id);

      $group_id = DeveloperPortalApi::getOrganizationIdByName($org_name);

      if (!$group_id)
      {
        AjaxHelper::error(JText::_("ATTACH_USER_NO_ORGANIZATION_FOUND"));
        
      }else if(in_array($group_id, $user->getAuthorisedGroups())){

        AjaxHelper::error(JText::_('ATTACH_USER_TO_ORGANIZATION_FAILED1'));

      }else{
        $db = JFactory::getDbo();
        $sql = 'select record_id from #__js_res_record_values where field_value='. (int)$user_id;
        $db->setQuery($sql);
        if ($result = $db -> loadObjectList()) {
          foreach($result as $record) {
            $db->setQuery('delete from #__js_res_record where id='.(int)$record->record_id);
            $db -> execute();
            $db->setQuery('delete from #__js_res_record_values where record_id='.(int)$record->record_id);
            $db -> execute();
          }
        }
        if(in_array(12, $user->getAuthorisedGroups())) { // User has already joined an organization.
          $db->setQuery('select * from #__user_usergroup_map where user_id='.(int)$user_id);
          if ($result = $db -> loadObjectList()) {
            foreach($result as $record) {
              if ($record->group_id > 12) {
                $db->setQuery('update #__user_usergroup_map set group_id='.(int)$group_id.' where user_id='.(int)$user_id.' and group_id='.(int)$record->group_id);
                if($db -> execute()) {
                  AjaxHelper::send("");
                } else {
                  AjaxHelper::error(JText::_('ATTACH_USER_TO_ORGANIZATION_FAILED1'));
                }
              }
            }
          }
        } else {
          $db->setQuery('insert into #__user_usergroup_map values ('.(int)$user_id.','.(int)$group_id.')');
          if($db -> execute()) {
            AjaxHelper::send("");
          } else {
            AjaxHelper::error(JText::_('ATTACH_USER_TO_ORGANIZATION_FAILED1'));
          }
        }
      }
    }
    
    

    /**

     * update joomla user group for a user when his userprofile user group get's updated.

     *

     * @author Sagar

     */

    function updateUsersGroup()

    {

    

      $user_id = JRequest::getVar("userId",0);

      $org_name = JRequest::getVar("jform",array());

      $org_name = $org_name['user_group_name'];

      $usergrouptoupdate = JRequest::getVar("jform",array());

      $usergrouptoupdate = $usergrouptoupdate['old_user_group_name'];

       

       

      if(!$user_id || !$org_name)

      {

        AjaxHelper::error(JText::_('ATTACH_USER_TO_ORGANIZATION_NO_USER_ORGANIZATION'));

      }

    

      $user = JFactory::getUser($user_id);

    

      $old_group_id = DeveloperPortalApi::getOrganizationIdByName($usergrouptoupdate);

      $group_id = DeveloperPortalApi::getOrganizationIdByName($org_name);

       

      if (!$group_id)

      {

        AjaxHelper::error(JText::_("ATTACH_USER_NO_ORGANIZATION_FOUND"));

    

      }

       

      if(in_array(12, $user->getAuthorisedGroups())) { // if User belongs to partner

        $db = JFactory::getDbo();

        $db->setQuery('select * from #__user_usergroup_map where user_id='.(int)$user_id.' and group_id='.(int)$old_group_id);

        if ($result = $db -> loadObjectList()) {

    

          foreach($result as $record) {

            if ($record->group_id > 12) {

              $db->setQuery('update #__user_usergroup_map set group_id='.(int)$group_id.' where user_id='.(int)$user_id.' and group_id='.(int)$record->group_id);

              if($db -> execute()) {

                AjaxHelper::send("success",'result');

              } else {

                AjaxHelper::error(JText::_('ATTACH_USER_TO_ORGANIZATION_FAILED1'));

              }

            }

          }

        }else {

          AjaxHelper::error(JText::_('USERGROUP_OUTOFSYNC'));

        }

      }

    }
    
    /**
     * send email when an organization has passed their alert threshold value
     * Example: /index.php?option=com_cobalt&task=ajaxmore.noticeAPILimitUsageThreshold&orgId=220&subId=238&usedPercentage=75
     * @author Owen
     */
    public function noticeAPILimitUsageThreshold() {
      $org_id  = $_REQUEST["orgId"];
      $sub_id  = $_REQUEST["subId"];
      $usedPercentage  = $_REQUEST["usedPercentage"];
      $_domain = DeveloperPortalApi::getHostUrl();

      $config = JFactory::getConfig();
      if ($org_id && $sub_id) {
        $sub_url          = $_domain.JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id='.$sub_id);
        $organization_url = $_domain.JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id='.$org_id);
//         $organization     = TibcoTibco::getOrganizationDetailById($org_id);
//         $organization_percentage = $organization[0];
        
        $results = $this->_getEmailTemplateByAlias("api_limit_usage_threshold");
        if ($results -> subject && $results -> content) {
          $title = $results -> subject;
          $content = $results -> content;
          $title = str_replace("{SUBSCRIPTION_URL}", $sub_url, $title);
          $content = str_replace("{ORGANIZATION_URL}", $organization_url, $content);
          $content = str_replace("{SUBSCRIPTION_URL}", $sub_url, $content);
          $content = str_replace("{USED_PERCENTAGE}", $usedPercentage, $content);
  
          $org_admin_email_group = array_merge(DeveloperPortalApi::getEmailsOfOrganizationAdmin($org_id), DeveloperPortalApi::getEmailsOfOrganizationContact($org_id));
          if (DeveloperPortalApi::send_email($org_admin_email_group, $title, $content, $results -> isHTML)) {
            $result = array(
              'mail_to_list' => $org_admin_email_group,                
              'mail_content' => $content,                
            );
            AjaxHelper::send($result, "result");
          }else{
            AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_2'));
          }
        }
      }else{
        AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_4'));
      }
    }

        /**
     * get enabled subscriptions' product&plan in specific application. 
     */
  public function getDashboardData() {
   $app = JFactory::getApplication();
   $input= $app->input;
   $filteredData = $input->getArray(array(
         "app_ids" => 'int',
         "sub_ids" => 'int',
         "plan_ids" => 'int',
       ));

   $app_ids = $filteredData["app_ids"];
   $sub_ids = $filteredData["sub_ids"];
   $plan_ids = $filteredData["plan_ids"];


    $counter = array();
    if (!empty($app_ids)) {
      $counter = $app_ids;
    }else if(!empty($sub_ids)){
      $counter = array(1);
    }else{
      AjaxHelper::error("");
    }
    $returnValue = array();

    foreach ($counter as $app_id) {

      if (!empty($app_ids)) {
        $records = DeveloperPortalApi::subscriptionsInApplication($app_id);
      }
      else{
              $records = $sub_ids;
              if (count($records)>0) {
              $results = array();
              $db = JFactory::getDbo();
              ArrayHelper::toInteger($records);

              /*
              $sql = 'select title as productName from `#__js_res_record` where id in (SELECT field_value FROM `#__js_res_record_values` WHERE field_id=114 and record_id in ('.implode(',',$records).'))';
              $db -> setQuery($sql);
              $results['products'] = $db->loadObjectList();
              */
              $sql = 'select fields as planDetail from `#__js_res_record` where id in (SELECT field_value FROM `#__js_res_record_values` WHERE field_id=69 and record_id in ('.implode(',',$records).'))';
              if (!empty($plan_ids)) {
                  ArrayHelper::toInteger($plan_ids);
                $sql = 'select id,title,fields as planDetail from `#__js_res_record` where id in ('.implode(',',$plan_ids).')';
              }
              $db -> setQuery($sql);
              $results['plans'] = $db->loadObjectList(); 
              $sql = 'select id,current_usage,pct from (select id from #__js_res_record where id in ('.implode(',',$records).')) a left join (SELECT subscription_id, current_usage, pct FROM  `asg_subscription_usage` WHERE subscription_id in ('.implode(',',$records).')) b on a.id=b.subscription_id';
              $db -> setQuery($sql);
              $results['usage'] = $db->loadObjectList();
              $returnValue[] = $results;
              }else {
                $returnValue[] = array();
              }

               AjaxHelper::send($returnValue);
      }
      

      if (count($records)>0) {

              $results = array();
              $db = JFactory::getDbo();
              ArrayHelper::toInteger($records);

              //Use API instead of a SQL call
              $results['products'] = DeveloperPortalApi::getProductsInApplication($app_id);
              $results['product_ids'] = DeveloperPortalApi::getProductIdsInApplication($app_id);
              $results['plans'] = DeveloperPortalApi::getPlanModel();
              $results['application_subscriptions'] = DeveloperPortalApi::getApplicationSubscriptions($app_id);
              $results['subscriptions'] = DeveloperPortalApi::subscriptionsInApplication($app_id);
             
              $sql = 'select id,current_usage,pct from (select id from #__js_res_record where id in ('.implode(',',$records).')) a left join (SELECT subscription_id, current_usage, pct FROM  `asg_subscription_usage` WHERE subscription_id in ('.implode(',',$records).')) b on a.id=b.subscription_id';
              $db -> setQuery($sql);
              $results['usage'] = $db->loadObjectList();
              $returnValue[] = $results;
         }

          else {
            
            $returnValue[] = array();

          }
    }
    
    AjaxHelper::send($returnValue);
  }
  


    /**
     * send email when they have completely used up their quota.
     * Example: /index.php?option=com_cobalt&task=ajaxmore.noticeAPILimitUsageFull&orgId=220&subId=238
     * @author Owen
     */
    public function noticeAPILimitUsageFull() {
      $org_id  = $_REQUEST["orgId"];
      $sub_id  = $_REQUEST["subId"];
      $_domain = DeveloperPortalApi::getHostUrl();
      
      $config = JFactory::getConfig();
      if ($org_id && $sub_id) {
        $sub_url          = $_domain.JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id='.$sub_id);
        $organization_url = $_domain.JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id='.$org_id);
//         $organization     = TibcoTibco::getOrganizationDetailById($org_id);
//         $organization_percentage = $organization[0];
    
        $results = $this->_getEmailTemplateByAlias("api_limit_usage_full");
        if ($results -> subject && $results -> content) {
          $title = $results -> subject;
          $content = $results -> content;
          $title = str_replace("{SUBSCRIPTION_URL}", $sub_url, $title);
          $content = str_replace("{ORGANIZATION_URL}", $organization_url, $content);
          $content = str_replace("{SUBSCRIPTION_URL}", $sub_url, $content);
    
          $org_admin_email_group = array_merge(DeveloperPortalApi::getEmailsOfOrganizationAdmin($org_id), DeveloperPortalApi::getEmailsOfOrganizationContact($org_id));
          if (DeveloperPortalApi::send_email($org_admin_email_group, $title, $content, $results -> isHTML)) {
            $result = array(
              'mail_to_list' => $org_admin_email_group,                
              'mail_content' => $content,                
            );
            AjaxHelper::send($result, "result");
          }else{
            AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_2'));
          }
        }
      }else{
        AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_4'));
      }
    }
    /**
     * change user's password
     * Example: /index.php?option=com_cobalt&task=ajaxmore.changePassword&old_password=charper&new_password=charper_new
     * @author Owen
     */
    public function changePassword() {
      $old_password  = $_REQUEST["old_password"];
      $new_password  = $_REQUEST["new_password"];
    
      $user = JFactory::getUser($user_id);

      if ($old_password && $new_password) {
        // below confirm password logic is as same as "plugins/authentication/joomla/joomla.php, onUserAuthenticate()" 
        $db   = JFactory::getDbo();
        $query  = $db->getQuery(true)
        ->select('id, password')
        ->from('#__users')
        ->where('id=' . $user->id);
        
        $db->setQuery($query);
        $result = $db->loadObject();
        
        if ($result) {
          //if the old password is correct.
         if (JUserHelper::verifyPassword($old_password,$result->password)){
            $new_password_status = $this->checkPasswordRules($new_password);
            if ( $new_password_status == 1 ) {
                // Generate the new password hash.
                $crypted = JUserHelper::hashPassword($new_password);
                $new_password_hash = $crypted;
                $sql = 'UPDATE #__users SET password="'.$new_password_hash.'" WHERE id='.(int)$user->id;
                $db->setQuery($sql);
                $db -> execute();
                AjaxHelper::send("");
            }else{
                AjaxHelper::error($new_password_status);
            }
          }else{
            AjaxHelper::error(JText::_('ERROR_CHANGE_PASSWORD'));
          }
        }else{
          AjaxHelper::error(JText::_('ERROR_CHANGE_PASSWORD'));
        }
      }else{
        AjaxHelper::error(JText::_('EMAIL_RETURN_NOTES_4'));
      }
    }

    public function approvepublish() {
      $product_id  = $_REQUEST["record_id"];
      $flag = (boolean) $_REQUEST["is_to_show"];
      $flag = (int) !$flag;

      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->update($db->quoteName('asg_product_show_map'))->set($db->quoteName('is_show') . '="' . $flag . '"')->where($db->quoteName('product_id') . '=' . (int)$product_id);
      $db->setQuery($query);
      $flag = $db->query();
      if($flag){
        AjaxHelper::send("");
      }else{
        AjaxHelper::error(JText::_('failed!'));
      }
    }

    public function checkvalidate() {
      if(JRequest::getVar("code",'') == $_SESSION['code']){
        AjaxHelper::send("");
      }else{
        AjaxHelper::error(JText::_(''));
      }
      
    }

    public function checkEnvironmentsUsedByProduct(){
       $origEnvs = explode(",", JRequest::getVar('origEnvs', ''));
       $currEnvs = explode(",", JRequest::getVar('currEnvs', ''));
       $record_id = JRequest::getVar('record_id', 0);


       $diff = array_intersect($origEnvs,$currEnvs);
       $deletedEnvs = array();


       if(count($diff)<count($origEnvs))
       {
        $deletedEnvs = array_diff($origEnvs, $diff);
       }

       $db = JFactory::getDbo();
       $query = $db->getQuery(true);

       $query->select("e.field_value AS product_id, e.record_id AS environment_id")->from("#__js_res_record_values AS e")
             ->where("e.record_id IN (" . implode(",", $deletedEnvs) . ")")
             ->where("e.field_id = 34")
             ->where('e.field_value IN ( SELECT a.field_value from #__js_res_record_values AS a where a.field_id = 6 and a.record_id = ' . (int) $record_id . ')' );

      $db->setQuery($query);

      $result = $db->loadObjectList();

      if(count($result)){
        $errorMsg = array();
        foreach ($result as $key => $envInProduct) {
          $errMsg = JText::sprintf("ENVIRONMENT_USED_BY_THE_PRODUCT",ItemsStore::getRecord($envInProduct->environment_id)->title,ItemsStore::getRecord($envInProduct->product_id)->title);    
          $errorMsg[] = $errMsg;
        }
        AjaxHelper::error(implode("<br/>", $errorMsg));
      }else{
        AjaxHelper::send("");
      }       
    }

    public function resetWorkThrough(){
      $user = JFactory::getUser();
      $user_id = $user->id;
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $app = JFactory::getApplication();
      // $return = $_POST['return'];
      $return = JRequest::getVar("return",base64_encode(JRoute::_("index.php")),"post");
      $return = base64_decode($return);


      $query->delete()->from("#__user_profiles")->where($db->quoteName('user_id') . '=' . (int)$user_id . ' and ' . $db->quoteName('profile_key') . 'IN (' . $db->quote("guide.show") . " , " . $db->quote("guide.step") . ")");

      $db->setQuery($query);
      $db->query();

      $userProfile_guide_flag = new stdClass();
      $userProfile_guide_flag->user_id = $user_id;
      $userProfile_guide_flag->profile_key = "guide.show";
      $userProfile_guide_flag->profile_value = 1;

      $userProfile_guide_step = new stdClass();
      $userProfile_guide_step->user_id = $user_id;
      $userProfile_guide_step->profile_key = "guide.step";
      $userProfile_guide_step->profile_value = 1;

      $db->insertObject("#__user_profiles",$userProfile_guide_flag,'id');
      $db->insertObject("#__user_profiles",$userProfile_guide_step,'id');

      if($db->getErrorNum())
      {
        $app->redirect($return, JText::_('RESET_WORK_THROUGH_FAILED'));
      }else{
       $cookieName =  md5(strtotime($user->registerDate).$user->id."_guide_step");
        if (isset($_COOKIE[$cookieName]))
        {
          unset($_COOKIE[$cookieName]);
          setcookie($cookieName, "", time()-3600, '/');
        }
         
        $app->redirect($return, JText::_('RESET_WORK_THROUGH_SUCCESSFULLY'));
      }
    }
  
  public function addUserToGroup(){
          $org_id = $_POST['org_id']*1;
          $user_email = $_POST['user_email'];
      $user_type = empty($_POST['user_type']) ? 'Member' : ucwords(strtolower($_POST['user_type']));
      $group_name = 'Organization '.$org_id.' '.$user_type;
          $db = JFactory::getDbo();
          //get user id
           $userIdSql = "SELECT * FROM `#__users` WHERE `email` LIKE ".$db->quote($user_email)." ORDER BY id DESC";
          $db->setQuery($userIdSql);
          $userObj = $db->loadObject();
          $user_id = $userObj->id;
        
          //get group id
          $getGroupIdSql = 'SELECT `id` FROM `#__usergroups` WHERE title = '.$db->quote($group_name).' LIMIT 1';
          $db->setQuery($getGroupIdSql);
          $groupObj = $db->loadObject();
          $group_id = $groupObj->id;
          
          //insert group map
          $insertSql = 'INSERT INTO `#__user_usergroup_map` (`user_id`, `group_id`) VALUES ("'.$user_id.'","'.$group_id.'")';
          $db->setQuery($insertSql);
          $result = $db->execute();
          return $result;
  }
    /*background function use validate password rules*/
    private function checkPasswordRules( $passwd ) {
      $resArr = TibcoTibco::validatePassword( $passwd );
        if ( $resArr['success'] == 0 ) {
          $msg = $this->getErrorMsg($resArr['errno']);
          return $msg;
        } else {
          return 1;
        }
    }
    /*javascript use validate password rules*/
    public function validatePasswordRules(){
      $passwd = $_POST['password'];
      $check = 0;
      $resArr = TibcoTibco::validatePassword( $passwd );
    if ( $resArr['success'] == 0 ) {
      $msg = $this->getErrorMsg($resArr['errno']);
      $check = 1;
    }
    if ($check == 1)
      AjaxHelper::error($msg);
      else 
        AjaxHelper::send('');
    }
    
    public function validateOldPassword(){
        $email = $_POST['email'];
        $old_password = $_POST['oldPassword'];
        if ( !empty( $email ) && !empty( $old_password ) ) {
            $db = JFactory::getDbo();
            $sql = "SELECT `password` FROM `#__users` WHERE `email` = '".$db->quote($email)."' ";
            $db->setQuery( $sql );
            $obj = $db->loadObject();
            if ( JUserHelper::verifyPassword( $old_password, $obj->password ) ) {
                AjaxHelper::send('');
            }
            AjaxHelper::error( JText::_("OLD_PASSWORD_ERROR") );
        } elseif ( empty( $old_password ) ) {
            AjaxHelper::error( JText::_("OLD_PASSWORD_NULL") );
        }
        AjaxHelper::error( JText::_("OLD_PASSWORD_UNKNOW_ERROR") );
    }
    
    /*return error messages.*/
    private function getErrorMsg($errno) {
      $msg = '';
      if( isset($errno['len']) && !empty($errno['len']) ) {
      $msg .= sprintf(JText::_("COM_AJAXMORE_LEN"),$errno['len']).'</br>';
    }
    if( isset($errno['int']) && !empty($errno['int']) ) {
      $msg .= sprintf(JText::_("COM_AJAXMORE_INT"),$errno['int']).'</br>';
    }
    if( isset($errno['upp']) && !empty($errno['upp']) ) {
      $msg .= sprintf(JText::_("COM_AJAXMORE_UPP"),$errno['upp']).'</br>';
    }
    if( isset($errno['sym']) && !empty($errno['sym']) ) {
      $msg .= sprintf(JText::_("COM_AJAXMORE_SYM"),$errno['sym']).'</br>';
    }
    return $msg;
    }

    public function getFormToken() {
      AjaxHelper::send(JFactory::getSession()->getFormToken());
    }
    /**
     * Check user login or no-login.
    */
    private function userState() {
        $user = JFactory::getUser();
        if ( isset( $user->id ) && !empty( $user->id ) ) {
            return true;
        }   
        return false;
    }

    /**
     * Save a policy to database
     */
    public function savePolicy(){
        $res = 0;
        $user = JFactory::getUser();
        if ( in_array( 8, $user->groups ) ) {
            $db = JFactory::getDbo();
            $policy = new stdClass();
            $policy->field_value = addslashes( strip_tags( preg_replace("'([\r\n])[\s]+'", "", $_POST['policy']) ) );
            $policy->record_id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $policy->user = JFactory::getUser();
            $policy->field_id = 151;
            $policy->field_key = 'k' . md5($field->label . '-' . $field->type);
            $policy->field_type = 'textarea';
            $policy->field_label = 'Policy';
            $policy->user_id = $user->id;
            $policy->type_id = 6;
            $policy->section_id = 2;
            $policy->category_id = 0;
            $policy->ctime = date( 'Y-m-d H:i:s', time() );
            $policy->value_index = 0;
            $policy->ip = $_SERVER['REMOTE_ADDR'];
            if ( !empty( $policy->field_value ) && !empty( $_POST['id'] ) ) {
                if ( $db->insertObject("#__js_res_record_values",$policy,'id') ) {
                    $res = $db->insertid();
                }
            }
        }
        return AjaxHelper::send( $res );
    }
    /**
    * Parsing a wsdl file.
    * @return Json string.
    */
    public function soapClient() {
        $resArr = array();
        if (!empty($_REQUEST['filename'])) {
            $apiID = $_REQUEST['apiID'];
            $wsdl_subfolder = TibcoTibco::getWSDLSubfolder();
            $time = substr($_REQUEST['filename'], 0, stripos($_REQUEST['filename'], '_'));
            $dirname = date('Y-m/', $time) . $_REQUEST['filename'];
            $handle = fopen(JPATH_BASE . "/uploads/$wsdl_subfolder/wsdlName.json", 'w');
            fwrite($handle, $dirname);
            fclose($handle);
            $uri = JUri::base().'soapServer.php?WSDL';
            $client = new SoapClient($uri, array('cache_wsdl' => 0));
            $funcsArr = $client->__getFunctions();
            $soapActionArr = $this->getSoapAction(JPATH_BASE . "/uploads/$wsdl_subfolder/".$dirname);
            $db = JFactory::getDbo();
            foreach ($funcsArr as $idx => $func) {
                $name = rtrim( substr( $func, stripos( $func, ' ' ) + 1), ')');
                $arr = explode('(', $name);
                $arr[1] = explode(',', $arr[1]);
                $document = $this->getSoapDocument( JPATH_BASE . "/uploads/$wsdl_subfolder/".$dirname, $arr[0] );
                if ( empty($document) ) { $document = 'No description for '.$arr[0]; }
                $resArr['apis'][] = $this->getDetails($arr, $soapActionArr[$idx], $document);
                if ( !empty( $apiID ) )
                    $this->checkOldOperation($arr[0], $apiID);
            }
        }
        AjaxHelper::send($resArr);
    }
    /**
    * The fields of the function.
    * @return array of the function.
    */
    private function getDetails($arr, $soapAction, $document) {
        $resArr = array(
            'path' => $soapAction,
            "description" => $document,
            'operations' => array(
                array(
                    'method' => 'POST',
                    'timeout' => 10000,
                    'summary' => "",
                    "produces" => array("application/xml"),
                    'nickname' => $arr[0],
                    'parameters' => array(
                        "name" => "Title",
                        "description" => "Request Payload",
                        "paramType" => "body",
                        "required" => true,
                        "allowMultiple" => false,
                        "dataType" => "String"
                    )
                )
            )
        );
        return $resArr;
    }
    private function getSoapDocument( $filename, $fname ){
        $content = '';
        $doc = new DOMDocument();
        $doc->load($filename);
        $xml = $doc->saveXMl();
        $start = strpos($xml,'operation name="'.$fname.'"');
        $end = strrpos($xml,'operation name="'.$fname.'"');
        $matches = substr($xml,$start,$end-$start);
        $startT = strpos($matches,'<wsdl11:documentation>');
        $endT = strpos($matches,'</wsdl11:documentation>');
        if ($startT)
            $content = str_replace('<wsdl11:documentation>','',substr($matches,$startT,$endT-$startT));
        return $content;
    }
    private function getSoapAction( $filename ) {
        $doc = new DOMDocument();
        $doc->load($filename);
        $xml = $doc->saveXMl();
        preg_match_all('/soapAction=.*/',$xml, $matches );
        $saArr = array();
        foreach( $matches[0] as $key => $val ) {
           $start=strpos($val,'soapAction=')+12;
           $saArr[$key] = rtrim(rtrim( trim(substr( $val, $start, strpos($val,'style="document')-$start)), '"' ), "'");
        }
        return $saArr;
    }
    /**
     * check and delete old operation.
     */
    private function checkOldOperation( $title, $apiID ){
        $db = JFactory::getDbo();
        $sql = 'SELECT `record_id` FROM `#__js_res_record_values` WHERE `field_id` = 30 and `field_value` = '.(int)$apiID;
        $db->setQuery($sql);
        $operation = $db->loadObjectList();
        foreach ( $operation as $opt ) {
            if ( !empty( $opt->record_id ) ) {
                $sql2 = 'SELECT `title` FROM `#__js_res_record` WHERE `id` = '.(int)$opt->record_id;
                $db->setQuery($sql2);
                $record = $db->loadObject();
                if ( !empty ( $record->title ) && $record->title == $title ) {
                    $sql3 = 'DELETE FROM `#__js_res_record` WHERE `id` = '.(int)$opt->record_id;
                    $db->setQuery($sql3);
                    $db->execute();
                    $sql4 = 'DELETE FROM `#__js_res_record_values` WHERE `record_id` = '.(int) $opt->record_id;
                    $db->setQuery($sql4);
                    $db->execute();
                }
            }
        }
        return true;
    }

    /**
     * Delete a record.
     * 
     * @author Vivian Ma<xima@tibco-support.com> 
     * @return string A JSON string
     * update 3/7/2015 by Vivian
     */
    public function deleteRecord(){
        $app  = JFactory::getApplication();
        $id = $app->input->get('id');
        $type_id = $app->input->get('nTypeId');
        $result = TibcoTibco::deleteRecord($id,$type_id);
        if($result){
          AjaxHelper::send("success");
        }else{
          AjaxHelper::error("error");
        }
    }

    /**
     * Find the WSDL file of an API according to the parameters and replace the value of all "location" properties of
     * the "address" elements with the base path of the facade environment of that API.
     *
     * If everything goes fine the user will be prompted with a file download dialog. Otherwise, the user will be
     * redirected to the API's detail page with an error shown at the top of the page.
     *
     * @author Kevin Li<huali@tibco-support.com>
     */
    public function getFacadeWSDL() {

        $file_id = $_REQUEST['file_id'];

        $app = JFactory::getApplication();

        if($file_id > 0) {

            $file_record = TibcoTibco::getFileById($file_id);

            if(file_record != null) {

                $file_contents = TibcoTibco::replaceLocationsInWSDL($file_record);

                if (strlen($file_contents) > 0) {

                    header('Cache-Control: no-cache, must-revalidate');
                    header('Content-Disposition: attachment; filename="' . TibcoTibco::insertFacadeInFileName($file_record->realname) . '"');
                    header('Content-Encoding: none');
                    header('Content-Length: ' . strlen($file_contents));
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Type: application/octet-stream', true);
                    header('Pragma: no-cache');

                    echo $file_contents;

                    $app->close();

                } else {

                    $app->enqueueMessage("No contents in the file.", 'error');
                    $app->redirect($_SERVER['HTTP_REFERER']);
                }
            } else {

                $app->enqueueMessage("No file record found in the database.", 'error');
                $app->redirect($_SERVER['HTTP_REFERER']);
            }

        } else {

            $app->enqueueMessage("Parameters missing.", 'error');
            $app->redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function copyMapping(){
        $user = JFactory::getUser();
        if ( $user->id != 129 ){
            AjaxHelper::error( JText::_("MAPPING_USER_VALIDATE") );
        }
        $model = new CobaltBModelRecord();
        $ids[]   = $_POST['ids'];
        $title = $_POST['title'];
        $copyTitle = $_POST['copyTitle'];
        if (empty($ids)) {
            AjaxHelper::error( JText::_("MAPPING_ID_EMPTY") );
        }
        else {
            $db = JFactory::getDbo();
            $selectSql = 'SELECT `id` FROM `#__js_res_record` WHERE `title` = "'.$db->quote($copyTitle).'"';
            $db->setQuery($selectSql);
            $res = $db->loadObject();
            if ( $res->id ){
                AjaxHelper::error( JText::_("MAPPING_NAME_EXIST") );
            }
            if (!$model->copy($ids)) {
                AjaxHelper::error($model->getError());
            }

            $updateSql = 'UPDATE `#__js_res_record` SET `title` = \''. $db->quote($copyTitle).'\' WHERE `title` = \''.$db->quote($title).'\'';
            $db->setQuery($updateSql);
            $db->execute();
        }
        AjaxHelper::send("success");
    }

    public function getAllswaggerJson(){
        $db = JFactory::getDbo();
        $product_id = (int) $_POST['id'];
        $sql = 'SELECT `fields`FROM `#__js_res_record` WHERE `id` = '.$product_id;
        $db->setQuery($sql);
        $data = $db->loadObject();
        $fields = json_decode($data->fields);
        $ids = implode( ',', $fields->{7} );
        $sql2 = 'SELECT `realname`, `filename`, `ctime` FROM `#__js_res_files` WHERE `record_id` IN (SELECT id from `#__js_res_record` WHERE id IN ('.$ids.') AND published = 1) AND `saved` = 1';
        //$sql2 = 'SELECT `realname`, `filename`, `ctime` FROM `#__js_res_files` WHERE `record_id` IN ('.$ids.') AND `saved` = 1';
        $db->setQuery($sql2);
        $swaggers = $db->loadObjectList();
        AjaxHelper::send($swaggers);
    }

    /**
     * Check delete status from portal engine.
     * if success, call deleteRecord function.
     * If failed, show error on portal, send email to administrator, and set 'published' as "1" to show the archived objects.
     *
     * Example: /index.php?option=com_cobalt&task=ajaxmore.deleteObject&status=success&objectType=API&objectId=301
     * @author Crystal Liu<yunliu@tibco-support.com>
     */
    public function deleteObject(){
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $object_id = $_REQUEST['objectId'];
        $object_type = $_REQUEST['objectType'];
        $sql = 'SELECT title,type_id FROM `#__js_res_record` WHERE id='. (int)$object_id.'';
        $db->setQuery($sql);
        $result = $db->loadObject();
        $type_id = $result->type_id;
        $type_title = $result->title;
        $user = JFactory::getUser();
        $uuid = CreateSubscriptionApi::getUuid('');

        if($_REQUEST['status'] == "success"){
            $app->input->set('id', $object_id);
            $result = TibcoTibco::deleteRecord($object_id,$type_id);
            if($result){
                AjaxHelper::send("success");
            }else{
                AjaxHelper::error("error");
            }
        }else{

            $email_template = $this->_getEmailTemplateByAlias("delete_objects_failed_notify_admin_of_joomla");

            if ($email_template -> subject && $email_template -> content) {
                $title = $email_template -> subject;
                $content = $email_template -> content;
                $content = str_replace("{USERNAME}", $user->username, $content);
                $content = str_replace("{USER_ID}", $user->id, $content);
                $content = str_replace("{OBJECT_TYPE}", $object_type, $content);
                $content = str_replace("{OBJECT_TITLE}", $type_title, $content);
                $content = str_replace("{OBJECT_ID}", $object_id, $content);
                $content = str_replace("{UUID}", $uuid, $content);

                $admin_email_group = DeveloperPortalApi::getEmailsOfJoomlaAdmins();
                DeveloperPortalApi::send_email($admin_email_group, $title, $content, $email_template->isHTML);
            }

            $_POST['log_type'] = "error";
            $_POST['is_show'] = 0;
            $_POST['org_id'] = 0;
            $_POST['summary'] = $email_template -> subject;
            $_POST['content'] = $email_template -> content;
            $_POST['event'] = "delete";
            $_POST['event_status'] = "second phase error";
            $_POST['entity_type'] = $object_type;
            $_POST['entity_id'] = $object_id;
            $_POST['uid'] = $user->id ? $user->id : 0;
            $_POST['uuid'] = $uuid;

            $app->enqueueMessage(JText::_("PORTAL_UNREACHABLE_ERROR_MESSAGE").$uuid);
            $this->asgLogs();
        }
    }
}
?>
