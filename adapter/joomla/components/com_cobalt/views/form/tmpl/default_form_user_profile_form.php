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
require_once JPATH_BASE . "/includes/api.php";
$started = false;
$params = $this->tmpl_params;
if($params->get('tmpl_params.form_grouping_type', 0))
{
	$started = true;
}

$k = 0;
$path = JRequest::getURI();
$current_user_org_id = TibcoTibco::getCurrentUserOrgId();
$auth_group_ids = $this->user->getAuthorisedGroups();
$path = JRequest::getURI();
$userprofile = false;
if(strpos($path,'userprofile') || strpos($path, 'dashboard')){
  if(!(in_array(7, $auth_group_ids) || in_array(8, $auth_group_ids))) {
    $userprofile = true;
  }
  $users_userProfileID = DeveloperPortalApi::getUserProfileId();

  $userprofile_id = $this->item->id;

  if(!($users_userProfileID == $userprofile_id || in_array(7, $auth_group_ids) || in_array(8, $auth_group_ids))){

  	JFactory::getApplication()->enqueueMessage(JText::_('USERPROFILE_CUSTOM_ACCESS_DENIED_ERROR'), 'error');

    return;
  }
}
$userorganizations = false;
if(strpos($path,'userorganizations')){
	$userorganizations = true;
}
?>
<?php  if($userprofile):?>
  <script type="text/javascript">
    if(typeof asgUserProfile == 'undefined'){
      var asgUserProfile = true;
    }
  </script>
<?php endif;?>
<script type="text/javascript">
var asgUserOrganisation=false;
</script>
<?php  if($userorganizations):?>
  <script type="text/javascript">
     asgUserOrganisation = true;

  </script>
 <?php endif;?>
<style>
	.licon {
	 	float: right;
	 	margin-left: 5px;
	}
	.line-brk {
		margin-left: 0px !important;
	}
	.control-group {
		margin-bottom: 10px;
		padding: 8px 0;
		-webkit-transition: all 200ms ease-in-out;
		-moz-transition: all 200ms ease-in-out;
		-o-transition: all 200ms ease-in-out;
		-ms-transition: all 200ms ease-in-out;
		transition: all 200ms ease-in-out;
	}
	.highlight-element {
		-webkit-animation-name: glow;
		-webkit-animation-duration: 1.5s;
		-webkit-animation-iteration-count: 1;
		-webkit-animation-direction: alternate;
		-webkit-animation-timing-function: ease-out;

		-moz-animation-name: glow;
		-moz-animation-duration: 1.5s;
		-moz-animation-iteration-count: 1;
		-moz-animation-direction: alternate;
		-moz-animation-timing-function: ease-out;

		-ms-animation-name: glow;
		-ms-animation-duration: 1.5s;
		-ms-animation-iteration-count: 1;
		-ms-animation-direction: alternate;
		-ms-animation-timing-function: ease-out;
	}
	<?php echo $params->get('tmpl_params.css');?>
@-webkit-keyframes glow {
	0% {
		background-color: #fdd466;
	}
	100% {
		background-color: transparent;
	}
}
@-moz-keyframes glow {
	0% {
		background-color: #fdd466;
	}
	100% {
		background-color: transparent;
	}
}

@-ms-keyframes glow {
	0% {
		background-color: #fdd466;
	}
	100% {
		background-color: transparent;
	}
}
#tabs-list {
  display: none;
}

#tabs-box {
  border-style: none;
}
</style>
	<?php
 	$comEmail = JComponentHelper::getComponent('com_emails');
	$spotfire_domain = $comEmail->params->get('spotfire_domain');
	if(!$spotfire_domain){
		$spotfire_domain = '';
	}
	?>

<script type="text/javascript">

var old_usertype;
var existUserFlag = false;
var userProfileId = <?php echo $this->item->id ? $this->item->id : 0 ?>;
(function($){
	$(function(){
		$(window).load(function(){
			 old_usertype = jQuery("#form_field_list_88").val();
		});
	});
})(jQuery);

