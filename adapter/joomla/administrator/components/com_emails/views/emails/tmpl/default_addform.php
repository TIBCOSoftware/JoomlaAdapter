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
?>
<div class="accordion" id="accordion1">
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#batch">
				<?php echo JText::_('COM_EMAILS_FIELD_NEW_URL_LABEL');?>
			</a>
		</div>
		<div id="batch" class="accordion-body collapse">
			<div class="accordion-inner">
				<fieldset class="batch form-inline">
					<div class="control-group">
						<label for="alias" class="control-label"><?php echo JText::_('COM_EMAILS_FIELD_NEW_URL_LABEL'); ?></label>
						<div class="controls">
							<input type="text" name="alias" id="alias" value="" size="50" title="<?php echo JText::_('COM_EMAILS_FIELD_NEW_URL_DESC'); ?>" />
						</div>
					</div>
					<div class="control-group">
						<label for="comment" class="control-label"><?php echo JText::_('COM_EMAILS_FIELD_COMMENT_LABEL'); ?></label>
						<div class="controls">
							<input type="text" name="comment" id="comment" value="" size="50" title="<?php echo JText::_('COM_EMAILS_FIELD_COMMENT_DESC'); ?>" />
						</div>
					</div>
					<button class="btn btn-primary" type="button" onclick="this.form.task.value='emails.activate';this.form.submit();"><?php echo JText::_('COM_EMAILS_BUTTON_UPDATE_LINKS'); ?></button>
				</fieldset>
			</div>
		</div>
	</div>
</div>
