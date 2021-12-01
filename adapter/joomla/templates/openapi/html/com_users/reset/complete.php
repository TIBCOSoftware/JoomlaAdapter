<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
?>
<div class="reset-complete<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<form id="complete" action="<?php echo JRoute::_('index.php?option=com_users&task=reset.complete'); ?>" method="post" class="form-validate">
            <div id="password_error"></div>
		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
		<p><?php echo JText::_($fieldset->label); ?></p>		<fieldset>
			<dl>
			<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field) : ?>
				<dt><?php echo $field->label; ?></dt>
				<dd><?php echo $field->input; ?></dd>
			<?php endforeach; ?>
			</dl>
		</fieldset>
		<?php endforeach; ?>

		<div>
                    <button type="button" id="subcomplete" class="validate"><?php echo JText::_('JSUBMIT'); ?></button>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
<script>
    jQuery(document).ready(function(){
        jQuery('#subcomplete').click(function(){
            var password1 = jQuery('input[name="jform[password1]"]').val();
            var password2 = jQuery('input[name="jform[password2]"]').val();
            var hidden_name = jQuery('#complete input[type="hidden"]').attr('name');
            var hidden_val = jQuery('#complete input[type="hidden"]').val();
            jQuery.post(GLOBAL_CONTEXT_PATH+'index.php?option=com_cobalt&task=ajaxmore.validatePasswordRules',{ password : password1 }, 
            	    function(res){
          	    		if ( res.success == 0 ) {
              	    		Joomla.showError([res.error]);
          	  	    	} else {
          	  	    		jQuery('#complete').submit();
              	  	    }
               		},'json');
       		return false;
        });
    });
</script>