Joomla.submitform = function(task) {
  DeveloperPortal.submitForm(task,
    function(nObjectId, sRedirectUrl) {
        if (!existUserFlag && userProfileId === 0) {
            var email = jQuery("#field_102").val();
            var userType = ""+jQuery("#form_field_list_88").val();
            jQuery.post(
              GLOBAL_CONTEXT_PATH+"index.php?option=com_cobalt&task=ajaxMore.addUserToGroup",
              {'org_id':''+nObjectId, 'user_email':email, 'user_type':userType},
              function(data){
				  window.location.href = sRedirectUrl;
              },
                'json'
            ).fail(function(res){
				window.location.href = sRedirectUrl;
            });
		}else{
			window.location.href = sRedirectUrl;
		}
    },
    function(sRedirectUrl) {
      window.location.href = sRedirectUrl;
    }
  );
};

    Joomla.beforesubmitform = function(callback, errorback) {

        var joomla_user_id = jQuery("#77_id");
        var joomla_user_name = jQuery("#77_name");
        var record_id = jQuery("input[name='id'][type='hidden']").val();
        var username = jQuery("#field_101");
        var name = jQuery("#jform_title");
        var firstName = jQuery('#field_45');
        var password1 = jQuery("#jform_password1");
        var password2 = jQuery("#jform_password2");
        var email = jQuery("#field_102");
        var token = jQuery("input:hidden[value='1']").last().attr("name");
        var userType = jQuery("#form_field_list_88");
        username.val(email.val());
        var new_userType_val=userType.val();
        if((password1.val() !== '' || password2.val() !== '')) {
            if(password1.val() === password2.val()) {
                jQuery.ajax({
                    type : "post",
                    dataType: "json",
                    url : GLOBAL_CONTEXT_PATH+'index.php?option=com_cobalt&task=ajaxmore.validatePasswordRules',
                    data : { password : password1.val() },
                    success : function(res){
                        if ( res.success == 0 ) {
                            Joomla.showError([res.error]);
                        } else {
                            manageJoomlaACL();
                        }
                    }
                });
            } else {
                Joomla.showError(['<?php echo JText::_("INVALID_CONFIRMPWD"); ?>']);
            }
        } else {
            manageJoomlaACL();
        }

        function manageJoomlaACL() {
            var data = {
                "option" : "com_users",
                "task" : "autoreg.createMember"
            };
            data.jform = {
                "email1" : email.val(),
                "email2" : email.val(),
                "name" : name.val(),
                "first-name": firstName.val(),
                "username" : email.val(),
                "password1" : password1.val(),
                "password2" : password2.val(),
                "user_group_name" : "Organization <?php echo $this->fields[47]->value[0]; ?> " + userType.val(),
                "old_user_group_name" : "Organization <?php echo $this->fields[47]->value[0]; ?> " + old_usertype
            };
            data[token]=1;
            //Set domain for handling correct redirect to dashboard page
            //console.log(profile_id);
            if(profile_id){

            function analyticsErrorHandler(errorCode, description){
                    console.error("Error loading analtyics: code(" + errorCode + ")\n\t" + description);
            }
            }

            if((joomla_user_id.length && joomla_user_id.val() == '0' && !parseInt(record_id))||(asgUserOrganisation && !parseInt(record_id))){

                 jQuery.ajax({
                    type : 'post',
                    data : data,
                    dataType: 'json',
                    complete: function(jqXHR, textStatus) {
                        var result = jQuery.parseJSON(jqXHR.responseText);
                        if(result && result.userid && result.userid[0]) {
                            jQuery("#fld-101,#fld-102").slideUp().attr('readonly', 'true');
                            joomla_user_name.val(data.jform.name);
                            joomla_user_id.val(result.userid[0]);
                            callback();
                        } else if(result.error) {
                            errorback(result.error);
                        }
                    }
                });
            } else if(joomla_user_id.length && joomla_user_id.val() && !parseInt(record_id)){
                            existUserFlag = true;
                data.task   = "ajaxmore.attachUserToGroup";
                data.option = "com_cobalt";
                data.userId = joomla_user_id.val();
                jQuery.ajax({
                    type : 'post',
                    data : data,
                    dataType:'json',
                    complete: function(jqXHR, textStatus) {
                        var result = jQuery.parseJSON(jqXHR.responseText);
                        if(result.success) {
                            callback();
                        } else if(result.error) {
                            errorback(result.error);
                        }
                    }
                });
            }else if(joomla_user_id.length && joomla_user_id.val() && parseInt(record_id) && !(old_usertype == new_userType_val)){
                data.task   = "ajaxmore.updateUsersGroup";
                data.option = "com_cobalt";
                data.userId = joomla_user_id.val();
                jQuery.ajax({
                    type : 'post',
                    data : data,
                    dataType:'json',
                    complete: function(jqXHR, textStatus) {
                        var result = jQuery.parseJSON(jqXHR.responseText);
                        if(result.success) {

                            callback();
                        } else if(result.error) {
                            errorback(result.error);
                        }
                    }
                });

            }else{
                    callback();
            }
        };
    };

</script>
<div class="form-horizontal">
<?php if(in_array($params->get('tmpl_params.form_grouping_type', 0), array(1, 4))):?>
	<div class="tabbable<?php if($params->get('tmpl_params.form_grouping_type', 0) == 4) echo ' tabs-left' ?>">
		<ul class="nav nav-tabs" id="tabs-list">
			<li><a href="#tab-main" data-toggle="tab"><?php echo JText::_($params->get('tmpl_params.tab_main', 'Main'));?></a></li>

			<?php if(isset($this->sorted_fields)):?>
				<?php foreach ($this->sorted_fields as $group_id => $fields) :?>
					<?php if($group_id == 0) continue;?>
					<li><a class="taberlink" href="#tab-<?php echo $group_id?>" data-toggle="tab"><?php echo HTMLFormatHelper::icon($this->field_groups[$group_id]['icon'])?> <?php echo $this->field_groups[$group_id]['name']?></a></li>
				<?php endforeach;?>
			<?php endif;?>

			<?php if(count($this->meta)):?>
				<li><a href="#tab-meta" data-toggle="tab"><?php echo JText::_('Meta Data');?></a></li>
			<?php endif;?>
			<?php if(count($this->core_admin_fields)):?>
				<li><a href="#tab-special" data-toggle="tab"><?php echo JText::_('Special Fields');?></a></li>
			<?php endif;?>
			<?php if(count($this->core_fields)):?>
				<li><a href="#tab-core" data-toggle="tab"><?php echo JText::_('Core Fields');?></a></li>
			<?php endif;?>
		</ul>
