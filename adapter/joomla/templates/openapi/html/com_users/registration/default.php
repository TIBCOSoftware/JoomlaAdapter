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

?>

<style type="text/css">
    #member-registration legend {
        border-bottom: none;
    }

    #jform_apiuser_validate,
    img {
        display: inline-block;
    }

    #jform_apiuser_validate {
        width: 100px;
    }

    .form-actions {
        background: transparent;
        border: none;
        padding: 0;
    }

    #member-registration #jform_apiuser_first_name,
    #member-registration #jform_apiuser_last_name,
    #member-registration #jform_apiuser_state,
    #member-registration #jform_apiuser_zipcode {
        width: 200px;
    }

    #member-registration .controls {
        margin-left: 20px;
    }

    #member-registration .first-name {
        width: 200px;
    }

    #member-registration #jform_apiuser_user_email {
        width: 420px;
    }

    #member-registration #jform_apiuser_to_agree {
        margin-right: 5px;
        margin-top: -1px
    }

    .form-actions {
        width: 160px;
        position: relative;
        top: -100px;
        left: 300px;
    }

    .term {
        width: 300px;
        font-size: 12px;
    }

    #member-registration {
        width: 500px;
        margin: 0 auto;
    }
</style>

<div class="registration<?php echo $this->pageclass_sfx ?>">
    <form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
        <?php $fields = $this->form->getFieldset('apiuser'); ?>
        <?php $terms = JUri::root() . "index.php/2-uncategorised/8-terms-conditions"; ?>
        <?php $html_segments = array(); ?>
        <?php if (count($fields)): ?>
            <fieldset aria-invalid="false">
                <legend><?php echo JText::_("PLG_USER_APIUSER_FORM_TITLE"); ?></legend>
                <?php foreach ($fields as $field) :// Iterate through the fields in the set and display them.?>
                    <?php if ($field->hidden):// If the field is hidden, just display the input.?>
                        <?php echo $field->input; ?>
                    <?php else: ?>
                        <!-- In order to put the first name and the last name on the same line, only render the output of these two fields in the loop.-->
                        <!-- Other fields' output will be stored in an array and be rendered after the loop. -->
                        <?php if(substr_count($field->name, "first-name") > 0): ?>
                            <div class="control-group row-fluid">
                                <div class="control span4 first-name">
                                    <?php echo $field->label; ?>
                                    <?php echo $field->input; ?>
                                </div>
                        <?php elseif(substr_count($field->name, "last-name") > 0): ?>
                                <div class="controls span5">
                                    <?php echo $field->label; ?>
                                    <?php echo $field->input; ?>
                                </div>
                            </div>
                        <?php elseif(substr_count($field->name, "to-agree") > 0): ?>
                            <?php
                                array_push($html_segments, '<div class="control-group row-fluid"><span>');
                                array_push($html_segments, JText::_("PLG_USER_APIUSER_FIELD_REQUIRED_TIP"));
                                array_push($html_segments, '</span>');
                                array_push($html_segments, '</div><div class="control-group row-fluid term"><div class="control">');
                                array_push($html_segments, '<label id="jform_apiuser_to_agree-lbl" class="required validate-is_agree" style="margin-right: 5px;" for="jform_apiuser_to_agree">');
                                array_push($html_segments, $field->input);
                                array_push($html_segments, JText::_("PLG_USER_APIUSER_FIELD_TO_AGREE_LABEL_PART1"));
                                array_push($html_segments, '<a href="');
                                array_push($html_segments, $terms);
                                array_push($html_segments, '" target="_blank">');
                                array_push($html_segments, JText::_("PLG_USER_APIUSER_FIELD_TO_AGREE_LINK_TEXT"));
                                array_push($html_segments, '</a>');
                                array_push($html_segments, JText::_("PLG_USER_APIUSER_FIELD_TO_AGREE_LABEL_PART2"));
                                array_push($html_segments, '</label></div></div>');
                            ?>
                        <?php else: ?>
                            <?php
                                array_push($html_segments, '<div class="control-group row-fluid"><div class="control span4 email">');
                                array_push($html_segments, $field->label);
                                array_push($html_segments, $field->input);
                                array_push($html_segments, '</div></div>');
                            ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php echo implode('', $html_segments); ?>
            </fieldset>
        <?php endif; ?>
        <br/>
        <div class="form-actions">
            <button id=submitButton type="submit"
                    class="btn btn-primary validate"><?php echo JText::_('JREGISTER'); ?></button>
            <input type="hidden" name="option" value="com_users"/>
            <input type="hidden" name="task" value="autoreg.register"/>
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>
</div>

<script type="text/javascript">

    window.addEvent('domready', function () {
        document.formvalidator.setHandler('is_agree', function (value) {
            return jQuery("#jform_apiuser_to_agree").get(0).checked;
        });
        jQuery('#code').click();
    });

</script>