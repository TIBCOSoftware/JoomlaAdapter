<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
include_once JPATH_BASE . "/includes/api.php";


$k = $p1 = 0;
$params = $this->tmpl_params['list'];
$core = array('type_id' => 'Type', 'user_id','','','','','','','','', );
JHtml::_('dropdown.init');
$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach ($exclude as &$value) {
  $value = $this->fields_keys_by_id[$value];
}

$user = JFactory::getUser();

$isAdmin = (int)$user->id === 129 ? true : false;

/**
 * If the current user is not Administrator, do the things
 */
if(!$isAdmin){
  $tempItems = array();

  $myOrgId = DeveloperPortalApi::getUserOrganization();

  if(is_array($myOrgId) && !empty($myOrgId))
  {
    $myOrgId = $myOrgId[0];
  }

  foreach ($this->items as $key => $item) {
    if($myOrgId && $myOrgId == $item->fields["73"])
    {
      $tempItems[] = $item;
    }
  }

  $this->items = $tempItems;

  $session = JFactory::getSession();
  $nowStatus = $session->get('user_status',1);
  $request_list = TibcoTibco::getRequestList($nowStatus);


  $lang = JFactory::getLanguage();
  $lang->load('com_request', JPATH_SITE);

  JText::Script("COM_REQUEST_CONFIRM_CANCEL",false,false);
  JText::Script("COM_REQUEST_DISCARD_CANCEL",false,false);
  JText::Script("COM_REQUEST_CONFIRM_RESUBMIT",false,false);
  JText::Script("COM_REQUEST_RESUBMIT_FORM_TITLE",false,false);
  JText::Script("COM_REQUEST_CANCEL_RESUBMIT",false,false);
}

?>
<?php if($params->get('tmpl_core.show_title_index')):?>
  <h2><?php echo JText::_('CONTHISPAGE')?></h2>
  <ul>
    <?php foreach ($this->items AS $item):?>
      <li><a href="#record<?php echo $item->id?>"><?php echo $item->title?></a></li>
    <?php endforeach;?>
  </ul>
<?php endif;?>

<style type="text/css">
.fields-list {
  padding: 5px 5px 5px 25px !important;
}
.relative_ctrls {
  position: relative;
}
.user-ctrls {
  position: absolute;
  top:-8px;
  right: 0;
}

.subscriptions-request-list{margin-top: 10px;}
.rwd-table-header{font-weight: bold;font-size:13px;border-bottom:1px solid #bcbcbc;}
.rwd-table-cell{cursor: pointer;margin:0px;border:none;}
.rwd-table-cell li{padding:0px;padding-left:20px;}
.rwd-table-column{display: inline-block;}
.request-product-thumbnail{display: block;border: 0;float: left;padding: 0;}
.subscriptions-request-item-detail,
.subscriptions-request-item-detail tr,
.subscriptions-request-item-detail td,
.subscriptions-request-item-detail th{
  border: none!important;
  white-space:normal;
}

.subscriptions-request-item-detail thead,
.subscriptions-request-item-detail tbody tr:nth-child(2n){background-color: transparent;}
.subscriptions-request-item-detail tbody tr:nth-child(2n){border-top: 1px solid #dbdcdd!important;}
.accordion-body{
  background-color: #f9f9f9!important;
}
.collapsed .arrowR{
  -webkit-transform: rotate(0deg);
      -ms-transform: rotate(0deg);
          transform: rotate(0deg);
}

.arrowR{
  /*position: absolute;
  top: 50%;
  left: 0px;
  margin-top: -10px;*/
  font-size: 20px;
  float:left;
  line-height:32px;
  margin-left:20px;
  -webkit-transform: rotate(90deg);
      -ms-transform: rotate(90deg);
          transform: rotate(90deg);
}


.accordion-inner{padding: 0;}

#request-plan-form{
  height:500px;
  width:720px !important;
  margin-top: -250px;
  margin-left: -360px;
}

.request-form-detail-box{
  background: #efefef;
  padding: 20px 10px;
  border: 1px solid #dbdcdd;
  width: 200px!important;
  height: 194px;
  border: 1px solid #ccc;
  box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
  border-radius: 3px;
}

.request-tips{color: red; margin-bottom: 30px;}

.request-form-product{}
.request-product-title{color:#333;display: inline-block;line-height: 50px;font-size:16px;}
.plan-block{font-size: 12px;margin-top:25px;padding:0 10px;}
.request-terms,.request-terms label{font-size: 8px;}
.request-note-block{width:444px;}
.request-note-block label{margin-bottom: 0;}
.request-note-block textarea{width:430px;height:180px;}


.cancel-form-element,
.resubmit-form-element
{
  display: none;
}

.accordion{
  margin-bottom:0px;
}

.accordion-group{
  border:none;
  border-bottom:1px solid #bcbcbc;
  border-radius: 0px !important;
}

.accordion-heading .rwd-table-cell{
  padding:5px 0;
}

.accordion-heading .rwd-table-cell .rwd-table-column{
  padding:0px;
  font-size:12px;
}

.rwd-table-cell li{
  padding:0;
}

.rwd-table-cell .rwd-table-column:nth-child(1){
  width:32%;
}

.accordion-inner tbody tr td:nth-child(1){
  width:32%;
}

.rwd-table-cell .rwd-table-column:nth-child(n+2){
  width:22%;
}

.accordion-inner tbody tr td:nth-child(n+2){
  width:22%;
}

.accordion-inner thead tr th:first-child, .accordion-inner tbody tr td:first-child {
    padding-left: 10px;
    padding-right: 0;
}

.accordion-inner thead tr th:nth-child(n+2), .accordion-inner td:nth-child(n+2) {
    padding-left: 0;
    padding-right: 0;
}
.accordion-inner tbody tr td:nth-child(n+2) div b{
  font-size:12px;
  color:#afafaf;
}

.accordion-heading .rwd-table-cell .rwd-table-column p{
  padding:0;
  margin:0;
  font-weight:normal;
  font-size:18px;
}

.accordion-heading .rwd-table-cell .rwd-table-column img{
  width:36px;
  height:36px;
}

.accordion-heading .rwd-table-cell .rwd-table-column:last-child p{
  font-size:12px;
  color:#333;
}

.req-status:before{
   content: "";
   float:left;
   width: 15px;
   height: 15px;
   border: 1px solid #bbb;
   border-radius: 50%;
   background: #fff;
   margin-right:2px;
}

/**
 * 1 pending
 * 2 approve
 * 3 reject
 * 4 cancel
 */
.request_status_1{
  color: rgb(195, 196, 10);
}
.request_status_3,
.request_status_4{
  color: red;
}

.request_status_2{
  color: green;
}

.request-custom-block{

}

#subscriptions-tab .nav-tabs{
  border-bottom:1px solid #006699;
}

#subscriptions-tab .nav-tabs li:first-child{
  margin-left:0px;
}

