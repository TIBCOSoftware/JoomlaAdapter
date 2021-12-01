<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die();

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

$app = JFactory::getApplication();

// Create shortcut to parameters.
//$params = $this->state->get('params');
?>

<script type="text/javascript"><!--

    Joomla.oldsubmitform = Joomla.submitform;

    Joomla.validate = function() {
        var hfid = [], isValid = true, errorText = [''];
        <?php if($this->type->params->get('properties.item_title', 1) == 1):?>
            if(!document.getElementById('jform_title').value)
            {
                isValid = false;
                errorText.push('<?php echo JText::_('CPLEASESELECTTITEL');?>');
            }
            <?php if($this->type->params->get('properties.item_title_limit')):?>
                if(document.getElementById('jform_title').value.length > <?php echo $this->type->params->get('properties.item_title_limit')?>)
                {
                    isValid = false;
                    errorText.push('<?php echo JText::sprintf('C_MSG_TITLETOLONG', $this->type->params->get('properties.item_title_limit', 0));?>');
                }
            <?php endif; ?>
        <?php endif; ?>

        <?php if($this->section->params->get('personalize.personalize', 0) && in_array($this->section->params->get('personalize.pcat_submit', 0), $this->user->getAuthorisedViewLevels())) : ?>
            if(!document.getElementById('jform_ucatid').getSelected().length)
            {
                isValid = false;
                errorText.push('<?php echo JText::_('CUSERCATSELECT');?>');
            }
        <?php endif; ?>

        <?php if(in_array($this->params->get('submission.allow_category'), $this->user->getAuthorisedViewLevels())&& $this->section->categories):?>

            var catlength = null;
            var cats = jQuery('[name^="jform\\[category\\]"]');

            if(cats.attr('id') == 'category')
            {
                catlength = cats.val().split(',').length;
            }
            else if(cats.attr('id') == 'jformcategory')
            {
                catlength = cats.find('option:selected').length || (cats.val() ? cats.val().length : 0);
            }
            else
            {
                catlength = cats.length;
            }

            if(catlength <= 0 )
            {
                isValid = false;
                errorText.push('<?php echo JText::_('CPLEASESELECTCAT');?>');
            }

            <?php if($this->params->get('submission.multi_category', 0)): ?>
                if(catlength > <?php echo  $this->params->get('submission.multi_max_num', 3) ?>)
                {
                    isValid = false;
                    errorText.push('<?php echo JText::_('CCATEGORYREACHMAXLIMIT');?>');
                }
            <?php endif;?>
        <?php endif;?>

        <?php if($this->anywhere) : ?>

            if(jQuery('#posts-list').children('div.alert').length <= 0 )
            {
                isValid = false;
                errorText.push('<?php echo JText::_('PPLEASEWHERETOPOST');?>');
            }
        <?php endif;?>

        <?php foreach ($this->fields AS $field):?>
            /*<?php echo $field->id.' '.$field->title;?>*/
            <?php if($field->id == 101) : ?>
//                 var txt101 = jQuery('[name^="jform\\[fields\\]\\[101\\]"]').val();
//                 var reg = /[()<>\\&%;"']/;
//                 if(!txt101){
//                   hfid.push(101); 
//                   isValid = false; 
//                   errorText.push('Field Username is required');
//                 } else if (txt101.length < 2 || reg.test(txt101)) {
//                   hfid.push(101); 
//                   isValid = false; 
//                   errorText.push('Please enter a valid username. No spaces, at least 2 characters and must not contain the following characters: < > \ " \' % ; ( ) &');
//                 }
            <?php elseif ($field->id == 2): ?>

                var txt2 = jQuery(document.getElementById('field_2_ifr').contentWindow.document.body).text();
                 if(!txt2){
                   isValid = false; 
                   errorText.push('Description is required.');
                 }
            <?php elseif ($field->id == 55): ?>
                var txt55 = jQuery(document.getElementById('field_55_ifr').contentWindow.document.body).text();
                 if(!txt55){
                   isValid = false; 
                   errorText.push('<?php echo JText::_('PLAN_DETAILS_IS_REQUIRED');?>');
                 }
            <?php else : ?>
              <?php echo $field->js;?>
            <?php endif;?>
        <?php endforeach;?>
        if(isValid) {
            return true;
        } else {
            return errorText;
        }
    };
	
    Joomla.checkUploadState = function() {
	  var uploadFlag = true;
    	  jQuery("div.mooupload_listview").each(function(){
		  var nodes = jQuery(this).find("ul li:not(:first-child)");
		  if (nodes && nodes.length>0) {
			  nodes.each(function(){
				 var node = jQuery(this).find("div.result");
			     if (node.html()=='') {
					 uploadFlag = false;
					 return uploadFlag;
			     }
			  });
		  }
    	  });
	  return uploadFlag;
    }

    Joomla.submitbutton = function(task) {

        jQuery('.btn-submit').attr('disabled', 'disabled');
        if (task == 'form.cancel')
        {
          	jQuery('#adminForm').removeAttr('target');
            Joomla.oldsubmitform(task);
            return;
        }

		var bValid = true;
		
		//check for attached files upload state
		if (Joomla.checkUploadState() === false) {
			bValid = ['<?php echo JText::_("CREATE_NEW_UPLOAD_NOT_READY"); ?>'];
		}else {
			bValid = Joomla.validate();
		}
		
        if(bValid === true) {
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////// This "beforesubmitform" function is supposed to be used by individual form to do any supplementary data insertion to the form or validation. //////////
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if(typeof Joomla.beforesubmitform === 'function') {
                Joomla.beforesubmitform(function() {
                    Joomla.submitform(task);
                }, function(errorMsg) {
                    Joomla.showError([errorMsg]);
                });
            } else {
                Joomla.submitform(task);
            }
        } else {
            Joomla.showError(bValid);
        }
    };

    Joomla.submitform = function(task) {
        DeveloperPortal.submitForm(task);
    }

