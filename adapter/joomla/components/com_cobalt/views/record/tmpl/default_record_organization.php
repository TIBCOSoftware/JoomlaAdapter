<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
 
 /* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */

defined('_JEXEC') or die();
require_once JPATH_BASE . "/includes/api.php";

//PHP Variable Definitions

$item = $this->item;


$params = $this->tmpl_params['record'];

$icons = array();
$category = array();
$author = array();
$details = array();
$tasks_to_hide = array();

$started = FALSE;
$i = $o = 0;

$membersValue = (object)null;
$contactInfo = (object)null;
$subscriptionsValue = (object)null;
$appsValue = (object)null;
$threshold = (object)null;

$thresholdValue = '99';

$org_admin_group_id = TibcoTibco::getGroupByOrgAndType($this->item->id, DeveloperPortalApi::USER_TYPE_MANAGER);

$auth_group_ids = $this->user->getAuthorisedGroups();

$path = JRequest::getURI();

$current_user_org_id = TibcoTibco::getCurrentUserOrgId();


//Access Control
if(!($current_user_org_id == $this->item->id || in_array(7, $auth_group_ids) || in_array(8, $auth_group_ids))){
  	JFactory::getApplication()->enqueueMessage(JText::_('USERPROFILE_CUSTOM_ACCESS_DENIED_ERROR'), 'error');
   return;
  }

//Enabling Deleting Objects
if(!in_array(8, $auth_group_ids) || JComponentHelper::getParams("com_emails")->get("enable_deleting_objects") != 1) {
  $tasks_to_hide = array(DeveloperPortalApi::TASK_DELETE);
}

// Variables?
if(isset($this->item->fields_by_groups[null])){
	foreach ($this->item->fields_by_groups[null] as $field_id => $field){
		$started = true;
		if ($field->id == 56) {
			$membersValue = $field;
		}else if ($field->id == 20) {
			$contactValue = $field->value;
		}else if ($field->id == 48) {
			$contactInfo = $field;
		}else if ($field->id == 74) {
			$subscriptionsValue = $field;
		}else if ($field->id == 61) {
			$appsValue = $field;
		}else if ($field->id ==130){
			$threshold = $field;
			if (!empty($threshold->value)) {
				$thresholdValue = $threshold->value;
			}
		}
	}
}

//Maybe Start a Function here

//Setting the JoomlaRoot to a JS variable.
$hasJoomlaRoot= JUri::base(true);

if($hasJoomlaRoot){

	?>
	<script type="text/javascript">

	var hasJoomlaRoot='<?php echo $hasJoomlaRoot;?>';

	</script>
	<?php
}

else{

	?>
	<script type="text/javascript">
	var hasJoomlaRoot=false;
	</script>
	<?php
}

?>

<!-- Starting to Make the Page in HTML -->
<style>
#app-list .alert-container
{
  width:100%;

}


#app-list .org-container
{
  margin-top:18px;
}

.application_box
{
  width:100%;
}

.contact-info
{
  margin-left:0;
  margin-right:0;
  width:23%;
  float:left;
}

.contact-info tbody tr td:last-child
{
  background-color:#ebebeb;
  line-height:26px;
  padding:0 5px;
}

.ctrl-org
{
  margin:12px 10px 0 0;
}

.dl-horizontal dd
{
  margin-bottom:10px;
}

.inline-doc
{
  padding:0;
}

.inline-doc h2
{
  cursor:pointer;
  background-color:#f6f6f6;
  font-weight:400;
  margin:0;
  padding:20px 0;
}

.inline-doc h2 span
{
  display:inline-block;
  height:100%;
  padding-left:30px;
  font-size:25px;
  line-height:25px;
  color:#333;
}

.inline-doc-content
{
  border:0;
  padding:4%;
}

.inline-doc-content dl
{
  margin-bottom:30px;
  height:429px;
  overflow:auto;
  padding:0 30px;
}

.inline-doc-content dl dd
{
  margin-top:10px;
  margin-left:0;
  text-indent:0;
  font-size:14px;
  line-height:18px;
  color:#666;
}

