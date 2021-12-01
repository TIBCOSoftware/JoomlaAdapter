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
$k = $p1 = 0;
$params = $this->tmpl_params['list'];
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root(TRUE).'/components/com_cobalt/views/records/tmpl/default_list_featured4list/css/featured4list.css');
$core = array('type_id' => 'Type', 'user_id','','','','','','','','', );
JHtml::_('dropdown.init');
$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach ($exclude as &$value) {
	$value = $this->fields_keys_by_id[$value];
}

?>
<?php if($params->get('tmpl_core.show_title_index')):?>
	<h2 class="featured4list-title"><?php echo JText::_('CONTHISPAGE')?></h2>
	<ul>
		<?php foreach ($this->items AS $item):?>
			<li><a href="#record<?php echo $item->id?>"><?php echo $item->title?></a></li>
		<?php endforeach;?>
	</ul>
<?php endif;?>

<ul class="thumbnails newsflash-horiz featured4list">
  <?php $color = 0;?>
  <?php foreach ($this->items AS $item):?>
  <?php $color++; ?>
  <li class="span3 primary-color<?php echo $color?>">
      <a href="<?php echo JRoute::_($item->url);?>">
    <?php foreach ($this->total_fields_keys AS $field):?>
      <?php if(in_array($field->key, $exclude)) continue; ?>
      <?php if(isset($item->fields_by_key[$field->key]->result) && mb_strtolower($field->label)=='thumbnail') echo str_replace("/a>","/span>",str_replace("<a","<span",$item->fields_by_key[$field->key]->result));?>
    <?php endforeach;?>
    <h4 class="newsflash-title">
        <?php echo $item->title?>
    </h4>
      </a>
  </li>
  <?php if($color == 5)
		$color = 0;?>
  <?php endforeach;?>
</ul>