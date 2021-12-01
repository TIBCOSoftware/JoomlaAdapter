<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
//load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);
?>
<style>
  #member-changePassword legend {
    border-bottom: 10px solid #006699;
  }
</style>
<div class="profile-edit<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>
<?php
  $fields = $this->form->getFieldset('core');
?>
  <div id="member-changePassword">
		<legend>Change password <?php echo JText::_($fieldset->label); ?></legend>
    <div class="control-group row-fluid">
      <div class="control span5">
        <label>Email:</label>
        <input type="text" value="<?php echo $fields['jform_email1']->value;?>" readonly="true"></input>
      </div>
    </div>
    <div class="control-group row-fluid">
      <div class="control span5">
        <label for="curpwd" class="required" aria-invalid="true">Current password:</label>
        <input id="curpwd" type="password" name="curpwd" class="required" aria-invalid="true"></input>
      </div>
    </div>
    <div class="control-group row-fluid">
      <div class="control span5">
        <label for="newpwd" class="required" aria-invalid="true">New password:</label>
        <input id="newpwd" type="password" name="newpwd" class="required" aria-invalid="true"></input>
      </div>
    </div>
    <div class="control-group row-fluid">
      <div class="control span5">
        <label for="cfmpwd" class="required" aria-invalid="true">Confirm password:</label>
        <input id="cfmpwd" type="password" name="cfmpwd" class="required" aria-invalid="true"></input>
      </div>
    </div>
	</div>
  <div class="form-actions">
    <button class="btn btn-primary btn-submit"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
    <a class="btn" href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>
  </div>
</div>
<script>

Joomla.JText.load({
	  "success": "Success"
	});


Joomla.showSuccess = function(msg) {
jQuery('.btn-submit').removeAttr('disabled');
////////////////////////////////////////////////////////////////////
////////// Show the success messages at the top of the page //////////
////////////////////////////////////////////////////////////////////
_renderMessages({
    "success": msg
});
};

  (function($) {
    $('.btn.btn-primary.btn-submit').on('click', function() {
      $(this).prop('disabled', true);
      var messages = [];
      var curpwd = $('#curpwd').val();
      var newpwd = $('#newpwd').val();
      var cfmpwd = $('#cfmpwd').val();
      if (!curpwd) {
        messages.push('<?php echo JText::_("INVALID_FIELD"); ?>' + ': ' + $('label[for="curpwd"]').text());
      }
      if (!newpwd) {
        messages.push('<?php echo JText::_("INVALID_FIELD"); ?>' + ': ' + $('label[for="newpwd"]').text());
      }
      if (!cfmpwd) {
        messages.push('<?php echo JText::_("INVALID_FIELD"); ?>' + ': ' + $('label[for="cfmpwd"]').text());
      }
      if (newpwd != cfmpwd) {
        messages.push('<?php echo JText::_("INVALID_CONFIRMPWD"); ?>');
      }
      if (messages.length > 0) {
        Joomla.showError(messages);
      } else {
        $.ajax({
          type: 'POST',
          data: {
            new_password: newpwd,
            old_password: curpwd
          },
          url: 'index.php?option=com_cobalt&task=ajaxmore.changePassword'
        }).done(function(responseText) {
          var res = JSON.parse(responseText);
          if (res.success) {
        	  messages.push('<?php echo JText::_('SUCCESS_CHANGE_PASSWORD');?>');
        	  Joomla.showSuccess(messages);
				$('.profile-edit').hide();
				setTimeout(function() {
				      // Do something after 2 seconds
					window.location='<?php echo JURI::base();?>';
				}, 2000);

            //$('.logout-button [type="submit"]').click();
          } else {
            messages.push(res.error);
            Joomla.showError(messages);
          }
        }).fail(function() {
          messages.push('<?php echo JText::_("ERROR_CHANGE_PASSWORD"); ?>');
          Joomla.showError(messages);
        });
      }
    });
  }(jQuery));
</script>
