<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$template_name = JFactory::getApplication()->getTemplate('template')->template;
$policy_data = str_replace($this->value, "", "\n");
?>
<link href="<?php echo JUri::root(); ?>templates/<?php echo $template_name ?>/css/policy_viewer.css" type="text/css" rel="stylesheet"/>
<script type="text/javascript" src="<?php echo JUri::root(); ?>components/com_cobalt/fields/textarea/tmpl/output/js/event_emitter.js"></script>
<script type="text/javascript" src="<?php echo JUri::root(); ?>components/com_cobalt/fields/textarea/tmpl/output/js/policy_viewer.js"></script>
<a class="btn btn-small"
   onclick="policyViewer.oUI.open('overview');"><?php echo JText::_("SHOW_POLICY_VIEWER_BUTTON"); ?></a>
<span id="no_policies_applied" <?php if(strlen($policy_data) > 0) { echo ' style="display: none;"'; } ?>><?php echo JText::_('NO_POLICIES_APPLIED'); ?></span>
<span id="policy_types_icons" <?php if(empty($this->value)) { echo ' style="display: none;"'; } ?>></span>
<script type="text/javascript">

    jQuery(function () {

        var aPolicyData;

        try {

            aPolicyData = JSON.parse('<?php echo $policy_data; ?>');

        } catch (e) {

            aPolicyData = [];
        }
        policyViewer.initialize(aPolicyData);

    });

</script>