#subscriptions-tab .nav-tabs li.end-ctl{
  float:right;
  line-height:35px;
  height:35px;
}

.accordion-inner{
  border:none;
}

.subscriptions-request-item-detail{
  background-color:white;
}

.subscriptions-request-item-detail tbody tr{
  background: white !important;
}

.subscriptions-request-item-detail tbody tr td{
  background: white !important;
}

.col-field div{
  width:80%;
}

.col-field div p{
  padding:0;
  border-bottom:1px solid #cdcdcd;
}

.col-field div div label:first-child{
  float:left;
  border:1px solid #efefef;
  height:36px;
  line-height:36px;
  padding:0 15px;
  width: 50px;
  overflow: hidden;
}

.col-field div div label:last-child{
  float:left;
  height:36px;
  line-height:18px;
  margin-left:10px;
}

</style>

<?php if(!$isAdmin):?>
<div id="subscriptions-tab" class="span12">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#subscriptions-request-list-container"  data-toggle="tab">Request</a></li>
    <li><a href="#subscriptions-subscripiton-list-container"  data-toggle="tab">Subscriptions</a></li>
  <li id="req-ctl" class="end-ctl">
        <form action='.' method="post" class="pull-right">
            <select name="status" id="status" onchange="this.form.submit();" value=''>
                <option value="0" <?php echo $nowStatus == 0 ? 'selected' : '' ; ?> ><?php echo ' '. JText::_('COM_REQUEST_STATUS_0');?></option>
                <option value="1" <?php echo $nowStatus == 1 ? 'selected' : '' ; ?>><?php echo ' '.JText::_('COM_REQUEST_STATUS_1');?></option>
                <option value="2" <?php echo $nowStatus == 2 ? 'selected' : '' ; ?>><?php echo ' '.JText::_('COM_REQUEST_STATUS_2');?></option>
                <option value="3" <?php echo $nowStatus == 3 ? 'selected' : '' ; ?>><?php echo ' '.JText::_('COM_REQUEST_STATUS_3');?></option>
                <option value="4" <?php echo $nowStatus == 4 ? 'selected' : '' ; ?>><?php echo ' '.JText::_('COM_REQUEST_STATUS_4');?></option>
            </select>
            <input type="hidden" name="option" value="com_request" />
            <input type="hidden" name="task" value="lists.userStatus" />
            <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
        </form>
  </li>
  <li id="sub-ctl" class="end-ctl" style="display:none;"></li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane active" id="subscriptions-request-list-container" class="span12">
    <!-- Begin -->
    <div class="span12 rwd-table subscriptions-request-list">
      <p style="padding:20px 25px 0 20px;font-size:18px"><?php echo JText::_('COM_REQUEST_SUBSCRIPTIONS_PAGE');?></p>
      <div class="clearfix"></div>
      <div class="rwd-table-header">
        <ul class="rwd-table-cell">
          <li class="rwd-table-column"><span style="padding-left:20px;"><?php echo JText::_('COM_REQUEST_STATUS_HEAD_PRODUCT');?></span></li>
          <li class="rwd-table-column"><?php echo JText::_('COM_REQUEST_STATUS_HEAD_REQUESTED');?></li>
          <li class="rwd-table-column"><?php echo JText::_('COM_REQUEST_STATUS_HEAD_UPDATED');?></li>
          <li class="rwd-table-column"><?php echo JText::_('COM_REQUEST_STATUS_HEAD_STATUS');?></li>
        </ul>
      </div>
      <div class="rwd-table-body">
        <?php foreach ($request_list as $key => $request_item):
                if ( !empty( $request_item->plan_id ) ) {
                    $request_item->plan = JModelLegacy::getInstance('Record', 'CobaltModel')->_prepareItem(JModelLegacy::getInstance('Record', 'CobaltModel')->getItem($request_item->plan_id), 'full');
                } else {
                    $custom = json_decode($request_item->custom);
                    $request_item->plan = (Object)array('title'=>'custom', 'fields'=>array( 79=>$custom->rlimit, 80=>$custom->qlimit));
                }
                $request_item->user = JFactory::getUser($request_item->created_by);
        ?>
          <div class="rwd-table-row">
            <div>
              <div class="accordion" id="request-lists-<?php echo $key;?>">
                <div class="accordion-group">
                  <div class="accordion-heading collapsed" class="accordion-toggle" data-toggle="collapse" data-parent="#request-lists-<?php echo $key;?>" data-target="#collapse-<?php echo $key;?>">
                    <ul class="rwd-table-cell">
                      <li class="rwd-table-column">
                        <span class="arrowR">&#x2023;</span>
                        <img class="request-product-thumbnail" src="<?php echo JURI::base().TibcoTibco::getProductImage($request_item->product_id);?>"
                        alt="">
                        <p><?php echo $request_item->product;?></p>
                        <span>
              <?php if ($request_item->plan->fields[39][0]<0 || empty($request_item->plan->fields[39][0])): ?>
                <?php echo 'Custom';?>
              <?php else: ?>
                <?php echo JText::_('COM_REQUEST_LEVEL');?>&nbsp;<?php echo $request_item->plan->fields[39][0];?>
              <?php endif ?>
            </span>
                      </li>
                      <li class="rwd-table-column"><?php echo substr($request_item->requested_by, 0, strpos($request_item->requested_by,' '));?><br/>&nbsp;</li>
                      <li class="rwd-table-column"><?php echo  substr($request_item->updated, 0, strpos($request_item->updated,' '));?><br/>&nbsp;</li>
                      <li class="rwd-table-column <?php echo "request_status_" . $request_item->status;?>"><p class="req-status req-status-<?php echo $request_item->status;?>"><?php echo JText::_('COM_REQUEST_STATUS_'.$request_item->status);?><br/>&nbsp;</p></li>
                    </ul>
                  </div>
                  <div id="collapse-<?php echo $key;?>" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <table class="table subscriptions-request-item-detail">
                        <!-- <thead>
                          <tr>
                            <th class="col-field"><?php echo JText::_('COM_REQUEST_STATUS_HEAD_PRODUCT');?></th>
                            <th><?php echo JText::_('COM_REQUEST_STATUS_HEAD_REQUESTED');?></th>
                            <th><?php echo JText::_('COM_REQUEST_STATUS_HEAD_UPDATED');?></th>
                            <th><?php echo JText::_('COM_REQUEST_STATUS_HEAD_STATUS');?></th>
                          </tr>
                        </thead> -->
                        <tbody>
                          <tr>
                            <td class="col-field">
                <div>
                                <p><?php echo JText::_('COM_REQUEST_PLAN')?></p>
                  <div>
                      <label><?php echo $request_item->plan->title;?></label>
                      <?php 
                            if(!empty($custom->climit))
                            {
                              $concurrent_calls = $custom->climit." ".JText::_("CONCURRENT_CALLS");
                            }else{
                              $concurrent_calls ='';
                            }

                      ?>
                    <label><?php echo str_replace("/"," calls per ", $request_item->plan->fields[79]);?><br/>
                        <?php echo str_replace("/"," calls per ", $request_item->plan->fields[80]);?><br/><?php echo $concurrent_calls; ?></label>
                  </div>
                </div>
                            </td>
                            <td>
                <div>
                  <b><?php echo JText::_('COM_REQUEST_REQUESTOR');?></b><?php echo $request_item->user->name;?>
                                  <br/>
                  <b><?php echo JText::_('COM_REQUEST_EMAIL');?></b><?php echo $request_item->user->email;?>
                                  <br/>
                                    <br/>
                                  <b><?php echo JText::_('COM_REQUEST_APPLICATION');?></b> <?php echo TibcoTibco::getAppName($request_item->application_id);?>
                </div>

                            </td>
                            <td>
                <div>
                  </div>
                            </td>
                            <td>
                              <?php if($request_item->status == 1):?>
                              <button
                            class="btn request-dialog" data-request='{"request_id":"<?php echo $request_item->id;?>","request_product":"<?php echo $request_item->product;?>",
                            "request_product_thumbnail":"<?php echo JURI::base().TibcoTibco::getProductImage($request_item->product_id);?>","request_plan_name":"<?php echo
                            $request_item->plan->title;?>","request_dlimit":<?php echo (int)$request_item->plan->fields[80];?>,"request_slimit":<?php echo
                            (int)$request_item->plan->fields[79];?>,"request_status":"<?php echo $request_item->status;?>","request_user_name":"<?php echo $request_item->user->name;?>","user_note":"<?php if($request_item->user_note && $request_item->user_note !== 'null'){echo addslashes($request_item->user_note);}else{echo '';}?>"}'>Cancel</button>
                            <?php endif;?>

                            <?php if($request_item->status == 2):?>
                              <div>
                                <b><?php echo JText::_('COM_REQUEST_STATUS_2');?></b><br/>
                                On <?php echo $request_item->updated; ?><br/>
                                <a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=record&Itemid=140&id='.$request_item->subscriptions_id); ?>">View subscription</a>
                              </div>
                            <?php endif;?>

                            <?php if($request_item->status == 4):?>
                            <button class="btn request-dialog" data-request='{"request_id":"<?php echo $request_item->id;?>","request_product":"<?php echo $request_item->product;?>",
                            "request_product_thumbnail":"<?php echo JURI::base().TibcoTibco::getProductImage($request_item->product_id);?>","request_plan_name":"<?php echo
                            $request_item->plan->title;?>","request_dlimit":<?php echo (int)$request_item->plan->fields[80];?>,"request_slimit":<?php echo
                            (int)$request_item->plan->fields[79];?>,"request_status":"<?php echo $request_item->status;?>","request_user_name":"<?php echo $request_item->user->name;?>","user_note":"<?php if($request_item->user_note && $request_item->user_note !== 'null'){echo addslashes($request_item->user_note);}else{echo '';}?>"}'><?php echo JText::_('COM_REQUEST_CONFIRM_RESUBMIT');?></button>
                            <?php endif;?>
                            <?php if($request_item->status == 3):?>
                              <a class="btn btn-primary" href="<?php echo JURI::base().'index.php/support';?>"><?php echo JText::_('COM_REQUEST_CONTACT_ADMINISTRATOR_BUTTON');?></a>
                            <?php endif;?>

                          </td>
                        </tr>
                        <tr style="border:none !important;">
                          <td colspan="4" class="subsriptions-requests-log-list">
                            <?php if($request_item->user_note && $request_item->user_note !== 'null'):?>
                            <div class="subsriptions-requests-log-item">
                              <h6><?php echo JText::sprintf('COM_REQUESTER_LOGS_ITEM_TITLE', $request_item->user->name, $request_item->requested_by);?>:</h6>
                              <p style="word-wrap:break-word; overflow:hidden; word-break:break-all;">
                                <?php echo $request_item->user_note;?>
                              </p>
                            </div>
                            <?php endif;?>

                            <?php if($request_item->admin_note && $request_item->admin_note !== 'null'):?>
                            <div class="subsriptions-requests-log-item">
                              <h6><?php echo JText::sprintf('COM_APPROVER_LOGS_ITEM_TITLE', JFactory::getUser(129)->name, $request_item->updated);?>:</h6>
                              <p style="word-wrap:break-word; overflow:hidden; word-break:break-all;" >
                                <?php echo $request_item->admin_note;?>
                              </p>
                            </div>
                            <?php endif;?>
                          </td>
                        </tr>
                      </tbody>
                     </table>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
          <?php endforeach;?>
        </div>
      </div>
    <!-- /end -->
    </div>
    <div class="tab-pane" id="subscriptions-subscripiton-list-container">
      <?php endif;?>
      <!-- Original Data Table Begin -->
      <table class="table table-striped">
        <thead>
          <tr>
            <?php if($params->get('tmpl_core.item_title')):?>
              <th><?php echo JText::_('CTITLE');?></th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_rating')):?>
              <th>
                <?php echo JText::_('CRATING');?>
              </th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_author_avatar')):?>
              <th>
                <?php echo JText::_('CAVATAR');?>
              </th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_author') == 1):?>
              <th>
                <?php echo JText::_('CAUTHOR');?>
              </th>
            <?php endif;?>


            <?php if($params->get('tmpl_core.item_type') == 1):?>
              <th>
                <?php echo JText::_('CTYPE')?>
              </th>
            <?php endif;?>

            <?php foreach ($this->total_fields_keys AS $field):?>
              <?php if(in_array($field->key, $exclude)) continue; ?>
              <th width="1%" nowrap="nowrap">
                <?php echo JText::_($field->label);?></th>
            <?php endforeach;?>

            <?php if($params->get('tmpl_core.item_user_categories') == 1 && $this->section->params->get('personalize.pcat_submit')):?>
              <th nowrap="nowrap">
                <?php echo JText::_('CCATEGORY');?>
              </th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_categories') == 1 && $this->section->categories ):?>
              <th nowrap="nowrap">
                <?php echo JText::_('CCATEGORY');?>
              </th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_ctime') == 1):?>
              <th nowrap="nowrap">
                <?php echo JText::_('CCREATED');?>
              </th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_mtime') == 1):?>
              <th nowrap="nowrap">
                <?php echo JText::_('CCHANGED');?>
              </th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_extime') == 1):?>
              <th nowrap="nowrap">
                <?php echo JText::_('CEXPIRE');?>
              </th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_comments_num') == 1):?>
              <th nowrap="nowrap">
                <span rel="tooltip" data-original-title="<?php echo JText::_('CCOMMENTS');?>"><?php echo JString::substr(JText::_('CCOMMENTS'), 0, 1)?></span>
              </th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_favorite_num') == 1):?>
              <th nowrap="nowrap">
                <span rel="tooltip" data-original-title="<?php echo JText::_('CFAVORITE');?>"><?php echo JString::substr(JText::_('CFAVORITE'), 0, 1)?></span>
              </th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_vote_num') == 1):?>
              <th nowrap="nowrap">
                <span rel="tooltip" data-original-title="<?php echo JText::_('CVOTES');?>"><?php echo JString::substr(JText::_('CVOTES'), 0, 1)?></span>
              </th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_follow_num') == 1):?>
              <th nowrap="nowrap">
                <span rel="tooltip" data-original-title="<?php echo JText::_('CFOLLOWERS');?>"><?php echo JString::substr(JText::_('CFOLLOWERS'), 0, 1)?></span>
              </th>
            <?php endif;?>

            <?php if($params->get('tmpl_core.item_hits') == 1):?>
              <th nowrap="nowrap" width="1%">
                <span rel="tooltip" data-original-title="<?php echo JText::_('CHITS');?>"><?php echo JString::substr(JText::_('CHITS'), 0, 1)?></span>
              </th>
            <?php endif;?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->items AS $item):?>
            <tr class="<?php
            if($item->featured)
            {
              echo ' success';
            }
            elseif($item->expired)
            {
              echo ' error';
            }
            elseif($item->future)
            {
              echo ' warning';
            }
            ?>">
              <?php if($params->get('tmpl_core.item_title')):?>
                <td class="has-context">
                  <div class="relative_ctrls">
                    <?php if($this->user->get('id')):?>
                      <div class="user-ctrls">
                        <div class="btn-group" style="display: none;">
                          <?php echo HTMLFormatHelper::bookmark($item, $this->submission_types[$item->type_id], $params);?>
                          <?php echo HTMLFormatHelper::follow($item, $this->section);?>
                          <?php echo HTMLFormatHelper::repost($item, $this->section);?>
                          <?php echo HTMLFormatHelper::compare($item, $this->submission_types[$item->type_id], $this->section);?>
                        </div>
                      </div>
                    <?php endif;?>
                    <?php if($this->submission_types[$item->type_id]->params->get('properties.item_title')):?>
                      <div class="pull-left">
                        <<?php echo $params->get('tmpl_core.title_tag', 'h2');?> class="record-title">
                          <?php if($params->get('tmpl_core.item_link')):?>
                            <a <?php echo $item->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo JRoute::_($item->url);?>">
                              <?php echo $item->title?>
                            </a>
                          <?php else :?>
                            <?php echo $item->title?>
                          <?php endif;?>
                          <?php echo CEventsHelper::showNum('record', $item->id);?>
                        </<?php echo $params->get('tmpl_core.title_tag', 'h2');?> class="record-title">
                      </div>
                    <?php endif;?>
                  </div>
                </td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_rating')):?>
                <td nowrap="nowrap" valign="top">
                  <?php echo $item->rating?>
                </td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_author_avatar')):?>
                <td>
                  <img src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40));?>" />
                </td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_author') == 1):?>
                <td nowrap="nowrap"><?php echo CCommunityHelper::getName($item->user_id, $this->section);?>
                <?php if($params->get('tmpl_core.item_author_filter') /* && $item->user_id */):?>
                  <?php echo FilterHelper::filterButton('filter_user', $item->user_id, NULL, JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, true)), $this->section);?>
                <?php endif;?>
                </td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_type') == 1):?>
                <td nowrap="nowrap"><?php echo $item->type_name;?>
                <?php if($params->get('tmpl_core.item_type_filter')):?>
                  <?php echo FilterHelper::filterButton('filter_type', $item->type_id, NULL, JText::sprintf('CSHOWALLTYPEREC', $item->type_name), $this->section);?>
                <?php endif;?>
                </td>
              <?php endif;?>

              <?php foreach ($this->total_fields_keys AS $field):?>
                <?php if(in_array($field->key, $exclude)) continue; ?>
                <td class="<?php echo $field->params->get('core.field_class')?>"><?php if(isset($item->fields_by_key[$field->key]->result)) echo $item->fields_by_key[$field->key]->result ;?></td>
                <?php if($field->id == 71){ $item->startDate = $field->value;}?>
                <?php if($field->id == 72){ $item->endDate = $field->value;}?>
              <?php endforeach;?>

              <?php if($params->get('tmpl_core.item_user_categories') == 1 && $this->section->params->get('personalize.pcat_submit')):?>
                <td><?php echo $item->ucatname_link;?></td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_categories') == 1 && $this->section->categories):?>
                <td><?php echo implode(', ', $item->categories_links);?></td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_ctime') == 1):?>
                <td><?php echo JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format'));?></td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_mtime') == 1):?>
                <td><?php echo JHtml::_('date', $item->modify, $params->get('tmpl_core.item_time_format'));?></td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_extime') == 1):?>
                <td><?php echo ( $item->expire ? JHtml::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : JText::_('CNEVER'));?></td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_comments_num') == 1):?>
                <td><?php echo CommentHelper::numComments($this->submission_types[$item->type_id], $item);?></td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_favorite_num') == 1):?>
                <td><?php echo $item->favorite_num;?></td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_vote_num') == 1):?>
                <td><?php echo $item->votes;?></td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_follow_num') == 1):?>
                <td><?php echo $item->subscriptions_num;?></td>
              <?php endif;?>

              <?php if($params->get('tmpl_core.item_hits') == 1):?>
                <td><?php echo $item->hits;?></td>
              <?php endif;?>

            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <!-- /End Original Data Table -->
      <?php if(!$isAdmin): ?>
    </div>
  </div>
