<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$key = $this->id.'-'.$record->id;

$db = JFactory::getDbo();
$query = 'Select id, title from `#__js_res_record` where id in
	(select record_id from `#__js_res_record_values` where type_id=6 and field_value=' . $record->id .') and published=1';
$db->setQuery($query);
$results = $db->loadObjectList();

$rids = array();
$operations = array();

foreach($results AS $i => $keys){
	array_push($rids, $keys->id);
	array_push($operations, $keys->title);
}

echo $this->content['html'];

if($this->show_btn_new)
{
	$url = 'index.php?option=com_cobalt&view=form';
	$url .= '&section_id='.$section->id;
	$url .= '&type_id='.$type->id;
	$url .= '&fand='.$record->id;
	$url .= '&field_id='.$this->params->get('params.child_field');
	$url .= '&return='.Url::back();
	$url .= '&Itemid='.$section->params->get('general.category_itemid');

	$links[] = sprintf('<a href="%s" class="btn btn-small">%s</a>', JRoute::_($url), JText::_($this->params->get('params.invite_add_more')));
}

if($this->content['total'] >= 5) {
	$doTask = JRoute::_('index.php?option=com_cobalt&view=elements&layout=records&tmpl=component&section_id=' . $section->id .
		'&type_id=' . $type->id .
		'&record_id=' . $record->id .
		'&type=' . $this->type .
		'&field_id=' . $this->id .
		'&excludes=' . implode(',', $this->content['ids']), false);

	$links[] = "<a data-toggle=\"modal\" role=\"button\" class=\"btn btn-small\" href=\"#modal_{$key}\">Show More...</a>\n";
	?>
	<div style="width:770px;" class="modal hide fade" id="modal_<?php echo $key; ?>" tabindex="-1" role="dialog"
		 aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel"><?php echo $record->id . '_api'; ?></h3>
		</div>

		<div class="modal-body" style="overflow-x: hidden; max-height:500px; padding:0;">
<!--						<iframe frameborder="0" width="100%" height="410px"></iframe>-->
			<form name="adminForm" id="adminForm" method="post"
				  style="padding:10px; min-height: 400px; box-shadow:none; background:#fff;">
				<div class="container-fluid">
					<div id="row-fluid">
						<div class="pull-left input-append">

							<input type="text" name="filter_search" id="filter_search" value="" />
							<button class="btn" id="searchBtn" type="button">
								<?php echo HTMLFormatHelper::icon('document-search-result.png');  ?>
								<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
							<button class="btn" id="clearBtn" type="button" onclick="document.id('filter_search').value='';">
								<?php echo HTMLFormatHelper::icon('eraser.png');  ?>
								<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="container-fluid" id="table">
					<table class="table">
						<thead style="display:block">
						<th width="1%">
							<?php echo JText::_('CNUM'); ?>
						</th>
						<th>
							<?php echo JText::_('CTITLE') ?>
						</th>
						</thead>
						<tbody>
						<?php foreach ($operations AS $i => $operation): ?>
							<tr>
								<td><?php //echo $this->pagination->getRowOffset(); ?></td>
								<td><a href="<?php echo JUri::base().'index.php/apis/item/'.$rids[$i].'-'.$operation; ?>"><?php echo $operation; ?></a></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>

				</div>
			</form>

		</div>

		<div class="modal-footer">
			<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
		</div>
	</div>
	<script>
		(function ($) {
			$('#modal_<?php echo $key;?>').on('show', function () {
				$("iframe", this).attr('src', '<?php echo $doTask;?>');
			});
		}(jQuery));
	</script>
	<?php
}
if($this->show_btn_all)
{
	$links[] = sprintf('<a class="showALL" href="%s" class="btn btn-small">%s</a>', JRoute::_($this->show_btn_all), JText::_($this->params->get('params.invite_view_more')));
}
?>

<?php if(!empty($links)): ?>
	<?php echo implode(' ', $links);?>
<?php endif; ?>

<script>
	jQuery('a.showALL').hide();

	var operations = <?php print_r(json_encode($results)); ?>;
	var search = jQuery('#filter_search'),
		searchBtn = jQuery('#searchBtn'),
		clearBtn = jQuery('#clearBtn'),
		tableContainer = jQuery('#table'),
		table = jQuery('#table table');

	searchBtn.on('click', function(){
		filterOperation();
		return false;
	});

	clearBtn.on('click', function(){
		search.val('');
		tableContainer.html(table);
		return false;
	});

	function filterOperation(){
		var searchText = jQuery('#filter_search').val();
		if(searchText !== "") {
			var tab = '<table class="table"> <thead style="display:block"><th width="1%"><?php echo JText::_('CNUM'); ?></th><th><?php echo JText::_('CTITLE') ?> </th> </thead>';
			jQuery.each(operations, function (id, item) {
				if (item.title.indexOf(searchText) != -1) {
					tab += "<tr align='center'><td>" + item.id + "</td><td><a href=\"<?php echo JUri::base().'index.php/apis/item/'.$rids[$i].'-'.$operation; ?>\">" + item.title + "</a></td></tr>";
				}else{
				}
			})
		}else{
			tableContainer.html("");
		}
		x=
		tab += "</table>";
		tableContainer.html(tab);
	}
</script>