.inline-doc-content dl dd.time
{
  font-size:8px;
  color:#888;
  text-transform:uppercase;
}

.inline-doc-content dl dt
{
  margin-top:20px;
  padding-right:10px;
  overflow:hidden;
  font-size:12px;
  font-weight:700;
  color:#000;
}

.line-brk
{
  margin-left:0!important;
}

.mem-email
{
  max-width:200px;
}

.mem-email a
{
  float:left;
}

.mem-email a.btn
{
  float:right;
  margin-right:8%;
}

.mem-email p
{
  margin:0;
}

.member-details
{
  margin-right:2%;
  width:68%;
  float:left;
}

.menu-bar
{
  width:100%;
  height:45px;
  background-color:#eee;
  box-shadow:inset 0 0 20px rgba(0,0,0,.1);
}

.menu-bar button
{
  margin-top:10px;
  margin-left:10px;
}

.o-useage
{
  width:108px;
  height:12px;
  background-color:#e1e1e1;
  border:1px solid #cbced4;
  float:left;
  margin-top:2px;
  margin-bottom:4px;
}

.o-useage span
{
  display:inline-block;
  height:12px;
  line-height:12px;
}

.org-container .left-top .inline-doc-content
{
  min-height:150px;
}

.org-detail-table
{
  width:92%;
  border-spacing:20px;
}

.org-detail-table tbody tr
{
  margin:15px 0;
}

.org-detail-table tbody tr td
{
  font-size:11px;
  color:#2d2d2d;
  line-height:20px;
  vertical-align:top;
  padding:5px 0;
}

.org-detail-table tbody tr td:last-child
{
  line-height:15px;
  width:27%;
}

.org-detail-table.member-details tbody tr td:last-child
{
  width:12%;
}

.tag_list li
{
  display:block;
  float:left;
  list-style-type:none;
  margin-right:5px;
}

.tag_list li *
{
  line-height:30px;
  margin:0;
  padding:0;
}

.tag_list li a
{
  background-color:#F2F8FF;
  border-radius:8px;
  border:1px solid #445D83;
  color:#000;
  text-decoration:none;
  padding:5px 10px;
}

.tag_list li a:HOVER
{
  color:#000;
  text-decoration:underline;
}

.tag_list li#tag-first
{
  line-height:30px;
}

td.o-plan-detail span
{
  float:left;
  width:48px;
  height:27px;
  line-height:27px;
  overflow:hidden;
  margin-right:6px;
  margin-top:5px;
  color:#fff;
  text-transform:uppercase;
  text-align:center;
}

td.o-plan-detail span.line0
{
  background-color:#aba000;
}

td.o-plan-detail span.line1
{
  background-color:#a3620a;
}

td.o-plan-detail span.line2
{
  background-color:#898989;
}

td.o-plan-detail span.line3
{
  background-color:#448ccb;
}

td.time span
{
  display:inline-block;
  width:40px;
}

th
{
  text-align:left;
}

th span
{
  width:92%;
  font-size:10px;
  color:#474747;
  font-weight:700;
  border-bottom:1px solid #d2d5db;
  display:inline-block;
}
</style>
<article class="<?php echo $this->appParams->get('pageclass_sfx')?><?php if($item->featured) echo ' article-featured' ?>">
<?php if($params->get('tmpl_core.item_title')):?>
		<?php if($this->type->params->get('properties.item_title')):?>
			<div class="page-header">
				<<?php echo $params->get('tmpl_params.title_tag', 'h1')?>>
					<?php echo $item->title?>
					<?php echo CEventsHelper::showNum('record', $item->id);?>
				</<?php echo $params->get('tmpl_params.title_tag', 'h1')?>>
			</div>
		<?php endif;?>
	<?php endif;?>
<div class="clearfix"></div>

