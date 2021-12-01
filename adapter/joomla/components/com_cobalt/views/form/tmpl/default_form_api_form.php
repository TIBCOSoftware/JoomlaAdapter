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
require_once JPATH_ROOT . '/includes/api.php';

$started = false;
$params = $this->tmpl_params;
if ($params->get('tmpl_params.form_grouping_type', 0)) {
    $started = true;
}
$k = 0;
$app = JFactory::getApplication();

$folder_format = $this->appParams->get('folder_format', 1);
$folder_format = date($folder_format, time());

$api_id = $app->input->getInn("id", 0);
$old_operations_of_api = DeveloperPortalApi::getOperationsOfApiByApiId($api_id);


$doc = JFactory::getDocument();
$old_operations_of_api_js = "var api_id=" . $api_id . ";";
$old_operations_of_api_js .= 'var old_operation_of_api = ' . json_encode($old_operations_of_api) . ';';
$doc->addScriptDeclaration($old_operations_of_api_js);



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

        .asg-api-step-title-container {
            height: 23px;
            position: relative;
        }

        .asg-api-step-title-container-line {
            height: 10px;
            background: #006699;
            position: relative;
            top: 8px;
        }

        .asg-api-step-title {
            position: absolute;
            background: #FFFFFF;
            padding: 0px 5px;
            z-index: 999;
        }

        .asg-api-step-title i {
            font-size: 14px;
            color: #006699;
            cursor: pointer;
        }

        .asg-create-api-step1 {
            height: auto;
            margin-bottom: 15px;
        }

        .asg-create-api-step2 {
            height: auto;
            margin-bottom: 15px;
        }

        .asg-create-api-step3 {
            height: auto;
            margin-bottom: 15px;
        }

        .form-actions .btn:last-child {
            display: inline-block;
        }

        #fld-145 .btn:active,
        #fld-145 .btn.active {
            background-color: #2E9ACF;
        }

        .hidden-div {
            display: none !important;
        }
    </style>
    <div class="form-horizontal">
<?php if (in_array($params->get('tmpl_params.form_grouping_type', 0), array(1, 4))): ?>
    <div class="tabbable<?php if ($params->get('tmpl_params.form_grouping_type', 0) == 4) echo ' tabs-left' ?>">
    <ul class="nav nav-tabs" id="tabs-list">
        <li><a href="#tab-main"
               data-toggle="tab"><?php echo JText::_($params->get('tmpl_params.tab_main', 'Main')); ?></a></li>

        <?php if (isset($this->sorted_fields)): ?>
            <?php foreach ($this->sorted_fields as $group_id => $fields) : ?>
                <?php if ($group_id == 0) continue; ?>
                <li><a class="taberlink" href="#tab-<?php echo $group_id ?>"
                       data-toggle="tab"><?php echo HTMLFormatHelper::icon($this->field_groups[$group_id]['icon']) ?> <?php echo $this->field_groups[$group_id]['name'] ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (count($this->meta)): ?>
            <li><a href="#tab-meta" data-toggle="tab"><?php echo JText::_('Meta Data'); ?></a></li>
        <?php endif; ?>
        <?php if (count($this->core_admin_fields)): ?>
            <li><a href="#tab-special" data-toggle="tab"><?php echo JText::_('Special Fields'); ?></a></li>
        <?php endif; ?>
        <?php if (count($this->core_fields)): ?>
            <li><a href="#tab-core" data-toggle="tab"><?php echo JText::_('Core Fields'); ?></a></li>
        <?php endif; ?>
    </ul>