<?php endif;?>
	<?php group_start($this, $params->get('tmpl_params.tab_main', 'Main'), 'tab-main');?>
  <div class="asg-create-userprofile-step1">
    <div class="row-fluid"  <?php if($userprofile){echo " id='asg-userprofile-form'";} ?>>
      <div class="span8">
        <?php if($params->get('tmpl_params.tab_main_descr')):?>
            <?php echo $params->get('tmpl_params.tab_main_descr'); ?>
        <?php endif;?>

        <?php if($this->type->params->get('properties.item_title', 1) == 1):?>
          <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="title-lbl" for="jform_title" class="control-label" >
             <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>">
                <?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php if($params->get('tmpl_core.form_title_icon', 1)):?>
                <?php echo HTMLFormatHelper::icon($params->get('tmpl_core.item_icon_title_icon', 'edit.png'));  ?>
              <?php endif;?>

              <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_title', 'Title')) ?>

            </label>
            <div class="controls">
              <div id="field-alert-title" class="alert alert-error" style="display:none"></div>
              <div class="row-fluid">
                <?php echo $this->form->getInput('title'); ?>
              </div>
            </div>
          </div>
        <?php else :?>
          <input type="hidden" name="jform[title]" value="<?php echo htmlentities(!empty($this->item->title) ? $this->item->title : JText::_('CNOTITLE').': '.time(), ENT_COMPAT, 'UTF-8')?>" />
        <?php endif;?>

        <?php if($this->anywhere) : ?>
          <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="anywhere-lbl" class="control-label" >
              <?php if($params->get('tmpl_core.form_anywhere_icon', 1)):?>
                <?php echo HTMLFormatHelper::icon('document-share.png');  ?>
              <?php endif;?>

              <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_anywhere', 'Where to post')) ?>
              <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
            </label>
            <div class="controls">
              <div id="field-alert-anywhere" class="alert alert-error" style="display:none"></div>
              <?php echo JHTML::_('users.wheretopost', @$this->item); ?>
            </div>
          </div>


          <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="anywherewho-lbl" for="whorepost" class="control-label" >
              <?php if($params->get('tmpl_core.form_anywhere_who_icon', 1)):?>
                <?php echo HTMLFormatHelper::icon('arrow-retweet.png');  ?>
              <?php endif;?>

              <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_anywhere_who', 'Who can repost')) ?>
            </label>
            <div class="controls">
              <div id="field-alert-anywhere" class="alert alert-error" style="display:none"></div>
              <?php echo $this->form->getInput('whorepost'); ?>
            </div>
          </div>
        <?php endif;?>

        <?php if(in_array($this->params->get('submission.allow_category'), $this->user->getAuthorisedViewLevels()) && $this->section->categories):?>
          <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <?php if($this->catsel_params->get('tmpl_core.category_label', 0)):?>
              <label id="category-lbl" for="category" class="control-label" >
                <?php if($params->get('tmpl_core.form_category_icon', 1)):?>
                  <?php echo HTMLFormatHelper::icon('category.png');  ?>
                <?php endif;?>

                <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_category', 'Category')) ?>

                <?php if(!$this->type->params->get('submission.first_category', 0) && in_array($this->type->params->get('submission.allow_category', 1), $this->user->getAuthorisedViewLevels())) : ?>
                  <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
                <?php endif;?>
              </label>
            <?php endif;?>
            <div class="controls">
              <div id="field-alert-category" class="alert alert-error" style="display:none"></div>
              <?php echo $this->loadTemplate('category_'.$params->get('tmpl_params.tmpl_category', 'default')); ?>
            </div>
          </div>
        <?php elseif(!empty($this->category->id)):?>
          <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="category-lbl" for="category" class="control-label">
              <?php if($params->get('tmpl_core.form_category_icon', 1)):?>
                <?php echo HTMLFormatHelper::icon('category.png');  ?>
              <?php endif;?>

              <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_category', 'Category')) ?>

              <?php if(!$this->type->params->get('submission.first_category', 0) && in_array($this->type->params->get('submission.allow_category', 1), $this->user->getAuthorisedViewLevels())) : ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>
            </label>
            <div class="controls">
              <div id="field-alert-category" class="alert alert-error" style="display:none"></div>
              <?php echo $this->section->name;?>/<?php echo $this->category->path; ?>
            </div>
          </div>
        <?php endif;?>


        <?php if($this->ucategory) : ?>
          <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="ucategory-lbl" for="ucatid" class="control-label" >
              <?php if($params->get('tmpl_core.form_ucategory_icon', 1)):?>
                <?php echo HTMLFormatHelper::icon('category.png');  ?>
              <?php endif;?>

              <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_ucategory', 'Category')) ?>

              <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
            </label>
            <div class="controls">
              <div id="field-alert-ucat" class="alert alert-error" style="display:none"></div>
              <?php echo $this->form->getInput('ucatid'); ?>
            </div>
          </div>
        <?php else:?>
          <?php $this->form->setFieldAttribute('ucatid', 'type', 'hidden'); ?>
          <?php $this->form->setValue('ucatid', null, '0'); ?>
          <?php echo $this->form->getInput('ucatid'); ?>
        <?php endif;?>

        <?php if($this->multirating):?>
          <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="jform_multirating-lbl" class="control-label" for="jform_multirating" ><?php echo strip_tags($this->form->getLabel('multirating'));?></label>
            <div class="controls">
              <?php echo $this->multirating;?>
            </div>
          </div>
        <?php endif;?>

        <!-- system user goes here -->

          <?php $sysuser = $this->sorted_fields[0][77]?>
          <div id="fld-<?php echo $sysuser->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-77'; ?> <?php echo $sysuser->fieldclass;?>">
            <?php if($sysuser->params->get('core.show_lable') == 1 || $sysuser->params->get('core.show_lable') == 3):?>
              <label id="lbl-<?php echo $sysuser->id;?>" for="field_<?php echo $sysuser->id;?>" class="control-label <?php echo $sysuser->class;?>" >
                <?php if($sysuser->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                  <?php echo HTMLFormatHelper::icon($sysuser->params->get('core.icon'));  ?>
                <?php endif;?>


                <?php if ($sysuser->required): ?>
                  <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
                <?php endif;?>

                <?php if ($sysuser->description):?>
                  <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($sysuser->translateDescription ? JText::_($sysuser->description) : $sysuser->description), ENT_COMPAT, 'UTF-8');?>">
                    <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                  </span>
                <?php endif;?>

                <?php echo $sysuser->label; ?>

              </label>
              <?php if(in_array($sysuser->params->get('core.label_break'), array(1,3))):?>
                <div style="clear: both;"></div>
              <?php endif;?>
            <?php endif;?>

            <div class="controls<?php if(in_array($sysuser->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($sysuser->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $sysuser->fieldclass  ?>">
              <div id="field-alert-<?php echo $sysuser->id?>" class="alert alert-error" style="display:none"></div>
              <?php echo $sysuser->result; ?>
            </div>
          </div>


        <!-- user type goes here -->
          <?php $usertype = $this->sorted_fields[0][88]?>
          <div id="fld-<?php echo $usertype->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-88'; ?> <?php echo $usertype->fieldclass;?>">
            <?php if($usertype->params->get('core.show_lable') == 1 || $usertype->params->get('core.show_lable') == 3):?>
              <label id="lbl-<?php echo $usertype->id;?>" for="field_<?php echo $usertype->id;?>" class="control-label <?php echo $usertype->class;?>" >
                <?php if($usertype->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                  <?php echo HTMLFormatHelper::icon($usertype->params->get('core.icon'));  ?>
                <?php endif;?>


                <?php if ($usertype->required): ?>
                  <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
                <?php endif;?>

                <?php if ($usertype->description):?>
                  <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($usertype->translateDescription ? JText::_($usertype->description) : $usertype->description), ENT_COMPAT, 'UTF-8');?>">
                    <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                  </span>
                <?php endif;?>

                <?php echo $usertype->label; ?>

              </label>
              <?php if(in_array($usertype->params->get('core.label_break'), array(1,3))):?>
                <div style="clear: both;"></div>
              <?php endif;?>
            <?php endif;?>

            <div class="controls<?php if(in_array($usertype->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($usertype->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $usertype->fieldclass  ?>">
              <div id="field-alert-<?php echo $usertype->id?>" class="alert alert-error" style="display:none"></div>
              <?php echo $usertype->result; ?>
            </div>
          </div>

        <!-- user name goes here -->
        <?php $username = $this->sorted_fields[0][101]?>
        <div id="fld-<?php echo $username->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-101'; ?> <?php echo $username->fieldclass;?>">
          <?php if($username->params->get('core.show_lable') == 1 || $username->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $username->id;?>" for="field_<?php echo $username->id;?>" class="control-label <?php echo $username->class;?>" >
              <?php if($username->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($username->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($username->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($username->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($username->translateDescription ? JText::_($username->description) : $username->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $username->label; ?>

            </label>
            <?php if(in_array($username->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($username->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($username->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $username->fieldclass  ?>">
            <div id="field-alert-<?php echo $username->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $username->result; ?>
          </div>
        </div>
        <?php if($userprofile): ?>
        <div class="control-group" id="asg-userprofile-oldPwd">
              <label class="control-label"> <span class="pull-left" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>Current password</label>
              <div class="controls ">
                <div style="display:none" class="alert alert-error"></div>
                  <input type="password" size="30" class="validate-password" autocomplete="off" value="" id="jform_oldPassword" name="jform[oldPassword]" aria-invalid="false">
              </div>
        </div>
        <?php endif; ?>
        <div class="control-group" id="asg-userprofile-pwd1">

              <label class="control-label"> <span class="pull-left" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>New password</label>
              <div class="controls ">
                <div style="display:none" class="alert alert-error"></div>
                  <input type="password" size="30" class="validate-password" autocomplete="off" value="" id="jform_password1" name="jform[password1]" aria-invalid="false">
              </div>
        </div>
        <div class="control-group" id="asg-userprofile-pwd2">
              <label class="control-label"><span class="pull-left" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>Confirm password</label>
              <div class="controls">
                <div style="display:none" class="alert alert-error"></div>
                <input type="password" size="30" class="validate-password" autocomplete="off" value="" id="jform_password2" name="jform[password2]">
              </div>
        </div>
        <!-- email goes here -->
        <?php $email = $this->sorted_fields[0][102]?>
        <div id="fld-<?php echo $email->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-102'; ?><?php echo $email->fieldclass;?>">
          <?php if($email->params->get('core.show_lable') == 1 || $email->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $email->id;?>" for="field_<?php echo $email->id;?>" class="control-label <?php echo $email->class;?>" >
              <?php if($email->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($email->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($email->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($email->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($email->translateDescription ? JText::_($email->description) : $email->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $email->label; ?>

            </label>
            <?php if(in_array($email->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div id="email<?php echo $email->id; ?>" class="controls<?php if(in_array($email->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($email->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $email->fieldclass  ?>">
            <div id="field-alert-<?php echo $email->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $email->result;  ?>
            <p><?php if ($this->item->id) { echo JText::_('PROFILE_FORM_CONTACT_EMAIL'); }?></p>
          </div>
        </div>



        <!-- first name goes here -->
        <?php $fname = $this->sorted_fields[0][45]?>
        <div id="fld-<?php echo $fname->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-45'; ?> <?php echo $fname->fieldclass;?>">
          <?php if($fname->params->get('core.show_lable') == 1 || $fname->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $fname->id;?>" for="field_<?php echo $fname->id;?>" class="control-label <?php echo $fname->class;?>" >
              <?php if($fname->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($fname->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($fname->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($fname->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($fname->translateDescription ? JText::_($fname->description) : $fname->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $fname->label; ?>

            </label>
            <?php if(in_array($fname->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($fname->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($fname->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $fname->fieldclass  ?>">
            <div id="field-alert-<?php echo $fname->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $fname->result; ?>
          </div>
        </div>

        <!-- last name goes here -->
        <?php $lname = $this->sorted_fields[0][46]?>
        <div id="fld-<?php echo $lname->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-46'; ?> <?php echo $lname->fieldclass;?>">
          <?php if($lname->params->get('core.show_lable') == 1 || $lname->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $lname->id;?>" for="field_<?php echo $lname->id;?>" class="control-label <?php echo $lname->class;?>" >
              <?php if($lname->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($lname->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($lname->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($lname->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($lname->translateDescription ? JText::_($lname->description) : $lname->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $lname->label; ?>

            </label>
            <?php if(in_array($lname->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($lname->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($lname->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $lname->fieldclass  ?>">
            <div id="field-alert-<?php echo $lname->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $lname->result; ?>
          </div>
        </div>


        <!-- Contact phone number -->
        <?php $lname = $this->sorted_fields[0][121]?>
        <div id="fld-<?php echo $lname->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-46'; ?> <?php echo $lname->fieldclass;?>">
          <?php if($lname->params->get('core.show_lable') == 1 || $lname->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $lname->id;?>" for="field_<?php echo $lname->id;?>" class="control-label <?php echo $lname->class;?>" >
              <?php if($lname->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($lname->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($lname->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($lname->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($lname->translateDescription ? JText::_($lname->description) : $lname->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $lname->label; ?>

            </label>
            <?php if(in_array($lname->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($lname->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($lname->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $lname->fieldclass  ?>">
            <div id="field-alert-<?php echo $lname->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $lname->result; ?>
          </div>
        </div>


        <!-- member of organizations goes here -->
          <?php $member = $this->sorted_fields[0][47]?>
          <?php  //TODO
        // if(isset($member)):?>
          <div id="fld-<?php echo $member->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-47'; ?> <?php echo $member->fieldclass;?>">
            <?php if($member->params->get('core.show_lable') == 1 || $member->params->get('core.show_lable') == 3):?>
              <label id="lbl-<?php echo $member->id;?>" for="field_<?php echo $member->id;?>" class="control-label <?php echo $member->class;?>" >
                <?php if($member->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                  <?php echo HTMLFormatHelper::icon($member->params->get('core.icon'));  ?>
                <?php endif;?>


                <?php if ($member->required): ?>
                  <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
                <?php endif;?>

                <?php if ($member->description):?>
                  <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($member->translateDescription ? JText::_($member->description) : $member->description), ENT_COMPAT, 'UTF-8');?>">
                    <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                  </span>
                <?php endif;?>

                <?php echo $member->label; ?>

              </label>
              <?php if(in_array($member->params->get('core.label_break'), array(1,3))):?>
                <div style="clear: both;"></div>
              <?php endif;?>
             <?php// endif;?>

            <div class="controls<?php if(in_array($member->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($member->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $member->fieldclass  ?>">
              <div id="field-alert-<?php echo $member->id?>" class="alert alert-error" style="display:none"></div>
              <?php echo $member->result; ?>
            </div>
          </div>
          <?php endif;?>


        <!-- contact of organizations goes here -->
        <?php $organizations = $this->sorted_fields[0][50]?>
        <?php  //TODO
        if(isset($organizations)):?>
        <div id="fld-<?php echo $organizations->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-50'; ?> <?php echo $organizations->fieldclass;?>">
          <?php if($organizations->params->get('core.show_lable') == 1 || $organizations->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $organizations->id;?>" for="field_<?php echo $organizations->id;?>" class="control-label <?php echo $organizations->class;?>" >
              <?php if($organizations->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($organizations->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($organizations->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($organizations->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($organizations->translateDescription ? JText::_($organizations->description) : $organizations->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $organizations->label; ?>

            </label>
            <?php if(in_array($organizations->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($organizations->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($organizations->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $organizations->fieldclass  ?>">
            <div id="field-alert-<?php echo $organizations->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $organizations->result; ?>
          </div>
        </div>
        <?php endif;?>

        <!-- contact for product goes here -->
        <?php $products = $this->sorted_fields[0][49]?>
        <?php  //TODO
        if(isset($products)):?>
        <div id="fld-<?php echo $products->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-49'; ?> <?php echo $products->fieldclass;?>">
          <?php if($products->params->get('core.show_lable') == 1 || $products->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $products->id;?>" for="field_<?php echo $products->id;?>" class="control-label <?php echo $products->class;?>" >
              <?php if($products->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($products->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($products->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($products->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($products->translateDescription ? JText::_($products->description) : $products->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $products->label; ?>

            </label>
            <?php if(in_array($products->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($products->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($products->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $products->fieldclass  ?>">
            <div id="field-alert-<?php echo $products->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $products->result; ?>
          </div>
        </div>
        <?php endif;?>


        <!-- contact for APIs goes here -->
        <?php $apis = $this->sorted_fields[0][51]?>
        <?php  //TODO
        if(isset($apis)):?>
        <div id="fld-<?php echo $apis->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-51'; ?> <?php echo $apis->fieldclass;?>" >
          <?php if($apis->params->get('core.show_lable') == 1 || $apis->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $apis->id;?>" for="field_<?php echo $apis->id;?>" class="control-label <?php echo $apis->class;?>" >
              <?php if($apis->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($apis->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($apis->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($apis->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($apis->translateDescription ? JText::_($apis->description) : $apis->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $apis->label; ?>

            </label>
            <?php if(in_array($apis->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($apis->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($apis->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $apis->fieldclass  ?>">
            <div id="field-alert-<?php echo $apis->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $apis->result; ?>
          </div>
        </div>
        <?php endif;?>

        <!-- contact for plans goes here -->
        <?php $plans = $this->sorted_fields[0][52]?>
        <?php  //TODO
        if(isset($plans)):?>
        <div id="fld-<?php echo $plans->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-52'; ?> <?php echo $plans->fieldclass;?>">
          <?php if($plans->params->get('core.show_lable') == 1 || $plans->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $plans->id;?>" for="field_<?php echo $plans->id;?>" class="control-label <?php echo $plans->class;?>" >
              <?php if($plans->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($plans->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($plans->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($plans->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($plans->translateDescription ? JText::_($plans->description) : $plans->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $plans->label; ?>

            </label>
            <?php if(in_array($plans->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($plans->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($plans->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $plans->fieldclass  ?>">
            <div id="field-alert-<?php echo $plans->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $plans->result; ?>
          </div>
        </div>
        <?php endif;?>


        <!-- contact for applications goes here -->
        <?php $applications = $this->sorted_fields[0][59]?>
        <?php  //TODO
        if(isset($applications)):?>
        <div id="fld-<?php echo $applications->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-59'; ?> <?php echo $applications->fieldclass;?>">
          <?php if($applications->params->get('core.show_lable') == 1 || $applications->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $applications->id;?>" for="field_<?php echo $applications->id;?>" class="control-label <?php echo $applications->class;?>" >
              <?php if($applications->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($applications->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($applications->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($applications->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($applications->translateDescription ? JText::_($applications->description) : $applications->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $applications->label; ?>

            </label>
            <?php if(in_array($applications->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($applications->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($applications->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $applications->fieldclass  ?>">
            <div id="field-alert-<?php echo $applications->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $applications->result; ?>
          </div>
        </div>
        <?php endif;?>


        <!-- contact for subscriptions goes here -->
        <?php $subscriptions = $this->sorted_fields[0][68]?>
        <?php  //TODO
        if(isset($applications)):?>
        <div id="fld-<?php echo $subscriptions->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-68'; ?> <?php echo $subscriptions->fieldclass;?>">
          <?php if($subscriptions->params->get('core.show_lable') == 1 || $subscriptions->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $subscriptions->id;?>" for="field_<?php echo $subscriptions->id;?>" class="control-label <?php echo $subscriptions->class;?>" >
              <?php if($subscriptions->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($subscriptions->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($subscriptions->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($subscriptions->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($subscriptions->translateDescription ? JText::_($subscriptions->description) : $subscriptions->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $subscriptions->label; ?>

            </label>
            <?php if(in_array($subscriptions->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($subscriptions->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($subscriptions->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $subscriptions->fieldclass  ?>">
            <div id="field-alert-<?php echo $subscriptions->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $subscriptions->result; ?>
          </div>
        </div>
        <?php endif;?>
      </div>
      <div class="span4">
        <!-- thumbnails goes here -->
      </div>
    </div>
    <?php unset($this->sorted_fields[0]);?>
  </div>

	<?php if(MECAccess::allowAccessAuthor($this->type, 'properties.item_can_add_tag', $this->item->user_id) &&
		$this->type->params->get('properties.item_can_view_tag')):?>
		<div class="control-group odd<?php echo $k = 1 - $k ?>">
			<label id="tags-lbl" for="tags" class="control-label" >
				<?php if($params->get('tmpl_core.form_tags_icon', 1)):?>
					<?php echo HTMLFormatHelper::icon('price-tag.png');  ?>
				<?php endif;?>
				<?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_tags', 'Tags')) ?>
			</label>
			<div class="controls">
				<?php //echo JHtml::_('tags.tagform', $this->section, json_decode($this->item->tags, TRUE), array(), 'jform[tags]'); ?>
				<?php echo $this->form->getInput('tags'); ?>
			</div>
		</div>
	<?php endif;?>

	<?php group_end($this);?>


	<?php if(isset($this->sorted_fields)):?>
		<?php foreach ($this->sorted_fields as $group_id => $fields) :?>
			<?php $started = true;?>
			<?php group_start($this, $this->field_groups[$group_id]['name'], 'tab-'.$group_id);?>
			<?php if(!empty($this->field_groups[$group_id]['descr'])):?>
				<?php echo $this->field_groups[$group_id]['descr'];?>
			<?php endif;?>
			<?php foreach ($fields as $field_id => $field):?>
				<div id="fld-<?php echo $field->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-'.$field_id; ?> <?php echo $field->fieldclass;?>">
					<?php if($field->params->get('core.show_lable') == 1 || $field->params->get('core.show_lable') == 3):?>
						<label id="lbl-<?php echo $field->id;?>" for="field_<?php echo $field->id;?>" class="control-label <?php echo $field->class;?>" >
							<?php if($field->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
								<?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
							<?php endif;?>
							<?php if ($field->required): ?>
								<span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
							<?php endif;?>

							<?php if ($field->description):?>
								<span class="pull-right" rel="tooltip" style="cursor: help;" data-original-title="<?php echo htmlspecialchars(($field->translateDescription ? JText::_($field->description) : $field->description), ENT_COMPAT, 'UTF-8');?>">
									<?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
								</span>
							<?php endif;?>
							<?php echo $field->label; ?>
						</label>
						<?php if(in_array($field->params->get('core.label_break'), array(1,3))):?>
							<div style="clear: both;"></div>
						<?php endif;?>
					<?php endif;?>

					<div class="controls<?php if(in_array($field->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($field->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $field->fieldclass  ?>">
						<div id="field-alert-<?php echo $field->id?>" class="alert alert-error" style="display:none"></div>
						<?php echo $field->result; ?>
					</div>
				</div>
			<?php endforeach;?>
			<?php group_end($this);?>
		<?php endforeach;?>
	<?php endif; ?>

	<?php if(count($this->meta)):?>
		<?php $started = true?>
		<?php group_start($this, JText::_('CSEO'), 'tab-meta');?>
			<?php foreach ($this->meta as $label => $meta_name):?>
				<div class="control-group odd<?php echo $k = 1 - $k ?>">
					<label id="jform_meta_descr-lbl" class="control-label" title="" for="jform_<?php echo $meta_name;?>">
					<?php echo JText::_($label); ?>
					</label>
					<div class="controls">
						<div class="row-fluid">
							<?php echo $this->form->getInput($meta_name); ?>
						</div>
					</div>
				</div>
			<?php endforeach;?>

		<?php group_end($this);?>
	<?php endif;?>



	<?php if(count($this->core_admin_fields)):?>
		<?php $started = true?>
		<?php group_start($this, 'Special Fields', 'tab-special');?>
			<div class="admin">
			<?php foreach($this->core_admin_fields as $key => $field ):?>
				<div class="control-group odd<?php echo $k = 1 - $k ?>">
					<label id="jform_<?php echo $field?>-lbl" class="control-label" for="jform_<?php echo $field?>" ><?php echo strip_tags($this->form->getLabel($field));?></label>
					<div class="controls field-<?php echo $field;  ?>">
						<?php echo $this->form->getInput($field); ?>
					</div>
				</div>
			<?php endforeach;?>
			</div>
		<?php group_end($this);?>
	<?php endif;?>

  <?php
    $profile_id = JFactory::getApplication()->input->getInt('id', 0);
    $user_id = DeveloperPortalApi::getUserIdByProfileId($profile_id);
  ?>
  	<script type="text/javascript">
	var profile_id =false;
	<?php  if($profile_id == 0):?>

	profile_id=true;
	<?php endif;?>

	</script>

	<?php if(count($this->core_fields)):?>
		<?php group_start($this, 'Core Fields', 'tab-core');?>
		<?php foreach($this->core_fields as $key => $field ):?>
			<div class="control-group odd<?php echo $k = 1 - $k ?>">
				<label id="jform_<?php echo $field?>-lbl" class="control-label" for="jform_<?php echo $field?>" >
					<?php if($params->get('tmpl_core.form_'.$field.'_icon', 1)):?>
						<?php echo HTMLFormatHelper::icon('core-'.$field.'.png');  ?>
					<?php endif;?>
					<?php echo strip_tags($this->form->getLabel($field));?>
				</label>
				<div class="controls">
					<?php echo $this->form->getInput($field); ?>
				</div>
			</div>
		<?php endforeach;?>
		<?php group_end($this);?>
	<?php endif;?>

	<?php if($started):?>
		<?php total_end($this);?>
	<?php endif;?>
	<br />
</div>
<script>
  (function($) {
    SqueezeBox.initialize({
      onClose: function() {
        setUserProfile();
      }
    });
    function setUserProfile() {

        <?php //echo DeveloperPortalApi::getUserIdByProfileId($this->item->id);?>
      var id = $('#77_id').val()||"<?php echo $this->item->id?DeveloperPortalApi::getUserIdByProfileId($this->item->id):0;?>";
      if (id && id != 0) {
        //TODO: call an api to get username and email
        $.ajax({
          url: GLOBAL_CONTEXT_PATH + 'index.php?option=com_cobalt&task=ajaxMore.getUserByUid&uid=' + id
        }).done(function(data) {
          var result = JSON.parse(data).result;
          $('#field_101').val(result.username).attr('readonly', 'true');
          $('#field_102').val(result.email).attr('readonly', 'true');
        });
      }
    }
    setUserProfile();
  }(jQuery));
</script>

<script type="text/javascript">
	<?php if(in_array($params->get('tmpl_params.form_grouping_type', 0), array(1,4))):?>
		jQuery('#tabs-list a:first').tab('show');
	<?php elseif(in_array($params->get('tmpl_params.form_grouping_type', 0), array(2))):?>
		jQuery('#tab-main').collapse('show');
	<?php endif;?>

<?php if($userprofile):?>
  (function($){
    var USER_PROFILE_FORM_SUBMIT_BUTTON_CLASS = 'asg-submit-userprofile-form';
    $("#asg-userprofile-form").parents("form").find("#field_102").attr('readonly', 'true');
    /**
     * Get the fields which are required to update the Joomla User Profile
     * @return {Object} if the validation is failed, return Null, otherwise return request data object
     */
    function _getDataForJoomla(){
      var flag          =   true;
          form          =   $("#asg-userprofile-form").parents("form"),
          $oldPwdBox    =   form.find("#asg-userprofile-oldPwd input"),
          $oldPwd       =   $oldPwdBox.val(),
          $pwd1Box      =   form.find("#asg-userprofile-pwd1 input"),
          $pwd1         =   $pwd1Box.val(),
          $pwd2Box      =   form.find("#asg-userprofile-pwd2 input"),
          $pwd2         =   $pwd2Box.val(),
          $usernameBox  =   form.find("#jform_title"),
          $username     =   $usernameBox.val(),
          $nameBox      =   form.find("#field_101"),
          $name         =   $nameBox.val(),
          $emailBox     =   form.find("#field_102"),
          $email        =   $emailBox.val();
          $token        =   form.find("input[type='hidden'][value='1']").attr("name");


      if(!$name.length){
        _getAlertBox.apply($nameBox).text("Name is invalid!").slideDown();
        flag = false;
      }
      if(!$oldPwd.length){
        _getAlertBox.apply($oldPwdBox).text("Current Password can not is null.").slideDown();
        flag = false;
      }

      if(!$username.length){
        _getAlertBox.apply($usernameBox).text("username is invalid!").slideDown();
        flag = false;
      }


      if(!$email.length || !/^(.+)@(.+)$/.test($email)){
        _getAlertBox.apply($emailBox).text("Email address is invalid!").slideDown();
        flag = false;
      }


      if($pwd1 !== $pwd2){
        _getAlertBox.apply($pwd2Box).text("Please check your password!").slideDown();
        flag = false;
      }

      if(!flag){
        return null;
      }
      var data          =  {
                              'option'              :'com_users',
                              'task'                :'profile.save',
                              'jform[password1]'    :$pwd1,
                              'jform[password2]'    :$pwd1,
                              'jform[email1]'       :$email,
                              'jform[email2]'       :$email,
                              'jform[name]'         :$username,
                              'jform[username]'     :$name,
                              'jform[id]'           :"<?php echo $user_id;?>"
                            };

          data[$token]  =  1;
      return data;
    }


    function _getAlertBox(){
      return this.parents("div.control-group").find("div.alert");
    }

    function _resetAlert(){
      _getAlertBox.apply(this).slideUp();
    }


    function _renderForJoomlaProfile(){
        var actions       = $("#adminForm").find(".form-actions").show(),
            submitButton  = actions.find("button[onclick*='form.save']");

            submitButton.removeAttr("onclick")
                        .addClass(USER_PROFILE_FORM_SUBMIT_BUTTON_CLASS);

    }

    $(function(){
      $("input").focus(function(){
        _resetAlert.apply($(this));
      });

      $("."+USER_PROFILE_FORM_SUBMIT_BUTTON_CLASS).live('click',function(){
        var data = _getDataForJoomla();

        if(data === null){return false;}
          $.post(GLOBAL_CONTEXT_PATH + 'index.php?option=com_cobalt&task=ajaxmore.validateOldPassword',{ email : data['jform[email1]'], oldPassword : $oldPwd },
                function(res){
                  if ( res.success == 0 ) {
                                  _getAlertBox.apply($oldPwdBox).text(res.error).slideDown();
                    } else {
                      validatePassword();
                  }
                      },'json');
        function validatePassword() {
          $.post(GLOBAL_CONTEXT_PATH+'index.php?option=com_cobalt&task=ajaxmore.validatePasswordRules',{ password : data['jform[password1]'] },
          	    function(res){
        	    		if ( res.success == 0 ) {
        	    			_getAlertBox.apply($pwd1Box).html(res.error).slideDown();
        	  	    	} else {
  	      	  	    	$.post(GLOBAL_CONTEXT_PATH, data, function(){
  	      	  	          Joomla.submitbutton('form.save');
  	      	  	        });
            	  	    }
             		},'json');
        }
        return false;
      });

      asgUserProfile?_renderForJoomlaProfile():$("#adminForm").find(".form-actions").show();

    });
  })(jQuery);
  <?php endif;?>
</script>
<?php
function group_start($data, $label, $name)
{
	static $start = false;
	switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0))
	{
		//tab
		case 4:
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
            if($name != 'tab-main') {
                echo "<legend>{$label}</legend>";
            }
		break;
	}
}

function group_end($data)
{
	switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0))
	{
		case 4:
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
	switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0))
	{
		//tab
		case 4:
		case 1:
			echo '</div></div>';
		break;
		case 2:
			echo '</div>';
		break;
	}
}
?>
