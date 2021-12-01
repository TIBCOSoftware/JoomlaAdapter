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

require_once JPATH_BASE . "/includes/api.php";
$k = $p1 = 0;
$params = $this->tmpl_params['list'];
$total_fields_keys = $this->total_fields_keys;
$fh = new FieldHelper($this->fields_keys_by_id, $this->total_fields_keys);
$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach ($exclude as &$value) {
	$value = $this->fields_keys_by_id[$value];
}
//JHtml::_('dropdown.init');
$applications_for_user = DeveloperPortalApi::getApplicationsForUser();
?>
<?php if($params->get('tmpl_core.show_title_index')):?>
	<h2><?php echo JText::_('CONTHISPAGE')?></h2>
	<ul>
		<?php foreach ($this->items AS $item):?>
			<li><a href="#record<?php echo $item->id?>"><?php echo $item->title?></a></li>
		<?php endforeach;?>
	</ul>
<?php endif;?>

<table class="application-list">
	<thead>
		<tr>
			<th><div>Application name</div></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->items AS $item):?>
            <?php if($item->type_id == 9 && in_array($item->id, $applications_for_user)): ?>
        		<tr class="short-view">
        			<td>
        				<dl>
        					<dd>
                                    <?php echo $item->fields_by_id[119]->result; ?>
                            </dd>
        					<dt><div>
        							<?php if($params->get('tmpl_core.item_title')):?>
        								<?php if($this->submission_types[$item->type_id]->params->get('properties.item_title')):?>
        										<h3>
        											<?php if(in_array($params->get('tmpl_core.item_link'), $this->user->getAuthorisedViewLevels())):?>
        												<a <?php echo $item->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo JRoute::_($item->url);?>">
        													<?php echo $item->title?>
        												</a>
        											<?php else:?>
        												<?php echo $item->title?>
        											<?php endif;?>
        											<?php echo CEventsHelper::showNum('record', $item->id);?>
                                                </h3>
        								<?php endif;?>
        							<?php endif;?>
                                <div class="application-desc">
                                <?php
                                    echo $item->fields[57];
                                ?>
                                </div>
        						<?php foreach ($item->fields_by_id AS $field):?>
        							<?php if(in_array($field->key, $exclude)) continue; ?>
        							<div id="<?php echo $field->id;?>-lbl"  class="<?php echo $field->class;?>" >
        								<?php echo $field->result; ?>
        							</div>
        						<?php endforeach;?>
                            </div></dt>
        				</dl>
        			</td>
        		</tr>
            <?php endif; ?>
		<?php endforeach; ?>
	</tbody>
</table>
<div class="clearfix"></div>