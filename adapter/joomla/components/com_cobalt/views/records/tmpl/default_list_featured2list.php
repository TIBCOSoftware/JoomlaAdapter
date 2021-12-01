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
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root(TRUE).'/components/com_cobalt/views/records/tmpl/default_list_featured2list/css/featured2list.css');
$core = array('type_id' => 'Type', 'user_id','','','','','','','','', );
JHtml::_('dropdown.init');
$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach ($exclude as &$value) {
	$value = $this->fields_keys_by_id[$value];
}

$user = JFactory::getUser();
$isAdmin = in_array(7, $user->getAuthorisedGroups()) || in_array(8, $user->getAuthorisedGroups()) || in_array($user->id, DeveloperPortalApi::getIdsOfOrganizationAdmin(68));
?>
<?php if($params->get('tmpl_core.show_title_index')):?>
	<h2><?php echo JText::_('CONTHISPAGE')?></h2>
	<ul>
		<?php foreach ($this->items AS $item):?>
			<li><a href="#record<?php echo $item->id?>"><?php echo $item->title?></a></li>
		<?php endforeach;?>
	</ul>
<?php endif;?>

<div id="products-featured-product" class="featured-items">
  <h3>Featured Products</h3>
  <div class="als-container" id="products-featured-list">
<!--    <div class="my-als-wrapper">-->
    <span class="als-prev">&nbsp;</span>
    <div class="my-als-items">
    <div class="als-viewport">
	      <ul class="als-wrapper products-featured-product featured2list">
	          <?php $color=0; ?>
	            <?php $count = 0;?>
	            <?php foreach ($this->items AS $item):?>
               <?php
                  if(TibcoTibco::getFlagForShow($item->id) && !$isAdmin)
                  {
                    continue;
                  }
                ?>
	                <?php $color++;
                          $count++;
                    ?>
	            <li class="als-item span3">
	              <div class="primary-color<?php echo $color; ?>">
	              <a href="<?php echo JRoute::_($item->url);?>">
	              <?php echo str_replace("/a>","/span>",str_replace("<a","<span",$item->fields_by_id[3]->result));?>
	              <div class="newsflash-title">
	                  <h4><?php echo $item->title?></h4>
	              </div>
	              <div class="products-list-item-description"><?php echo $item->fields_by_id[2]->result;?></div>
	                </a>
	                </div>
	            </li>
      	            <?php if($color == 5)
      	            $color = 0; ?>
                <?php endforeach; ?>
	            <?php if($count<4):?>
	              <?php for($i=0; $i<4-$count;$i++):?>
	              <li class="als-item span3">
					<div class="empty-card">
					<div>
	              </li>
	              <?php endfor;?>
                <?php endif;?>
	      </ul>

    </div>
    </div>
        <span class="als-next">&nbsp;</span>
<!--        <div class="clearfix"></div>-->
<!--    </div>-->
  </div>
</div>
<script type="text/javascript">
(function($){
  $(function(){
    $("#products-featured-list").als({
      visible_items: 4,
//      scrolling_items: 1,
      orientation: "horizontal",
      circular: "yes",
      autoscroll: "no"
    });
  });
})(jQuery);
</script>