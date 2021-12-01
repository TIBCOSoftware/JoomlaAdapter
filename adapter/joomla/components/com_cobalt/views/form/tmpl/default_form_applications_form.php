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
require_once JPATH_ROOT . "/includes/api.php";
$started = false;

// Check if this is a superadmin
$is_superadmin = in_array('8', JFactory::getUser()->groups);

// Make sure user is in the same organization or in the SuperAdmin group
if( !$is_superadmin && isset($this->item->id) )
{
	$user_access_id = DeveloperPortalApi::getUserOrganizationAccessLevel();
	if( $this->item->access != $user_access_id ) {
		$formcontroller = new JControllerLegacy();
		$formcontroller->setRedirect(JRoute::_('index.php?option=com_cobalt&view=record&id='.$this->item->id, false));
		$formcontroller->redirect();
		return false;
	}
}
// Keep access the same if edited by a SuperAdmin, otherwise set it to the user's organization
if($is_superadmin && isset($this->item->id)) {
	$access_level = $this->item->access;
	$fields = json_decode($this->item->fields);
	$owner_organization = (array) $fields->{60};
} else {
	$owner_organization = DeveloperPortalApi::getUserOrganization();
	$access_level = DeveloperPortalApi::getUserAccessGroupId();
}

$params = $this->tmpl_params;

if($params->get('tmpl_params.form_grouping_type', 0))
{
	$started = true;
}
$k = 0;
$user_profile_id = DeveloperPortalApi::getUserProfileId();
$product_ids_in_application = DeveloperPortalApi::getProductIdsInApplication($this->item->id);
$old_keys = DeveloperPortalApi::getKeysOfApplication($this->item->id);
$active_key = count($app_oauth) > 0 && $app_oauth[0]->use_oauth == 1 ? DeveloperPortalApi::getActiveOAuthKeyOfApp($this->item->id) : DeveloperPortalApi::getActiveKeyOfApplication($this->item->id);
$comEmail = JComponentHelper::getComponent('com_emails');
$oauthState = $comEmail->params->get('enable_oauth');

?>
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
#fld-64 .btn.active,
#fld-64 .btn:active{
  background-color: #00A2D5;
}
</style>

