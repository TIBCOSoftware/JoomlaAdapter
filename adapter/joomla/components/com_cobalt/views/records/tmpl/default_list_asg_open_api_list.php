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
defined('_JEXEC') or die; 


$k = $p1 = $j = $c = 0;
$tab_list = array();
$params = $this->tmpl_params['list'];
$total_fields_keys = $this->total_fields_keys;
$fh = new FieldHelper($this->fields_keys_by_id, $this->total_fields_keys);
$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach ($exclude as &$value) {
	$value = $this->fields_keys_by_id[$value];
}
JHtml::_('dropdown.init');

$this->items = array_filter($this->items,"getApiRecords");
usort($this->items, "cmp");
makeListForTab($this->items,$tab_list);

?>

<div class="asg-products-list">
	<?php foreach ($tab_list as $key=>$items):?>
	<div class="list-by-category">
		  <h3>Products grouped by <?php echo $key;?></h3>
		  <ul class="thumbnails products-list">
		  	<?php foreach ($items AS $item):?>
			  		<li class="span3 products-items">
			  				<a name="record<?php echo $item->id;?>"></a>
			  				<?php echo $item->field_show_list['Thumbnail'];?>
			  				<?php if($params->get('tmpl_core.item_title')):?>
			  					<?php if($this->submission_types[$item->type_id]->params->get('properties.item_title')):?>
			  							<h4 class="newsflash-title products-featured-product">
			  								<?php if(in_array($params->get('tmpl_core.item_link'), $this->user->getAuthorisedViewLevels())):?>
			  									<a class="readmore" <?php echo $item->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo JRoute::_($item->url);?>">
			  										<?php echo $item->title?>
			  									</a>
			  								<?php else:?>
			  									<?php echo $item->title?>
			  								<?php endif;?>
			  								<?php echo CEventsHelper::showNum('record', $item->id);?>
			  							</h4>
			  					<?php endif;?>
			  				<?php endif;?>
			  				<div class="products-list-item-description">
			  				<?php echo HTMLFormatHelper::cutHTML($item->field_show_list['Description'],20);?>
			  				</div>
			  		</li>
		  	<?php endforeach;?>
		  </ul>
	</div>
	<?php endforeach;?>
</div>
<div class="clearfix"></div>
<p></p>

<?php 
	/**
	 * Get the right records which we need
	 * @param array element $item
	 * @return boolean
	 */
	function getApiRecords($item){
		return (isset($item->type_id) && $item->type_id==1);
	}
	//user defined function
	function cmp ($a, $b) {
	    if ($a->category_id == $b->category_id) return 0;
	    return ($a->category_id > $b->category_id) ? 1 : -1;
	}
	//makeList
  function makeListForTab(&$items=array(), &$destination = array()){
  	$show_fields = array("thumbnail","name","description");
  	foreach ($items as $item) {
  		$keys = array_values($item->categories);
      if (!empty($keys[0])){
        $key = $keys[0];
      }elseif (!empty($item->ucatname)){
        $key = $item->ucatname;
      }else{
        $key = "All Products";
      }
  		if(!$item->field_show_list){
  			$item->field_show_list = array();
  		}
  		foreach ($item->fields_by_id AS $field){
  			if(in_array(strtolower($field->label),$show_fields)){
  				$item->field_show_list[$field->label] = $field->result;
  			}
  		}
  		if(!$destination[$key]){
  			$destination[$key] = array();
  		}
  		$destination[$key][] = $item;
  	}
  }
?>