--></script>
<?php if ($this->tmpl_params->get('properties.form_heading', 1)): ?>
	<h1>
			<?php if($this->item->id):?>
					<?php echo JText::sprintf('CTEDIT', $this->escape($this->type->name), $this->item->title); ?>
			<?php else:?>
				     <?php if($this->type->id==9):?> 
                      <?php $createFormText=  JText::_('REGISTER_APP').JText::_($this->type->name); 
                       echo $createFormText; ?>	
                      <?php else:?> 
                      <?php $createFormText= JText::sprintf('CTSUBMIT', $this->escape($this->type->name));
                       echo $createFormText;?>           
					   <?php endif; ?>
			<?php endif; ?>

        <?php if($this->parent):?>
            - <?php echo $this->parent; ?>
        <?php endif; ?>
    </h1>
<?php endif; ?>

<div class="alert alert-warning" style="display:none" id="form-error"></div>

<?php if($this->type->description):?>
	<?php echo $this->type->description;?>
	<br />
<?php endif; ?>

<?php if(!$this->user->get('id') && $this->type->params->get('submission.public_alert') && ($this->type->params->get('submission.public_edit') == 0)):?>
	<div class="alert alert-warning"><?php echo $this->tmpl_params->get('tmpl_core.form_public_alert', JText::_('CNOTREGISTERED'));?></div>
	<br />
<?php endif;?>

<form method="post" action="<?php echo JUri::getInstance()->toString()?>" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data" novalidate="novalidate">

	<?php if($this->tmpl_params->get('tmpl_core.show_top_tab')):?>
		<?php echo $this->loadTemplate('tab_'.$this->params->get('properties.tmpl_articleform'));?>
	<?php endif; ?>
	<?php if(in_array($this->tmpl_params->get('tmpl_core.form_button_position', 1), array(1,3))):?>
		<?php echo $this->loadTemplate('buttons');?>
	<?php endif;?>

	<?php echo $this->loadTemplate('form_'.$this->params->get('properties.tmpl_articleform'));?>

	<?php if($this->tmpl_params->get('tmpl_core.form_captcha', 1) && !$this->user->get('id')):?>
		<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label">&nbsp;</label>
				<div class="controls">
					<?php  echo $this->form->getInput('captcha'); ?>
				</div>
			</div>
		</div>
	<?php endif;?>

	<?php if(in_array($this->tmpl_params->get('tmpl_core.form_button_position', 1), array(2,3))):?>
		<?php echo $this->loadTemplate('buttons');?>
	<?php endif;?>

	<?php echo $this->form->getInput('section_id'); ?>
	<?php echo $this->form->getInput('type_id'); ?>
	<?php echo $this->form->getInput('id'); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $app->input->getInt('id', 0);?>" />
	<input type="hidden" name="Itemid" value="<?php echo $app->input->getInt('Itemid');?>" />
	<input type="hidden" name="return" value="<?php echo $app->input->getBase64('return');?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>



<?php if($h = $app->getUserState('com_cobalt.fieldhighlights')):?>
	<script type="text/javascript">
		<?php foreach ($h AS $field_id => $msg):?>
			Cobalt.fieldError(<?php echo $field_id?>, '<?php echo $msg?>');
		<?php endforeach;?>
	</script>
	<?php $app->setUserState('com_cobalt.fieldhighlights', NULL);?>
<?php endif;?>