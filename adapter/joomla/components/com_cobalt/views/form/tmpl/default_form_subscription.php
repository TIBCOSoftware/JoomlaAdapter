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
$item_fields = json_decode($this->item->fields, true);
if (isset($_GET['sub_product_id'])) {
$productID = $_GET['sub_product_id'];
} 
else {
$productID = $item_fields[114];
}
if (isset($_GET['sub_plan_id'])) {
$planID = $_GET['sub_plan_id'];
} 
else {
$planID = $item_fields[69];
}

if (!$orgID = JRequest::getVar('organization_id',0))
{
	$orgID = $item_fields[73];
} 
$sub_uid = $_GET['sub_uid'];
if(!empty($productID) && !empty($planID)){
	$product = DeveloperPortalApi::getRecordById($productID);
	$plan = DeveloperPortalApi::getRecordById($planID);
}

if($orgID){
	$org = DeveloperPortalApi::getRecordById($orgID);
}
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
	
</style>

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

    <?php if($params->get('tmpl_params.tab_main_descr')):?>
        <?php echo $params->get('tmpl_params.tab_main_descr'); ?>
	<?php endif;?>

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


	<?php if(isset($this->sorted_fields[0])):?>
		<?php foreach ($this->sorted_fields[0] as $field_id => $field):?>
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
				<?php echo $field->result;?>
			</div>
		</div>
		<?php endforeach;?>
		<?php unset($this->sorted_fields[0]);?>
	<?php endif;?>

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

