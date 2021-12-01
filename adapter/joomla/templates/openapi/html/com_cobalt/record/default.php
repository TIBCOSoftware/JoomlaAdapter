<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
?>
<?php

defined('_JEXEC') or die();
?>
<script type="text/javascript">
    var RecordTemplate = {
        nRecordId: <?php echo $this->item->id; ?>,
        sPromptMsg: '<?php echo JText::_("CCONFIRMDELET_1"); ?>'
    };
</script>

<div class="contentpaneopen">
	<?php echo $this->loadTemplate('record_'.$this->type->params->get('properties.tmpl_article', 'default'));?>

	<div id="comments"><?php echo $this->loadTemplate('comments');?></div>
</div>