</div>
<?php endif;?>

<?php if(!$isAdmin): ?>
<!-- Dialog Template Goes Here -->
    <!--For cancel form-->
    <div id="request-plan-form" class="modal hide fade" role="dialog" aria-hidden="true">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">
          <span class="cancel-form-element"><?php echo JText::_("COM_REQUEST_CANCEL_FORM_TITLE");?></span>
          <span class="resubmit-form-element"><?php echo JText::_("COM_REQUEST_RESUBMIT_FORM_TITLE");?></span>
        </h3>
      </div>
      <div class="modal-body">
        <form id="request-diloag-form" action="">
          <div class="request-tips cancel-form-element">
            <?php echo JText::_('COM_REQUEST_CANCEL_TIPS');?>
          </div>
          <div>
            <div class="span4" style="margin-right:10px;">
        <p></p>
              <div class="span8 request-form-detail-box" style="height:218px">
                <div class="request-form-product">
                  <img class="request-product-thumbnail" alt=""/>
                  <span class="request-product-title"></span>
                </div>
                <div class="plan-block">
                  <b style="width:50px;display:inline-block;"><?php echo JText::_('COM_REQUEST_PLAN');?>:&nbsp;</b><span class="request-plan-title"></span><br/>
                    <span style="width:50px;display:inline-block;"></span><span class="request-plan-dlimit"></span><?php echo ' calls per '.substr($request_item->plan->fields[80]
                            , strpos($request_item->plan->fields[80],'/')+1);?><br/>
                  <span style="width:50px;display:inline-block;"></span><span class="requestSlimit"><span class="request-plan-slimit"></span><?php echo ' calls per '.substr($request_item->plan->fields[79]
, strpos($request_item->plan->fields[79],'/')+1);?></span>
                  <span style="width:50px;display:inline-block;"></span><span class="request-plan-dlimit"></span><?php echo $concurrent_calls; ;?><br/>
                </div>

              </div>
              <div class="span12">
                <b><?php echo JText::_('COM_REQUEST_STATUS_HEAD_REQUEST_BY');?>:</b> <span class="requested_by"></span>
              </div>
              <div class="clearfix"></div>
              <div class="control-group">
                <div class="resubmit-form-element">
                  <label><input type="checkbox" name="is_email" class="is_email" value="1"> <?php echo JText::_('COM_REQUEST_NOTIFY_VIA_EMAIL_TIPS');?></label>
                </div>
              </div>
            </div>
            <div class="span7">
              <div class="control-group request-custom-block">
                  <div>
                    <label for="dlimit">
                      <?php echo JText::_("PLAN_REQUEST_PER_SENCONDS"); ?>
                    </label>
                    <input id="request_rate_limit" name="rate_limit" type="text" />
                    <div style="display:none;margin-right:5px;" class="alert alert-error"></div>
                  </div>
                  <div>
                    <label for="burst">
                      <?php echo JText::_("PLAN_REQUEST_PER_DAY"); ?>
                    </label>
                    <input id="request_quota_limit" name="quota_limit" type="text" />
                    <div style="display:none;margin-right:5px;" class="alert alert-error"></div>
                  </div>
              </div>
              <div class="control-group request-note-block" >
                <label for=""><?php echo JText::_('COM_REQUEST_NOTE_COMMENT_LABEL');?></label>
                <textarea name="user_note" id="" style="height:205px"></textarea>
                <div class="control-group" style="text-align:right;">
                  <input type="hidden" name="option" value="com_request">
                  <input type="hidden" name="id" value="">
                  <input type="hidden" name="task" value="lists.toCancel">
                  <button class="btn submit-request-form"></button>
                  <button class="btn cancel-request-form" data-dismiss="modal" aria-hidden="true"></button>
                </div>
              </div>
            </div>
            <div class="span12">

            </div>
          </div>
        </form>
      </div>
    </div>