<?php endif; ?>
<?php group_start($this, $params->get('tmpl_params.tab_main', 'Main'), 'tab-main'); ?>
    <div class="asg-api-step-title-container">
        <div class="asg-api-step-title"><i class="icon-chevron-down"></i><?php echo JText::_('CREATE_API_STEP1_DES') ?>
        </div>
        <div class="asg-api-step-title-container-line"></div>
    </div>
    <div class="asg-create-api-step1">
        <div class="row-fluid">
            <div class="span12">
                <?php if ($params->get('tmpl_params.tab_main_descr')): ?>
                    <?php echo $params->get('tmpl_params.tab_main_descr'); ?>
                <?php endif; ?>
                <?php if ($this->type->params->get('properties.item_title', 1) == 1): ?>
                    <div class="control-group odd<?php echo $k = 1 - $k ?>">
                        <label id="title-lbl" for="jform_title" class="control-label">
                            <?php if ($params->get('tmpl_core.form_title_icon', 1)): ?>
                                <?php echo HTMLFormatHelper::icon($params->get('tmpl_core.item_icon_title_icon', 'edit.png')); ?>
                            <?php endif; ?>

                            <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_title', 'Title')) ?>
                            <span class="pull-right" rel="tooltip"
                                  data-original-title="<?php echo JText::_('CREQUIRED') ?>">
          <?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                        </label>

                        <div class="controls">
                            <div id="field-alert-title" class="alert alert-error" style="display:none"></div>
                            <div class="row-fluid">
                                <?php echo $this->form->getInput('title'); ?>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <input type="hidden" name="jform[title]"
                           value="<?php echo htmlentities(!empty($this->item->title) ? $this->item->title : JText::_('CNOTITLE') . ': ' . time(), ENT_COMPAT, 'UTF-8') ?>"/>
                <?php endif; ?>
                <?php if ($this->anywhere) : ?>
                    <div class="control-group odd<?php echo $k = 1 - $k ?>">
                        <label id="anywhere-lbl" class="control-label">
                            <?php if ($params->get('tmpl_core.form_anywhere_icon', 1)): ?>
                                <?php echo HTMLFormatHelper::icon('document-share.png'); ?>
                            <?php endif; ?>

                            <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_anywhere', 'Where to post')) ?>
                            <span class="pull-right" rel="tooltip"
                                  data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                        </label>

                        <div class="controls">
                            <div id="field-alert-anywhere" class="alert alert-error" style="display:none"></div>
                            <?php echo JHTML::_('users.wheretopost', @$this->item); ?>
                        </div>
                    </div>


                    <div class="control-group odd<?php echo $k = 1 - $k ?>">
                        <label id="anywherewho-lbl" for="whorepost" class="control-label">
                            <?php if ($params->get('tmpl_core.form_anywhere_who_icon', 1)): ?>
                                <?php echo HTMLFormatHelper::icon('arrow-retweet.png'); ?>
                            <?php endif; ?>

                            <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_anywhere_who', 'Who can repost')) ?>
                        </label>

                        <div class="controls">
                            <div id="field-alert-anywhere" class="alert alert-error" style="display:none"></div>
                            <?php echo $this->form->getInput('whorepost'); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (in_array($this->params->get('submission.allow_category'), $this->user->getAuthorisedViewLevels()) && $this->section->categories): ?>
                    <div class="control-group odd<?php echo $k = 1 - $k ?>">
                        <?php if ($this->catsel_params->get('tmpl_core.category_label', 0)): ?>
                            <label id="category-lbl" for="category" class="control-label">
                                <?php if ($params->get('tmpl_core.form_category_icon', 1)): ?>
                                    <?php echo HTMLFormatHelper::icon('category.png'); ?>
                                <?php endif; ?>

                                <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_category', 'Category')) ?>

                                <?php if (!$this->type->params->get('submission.first_category', 0) && in_array($this->type->params->get('submission.allow_category', 1), $this->user->getAuthorisedViewLevels())) : ?>
                                    <span class="pull-right" rel="tooltip"
                                          data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                                <?php endif; ?>
                            </label>
                        <?php endif; ?>
                        <div class="controls">
                            <div id="field-alert-category" class="alert alert-error" style="display:none"></div>
                            <?php echo $this->loadTemplate('category_' . $params->get('tmpl_params.tmpl_category', 'default')); ?>
                        </div>
                    </div>
                <?php elseif (!empty($this->category->id)): ?>
                    <div class="control-group odd<?php echo $k = 1 - $k ?>">
                        <label id="category-lbl" for="category" class="control-label">
                            <?php if ($params->get('tmpl_core.form_category_icon', 1)): ?>
                                <?php echo HTMLFormatHelper::icon('category.png'); ?>
                            <?php endif; ?>

                            <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_category', 'Category')) ?>

                            <?php if (!$this->type->params->get('submission.first_category', 0) && in_array($this->type->params->get('submission.allow_category', 1), $this->user->getAuthorisedViewLevels())) : ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>
                        </label>

                        <div class="controls">
                            <div id="field-alert-category" class="alert alert-error" style="display:none"></div>
                            <?php echo $this->section->name; ?>/<?php echo $this->category->path; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($this->ucategory) : ?>
                    <div class="control-group odd<?php echo $k = 1 - $k ?>">
                        <label id="ucategory-lbl" for="ucatid" class="control-label">
                            <?php if ($params->get('tmpl_core.form_ucategory_icon', 1)): ?>
                                <?php echo HTMLFormatHelper::icon('category.png'); ?>
                            <?php endif; ?>

                            <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_ucategory', 'Category')) ?>

                            <span class="pull-right" rel="tooltip"
                                  data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                        </label>

                        <div class="controls">
                            <div id="field-alert-ucat" class="alert alert-error" style="display:none"></div>
                            <?php echo $this->form->getInput('ucatid'); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <?php $this->form->setFieldAttribute('ucatid', 'type', 'hidden'); ?>
                    <?php $this->form->setValue('ucatid', null, '0'); ?>
                    <?php echo $this->form->getInput('ucatid'); ?>
                <?php endif; ?>

                <?php if ($this->multirating): ?>
                    <div class="control-group odd<?php echo $k = 1 - $k ?>">
                        <label id="jform_multirating-lbl" class="control-label"
                               for="jform_multirating"><?php echo strip_tags($this->form->getLabel('multirating')); ?></label>

                        <div class="controls">
                            <?php echo $this->multirating; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- description goes here -->
                <?php $description = $this->sorted_fields[0][5] ?>
                <div id="fld-<?php echo $description->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-5' ?> <?php echo $description->fieldclass; ?>">
                    <?php if ($description->params->get('core.show_lable') == 1 || $description->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $description->id; ?>" for="field_<?php echo $description->id; ?>"
                               class="control-label <?php echo $description->class; ?>">
                            <?php if ($description->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($description->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($description->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($description->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($description->translateDescription ? JText::_($description->description) : $description->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $description->label; ?>

                        </label>
                        <?php if (in_array($description->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($description->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($description->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $description->fieldclass ?>">
                        <div id="field-alert-<?php echo $description->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $description->result; ?>
                    </div>
                </div>
                <!-- API Type goes here -->
                <?php $api_type = $this->sorted_fields[0][75] ?>
                <div id="fld-<?php echo $api_type->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-75' ?> <?php echo $api_type->fieldclass; ?>">
                    <?php if ($api_type->params->get('core.show_lable') == 1 || $api_type->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $api_type->id; ?>" for="field_<?php echo $api_type->id; ?>"
                               class="control-label <?php echo $api_type->class; ?>">
                            <?php if ($api_type->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($api_type->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($api_type->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($api_type->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($api_type->translateDescription ? JText::_($api_type->description) : $api_type->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $api_type->label; ?>

                        </label>
                        <?php if (in_array($api_type->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($api_type->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($api_type->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $api_type->fieldclass ?>">
                        <div id="field-alert-<?php echo $api_type->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $api_type->result; ?>
                    </div>
                </div>



                <!-- API Type goes here -->
                <?php $use_existing_facade = $this->sorted_fields[0][145] ?>
                <div id="fld-<?php echo $use_existing_facade->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-145' ?> <?php echo $use_existing_facade->fieldclass; ?>">
                    <?php if ($use_existing_facade->params->get('core.show_lable') == 1 || $use_existing_facade->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $use_existing_facade->id; ?>"
                               for="field_<?php echo $use_existing_facade->id; ?>"
                               class="control-label <?php echo $use_existing_facade->class; ?>">
                            <?php if ($use_existing_facade->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($use_existing_facade->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($use_existing_facade->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($use_existing_facade->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($use_existing_facade->translateDescription ? JText::_($use_existing_facade->description) : $use_existing_facade->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $use_existing_facade->label; ?>

                        </label>
                        <?php if (in_array($use_existing_facade->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($use_existing_facade->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($use_existing_facade->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $use_existing_facade->fieldclass ?>">
                        <div id="field-alert-<?php echo $use_existing_facade->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $use_existing_facade->result; ?>
                    </div>
                </div>

                <!-- Contact email goes here -->
                <?php $contact_email = $this->sorted_fields[0][21] ?>
                <div id="fld-<?php echo $contact_email->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-21' ?> <?php echo $contact_email->fieldclass; ?>">
                    <?php if ($contact_email->params->get('core.show_lable') == 1 || $contact_email->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $contact_email->id; ?>" for="field_<?php echo $contact_email->id; ?>"
                               class="control-label <?php echo $contact_email->class; ?>">
                            <?php if ($contact_email->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($contact_email->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($contact_email->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($contact_email->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($contact_email->translateDescription ? JText::_($contact_email->description) : $contact_email->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $contact_email->label; ?>

                        </label>
                        <?php if (in_array($contact_email->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($contact_email->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($contact_email->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $contact_email->fieldclass ?>">
                        <div id="field-alert-<?php echo $contact_email->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $contact_email->result; ?>
                    </div>
                </div>
                <!-- Owner's Organization goes here -->
                <?php $organization = $this->sorted_fields[0][40] ?>
                <div id="fld-<?php echo $organization->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-40' ?> <?php echo $organization->fieldclass; ?>">
                    <?php if ($organization->params->get('core.show_lable') == 1 || $organization->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $organization->id; ?>" for="field_<?php echo $organization->id; ?>"
                               class="control-label <?php echo $organization->class; ?>">
                            <?php if ($organization->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($organization->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($organization->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($organization->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($organization->translateDescription ? JText::_($organization->description) : $organization->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $organization->label; ?>

                        </label>
                        <?php if (in_array($organization->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($organization->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($organization->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $organization->fieldclass ?>">
                        <div id="field-alert-<?php echo $organization->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $organization->result; ?>
                    </div>
                </div>
                <!-- Contained in Products goes here -->
                <?php $contained = $this->sorted_fields[0][6] ?>
                <?php //TODO
                      if(isset($contained)):?>
                <div id="fld-<?php echo $contained->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-6' ?> <?php echo $contained->fieldclass; ?>">
                    <?php if ($contained->params->get('core.show_lable') == 1 || $contained->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $contained->id; ?>" for="field_<?php echo $contained->id; ?>"
                               class="control-label <?php echo $contained->class; ?>">
                            <?php if ($contained->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($contained->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($contained->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($contained->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($contained->translateDescription ? JText::_($contained->description) : $contained->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $contained->label; ?>

                        </label>
                        <?php if (in_array($contained->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($contained->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($contained->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $contained->fieldclass ?>">
                        <div id="field-alert-<?php echo $contained->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $contained->result; ?>
                    </div>
                </div>
                <?php endif;?>
                <!-- Operations goes here -->
                <?php $operations = $this->sorted_fields[0][31] ?>
                <?php //TODO
                      if(isset($operations)):?>
                <div id="fld-<?php echo $operations->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-31' ?> <?php echo $operations->fieldclass; ?>">
                    <?php if ($operations->params->get('core.show_lable') == 1 || $operations->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $operations->id; ?>" for="field_<?php echo $operations->id; ?>"
                               class="control-label <?php echo $operations->class; ?>">
                            <?php if ($operations->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($operations->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($operations->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($operations->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($operations->translateDescription ? JText::_($operations->description) : $operations->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $operations->label; ?>

                        </label>
                        <?php if (in_array($operations->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($operations->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($operations->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $operations->fieldclass ?>">
                        <div id="field-alert-<?php echo $operations->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $operations->result; ?>
                    </div>
                </div>
                <?php endif;?>
                <?php if (MECAccess::allowAccessAuthor($this->type, 'properties.item_can_add_tag', $this->item->user_id) &&
                    $this->type->params->get('properties.item_can_view_tag')
                ): ?>
                    <div class="control-group odd<?php echo $k = 1 - $k ?>">
                        <label id="tags-lbl" for="tags" class="control-label">
                            <?php if ($params->get('tmpl_core.form_tags_icon', 1)): ?>
                                <?php echo HTMLFormatHelper::icon('price-tag.png'); ?>
                            <?php endif; ?>
                            <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_tags', 'Tags')) ?>
                        </label>

                        <div class="controls">
                            <?php //echo JHtml::_('tags.tagform', $this->section, json_decode($this->item->tags, TRUE), array(), 'jform[tags]'); ?>
                            <?php echo $this->form->getInput('tags'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="asg-api-step-title-container">
        <div class="asg-api-step-title"><i class="icon-chevron-down"></i><?php echo JText::_('CREATE_API_STEP2_DES') ?>
        </div>
        <div class="asg-api-step-title-container-line"></div>
    </div>
    <div class="asg-create-api-step2">
        <div class="row-fluid">
            <div class="span12">
                <!-- Upload REST API Spec goes here -->
                <?php $upload_api_spec = $this->sorted_fields[0][23] ?>
                <div id="fld-<?php echo $upload_api_spec->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-23' ?> <?php echo $upload_api_spec->fieldclass; ?>">
                    <?php if ($upload_api_spec->params->get('core.show_lable') == 1 || $upload_api_spec->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $upload_api_spec->id; ?>"
                               for="field_<?php echo $upload_api_spec->id; ?>"
                               class="control-label <?php echo $upload_api_spec->class; ?>">
                            <?php if ($upload_api_spec->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($upload_api_spec->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($upload_api_spec->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($upload_api_spec->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($upload_api_spec->translateDescription ? JText::_($upload_api_spec->description) : $upload_api_spec->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $upload_api_spec->label; ?>

                        </label>
                        <?php if (in_array($upload_api_spec->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($upload_api_spec->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($upload_api_spec->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $upload_api_spec->fieldclass ?>">
                        <div id="field-alert-<?php echo $upload_api_spec->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $upload_api_spec->result; ?>
                    </div>
                </div>
                <!-- Upload REST API Spec goes here -->
                <?php $upload_wsdl_spec = $this->sorted_fields[0][127] ?>
                <div id="fld-<?php echo $upload_wsdl_spec->id; ?>"
                     class="hidden-div control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-127' ?> <?php echo $upload_wsdl_spec->fieldclass; ?>">
                    <?php if ($upload_wsdl_spec->params->get('core.show_lable') == 1 || $upload_wsdl_spec->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $upload_wsdl_spec->id; ?>"
                               for="field_<?php echo $upload_wsdl_spec->id; ?>"
                               class="control-label <?php echo $upload_wsdl_spec->class; ?>">
                            <?php if ($upload_wsdl_spec->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($upload_wsdl_spec->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($upload_wsdl_spec->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($upload_wsdl_spec->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($upload_wsdl_spec->translateDescription ? JText::_($upload_wsdl_spec->description) : $upload_wsdl_spec->description), ENT_COMPAT, 'UTF-8'); ?>">
                    <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
                  </span>
                            <?php endif; ?>

                            <?php echo $upload_wsdl_spec->label; ?>

                        </label>
                        <?php if (in_array($upload_wsdl_spec->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($upload_wsdl_spec->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($upload_wsdl_spec->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $upload_wsdl_spec->fieldclass ?>">
                        <div id="field-alert-<?php echo $upload_wsdl_spec->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $upload_wsdl_spec->result; ?>
                    </div>
                </div>


                <!-- Environments Path goes here -->
                <?php $environments = $this->sorted_fields[0][26] ?>
                <?php //TODO
                      if(isset($environments)):?>
                <div id="fld-<?php echo $environments->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-26' ?> <?php echo $environments->fieldclass; ?>">
                    <?php if ($environments->params->get('core.show_lable') == 1 || $environments->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $environments->id; ?>" for="field_<?php echo $environments->id; ?>"
                               class="control-label <?php echo $environments->class; ?>">
                            <?php if ($environments->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($environments->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($environments->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($environments->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($environments->translateDescription ? JText::_($environments->description) : $environments->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $environments->label; ?>

                        </label>
                        <?php if (in_array($environments->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($environments->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($environments->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $environments->fieldclass ?>">
                        <div id="field-alert-<?php echo $environments->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $environments->result; ?>
                    </div>
                </div>
                <?php endif;?>
                <!-- Target Environments Path goes here -->
                <input id="sorted_value" type="hidden" value="<?php echo $this->sorted_fields[0][145]->value; ?>">
                <?php $target_environments = $this->sorted_fields[0][147] ?>
                <?php //TODO
                      if(isset($target_environments)):?>
                <div id="fld-<?php echo $target_environments->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-26' ?> <?php echo $target_environments->fieldclass; ?>">
                    <?php if ($target_environments->params->get('core.show_lable') == 1 || $target_environments->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $target_environments->id; ?>"
                               for="field_<?php echo $target_environments->id; ?>"
                               class="control-label <?php echo $target_environments->class; ?>">
                            <?php if ($target_environments->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($target_environments->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($target_environments->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($target_environments->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($target_environments->translateDescription ? JText::_($target_environments->description) : $target_environments->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $target_environments->label; ?>

                        </label>
                        <?php if (in_array($target_environments->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($target_environments->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($target_environments->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $target_environments->fieldclass ?>">
                        <div id="field-alert-<?php echo $target_environments->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $target_environments->result; ?>
                    </div>
                </div>
                <?php endif;?>

            </div>
        </div>
    </div>



    <div class="asg-api-step-title-container">
        <div class="asg-api-step-title"><i class="icon-chevron-down"></i><?php echo JText::_('CREATE_API_STEP3_DES') ?>
        </div>
        <div class="asg-api-step-title-container-line"></div>
    </div>
    <div class="asg-create-api-step3">
        <div class="row-fluid">
            <div class="span12">
                <!-- Attached documentation file goes here -->
                <?php $doc_file = $this->sorted_fields[0][24] ?>
                <div id="fld-<?php echo $doc_file->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-24' ?> <?php echo $doc_file->fieldclass; ?>">
                    <?php if ($doc_file->params->get('core.show_lable') == 1 || $doc_file->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $doc_file->id; ?>" for="field_<?php echo $doc_file->id; ?>"
                               class="control-label <?php echo $doc_file->class; ?>">
                            <?php if ($doc_file->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($doc_file->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($doc_file->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($doc_file->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($doc_file->translateDescription ? JText::_($doc_file->description) : $doc_file->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $doc_file->label; ?>

                        </label>
                        <?php if (in_array($doc_file->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($doc_file->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($doc_file->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $doc_file->fieldclass ?>">
                        <div id="field-alert-<?php echo $doc_file->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $doc_file->result; ?>
                    </div>
                </div>
                <!-- Inline Documentation goes here -->
                <?php $inline_doc = $this->sorted_fields[0][44] ?>
                <div id="fld-<?php echo $inline_doc->id; ?>"
                     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-44' ?> <?php echo $inline_doc->fieldclass; ?>">
                    <?php if ($inline_doc->params->get('core.show_lable') == 1 || $inline_doc->params->get('core.show_lable') == 3): ?>
                        <label id="lbl-<?php echo $inline_doc->id; ?>" for="field_<?php echo $inline_doc->id; ?>"
                               class="control-label <?php echo $inline_doc->class; ?>">
                            <?php if ($inline_doc->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')): ?>
                                <?php echo HTMLFormatHelper::icon($inline_doc->params->get('core.icon')); ?>
                            <?php endif; ?>


                            <?php if ($inline_doc->required): ?>
                                <span class="pull-right" rel="tooltip"
                                      data-original-title="<?php echo JText::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
                            <?php endif; ?>

                            <?php if ($inline_doc->description): ?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;"
                                      data-original-title="<?php echo htmlentities(($inline_doc->translateDescription ? JText::_($inline_doc->description) : $inline_doc->description), ENT_COMPAT, 'UTF-8'); ?>">
                <?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
              </span>
                            <?php endif; ?>

                            <?php echo $inline_doc->label; ?>

                        </label>
                        <?php if (in_array($inline_doc->params->get('core.label_break'), array(1, 3))): ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div
                        class="controls<?php if (in_array($inline_doc->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($inline_doc->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : NULL) ?><?php echo $inline_doc->fieldclass ?>">
                        <div id="field-alert-<?php echo $inline_doc->id ?>" class="alert alert-error"
                             style="display:none"></div>
                        <?php echo $inline_doc->result; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php unset($this->sorted_fields[0]); ?>
<?php group_end($this); ?>

<?php if (count($this->meta)): ?>
    <?php $started = true ?>
    <?php group_start($this, JText::_('CSEO'), 'tab-meta'); ?>
    <?php foreach ($this->meta as $label => $meta_name): ?>
        <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="jform_meta_descr-lbl" class="control-label" title="" for="jform_<?php echo $meta_name; ?>">
                <?php echo JText::_($label); ?>
            </label>

            <div class="controls">
                <div class="row-fluid">
                    <?php echo $this->form->getInput($meta_name); ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php group_end($this); ?>
<?php endif; ?>



<?php if (count($this->core_admin_fields)): ?>
    <?php $started = true ?>
    <?php group_start($this, 'Special Fields', 'tab-special'); ?>
    <div class="admin">
        <?php foreach ($this->core_admin_fields as $key => $field): ?>
            <div class="control-group odd<?php echo $k = 1 - $k ?>">
                <label id="jform_<?php echo $field ?>-lbl" class="control-label"
                       for="jform_<?php echo $field ?>"><?php echo strip_tags($this->form->getLabel($field)); ?></label>

                <div class="controls field-<?php echo $field; ?>">
                    <?php echo $this->form->getInput($field); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php group_end($this); ?>
<?php endif; ?>

<?php if (count($this->core_fields)): ?>
    <?php group_start($this, 'Core Fields', 'tab-core'); ?>
    <?php foreach ($this->core_fields as $key => $field): ?>
        <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="jform_<?php echo $field ?>-lbl" class="control-label" for="jform_<?php echo $field ?>">
                <?php if ($params->get('tmpl_core.form_' . $field . '_icon', 1)): ?>
                    <?php echo HTMLFormatHelper::icon('core-' . $field . '.png'); ?>
                <?php endif; ?>
                <?php echo strip_tags($this->form->getLabel($field)); ?>
            </label>

            <div class="controls">
                <?php echo $this->form->getInput($field); ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php group_end($this); ?>
<?php endif; ?>

<?php if ($started): ?>
    <?php total_end($this); ?>
<?php endif; ?>
    <br/>
    </div>

    <script type="text/javascript">
	
        <?php if(in_array($params->get('tmpl_params.form_grouping_type', 0), array(1,4))):?>
        jQuery('#tabs-list a:first').tab('show');
        <?php elseif(in_array($params->get('tmpl_params.form_grouping_type', 0), array(2))):?>
        jQuery('#tab-main').collapse('show');
        <?php endif;?>
    </script>

    <script
        src="<?php echo JURI::root(); ?>components/com_cobalt/views/form/tmpl/default_form_api_form/z-schema/ZSchema-browser-min.js"
        type="text/javascript"></script>

    <script>
        (function ($) {
            $('.asg-api-step-title-container .asg-api-step-title').on('click', '.icon-chevron-down', function () {
                $(this).removeClass('icon-chevron-down').addClass('icon-chevron-right');
                $(this).parent().parent().next().hide();
            });
            $('.asg-api-step-title-container .asg-api-step-title').on('click', '.icon-chevron-right', function () {
                $(this).removeClass('icon-chevron-right').addClass('icon-chevron-down');
                $(this).parent().parent().next().show();
            });
        }(jQuery));

        (function ($) {
            var specData, fileName, realName, deleted_operation_ids = [], operations_doc1, operations_doc2, is_deleted_operations = false;
            var originSpecName = "<?php echo $this->fields[23]->value[0]['realname'];?>";
            var originSpecFileName = "<?php echo $this->fields[23]->value[0]['filename'];?>";
            var originalName = '';
            var originalEnvironments = [];
            var originalTargetEnvironments = [];
            var originalCreateProxy = '';
            var currentName = '';
            var currentEnvironments = [];
            var currentTargetEnvironments = [];
            var currentCreateProxy = '';
            var sRecourcePath = '';
            var sAPIType = '';
            var operationsCount = <?php echo count($old_operations_of_api); ?>;

			//Visual Mapper Removal
	
			$("#fld-146, #fld-147, #fld-206, #fld-228, #fld-229, #fld-230").hide();
			$("#fld-147").show();
			
            $(function () {
                $('.asg-api-step-title-container .asg-api-step-title').on('click', '.icon-chevron-down', function () {
                    $(this).removeClass('icon-chevron-down').addClass('icon-chevron-right');
                    $(this).parent().parent().next().hide();
                });
                $('.asg-api-step-title-container .asg-api-step-title').on('click', '.icon-chevron-right', function () {
                    $(this).removeClass('icon-chevron-right').addClass('icon-chevron-down');
                    $(this).parent().parent().next().show();
                });
                operations_doc1 = $('#fld-23 .filename').eq(1).text();
                originalName = $('input[name="jform[title]"]').val();
                originalEnvironments = getEnvironments();
                originalRequestPreprocess = getRequestPreprocess();
                originalRequestTransform = getRequestTransform();
                originalResponseTransform = getResponseTransform();
                originalFaultTransform = getFaultTransform();
                sRecourcePath = $('input[name="jform[fields][22]"]').val();
                sAPIType = $('select[name="jform[fields][75]"]').val();
                originalTargetEnvironments = getTargetEnvironments();
                originalCreateProxy = $('input:checked[name="jform[fields][145]"]').val();
                sorted_value = $('#sorted_value').val();
                var checkApiType = $('select[name="jform[fields][75]"]').val();
                if (checkApiType === 'SOAP') {
                    $('#fld-23').addClass('hidden-div');
                    $('#fld-127').removeClass('hidden-div');
                } else {
                    $('#fld-127').addClass('hidden-div');
                    $('#fld-23').removeClass('hidden-div');
                }
                $('#form_field_list_75').change(function () {
                    var checkApiType = $('select[name="jform[fields][75]"]').val();
                    if (checkApiType === 'SOAP') {
                        $('#fld-23').addClass('hidden-div');
                        $('#fld-127').removeClass('hidden-div');
                    } else {
                        $('#fld-127').addClass('hidden-div');
                        $('#fld-23').removeClass('hidden-div');
                    }
                });
				// Visual Mapper
                /*$('#fld-145 button').on('click', function () {
                    if (/^bool-n/.test(this.id)) {
                        $("#fld-146, #fld-147, #fld-206, #fld-228, #fld-229, #fld-230").hide();
                        $('#fld-146 #field_146').val('');
                        $('#fld-147 #parent_list147 > div,#fld-206 #parent_list206 > div,#fld-228 #parent_list228 > div,#fld-229 #parent_list229 > div,#fld-230 #parent_list230 > div').remove();
                    } else {
                        $("#fld-146, #fld-147, #fld-206, #fld-228, #fld-229, #fld-230").show();
                    }
                });*/
				
				$('#fld-145 button').on('click', function () {
                    if (/^bool-n/.test(this.id)) {
                        $("#fld-146, #fld-147").hide();
                        $('#fld-146 #field_146').val('');
                        $('#fld-147 #parent_list147 > div').remove();
                    } else {
                        $("#fld-146, #fld-147").show();
                    }
                });

                <?php if(!$this->item->id):?>
                (function initForm() {
                    $("#bool-y145").addClass("active btn-success").prev("input").attr("checked", true);
                }());
                <?php else:?>
                if (sorted_value == -1) {
                    $("#fld-146, #fld-147,#fld-206, #fld-228, #fld-229, #fld-230").hide();
                } else if (sorted_value == 1) {
                    $("#fld-146, #fld-147,#fld-206, #fld-228, #fld-229, #fld-230").show();
                }
                <?php endif;?>
            });

            function getEnvironments() {
                return _getSelectedItems('parent_list26');
            }

            function getRequestPreprocess(){
                return _getSelectedItems('parent_list206');
            }
            function getRequestTransform(){
                return _getSelectedItems('parent_list228');
            }
            function getResponseTransform(){
                return _getSelectedItems('parent_list229');
            }
            function getFaultTransform(){
                return _getSelectedItems('parent_list230');
            }

            function getTargetEnvironments() {
                return _getSelectedItems('parent_list147');
            }

            function _getSelectedItems(id) {
                var envWrap = $("#" + id),
                    targetArray = [];
                envWrap.find(".list-item").each(function (i, ele) {
                    targetArray.push($(ele).attr("rel"));
                });

                targetArray = targetArray.sort();
                envWrap.attr("original", targetArray);
                return targetArray.sort();
            }

            /**
             * validate uploaded json file according to swagger spec1.2
             * @param  {[object]} specData [description]
             * @return {[boolean]}          [description]
             */
            function validateSwagger(validateData) {
                var schemaV1 = {
                    "$schema": "http://json-schema.org/draft-04/schema#",
                    "type": "object",
                    "properties": {
                        "apiVersion": {
                            "type": "string"
                        },
                        "swaggerVersion": {
                            "type": "string"
                        },
                        "basePath": {
                            "type": "string"
                        },
                        "resourcePath": {
                            "type": "string"
                        },
                        "apis": {
                            "type": "array",
                            "items": [
                                {
                                    "type": "object",
                                    "required": ["path", "operations"],
                                    "properties": {
                                        "path": {
                                            "type": "string"
                                        },
                                        "description": {
                                            "type": "string"
                                        },
                                        "operations": {
                                            "type": "array",
                                            "items": {
                                                "type": "object",
                                                "required": ["httpMethod", "nickname", "parameters"],
                                                "properties": {
                                                    "httpMethod": {
                                                        "type": "string"
                                                    },
                                                    "timeout": {
                                                        "type": "integer"
                                                    },
                                                    "timeout": {
                                                        "type": "integer"
                                                    },
                                                    "summary": {
                                                        "type": "string"
                                                    },
                                                    "produces": {
                                                        "type": "array",
                                                        "items": {
                                                            "type": "string"
                                                        }
                                                    },
                                                    "notes": {
                                                        "type": "string"
                                                    },
                                                    "responseClass": {
                                                        "type": "string"
                                                    },
                                                    "nickname": {
                                                        "type": "string"
                                                    },
                                                    "parameters": {
                                                        "type": "array",
                                                        "items": {
                                                            "type": "object",
                                                            "properties": {
                                                                "name": {
                                                                    "type": "string"
                                                                },
                                                                "description": {
                                                                    "type": "string"
                                                                },
                                                                "paramType": {
                                                                    "type": "string"
                                                                },
                                                                "required": {
                                                                    "type": "boolean"
                                                                },
                                                                "allowMultiple": {
                                                                    "type": "boolean"
                                                                },
                                                                "type": {
                                                                    "type": "string"
                                                                }
                                                            }
                                                        }
                                                    },
                                                    "errorResponses": {
                                                        "type": "array",
                                                        "items": [
                                                            {
                                                                "type": "object",
                                                                "properties": {
                                                                    "code": {
                                                                        "type": "integer"
                                                                    },
                                                                    "reason": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            },
                                                            {
                                                                "type": "object",
                                                                "properties": {
                                                                    "code": {
                                                                        "type": "integer"
                                                                    },
                                                                    "reason": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            }
                                                        ]
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    "type": "object",
                                    "required": ["path", "operations"],
                                    "properties": {
                                        "path": {
                                            "type": "string"
                                        },
                                        "description": {
                                            "type": "string"
                                        },
                                        "operations": {
                                            "type": "array",
                                            "items": {
                                                "type": "object",
                                                "required": ["httpMethod", "nickname", "parameters"],
                                                "properties": {
                                                    "httpMethod": {
                                                        "type": "string"
                                                    },
                                                    "timeout": {
                                                        "type": "integer"
                                                    },
                                                    "summary": {
                                                        "type": "string"
                                                    },
                                                    "notes": {
                                                        "type": "string"
                                                    },
                                                    "responseClass": {
                                                        "type": "string"
                                                    },
                                                    "nickname": {
                                                        "type": "string"
                                                    },
                                                    "parameters": {
                                                        "type": "array",
                                                        "items": {
                                                            "type": "object",
                                                            "properties": {
                                                                "name": {
                                                                    "type": "string"
                                                                },
                                                                "description": {
                                                                    "type": "string"
                                                                },
                                                                "paramType": {
                                                                    "type": "string"
                                                                },
                                                                "required": {
                                                                    "type": "boolean"
                                                                },
                                                                "allowMultiple": {
                                                                    "type": "boolean"
                                                                },
                                                                "type": {
                                                                    "type": "string"
                                                                }
                                                            }
                                                        }
                                                    },
                                                    "errorResponses": {
                                                        "type": "array",
                                                        "items": [
                                                            {
                                                                "type": "object",
                                                                "properties": {
                                                                    "code": {
                                                                        "type": "integer"
                                                                    },
                                                                    "reason": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            },
                                                            {
                                                                "type": "object",
                                                                "properties": {
                                                                    "code": {
                                                                        "type": "integer"
                                                                    },
                                                                    "reason": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            }
                                                        ]
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    "type": "object",
                                    "required": ["path", "operations"],
                                    "properties": {
                                        "path": {
                                            "type": "string"
                                        },
                                        "description": {
                                            "type": "string"
                                        },
                                        "operations": {
                                            "type": "array",
                                            "items": {
                                                "type": "object",
                                                "required": ["httpMethod", "nickname", "parameters"],
                                                "properties": {
                                                    "httpMethod": {
                                                        "type": "string"
                                                    },
                                                    "timeout": {
                                                        "type": "integer"
                                                    },
                                                    "summary": {
                                                        "type": "string"
                                                    },
                                                    "notes": {
                                                        "type": "string"
                                                    },
                                                    "responseClass": {
                                                        "type": "string"
                                                    },
                                                    "nickname": {
                                                        "type": "string"
                                                    },
                                                    "parameters": {
                                                        "type": "array",
                                                        "items": {
                                                            "type": "object",
                                                            "properties": {
                                                                "name": {
                                                                    "type": "string"
                                                                },
                                                                "description": {
                                                                    "type": "string"
                                                                },
                                                                "paramType": {
                                                                    "type": "string"
                                                                },
                                                                "required": {
                                                                    "type": "boolean"
                                                                },
                                                                "allowMultiple": {
                                                                    "type": "boolean"
                                                                },
                                                                "type": {
                                                                    "type": "string"
                                                                }
                                                            }
                                                        }
                                                    },
                                                    "errorResponses": {
                                                        "type": "array",
                                                        "items": [
                                                            {
                                                                "type": "object",
                                                                "properties": {
                                                                    "code": {
                                                                        "type": "integer"
                                                                    },
                                                                    "reason": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            },
                                                            {
                                                                "type": "object",
                                                                "properties": {
                                                                    "code": {
                                                                        "type": "integer"
                                                                    },
                                                                    "reason": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            }
                                                        ]
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    "type": "object",
                                    "required": ["path", "operations"],
                                    "properties": {
                                        "path": {
                                            "type": "string"
                                        },
                                        "description": {
                                            "type": "string"
                                        },
                                        "operations": {
                                            "type": "array",
                                            "items": {
                                                "type": "object",
                                                "required": ["httpMethod", "nickname", "parameters"],
                                                "properties": {
                                                    "httpMethod": {
                                                        "type": "string"
                                                    },
                                                    "timeout": {
                                                        "type": "integer"
                                                    },
                                                    "summary": {
                                                        "type": "string"
                                                    },
                                                    "notes": {
                                                        "type": "string"
                                                    },
                                                    "responseClass": {
                                                        "type": "string"
                                                    },
                                                    "nickname": {
                                                        "type": "string"
                                                    },
                                                    "parameters": {
                                                        "type": "array",
                                                        "items": {
                                                            "type": "object",
                                                            "properties": {
                                                                "name": {
                                                                    "type": "string"
                                                                },
                                                                "description": {
                                                                    "type": "string"
                                                                },
                                                                "paramType": {
                                                                    "type": "string"
                                                                },
                                                                "required": {
                                                                    "type": "boolean"
                                                                },
                                                                "allowMultiple": {
                                                                    "type": "boolean"
                                                                },
                                                                "type": {
                                                                    "type": "string"
                                                                }
                                                            }
                                                        }
                                                    },
                                                    "errorResponses": {
                                                        "type": "array",
                                                        "items": [
                                                            {
                                                                "type": "object",
                                                                "properties": {
                                                                    "code": {
                                                                        "type": "integer"
                                                                    },
                                                                    "reason": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            },
                                                            {
                                                                "type": "object",
                                                                "properties": {
                                                                    "code": {
                                                                        "type": "integer"
                                                                    },
                                                                    "reason": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            }
                                                        ]
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    "type": "object",
                                    "required": ["path", "operations"],
                                    "properties": {
                                        "path": {
                                            "type": "string"
                                        },
                                        "description": {
                                            "type": "string"
                                        },
                                        "operations": {
                                            "type": "array",
                                            "items": {
                                                "type": "object",
                                                "required": ["httpMethod", "nickname", "parameters"],
                                                "properties": {
                                                    "httpMethod": {
                                                        "type": "string"
                                                    },
                                                    "timeout": {
                                                        "type": "integer"
                                                    },
                                                    "summary": {
                                                        "type": "string"
                                                    },
                                                    "responseClass": {
                                                        "type": "string"
                                                    },
                                                    "nickname": {
                                                        "type": "string"
                                                    },
                                                    "parameters": {
                                                        "type": "array",
                                                        "items": {
                                                            "type": "object",
                                                            "properties": {
                                                                "description": {
                                                                    "type": "string"
                                                                },
                                                                "paramType": {
                                                                    "type": "string"
                                                                },
                                                                "required": {
                                                                    "type": "boolean"
                                                                },
                                                                "allowMultiple": {
                                                                    "type": "boolean"
                                                                },
                                                                "type": {
                                                                    "type": "string"
                                                                }
                                                            }
                                                        }
                                                    },
                                                    "errorResponses": {
                                                        "type": "array",
                                                        "items": {
                                                            "type": "object",
                                                            "properties": {
                                                                "code": {
                                                                    "type": "integer"
                                                                },
                                                                "reason": {
                                                                    "type": "string"
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            ]
                        },
                        "authorizations": {
                            "type": "object",
                            "properties": {
                                "oauth2": {
                                    "type": "object",
                                    "properties": {
                                        "type": {
                                            "type": "string"
                                        },
                                        "scopes": {
                                            "type": "array",
                                            "items": {
                                                "type": "object",
                                                "properties": {
                                                    "scope": {
                                                        "type": "string"
                                                    },
                                                    "description": {
                                                        "type": "string"
                                                    }
                                                }
                                            }
                                        },
                                        "grantTypes": {
                                            "type": "object",
                                            "properties": {
                                                "implicit": {
                                                    "type": "object",
                                                    "properties": {
                                                        "loginEndpoint": {
                                                            "type": "object",
                                                            "properties": {
                                                                "url": {
                                                                    "type": "string"
                                                                }
                                                            }
                                                        },
                                                        "tokenName": {
                                                            "type": "string"
                                                        }
                                                    }
                                                },
                                                "authorization_code": {
                                                    "type": "object",
                                                    "properties": {
                                                        "tokenRequestEndpoint": {
                                                            "type": "object",
                                                            "properties": {
                                                                "url": {
                                                                    "type": "string"
                                                                },
                                                                "clientIdName": {
                                                                    "type": "string"
                                                                },
                                                                "clientSecretName": {
                                                                    "type": "string"
                                                                }
                                                            }
                                                        },
                                                        "tokenEndpoint": {
                                                            "type": "object",
                                                            "properties": {
                                                                "url": {
                                                                    "type": "string"
                                                                },
                                                                "tokenName": {
                                                                    "type": "string"
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        "info": {
                            "type": "object",
                            "properties": {
                                "title": {
                                    "type": "string"
                                },
                                "description": {
                                    "type": "string"
                                }
                            }
                        },
                        "models": {
                            "type": "object",
                            "properties": {
                                "Book": {
                                    "type": "object",
                                    "properties": {
                                        "properties": {
                                            "type": "object",
                                            "properties": {
                                                "Title": {
                                                    "type": "object",
                                                    "properties": {
                                                        "type": {
                                                            "type": "string"
                                                        }
                                                    }
                                                },
                                                "Author": {
                                                    "type": "object",
                                                    "properties": {
                                                        "type": {
                                                            "type": "string"
                                                        }
                                                    }
                                                },
                                                "ISBN": {
                                                    "type": "object",
                                                    "properties": {
                                                        "type": {
                                                            "type": "string"
                                                        }
                                                    }
                                                },
                                                "Date": {
                                                    "type": "object",
                                                    "properties": {
                                                        "type": {
                                                            "type": "string"
                                                        }
                                                    }
                                                },
                                                "Publisher": {
                                                    "type": "object",
                                                    "properties": {
                                                        "type": {
                                                            "type": "string"
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                "SuccessResponse": {
                                    "type": "object",
                                    "properties": {
                                        "properties": {
                                            "type": "object",
                                            "properties": {
                                                "Code": {
                                                    "type": "object",
                                                    "properties": {
                                                        "type": {
                                                            "type": "string"
                                                        }
                                                    }
                                                },
                                                "Message": {
                                                    "type": "object",
                                                    "properties": {
                                                        "type": {
                                                            "type": "string"
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    },
                    "required": [
                        "swaggerVersion",
                        "basePath",
                        "apis"
                    ]
                };
                var schemaV2 = {
                    "title": "A JSON Schema for Swagger 2.0 API.",
                    "id": "http://swagger.io/v2/schema.json#",
                    "$schema": "http://json-schema.org/draft-04/schema#",
                    "type": "object",
                    "required": [
                        "swagger",
                        "info",
                        "paths"
                    ],
                    "additionalProperties": false,
                    "patternProperties": {
                        "^x-": {
                            "$ref": "#/definitions/vendorExtension"
                        }
                    },
                    "properties": {
                        "swagger": {
                            "type": "string",
                            "enum": [
                                "2.0"
                            ],
                            "description": "The Swagger version of this document."
                        },
                        "info": {
                            "$ref": "#/definitions/info"
                        },
                        "host": {
                            "type": "string",
                            "format": "uri",
                            "description": "The fully qualified URI to the host of the API."
                        },
                        "basePath": {
                            "type": "string",
                            "pattern": "^/",
                            "description": "The base path to the API. Example: '/api'."
                        },
                        "schemes": {
                            "$ref": "#/definitions/schemesList"
                        },
                        "consumes": {
                            "description": "A list of MIME types accepted by the API.",
                            "$ref": "#/definitions/mediaTypeList"
                        },
                        "produces": {
                            "description": "A list of MIME types the API can produce.",
                            "$ref": "#/definitions/mediaTypeList"
                        },
                        "paths": {
                            "$ref": "#/definitions/paths"
                        },
                        "definitions": {
                            "$ref": "#/definitions/definitions"
                        },
                        "parameters": {
                            "$ref": "#/definitions/parameterDefinitions"
                        },
                        "responses": {
                            "$ref": "#/definitions/responseDefinitions"
                        },
                        "security": {
                            "$ref": "#/definitions/security"
                        },
                        "securityDefinitions": {
                            "$ref": "#/definitions/securityDefinitions"
                        },
                        "tags": {
                            "type": "array",
                            "items": {
                                "$ref": "#/definitions/tag"
                            },
                            "uniqueItems": true
                        },
                        "externalDocs": {
                            "$ref": "#/definitions/externalDocs"
                        }
                    },
                    "definitions": {
                        "info": {
                            "type": "object",
                            "description": "General information about the API.",
                            "required": [
                                "version",
                                "title"
                            ],
                            "additionalProperties": false,
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            },
                            "properties": {
                                "title": {
                                    "type": "string",
                                    "description": "A unique and precise title of the API."
                                },
                                "version": {
                                    "type": "string",
                                    "description": "A semantic version number of the API."
                                },
                                "description": {
                                    "type": "string",
                                    "description": "A longer description of the API. Should be different from the title.  Github-flavored markdown is allowed."
                                },
                                "termsOfService": {
                                    "type": "string",
                                    "description": "The terms of service for the API."
                                },
                                "contact": {
                                    "$ref": "#/definitions/contact"
                                },
                                "license": {
                                    "$ref": "#/definitions/license"
                                }
                            }
                        },
                        "contact": {
                            "type": "object",
                            "description": "Contact information for the owners of the API.",
                            "additionalProperties": false,
                            "properties": {
                                "name": {
                                    "type": "string",
                                    "description": "The identifying name of the contact person/organization."
                                },
                                "url": {
                                    "type": "string",
                                    "description": "The URL pointing to the contact information.",
                                    "format": "uri"
                                },
                                "email": {
                                    "type": "string",
                                    "description": "The email address of the contact person/organization.",
                                    "format": "email"
                                }
                            }
                        },
                        "license": {
                            "type": "object",
                            "required": [
                                "name"
                            ],
                            "additionalProperties": false,
                            "properties": {
                                "name": {
                                    "type": "string",
                                    "description": "The name of the license type. It's encouraged to use an OSI compatible license."
                                },
                                "url": {
                                    "type": "string",
                                    "description": "The URL pointing to the license.",
                                    "format": "uri"
                                }
                            }
                        },
                        "paths": {
                            "type": "object",
                            "description": "Relative paths to the individual endpoints. They must be relative to the 'basePath'.",
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                },
                                "^/": {
                                    "$ref": "#/definitions/pathItem"
                                }
                            },
                            "additionalProperties": false
                        },
                        "definitions": {
                            "type": "object",
                            "additionalProperties": {
                                "$ref": "#/definitions/schema"
                            },
                            "description": "One or more JSON objects describing the schemas being consumed and produced by the API."
                        },
                        "parameterDefinitions": {
                            "type": "object",
                            "additionalProperties": {
                                "$ref": "#/definitions/parameter"
                            },
                            "description": "One or more JSON representations for parameters"
                        },
                        "responseDefinitions": {
                            "type": "object",
                            "additionalProperties": {
                                "$ref": "#/definitions/response"
                            },
                            "description": "One or more JSON representations for parameters"
                        },
                        "externalDocs": {
                            "type": "object",
                            "additionalProperties": false,
                            "description": "information about external documentation",
                            "required": [
                                "url"
                            ],
                            "properties": {
                                "description": {
                                    "type": "string"
                                },
                                "url": {
                                    "type": "string",
                                    "format": "uri"
                                }
                            }
                        },
                        "examples": {
                            "type": "object",
                            "patternProperties": {
                                "^[a-z0-9-]+/[a-z0-9\\-+]+$": {}
                            },
                            "additionalProperties": false
                        },
                        "mimeType": {
                            "type": "string",
                            "description": "The MIME type of the HTTP message."
                        },
                        "operation": {
                            "type": "object",
                            "required": [
                                "responses"
                            ],
                            "additionalProperties": false,
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            },
                            "properties": {
                                "tags": {
                                    "type": "array",
                                    "items": {
                                        "type": "string"
                                    },
                                    "uniqueItems": true
                                },
                                "summary": {
                                    "type": "string",
                                    "description": "A brief summary of the operation."
                                },
                                "description": {
                                    "type": "string",
                                    "description": "A longer description of the operation, github-flavored markdown is allowed."
                                },
                                "externalDocs": {
                                    "$ref": "#/definitions/externalDocs"
                                },
                                "operationId": {
                                    "type": "string",
                                    "description": "A friendly name of the operation"
                                },
                                "produces": {
                                    "description": "A list of MIME types the API can produce.",
                                    "$ref": "#/definitions/mediaTypeList"
                                },
                                "consumes": {
                                    "description": "A list of MIME types the API can consume.",
                                    "$ref": "#/definitions/mediaTypeList"
                                },
                                "parameters": {
                                    "$ref": "#/definitions/parametersList"
                                },
                                "responses": {
                                    "$ref": "#/definitions/responses"
                                },
                                "schemes": {
                                    "$ref": "#/definitions/schemesList"
                                },
                                "deprecated": {
                                    "type": "boolean",
                                    "default": false
                                },
                                "security": {
                                    "$ref": "#/definitions/security"
                                }
                            }
                        },
                        "pathItem": {
                            "type": "object",
                            "additionalProperties": false,
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            },
                            "properties": {
                                "$ref": {
                                    "type": "string"
                                },
                                "get": {
                                    "$ref": "#/definitions/operation"
                                },
                                "put": {
                                    "$ref": "#/definitions/operation"
                                },
                                "post": {
                                    "$ref": "#/definitions/operation"
                                },
                                "delete": {
                                    "$ref": "#/definitions/operation"
                                },
                                "options": {
                                    "$ref": "#/definitions/operation"
                                },
                                "head": {
                                    "$ref": "#/definitions/operation"
                                },
                                "patch": {
                                    "$ref": "#/definitions/operation"
                                },
                                "parameters": {
                                    "$ref": "#/definitions/parametersList"
                                }
                            }
                        },
                        "responses": {
                            "type": "object",
                            "description": "Response objects names can either be any valid HTTP status code or 'default'.",
                            "minProperties": 1,
                            "additionalProperties": false,
                            "patternProperties": {
                                "^([0-9]{3})$|^(default)$": {
                                    "$ref": "#/definitions/responseValue"
                                },
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            },
                            "not": {
                                "type": "object",
                                "additionalProperties": false,
                                "patternProperties": {
                                    "^x-": {
                                        "$ref": "#/definitions/vendorExtension"
                                    }
                                }
                            }
                        },
                        "responseValue": {
                            "oneOf": [
                                {
                                    "$ref": "#/definitions/response"
                                },
                                {
                                    "$ref": "#/definitions/jsonReference"
                                }
                            ]
                        },
                        "response": {
                            "type": "object",
                            "required": [
                                "description"
                            ],
                            "properties": {
                                "description": {
                                    "type": "string"
                                },
                                "schema": {
                                    "$ref": "#/definitions/schema"
                                },
                                "headers": {
                                    "$ref": "#/definitions/headers"
                                },
                                "examples": {
                                    "$ref": "#/definitions/examples"
                                }
                            },
                            "additionalProperties": false
                        },
                        "headers": {
                            "type": "object",
                            "additionalProperties": {
                                "$ref": "#/definitions/header"
                            }
                        },
                        "header": {
                            "type": "object",
                            "additionalProperties": false,
                            "required": [
                                "type"
                            ],
                            "properties": {
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "string",
                                        "number",
                                        "integer",
                                        "boolean",
                                        "array"
                                    ]
                                },
                                "format": {
                                    "type": "string"
                                },
                                "items": {
                                    "$ref": "#/definitions/primitivesItems"
                                },
                                "collectionFormat": {
                                    "$ref": "#/definitions/collectionFormat"
                                },
                                "default": {
                                    "$ref": "#/definitions/default"
                                },
                                "maximum": {
                                    "$ref": "#/definitions/maximum"
                                },
                                "exclusiveMaximum": {
                                    "$ref": "#/definitions/exclusiveMaximum"
                                },
                                "minimum": {
                                    "$ref": "#/definitions/minimum"
                                },
                                "exclusiveMinimum": {
                                    "$ref": "#/definitions/exclusiveMinimum"
                                },
                                "maxLength": {
                                    "$ref": "#/definitions/maxLength"
                                },
                                "minLength": {
                                    "$ref": "#/definitions/minLength"
                                },
                                "pattern": {
                                    "$ref": "#/definitions/pattern"
                                },
                                "maxItems": {
                                    "$ref": "#/definitions/maxItems"
                                },
                                "minItems": {
                                    "$ref": "#/definitions/minItems"
                                },
                                "uniqueItems": {
                                    "$ref": "#/definitions/uniqueItems"
                                },
                                "enum": {
                                    "$ref": "#/definitions/enum"
                                },
                                "multipleOf": {
                                    "$ref": "#/definitions/multipleOf"
                                },
                                "description": {
                                    "type": "string"
                                }
                            }
                        },
                        "vendorExtension": {
                            "description": "Any property starting with x- is valid.",
                            "additionalProperties": true,
                            "additionalItems": true
                        },
                        "bodyParameter": {
                            "type": "object",
                            "required": [
                                "name",
                                "in",
                                "schema"
                            ],
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            },
                            "properties": {
                                "description": {
                                    "type": "string",
                                    "description": "A brief description of the parameter. This could contain examples of use.  Github-flavored markdown is allowed."
                                },
                                "name": {
                                    "type": "string",
                                    "description": "The name of the parameter."
                                },
                                "in": {
                                    "type": "string",
                                    "description": "Determines the location of the parameter.",
                                    "enum": [
                                        "body"
                                    ]
                                },
                                "required": {
                                    "type": "boolean",
                                    "description": "Determines whether or not this parameter is required or optional.",
                                    "default": false
                                },
                                "schema": {
                                    "$ref": "#/definitions/schema"
                                }
                            },
                            "additionalProperties": false
                        },
                        "headerParameterSubSchema": {
                            "additionalProperties": false,
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            },
                            "properties": {
                                "required": {
                                    "type": "boolean",
                                    "description": "Determines whether or not this parameter is required or optional.",
                                    "default": false
                                },
                                "in": {
                                    "type": "string",
                                    "description": "Determines the location of the parameter.",
                                    "enum": [
                                        "header"
                                    ]
                                },
                                "description": {
                                    "type": "string",
                                    "description": "A brief description of the parameter. This could contain examples of use.  Github-flavored markdown is allowed."
                                },
                                "name": {
                                    "type": "string",
                                    "description": "The name of the parameter."
                                },
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "string",
                                        "number",
                                        "boolean",
                                        "integer",
                                        "array"
                                    ]
                                },
                                "format": {
                                    "type": "string"
                                },
                                "items": {
                                    "$ref": "#/definitions/primitivesItems"
                                },
                                "collectionFormat": {
                                    "$ref": "#/definitions/collectionFormat"
                                },
                                "default": {
                                    "$ref": "#/definitions/default"
                                },
                                "maximum": {
                                    "$ref": "#/definitions/maximum"
                                },
                                "exclusiveMaximum": {
                                    "$ref": "#/definitions/exclusiveMaximum"
                                },
                                "minimum": {
                                    "$ref": "#/definitions/minimum"
                                },
                                "exclusiveMinimum": {
                                    "$ref": "#/definitions/exclusiveMinimum"
                                },
                                "maxLength": {
                                    "$ref": "#/definitions/maxLength"
                                },
                                "minLength": {
                                    "$ref": "#/definitions/minLength"
                                },
                                "pattern": {
                                    "$ref": "#/definitions/pattern"
                                },
                                "maxItems": {
                                    "$ref": "#/definitions/maxItems"
                                },
                                "minItems": {
                                    "$ref": "#/definitions/minItems"
                                },
                                "uniqueItems": {
                                    "$ref": "#/definitions/uniqueItems"
                                },
                                "enum": {
                                    "$ref": "#/definitions/enum"
                                },
                                "multipleOf": {
                                    "$ref": "#/definitions/multipleOf"
                                }
                            }
                        },
                        "queryParameterSubSchema": {
                            "additionalProperties": false,
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            },
                            "properties": {
                                "required": {
                                    "type": "boolean",
                                    "description": "Determines whether or not this parameter is required or optional.",
                                    "default": false
                                },
                                "in": {
                                    "type": "string",
                                    "description": "Determines the location of the parameter.",
                                    "enum": [
                                        "query"
                                    ]
                                },
                                "description": {
                                    "type": "string",
                                    "description": "A brief description of the parameter. This could contain examples of use.  Github-flavored markdown is allowed."
                                },
                                "name": {
                                    "type": "string",
                                    "description": "The name of the parameter."
                                },
                                "allowEmptyValue": {
                                    "type": "boolean",
                                    "default": false,
                                    "description": "allows sending a parameter by name only or with an empty value."
                                },
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "string",
                                        "number",
                                        "boolean",
                                        "integer",
                                        "array"
                                    ]
                                },
                                "format": {
                                    "type": "string"
                                },
                                "items": {
                                    "$ref": "#/definitions/primitivesItems"
                                },
                                "collectionFormat": {
                                    "$ref": "#/definitions/collectionFormatWithMulti"
                                },
                                "default": {
                                    "$ref": "#/definitions/default"
                                },
                                "maximum": {
                                    "$ref": "#/definitions/maximum"
                                },
                                "exclusiveMaximum": {
                                    "$ref": "#/definitions/exclusiveMaximum"
                                },
                                "minimum": {
                                    "$ref": "#/definitions/minimum"
                                },
                                "exclusiveMinimum": {
                                    "$ref": "#/definitions/exclusiveMinimum"
                                },
                                "maxLength": {
                                    "$ref": "#/definitions/maxLength"
                                },
                                "minLength": {
                                    "$ref": "#/definitions/minLength"
                                },
                                "pattern": {
                                    "$ref": "#/definitions/pattern"
                                },
                                "maxItems": {
                                    "$ref": "#/definitions/maxItems"
                                },
                                "minItems": {
                                    "$ref": "#/definitions/minItems"
                                },
                                "uniqueItems": {
                                    "$ref": "#/definitions/uniqueItems"
                                },
                                "enum": {
                                    "$ref": "#/definitions/enum"
                                },
                                "multipleOf": {
                                    "$ref": "#/definitions/multipleOf"
                                }
                            }
                        },
                        "formDataParameterSubSchema": {
                            "additionalProperties": false,
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            },
                            "properties": {
                                "required": {
                                    "type": "boolean",
                                    "description": "Determines whether or not this parameter is required or optional.",
                                    "default": false
                                },
                                "in": {
                                    "type": "string",
                                    "description": "Determines the location of the parameter.",
                                    "enum": [
                                        "formData"
                                    ]
                                },
                                "description": {
                                    "type": "string",
                                    "description": "A brief description of the parameter. This could contain examples of use.  Github-flavored markdown is allowed."
                                },
                                "name": {
                                    "type": "string",
                                    "description": "The name of the parameter."
                                },
                                "allowEmptyValue": {
                                    "type": "boolean",
                                    "default": false,
                                    "description": "allows sending a parameter by name only or with an empty value."
                                },
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "string",
                                        "number",
                                        "boolean",
                                        "integer",
                                        "array",
                                        "file"
                                    ]
                                },
                                "format": {
                                    "type": "string"
                                },
                                "items": {
                                    "$ref": "#/definitions/primitivesItems"
                                },
                                "collectionFormat": {
                                    "$ref": "#/definitions/collectionFormatWithMulti"
                                },
                                "default": {
                                    "$ref": "#/definitions/default"
                                },
                                "maximum": {
                                    "$ref": "#/definitions/maximum"
                                },
                                "exclusiveMaximum": {
                                    "$ref": "#/definitions/exclusiveMaximum"
                                },
                                "minimum": {
                                    "$ref": "#/definitions/minimum"
                                },
                                "exclusiveMinimum": {
                                    "$ref": "#/definitions/exclusiveMinimum"
                                },
                                "maxLength": {
                                    "$ref": "#/definitions/maxLength"
                                },
                                "minLength": {
                                    "$ref": "#/definitions/minLength"
                                },
                                "pattern": {
                                    "$ref": "#/definitions/pattern"
                                },
                                "maxItems": {
                                    "$ref": "#/definitions/maxItems"
                                },
                                "minItems": {
                                    "$ref": "#/definitions/minItems"
                                },
                                "uniqueItems": {
                                    "$ref": "#/definitions/uniqueItems"
                                },
                                "enum": {
                                    "$ref": "#/definitions/enum"
                                },
                                "multipleOf": {
                                    "$ref": "#/definitions/multipleOf"
                                }
                            }
                        },
                        "pathParameterSubSchema": {
                            "additionalProperties": false,
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            },
                            "properties": {
                                "required": {
                                    "type": "boolean",
                                    "enum": [
                                        true
                                    ],
                                    "description": "Determines whether or not this parameter is required or optional."
                                },
                                "in": {
                                    "type": "string",
                                    "description": "Determines the location of the parameter.",
                                    "enum": [
                                        "path"
                                    ]
                                },
                                "description": {
                                    "type": "string",
                                    "description": "A brief description of the parameter. This could contain examples of use.  Github-flavored markdown is allowed."
                                },
                                "name": {
                                    "type": "string",
                                    "description": "The name of the parameter."
                                },
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "string",
                                        "number",
                                        "boolean",
                                        "integer",
                                        "array"
                                    ]
                                },
                                "format": {
                                    "type": "string"
                                },
                                "items": {
                                    "$ref": "#/definitions/primitivesItems"
                                },
                                "collectionFormat": {
                                    "$ref": "#/definitions/collectionFormat"
                                },
                                "default": {
                                    "$ref": "#/definitions/default"
                                },
                                "maximum": {
                                    "$ref": "#/definitions/maximum"
                                },
                                "exclusiveMaximum": {
                                    "$ref": "#/definitions/exclusiveMaximum"
                                },
                                "minimum": {
                                    "$ref": "#/definitions/minimum"
                                },
                                "exclusiveMinimum": {
                                    "$ref": "#/definitions/exclusiveMinimum"
                                },
                                "maxLength": {
                                    "$ref": "#/definitions/maxLength"
                                },
                                "minLength": {
                                    "$ref": "#/definitions/minLength"
                                },
                                "pattern": {
                                    "$ref": "#/definitions/pattern"
                                },
                                "maxItems": {
                                    "$ref": "#/definitions/maxItems"
                                },
                                "minItems": {
                                    "$ref": "#/definitions/minItems"
                                },
                                "uniqueItems": {
                                    "$ref": "#/definitions/uniqueItems"
                                },
                                "enum": {
                                    "$ref": "#/definitions/enum"
                                },
                                "multipleOf": {
                                    "$ref": "#/definitions/multipleOf"
                                }
                            }
                        },
                        "nonBodyParameter": {
                            "type": "object",
                            "required": [
                                "name",
                                "in",
                                "type"
                            ],
                            "oneOf": [
                                {
                                    "$ref": "#/definitions/headerParameterSubSchema"
                                },
                                {
                                    "$ref": "#/definitions/formDataParameterSubSchema"
                                },
                                {
                                    "$ref": "#/definitions/queryParameterSubSchema"
                                },
                                {
                                    "$ref": "#/definitions/pathParameterSubSchema"
                                }
                            ]
                        },
                        "parameter": {
                            "oneOf": [
                                {
                                    "$ref": "#/definitions/bodyParameter"
                                },
                                {
                                    "$ref": "#/definitions/nonBodyParameter"
                                }
                            ]
                        },
                        "schema": {
                            "type": "object",
                            "description": "A deterministic version of a JSON Schema object.",
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            },
                            "properties": {
                                "$ref": {
                                    "type": "string"
                                },
                                "format": {
                                    "type": "string"
                                },
                                "title": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/title"
                                },
                                "description": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/description"
                                },
                                "default": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/default"
                                },
                                "multipleOf": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/multipleOf"
                                },
                                "maximum": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/maximum"
                                },
                                "exclusiveMaximum": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/exclusiveMaximum"
                                },
                                "minimum": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/minimum"
                                },
                                "exclusiveMinimum": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/exclusiveMinimum"
                                },
                                "maxLength": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/definitions/positiveInteger"
                                },
                                "minLength": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/definitions/positiveIntegerDefault0"
                                },
                                "pattern": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/pattern"
                                },
                                "maxItems": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/definitions/positiveInteger"
                                },
                                "minItems": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/definitions/positiveIntegerDefault0"
                                },
                                "uniqueItems": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/uniqueItems"
                                },
                                "maxProperties": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/definitions/positiveInteger"
                                },
                                "minProperties": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/definitions/positiveIntegerDefault0"
                                },
                                "required": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/definitions/stringArray"
                                },
                                "enum": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/enum"
                                },
                                "additionalProperties": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/additionalProperties"
                                },
                                "type": {
                                    "$ref": "http://json-schema.org/draft-04/schema#/properties/type"
                                },
                                "items": {
                                    "anyOf": [
                                        {
                                            "$ref": "#/definitions/schema"
                                        },
                                        {
                                            "type": "array",
                                            "minItems": 1,
                                            "items": {
                                                "$ref": "#/definitions/schema"
                                            }
                                        }
                                    ],
                                    "default": {}
                                },
                                "allOf": {
                                    "type": "array",
                                    "minItems": 1,
                                    "items": {
                                        "$ref": "#/definitions/schema"
                                    }
                                },
                                "properties": {
                                    "type": "object",
                                    "additionalProperties": {
                                        "$ref": "#/definitions/schema"
                                    },
                                    "default": {}
                                },
                                "discriminator": {
                                    "type": "string"
                                },
                                "readOnly": {
                                    "type": "boolean",
                                    "default": false
                                },
                                "xml": {
                                    "$ref": "#/definitions/xml"
                                },
                                "externalDocs": {
                                    "$ref": "#/definitions/externalDocs"
                                },
                                "example": {}
                            },
                            "additionalProperties": false
                        },
                        "primitivesItems": {
                            "type": "object",
                            "additionalProperties": false,
                            "properties": {
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "string",
                                        "number",
                                        "integer",
                                        "boolean",
                                        "array"
                                    ]
                                },
                                "format": {
                                    "type": "string"
                                },
                                "items": {
                                    "$ref": "#/definitions/primitivesItems"
                                },
                                "collectionFormat": {
                                    "$ref": "#/definitions/collectionFormat"
                                },
                                "default": {
                                    "$ref": "#/definitions/default"
                                },
                                "maximum": {
                                    "$ref": "#/definitions/maximum"
                                },
                                "exclusiveMaximum": {
                                    "$ref": "#/definitions/exclusiveMaximum"
                                },
                                "minimum": {
                                    "$ref": "#/definitions/minimum"
                                },
                                "exclusiveMinimum": {
                                    "$ref": "#/definitions/exclusiveMinimum"
                                },
                                "maxLength": {
                                    "$ref": "#/definitions/maxLength"
                                },
                                "minLength": {
                                    "$ref": "#/definitions/minLength"
                                },
                                "pattern": {
                                    "$ref": "#/definitions/pattern"
                                },
                                "maxItems": {
                                    "$ref": "#/definitions/maxItems"
                                },
                                "minItems": {
                                    "$ref": "#/definitions/minItems"
                                },
                                "uniqueItems": {
                                    "$ref": "#/definitions/uniqueItems"
                                },
                                "enum": {
                                    "$ref": "#/definitions/enum"
                                },
                                "multipleOf": {
                                    "$ref": "#/definitions/multipleOf"
                                }
                            }
                        },
                        "security": {
                            "type": "array",
                            "items": {
                                "$ref": "#/definitions/securityRequirement"
                            },
                            "uniqueItems": true
                        },
                        "securityRequirement": {
                            "type": "object",
                            "additionalProperties": {
                                "type": "array",
                                "items": {
                                    "type": "string"
                                },
                                "uniqueItems": true
                            }
                        },
                        "xml": {
                            "type": "object",
                            "additionalProperties": false,
                            "properties": {
                                "name": {
                                    "type": "string"
                                },
                                "namespace": {
                                    "type": "string"
                                },
                                "prefix": {
                                    "type": "string"
                                },
                                "attribute": {
                                    "type": "boolean",
                                    "default": false
                                },
                                "wrapped": {
                                    "type": "boolean",
                                    "default": false
                                }
                            }
                        },
                        "tag": {
                            "type": "object",
                            "additionalProperties": false,
                            "required": [
                                "name"
                            ],
                            "properties": {
                                "name": {
                                    "type": "string"
                                },
                                "description": {
                                    "type": "string"
                                },
                                "externalDocs": {
                                    "$ref": "#/definitions/externalDocs"
                                }
                            },
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            }
                        },
                        "securityDefinitions": {
                            "type": "object",
                            "additionalProperties": {
                                "oneOf": [
                                    {
                                        "$ref": "#/definitions/basicAuthenticationSecurity"
                                    },
                                    {
                                        "$ref": "#/definitions/apiKeySecurity"
                                    },
                                    {
                                        "$ref": "#/definitions/oauth2ImplicitSecurity"
                                    },
                                    {
                                        "$ref": "#/definitions/oauth2PasswordSecurity"
                                    },
                                    {
                                        "$ref": "#/definitions/oauth2ApplicationSecurity"
                                    },
                                    {
                                        "$ref": "#/definitions/oauth2AccessCodeSecurity"
                                    }
                                ]
                            }
                        },
                        "basicAuthenticationSecurity": {
                            "type": "object",
                            "additionalProperties": false,
                            "required": [
                                "type"
                            ],
                            "properties": {
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "basic"
                                    ]
                                },
                                "description": {
                                    "type": "string"
                                }
                            },
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            }
                        },
                        "apiKeySecurity": {
                            "type": "object",
                            "additionalProperties": false,
                            "required": [
                                "type",
                                "name",
                                "in"
                            ],
                            "properties": {
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "apiKey"
                                    ]
                                },
                                "name": {
                                    "type": "string"
                                },
                                "in": {
                                    "type": "string",
                                    "enum": [
                                        "header",
                                        "query"
                                    ]
                                },
                                "description": {
                                    "type": "string"
                                }
                            },
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            }
                        },
                        "oauth2ImplicitSecurity": {
                            "type": "object",
                            "additionalProperties": false,
                            "required": [
                                "type",
                                "flow",
                                "authorizationUrl"
                            ],
                            "properties": {
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "oauth2"
                                    ]
                                },
                                "flow": {
                                    "type": "string",
                                    "enum": [
                                        "implicit"
                                    ]
                                },
                                "scopes": {
                                    "$ref": "#/definitions/oauth2Scopes"
                                },
                                "authorizationUrl": {
                                    "type": "string",
                                    "format": "uri"
                                },
                                "description": {
                                    "type": "string"
                                }
                            },
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            }
                        },
                        "oauth2PasswordSecurity": {
                            "type": "object",
                            "additionalProperties": false,
                            "required": [
                                "type",
                                "flow",
                                "tokenUrl"
                            ],
                            "properties": {
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "oauth2"
                                    ]
                                },
                                "flow": {
                                    "type": "string",
                                    "enum": [
                                        "password"
                                    ]
                                },
                                "scopes": {
                                    "$ref": "#/definitions/oauth2Scopes"
                                },
                                "tokenUrl": {
                                    "type": "string",
                                    "format": "uri"
                                },
                                "description": {
                                    "type": "string"
                                }
                            },
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            }
                        },
                        "oauth2ApplicationSecurity": {
                            "type": "object",
                            "additionalProperties": false,
                            "required": [
                                "type",
                                "flow",
                                "tokenUrl"
                            ],
                            "properties": {
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "oauth2"
                                    ]
                                },
                                "flow": {
                                    "type": "string",
                                    "enum": [
                                        "application"
                                    ]
                                },
                                "scopes": {
                                    "$ref": "#/definitions/oauth2Scopes"
                                },
                                "tokenUrl": {
                                    "type": "string",
                                    "format": "uri"
                                },
                                "description": {
                                    "type": "string"
                                }
                            },
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            }
                        },
                        "oauth2AccessCodeSecurity": {
                            "type": "object",
                            "additionalProperties": false,
                            "required": [
                                "type",
                                "flow",
                                "authorizationUrl",
                                "tokenUrl"
                            ],
                            "properties": {
                                "type": {
                                    "type": "string",
                                    "enum": [
                                        "oauth2"
                                    ]
                                },
                                "flow": {
                                    "type": "string",
                                    "enum": [
                                        "accessCode"
                                    ]
                                },
                                "scopes": {
                                    "$ref": "#/definitions/oauth2Scopes"
                                },
                                "authorizationUrl": {
                                    "type": "string",
                                    "format": "uri"
                                },
                                "tokenUrl": {
                                    "type": "string",
                                    "format": "uri"
                                },
                                "description": {
                                    "type": "string"
                                }
                            },
                            "patternProperties": {
                                "^x-": {
                                    "$ref": "#/definitions/vendorExtension"
                                }
                            }
                        },
                        "oauth2Scopes": {
                            "type": "object",
                            "additionalProperties": {
                                "type": "string"
                            }
                        },
                        "mediaTypeList": {
                            "type": "array",
                            "items": {
                                "$ref": "#/definitions/mimeType"
                            },
                            "uniqueItems": true
                        },
                        "parametersList": {
                            "type": "array",
                            "description": "The parameters needed to send a valid API call.",
                            "additionalItems": false,
                            "items": {
                                "oneOf": [
                                    {
                                        "$ref": "#/definitions/parameter"
                                    },
                                    {
                                        "$ref": "#/definitions/jsonReference"
                                    }
                                ]
                            },
                            "uniqueItems": true
                        },
                        "schemesList": {
                            "type": "array",
                            "description": "The transfer protocol of the API.",
                            "items": {
                                "type": "string",
                                "enum": [
                                    "http",
                                    "https",
                                    "ws",
                                    "wss"
                                ]
                            },
                            "uniqueItems": true
                        },
                        "collectionFormat": {
                            "type": "string",
                            "enum": [
                                "csv",
                                "ssv",
                                "tsv",
                                "pipes"
                            ],
                            "default": "csv"
                        },
                        "collectionFormatWithMulti": {
                            "type": "string",
                            "enum": [
                                "csv",
                                "ssv",
                                "tsv",
                                "pipes",
                                "multi"
                            ],
                            "default": "csv"
                        },
                        "title": {
                            "$ref": "http://json-schema.org/draft-04/schema#/properties/title"
                        },
                        "description": {
                            "$ref": "http://json-schema.org/draft-04/schema#/properties/description"
                        },
                        "default": {
                            "$ref": "http://json-schema.org/draft-04/schema#/properties/default"
                        },
                        "multipleOf": {
                            "$ref": "http://json-schema.org/draft-04/schema#/properties/multipleOf"
                        },
                        "maximum": {
                            "$ref": "http://json-schema.org/draft-04/schema#/properties/maximum"
                        },
                        "exclusiveMaximum": {
                            "$ref": "http://json-schema.org/draft-04/schema#/properties/exclusiveMaximum"
                        },
                        "minimum": {
                            "$ref": "http://json-schema.org/draft-04/schema#/properties/minimum"
                        },
                        "exclusiveMinimum": {
                            "$ref": "http://json-schema.org/draft-04/schema#/properties/exclusiveMinimum"
                        },
                        "maxLength": {
                            "$ref": "http://json-schema.org/draft-04/schema#/definitions/positiveInteger"
                        },
                        "minLength": {
                            "$ref": "http://json-schema.org/draft-04/schema#/definitions/positiveIntegerDefault0"
                        },
                        "pattern": {
                            "$ref": "http://json-schema.org/draft-04/schema#/properties/pattern"
                        },
                        "maxItems": {
                            "$ref": "http://json-schema.org/draft-04/schema#/definitions/positiveInteger"
                        },
                        "minItems": {
                            "$ref": "http://json-schema.org/draft-04/schema#/definitions/positiveIntegerDefault0"
                        },
                        "uniqueItems": {
                            "$ref": "http://json-schema.org/draft-04/schema#/properties/uniqueItems"
                        },
                        "enum": {
                            "$ref": "http://json-schema.org/draft-04/schema#/properties/enum"
                        },
                        "jsonReference": {
                            "type": "object",
                            "additionalProperties": false,
                            "properties": {
                                "$ref": {
                                    "type": "string"
                                }
                            }
                        }
                    }
                };
                var validateFlag = true;
                var apis = {};
                var validator = new ZSchema({breakOnFirstError: false});
                var tempKey = [];
                var countHTTPMethod = 0;
                var valid = false;

                //if swagger version is 2.0, use swagger spec 2.0 validation schema
                if (validateData.swagger == '2.0' && typeof validateData.swaggerVersion == "undefined") {
                    valid = validator.validate(validateData, schemaV2);
                } else {
                    apis = validateData.apis || {};
                    valid = validator.validate(validateData, schemaV1);
                }

                var errors = validator.getLastErrors() ? validator.getLastErrors() : [];
                var MISSING_METHOD_HTTPMETHOD = "Missing required property: method, or httpMethod and method are mixed together."
                var errorArr = [];
                var mixFlag = false;

              for (var i = 0; i < apis.length; i++) {
                    var api = apis[i];
                    for (var j = 0; j < api.operations.length; j++) {
                        var operation = api.operations[j];
                        for (var methodKey in operation) {
                            if (methodKey == "method" || methodKey == "httpMethod") {
                                tempKey.push(methodKey);
                            }
                            if (methodKey == "httpMethod") {
                                countHTTPMethod++;
                            }
                        }
                    }
                }
                if (countHTTPMethod <= tempKey.length && countHTTPMethod != 0) {
                   for(var i = errors.length -1; i >= 0 ; i--){
                        if (errors[i].params && errors[i].params.length && errors[i].params[0] == "httpMethod") {
                            if (!mixFlag) {
                                errors[i].message = MISSING_METHOD_HTTPMETHOD;
                                errorArr.push(errors[i].message);
                                mixFlag = true;
								
                            } else {
                                errors.splice(i, 1);
							
                            }
                        } else {
                            errorArr.push(errors[i].message);
							
                        }
                    }
                }
				
				if (countHTTPMethod == 0) {
					for(var i = errors.length -1; i >= 0 ; i--){
					if (errors[i].params && errors[i].params.length && errors[i].params[0] == "httpMethod") {
						errors.splice(i, 1);
					}
					else {
                            errorArr.push(errors[i].message);
                        }
					}
					}
	
				
                if (errorArr.length > 0) {
                    Joomla.showError(errorArr);
                    validateFlag = false;
                } else {
                    validateFlag = true;
                }
                return validateFlag;
            }


            /**
             *if swagger spec version is 2.0, need to collect a apis object.
             *
             */
            function collectAPIS(data) {
                var apisArr = [];
                for (var key1 in data.paths) {
                    for (var key2 in data.paths[key1]) {
                        data.paths[key1][key2]["method"] = key2;
                        data.paths[key1][key2]["nickname"] = data.paths[key1][key2]["operationId"];
                        apisArr.push({path: key1, operations: [data.paths[key1][key2]]});
                    }
                }
                return apisArr;
            }

            Joomla.beforesubmitform = function (fCallback, fErrorback) {
                wsdlName = $('input[name="jform[fields][127][]"]').val();
                fileName = $('input[name="jform[fields][23][]"]').val();
                realName = jQuery('#fld-23').find('li.mooupload_readonly div.filename').text();
                operations_doc2 = $('#fld-23 .filename').eq(1).text();
                var flag = true,
                    record_id = parseInt($("#jform_id").val()),
                    currentName = $('input[name="jform[title]"]').val(),
                    currentEnvironments = getEnvironments(),
                    currentTargetEnvironments = getTargetEnvironments(),
                    currentRequestPreprocess = getRequestPreprocess(),
                    currentRequestTransform = getRequestTransform(),
                    currentResponseTransform = getResponseTransform(),
                    currentFaultTransform = getFaultTransform(),
                    currentCreateProxy = $('input:checked[name="jform[fields][145]"]').val(),
                    sNewResourcePath = $('input[name="jform[fields][22]"]').val(),
                    sNewAPIType = $('select[name="jform[fields][75]"]').val();

                if (jQuery('[name="jform[fields][206][]"]').length != 0) {
                      jQuery('[name="requestPreprocess"]').val(jQuery('[name="jform[fields][206][]"]').val());
                }

                if (jQuery('[name="jform[fields][228][]"]').length != 0) {
                    jQuery('[name="jform[fields][228][]"]').each(function(){
                        jQuery('[name="requestTransform"]').val(jQuery('[name="jform[fields][228][]"]').val());
                    });
                }

                if (jQuery('[name="jform[fields][229][]"]').length != 0) {
                    jQuery('[name="jform[fields][229][]"]').each(function(){
                        jQuery('[name="responseTransform"]').val(jQuery('[name="jform[fields][229][]"]').val());
                    });
                }

                if (jQuery('[name="jform[fields][230][]"]').length != 0) {
                    jQuery('[name="jform[fields][230][]"]').each(function(){
                        jQuery('[name="faultTransform"]').val(jQuery('[name="jform[fields][230][]"]').val());
                    });
                }

                if (sRecourcePath !== sNewResourcePath || sAPIType !== sNewAPIType) {
                    window.oUpdatedFields = window.oUpdatedFields || {};
                    if (sRecourcePath !== sNewResourcePath) {
                        window.oUpdatedFields[22] = [sRecourcePath];
                    }
                    if (sAPIType !== sNewAPIType) {
                        window.oUpdatedFields[75] = [sAPIType];
                    }
                }
                if (currentName !== originalName) {
                    window.oUpdatedFields = window.oUpdatedFields || {};
                    window.oUpdatedFields['name'] = originalName;
                }
                if (!DeveloperPortal.arrayEqual(originalTargetEnvironments, currentTargetEnvironments)) {
                    window.oUpdatedFields = window.oUpdatedFields || {};
                    window.oUpdatedFields[147] = originalTargetEnvironments;
                }

                if(!DeveloperPortal.arrayEqual(originalRequestPreprocess, currentRequestPreprocess)) {
                    window.oUpdatedFields = window.oUpdatedFields || {};
                    window.oUpdatedFields[206] = originalRequestPreprocess;
                }

                if(!DeveloperPortal.arrayEqual(originalRequestTransform, currentRequestTransform)) {
                    window.oUpdatedFields = window.oUpdatedFields || {};
                    window.oUpdatedFields[228] = originalRequestTransform;
                }

                if(!DeveloperPortal.arrayEqual(originalResponseTransform, currentResponseTransform)) {
                    window.oUpdatedFields = window.oUpdatedFields || {};
                    window.oUpdatedFields[229] = originalResponseTransform;
                }

                if(!DeveloperPortal.arrayEqual(originalFaultTransform, currentFaultTransform)) {
                    window.oUpdatedFields = window.oUpdatedFields || {};
                    window.oUpdatedFields[230] = originalFaultTransform;
                }

                if (originalCreateProxy !== currentCreateProxy) {
                    window.oUpdatedFields = window.oUpdatedFields || {};
                    window.oUpdatedFields[145] = [originalCreateProxy];
                }
                if (record_id && !DeveloperPortal.arrayEqual(originalEnvironments, currentEnvironments)) {
                    var data = {
                        "option": "com_cobalt",
                        "task": "ajaxMore.checkEnvironmentsUsedByProduct",
                        "origEnvs": originalEnvironments.join(),
                        "currEnvs": currentEnvironments.join(),
                        "record_id": record_id
                    };

                    $.ajax({
                        url: '',
                        data: data,
                        type: 'post',
                        async: false,
                        dataType: 'json'
                    }).done(function (res) {
                        if (!res.success) {
                            flag = false;
                            fErrorback(res.error);
                        }
                    }).fail(function () {
                        flag = false;
                        fErrorback();
                    });
                }


                if (flag) {
                    if (wsdlName) {
                        if (originSpecName !== "" && wsdlName !== originSpecName && wsdlName !== undefined) {
                            var oldSpecPath = GLOBAL_CONTEXT_PATH + "<?php echo $this->appParams->get('general_upload',1).'/'.$this->fields[23]->params->get('params',1)->subfolder.'/'.$this->fields[23]->value[0]['fullpath'];?>";
                            $.post(
                                GLOBAL_CONTEXT_PATH + "index.php?option=com_cobalt&task=ajaxMore.soapClient",
                                {'filename': wsdlName, 'apiID': '<?php echo $this->item->id;?>'},
                                function (data) {
                                    if (data.success) {
                                        specData = data.result;
                                        createOperations(specData.apis, nObjectId, sRedirectUrl, 'wsdl');
                                    } else {
                                        DeveloperPortal.storeErrMsgInCookie(["<?php echo JText::_('CREATE_PRODUCT_UPLOAD_SPEC_ERROR')?>"]);
                                        window.location.href = GLOBAL_CONTEXT_PATH + 'index.php/apis/edit/' + nObjectId;
                                    }
                                },
                                'json'
                            ).error(function () {
                                    DeveloperPortal.storeErrMsgInCookie(["<?php echo JText::_('CREATE_PRODUCT_UPLOAD_SPEC_ERROR')?>"]);
                                    window.location.href = GLOBAL_CONTEXT_PATH + 'index.php/apis/edit/' + nObjectId;
                                });
                        } else {
                            fCallback();
                        }
                    } else if (fileName) {
                        if (originSpecName !== "" && realName !== originSpecName) {
                            var oldSpecPath = GLOBAL_CONTEXT_PATH + "<?php echo $this->appParams->get('general_upload',1).'/'.$this->fields[23]->params->get('params',1)->subfolder.'/'.$this->fields[23]->value[0]['fullpath'];?>";
                            if (operationsCount > 0 && confirm("<?php echo JText::_('REMOVE_ALL_OPERATION')?>")) {
                                $.post(
                                    GLOBAL_CONTEXT_PATH + "index.php?option=com_cobalt&task=ajaxMore.deleteOperationsAPI",
                                    {'apiID': '<?php echo $this->item->id;?>'},
                                    function (data) {
                                        if (data.success) {
                                            deleted_operation_ids = data.result;
                                            is_deleted_operations = true;
                                            fCallback();
                                        } else {
                                            realName = "";
                                            Joomla.showError(["Can't auto created operations now."]);
                                        }
                                    },
                                    'json'
                                ).fail(function () {
                                        var rootPath = GLOBAL_CONTEXT_PATH + "<?php echo $this->appParams->get('general_upload',1).'/'.$this->fields[23]->params->get('params',1)->subfolder.'/'.$folder_format.'/';?>";
                                        $.post(
                                            rootPath + fileName,
                                            {},
                                            function (data) {
                                                //if (validateSwagger(data) == true) {
                                                    fCallback();
                                               // }
                                            },
                                            'json'
                                        ).error(function () {
                                                Joomla.showError(["<?php echo JText::_('CREATE_PRODUCT_UPLOAD_SPEC_ERROR')?>"]);
                                            });

                                    });
                            } else {
                                fCallback();
                            }


                        } else if (operationsCount > 0 && originSpecName == "" && realName !== originSpecName) {
                            if (confirm("<?php echo JText::_('REMOVE_ALL_OPERATION')?>")) {
                                $.post(
                                    GLOBAL_CONTEXT_PATH + "index.php?option=com_cobalt&task=ajaxMore.deleteOperationsAPI",
                                    {'apiID': '<?php echo $this->item->id;?>'},
                                    function (data) {
                                        if (data.success) {
                                            deleted_operation_ids = data.result;
                                            is_deleted_operations = true;
                                            fCallback();
                                        } else {
                                            realName = "";
                                            Joomla.showError(["Can't auto created operations now."]);
                                        }
                                    },
                                    'json'
                                ).fail(function () {
                                        var rootPath = GLOBAL_CONTEXT_PATH + "<?php echo $this->appParams->get('general_upload',1).'/'.$this->fields[23]->params->get('params',1)->subfolder.'/'.$folder_format.'/';?>";
                                        $.post(
                                            rootPath + fileName,
                                            {},
                                            function (data) {
                                              //  if (validateSwagger(data) == true) {
                                                    fCallback();
                                               // }
                                            },
                                            'json'
                                        ).error(function () {
                                                Joomla.showError(["<?php echo JText::_('CREATE_PRODUCT_UPLOAD_SPEC_ERROR')?>"]);
                                            });

                                    });
                            } else {
                                fCallback();
                            }
                        } else {

                            var rootPath = GLOBAL_CONTEXT_PATH + "<?php echo $this->appParams->get('general_upload',1).'/'.$this->fields[23]->params->get('params',1)->subfolder.'/'.$folder_format.'/';?>";
                            $.post(
                                rootPath + fileName,
                                {},
                                function (data) {
                                   // if (validateSwagger(data) == true) {
                                        fCallback();
                                   // }
                                },
                                'json'
                            ).error(function () {
                                    Joomla.showError(["<?php echo JText::_('CREATE_PRODUCT_UPLOAD_SPEC_ERROR')?>"]);
                                });
                        }
                    } else {

                        if ((typeof originSpecName == 'undefined' || typeof fileName == 'undefined') && realName !== originSpecName) {
                            var oldSpecPath = GLOBAL_CONTEXT_PATH + "<?php echo $this->appParams->get('general_upload',1).'/'.$this->fields[23]->params->get('params',1)->subfolder.'/'.$this->fields[23]->value[0]['fullpath'];?>";
                            if (operationsCount > 0 && confirm("<?php echo JText::_('REMOVE_ALL_OPERATION')?>")) {
                                $.post(
                                    GLOBAL_CONTEXT_PATH + "index.php?option=com_cobalt&task=ajaxMore.deleteOperationsAPI",
                                    {'apiID': '<?php echo $this->item->id;?>'},
                                    function (data) {
                                        if (data.success) {
                                            deleted_operation_ids = data.result;
                                            is_deleted_operations = true;
                                            fCallback();
                                        } else {
                                            realName = "";
                                            Joomla.showError(["Can't auto created operations now."]);
                                        }
                                    },
                                    'json'
                                ).fail(function () {
                                        fCallback();
                                    });
                            } else {
                            fCallback();
                            }
                        } else {
                        fCallback();
                        }

                    }
                }

            };

            //operations auto-create
            Joomla.submitform = function (task) {
                DeveloperPortal.submitForm(task,
                    function (nObjectId, sRedirectUrl) {
                        if (wsdlName !== undefined && wsdlName.length > 0 && wsdlName !== originSpecName) {
                            $.post(
                                GLOBAL_CONTEXT_PATH + "index.php?option=com_cobalt&task=ajaxMore.soapClient",
                                {'filename': wsdlName, 'apiID': '<?php echo $this->item->id;?>'},
                                function (data) {
                                    if (data.success) {
                                        specData = data.result;
                                        createOperations(specData.apis, nObjectId, sRedirectUrl, 'wsdl');
                                    } else {
                                        DeveloperPortal.storeErrMsgInCookie(["<?php echo JText::_('CREATE_PRODUCT_UPLOAD_SPEC_ERROR')?>"]);
                                        window.location.href = GLOBAL_CONTEXT_PATH + 'index.php/apis/edit/' + nObjectId;
                                    }
                                },
                                'json'
                            ).error(function () {
                                    DeveloperPortal.storeErrMsgInCookie(["<?php echo JText::_('SPEC_FORMAT_INVALID')?>"]);
                                    window.location.href = GLOBAL_CONTEXT_PATH + 'index.php/apis/edit/' + nObjectId;
                                });
                        } else if (fileName !== undefined && fileName.length > 0 && realName !== originSpecName) {
                            var rootPath = GLOBAL_CONTEXT_PATH + "<?php echo $this->appParams->get('general_upload',1).'/'.$this->fields[23]->params->get('params',1)->subfolder.'/'.$folder_format.'/';?>";
                            $.post(
                                rootPath + fileName,
                                {},
                                function (data) {
                                    if (data.swagger == '2.0' && typeof data.swaggerVersion == "undefined") {
                                        data.apis = collectAPIS(data);
                                        specData = data;
                                    } else {
                                        specData = (data && data.apis) ? data : {};
                                    }
                                    if ($('input[name="jform[fields][22]"]').val() === '' && specData.resourcePath.length > 0) {
                                        $("#adminForm").append('<input type="hidden" name="jform[fields][22]" value="' + specData.resourcePath + '" />');
                                    }
                                    createOperations(specData.apis, nObjectId, sRedirectUrl, 'json');

                                },
                                'json'
                            ).error(function () {
                                    DeveloperPortal.storeErrMsgInCookie(["<?php echo JText::_('CREATE_PRODUCT_UPLOAD_SPEC_ERROR')?>"]);
                                    window.location.href = GLOBAL_CONTEXT_PATH + 'index.php/apis/edit/' + nObjectId;
                                });
                        } else {
                            if (is_deleted_operations) {
                                DeveloperPortal.sendUpdateNotification(nObjectId, DeveloperPortal.PORTAL_OBJECT_TYPE_API, {'31': []},
                                    function () {
                                        window.location.href = sRedirectUrl;
                                    }, function () {
                                        window.location.href = sRedirectUrl;
                                    });
                            } else {
                                window.location.href = sRedirectUrl;
                            }

                            if(originSpecFileName !== undefined && fileName === undefined){
                            DeveloperPortal.sendDeleteNotification(nObjectId, DeveloperPortal.PORTAL_OBJECT_TYPE_API,
                                function () {
                                    window.location.href = sRedirectUrl;
                                    }, function () {
                                    window.location.href = sRedirectUrl;
                                    });
                        }
                        
                        }
                        
                    },
                    function (sRedirectUrl) {
                        window.location.href = sRedirectUrl;
                    }
                );
            };

            function createOperations(apis, API_ID, redirectURL, type) {
                var counter = 0;
                var requestPreprocess = jQuery('[name="requestPreprocess"]').val();
                var requestTransform = jQuery('[name="requestTransform"]').val();
                var responseTransform = jQuery('[name="responseTransform"]').val();
                var faultTransform = jQuery('[name="faultTransform"]').val();

                try {
                    for (var i = 0; i < apis.length; i++) {
                        var api = apis[i];
                        var subcounter = 0;
                        for (var j = 0; j < api.operations.length; j++) {
                            var operation = api.operations[j];
                            var title = operation.nickname;
                            var sFormId = 'keyForm_' + i + j,
                                dForm = $('<form id="' + sFormId + '" name="' + sFormId + '" enctype="multipart/form-data" method="post" style="display:none;"></form>').appendTo('body'),
                                sAction = GLOBAL_CONTEXT_PATH + 'index.php/apis/submit/2-apis/6?fand=' + API_ID + '&field_id=30',
                                restPath = api.path,
                                method = operation.method,
                                description = api.description,
                                tokenInput = $('input[value="1"]')[0],
                                sIFrameId = 'iframe_submission_' + i + j,
                                dIFrame = $('<iframe id="' + sIFrameId + '" name="' + sIFrameId + '"  style="display:none;" />').appendTo('body'),
                                dWindow, sRedirectUrl;

                            if (operation.summary != null && operation.summary.length > 0) {
                                description += ':' + operation.summary;
                            }
                            if (operation.httpMethod && !method) {
                                method = operation.httpMethod;
                            }
                            dForm.attr('action', sAction);
                            dForm.attr('target', sIFrameId);
                            var inputStr = '<input type="hidden" name="jform[fields][29]" value="' + method + '" />';
                            if (type == 'wsdl') {
                                inputStr += '<input type="hidden" name="jform[fields][128]" value="' + restPath + '" />';
                            }
                            if (requestPreprocess !== "") {
                                inputStr += '<input type="hidden" name="jform[fields][213]" value="False" />'+
                                '<input type="hidden" name="jform[fields][210][]" value="' +  requestPreprocess + '" />';
                            }
                            if (requestTransform !== "") {
                                inputStr += '<input type="hidden" name="jform[fields][234]" value="False" />'+
                                    '<input type="hidden" name="jform[fields][231][]" value="' +  requestTransform + '" />';
                            }
                            if (responseTransform !== "") {
                                inputStr += '<input type="hidden" name="jform[fields][235]" value="False" />'+
                                    '<input type="hidden" name="jform[fields][232][]" value="' +  responseTransform + '" />';

                            }
                            if (faultTransform !== "") {
                                inputStr += '<input type="hidden" name="jform[fields][236]" value="False" />'+
                                    '<input type="hidden" name="jform[fields][233][]" value="' +  faultTransform + '" />';
                            }
                            dForm.append(
                                '<input type="hidden" name="task" value="form.save" />' +
                                '<input type="hidden" name="' + tokenInput.name + '" value="1" />' +
                                '<input type="hidden" name="jform[title]" value="' + title + '" />' +
                                '<input type="hidden" name="jform[fields][27]" value="' + description + '" />' +
                                '<input type="hidden" name="jform[fields][28]" value="' + restPath + '" />' +
                                inputStr +
                                '<input type="hidden" name="jform[fields][30]" value="' + API_ID + '" />' +
                                '<input type="hidden" name="jform[fields][149]" value="' + restPath + '" />' +
                                '<input type="hidden" name="jform[fields][108]" value="' + UUID.generate() + '" />' +
                                '<input type="hidden" name="jform[ucatid]" value="0" />' +
                                '<input type="hidden" name="jform[id]" value="0" />' +
                                '<input type="hidden" name="jform[section_id]" value="2" />' +
                                '<input type="hidden" name="jform[type_id]" value="6" />' +
                                '<input type="hidden" name="jform[published]" value="1" />'
                            );

                            dIFrame.on('load', function (oEvent) {
                                dWindow = dIFrame[0].contentWindow;
                                sRedirectUrl = dWindow.location.href;

                                if (dWindow.location.href == window.location.href) {
                                    var sErrMsg = 'Operation:' + ' of API ' + API_ID + ' is not successfully stored in the database.';
                                    DeveloperPortal.storeErrMsgInCookie(GENERIC_ERROR_MESSAGE);
                                    if (typeof fErrorback === 'function') {
                                        fErrorback([sErrMsg, GENERIC_ERROR_MESSAGE].join('<br />'));
                                    }
                                }

                                subcounter++;
                                if (subcounter == api.operations.length) {
                                    counter++;
                                    subcounter = 0;
                                }

                                if (counter == apis.length) {
                                    DeveloperPortal.sendUpdateNotification(API_ID, DeveloperPortal.PORTAL_OBJECT_TYPE_API, {'31': []},
                                        function () {
                                            window.location.href = redirectURL;
                                        }, function () {
                                            window.location.href = redirectURL;
                                        });
                                }
                            });

                            dForm.submit();
                        }


                    }
                } catch (e) {
                    DeveloperPortal.storeErrMsgInCookie(["<?php echo JText::_('CREATE_PRODUCT_UPLOAD_SPEC_ERROR')?>"]);
                    window.location.href = GLOBAL_CONTEXT_PATH + 'index.php/apis/edit/' + API_ID;
                }
            }
        }(jQuery));

        function scrollToAttach() {
            jQuery('html, body').animate({
                scrollTop: jQuery("#lbl-23").offset().top
            }, 300);
        }

    </script>


<?php
function group_start($data, $label, $name)
{
    static $start = false;
    switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0)) {
        //tab
        case 4:
        case 1:
            if (!$start) {
                echo '<div class="tab-content" id="tabs-box">';
                $start = TRUE;
            }
            echo '<div class="tab-pane" id="' . $name . '">';
            break;
        //slider
        case 2:
            if (!$start) {
                echo '<div class="accordion" id="accordion2">';
                $start = TRUE;
            }
            echo '<div class="accordion-group">
        <div class="accordion-heading">
          <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#' . $name . '">
               ' . $label . '
          </a>
        </div>
        <div id="' . $name . '" class="accordion-body collapse">
          <div class="accordion-inner">';
            break;
        // fieldset
        case 3:
            if ($name != 'tab-main') {
                echo "<legend>{$label}</legend>";
            }
            break;
    }
}

function group_end($data)
{
    switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0)) {
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
    switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0)) {
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