<!-- Starts Menu Bar -->
<div class="menu-bar">
	<?php if(!$this->print):?>
	<?php if($this->user->authorise('core.admin')):?>

	<?php

	if ($current_user_org_id == $this->item->id){
	?>
	<!-- Resync Button -->
    <button class="btn btn-resync resync-org pull-left" onclick="DeveloperPortal.resync('<?php echo $this->item->id; ?>', 'Organization');"><?php echo JText::_('RESYNC');?></button>
    
    <!-- Show Statistics Button -->
	<a href="<?php echo rtrim(JURI::root(), "/");?>/spotfire.php" class="btn btn-statistics pull-left" style="margin: 10px;" target="_blank"><?php echo JText::_('Show Statistics');?></a>

    <!-- System Message Alert Divs -->
	<div class="hidden"><div id="system-message" class="alert alert-info"><a class="close" data-dismiss="alert">&#215;</a><h4 class="alert-heading"><?php echo JText::_('INFO');?></h4><div><p><?php echo JText::_('RESYNC_COMPLETE');?></p></div></div></div>
    <?php
	}

	endif;
	else:
	?>

		<div class="pull-right controls">
			<a href="#" class="btn btn-mini" rel="tooltip" data-original-title="<?php echo JText::_('CPRINT');?>" onclick="window.print();return false;"><?php echo HTMLFormatHelper::icon('printer.png');  ?></a>
		</div>
	<?php endif;?>

	

<!--Settings Button and ICON-->
<div class="pull-right controls ctrl-org">
	<div class="btn-group">
		<?php if($params->get('tmpl_core.item_print')):?>
			<a class="btn btn-mini" rel="tooltip" data-original-title="<?php echo JText::_('CPRINT');?>" onclick="window.open('<?php echo JRoute::_($this->item->url.'&tmpl=component&print=1');?>','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
				<?php echo HTMLFormatHelper::icon('printer.png');  ?></a>
		<?php endif;?>

		<?php if($this->user->get('id')):?>
			<!--Checks for Access Control on Edit-->
			<?php if($item->controls && (in_array(8, $auth_group_ids) || in_array($org_admin_group_id, $auth_group_ids))):

			//Removing Moderator from the UI controls
			unset($item->controls[6]);
			?>
 	
	<a href="#" data-toggle="dropdown" class="dropdown-toggle btn-mini">
		<?php echo HTMLFormatHelper::icon('gear.png');  ?></a>
				<ul class="dropdown-menu">
					<?php echo DeveloperPortalApi::list_controls($item->controls, $tasks_to_hide, $this->item->id, $this->item->type_id);?>
				</ul>
			<?php endif;?>
		<?php endif;?>
	</div>
</div>
		</div>
<!-- Ends Menu Bar -->

<div id="app-list">

<!-- Begin the Alerts Box Container if this is the dashboard page -->

