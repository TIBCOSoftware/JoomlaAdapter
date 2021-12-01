<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_logs
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

<form action="<?php echo JRoute::_('index.php?option=com_logs&view=logs'); ?>" method="post" name="adminForm" id="adminForm">
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
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_LOGS_SEARCH_LINKS'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_LOGS_SEARCH_LINKS'); ?>" />
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
						<?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
          <th class="center">
            <?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_UUID', 'a.uuid', $listDirn, $listOrder); ?>
          </th>
					<th width="4%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_UID', 'a.uid', $listDirn, $listOrder); ?>
					</th>
					<?php if($this->params->get('log_type', 1)): ?>
					<th class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_TYPE', 'a.log_type', $listDirn, $listOrder); ?>
					</th>
					<?php endif;?>
					<?php if($this->params->get('http_status', 1)): ?>
					<th class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_HTTP_STATUS', 'a.http_status', $listDirn, $listOrder); ?>
					</th>
					<?php endif;?>

					<?php if($this->params->get('http_status_text', 1)): ?>
					<th class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_HTTP_STATUS_TEXT', 'a.http_status_text', $listDirn, $listOrder); ?>
					</th>
					<?php endif;?>

					<?php if($this->params->get('entity_type', 1)): ?>
					<th class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_ENTITY_TYPE', 'a.entity_type', $listDirn, $listOrder); ?>
					</th>
					<?php endif;?>

					<?php if($this->params->get('entity_id', 1)): ?>
					<th class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_ENTITY_ID', 'a.entity_id', $listDirn, $listOrder); ?>
					</th>
					<?php endif;?>


					<?php if($this->params->get('event', 1)): ?>
					<th class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_EVENT', 'a.event', $listDirn, $listOrder); ?>
					</th>
					<?php endif;?>

					<?php if($this->params->get('event_status', 1)): ?>
					<th class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_EVENT_STATUS', 'a.event_status', $listDirn, $listOrder); ?>
					</th>
					<?php endif;?>

          <th class="nowrap">
            <?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_SUMMARY', 'a.summary', $listDirn, $listOrder); ?>
          </th>

					<th class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_LOGS_HEADING_CREATED_DATE', 'a.create_time', $listDirn, $listOrder); ?>
					</th>


			</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				$canCreate = $user->authorise('core.create',     'com_logs');
				$canEdit   = $user->authorise('core.edit',       'com_logs');
				$canChange = $user->authorise('core.edit.state', 'com_logs');
				?>
				<td class="center">
				  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="center">
				 <?php echo $item->id; ?>
				</td>
        <td class="small">
          <?php echo $item->uuid; ?>
        </td>
				<?php if($this->params->get('log_type', 1)): ?>
				<td class="small">
				 <?php	
				 $string = $item->uid;

				 echo $string;
				 if($item->uid){
				 	echo "<br/>(".JFactory::getUser($item->uid)->username.")";
				 }

				 ?>
				</td>
				<?php endif;?>
				<?php if($this->params->get('log_type', 1)): ?>
				<td class="small">
          <?php if ($canEdit) : ?>
				  <a href="<?php echo JRoute::_('index.php?option=com_logs&task=log.edit&id='.$item->id);?>" title="<?php echo $this->escape($item->subject); ?>"><?php echo $this->escape(str_replace(JURI::root(), '', $item->log_type)); ?></a>
				  
				 <?php else : ?>
				    <?php echo $this->escape(str_replace(JURI::root(), '', $item->log_type)); ?>
				 <?php endif; ?>
				</td>
				<?php endif;?>

				<?php if($this->params->get('http_status', 1)): ?>
				<td class="small">
				  <?php echo $this->escape($item->http_status); ?>
				</td>
				<?php endif;?>

				<?php if($this->params->get('http_status_text', 1)): ?>
				<td class="small">
				 <?php echo $this->escape($item->http_status_text); ?>
				</td>
				<?php endif;?>

				<?php if($this->params->get('entity_type', 1)): ?>
				<td class="small">
				  <?php echo $this->escape($item->entity_type); ?>
				</td>
				<?php endif;?>

				<?php if($this->params->get('entity_id', 1)): ?>
				<td class="small">
				  <?php echo $this->escape($item->entity_id); ?>
				</td>
				<?php endif;?>


				<?php if($this->params->get('event', 1)): ?>
				<td class="small">
				 <?php echo $this->escape($item->event); ?>
				</td>
				<?php endif;?>

				<?php if($this->params->get('event_status', 1)): ?>
				<td class="small">
				 <?php echo $this->escape($item->event_status); ?>
				</td>
				<?php endif;?>

        <td class="small">
          <?php echo $this->escape($item->summary); ?>
        </td>

				<td class="small">
				  <?php echo JHtml::_('date', $item->create_time, JText::_('COM_LOGS_HEADING_CREATED_DATE_FORMAT')); ?>
				</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<div class="center">
			<?php echo $this->pagination->getListFooter(); ?>
		</div>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