<div class="form-horizontal asg-create-app-form">
  <div class="asg-app-step-title-container"><div class="asg-app-step-title"><i class="icon-chevron-down"></i>Application name and description</div><div class="asg-app-step-title-container-line"></div></div>
  <div class="asg-app-step-1 tab-content">
    <?php if($this->type->params->get('properties.item_title', 1) == 1):?>
    <div class="control-group odd<?php echo $k = 1 - $k ?>">
      <label id="title-lbl" for="jform_title" class="control-label" >
        <?php if($params->get('tmpl_core.form_title_icon', 1)):?>
          <?php echo HTMLFormatHelper::icon($params->get('tmpl_core.item_icon_title_icon', 'edit.png'));  ?>
        <?php endif;?>

        <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_title', 'Title')) ?>
        <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>">
          <?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
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

    <div id="fld-<?php echo $this->sorted_fields[0][119]->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-119'; ?> <?php echo $this->sorted_fields[0][119]->fieldclass;?>">
      <?php $field = $this->sorted_fields[0][119];?>
      <?php if($field->params->get('core.show_lable') == 1 || $field->params->get('core.show_lable') == 3):?>
        <label id="lbl-<?php echo $field->id;?>" for="field_<?php echo $field->id;?>" class="control-label <?php echo $field->class;?>" >
          <?php if($field->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
            <?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
          <?php endif;?>


          <?php if ($field->required): ?>
            <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
          <?php endif;?>

          <?php if ($field->description):?>
            <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($field->translateDescription ? JText::_($field->description) : $field->description), ENT_COMPAT, 'UTF-8');?>">
              <?php echo HTMLFormatHelper::icon('image.png');  ?>
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
    <div id="fld-<?php echo $this->sorted_fields[0][57]->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-57'; ?> <?php echo $this->sorted_fields[0][57]->fieldclass;?>">
      <?php $field = $this->sorted_fields[0][57];?>
      <?php if($field->params->get('core.show_lable') == 1 || $field->params->get('core.show_lable') == 3):?>
        <label id="lbl-<?php echo $field->id;?>" for="field_<?php echo $field->id;?>" class="control-label <?php echo $field->class;?>" >
          <?php if($field->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
            <?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
          <?php endif;?>


          <?php if ($field->required): ?>
            <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
          <?php endif;?>

          <?php if ($field->description):?>
            <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($field->translateDescription ? JText::_($field->description) : $field->description), ENT_COMPAT, 'UTF-8');?>">
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
		<input id="jform_ucatid" class="required" type="hidden" value="0" name="jform[ucatid]" aria-required="true" required="required">
		<input type="hidden" name="jform[fields][58]" value="<?php echo $user_profile_id ? $user_profile_id : '';?>">
		<input type="hidden" name="jform[fields][60]" value="<?php echo $owner_organization[0]; ?>">
        <?php if($access_level):?>
        <input type="hidden" id="jform_access" name="jform[access]" value="<?php echo $access_level;?>" />
        <?php endif;?>
  </div>
  <div class="asg-app-step-title-container"><div class="asg-app-step-title"><i class="icon-chevron-down"></i>Products</div><div class="asg-app-step-title-container-line"></div></div>
  <div class="asg-app-step-2 tab-content">
    <?php echo $this->loadTemplate('record_app_create2');?>
  </div>
  <?php $oauthContent = $this->sorted_fields[0][64]; $oauthValue = $oauthContent->value;?>
  <?php if ($oauthState!=3 || $oauthValue==1): ?>
  <div class="asg-app-step-title-container"><div class="asg-app-step-title"><i class="icon-chevron-down"></i>Scope</div><div class="asg-app-step-title-container-line"></div></div>
  <div class="asg-app-step-3 tab-content">
  <!-- Step3 goes here -->
    <div class="row-fluid">
      <div class="span12">
        <?php $apis = $oauthContent; ?>
        <div id="fld-<?php echo $apis->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-7' ?> <?php echo $apis->fieldclass;?>">
          <?php if($apis->params->get('core.show_lable') == 1 || $apis->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $apis->id;?>" for="field_<?php echo $apis->id;?>" class="control-label <?php echo $apis->class;?>" >
              <?php if($apis->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($apis->params->get('core.icon'));  ?>
              <?php endif;?>
              <?php if ($apis->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($apis->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;" data-original-title="<?php echo htmlspecialchars(($apis->translateDescription ? JText::_($apis->description) : $apis->description), ENT_COMPAT, 'UTF-8');?>">
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
            <div id="apis-alert-<?php echo $apis->id?>" class="alert alert-error" style="display:none"></div>
      <?php echo $apis->result; ?>
      <?php if ($oauthState==2 && (empty($this->item->id)||$oauthValue==1)): ?>
        <input type="hidden" name="jform[fields][64]" value="1">
      <?php endif ?>
          </div>
        </div>

        <?php $apis = $this->sorted_fields[0][125]; ?>
        <div id="fld-<?php echo $apis->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-7' ?> <?php echo $apis->fieldclass;?>" style="display:none;">
          <?php if($apis->params->get('core.show_lable') == 1 || $apis->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $apis->id;?>" for="field_<?php echo $apis->id;?>" class="control-label <?php echo $apis->class;?>" >
              <?php if($apis->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($apis->params->get('core.icon'));  ?>
              <?php endif;?>
              <?php if ($apis->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($apis->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;" data-original-title="<?php echo htmlspecialchars(($apis->translateDescription ? JText::_($apis->description) : $apis->description), ENT_COMPAT, 'UTF-8');?>">
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
            <div id="apis-alert-<?php echo $apis->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $apis->result; ?>
          </div>
        </div>

        <?php $redirect_url = $this->sorted_fields[0][65]; ?>
        <div id="fld-<?php echo $redirect_url->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-65' ?> <?php echo $redirect_url->fieldclass;?>" style="display:none;">
          <?php if($redirect_url->params->get('core.show_lable') == 1 || $redirect_url->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $redirect_url->id;?>" for="field_<?php echo $redirect_url->id;?>" class="control-label <?php echo $redirect_url->class;?>" >
              <?php if($redirect_url->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($redirect_url->params->get('core.icon'));  ?>
              <?php endif;?>
              <?php if ($redirect_url->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($redirect_url->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;" data-original-title="<?php echo htmlspecialchars(($redirect_url->translateDescription ? JText::_($redirect_url->description) : $redirect_url->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>
              <?php echo $redirect_url->label; ?>
            </label>
            <?php if(in_array($redirect_url->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($redirect_url->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($redirect_url->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $redirect_url->fieldclass  ?>">
            <div id="apis-alert-<?php echo $redirect_url->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $redirect_url->result; ?>
          </div>
        </div>

        <?php $uuid = $this->sorted_fields[0][104]; ?>
        <div id="fld-<?php echo $uuid->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-104' ?> <?php echo $uuid->fieldclass;?>">
          <?php if($uuid->params->get('core.show_lable') == 1 || $uuid->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $uuid->id;?>" for="field_<?php echo $uuid->id;?>" class="control-label <?php echo $uuid->class;?>" >
              <?php if($uuid->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($uuid->params->get('core.icon'));  ?>
              <?php endif;?>
              <?php if ($uuid->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($uuid->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;" data-original-title="<?php echo htmlspecialchars(($uuid->translateDescription ? JText::_($uuid->description) : $uuid->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>
              <?php echo $uuid->label; ?>
            </label>
            <?php if(in_array($uuid->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($uuid->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($uuid->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $uuid->fieldclass  ?>">
            <div id="apis-alert-<?php echo $uuid->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $uuid->result; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif ?>
<script>
    var ApplicationForm = {
        oauth: DeveloperPortal.getRadioButtonsValue('jform[fields][64]'),
        aOldKeys: <?php echo makeArrayString($old_keys); ?>,
        nActiveKeyCount: <?php echo count($active_key); ?>
    }, bUseOAuth;

   function toggleScopes(){
     var scopes_box             = jQuery("#fld-125"),
         scopes_list            = jQuery("#parent_list125"),
         redirect_url_box       = jQuery("#fld-65"),
         redirect_url_box_list  = redirect_url_box.find(".url-item.row-fluid:eq(0)"),
         is_oauth               = jQuery('#bool-y64.active');

     if(parseInt(jQuery('input[name="jform[fields][64]"]:checked').val()) === 1){
       scopes_box.show();
       redirect_url_box.show();
     }else{
       scopes_box.hide();
       redirect_url_box.hide()
       redirect_url_box_list.find("input").val('');
       scopes_list.empty();
     }
   }

  jQuery(function() {
      jQuery('.asg-app-step-title-container .asg-app-step-title').on('click', '.icon-chevron-down', function() {
        jQuery(this).removeClass('icon-chevron-down').addClass('icon-chevron-right');
        jQuery(this).parent().parent().next().hide();
      });
      jQuery('.asg-app-step-title-container .asg-app-step-title').on('click', '.icon-chevron-right', function() {
        jQuery(this).removeClass('icon-chevron-right').addClass('icon-chevron-down');
        jQuery(this).parent().parent().next().show();
      });
			<?php if($oauthState == 3): ?>
				var node = jQuery('.asg-create-app-guide li').eq(2);
				if (node.html()!==undefined) {
					node.remove();
				}
            <?php elseif($oauthState == 1): ?>
			    <?php if(empty($this->item->id)): ?>
                    jQuery('#bool-y64').addClass('active btn-success').prev('input').attr('checked', true);
                <?php endif; ?>
                <?php if(count($old_keys) > 0): ?>
                    bUseOAuth = jQuery('input:checked[name="jform[fields][64]"]').val();
                <?php endif; ?>
		    <?php else:?>
                <?php if(count($old_keys) > 0): ?>
                    bUseOAuth = jQuery('input:checked[name="jform[fields][64]"]').val();
                <?php endif; ?>
			<?php endif;?>

			<?php if($oauthState==2 && (empty($this->item->id)||$oauthValue==1)): ?>
                jQuery('#bool-y64').addClass('active btn-success').prev('input').attr('checked', true);
                jQuery('#fld-64').hide();
			<?php endif;?>

  	  ApplicationForm.aOldProducts = [];
      ApplicationForm.aOldScriptions = [];
      ApplicationForm.aOldScopes = [];
      jQuery('input[name="jform[fields][63][]"]').each(function(index, item) {
          ApplicationForm.aOldProducts.push(jQuery(item).val());
      });
      jQuery('input[name="jform[fields][115][]"]').each(function(index, item) {
          ApplicationForm.aOldScriptions.push(jQuery(item).val());
      });
      jQuery('input[name="jform[fields][125][]"]').each(function(index, item) {
          ApplicationForm.aOldScopes.push(jQuery(item).val());
      });
      toggleScopes();
      jQuery('#bool-y64').on("click.scope",toggleScopes);
      jQuery('#bool-n64').on("click.scope",toggleScopes);

  });

  Joomla.beforesubmitform = function(fCallback, fErrorback) {
      ApplicationForm.aNewProducts = [];
		  ApplicationForm.aNewScriptions = [];
		  ApplicationForm.aNewScopes = [];
      jQuery('input[name="jform[fields][63][]"]').each(function(index, item) {
          ApplicationForm.aNewProducts.push(jQuery(item).val());
      });
      jQuery('input[name="jform[fields][115][]"]').each(function(index, item) {
          ApplicationForm.aNewScriptions.push(jQuery(item).val());
      });
      jQuery('input[name="jform[fields][125][]"]').each(function(index, item) {
          ApplicationForm.aNewScopes.push(jQuery(item).val());
      });

      var paramFields = {};
      if(!DeveloperPortal.arrayEqual(ApplicationForm.aOldProducts, ApplicationForm.aNewProducts)) {
		  paramFields[63] = ApplicationForm.aOldProducts;
      }

      if(!DeveloperPortal.arrayEqual(ApplicationForm.aOldScriptions, ApplicationForm.aNewScriptions)) {
		  paramFields[115] = ApplicationForm.aOldScriptions;
      }

      if(!DeveloperPortal.arrayEqual(ApplicationForm.aOldScopes, ApplicationForm.aNewScopes)) {
		  paramFields[125] = ApplicationForm.aOldScopes;
      }
      if ((63 in paramFields) || (115 in paramFields) || (125 in paramFields)) {
          window.oUpdatedFields = paramFields;
      }
      if(bUseOAuth !== undefined && bUseOAuth !== jQuery('input[name="jform[fields][64]"][checked]').val()) {
        jQuery.post(GLOBAL_CONTEXT_PATH + 'index.php?option=com_cobalt&task=ajaxmore.updateStatusOfKey', {
            "keyList": <?php echo makeArrayString($old_keys); ?>
        }).done(function(data) {
            fCallback();
        }).fail(function() {
            DeveloperPortal.showError([DISABLE_KEYS_FAILED]);
        });
      } else {
	      fCallback();
      }
  };
</script>

<?php
    function makeArrayString($array) {
        $rv = '';
        foreach($array as $index => $item) {
            if($index != 0) {
                $rv .= ", ";
            }
            $rv .= $item;
        }
        return "[" . $rv . "]";
    }
?>