<!-- Dialog Template End Here -->
<script type="text/javascript">

  (function($){
    $(function(){

      $("[id='request-plan-form']").find("input[type!='hidden']:not(':disabled')").on('focus', function(){
        var alertBox = $(this).next("div.alert:visible");
        if(alertBox.length){
          alertBox.hide();
          $(this).val("");
        }
      });
      function _resetRequestForm(obj){
        $(obj).find(".request-product-thumbnail").attr('src','');
        $(obj).find(".request-product-title").text('');
        $(obj).find(".request-plan-title").text('');
        $(obj).find(".request-plan-dlimit").text('');
        $(obj).find(".request-plan-slimit").text('');
        $(obj).find(".request-plan-slimit").text('');
        $(obj).find(".requested_by").text('');
        $(obj).find(".request-tips").hide();
        $(obj).find("input[name='id']").val('');
        $(obj).find("input[name='task']").val('');
        $(obj).find("textarea[name='user_note']").val('');
        $(obj).find(".cancel-form-element,.resubmit-form-element").hide();
        $(obj).find(".submit-request-form").text("");
        $(obj).find("input[name='rate_limit']").val('');
        $(obj).find("input[name='quota_limit']").val('');
        $(obj).find(".request-custom-block").hide();
        $(obj).find(".request-custom-block div.alert").hide();
      }

      function _initRequestForm(obj,data){
        var task = '';
        $(obj).find(".request-product-thumbnail").attr("src",data.request_product_thumbnail);
        $(obj).find(".request-product-title").text(data.request_product);
    var tipsStr = $(obj).find(".request-tips").html();
    $(obj).find(".request-tips").html(tipsStr.replace(/Product pro/,data.request_product));
        $(obj).find(".request-plan-title").text(data.request_plan_name);
        $(obj).find(".request-plan-dlimit").text(data.request_dlimit);
        $(obj).find(".request-plan-slimit").text(data.request_slimit);
        $(obj).find(".requested_by").text(data.request_user_name);
        $(obj).find("input[name='id']").val(data.request_id);
        $(obj).find("textarea[name='user_note']").val(data.user_note);
        if(data.request_status == 1)
        {
          $(obj).find(".request-tips").show();
        }

        /**
         * 1 pending
         * 2 approve
         * 3 reject
         * 4 cancel
         */

        switch(data.request_status - 0) {
          case 1:
              task = "lists.ToCancel";
              $(obj).find(".cancel-form-element").show();
              $(obj).find(".submit-request-form").text(Joomla.JText._('COM_REQUEST_CONFIRM_CANCEL'));
              $(obj).find(".cancel-request-form").text(Joomla.JText._('COM_REQUEST_DISCARD_CANCEL'));
            break;
          case 4:
              task = "lists.reSubmit";
              $(obj).find(".resubmit-form-element").show();
              $(obj).find(".submit-request-form").text(Joomla.JText._('COM_REQUEST_CONFIRM_RESUBMIT'));
              $(obj).find(".cancel-request-form").text(Joomla.JText._('COM_REQUEST_CANCEL_RESUBMIT'));

              if($(obj).find(".request-plan-title").text().toLowerCase() === "custom")
              {
                $(obj).find("input[name='rate_limit']").val(data.request_slimit);
                $(obj).find("input[name='quota_limit']").val(data.request_dlimit);
                $(obj).find(".request-custom-block").show();
              }

            break;
        }

        $(obj).find("input[name='task']").val(task);
      }

      $("#adminForm").prependTo("#sub-ctl");
    $("#adminForm").css({
      "height":"35px",
      "line-height":"35px",
      "background-color":"white",
      "padding":"0px",
      "margin":"0px",
      "margin-right":"1px",
      "box-shadow":"none"
    });
    $("#adminForm").find(".search-form").css({
      "padding":"0px",
      "margin":"0px",
      "margin-right":"10px",
      "float":"none"
    });
    $("#adminForm").find(".order-by").css({
      "padding":"0px",
      "margin":"0px"
    });
    $("#adminForm").find(".order-by a.dropdown-toggle").css({
      "margin":"0px",
      "height":"30px"
    });

      $(".termsToggle").click(function(e) {
        $(this).parent().find(".termsBlock").toggle();
        return false;
      });

      $(".request-dialog").on("click",function(){
        $(this).addClass("clicked");
        var requestData = jQuery.parseJSON(jQuery('.request-dialog').attr('data-request'));
        if(requestData.request_slimit == 0){
           jQuery('.requestSlimit').remove();
        }
        $("#request-plan-form").modal('show');

      });

      $("#request-plan-form").on("show",function(){
        var data = $(".request-dialog.clicked").data("request");
        _resetRequestForm(this);
        _initRequestForm(this,data);
      });

      $("#request-plan-form").on("hidden",function(){
        _resetRequestForm();
        $(".request-dialog").removeClass("clicked");
      });

      $(".submit-request-form").on("click",function(){
        var data;
        var flag  =  true;
        var requestForm = $(this).parent().parent().parent().parent().parent();
        var dlimit = requestForm.find("input[name='rate_limit']");
        var slimit = requestForm.find("input[name='quota_limit']");

        data = $("#request-diloag-form").serialize();

        if(requestForm.find(".request-custom-block:visible").length && requestForm.find(".request-plan-title").text().toLowerCase() === "custom"){
          if(dlimit.length && !/^[1-9]\d{0,}$/.test(dlimit.val())){

            requestForm.find('.modal-header').addClass('alert-error');
            requestForm.find('#myModalLabel').text("<?php echo JText::_('PLAN_REQUEST_INVALID_DLIMIT'); ?>");
            dlimit.next("div.alert").text("<?php echo JText::_('PLAN_REQUEST_INVALID_DLIMIT');?>").slideDown();
            flag = false;
          }

          if(slimit.length && !/^[1-9]\d{0,}$/.test(slimit.val())){

            requestForm.find('.modal-header').addClass('alert-error');
            requestForm.find('#myModalLabel').text("<?php echo JText::_('PLAN_REQUEST_INVALID_DLIMIT'); ?>");
            slimit.next("div.alert").text("<?php echo JText::_('PLAN_REQUEST_INVALID_DLIMIT');?>").slideDown();
            flag = false;
          }

          if(!flag){
            return false;
          }
        }


        $.post(".", data, function(res){
          if(res.success)
          {
            window.location.reload();
            $("#request-plan-form").modal('hide');
          }
        },'json');

        return false;
      });



      // Bind keypress event to the Quota Limit and Rate Limit, the max value is 4294967295
      $(document).on('keypress', '#request-plan-form [name="rate_limit"], #request-plan-form [name="quota_limit"]', function(evt) {
        if ((evt.charCode>=48&&evt.charCode<=57) || evt.charCode==0) {
          if (evt.keyCode == 13) {
            return false;
          } else {
            var input = String.fromCharCode(evt.charCode);
            var newVal = parseInt($(this).val() + input);
            if (newVal>4294967295) {
              $(this).val('4294967295');
              return false;
            }
          }
        } else {
          return false;
        }
      });
      $(document).on('paste', '#request-plan-form [name="rate_limit"], #request-plan-form [name="quota_limit"]', function(evt) {
        return false;
      });

    //toggle controls for tabs
    $(document).on('click','#subscriptions-tab .nav-tabs li a',function(){
      var hrefVal = $(this).attr('href');
      if (hrefVal == '#subscriptions-request-list-container') {
        $('#sub-ctl').hide();
        $('#req-ctl').show();
      }else if(hrefVal == '#subscriptions-subscripiton-list-container'){
        $('#sub-ctl').show();
        $('#req-ctl').hide();
      }
    });

    //change background-color for requests
    $(document).on('click','.accordion-heading',function(){
      if ($(this).next(".accordion-body").height()==0) {
        $(this).css("background-color","#fffef3");
      }else {
        $(this).css("background-color","white");
      }
    });
    });

  })(jQuery);


</script>
<?php endif; ?>

