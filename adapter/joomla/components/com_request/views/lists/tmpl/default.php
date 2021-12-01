<?php
// no direct access
defined('_JEXEC') or die;
require_once JPATH_BASE . "/includes/api.php";
JHtml::stylesheet('com_request/style.css', false, true, false);
JFactory::getDocument()->addScript(JURI::root(TRUE) . '/media/system/js/calendar.js');
JFactory::getDocument()->addScript(JURI::root(TRUE) . '/media/system/js/calendar-setup.js');
JFactory::getDocument()->addStyleSheet(JURI::root(TRUE) . '/media/system/css/calendar-jos.css');

?>
<script type="text/javascript">
    Calendar._DN = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
    Calendar._SDN = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
    Calendar._FD = 0;
    Calendar._MN = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    Calendar._SMN = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    Calendar._TT = {"INFO": "About the Calendar", "ABOUT": "DHTML Date\/Time Selector\n(c) dynarch.com 2002-2005 \/ Author: Mihai Bazon\nFor latest version visit: http:\/\/www.dynarch.com\/projects\/calendar\/\nDistributed under GNU LGPL.  See http:\/\/gnu.org\/licenses\/lgpl.html for details.\n\nDate selection:\n- Use the \u00ab and \u00bb buttons to select year\n- Use the < and > buttons to select month\n- Hold mouse button on any of the above buttons for faster selection.", "ABOUT_TIME": "\n\nTime selection:\n- Click on any of the time parts to increase it\n- or Shift-click to decrease it\n- or click and drag for faster selection.", "PREV_YEAR": "Click to move to the previous year. Click and hold for a list of years.", "PREV_MONTH": "Click to move to the previous month. Click and hold for a list of the months.", "GO_TODAY": "Go to today", "NEXT_MONTH": "Click to move to the next month. Click and hold for a list of the months.", "SEL_DATE": "Select a date.", "DRAG_TO_MOVE": "Drag to move", "PART_TODAY": " Today ", "DAY_FIRST": "Display %s first", "WEEKEND": "0,6", "CLOSE": "Close", "TODAY": "Today", "TIME_PART": "(Shift-)Click or Drag to change the value.", "DEF_DATE_FORMAT": "%Y-%m-%d", "TT_DATE_FORMAT": "%a, %b %e", "WK": "wk", "TIME": "Time:"};
    jQuery(document).ready(function($) {
        Calendar.setup({
            // Id of the input field
            inputField: "jform_publish_up",
            // Format of the input field
            ifFormat: "%d %b %Y",
            // Trigger for the calendar (button ID)
            button: "jform_publish_up_img",
            // Alignment (defaults to "Bl")
            align: "Bl",
            singleClick: true,
            firstDay: 0
        });
    });
    jQuery(document).ready(function($) {
        Calendar.setup({
            // Id of the input field
            inputField: "jform_publish_down",
            // Format of the input field
            ifFormat: "%d %b %Y",
            // Trigger for the calendar (button ID)
            button: "jform_publish_down_img",
            // Alignment (defaults to "Bl")
            align: "Bl",
            singleClick: true,
            firstDay: 0
        });
    });
</script>

