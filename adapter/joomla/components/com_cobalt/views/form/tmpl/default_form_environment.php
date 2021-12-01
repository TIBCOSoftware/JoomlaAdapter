<?php
/* Portions copyright © 2015, TIBCO Software Inc.
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
$params = $this->tmpl_params;
if($params->get('tmpl_params.form_grouping_type', 0))
{
	$started = true;
}
$k = 0;
// pr($this->form);
// pr($this->form->getInput("id"));
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
#tabs-list {
  display: none;
}

#tabs-box {
  border-style: none;
}	


.managed_by_gateway .btn{
  background-color: #006699;
}


#fld-144 .btn:active,
#fld-144 .btn.active,
#fld-142 .btn:active,
#fld-142 .btn.active,
#fld-132 .btn:active,
#fld-132 .btn.active {
 background-color:  #2E9ACF;
}

.view-form .container > .row-fluid #content > form .form-horizontal .tab-content .control-group.is_enabled_ssl{
  display: none;
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
  <div class="asg-create-org-step1">
    <div class="row-fluid">
      <div class="span8">
        <?php if($params->get('tmpl_params.tab_main_descr')):?>
            <?php echo $params->get('tmpl_params.tab_main_descr'); ?>
        <?php endif;?>
        <!-- title goes here -->
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
        <!-- something goes here -->
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
        <!-- description goes here -->
        <?php $description = $this->sorted_fields[0][12]?>
        <div id="fld-<?php echo $description->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-12'; ?> <?php echo $description->fieldclass;?>">
          <?php if($description->params->get('core.show_lable') == 1 || $description->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $description->id;?>" for="field_<?php echo $description->id;?>" class="control-label <?php echo $description->class;?>" >
              <?php if($description->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($description->params->get('core.icon'));  ?>
              <?php endif;?>
                
              
              <?php if ($description->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($description->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($description->translateDescription ? JText::_($description->description) : $description->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $description->label; ?>
              
            </label>
            <?php if(in_array($description->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($description->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($description->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $description->fieldclass  ?>">
            <div id="field-alert-<?php echo $description->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $description->result; ?>
          </div>
        </div>
        <!-- type goes here -->
        <?php $type = $this->sorted_fields[0][13]?>
        <div id="fld-<?php echo $type->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-13'; ?> <?php echo $type->fieldclass;?>">
          <?php if($type->params->get('core.show_lable') == 1 || $type->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $type->id;?>" for="field_<?php echo $type->id;?>" class="control-label <?php echo $type->class;?>" >
              <?php if($type->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($type->params->get('core.icon'));  ?>
              <?php endif;?>
                
              
              <?php if ($type->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($type->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($type->translateDescription ? JText::_($type->description) : $type->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $type->label; ?>
              
            </label>
            <?php if(in_array($type->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($type->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($type->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $type->fieldclass  ?>">
            <div id="field-alert-<?php echo $type->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $type->result; ?>
          </div>
        </div>
        <!-- basepath goes here -->
        <?php $basepath = $this->sorted_fields[0][14]; ?>
        <div id="fld-<?php echo $basepath->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-14'; ?> <?php echo $basepath->fieldclass;?>">
          <?php if($basepath->params->get('core.show_lable') == 1 || $basepath->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $basepath->id;?>" for="field_<?php echo $basepath->id;?>" class="control-label <?php echo $basepath->class;?>" >
              <?php if($basepath->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($basepath->params->get('core.icon'));  ?>
              <?php endif;?>
                
              
              <?php if ($basepath->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($basepath->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($basepath->translateDescription ? JText::_($basepath->description) : $basepath->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $basepath->label; ?>
              
            </label>
            <?php if(in_array($basepath->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($basepath->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($basepath->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $basepath->fieldclass  ?>">
            <div id="field-alert-<?php echo $basepath->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $basepath->result; ?>
          </div>
        </div>

        <!-- managed by gateway goes here -->
        <?php $managed_by_gateway = $this->sorted_fields[0][132]?>
        <div id="fld-<?php echo $managed_by_gateway->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $managed_by_gateway->fieldclass;?>">
          <?php if($managed_by_gateway->params->get('core.show_lable') == 1 || $managed_by_gateway->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $managed_by_gateway->id;?>" for="field_<?php echo $managed_by_gateway->id;?>" class="control-label <?php echo $managed_by_gateway->class;?>" >
              <?php if($managed_by_gateway->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($managed_by_gateway->params->get('core.icon'));  ?>
              <?php endif;?>
                
              
              <?php if ($managed_by_gateway->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($managed_by_gateway->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($managed_by_gateway->translateDescription ? JText::_($managed_by_gateway->description) : $managed_by_gateway->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $managed_by_gateway->label; ?>
              
            </label>
            <?php if(in_array($managed_by_gateway->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($managed_by_gateway->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($managed_by_gateway->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $managed_by_gateway->fieldclass  ?>">
            <div id="field-alert-<?php echo $managed_by_gateway->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $managed_by_gateway->result; ?>
          </div>
        </div>
        

        <!-- organization goes here -->
        <?php $organization = $this->sorted_fields[0][133]?>
        <?php  //TODO
        if(isset($organization)):?>
        <div id="fld-<?php echo $organization->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $organization->fieldclass;?>">
          <?php if($organization->params->get('core.show_lable') == 1 || $organization->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $organization->id;?>" for="field_<?php echo $organization->id;?>" class="control-label <?php echo $organization->class;?>" >
              <?php if($organization->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($organization->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($organization->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($organization->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($organization->translateDescription ? JText::_($organization->description) : $organization->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $organization->label; ?>

            </label>
            <?php if(in_array($organization->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($organization->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($organization->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $organization->fieldclass  ?>">
            <div id="field-alert-<?php echo $organization->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $organization->result; ?>
          </div>
        </div>
        <?php else: ?>
          <div id="fld-133" class="control-group odd1 field-133 is_managed" style="display:none;">
									<label id="lbl-133" for="field_133" class="control-label  ">
						Organization
					      </label>
			  	<div class="controls ">
					  <div class="alert"> No Organization currently available for use</div>
				  </div>
			    </div>
        <?php endif;?>
        <!-- User Name goes here -->
        <?php $user_name = $this->sorted_fields[0][134]?>
        <div id="fld-<?php echo $user_name->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $user_name->fieldclass;?>">
          <?php if($user_name->params->get('core.show_lable') == 1 || $user_name->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $user_name->id;?>" for="field_<?php echo $user_name->id;?>" class="control-label <?php echo $user_name->class;?>" >
              <?php if($user_name->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($user_name->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($user_name->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($user_name->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($user_name->translateDescription ? JText::_($user_name->description) : $user_name->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $user_name->label; ?>

            </label>
            <?php if(in_array($user_name->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($user_name->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($user_name->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $user_name->fieldclass  ?>">
            <div id="field-alert-<?php echo $user_name->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $user_name->result; ?>
          </div>
        </div>

        <!-- Password goes here -->
        <?php $password= $this->sorted_fields[0][135]?>
        <div id="fld-<?php echo $password->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $password->fieldclass;?>">
          <?php if($password->params->get('core.show_lable') == 1 || $password->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $password->id;?>" for="field_<?php echo $password->id;?>" class="control-label <?php echo $password->class;?>" >
              <?php if($password->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($password->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($password->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($password->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($password->translateDescription ? JText::_($password->description) : $password->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $password->label; ?>

            </label>
            <?php if(in_array($password->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($password->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($password->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $password->fieldclass  ?>">
            <div id="field-alert-<?php echo $password->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $password->result; ?>
          </div>
        </div>

        <!-- Timeout goes here -->
        <?php $timeout= $this->sorted_fields[0][136]?>
        <div id="fld-<?php echo $timeout->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $timeout->fieldclass;?>">
          <?php if($timeout->params->get('core.show_lable') == 1 || $timeout->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $timeout->id;?>" for="field_<?php echo $timeout->id;?>" class="control-label <?php echo $timeout->class;?>" >
              <?php if($timeout->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($timeout->params->get('core.icon'));  ?>
              <?php endif;?>


              <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>

              <?php if ($timeout->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($timeout->translateDescription ? JText::_($timeout->description) : $timeout->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $timeout->label; ?>

            </label>
            <?php if(in_array($timeout->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($timeout->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($timeout->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $timeout->fieldclass  ?>">
            <div id="field-alert-<?php echo $timeout->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $timeout->result; ?>
          </div>
        </div>

        <!-- Retry Count goes here -->
        <?php $retry_count= $this->sorted_fields[0][137]?>
        <div id="fld-<?php echo $retry_count->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $retry_count->fieldclass;?>">
          <?php if($retry_count->params->get('core.show_lable') == 1 || $retry_count->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $retry_count->id;?>" for="field_<?php echo $retry_count->id;?>" class="control-label <?php echo $retry_count->class;?>" >
              <?php if($retry_count->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($retry_count->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($retry_count->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($retry_count->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($retry_count->translateDescription ? JText::_($retry_count->description) : $retry_count->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $retry_count->label; ?>

            </label>
            <?php if(in_array($retry_count->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($retry_count->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($retry_count->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $retry_count->fieldclass  ?>">
            <div id="field-alert-<?php echo $retry_count->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $retry_count->result; ?>
          </div>
        </div>

        <!-- Retry Interval goes here -->
        <?php $retry_interval= $this->sorted_fields[0][138]?>
        <div id="fld-<?php echo $retry_interval->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $retry_interval->fieldclass;?>">
          <?php if($retry_interval->params->get('core.show_lable') == 1 || $retry_interval->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $retry_interval->id;?>" for="field_<?php echo $retry_interval->id;?>" class="control-label <?php echo $retry_interval->class;?>" >
              <?php if($retry_interval->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($retry_interval->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($retry_interval->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($retry_interval->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($retry_interval->translateDescription ? JText::_($retry_interval->description) : $retry_interval->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $retry_interval->label; ?>

            </label>
            <?php if(in_array($retry_interval->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($retry_interval->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($retry_interval->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $retry_interval->fieldclass  ?>">
            <div id="field-alert-<?php echo $retry_interval->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $retry_interval->result; ?>
          </div>
        </div>

        <!-- Retry Timeout (ms) goes here -->
        <?php $retry_timeout= $this->sorted_fields[0][139]?>
        <div id="fld-<?php echo $retry_timeout->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $retry_timeout->fieldclass;?>">
          <?php if($retry_timeout->params->get('core.show_lable') == 1 || $retry_timeout->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $retry_timeout->id;?>" for="field_<?php echo $retry_timeout->id;?>" class="control-label <?php echo $retry_timeout->class;?>" >
              <?php if($retry_timeout->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($retry_timeout->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($retry_timeout->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($retry_timeout->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($retry_timeout->translateDescription ? JText::_($retry_timeout->description) : $retry_timeout->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $retry_timeout->label; ?>

            </label>
            <?php if(in_array($retry_timeout->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($retry_timeout->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($retry_timeout->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $retry_timeout->fieldclass  ?>">
            <div id="field-alert-<?php echo $retry_timeout->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $retry_timeout->result; ?>
          </div>
        </div>

        <!-- Headers To Forward goes here -->
        <?php $headers_to_fowards = $this->sorted_fields[0][140]?>
        <div id="fld-<?php echo $headers_to_fowards->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $headers_to_fowards->fieldclass;?>">
          <?php if($headers_to_fowards->params->get('core.show_lable') == 1 || $headers_to_fowards->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $headers_to_fowards->id;?>" for="field_<?php echo $headers_to_fowards->id;?>" class="control-label <?php echo $headers_to_fowards->class;?>" >
              <?php if($headers_to_fowards->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($headers_to_fowards->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($headers_to_fowards->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($headers_to_fowards->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($headers_to_fowards->translateDescription ? JText::_($headers_to_fowards->description) : $headers_to_fowards->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $headers_to_fowards->label; ?>

            </label>
            <?php if(in_array($headers_to_fowards->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($headers_to_fowards->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($headers_to_fowards->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $headers_to_fowards->fieldclass  ?>">
            <div id="field-alert-<?php echo $headers_to_fowards->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $headers_to_fowards->result; ?>
          </div>
        </div>


        <!-- Operation Features goes here -->
        <?php $operation_features = $this->sorted_fields[0][141]?>
        <div id="fld-<?php echo $operation_features->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $operation_features->fieldclass;?>">
          <?php if($operation_features->params->get('core.show_lable') == 1 || $operation_features->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $operation_features->id;?>" for="field_<?php echo $operation_features->id;?>" class="control-label <?php echo $operation_features->class;?>" >
              <?php if($operation_features->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($operation_features->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($operation_features->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($operation_features->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($operation_features->translateDescription ? JText::_($operation_features->description) : $operation_features->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $operation_features->label; ?>

            </label>
            <?php if(in_array($operation_features->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($operation_features->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($operation_features->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $operation_features->fieldclass  ?>">
            <div id="field-alert-<?php echo $operation_features->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $operation_features->result; ?>
          </div>
        </div>

        <!-- SSL Enabled goes here -->
        <?php $ssl_enabled = $this->sorted_fields[0][142]?>
        <div id="fld-<?php echo $ssl_enabled->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $ssl_enabled->fieldclass;?>">
          <?php if($ssl_enabled->params->get('core.show_lable') == 1 || $ssl_enabled->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $ssl_enabled->id;?>" for="field_<?php echo $ssl_enabled->id;?>" class="control-label <?php echo $ssl_enabled->class;?>" >
              <?php if($ssl_enabled->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($ssl_enabled->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($ssl_enabled->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($ssl_enabled->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($ssl_enabled->translateDescription ? JText::_($ssl_enabled->description) : $ssl_enabled->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $ssl_enabled->label; ?>

            </label>
            <?php if(in_array($ssl_enabled->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($ssl_enabled->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($ssl_enabled->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $ssl_enabled->fieldclass  ?>">
            <div id="field-alert-<?php echo $ssl_enabled->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $ssl_enabled->result; ?>
          </div>
        </div>


        <!-- Property File goes here -->
        <?php $property_file = $this->sorted_fields[0][143]?>
        <div id="fld-<?php echo $property_file->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $property_file->fieldclass;?>">
          <?php if($property_file->params->get('core.show_lable') == 1 || $property_file->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $property_file->id;?>" for="field_<?php echo $property_file->id;?>" class="control-label <?php echo $property_file->class;?>" >
              <?php if($property_file->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($property_file->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($property_file->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($property_file->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($property_file->translateDescription ? JText::_($property_file->description) : $property_file->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $property_file->label; ?>

            </label>
            <?php if(in_array($property_file->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($property_file->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($property_file->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $property_file->fieldclass  ?>">
            <div id="field-alert-<?php echo $property_file->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $property_file->result; ?>
          </div>
        </div>

        <!-- is SSL Anonymous goes here -->
        <?php $is_ssl_anonymous = $this->sorted_fields[0][144]?>
        <div id="fld-<?php echo $is_ssl_anonymous->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo $is_ssl_anonymous->fieldclass;?>">
          <?php if($is_ssl_anonymous->params->get('core.show_lable') == 1 || $is_ssl_anonymous->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $is_ssl_anonymous->id;?>" for="field_<?php echo $is_ssl_anonymous->id;?>" class="control-label <?php echo $is_ssl_anonymous->class;?>" >
              <?php if($is_ssl_anonymous->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($is_ssl_anonymous->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($is_ssl_anonymous->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($is_ssl_anonymous->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($is_ssl_anonymous->translateDescription ? JText::_($is_ssl_anonymous->description) : $is_ssl_anonymous->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $is_ssl_anonymous->label; ?>

            </label>
            <?php if(in_array($is_ssl_anonymous->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($is_ssl_anonymous->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($is_ssl_anonymous->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $is_ssl_anonymous->fieldclass  ?>">
            <div id="field-alert-<?php echo $is_ssl_anonymous->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $is_ssl_anonymous->result; ?>
          </div>
        </div>

        <!-- gateways goes here -->
        <?php $gateways = $this->sorted_fields[0][15]?>
        <?php //TODO
        if(isset($gateway)):?>
        <div id="fld-<?php echo $gateways->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-15'; ?> <?php echo $gateways->fieldclass;?>">
          <?php if($gateways->params->get('core.show_lable') == 1 || $gateways->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $gateways->id;?>" for="field_<?php echo $gateways->id;?>" class="control-label <?php echo $gateways->class;?>" >
              <?php if($gateways->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($gateways->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($gateways->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($gateways->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($gateways->translateDescription ? JText::_($gateways->description) : $gateways->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $gateways->label; ?>

            </label>
            <?php if(in_array($gateways->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($gateways->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($gateways->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $gateways->fieldclass  ?>">
            <div id="field-alert-<?php echo $gateways->id?>" class="alert alert-error" style="display:none"></div>
            <?php if($this->item->id != 0):?>
              <?php $array = DeveloperPortalApi::getGatewaysByEnvironmentId($this->item->id);?>
              <?php if(count($array) > 0):?>
              <div id="parent_list<?php echo $gateways->id?>">
                <?php foreach($array as $key=>$value):?>
                <div class="alert alert-info list-item" rel="<?php echo $value->record_id;?>">
                  <span><?php echo $value->title;?></span>
                  <input type="hidden" value="<?php echo $value->record_id;?>" name="jform[fields][15][]">
                </div>
                <?php endforeach;?>
              </div>
              <?php endif;?>
            <?php else: ?>
              <?php echo $gateways->result; ?>
            <?php endif;?>
          </div>
        </div>
        <?php endif;?>
        <!-- apis goes here -->
        <?php $apis = $this->sorted_fields[0][25]?>
        <?php  //TODO
        if(isset($apis)):?>
        <div id="fld-<?php echo $apis->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-25'; ?> <?php echo $apis->fieldclass;?>">
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
        <!-- public in products goes here -->
        <?php $pubproducts = $this->sorted_fields[0][34]?>
        <?php  //TODO
        if(isset($pubproducts)):?>
        <div id="fld-<?php echo $pubproducts->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-34'; ?> <?php echo $pubproducts->fieldclass;?>">
          <?php if($pubproducts->params->get('core.show_lable') == 1 || $pubproducts->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $pubproducts->id;?>" for="field_<?php echo $pubproducts->id;?>" class="control-label <?php echo $pubproducts->class;?>" >
              <?php if($pubproducts->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($pubproducts->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($pubproducts->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($pubproducts->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($pubproducts->translateDescription ? JText::_($pubproducts->description) : $pubproducts->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $pubproducts->label; ?>

            </label>
            <?php if(in_array($pubproducts->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($pubproducts->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($pubproducts->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $pubproducts->fieldclass  ?>">
            <div id="field-alert-<?php echo $pubproducts->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $pubproducts->result; ?>
          </div>
        </div>
        <?php endif;?>
        <!-- private in products goes here -->
        <?php $priproducts = $this->sorted_fields[0][33]?>
        <?php  //TODO
        if(isset($priproducts)):?>
        <div id="fld-<?php echo $priproducts->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-33'; ?> <?php echo $priproducts->fieldclass;?>">
          <?php if($priproducts->params->get('core.show_lable') == 1 || $priproducts->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $priproducts->id;?>" for="field_<?php echo $priproducts->id;?>" class="control-label <?php echo $priproducts->class;?>" >
              <?php if($priproducts->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($priproducts->params->get('core.icon'));  ?>
              <?php endif;?>


              <?php if ($priproducts->required): ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

              <?php if ($priproducts->description):?>
                <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($priproducts->translateDescription ? JText::_($priproducts->description) : $priproducts->description), ENT_COMPAT, 'UTF-8');?>">
                  <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                </span>
              <?php endif;?>

              <?php echo $priproducts->label; ?>

            </label>
            <?php if(in_array($priproducts->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="controls<?php if(in_array($priproducts->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($priproducts->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $priproducts->fieldclass  ?>">
            <div id="field-alert-<?php echo $priproducts->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $priproducts->result; ?>
          </div>
        </div>
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
      </div>
      <div class="span4">
        <!-- Thumbnail goes here -->
      </div>
    </div>
    <?php unset($this->sorted_fields[0]);?>
  </div>
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
</script>
<script type="text/javascript">
  if (jQuery('#jform_id').val() != 0) {
    var environmentForm = {};

    (function($) {
        $(function() {
          var manage_related_fields = $(".control-group.is_managed");
          var field_timeout = $("#fld-136");

          <?php if(!$this->item->id):?>
            (function initForm(){
              $("#bool-y132").addClass("active btn-success").prev("input").attr("checked",true);
              enabled_manage();
            }());
          <?php else:?>
            if($("input:checked[name='jform[fields][132]']").val() === "1") {
              enabled_manage();
              $("#fld-143,#fld-144").hide();
            } else {
              manage_related_fields.show();
              if($("input:checked[name='jform[fields][142]']").val() === "1") {
                $("#fld-143,#fld-144").show();
              } else {
                $("#fld-143,#fld-144").hide();
              }
            }
          <?php endif;?>


          //Do something after enabled manage by gateway
          function enabled_manage(){
            manage_related_fields.hide();
            field_timeout.find("input").removeAttr("required");
            clearHiddenFields();
          }


          //Do something after disabled manage by gateway
          function disabled_manage(){
            manage_related_fields.show();
            field_timeout.find("input").attr("required",true);
            initShowUpFields();
          }

          // Hide the property file and is SSL Anonymous fields when the managed by gateway fields is changing
          function resetSSLFields(){
            $("#fld-142").find(".btn").removeClass("active btn-success btn-danger");
            $("#fld-142").find("input:checked").removeAttr("checked");
            $("#fld-143, #fld-144").hide();
            $('#field-143').val('');
            $('#fld-144').find('input:checked').removeAttr('checked');
          }
          
          function initSSLFields() {
              $("#bool-n142").addClass("active btn-danger").next("input").attr("checked", true);
              $('#fld-143, #fld-144').hide();
              $('#field-143').val('');
              $('#fld-144').find('input:checked').removeAttr('checked');
          }
          
          function initShowUpFields() {
              $("#fld-136").find("input").val(20000);
              $("#fld-137").find("input").val(0);
              $("#fld-138").find("input").val(0);
              $("#fld-139").find("input").val(0);
              initSSLFields();
          }
          
          function clearHiddenFields() {
              $('input[name="jform[fields][134]"], input[name="jform[fields][135]"],#field_136, #field_137, #field_138, #field_139, #field_140, #field_141').val('');
              resetSSLFields();
          }

          // This is for toggle the proxy fields
          $("#fld-132 button").on("click",function(){

            if(/^bool-y/.test(this.id)) {
              enabled_manage();
              $('#parent_list133 > div').remove();
            } else {
              disabled_manage();
              // manage_related_fields.show();
            }

          });

          $("#fld-142 button").on("click",function(){
              var ssl_related_fields = $(".control-group.is_enabled_ssl");
              if(/^bool-y/.test(this.id)) {
                  ssl_related_fields.show();
                  $('#bool-n144').addClass('active btn-danger').next('input').attr('checked', true);
              } else {
                  ssl_related_fields.hide();
                  $('#field-143').val('');
                  $('#fld-144').find('input:checked').removeAttr('checked');
              }
          });
            var oldBasepath = jQuery('#url-list14 .url-item.row-fluid input[name$="[url]"]').val();
            var filterXssString = filterXSS(oldBasepath);
            environmentForm.oldBasepath = filterXssString;
            environmentForm.oldGateways = [];
            jQuery('#adminForm input[name="jform[fields][15][]"]').each(function(index, item) {
                environmentForm.oldGateways.push(jQuery(item).val());
            });
            environmentForm.oldManagedByGateway = jQuery('input:checked[name="jform[fields][132]"]').val();
            environmentForm.oldHeadersToForward = jQuery('input[name="jform[fields][140]"]').val();
            environmentForm.oldTimeouts = jQuery('input[name="jform[fields][136]"]').val();

            Joomla.beforesubmitform = function(fCallback, fErrorback) {
                var urlXss = filterXSS(jQuery('#url-list14 .url-item.row-fluid input[name$="[url]"]').val());
                jQuery('#url-list14 .url-item.row-fluid input[name$="[url]"]').val(urlXss);
                window.oUpdatedFields = {};
                environmentForm.newBasepath = jQuery('#url-list14 .url-item.row-fluid input[name$="[url]"]').val();
                environmentForm.newGateways = [];
                environmentForm.newManagedByGateway = jQuery('input:checked[name="jform[fields][132]"]').val();
                environmentForm.newHeadersToForward = jQuery('input[name="jform[fields][140]"]').val();
                environmentForm.newTimeouts = jQuery('input[name="jform[fields][136]"]').val();
                jQuery('#adminForm input[name="jform[fields][15][]"]').each(function(index, item) {
                    environmentForm.newGateways.push(jQuery(item).val());
                });
                if(environmentForm.oldBasepath != environmentForm.newBasepath) {
                  window.oUpdatedFields[14] = [environmentForm.oldBasepath];
                }

                if(environmentForm.oldManagedByGateway != environmentForm.newManagedByGateway)
                {
                  window.oUpdatedFields[132] = [environmentForm.oldManagedByGateway];
                }

                if(environmentForm.newManagedByGateway === "-1" && environmentForm.oldHeadersToForward != environmentForm.newHeadersToForward) {
                  window.oUpdatedFields[140] = [environmentForm.oldHeadersToForward];
                }

                if(environmentForm.newManagedByGateway === "-1" && environmentForm.oldTimeouts !== environmentForm.newTimeouts) {
                  window.oUpdatedFields[136] = [environmentForm.oldTimeouts];
                }
                if(!DeveloperPortal.arrayEqual(environmentForm.oldGateways, environmentForm.newGateways)) {
                  window.oUpdatedFields[15] = environmentForm.oldGateways;
                }
                if(jQuery("input:checked[name='jform[fields][132]']").val() === "1") {
                    clearHiddenFields();
                }
                fCallback();
            };
        });
      }(jQuery));
    }



      jQuery('document').ready(function() {
        addSSLCheckbox();
        jQuery("#add-url14").bind("click",function(){
          addSSLCheckbox();
        });
      });

       
      function addSSLCheckbox() {
         jQuery('#url-list14 .url-item.row-fluid input[type="text"][name^="jform[fields][14]"]').each(function(index, item) {
          if (jQuery(item).parent().find("label input[type='checkbox']").length==0) {
            var checkbox = jQuery('<label style="vertical-align:text-bottom;margin-top:5px;"><input type="checkbox" id="ssl89" style="vertical-align:text-bottom;">&nbsp;SSL</label>');
            checkbox.find("input").bind("click",function(){
                var urlText = jQuery(item).val();
                urlText = urlText.length===0?'http://':urlText;
                if (jQuery(this).attr("checked")==="checked") {
                  urlText = urlText.replace("http","https");
                }else{
                  urlText = urlText.replace("https","http");
                }
                jQuery(item).val(urlText);
                
                return;
            });
            if (jQuery(this).val().indexOf("https:")>=0) {
              checkbox.find("input").attr("checked",true);
            }
            jQuery(item).after(checkbox);
            jQuery(item).bind("blur",function(){
              if (jQuery(this).val().indexOf("https:")<0) {
                checkbox.find("input").attr("checked",false);
              }else{
                checkbox.find("input").attr("checked",true);
              }
              
            });
        }
      });
    }



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


