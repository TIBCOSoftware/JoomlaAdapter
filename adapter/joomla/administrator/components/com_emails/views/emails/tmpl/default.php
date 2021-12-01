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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo JRoute::_('index.php?option=com_emails&view=emails'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_EMAILS_SEARCH_LINKS'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_EMAILS_SEARCH_LINKS'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="clearfix"> </div>
		<table class="table table-striped">
			<thead>
				<tr>
					<th width="20" class="center">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="4%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
					<th class="title">
						<?php echo JHtml::_('grid.sort', 'COM_EMAILS_HEADING_SUBJECT', 'a.subject', $listDirn, $listOrder); ?>
					</th>
					<th width="15%" class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_EMAILS_HEADING_ALIAS', 'a.alias', $listDirn, $listOrder); ?>
					</th>
					<th width="45%" class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_EMAILS_HEADING_CONTENT', 'a.content', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_EMAILS_HEADING_CREATED_DATE', 'a.create_time', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				$canCreate = $user->authorise('core.create',     'com_emails');
				$canEdit   = $user->authorise('core.edit',       'com_emails');
				$canChange = $user->authorise('core.edit.state', 'com_emails');
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
					<td>
						<?php echo JHtml::_('emails.created_by', $item->created_by, $i); ?>
						<?php if ($canEdit) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_emails&task=email.edit&id='.$item->id);?>" title="<?php echo $this->escape($item->subject); ?>">
								<?php echo $this->escape(str_replace(JURI::root(), '', $item->subject)); ?></a>
						<?php else : ?>
								<?php echo $this->escape(str_replace(JURI::root(), '', $item->subject)); ?>
						<?php endif; ?>
					</td>
					<td class="small">
						<?php echo $this->escape($item->alias); ?>
					</td>
					<td class="small">
						<?php echo $this->escape($item->content); ?>
					</td>
					<td class="small">
						<?php echo JHtml::_('date', $item->create_time, JText::_('DATE_FORMAT_LC4')); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
