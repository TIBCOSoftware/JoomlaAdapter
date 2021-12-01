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
require_once JPATH_BASE . "/components/com_cobalt/controllers/ajaxmore.php";
require_once JPATH_ROOT . '/components/com_cobalt/library/php/helpers/itemsstore.php';

$started = false;
$params = $this->tmpl_params;
if($params->get('tmpl_params.form_grouping_type', 0))
{
	$started = true;
}
$k = 0;
$user_profile_id = DeveloperPortalApi::getUserProfileId();

$apisData = DeveloperPortalApi::valueForKeyFromJson($this->item->fields, 7);

$archivedAPIs = array();
$userOrgIds = DeveloperPortalApi::getUserOrganization();

if(!empty($userOrgIds)){
  $userOrg = ItemsStore::getRecord($userOrgIds[0]);
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
#tabs-list {
  display: none;
}

#tabs-box {
  border-style: none;
}
.form-actions .btn:last-child {
  display: inline-block;
}
#parent_list7 {
  /*display: none;*/
}
.asg-create-product-step1 {
  height: auto;
  margin-bottom: 15px;
}
.asg-create-product-step2 {
  height: auto;
  margin-bottom: 15px;
}
.asg-create-product-step2 .table {
  display: none;
}
.asg-create-product-step2 .col1 {
  width: 50%;
}
.asg-create-product-step2 .col2 {
  width: 30%;
}
.asg-create-product-step2 .col3 {
  width: 20%;
}
.asg-create-product-step2 table.table tbody tr td {
  vertical-align: middle;
}
.asg-create-product-step3 {
  height: auto;
  margin-bottom: 15px;
}
.asg-pro-step-title-container {
  height:23px;
  position:relative;
}
.asg-pro-step-title-container-line {
  height:10px;
  background:#006699;
  position:relative;
  top:8px;
}
.asg-pro-step-title {
  position:absolute;
  background:#FFFFFF;
  padding:0px 5px;
  z-index:999;
}
.asg-pro-step-title i {
  font-size:14px;
  color:#006699;
  cursor: pointer;
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
  <div class="asg-pro-step-title-container"><div class="asg-pro-step-title"><i class="icon-chevron-down"></i><?php echo JText::_('CREATE_PRODUCT_STEP1_DES')?></div><div class="asg-pro-step-title-container-line"></div></div>
  <div class="asg-create-product-step1">
    <div class="row-fluid">
      <div class="span12">
    <?php if($params->get('tmpl_params.tab_main_descr')):?>
        <?php echo $params->get('tmpl_params.tab_main_descr'); ?>
	<?php endif;?>
  <!-- Title goes here -->
    <?php if($this->type->params->get('properties.item_title', 1) == 1):?>
      <div class="control-group odd<?php echo $k = 1 - $k ?>">
        <label id="title-lbl" for="jform_title">
          <?php if($params->get('tmpl_core.form_title_icon', 1)):?>
            <?php echo HTMLFormatHelper::icon($params->get('tmpl_core.item_icon_title_icon', 'edit.png'));  ?>
          <?php endif;?>

          Name of the Product
          <span rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>">
            <?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
        </label>
<!--        <p>One line description goes here to tell what is about this input box</p>-->
        <div>
          <div id="field-alert-title" class="alert alert-error" style="display:none"></div>
          <div class="row-fluid">
            <div class="span8">
            <?php echo $this->form->getInput('title'); ?>
            </div>
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
  <!-- Description goes here -->
    <?php $description = $this->sorted_fields[0][2]?>
    <div id="fld-<?php echo $description->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-2'; ?> <?php echo $description->fieldclass;?>">
      <?php if($description->params->get('core.show_lable') == 1 || $description->params->get('core.show_lable') == 3):?>
        <label id="lbl-<?php echo $description->id;?>" for="field_<?php echo $description->id;?>" class="<?php echo $description->class;?>" >
          <?php if($description->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
            <?php echo HTMLFormatHelper::icon($description->params->get('core.icon'));  ?>
          <?php endif;?>

          <?php echo $description->label; ?>
          <?php if ($description->required): ?>
            <span rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
          <?php endif;?>



        </label>
<!--        <p>One line description goes here to tell what is about this input box</p>-->
        <?php if(in_array($description->params->get('core.label_break'), array(1,3))):?>
          <div style="clear: both;"></div>
        <?php endif;?>
      <?php endif;?>

      <div class="<?php if(in_array($description->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($description->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $description->fieldclass  ?>">
        <div id="field-alert-<?php echo $description->id?>" class="alert alert-error" style="display:none"></div>
        <?php echo $description->result; ?>
      </div>
    </div>
  <!-- Category goes here -->
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
        <label id="ucategory-lbl" for="ucatid" >
          <?php if($params->get('tmpl_core.form_ucategory_icon', 1)):?>
            <?php echo HTMLFormatHelper::icon('category.png');  ?>
          <?php endif;?>

          Product Category

          <span rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
        </label>
<!--        <p>One line description goes here to tell what is about this input box</p>-->
        <div>
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
  <!-- Organization goes here -->
  <?php $organization = $this->sorted_fields[0][42]?>
  <div id="fld-<?php echo $organization->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-42'; ?> <?php echo $organization->fieldclass;?>">
    <?php if($organization->params->get('core.show_lable') == 1 || $organization->params->get('core.show_lable') == 3):?>
      <label id="lbl-<?php echo $organization->id;?>" for="field_<?php echo $organization->id;?>" class="<?php echo $organization->class;?>" >
        <?php if($organization->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
          <?php echo HTMLFormatHelper::icon($organization->params->get('core.icon'));  ?>
        <?php endif;?>


        <?php if ($organization->required): ?>
          <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
        <?php endif;?>

        <?php if ($organization->description):?>
          <span rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($organization->translateDescription ? JText::_($organization->description) : $organization->description), ENT_COMPAT, 'UTF-8');?>">
            <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
          </span>
        <?php endif;?>

        <?php echo $organization->label; ?>

      </label>
      <?php if(in_array($organization->params->get('core.label_break'), array(1,3))):?>
        <div style="clear: both;"></div>
      <?php endif;?>
    <?php endif;?>

    <div class="<?php if(in_array($organization->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($organization->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $organization->fieldclass  ?>">
      <div id="field-alert-<?php echo $organization->id?>" class="alert alert-error" style="display:none"></div>
      <?php //echo $organization->result; ?>
      <div id="parent_list<?php echo $organization->id?>">
        <div rel="<?php echo $userOrg->id;?>" class="alert alert-info list-item">
          <span><?php echo $userOrg->title;?></span><input type="hidden" value="<?php echo $userOrg->id;?>" name="jform[fields][<?php echo $organization->id?>]">
        </div>
      </div>
    </div>
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
      </div>
      <div class="span12">
        <!-- Thumbnail goes here -->
        <?php $thumbnail = $this->sorted_fields[0][3]?>
        <div id="fld-<?php echo $thumbnail->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-3'; ?> <?php echo $thumbnail->fieldclass;?>">
          <?php if($thumbnail->params->get('core.show_lable') == 1 || $thumbnail->params->get('core.show_lable') == 3):?>
            <label id="lbl-<?php echo $thumbnail->id;?>" for="field_<?php echo $thumbnail->id;?>" class="<?php echo $thumbnail->class;?>" >
              <?php if($thumbnail->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                <?php echo HTMLFormatHelper::icon($thumbnail->params->get('core.icon'));  ?>
              <?php endif;?>

              Upload product thumbnail
              <?php if ($thumbnail->required): ?>
                <span rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
              <?php endif;?>

            </label>
            <?php if(in_array($thumbnail->params->get('core.label_break'), array(1,3))):?>
              <div style="clear: both;"></div>
            <?php endif;?>
          <?php endif;?>

          <div class="<?php if(in_array($thumbnail->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($thumbnail->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $thumbnail->fieldclass  ?>">
            <div id="field-alert-<?php echo $thumbnail->id?>" class="alert alert-error" style="display:none"></div>
            <?php echo $thumbnail->result; ?>
          </div>
        </div>
      </div>

    </div>
  </div>
  <div class="asg-pro-step-title-container"><div class="asg-pro-step-title"><i class="icon-chevron-down"></i><?php echo JText::_('CREATE_PRODUCT_STEP2_DES')?></div><div class="asg-pro-step-title-container-line"></div></div>
  <div class="asg-create-product-step2">
    <div class="row-fluid">
      <div class="span12">
        <?php $apis = $this->sorted_fields[0][7]; ?>
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
			<?php
			foreach ($apis->value as $value) {
				$apiRecord = ItemsStore::getRecord($value);
				if ($apiRecord->published==2) {
					$archivedAPIs[] = $value;
				}
			}
			?>
          </div>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th class="col1">API Name & Descrpition</th>
              <th class="col2">Last updated</th>
              <th class="col3">Author</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="asg-pro-step-title-container"><div class="asg-pro-step-title"><i class="icon-chevron-down"></i><?php echo JText::_('CREATE_PRODUCT_STEP3_DES')?></div><div class="asg-pro-step-title-container-line"></div></div>
  <div class="asg-create-product-step3">
      <div class="row-fluid">
        <div class="span12">
    <?php $documentation = $this->sorted_fields[0][117]?>
    <div id="fld-<?php echo $documentation->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-117' ?> <?php echo $documentation->fieldclass;?>">
      <?php if($documentation->params->get('core.show_lable') == 1 || $documentation->params->get('core.show_lable') == 3):?>
        <label id="lbl-<?php echo $documentation->id;?>" for="field_<?php echo $documentation->id;?>" class="control-label <?php echo $documentation->class;?>" >
          <?php if($documentation->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
            <?php echo HTMLFormatHelper::icon($documentation->params->get('core.icon'));  ?>
          <?php endif;?>
          <?php if ($documentation->required): ?>
            <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
          <?php endif;?>

          <?php if ($documentation->description):?>
            <span class="pull-right" rel="tooltip" style="cursor: help;" data-original-title="<?php echo htmlspecialchars(($documentation->translateDescription ? JText::_($documentation->description) : $documentation->description), ENT_COMPAT, 'UTF-8');?>">
              <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
            </span>
          <?php endif;?>
          <?php echo $documentation->label; ?>
        </label>
        <?php if(in_array($documentation->params->get('core.label_break'), array(1,3))):?>
          <div style="clear: both;"></div>
        <?php endif;?>
      <?php endif;?>

      <div class="controls<?php if(in_array($documentation->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($documentation->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $documentation->fieldclass  ?>">
        <div id="apis-alert-<?php echo $documentation->id?>" class="alert alert-error" style="display:none"></div>
        <?php echo $documentation->result; ?>
      </div>
    </div>
    <?php $docfile = $this->sorted_fields[0][118]?>
    <div id="fld-<?php echo $docfile->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-118' ?> <?php echo $docfile->fieldclass;?>">
      <?php if($docfile->params->get('core.show_lable') == 1 || $docfile->params->get('core.show_lable') == 3):?>
        <label id="lbl-<?php echo $docfile->id;?>" for="field_<?php echo $docfile->id;?>" class="control-label <?php echo $docfile->class;?>" >
          <?php if($docfile->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
            <?php echo HTMLFormatHelper::icon($docfile->params->get('core.icon'));  ?>
          <?php endif;?>
          <?php if ($docfile->required): ?>
            <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
          <?php endif;?>

          <?php if ($docfile->description):?>
            <span class="pull-right" rel="tooltip" style="cursor: help;" data-original-title="<?php echo htmlspecialchars(($docfile->translateDescription ? JText::_($docfile->description) : $docfile->description), ENT_COMPAT, 'UTF-8');?>">
              <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
            </span>
          <?php endif;?>
          <?php echo $docfile->label; ?>
        </label>
        <?php if(in_array($docfile->params->get('core.label_break'), array(1,3))):?>
          <div style="clear: both;"></div>
        <?php endif;?>
      <?php endif;?>

      <div class="controls<?php if(in_array($docfile->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($docfile->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $docfile->fieldclass  ?>">
        <div id="field-alert-<?php echo $docfile->id?>" class="alert alert-error" style="display:none"></div>
        <?php echo $docfile->result; ?>
      </div>
    </div>
      <?php $terms = $this->sorted_fields[0][129]?>
      <div id="fld-<?php echo $terms->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-129' ?> <?php echo $terms->fieldclass;?>">
        <?php if($terms->params->get('core.show_lable') == 1 || $terms->params->get('core.show_lable') == 3):?>
          <label id="lbl-<?php echo $terms->id;?>" for="field_<?php echo $terms->id;?>" class="control-label <?php echo $terms->class;?>" >
            <?php if($terms->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
              <?php echo HTMLFormatHelper::icon($terms->params->get('core.icon'));  ?>
            <?php endif;?>
            <?php if ($terms->required): ?>
              <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
            <?php endif;?>

            <?php if ($terms->description):?>
              <span class="pull-right" rel="tooltip" style="cursor: help;" data-original-title="<?php echo htmlspecialchars(($terms->translateDescription ? JText::_($terms->description) : $terms->description), ENT_COMPAT, 'UTF-8');?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
              </span>
            <?php endif;?>
            <?php echo $terms->label; ?>
          </label>
          <?php if(in_array($terms->params->get('core.label_break'), array(1,3))):?>
            <div style="clear: both;"></div>
          <?php endif;?>
        <?php endif;?>

        <div class="controls<?php if(in_array($terms->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($terms->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $terms->fieldclass  ?>">
          <div id="apis-alert-<?php echo $terms->id?>" class="alert alert-error" style="display:none"></div>
          <?php echo $terms->result; ?>
        </div>
      </div>

      <?php $uuid = $this->sorted_fields[0][111]; ?>
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
	<?php group_end($this);?>
  <?php unset($this->sorted_fields[0]);?>
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

jQuery(function(){
  var contact = jQuery("input[name='jform[fields][32]']");
  var organization = jQuery("input[name='jform[fields][42]']");
  var contact_wrap = jQuery("#parent_list32").children("div.alert");
  if(!contact_wrap.length)
  {
    jQuery("<div class='alert'><div>").appendTo("#parent_list32");
    if(contact.length)
    {
      contact.val("<?php echo $user_profile_id ? $user_profile_id : '';?>");
    }else{
      jQuery("#adminForm").append('<input type="hidden" name="jform[fields][32]" value="<?php echo $user_profile_id ? $user_profile_id : '';?>">');
    }
  }

  // if(organization.length)
  // {
  //   organization.val("<?php echo $owner_organization[0]; ?>");
  // }else{
  //   jQuery("#adminForm").append('<input type="hidden" name="jform[fields][42]" value="<?php echo $owner_organization[0]; ?>">');
  // }
});

</script>
<script>
    var ProductForm = {
        aOldAPIs: []
    };
    function renderAPITable() {
      var ids = [];
	  var archivedIDs = '<?php echo implode(',',$archivedAPIs); ?>'.split(',');
      jQuery('#parent_list7').children().each(function(index, node) {
		aid = jQuery(node).attr('rel');
		flag = true;
		for (var i = 0; i < archivedIDs.length; i++) {
			if (archivedIDs[i] == aid) {
				flag = false;
			}
		}
		if (flag) {
			ids.push(aid);
		}
      });
      if (ids.length) {
        jQuery.ajax({
          url: GLOBAL_CONTEXT_PATH + 'index.php?option=com_cobalt&task=ajaxMore.getApiRecordsById',
          data: {ids: ids.join(',')}
        }).done(function(data) {
          var html = [], i,
          result = JSON.parse(data).result;
          for (i=0;i<result.length;i++) {
            html.push('<tr api="'+ids[i]+'"><td>' + result[i].title + '</br>' + (result[i].description || '') + '</td><td>' + result[i].mtime + '</td><td>' + result[i].author + '</td></tr>');
          }
          jQuery('.asg-create-product-step2 table.table tbody').html(html.join());
          if (jQuery('.asg-create-product-step2 table.table').is(':hidden')) {
            jQuery('.asg-create-product-step2 table.table').show();
          }
        });
      } else {
        if (jQuery('.asg-create-product-step2 table.table').is(':visible')) {
          jQuery('.asg-create-product-step2 table.table tbody').html('');
          jQuery('.asg-create-product-step2 table.table').hide();
        }
      }
    }
    // renderAPITable(); //hide this table for ticket-4445
    // jQuery('#modal7').on('hide', function() {
    //   renderAPITable();
    // });
    jQuery('.asg-pro-step-title-container .asg-pro-step-title').on('click', '.icon-chevron-down', function() {
      jQuery(this).removeClass('icon-chevron-down').addClass('icon-chevron-right');
      jQuery(this).parent().parent().next().hide();
    });
    jQuery('.asg-pro-step-title-container .asg-pro-step-title').on('click', '.icon-chevron-right', function() {
      jQuery(this).removeClass('icon-chevron-right').addClass('icon-chevron-down');
      jQuery(this).parent().parent().next().show();
    });
    // jQuery('.asg-create-app-next').on('click', function() {
      // var tab = jQuery(this).parent().parent().parent();
      // tab.hide();
      // tab.next().show();
      // jQuery('.asg-create-app-guide .active').removeClass('active').next().addClass('active');
      // if (jQuery('.asg-create-app-guide .active').is(':last-child')) {
        // jQuery('.form-actions .btn').show();
      // }
    // });
    // jQuery('.asg-create-app-back').on('click', function() {
      // var tab = jQuery(this).parent().parent().parent();
      // tab.hide();
      // tab.prev().show();
      // jQuery('.asg-create-app-guide .active').removeClass('active').prev().addClass('active');
      // jQuery('.form-actions .btn').hide();
      // jQuery('.form-actions .btn:last-child').show();
    // });

    jQuery(function() {
        jQuery('input[name="jform[fields][7][]"]').each(function() {
            ProductForm.aOldAPIs.push(jQuery(this).val());
        });
    });

    Joomla.beforesubmitform = function(fCallback, fErrorback) {
        var aNewAPIs = [];
        jQuery('input[name="jform[fields][7][]"]').each(function() {
            aNewAPIs.push(jQuery(this).val());
        });
        
        if(!DeveloperPortal.arrayEqual(ProductForm.aOldAPIs, aNewAPIs)) {
            window.oUpdatedFields = {
                7: ProductForm.aOldAPIs
            }
        }
        fCallback();
    };
	
	
	jQuery(function() {
		<?php foreach ($archivedAPIs as $apiID): ?>
		jQuery('input[value="<?php echo $apiID; ?>"]').remove();
		jQuery('div[rel="<?php echo $apiID; ?>"]').remove();
		<?php endforeach;?>
	});
	
  // Commented out for published state
  // jQuery('document').ready(function() {
    // var published = <?php echo $this->item->published;?>;
    // if((published === 1) && (jQuery('#jform_id').val() != '0')) {
      // jQuery('#fld-7 .controls .btn').attr({
        // 'href': '',
        // 'disabled': 'true'
      // });
    // }
  // });
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