<div class="container-fluid request" >
    <!-- row-fluid S -->
    <div class="row-fluid">
        <div class="span12 requestHeader">
            <h1>
                Manage requests
            </h1>
            <div class="btn-group pull-right">
                <a class="btn btn-group dropdown-toggle" data-toggle="dropdown" href="#">
                    <?php if ( isset($this->nowState) && !empty($this->nowState) ) {
                            echo $this->statusArr[$this->nowState];
                            unset($this->statusArr[$this->nowState]);
                            $showAll = 1;
                    } else {
                            echo JText::_('COM_REQUEST_STATUS_0');
                    }?>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <?php if( isset($showAll) && $showAll == 1 ):?>
                    <li>
                        <a href="javascript:void(0);" onclick="selectStatus(0)" >All</a>
                    </li>
                    <li class="divider">
                    </li>
                    <?php endif; ?>
                <?php foreach ( $this->statusArr as $k => $v ):?>
                    <li>
                        <a href="javascript:void(0);" onclick="selectStatus(<?php echo $k;?>)" ><?php echo $v;?></a>
                    </li>
                    <li class="divider">
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php
            //Joomla Component Creator code to allow adding non select list filters
            if (!empty($this->extra_sidebar)) {
                $this->sidebar .= $this->extra_sidebar;
            }
            ?>
            <form class="search-form pull-right"  style="margin-right: 50px;" action="<?php echo JRoute::_('index.php?option=com_request&view=lists'); ?>" method="post" name="adminForm">
                <input  type="text" name="filter_search" id="filter_search" placeholder="Search Organization..." value="<?php echo $this->escape($this->state->get('filter.search')); ?>" style="max-width: 300px; min-width: 50px;" >
            </form>
        </div>
        <div class="clearfix"></div>
        <div class="span12 request-thead">
            <div class="span4 request-tr pull-left">
                <h2 class="request-td">Subscribing organization</h2>
            </div>
            <div class="span8 request-tr pull-right">
                <div class="span3 request-td">
                    <h2>Request ID</h2>
                </div>
                <div class="span3 request-td">
                    <h2>Requested</h2>
                </div>
                <div class="span3 request-td">
                    <h2>Updated</h2>
                </div>
                <div class="span3 request-td">
                    <h2>Status</h2>
                </div>
            </div>
        </div>
        <div class="clearfix "></div>
        <?php $show = true; ?>
        <?php for( $i = 0; $i < $this->count; $i++ ): ?>
        <?php $plan_id = $this->items[$i]->plan_id;?> 
        <?php if ( $this->items[$i]->org_id != $prev ):?>
        <?php $org = $this->getOrginfo($this->items[$i]->org_id);?>
        <!-- request-tbody S -->
        <div class="span12 request-tbody">
            <div class="span4  pull-left request-tr-rowspan">
                <ul class="unstyled request-td-rowspan">
                    <li>
                        <h4><?php echo $this->items[$i]->org_title; ?></h4><br>
                        <?php echo $org['street'];?><br>
                        <?php echo $org['country'];?><br>
                    </li>
                    <li>
                        <a href="mailto:<?php echo $org['email'];?>"><?php echo $org['email'];?></a>
                    </li>
                </ul>
            </div>
            <div class="span8 pull-right request-tr content">
        <?php endif;?>
                <div class="accordion-heading accordion-toggle collapsed span12 accordion-request" data-toggle="collapse" data-target="#collapse<?php echo $i;?>">
                    <div class="span3 request-td" >
                        <span class="arrowR"></span><?php echo $this->items[$i]->id; ?>
                    </div>
                    <div class="span3 request-td"><?php echo $this->showTime($this->items[$i]->requested_by); ?></div>
                    <div class="span3 request-td"><?php echo $this->showTime($this->items[$i]->updated); ?></div>
                    <div class="span3 request-td"><span class=" <?php echo $this->getClass($this->items[$i]->status)?> status">
                        <?php switch ($this->items[$i]->status){
                                case 1 : echo JText::_('COM_REQUEST_STATUS_1'); break;
                                case 2 : echo JText::_('COM_REQUEST_STATUS_2'); break;
                                case 3 : echo JText::_('COM_REQUEST_STATUS_3'); break;
                                case 4 : echo JText::_('COM_REQUEST_STATUS_4'); break;
                        }?>
                    </span>
                    </div>
                <!-- collapse S -->
                <div id="collapse<?php echo $i;?>" class="span12 accordion-body collapse minH">
                    <input type="hidden" class="id" name="id" value="<?php echo $this->items[$i]->id; ?>" />
                    <!-- accordition-inner S -->
                    <div class="accordion-inner request-accordion">
                        <div id="content<?php echo $this->items[$i]->id;?>" class="pull-left span4">
                            <h4>
                                <div class="pull-left request-product-thumbnail" >
                                <?php if(TibcoTibco::getProductImage($this->items[$i]->product_id)): ?>
                                    <img src="<?php  echo JURI::base().TibcoTibco::getProductImage($this->items[$i]->product_id);?>" alt="">
                                <?php endif; ?>
                                </div>
                                <p>
                                    <span class="request-plan-name"><?php echo $this->items[$i]->product; ?></span><br>