<?php
	
	if ($current_user_org_id == $this->item->id)
	{

	?>
	<div class="org-container alert-container">

		<div class="inline-doc">

		<!-- Alert Box Title and UpandDownArrow embedded in the span css (Global LAnguage usage is what Jtext is it can be updated at Joomla\language\en-GB\en-GB.tpl_openapi.ini-->
			<h2><span id="alert-title"><img src="<?php echo JURI::root(); ?>/images/icons/128x128/Alert_ic_128x128.png" style="vertical-align: middle; height:40px;" hspace="0" vspace="0">
			<?php 
			echo JText::_('DASHBOARD_ALERTS');
			?></span></h2>

			<div class="inline-doc-content">

				<dl id="alert-msg" orgID="<?php echo $this->item->id; ?>">

					<dt><?php echo JText::_('DASHBOARD_PLACEHOLDER_ALERT');?></dt>

					<dd class="time"><?php echo date('Y-m-d H:i:s'); ?></dd>

				</dl>

			</div>

		</div>

	</div>

	<?php
	}
	?>

<!-- Start of the Applications Box -->

	<div class="org-container pull-left application_box">
		<div class="inline-doc active left-top">
			<h2><span>Applications</span></h2>
			<div class="inline-doc-content">
				<table class="org-detail-table">
					<thead>
						<tr>
							<th><span><?php echo JText::_('DASHBOARD_APPLICATIONS');?></span></th>
							<th><span><?php echo JText::_('DASHBOARD_PRODUCT');?></span></th>
							<th><span><?php echo (JText::_('DASHBOARD_PLAN'));?></span></th>
							<th style="width:30%;"><span><?php echo JText::_('DASHBOARD_USEAGE');?></span></th>
						</tr>
					</thead>
					<tbody>
						<!--Get Data for Application Names-->
						<?php if (isset($appsValue->content['list'])): ?>
							<?php foreach ($appsValue->content['list'] as $key => $value): ?>
							<tr style="<?php echo array_search($key, array_keys($appsValue->content['list']))%2==0?'':'background-color:#f6f6f6;'; ?>">
								<td class="app-name" appID="<?php echo $value->id; ?>"><a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=record&id='.$value->id);?>"><?php echo $value->title; ?></a></td>
								
								<!-- Put in Plan Names -->

								<td><?php echo JText::_('DASHBOARD_PLACEHOLDER_LOADING');?></td>
								<td><?php echo JText::_('DASHBOARD_PLACEHOLDER_LOADING');?></td>
								<td></td>
							</tr>
							<?php endforeach; ?>
						<?php endif ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<!-- Start of the Subscription Box -->
	<div class="org-container pull-left application_box">
		<div class="inline-doc active left-top">
			<h2>
				<span><?php echo $subscriptionsValue->label?$subscriptionsValue->label:JFactory::getApplication()->getMenu()->getItem(114)->title; ?></span>
			</h2>
			<div class="inline-doc-content">
				<table class="org-detail-table">
					<thead>
						<tr>
							<th><span><?php echo JText::_('DASHBOARD_SUBSCRIPTION_PRODUCT');?></span></th>
							<th  style="width: 16%;"><span><?php echo JText::_('DASHBOARD_SUBSCRIPTION_PERIOD');?></span></th>
							<th><span><?php echo JText::_('DASHBOARD_QUOTA');?></span></th>
							<th><span><?php echo JText::_('DASHBOARD_USEAGE');?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php if (isset($subscriptionsValue->content['list'])): ?>
							<?php foreach ($subscriptionsValue->content['list'] as $key => $value): ?>
								<tr style="<?php echo array_search($key, array_keys($subscriptionsValue->content['list']))%2==0?'':'background-color:#f6f6f6;'; ?>">
									<td style="max-width:150px; padding-right:1%;"><a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=record&id='.$value->id);?>"><?php echo $value->title; ?></a></td>
									<td class="time"><?php echo '<span>From</span>'.$value->fields[71][0].'<br/>'.'<span>To</span>'.$value->fields[72][0]; ?></td>
									<td class="o-plan-detail" planID="<?php echo $value->fields[69]; ?>" subID="<?php echo $value->id; ?>">Loading...</td>
									<td><div class="o-useage" subID="<?php echo $value->id; ?>">Loading...</div></td>
								</tr>
							<?php endforeach; ?>
						<?php endif ?>
					</tbody>
				</table>
			</div>
		</div>
    <?php echo $subscriptionsValue->result; ?>
	</div>
	<div class="clearfix"></div>

	<div class="org-container">
		<div class="inline-doc active">
			<h2>
				<span><?php echo $membersValue->label; ?></span>
			</h2>
			<div class="inline-doc-content">
				<table class="org-detail-table member-details">
					<thead>
						<tr>
							<th><span><?php echo JText::_('DASHBOARD_NAME');?></span></th>
							<th><span><?php echo JText::_('DASHBOARD_EMAIL');?></span></th>
							<th><span><?php echo JText::_('DASHBOARD_ROLE');?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php if (isset($membersValue->content['list'])): ?>
							<?php foreach ($membersValue->content['list'] as $key => $value): ?>
							<tr>
								<td><a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=record&id=' . $value->id);?>"><?php echo $value->title; ?></a></td>
								<td class="mem-email" id="mem<?php echo $key; ?>"><?php echo $value->fields[102]; ?></td>
								<td><?php  if (is_array($value->fields[88])){
									echo $value->fields[88][0];
								}else {
									echo $value->fields[88];
								}  ?></td>
							</tr>
							<?php endforeach; ?>
						<?php endif ?>
					</tbody>
				</table>
				<table class="org-detail-table contact-info">
					<thead>
						<tr>
							<th><span style="width:100%;"><?php echo JText::_('DASHBOARD_CONTACT');?></span></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $contactInfo->content['list'][0]->fields[45].' '.$contactInfo->content['list'][0]->fields[46].'<br/>'.$contactInfo->content['list'][0]->fields[102]; ?><br/><?php echo $contactValue['address']['address1'].' '.$contactValue['address']['address2'].' '.$contactValue['address']['zip'].'<br/>'.$contactValue['address']['city'].' '.$contactValue['address']['state'].'<br/>'.($contactValue['contacts']['tel']?"Phone:":"").$contactValue['contacts']['tel']; ?></td>
						</tr>
					</tbody>
				</table>
				<div class="clearfix"></div>
			</div>
		</div>
    <?php echo $membersValue->result; ?>
	</div>
	<div class="clearfix"></div>
</div>

<script>
	(function ($) {
		$("table.table-striped:last").find("tbody tr td:last-child").each(function(i){
			var cloneInfo = $(this).clone();
			cloneInfo.find("script").remove();
			$("td#mem"+i).html(cloneInfo.html());
		});
		$("table.table-striped").remove();
	})(jQuery);
</script>

<!-- <?php if(in_array($params->get('tmpl_params.item_grouping_type', 0), array(1)) && count($this->item->fields_by_groups)):?>
<div class="clearfix"></div>
<div class="tabbable <?php echo $params->get('tmpl_params.tabs_position');  ?>">
	<ul class="nav <?php echo $params->get('tmpl_params.tabs_style', 'nav-tabs');  ?>" id="tabs-list">
		<?php if(isset($this->item->fields_by_groups)):?>
			<?php foreach ($this->item->fields_by_groups as $group_id => $fields) :?>
				<li><a href="#tab-<?php echo $o++?>" data-toggle="tab"> <?php echo HTMLFormatHelper::icon($item->field_groups[$group_id]['icon'])?> <?php echo JText::_($group_id)?></a></li>
			<?php endforeach;?>
		<?php endif;?>
	</ul>
<?php endif;?> -->

<?php if($started):?>
	<script type="text/javascript">
		<?php if(in_array($params->get('tmpl_params.item_grouping_type', 0), array(1))):?>
			jQuery('#tabs-list a:first').tab('show');
		<?php elseif(in_array($params->get('tmpl_params.item_grouping_type', 0), array(2))):?>
			jQuery('#tab-main').collapse('show');
		<?php endif;?>
	</script>
<?php endif;?>

<!-- Start one Huge JS call -->

<script type="text/javascript">

    (function ($) {

    	//Collapse the Apps/Alerts/Members/Subs Boxes
        $('.inline-doc h2').click(function (e) {
            if($(this).parent().hasClass("active")) {
                $(this).parent().removeClass("active");
            }else {
                $(this).parent().addClass("active");
            }
        });

		
		//Function for removing alert messages
		removeAlert = function(e) {	

		  postData = {};
		  postData.uuid = $(e).parent().attr("id");

		  $.post(GLOBAL_CONTEXT_PATH+"index.php?option=com_cobalt&task=ajaxMore.unpublishDashboardAlertMessage", postData , function (response) {
     		 alert(response);
   			},'json');

		  $(e).parent().remove();

		  $('#alert-title').prop('count', ($('#alert-title').prop('count') - 1))

		  $('#alert-title').html( '<img src="<?php echo JURI::root(); ?>/images/icons/128x128/Alert_ic_128x128.png" style="vertical-align: middle; height:40px;" hspace="0" vspace="0">' + ' ' + $('#alert-title').prop('count') + ' New Alerts ' );

		}

		//load alerts
		loadAlerts($('dl#alert-msg'));

		//load products / Load App Box
		loadProducts($.makeArray($('.app-name')));

		//load PlanDetails // Load Sub Box
		loadPlanDetail($.makeArray($('td.o-plan-detail')));


		function loadProducts(apps){
			if (apps.length>0) {
				app_ids = [];
				$(apps).each(function(i){
					app_ids.push($(this).attr('appID'));

				});

				//filter out any null or zero values so it doesn't mess up the UI down the Line.
				app_ids = app_ids.filter(Number);

			  	$.post(
		GLOBAL_CONTEXT_PATH+"index.php?option=com_cobalt&task=ajaxMore.getDashboardData",
				  {'app_ids':app_ids},
				  function(data){
					$(data.result).each(function(row){
						node = $(apps[row]);
					  	products = '';
					  	subscriptions_id_array = [];
						plans = '';
						total = [];

						//console.log(data.result);

						$(this.products).each(function(i){
							products += $(this).attr('title')+'<br/>';
						});
						
						$(this.application_subscriptions).each(function(i){
							subscriptions_id_array.push($(this).attr('plan_ids')) ;
						});	

													//Render Plan Names for Application 
						$(this.plans).each(function(i){
							if ($.inArray($(this).attr('id'), subscriptions_id_array) !== -1)
							{
							plans += $(this).attr('plan_title')+'<br/>';

							jsonObj = JSON.parse($(this).attr('plan_fields'));
                            var quotaVal = jsonObj[80] || "";
                            var rateVal = jsonObj[79] || "";
							if(jsonObj[152]){
							var concurrent_calls = jsonObj[152] +" "+ "<?php echo JText::_('CONCURRENT_CALLS'); ?>"
							}else{
							var concurrent_calls="";
							}
							total.push(quotaVal.replace('/1 day',''));
							}
						});

						//console.log('app');
						//console.log(data.result);
						//Render Useage for Application Box
						$(this.usage).each(function(i){
							node.next("td").next("td").next("td").append('<div class="o-useage rendered"></div>');
							usageNode = $('.rendered:last-child');
							usageValue = $(this).attr('current_usage');
							pctValue = $(this).attr('pct') || 0;
							renderUseage(usageNode, usageValue?usageValue:0,false,pctValue);
						});


						node.next("td").html(products);
						node.next("td").next("td").html(plans);
						
					});
					
				

				  },
			      'json'
			  	).fail(function(){
				
						
			  	});
			}
		}


		
		function loadPlanDetail(subs){
			if (subs.length>0) {

				//console.log(subs);
				sub_ids = [];
				plan_ids = [];
				postData = {};
				$(subs).each(function(i){
					sub_ids.push($(this).attr('subID'));
					plan_ids.push($(this).attr('planID'));
				});
				postData.sub_ids = sub_ids;
				if (plan_ids.length>0) {
					postData.plan_ids = plan_ids;
				}
			  	$.post(
	GLOBAL_CONTEXT_PATH+"index.php?option=com_cobalt&task=ajaxMore.getDashboardData",
				  postData,
				  function(data){
					  $(data.result).each(function(row){
					  	node = $(subs[row]);
					  	usage_value = [];
					  	usage_percent = [];
					  	//console.log('sub');
					  	//console.log(data.result);
							
						$(this.usage).each(function(i){
							usage_value[i] = $(this).attr('current_usage') || 0;
							usage_percent[i] = $(this).attr('pct') || 0;
							//console.log(usage_value);
						});
									

						$(this.plans).each(function(row){
							if(plan_ids && plan_ids.length>0){

								for(i=0;i<plan_ids.length;i++){
									jsonObj = JSON.parse(this.planDetail);
                                    var quotaVal = jsonObj[80] || "";
                                    var rateVal = jsonObj[79] || "";
									if(jsonObj[152]){
									var concurrent_calls = jsonObj[152] +" "+ "<?php echo JText::_('CONCURRENT_CALLS'); ?>"
									}else{
									var concurrent_calls="";
									}
                                    plan = quotaVal.replace('/',' calls per ')+'<br/>' + rateVal.replace('/',' calls per ') + '<br/>' + concurrent_calls;
									if(plan_ids[i]==this.id){
										node = $(subs[i]);
									node.html(plan);
									//node.prev('td').prev('td').html('<span class="line'+(row%4)+'">'+this.title+'</span>');
									total = quotaVal.replace('/1 day','');
									
									renderUseage(node.next("td").find("div"), usage_value[row], total, usage_percent[row]);
									}
									}

							}else {
								jsonObj = JSON.parse(this.planDetail);
	                            var quotaVal = jsonObj[80] || "";
	                            var rateVal = jsonObj[79] || "";
								if(jsonObj[152]){
								var concurrent_calls = jsonObj[152] +" "+ "<?php echo JText::_('CONCURRENT_CALLS'); ?>"
								}else{
								var concurrent_calls="";
								}
	                            plan = quotaVal.replace('/',' calls per ')+'<br/>'+rateVal.replace('/',' calls per ')+'<br/>'+concurrent_calls;
								node = $(subs[row]);
								node.html('<span class="line'+(row%4)+'">'+this.title+'</span>'+plan);
								}
							});

							//console.log(total);

								
									
					  });
			
				  },
			      'json'
			  	).fail(function(){

			  	});
			}else {

			}
		}

		//A function to Load System Alerts into the HTML SELECTOR that you send it.
		//alertNode - string (HTML SELECTOR)
		
		function loadAlerts(alertNode){
			org_id = alertNode.attr('orgID');
		  	$.post(
			  GLOBAL_CONTEXT_PATH+"index.php?option=com_cobalt&task=ajaxMore.alertMessages",
			  {'org_id':org_id},
			  function(data){
			  	 //var elements = '';
				  var elements = new Array();
				  $(data.result).each(function(){

					  var element = '', aTagStart = '', aTagEnd = '';
					  if (this.log_type == 'Request') {
						  aTagStart = '<a href="index.php/subscriptions">';
						  aTagEnd = '</a>';
					  }
					  //element = '<span id="'+this.uuid+'"><dt class="'+renderAlertType(this.event_status)+'">'+aTagStart+this.entity_type+' : '+ (this.event||"")+aTagEnd+'</dt><dd>'+aTagStart+ (this.summary||"")+aTagEnd+'</dd><dd class="time">'+this.create_time+'</dd> <button onclick="removeAlert(this)">Ignore</button></span>';
					  element = '<span id="'+this.uuid+'"><dt class="'+renderAlertType(this.event_status)+'">'+aTagStart+this.entity_type+' : '+ (this.event||"")+aTagEnd+'</dt><dd>'+aTagStart+ (this.summary||"")+aTagEnd+'</dd><dd class="time">'+this.create_time+'</dd></span>';
					  elements.push( element );
				
					  });
				  if (elements.length>0) {
				  	alertNode.html(elements);
				    //Update Alert Count
				  	//$('#alert-title').prop('count',elements.length);
				  	//$('#alert-title').html('<img src="<?php echo JURI::root(); ?>/images/icons/128x128/Alert_ic_128x128.png" style="vertical-align: middle; height:40px;" hspace="0" vspace="0">' + ' ' + elements.length + ' New Alerts ');
				  	
				  }
			  },
		      'json'
		  	).fail(function(){
				//void
		  	});
		}

		function renderUseage(node, value, total, pct){
			var percentage=Math.floor(pct*100)/100;
			var threshold= '<?php echo $thresholdValue; ?>';
			barWidth = node.width()*percentage/100;
			if (percentage<threshold ) {
				bgColor = '6c3';
			}else if (percentage>=threshold && (barWidth < node.width())) {
				bgColor = 'ff0';
			}else{
				bgColor = 'f66';
			}
			node.html('<span style="width:'+barWidth+'px;background:#'+bgColor+';"></span>');
				if (total){
					node.after( '<span style="margin-left:1%";>' +pct+'% </span>' + '<div style="float:left;height:14px;margin-top:2px;margin-left:2px;">'+value+' out of '+total+' calls </div><div class="clearfix"></div>');
				}
				else{
					node.after( '<span style="margin-left:1%";>' +pct+'% </span>' + '<div style="float:left;height:14px;margin-top:2px;margin-left:2px;"></div><div class="clearfix"></div>');
				}
			}

		function renderAlertType(typeString){
			var status=typeString.toLowerCase();
			var returnType = "";
			if (status==='error') {
				returnType = 'error';
			}else if (status==='success' || status==='complete') {
				returnType = 'normal';
			}else if (status==='portalalert') {
				returnType = 'alert1';
			}else if(status==='partially completed'){
				returnType = 'alert2';
			}

			return returnType;
		}
		

    })(jQuery);
</script>
<script>
  //reload send mail iframe
  jQuery("[id^='emailmodal']").on('hide', function() {
    var ifr = jQuery(this).find('iframe');
    ifr.attr('src', ifr.attr('src'));
  });
  
  <?php if (strrpos(JURI::current(),"/".JFactory::getApplication()->getMenu()->getItem(140)->alias."/")): ?>
  	jQuery("h1#banner-title-heading").html('<?php echo JFactory::getApplication()->getMenu()->getItem(140)->title; ?>');
  <?php endif ?>
</script>

<?php if(strpos($path,'userprofile') || strpos($path,'dashboard')):?>
<script type="text/javascript">
(function($){
	$(function(){
			var org_links_seletor,search_selector, mem_links;
			if(hasJoomlaRoot){
				 org_links_seletor="table.org-detail-table.member-details td>a[href^='"+hasJoomlaRoot+"\/index.php\/organizations\/']";
				 search_selector=new RegExp('^('+hasJoomlaRoot+'\/?index\.php\/)(organizations)(\/.*)$');
				  mem_links = $("a[href^='"+hasJoomlaRoot+"\/index.php\/organizations\/submit\/']");
				  mem_search_selector=new RegExp("^("+hasJoomlaRoot+"\/?index\.php\/)(organizations)(\/.*)$");
			}else{
				 org_links_seletor="table.org-detail-table.member-details td>a[href^='\/index.php\/organizations\/']";
				 search_selector=new RegExp("^(\/?index\.php\/)(organizations)(\/.*)$");
				 mem_links = $("a[href^='\/index.php\/organizations\/submit\/']");
				 mem_search_selector=new RegExp("^(\/?index\.php\/)(organizations)(\/.*)$");
				} 
			var org_links = $(org_links_seletor);
			org_links.each(function(i,ele){
				
				var new_link = $(ele).attr("href").replace(search_selector,function(m,p1,p2,p3){
						return p1+"dashboard"+p3;});
				$(ele).attr("href",new_link);						
			});
		
			mem_links.each(function(i,ele){
				var new_mem_link = $(ele).attr("href").replace(mem_search_selector,function(m,p1,p2,p3){
				        							return p1+"dashboard"+p3;
									});
				$(ele).attr("href",new_mem_link);						
			});
				
  });})(jQuery);
</script>
<?php endif;?>
<?php
function group_start($data, $label, $name)
{
	static $start = false;
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		//tab
		case 1:
			if(!$start)
			{
				echo '<div class="tab-content" id="tabs-box">';
				$start = TRUE;
			}
			echo '<div class="tab-pane" id="'.$name.'">';
			break;
		//slider
		case 2:
			if(!$start)
			{
				echo '<div class="accordion" id="accordion2">';
				$start = TRUE;
			}
			echo '<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#'.$name.'">
					     '.$label.'
					</a>
				</div>
				<div id="'.$name.'" class="accordion-body collapse">
					<div class="accordion-inner">';
			break;
		// fieldset
		case 3:
			echo "<legend>{$label}</legend>";
		break;
	}
}

function group_end($data)
{
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		case 1:
			echo '</div>';
		break;
		case 2:
			echo '</div></div></div>';
		break;
	}
}

function total_end($data)
{
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		//tab
		case 1:
			echo '</div>';
		break;
		case 2:
			echo '</div>';
		break;
	}
}