<script type="text/javascript">
	<?php if(in_array($params->get('tmpl_params.form_grouping_type', 0), array(1,4))):?>
		jQuery('#tabs-list a:first').tab('show');
	<?php elseif(in_array($params->get('tmpl_params.form_grouping_type', 0), array(2))):?>
		jQuery('#tab-main').collapse('show');
	<?php endif;?>
	<?php if($planID && $productID):?>
		var selected_product = '<div class="alert alert-info list-item" rel="'
														+"<?php echo $product->id;?>"
														+'"><span>'
														+"<?php echo $product->title;?>"
														+'</span><input type="hidden" name="jform[fields][114]" value="'+
														+"<?php echo $product->id;?>"
														+'"></div>';

		var selected_plan 	 = '<div class="alert alert-info list-item" rel="'
														+"<?php echo $plan->id;?>"
														+'"><span>'
														+"<?php echo $plan->title;?>"
														+'</span><input type="hidden" name="jform[fields][69]" value="'+
														+"<?php echo $plan->id;?>"
														+'"></div>';
		jQuery("#parent_list114").html(selected_product);
		jQuery("#parent_list69").html(selected_plan);
		jQuery("a[href='#modal114']").remove();
		jQuery("a[href='#modal69']").remove();
	<?php endif;?>

	<?php if($orgID):?>
		var selected_organization 	 = '<div class="alert alert-info list-item" rel="'
																		+"<?php echo $org->id;?>"
																		+'"><span>'
																		+"<?php echo $org->title;?>"
																		+'</span><input type="hidden" name="jform[fields][73]" value="'+
																		+"<?php echo $org->id;?>"
																		+'"></div>';
		jQuery("#parent_list73").html(selected_organization);
		jQuery("a[href='#modal73']").remove();
	<?php endif;?>

	<?php if(!$this->item->id):?>
		function getRecordIdFromRedirectURI(sRedirectUrl){
			var url = document.getElementById("iframe_form_submission").contentWindow.RecordTemplate.nRecordId;
			return url;
		}
		(function($) {
					Joomla.submitform = function(task) {
						      DeveloperPortal.submitForm(
						      	task, 
						      	function(nObjectId, sRedirectUrl) {
						      			var data = {};
						      			data.product_id = $("#parent_list114 > .list-item").eq(0).attr("rel");
						      			data.subscription_id = getRecordIdFromRedirectURI(sRedirectUrl);

						      			<?php if($productID && $sub_uid):?>
						      				data.requester_uid = '<?php echo $sub_uid;?>';
						      			<?php else:?>
						      				data.requester_uid = "<?php echo $this->user->id;?>";
						      			<?php endif;?>
										$.post(
											GLOBAL_CONTEXT_PATH+"index.php?option=com_cobalt&task=ajaxMore.subscriptionDidCreate", 
											data,
											null,
											'json')
											.done(function(data){
											if(!data.success){
												DeveloperPortal.storeErrMsgInCookie(['<?php echo JText::_("EMAIL_RETURN_NOTES_5");?>']);
											}

											window.location.href = sRedirectUrl;
										}).fail(function(){
										    DeveloperPortal.storeErrMsgInCookie(['<?php echo JText::_("EMAIL_RETURN_NOTES_5");?>']);
											window.location.href = sRedirectUrl;
										});
						      	}, function(sRedirectUrl) {
						      	    if(sRedirectUrl !== GENERIC_ERROR_MESSAGE) {
							      			var data = {};
							      			data.product_id = $("#parent_list114 > .list-item").eq(0).attr("rel");
							      			data.subscription_id = getRecordIdFromRedirectURI(sRedirectUrl);
						      			  <?php if($productID && $sub_uid):?>
							      				data.requester_uid = '<?php echo $sub_uid;?>';
							      			<?php else:?>
							      				data.requester_uid = "<?php echo $this->user->id;?>";
							      			<?php endif;?>
													$.post(
														GLOBAL_CONTEXT_PATH+"index.php?option=com_cobalt&task=ajaxMore.subscriptionDidCreate", 
														data,
														null,
														'json')
														.done(function(data){
														if(!data.success){
															DeveloperPortal.storeErrMsgInCookie(['<?php echo JText::_("EMAIL_RETURN_NOTES_5");?>']);
														}
														window.location.href = sRedirectUrl;
													}).fail(function(){
													    DeveloperPortal.storeErrMsgInCookie(['<?php echo JText::_("EMAIL_RETURN_NOTES_5");?>']);
														window.location.href = sRedirectUrl;
													});
						      	    }
							    });
					};
		}(jQuery));
	<?php endif;?>

	(function($) {
		window.oSubscriptionForm = {};
	    $(function() {
	        oSubscriptionForm.nPlanId = $('input[name="jform[fields][69]"]').val();
	        oSubscriptionForm.sStartDate = $('input[name="jform[fields][71][]"]').val();
	        oSubscriptionForm.sEndDate = $('input[name="jform[fields][72][]"]').val();
	        oSubscriptionForm.sStatus = DeveloperPortal.getRadioButtonsValue('jform[fields][78]');
		});

		function isEndDateValid() {
		    var sStartDate = $('input[name="jform[fields][71][]"]').val(),
		        sEndDate = $('input[name="jform[fields][72][]"]').val(), rv = false;

		    if(typeof(sEndDate) ==="undefined"){
		    	sEndDate = "2038-01-01";
				jQuery('#field_container72').append(jQuery('<input type="hidden" name="jform[fields][72][]" value="">'));
				jQuery('input[name="jform[fields][72][]"]').val(sEndDate);
		    }
	        if(sStartDate && sEndDate) {
	            try {
	                if(Date.parse(sEndDate).getTime() >= Date.parse(sStartDate).getTime()) {
	                    rv = true;
	                }
	            } catch(oE) {
	                // Should've set rv to false but since it's initialized with false so do nothing
	            }
	        }
	        return rv;
		};

		Joomla.beforesubmitform = function(fCallback, fErrorback) {
			if(isEndDateValid()) {
    	        var nPlanId = $('input[name="jform[fields][69]"]').val(),
    	        sStartDate = $('input[name="jform[fields][71][]"]').val(),
    	        sEndDate = $('input[name="jform[fields][72][]"]').val(),
    	        sStatus = DeveloperPortal.getRadioButtonsValue('jform[fields][78]');
    	        if(nPlanId !== oSubscriptionForm.nPlanId || sStartDate !== oSubscriptionForm.sStartDate || sEndDate !== oSubscriptionForm.sEndDate || sStatus !== oSubscriptionForm.sStatus) {
    		        window.oUpdatedFields = {};
       		    }
    	        if(nPlanId !== oSubscriptionForm.nPlanId) {
    		        window.oUpdatedFields[69] = oSubscriptionForm.nPlanId;
    	        }
    	        if(sStartDate !== oSubscriptionForm.sStartDate) {
    		        window.oUpdatedFields[71] = oSubscriptionForm.sStartDate;
    	        }
    	        if(sEndDate !== oSubscriptionForm.sEndDate) {
    		        window.oUpdatedFields[72] = oSubscriptionForm.sEndDate;
    	        }
    	        if(sStatus !== oSubscriptionForm.sStatus) {
    		        window.oUpdatedFields[78] = oSubscriptionForm.sStatus;
    	        }
    	        fCallback();
			} else {
			    Joomla.showError([INVALID_SUBSCRIPTION_END_DATE]);
			}
		};
    var orgProductId = jQuery('#parent_list114 div.alert.alert-info.list-item').attr('rel');
    jQuery('#parent_list114').on('click', 'a.close', function() {
      jQuery('#parent_list69').html('');
    });
    jQuery('#modal114').on('hide', function() {
      var curProductId = jQuery('#parent_list114 div.alert.alert-info.list-item').attr('rel');
      if (curProductId != orgProductId) {
        jQuery('#parent_list69').html('');
        orgProductId = curProductId;
      }
    });
	}(jQuery));
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
