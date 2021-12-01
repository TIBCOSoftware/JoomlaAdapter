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
$params = $this->tmpl_params['list'];
?>

<style>
.applications-scropes-list{
	list-style: none;
	margin: 0;
	padding: 0;
}
.applications-scropes-list-item:oven{
	background-color: gray;
}
</style>
<ul class="clearfix applications-scropes-list">
<?php foreach ($this->items AS $item):?>
		<?php if($params->get('tmpl_core.item_title')):?>
			<li class="applications-scropes-list-item">
					<?php if($this->submission_types[$item->type_id]->params->get('properties.item_title')):?>
							<<?php echo $params->get('tmpl_core.title_tag', 'h2');?> class="record-title">
								<?php if($params->get('tmpl_core.item_link')):?>
									<a <?php echo $item->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo JRoute::_($item->url);?>">
										<?php echo $item->title?>
									</a>
								<?php else :?>
									<?php echo $item->title?>
								<?php endif;?>
								<?php echo CEventsHelper::showNum('record', $item->id);?>
							</<?php echo $params->get('tmpl_core.title_tag', 'h2');?> class="record-title">
					<?php endif;?>
			</li>
		<?php endif;?>
<?php endforeach;?>
</ul>