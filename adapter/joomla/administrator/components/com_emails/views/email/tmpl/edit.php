<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_emails
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'email.cancel' || document.formvalidator.isValid(document.id('email-form')))
		{
			Joomla.submitform(task, document.getElementById('email-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_emails&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="email-form" class="form-validate form-horizontal">
	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#basic" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EMAILS_NEW_LINK') : JText::sprintf('COM_EMAILS_EDIT_LINK', $this->item->id); ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="basic">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('subject'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('subject'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('isHTML'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('isHTML'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('content'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('content'); ?></div>
				</div>
			</div>
		</div>
		<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('created'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('last_modified'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('pulished'); ?></div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</form>
