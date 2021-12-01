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
     *
     * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
     * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
     */
    defined('_JEXEC') or die;
    include_once JPATH_BASE . "/includes/api.php"; 

    $k = $p1 = $j = $c = 0;
    $max_cat_num = 5;
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

    $this->items = array_filter($this->items, "getProductRecords");
    $user_org = DeveloperPortalApi::getUserOrganization();
    $user_org = $user_org[0];

    $isAdmin = in_array($this->user->id, DeveloperPortalApi::getIdsOfOrganizationAdmin(68)) || in_array(7, $this->user->getAuthorisedGroups()) || in_array(8, $this->user->getAuthorisedGroups());

    foreach ($this->items as $key => $item) {
        $toShow = TibcoTibco::getFlagForShow($item->id);
        if($toShow && !$isAdmin){
            unset($this->items[$key]);
        }
    }
    makeListForTab($this->items, $tab_list);

?>
    <div class="products_cat_tab hidden">
        <ul class="nav nav-tabs" id="myTab">
            <?php foreach (array_keys($tab_list) as $key => $val): ?>
                <?php
                if ($c >= $max_cat_num) {
                    break 1;
                }
                $c++;
                ?>
                <li><a class="<?php echo "category-".str_replace(' ', '', $val); ?>" href="#<?php echo str_replace(' ', '', $val); ?>"><?php echo $val; ?></a></li>
            <?php endforeach; ?>
            <li><span href="#" class="view-all-products">View All Products</span></li>
            <?php $c = 0; ?>
        </ul>
        <div class="tab-content">
            <?php foreach ($tab_list as $key => $items): ?>
                <?php if ($c > $max_cat_num) {
                    $c = 0;
                    break 1;
                }
                $c++; ?>
                <div class="tab-pane asg-products-list <?php echo "category-".str_replace(' ', '', $key); ?>" id="<?php echo str_replace(' ', '', $key); ?>">
                    <div class="nav-tab-title <?php echo "category-".str_replace(' ', '', $key); ?>"></div>
                    <h3><?php echo $key; ?></h3>
                    <ul class="thumbnails products-list">
                        <?php $color = 0; ?>
                        <?php foreach ($items AS $item): ?>
                            <?php $color++; ?>
                            <li class="span3 products-items">
                                <a name="record<?php echo $item->id; ?>"></a>
                                <?php echo "<div class=\"primary-color".$color."\">"; ?>
                                <?php echo str_ireplace("img-polaroid", "transparent", $item->field_show_list['Thumbnail']); ?>
                                <?php echo "</div>"; ?>
                                <div class="description">
                                    <?php if (in_array($params->get('tmpl_core.item_link'), $this->user->getAuthorisedViewLevels())): ?>
                                    <a class="readmore" <?php echo $item->nofollow ? 'rel="nofollow"' : ''; ?>
                                       href="<?php echo JRoute::_($item->url); ?>">
                                        <?php endif; ?>
                                        <?php if ($params->get('tmpl_core.item_title')): ?>
                                            <?php if ($this->submission_types[$item->type_id]->params->get('properties.item_title')): ?>
                                                <h4 class="newsflash-title products-featured-product">
                                                    <?php echo $item->title ?>
                                                    <?php echo CEventsHelper::showNum('record', $item->id); ?>
                                                </h4>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <div class="products-list-item-description">
                                            <?php echo HTMLFormatHelper::cutHTML($item->field_show_list['Description'], 20); ?>
                                        </div>
                                        <?php if (in_array($params->get('tmpl_core.item_link'), $this->user->getAuthorisedViewLevels())): ?>
                                    </a>
                                <?php endif; ?>
                                </div>
                            </li>
                            <?php if ($color == 5)
                                    {
                                        $color = 0;
                                    }?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
            <?php $c = 0; ?>
        </div>
    </div>

    <div id="asg-view-by-cats" class="asg-products-list">
        <?php foreach ($tab_list as $key => $items): ?>
            <div class="list-by-category">
                <h3><?php echo $key; ?></h3>
                <ul class="thumbnails products-list">
                    <?php $color = 0; ?>
                    <?php foreach ($items AS $item): ?>
                        <?php $color++; ?>
                        <li class="span3 products-items">
                            <a name="record<?php echo $item->id; ?>"></a>
                            <?php echo "<div class=\"primary-color".$color."\">"; ?>
                            <?php echo str_ireplace("img-polaroid", "transparent", $item->field_show_list['Thumbnail']); ?>
                            <?php echo "</div>"; ?>
                            <div class="description">
                                <?php if (in_array($params->get('tmpl_core.item_link'), $this->user->getAuthorisedViewLevels())): ?>
                                <a class="readmore" <?php echo $item->nofollow ? 'rel="nofollow"' : ''; ?>
                                   href="<?php echo JRoute::_($item->url); ?>">
                                    <?php endif; ?>
                                    <?php if ($params->get('tmpl_core.item_title')): ?>
                                        <?php if ($this->submission_types[$item->type_id]->params->get('properties.item_title')): ?>
                                            <h4 class="newsflash-title products-featured-product">
                                                <?php echo $item->title ?>
                                                <?php echo CEventsHelper::showNum('record', $item->id); ?>
                                            </h4>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <div class="products-list-item-description">
                                        <?php echo HTMLFormatHelper::cutHTML($item->field_show_list['Description'], 20); ?>
                                    </div>
                                    <?php if (in_array($params->get('tmpl_core.item_link'), $this->user->getAuthorisedViewLevels())): ?>
                                </a>
                            <?php endif; ?>
                            </div>
                        </li>
                        <?php if ($color == 5)
                                {
                                    $color = 0;
                                }?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="clearfix"></div>
    <script type="text/javascript">
        (function ($) {
//            $('#adminForm').insertAfter($('.nav-tab-title'));
            $('ul#myTab a:first').tab('show');
            $('#tab-main').collapse('show');
            $("ul#myTab a").live("click", function () {
                if ($(this).parents("li").hasClass("active")) return false;
                $(this).parents("li").addClass("active").siblings().removeClass("active");
                var sel = $(this).attr("href");
                $(sel).show().siblings().hide();
                return false;
            });
            $("ul#product").parents("div").find("#overview").addClass("active");
            $("#login-button").live("click", function () {
                $("#login-form").toggle();
            });

            $("span.view-all-products").live('click', function () {
                $("#asg-view-by-cats").show();
                $(this).parents(".products_cat_tab").hide();
                $("#products-featured-product").hide();
                return false;
            });
            // NOTE: View all products for 2.1.0 version, previous category tabs are hidden.
            $("#asg-view-by-cats").show();
            $("#products-featured-product").hide();
        })(jQuery);
    </script>

<?php
    /**
     * Get the right records which we need
     *
     * @param array element $item
     *
     * @return boolean
     */
    function getProductRecords($item) {
        return (isset($item->type_id) && $item->type_id == 1);
    }

    //makeLists
    function makeListForTab(&$items = array(), &$destination = array()) {
        $show_fields = array("thumbnail", "name", "description");
        foreach ($items as $item) {
            $keys = array_values($item->categories);

            if(empty($keys))
            {
                $keys = array("All Products");
            }

            if (!$item->field_show_list) {
                $item->field_show_list = array();
            }

            foreach ($item->fields_by_id AS $field) {
                if (in_array(strtolower($field->label), $show_fields)) {
                    $item->field_show_list[$field->label] = $field->result;
                }
            }

            foreach ($keys as $cat_name) {
               if (!isset($destination[$cat_name])) {
                   $destination[$cat_name] = array();
               }
               $destination[$cat_name][] = $item;
            }
            
        }
    }

?>