<!--                                    <span class="plan-level">-->
<!--                                        --><?php
//                                            $planLevel = $this->showLevel($this->items[$i]->plan_id);
//                                            switch ($planLevel) {
//                                                case '-1' :
//                                                        echo "Custom Plan";
//                                                        break;
//                                                case '1' :
//                                                case '2' :
//                                                case '3' :
//                                                case '4' :
//                                                case '5' :
//                                                        echo "Standard Plan";
//                                                        break;
//                                                default: echo "Custom Plan";
//                                                        break;
//                                            }
//
//                                        ?>
<!--                                    </span>-->
                                </p>

                            </h4>
                        </div>
                        <?php 
                          $concurrent_calls = JText::sprintf($this->showJson($this->items[$i]->custom, 'climit'));
                          if(!empty($concurrent_calls))
                          {
                            $concurrent_calls = $concurrent_calls . " " . JText::_("CONCURRENT_CALLS");
                          }else{
                            $concurrent_calls ='';
                          }

                        ?>
                        <div id="content<?php echo $this->items[$i]->id;?>" class="pull-left span4 request-accordion-col2">
                               <p class="plan-status pull-left"><?php echo $this->items[$i]->plan;?></p>
                                <p class="pull-left">
                                    <span class="quota-limit"><?php echo str_replace("/"," calls per ", JText::sprintf($this->showJson($this->items[$i]->custom, 'qlimit')));  ?></span><br>
                                    <span class="rate-limit"><?php echo str_replace("/"," calls per ", JText::sprintf($this->showJson($this->items[$i]->custom, 'rlimit')));?></span><br>
                                    <span class="quota-limit"><?php echo $concurrent_calls;?></span><br>
                                 <input type="hidden" name="rate_limit" value="<?php echo $this->showJson($this->items[$i]->custom, 'rlimit'); ?>"/>
                                 <input type="hidden" name="quota_limit" value="<?php echo $this->showJson($this->items[$i]->custom, 'qlimit');  ?>"/>
                                 <input type="hidden" name="concurrent_calls" value="<?php echo $concurrent_calls;  ?>"/>
                                 <input type="hidden" name="username" class="" value="<?php echo $this->items[$i]->username; ?>">
                                </p>
                        </div>
                        <!-- pull right S -->
                        <?php if($this->items[$i]->status == 1): ?>
                        <div class="pull-right span4" class="request-button">
                            <button class="btn btn-default submit-approve-request" id="<?php echo $this->items[$i]->id;?>" data-toggle="modal" data-target="#approveModal"><?php echo JText::_('PLAN_REQUEST_APPROVE_BUTTON'); ?></button>
                            <button class="btn btn-default submit-approve-request " data-toggle="modal" data-target="#rejectModal"><?php echo JText::_('PLAN_REQUEST_REJECT_BUTTON'); ?></button>
                        </div>
                        <?php endif; ?>
                        <!-- pull right E -->
                        <div class="clearfix"></div>
                        <!-- comments info S -->
                        <p id="content<?php echo $this->items[$i]->id;?>" class="word-wrap">
                            <?php if( isset($this->items[$i]->user_note) && ($this->items[$i]->user_note != NULL) && ($this->items[$i]->user_note != 'null')): ?>
                            <strong><span class="requester"><?php echo $this->items[$i]->username; ?></span> - on <?php echo $this->showTime($this->items[$i]->requested_by); ?></strong><br>
                            <?php echo $this->items[$i]->user_note; ?><br>
                            <?php endif; ?>
                            <?php if( isset($this->items[$i]->admin_note) && ($this->items[$i]->admin_note != NULL) && ($this->items[$i]->admin_note != 'null')): ?>
                            <strong>Host Administrator - on <?php echo $this->showTime($this->items[$i]->updated); ?></strong><br>
                            <?php echo $this->items[$i]->admin_note; ?>
                            <?php endif; ?>
                        </p>
                        <!-- comments info E -->
                    </div>
                    <!-- accordition-inner E -->
                </div>
                <!-- accordion E -->
                </div>
                <?php if ( $this->items[$i]->org_id != $this->items[$i+1]->org_id ): ?>
            </div>
        </div>
        <!-- request-tbody E -->
        <div class="clearfix requestBorder"></div>
        <?php endif; $prev = $this->items[$i]->org_id; endfor; ?>
    </div>
    <!-- row fluid E -->
