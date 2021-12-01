<?php

/**
 * @version     1.0.0
 * @package     com_request
 * @copyright
 * @license
 * @author      burtyu <ybt7755221@sohu.com> - http://burtyu.com
 */
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_BASE . "/includes/api.php";
require_once JPATH_BASE . "/includes/subcreate.php";

/**
 * Lists list controller class.
 */
class RequestControllerLists extends RequestController {

    public $statusArr = array(1 => 'Pending', 2 => 'Approved', 3 => 'Rejected', 4 => 'Cancelled');

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function &getModel($name = 'Lists', $prefix = 'RequestModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    /**
     * change Operation
     */
    public function changeOperation() {
        if (isset($_POST['jform'])) {
        	$resArr = '';
            $is_email = 0;
            $state = $_POST['jform']['status'];
            $plan_type = $_POST['jform']['plan_type'];
            $id = $_POST['jform']['id'] * 1;
            if ( isset($_POST['jform']['admin_note']) && !empty($_POST['jform']['admin_note']) )
                $admin_note = addslashes($_POST['jform']['admin_note']);
            else
                $admin_note = 'null';
            $db = JFactory::getDbo();
            /* send email and insert asg_logs and get custom */
            $selectSql = 'SELECT `created_by`, `plan_id`, `plan`, `product_id`, `subscriptions_id`, `product`, `org_id`, `custom` FROM `#__request_list` WHERE `id` = ' . $id;
            $db->setQuery($selectSql);
            $planObj = $db->loadObject();
            $sub_id = $planObj->subscriptions_id;
            $plan_id = $planObj->plan_id;
            /* send email and insert asg_logs and get custom */
            $customArr = json_decode($planObj->custom);
            $customArr->subscription_start = $_POST['jform']['start_time'];
            $customArr->subscription_end = $_POST['jform']['end_time'];
            $is_email = $customArr->is_email;
            $qlimit = $customArr->qlimit;
            $rlimit = $customArr->rlimit;
            $climit = $customArr->climit;
            $applications = $customArr->application;
            if( isset($customArr->is_plan) && $customArr->is_plan == 0 ) {
            	$customArr->is_plan = $this->createCustom( $planObj->created_by, $planObj->product_id, $rlimit, $qlimit, $climit, $plan_type );
            	$plan_id = $customArr->is_plan;
            }
            if( isset($customArr->is_sub) && $customArr->is_sub == 0 ) {
            	$org_title = $this->getOrgName($planObj->org_id);
            	if ( $state == 2 ) {
            		$resArr = $this->insertSub("", $planObj->product_id, $planObj->product, $plan_id, $planObj->plan, $_POST['jform']['start_time'], $_POST['jform']['end_time'], $planObj->org_id, $org_title, $applications );
            		$sub_id = $resArr['record_id'];
            		$customArr->is_sub = 1;
            	} else {
            		$sub_id = 0;
            	}
            }
            $out = array();
            if($state == 2) {
                if($sub_id == 0) {
                    $out['success'] = 0;
                    $out['result'] = JText::sprintf('COM_REQUEST_CREATE_SUBSCRIPTION_FAILED', $planObj->plan, $org_title);
                } else {
                    $out['success'] = 1;
                    $out['result'] = $resArr;
                }
            } else {
                $out['success'] = 1;
            }
            echo json_encode($out);

            $custom = addslashes(json_encode($customArr));
            /* send email and insert asg_logs and get custom */
            if ($state == 2) {
                $sql = 'UPDATE `#__request_list` SET plan_id = '.$plan_id.', subscriptions_id = "'.$sub_id.'", updated = "'. date('Y-m-d H:i:s', time()) .'", status =' . $state . ', `admin_note` = "' . $admin_note . '", `custom` = "' . $custom . '" WHERE id =' . $id;
            } elseif ($state == 3) {
                $sql = 'UPDATE `#__request_list` SET updated = "'. date('Y-m-d H:i:s', time()) .'", subscriptions_id = "'.$sub_id.'", status =' . $state . ', `admin_note` = "' . $admin_note . '", `custom` = "' . $custom . '" WHERE id =' . $id;
            }
            $db->setQuery($sql);
            $result = $db->execute();
            if($result) {
	                $this->insertLogs($planObj->created_by, $planObj->plan_id, $planObj->plan, $this->statusArr[$state]);
	                if ( $is_email == 1 )
	                    $this->sendEmail($planObj->created_by, $planObj->plan_id, $planObj->product_id, $rlimit, $qlimit, $state, $planObj->plan, 0 );
            }
        }
        JFactory::getApplication()->close();
        //$this->ajaxSend($resArr);
        //$this->setRedirect(JRoute::_('index.php?option=com_request', false));
    }

    /**
     * Insert request state to asg_logs;
     * @param type $uid
     * @param type $plan_id
     * @param type $plan
     * @param type $state
     * @return boolean
     */
    private function insertLogs($user_id, $plan_id, $plan, $state) {
        $org = $this->getOrganization($user_id);
        $user_org = $org[0];
        $uuid = CreateSubscriptionApi::getUuid('');
        $is_show = 0;
        $log_type = 'Request';
        $http_status = 200;
        $summary = 'Request Plan State: Your request(' . $plan . ') had been processed,new state is ' . $state . '.';
        $content = htmlspecialchars('<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\"><html><head><title>200 Success</title></head><body><h1>Plan State</h1><p>' . $summary . '</p></body></html>');
        $entity_type = 'Plan';
        $entity_id = $plan_id;
        $event = 'Request';
        $event_status = 'Request';
        $status = 1;
        $published = 1;
        $create_time = date('Y-m-d H:i:s', time());
        $db = JFactory::getDbo();
        $sql = 'INSERT INTO asg_logs (`uuid`,`uid`,`org_id`,`is_show`,`log_type`,`http_status`,`summary`,`content`,`entity_type`,`entity_id`,`event`,`event_status`,`status`,`published`,`create_time`)';
        $sql .= 'VALUES ("' . $uuid . '", ' . $user_id . ', ' . $user_org . ', ' . $is_show . ', "' . $log_type . '", "' . $http_status . '", "' . $summary . '", "' . $content . '", "' . $entity_type . '", ' . $entity_id . ', "' . $event . '", "' . $event_status . '", ' . $status . ', ' . $published . ', "' . $create_time . '")';
        $db->setQuery($sql);
        $db->execute();
        return TRUE;
    }

    private function createCustom($user_id, $product_id, $rlimit, $qlimit,$climit,$plan_type){
        $db = JFactory::getDbo();
        $plan = new stdClass();
        $fields = new stdClass();
        $user = JFactory::getUser();
        $lang   = JFactory::getLanguage();
        $fields->{37} = '<p>per month <br /><br />Best value for active applications</p>';
        $fields->{39} = array("-1");
        $fields->{53} = $product_id;
        $fields->{55} = 'Best value for active applications';
        $fields->{79} = $rlimit;
        $fields->{80} = $qlimit;
        $fields->{110} = CreateSubscriptionApi::getUuid('');;
        $fields->{120} = '$99.9';
        $fields->{123} = '';
        $fields->{152} = $climit;
        if (!empty($plan_type))
        	$fields->{131} = addslashes($plan_type);
        $plan->id = null;
        $plan->title       =   'Custom '.date('Y-m-d H:i:s',time());
        $plan->published   =   1;
        $plan->access      =   1;
        $plan->user_id     =   $user_id;
        $plan->section_id  =   1;
        $plan->parent_id   =   0;
        $plan->ctime       =   JFactory::getDate()->toSql();
        $plan->extime      =   '';
        $plan->mtime       =   JFactory::getDate()->toSql();
        $plan->inittime    =   JFactory::getDate()->toSql();
        $plan->ftime       =   '';
        $plan->type_id     =   7;
        $plan->meta_descr  =   '';
        $plan->meta_key    =   '';
        $plan->meta_index  =   '';
        $plan->alias       =   JApplication::stringURLSafe(strip_tags($plan->title));
        $plan->featured    =   0;
        $plan->archive     =   0;
        $plan->ucatid      =   0;
        $plan->langs       =   $lang->getTag();
        $plan->ip          =   $_SERVER['REMOTE_ADDR'];
        $plan->hidden      =   0;
        $plan->access_key  =   md5(time() . $_SERVER['REMOTE_ADDR'] . $plan->title);
        $plan->fields      =   json_encode($fields);
        $plan->fieldsdata  =   'per month Best value for active applications, Custom';
        $plan->categories  =   "[]";
        $record_id = 0;
        $result = $db->insertObject("#__js_res_record",$plan,'id');
        if ( $result ) {
            $record_id = $db->insertid();
            $time = date('Y-m-d H:i:s', time());
            $field_key = array();
            $field_key[37] = 'k'.md5($fields->{37}.'-html');
            $field_key[39] = 'k'.md5($fields->{39}.'-plevel');
            $field_key[53] = 'k'.md5($fields->{53}.'-child');
            $field_key[55] = 'k'.md5($fields->{55}.'-html');
            $field_key[79] = 'k'.md5($fields->{79}.'-digits');
            $field_key[80] = 'k'.md5($fields->{80}.'-digits');
            $field_key[152] = 'k'.md5($fields->{152}.'-digits');
            $field_key[110] = 'k'.md5($fields->{110}.'-uuid');
            $field_key[120] = 'k'.md5($fields->{120}.'-text');
            if (!empty($plan_type))
            	$field_key[131] = 'k9b0a5d3720001d8d39da1603fc707da5';
            $content  = '(37,"'.$field_key[37].'","html", "Description", "'.$fields->{37}.'",'.$record_id.','.$user_id.', 7, 1, "::1", "'.$time.'"),';
            $content .= '(39,"'.$field_key[39].'","plevel", "Level", "-1",'.$record_id.','.$user_id.', 7, 1, "::1", "'.$time.'"),';
            $content .= '(53,"'.$field_key[53].'","child", "Product", "'.$fields->{53}.'",'.$record_id.','.$user_id.', 7, 1, "::1", "'.$time.'"),';
            $content .= '(55,"'.$field_key[55].'","html", "Plan details", "'.$fields->{55}.'",'.$record_id.','.$user_id.', 7, 1, "::1",  "'.$time.'"),';
            $content .= '(79,"'.$field_key[79].'","digits", "Rate limit", "'.$fields->{79}.'",'.$record_id.','.$user_id.', 7, 1, "::1", "'.$time.'"),';
            $content .= '(80,"'.$field_key[80].'","digits", "Quota limit", "'.$fields->{80}.'",'.$record_id.','.$user_id.', 7, 1, "::1",  "'.$time.'"),';
            $content .= '(152,"'.$field_key[152].'","digits", "Concurrent Limit", "'.$fields->{152}.'",'.$record_id.','.$user_id.', 7, 1, "::1",  "'.$time.'"),';
            $content .= '(110,"'.$field_key[110].'","uuid", "uuid", "'.$fields->{110}.'",'.$record_id.','.$user_id.', 7, 1, "::1", "'.$time.'"),';
            if (!empty($plan_type)) {
            	$content .= '(120,"'.$field_key[120].'","text", "Price or keyword", "'.$fields->{120}.'",'.$record_id.','.$user_id.', 7, 1, "::1", "'.$time.'"),';
            	$content .= '(131,"'.$field_key[131].'","text", "Plan type", "'.addslashes($plan_type).'",'.$record_id.','.$user_id.', 7, 1, "::1", "'.$time.'")';
            } else {
            	$content .= '(120,"'.$field_key[120].'","text", "Price or keyword", "'.$fields->{120}.'",'.$record_id.','.$user_id.', 7, 1, "::1", "'.$time.'")';
            }
            $sql = 'INSERT INTO `#__js_res_record_values`(`field_id`, `field_key`, `field_type`, `field_label`, `field_value`, `record_id`, `user_id`, `type_id`, `section_id`, `ip`, `ctime`) VALUES '.$content;
            $db->setQuery($sql);
            $db->execute();
        }
        return $record_id;
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
    private function insertSub($description="", $product_id, $product_title, $plan_id, $plan_title, $start_date, $end_date, $org_id, $org_title, $applications )
    {

      if($applications){
        $applications = explode(",", $applications);
      }

      $sub_id = 0;
      $db = JFactory::getDbo();
      $sub = new stdClass();
      $fields = new stdClass();
      $user = JFactory::getUser();
      $lang   = JFactory::getLanguage();

      $stimeStr = strtotime($start_date);
      $stime = date('Y-m-d', $stimeStr);

      $etime = strtotime($end_date);
      $etime = date('Y-m-d', $etime);

      $fields->{'66'}   =   "";
      $fields->{'114'}  =   $product_id;
      $fields->{'69'}   =   $plan_id;
      $fields->{'71'}   =   array($stime);
      $fields->{'72'}   =   array($etime);
      $fields->{'73'}   =   $org_id;
	  
	  if ($stimeStr > time())
	  {
		  $fields->{'78'}   =   array("Inactive");
	  }
	  
	  else
	  {
		  $fields->{'78'}   =   array("Active");
	  }
	  

      $fields->{'112'}  =   CreateSubscriptionApi::getUuid('');

      $org_title        =  $this->getOrgName($org_id);
      $product_title    =  $product_title;
      $plan_title       =  $plan_title;
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
              }
            }else{
              $result = array("record_id"=>$record_id, "msg"=>JText::_('AUTO_CREATE_SUBSCRIPTION_SUCCESS'));
            }
          }
        }
      }
      return $result;
    }

    /**
     * Start
     * Send Email to user.
     */
    public function sendEmail($user_id = '129', $plan_id = '239', $product_id = '240',$rate = 0, $quota = 0, $state=2, $plan_name="custom", $canceller=0) {
        if (!empty($user_id)) {
            $user = JFactory::getUser($user_id);
            $user_email = $user->email;
            $results = $this->_getEmailTemplateByAlias("request_plan_processed");
            $_domain = DeveloperPortalApi::getHostUrl();
            $config = JFactory::getConfig();
            $product_url = $_domain . JRoute::_(Url::record($product_id));
            $plan_url = $_domain . JRoute::_(Url::record($plan_id));
            $title = $results->subject;
            $content = $results->content;
            $content = str_replace("{PLAN_NAME}", $plan_name, $content);
            $content = str_replace("{CREATE_SUB_URL}", $create_sub_url, $content);
            $content = str_replace("{PLAN_URL}", $plan_url, $content);
            $content = str_replace("{PRODUCT_URL}", $product_url, $content);
            $content = str_replace("{RATE_LIMIT}", $rate, $content);
            $content = str_replace("{QUOTA_LIMIT}", $quota, $content);
            $content = str_replace("{STATUS}", $this->statusArr[$state], $content);
            if ( $canceller == 0 )
            	$content = str_replace("Canceler:{CANCELER}", '', $content);
            else {
				$user_url = $_domain . JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id=' . DeveloperPortalApi::getUserProfileId($canceller));
				$content = str_replace("{CANCELER}", $user_url, $content);
            }
            $is_send_email_to_user = DeveloperPortalApi::send_email($user_email, $title, $content, $results->isHTML);
        }
    }

    /**
     * Start
     * Send Email to admin.
     */
    public function sendAdminEmails($user_id = '129', $plan_id, $product_id, $organization_id, $rate = 0, $quota = 0, $plan_name="Custom", $additional_request, $request_url) {
        if (!empty($user_id)) {
            $_domain = DeveloperPortalApi::getHostUrl();
//            $config = JFactory::getConfig();
//            $user = JFactory::getUser($user_id);
            $user_url = $_domain . JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id=' . DeveloperPortalApi::getUserProfileId($user_id));

//            $user_url = $_domain . JRoute::_(Url::record($user_id));

            if($plan_name=="Custom") {
                $results = $this->_getEmailTemplateByAlias("request_custom_plan_notify_admin_contacter");
            }
            else {
                $results = $this->_getEmailTemplateByAlias("request_normal_plan_notify_admin_contacter_of_organization");
            }

            $content = $results->content;
            $title = $results->subject;


            $content = str_replace("{PLAN_NAME}", $plan_name, $content);
            $content = str_replace("{USER_URL}", $user_url, $content);
            $content = str_replace("{ADDITIONAL_REQUEST}", $additional_request, $content);
            $content = str_replace("{RATE_LIMIT}", $rate, $content);
            $content = str_replace("{QUOTA_LIMIT}", $quota, $content);
            $content = str_replace("{REQUEST_URL}", JPATH_BASE . $request_url, $content);


            $plan_url = $_domain . JRoute::_(Url::record($plan_id));
            $content = str_replace("{PLAN_URL}", $plan_url, $content);


            $product_url = $_domain . JRoute::_(Url::record($product_id));
            $content = str_replace("{PRODUCT_URL}", $product_url, $content);


            $organization_url = $_domain . JRoute::_(Url::record($organization_id));
            $content = str_replace("{ORGANIZATION_URL}", $organization_url, $content);


            $admin_email_group = DeveloperPortalApi::getEmailsOfJoomlaAdmins();

            $is_send_email_to_admin = DeveloperPortalApi::send_email($admin_email_group, $title, $content, false);

//            $is_send_email_to_admin = DeveloperPortalApi::send_email($user_email, $title, $content, $results->isHTML);
        }
    }

    /**
     * insert new Plan data.
     */
    public function insertRequest() {
        if (isset($_POST) && !empty($_POST)) {
            $is_email = 0;
            $appliction = 0;
            if ( isset($_POST['appliction']) && !empty($_POST['appliction']) )
              $appliction = $_POST['appliction'];
            if ( isset($_POST['is_email']) && $_POST['is_email'] == 1 )
                    $is_email = 1;
            if (isset($_POST['plan'])) {
                $plan_id = $_POST['plan_id'] * 1;
                $db = JFactory::getDbo();
                $db->setQuery('SELECT field_label, field_value, field_id FROM `openapi_js_res_record_values` WHERE `record_id` = ' . $plan_id);
                $planInfo = $db->loadObjectList();
                foreach ($planInfo as $v) {
                    if ($v->field_id == 79) {
                        $rlimit = $v->field_value;
                    }
                    elseif ($v->field_id == 80) {
                        $qlimit = $v->field_value;
                    }
                    elseif($v->field_id == 152){
                        $climit = $v->field_value;
                    }
                }
                $plan = $_POST['plan'];
                $db = JFactory::getDbo();
                $queryString = "SELECT `title` FROM `openapi_js_res_record` WHERE `id`=" . $plan_id;
                $db->setQuery($queryString);
                $result = $db->loadObjectList();
                $plan_name = $result[0]->title;
                $custom = addslashes(json_encode(array('qlimit' => $qlimit, 'rlimit' => $rlimit, 'climit' => $climit, 'comment' => $_POST['comment'], 'subscription_start' => '', 'subscription_end' => '','application'=>$appliction,'is_sub'=>0,'is_email'=>$is_email)));
            } else {
                $plan = 'Custom';
                $plan_name = "Custom";
                $plan_id = 0;
                $custom = addslashes(json_encode(array('qlimit' => $_POST['quota_limit'], 'rlimit' => $_POST['rate_limit'], 'climit' => $_POST['concurrent_calls'], 'comment' => $_POST['comment'], 'subscription_start' => '', 'subscription_end' => '','application'=>$appliction,'is_plan'=>0,'is_sub'=>0,'is_email'=>$is_email)));
                $rlimit = $_POST['rate_limit'];
                $qlimit = $_POST['quota_limit'];
                $climit = $_POST['concurrent_calls'];
            }
            if ( isset($_POST['comment']) && !empty($_POST['comment']) )
                $user_note = addslashes($_POST['comment']);
            else
                $user_note = 'null';
            $admin_note = 'null';
            $product = $_POST['product'];
            $product_id = $_POST['product_id'] * 1;
            $user = JFactory::getUser();
            $create_by = $user->id;
            $organization = $this->getOrganization($user->id);
            $org_id = $organization[0];
            $requested_by = date('Y-m-d H:i:s', time());
            $updated = date('Y-m-d H:i:s', time());
            $status = 1;
            $db = JFactory::getDbo();
            $sql = "INSERT INTO `#__request_list` (`created_by`, `requested_by`, `product`, `product_id`, `application_id`, `status`, `updated`, `plan`, `plan_id`, `org_id`, `user_note`, `admin_note`, `custom`) VALUES ({$create_by},'{$requested_by}','{$product}',{$product_id},'{$appliction}',{$status},'{$updated}','{$plan}',{$plan_id},{$org_id},'{$user_note}','{$admin_note}',\"{$custom}\")";
            $db->setQuery($sql);
            $result = $db->execute();
            //$this->($user->id, $plan_id, $product_id);
            $this->sendAdminEmails($user->id,$plan_id,$product_id,$org_id,$rlimit,$qlimit,$climit,$plan_name,$user_note,"/subscriptions/requests");
            $this->ajaxSend($result);
        }
    }
    /**
     * According to state select plan data.
     */
    public function selectState() {
        if( isset( $_POST['state'] ) ) {
            $select = $_POST['state'];
            $session = JFactory::getSession();
            $session->set('select', $select);
            $this->ajaxSend('success');
        }
    }

    public function userStatus(){
        if( isset( $_POST['status'] ) ) {
            $select = $_POST['status']*1;
            $session = JFactory::getSession();
            $session->set('user_status', $select);
        }
        $this->setRedirect('index.php/subscriptions');
    }
    /**
     * get all request information
     * if you want to order by status,you just request a post data by named status.
     * status code: void or 0 is All,1 is pending, 2 is approved, 3 is rejected, 4 is cancelled.
     */
    public function getRequestList() {
        $user = JFactory::getUser();
        if (isset($user->id) && !empty($user->id)) {
            $db = JFactory::getDbo();
            $sql = 'SELECT * FROM `#__request_list` WHERE `created_by` = ' . $user->id;
            if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] != 0) {
                $sql .= ' AND `status` = ' . $_POST['status'] * 1;
            }
            $db->setQuery($sql);
            $result = $db->loadObjectList();
        } else {
            $result = array('success' => 0, 'result' => 'You have not login.');
        }
        return $result;
    }

    /**
     * Return data in ajax
     */
    private function ajaxSend($result, $key = 'result') {
        $out = array(
            'success' => 1,
            $key => $result
        );
        echo json_encode($out);
        JFactory::getApplication()->close();
    }

    /**
     * Return error data in ajax.
     */
    private static function error($msg) {
        $out = array(
            'success' => 0,
            'error' => $msg
        );
        echo json_encode($out);
        JFactory::getApplication()->close();
    }


    private function getOrganization($user_id) {
        $rv = array();
        $db = JFactory::getDbo();
        $db->setQuery("SELECT field_value FROM #__js_res_record_values WHERE record_id IN (SELECT record_id FROM #__js_res_record_values WHERE field_id=77 AND field_value=" . $user_id . ") and field_id=47");
        if ($result = $db->loadObjectList()) {
            foreach ($result as $record) {
                array_push($rv, $record->field_value);
            }
        }
        return $rv;
    }

    private function _getEmailTemplateByAlias($alias) {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT `subject`,`content` FROM #__email_templates WHERE alias="' . $alias . '" AND published = 1 LIMIT 1');
        return $db->loadObject();
    }

    private function getOrgName($id) {
        $db = JFactory::getDbo();
        $sql = 'SELECT `title` FROM `#__js_res_record` WHERE `id` =  '.$id;
        $db->setQuery($sql);
        $result = $db->loadObject();
        return $result->title;
    }

    /**---api---**/

    public function ToCancel() {
        if ( isset($_POST['id']) ) {
            $id = $_POST['id']*1;
            $status = 4;
            $user_note = addslashes($_POST['user_note']);
            $res = $this->changeStatus($id, $status, $user_note);
            echo json_encode($res);
            exit;
        }
    }

    public function reSubmit() {
        if ( isset($_POST['id']) ) {
            $id = $_POST['id']*1;
            $status = 1;
            $rate_limit = $_POST['rate_limit']*1;
            $quota_limit = $_POST['quota_limit']*1;
            $user_note = addslashes($_POST['user_note']);
            $res = $this->changeStatus($id, $status, $user_note, $rate_limit, $quota_limit);
            echo json_encode($res);
            exit;
        }
    }

        /**
     * cancel request or become pending
     * 1 is Pendding Status. 4 is Cancelled Status.
     */
    private function changeStatus( $id, $status, $user_note, $rate_limit=-1, $quota_limit=-1 ) {
    	$is_email = 1;
        $user = JFactory::getUser();
        if (isset($user->id) && !empty($user->id)) {
            if (isset($status) && ( $status == 1 || $status == 4 )) {
                $db = JFactory::getDbo();
                /* send email and insert asg_logs and get custom */
                $selectSql = 'SELECT `created_by`, `plan_id`, `plan`, `product_id`, `custom` FROM `#__request_list` WHERE `id` = ' . $id;
                $db->setQuery($selectSql);
                $planObj = $db->loadObject();
                /* send email and insert asg_logs and get custom */
                $customObj = json_decode($planObj->custom);
                $is_email = $customObj->is_email;
                $qlimit = $customObj->qlimit;
                $rlimit = $customObj->rlimit;
                if ( $status == 4 ) {
                    $sql = 'UPDATE `#__request_list` SET status =' . $status . ', `user_note` = "' . $user_note . '" WHERE id =' . $id;
                } else if( $status == 1 ) {
                    if ( $rate_limit > 0 )
                        $customObj->rlimit = $rate_limit;
                    if ( $quota_limit > 0 )
                        $customObj->qlimit = $quota_limit;
                    $custom = addslashes(json_encode($customObj));
                    $sql = 'UPDATE `#__request_list` SET status =' . $status . ', `user_note` = "' . $user_note . '", `custom` = "'.$custom.'" WHERE id =' . $id;
                }
                $db->setQuery($sql);
                $result = $db->execute();
                if ($result) {
                    $this->insertLogs($planObj->created_by, $planObj->plan_id, $planObj->plan, $this->statusArr[$status]);
                    $resArr = array('success' => 1, 'result' => 'Operation Successed!');
                }
            } else {
                $resArr = array('success' => 0, 'result' => 'Illegal operation.');
            }
        } else {
            $resArr = array('success' => 0, 'result' => 'You have not login.');
        }
        return $resArr;
    }

}
