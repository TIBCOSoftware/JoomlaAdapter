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
$doc->addStyleSheet(JUri::root(TRUE) . '/components/com_cobalt/views/records/tmpl/default_list_home_all_products/css/home_all_products.css');
$core = array('type_id' => 'Type', 'user_id','','','','','','','','', );
JHtml::_('dropdown.init');
$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
$user = JFactory::getUser();
$isAdmin = in_array(7, $user->getAuthorisedGroups()) || in_array(8, $user->getAuthorisedGroups()) || in_array($user->id, DeveloperPortalApi::getIdsOfOrganizationAdmin(68));
foreach ($exclude as &$value) {
  $value = $this->fields_keys_by_id[$value];
}

foreach($this->items AS $key=>$item) {
  if($item->type_id != 1 || (TibcoTibco::getFlagForShow($item->id) && !$isAdmin)) {
    unset($this->items[$key]);
  }
}
?>
<div id="home-all-product" class="home-all-items">
  <h3><span><?php echo JText::sprintf("TITLE_HOME_ALL_PRODUCTS", count($this->items)); ?></span><?php if(count($this->items) == 0 || count($this->items) > 1) {echo "s";} ?></h3>
  <div class="als-container" id="products-all-list">
    <div class="my-als-items">
      <div class="als-viewport">
        <ul class="als-wrapper products-all-product">
          <?php foreach ($this->items AS $item): ?>
            <?php
            $matches = array();
            preg_match('/<[^<>]+>[^<>]+<\/[^<>]+>/', $item->fields_by_id[2]->result, $matches);
            ?>
            <li class="als-item span3">
              <a href="<?php echo JRoute::_($item->url); ?>">
                <div><?php echo strip_tags($item->fields_by_id[3]->result, "<img>"); ?></div>
                <div class="products-list-item-texts">
                  <h5 class="products-list-item-title" title="<?php echo $item->title; ?>"><?php echo $item->title; ?></h5>
                  <div class="products-list-item-description" title="<?php echo strip_tags($matches[0]); ?>"><?php echo strip_tags($matches[0]); ?></div>
                </div>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>