</div>

<div class="clearfix"></div>
<?php if ($show): ?>
    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>

<!-- approveModal S -->
<div id="approveModal" class="request-modal modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel" class="requestWarn"><?php echo JText::_('PLAN_REQUEST_APPROVE_TITLE'); ?></h3>
        </div>
        <div class="modal-body">
            <div class=" pull-left">
                <div class="plan-body">
                    <h3 class="requestPlan plan-name" ></h3>
                    <div class="clearfix"><strong>Selected plan</strong></div>
                    <ul class="unstyled request_list">
                    </ul>

                </div>
                <br>
            </div>
            <div class=" pull-right" >
                <strong><?php echo JText::_('COM_REQUEST_SUB_START_TIME');?></strong> <br>
                <div class="input-append">
                    <input type="text" title="" name="jform[publish_up]" id="jform_publish_up" value="" size="22" maxlength="45" class="input-medium hasTooltip" data-original-title="">
                    <button type="button" class="btn" id="jform_publish_up_img" aria-invalid="false" style="min-height: 32px;">
                        <i class="icon-calendar"></i>
                    </button>
                </div>
                <div class="clearfix"></div>
                <strong><?php echo JText::_('COM_REQUEST_SUB_END_TIME');?></strong> <br>
                <div class="input-append">
                    <input type="text" title="" name="jform[publish_down]" id="jform_publish_down" value="" size="22" maxlength="45" class="input-medium hasTooltip" data-original-title="" aria-invalid="false">
                    <button type="button" class="btn" id="jform_publish_down_img" aria-invalid="false" style="min-height: 32px;"><i class="icon-calendar"></i></button>
                </div>
                <div class="clearfix"></div>
                <div id ="aprove_planType">
                    <strong><?php echo JText::_('COM_REQUEST_PLAN_TYPE');?></strong> <br>
                    <input type="text" name="jform[plan_type]" value="" style="max-width: 200px; min-height: 22px;"/>
                </div>
                <strong><?php echo JText::_('COM_REQUEST_COMMENT');?></strong> <br>
                <textarea rows="3" name="jform[admin_note]" style="max-width: 200px; min-height: 65px;"></textarea>
                <input type="hidden" class="request_id" name="jform[id]" value="" />
                <input type="hidden" name="jform[status]" value="2" />
                <br>
                <br>
                <div class="control-group pull-right">
                    <button class="btn btn-default changeStatus"><?php echo JText::_('CONFIRM_REQUEST_APPROVE_BUTTON')?></button>
                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('CONFIRM_REQUEST_CANCEL_BUTTON')?></button>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
</div>
<!-- approveModal E -->
<!-- rejectModal S -->
<div id="rejectModal" class="request-modal modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel" class="requestWarn"><?php echo JText::_('PLAN_REQUEST_REJECT_TITLE'); ?></h3>
        </div>
        <div class="modal-body">
            <div class=" pull-left">
                <div class="plan-body">
                    <h3 class="requestPlan plan-name" ></h3>
                    <div class="clearfix"><strong>Selected plan</strong></div>

                    <ul class="unstyled request_list">
                    </ul>
                </div>
                <br>
            </div>
            <div class=" pull-right" >
                <strong><?php echo JText::_('COM_REQUEST_COMMENT'); ?></strong> <br>
                <textarea name="jform[admin_note]" rows="3" style="max-width: 200px; min-height: 65px;"></textarea>
                <input type="hidden" class="request_id" name="jform[id]" value="" />
                <input type="hidden" name="jform[status]" value="3" />
                <br>
                <br>
                <div  class="control-group pull-right">
                    <button class="btn btn-default changeStatus"><?php echo JText::_('CONFIRM_REQUEST_REJECT_BUTTON')?></button>
                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true" ><?php echo JText::_('CONFIRM_REQUEST_CANCEL_BUTTON')?></button>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
</div>
<!-- rejectModal E -->
<script type="text/javascript">
    jQuery(function() {

        requestForm  =  jQuery("[id^='myModalLabel']");
        jQuery(".submit-approve-request:button").click(function(e) {
            var flag = jQuery(this).parent().parent().parent().attr('id');
            var status = jQuery("[data-target=#" + flag + "] .status").text();
            var planStatus = jQuery('#' + flag + ' .plan-status').text();
            var product = jQuery('#' + flag + ' .request-plan-name').text();
            var id = jQuery('#' + flag + ' [name="id"]').val();
            var rateLimit = jQuery('#' + flag + ' [name="rate_limit"]').val();
            var quotaLimit = jQuery('#' + flag + ' [name="quota_limit"]').val();
            var concurrent_calls = jQuery('#' + flag + ' [name="concurrent_calls"]').val();
            var username = jQuery('#' + flag + ' [name="username"]').val();
            var request_thumbnail = jQuery('#' + flag + ' .request-product-thumbnail').html();
            rateLimit = rateLimit.split('/').join(" calls per ");
            quotaLimit = quotaLimit .split('/').join(" calls per ");
            if ( planStatus != 'Custom' && planStatus != 'custom' ) {
                jQuery('#aprove_planType').remove();
            }

            var dialog = "<li><p class='plan-status pull-left'>"+planStatus+"</p><p class='pull-left'><span class='quota-limit '>"+ quotaLimit +"</span><br><span class='rate-limit'>"+ rateLimit +"</span><br><span class='rate-limit'>"+ concurrent_calls +"</span></p><div class='clearfix'></div><p><?php echo JText::_("COM_REQUEST_STATUS_HEAD_REQUEST_BY");?>:<br><a href=''>"+ username + "</a></p></li>";
            jQuery(".request_list").html(dialog);
            jQuery('.plan-name').html(request_thumbnail+product);
            jQuery("[name='jform[id]']").val(id);

        });
        jQuery('.changeStatus').click(function(e) {
            e.preventDefault();
            var form = jQuery(this.form);
            var id = form.find("[name='jform[id]']").val();
            var status = form.find("[name='jform[status]']").val();
            var request_Warn = form.find(".requestWarn").text();
            var plan_type = form.find("[name='jform[plan_type]']").val();
            var admin_note = form.find("[name='jform[admin_note]']").val();
            var start_time = form.find("[name='jform[publish_up]']").val(),
                    end_time = form.find("[name='jform[publish_down]']").val(), rv = false;
            var jsonData = {};

            function isEndDateValid() {
                if(end_time == ""){
                    end_time = "2038-01-01";
                    form.find("[name='jform[publish_down]']").val(end_time);
                }
                if(start_time && end_time) {
                    try {
                        if(Date.parse(end_time).getTime() >= Date.parse(start_time).getTime()) {
                            rv = true;
                        }
                    } catch(oE) {
                        // Should've set rv to false but since it's initialized with false so do nothing
                    }
                }
                return rv;
            };

            if (status == 2) {
                 if ( isEndDateValid()) {
                     jsonData = {
                         "jform": {
                             "id": id,
                             "status": status,
                             "plan_type": plan_type,
                             "admin_note": admin_note,
                             "start_time": start_time,
                             "end_time" : end_time
                         }
                     };
                 }else{
                    request_Warn = jQuery("#approveModal .requestWarn").text("Please input correct date.");
                    jQuery("#approveModal .modal-header").addClass('alert-error');
                 }
            } else if ( status == 3 ) {
                jsonData = {
                    "jform": {
                        "id": id,
                        "status": status,
                        "admin_note": admin_note
                    }
                };
            }

            jQuery.ajax({
                type:"POST",
                url: GLOBAL_CONTEXT_PATH + 'index.php?option=com_request&task=lists.changeOperation',
                data: jsonData,
                dataType:"json",
                success: function(res, textStatus, jqXHR) {
                    if (res.success == 1) {
                        if (res.result) {
                            sendNotfyForCreatedSubscription(res, requestForm.find('.modal-header'));
                        } else {
                            window.location.reload();
                        }
                    } else {
                        var sUUID = UUID.generate(),
                            sErrMsg = res.result + PORTAL_RESP_SUMMARY_POSTFIX_UUID;
                        DeveloperPortal._saveLogInDatabase({
                            log_type: 'Request',
                            status: jqXHR.status,
                            statusText: jqXHR.statusText,
                            content: jqXHR.responseText,
                            summary: sErrMsg,
                            entity_type: 'Request',
                            entity_id: id,
                            event: 'Request',
                            event_status: 'error',
                            uuid: sUUID
                        }, function(sErrMsg) {
                            DeveloperPortal.storeErrMsgInCookie([sErrMsg]);
                            window.location.reload();
                        }, [DeveloperPortal._urlifySupport(sUUID, sErrMsg) + sUUID]);
                        window.location.reload();
                    }
                }
            });
        });
    });

    function sendNotfyForCreatedSubscription(res, message_container){

        //  console.log(res);
         DeveloperPortal.sendCreateNotification(res.result.record_id, DeveloperPortal.PORTAL_OBJECT_TYPE_SUBSCRIPTION, function(data){
            if (res.result.appIds){
                sendNotifyForUpdatedApplication(res,message_container);
             }
              else{
                  window.location.reload();
              }
                  }, function(error){
                      window.location.reload();
         });
       }


       function sendNotifyForUpdatedApplication(res, message_container){
          for(i = 0; i < res.result.appIds.length; i++) {
            var appid=res.result.appIds[i],updatedfields='';
            if(res.result.app_old_subscriptions){
              updatedfields = {115:res.result.app_old_subscriptions[appid]};
            }else{
              updatedfields = {115:[]};
            }
             DeveloperPortal.sendUpdateNotification(appid, DeveloperPortal.PORTAL_OBJECT_TYPE_APPLICATION,updatedfields,function(data){
                 window.location.reload();
           }, function(error){
               window.location.reload();
           });
         }
       }

       //show the tip message, whatever is error message or success message
       function showTip(res, container){
           var tips = res.success == 1 ? res.result.msg : res.error;
           var status_class = res.success == 1 ? "alert-success" : "alert-error";
           container.addClass(status_class);
            container.removeClass('alert-error').addClass(status_class);
            container.find('#myModalLabel').text(tips);
          }

    function selectStatus(id) {
        jQuery.ajax({
            type:"POST",
            url: GLOBAL_CONTEXT_PATH + 'index.php?option=com_request&task=lists.selectState',
            data: {state:id},
            dataType:"json",
            success:function( data ) {
                location.reload();
            }
        });
    }





</script>