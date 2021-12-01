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
$user_id = $this->input->getInt('user_id', 0);
$app = JFactory::getApplication();

$markup = $this->tmpl_params['markup'];

$listOrder  = @$this->ordering;
$listDirn = @$this->ordering_dir;
$type_ids_to_create = array(1, 2, 4, 5, 9, 10,12,20,21);

$back = NULL;
if($this->input->getString('return'))
{
  $back = Url::get_back('return');
}

$isMe = $this->isMe;
$current_user = JFactory::getUser($this->input->getInt('user_id', $this->user->get('id')));

?>
<?php if($markup->get('main.css')):?>
<style>
<!--
  <?php echo $markup->get('main.css');?>
-->
</style>
<?php endif;?>
<!-- Show page header -->

<!-- If section is personalized load user block -->
<?php if(($this->section->params->get('personalize.personalize') && $this->input->getInt('user_id')) || $this->isMe):?>
  <?php echo $this->loadTemplate('user_block');?>


<!-- If title is allowed to be shown -->
<?php elseif($markup->get('title.title_show')):?>
  <div class="page-header">
    <?php if(in_array($this->section->params->get('events.subscribe_category'), $this->user->getAuthorisedViewLevels()) && $this->input->getInt('cat_id')):?>
      <div class="pull-right">
        <?php echo HTMLFormatHelper::followcat($this->input->getInt('cat_id'), $this->section);?>
      </div>
    <?php elseif(in_array($this->section->params->get('events.subscribe_section'), $this->user->getAuthorisedViewLevels())):?>
      <div class="pull-right">
        <?php echo HTMLFormatHelper::followsection($this->section);?>
      </div>
    <?php endif;?>
    <h1>
      <?php $dtitle = $this->escape($this->title); ?>
            <!--            --><?php //echo $dtitle; ?>
            <!--            --><?php //echo CEventsHelper::showNum('section', $this->section->id, true); ?>
                    </h1>

                    <script type="text/javascript">
                        (function($){
                            $(function(){
                                <?php
                                    echo "jQuery('#banner-title-heading').text('".$dtitle."');";
                                ?>
                            });
                        })(jQuery);
                    </script>
  </div>



    <!-- If menu parameters title is set -->
<?php elseif ($this->appParams->get('show_page_heading', 0) && $this->appParams->get('page_heading', '')) : ?>
  <div class="page-header">
    <h1>
      <?php echo $this->escape($this->appParams->get('page_heading')); ?>
    </h1>
  </div>
<?php endif;?>

<div id="compare" <?php echo !$this->compare ? 'class="hide"' : '';?>>
  <div class="alert alert-info alert-block">
    <h4><?php echo JText::sprintf('CCOMPAREMSG', $this->compare) ?></h4>
    <br><a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=compare&section_id='.$this->section->id.'&return='.Url::back()); ?>" class="btn btn-primary"><?php echo JText::_('CCOMPAREVIEW');?></a>
    <button onclick="Cobalt.CleanCompare()" class="btn"><?php echo JText::_('CCLEANCOMPARE');?></button>
  </div>
</div>

<!--  Show description of the current category or section -->
<?php
    if($this->description) {
        $tmp = $this->description;
        if($this->user->id) {
            $tmp = preg_replace("/guests-only/","guests-only hidden",$tmp);
        } else {
            $tmp = preg_replace("/registered-only/","registered-only hidden",$tmp);
        }
        echo $tmp;
    }
?>


<!--  Show menu and filters -->
   <?php if($markup->get('menu.menu') || $markup->get('filters.filters')): ?>
   <form method="post" action="<?php echo $this->action; ?>" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <?php if(!empty($this->postbuttons) && in_array($markup->get('menu.menu_newrecord'), $this->user->getAuthorisedViewLevels())):?>
        <?php $has_allowed_types = false; ?>
        <?php foreach ($this->postbuttons AS $type)
              {
               if(in_array($type->id, $type_ids_to_create)) {
                      $has_allowed_types=true;
                      //check for application and switch create button copy to register
                      if($type->id ==9){
                      	$createButtonText=  JText::_('REGISTER_APP').JText::_($type->name);
                      }else{
                        $createButtonText=  JText::_('CREATE_NEW').JText::_($type->name);
                      }
                      echo '<a class="btn new-recorder pull-left"  href="'.Url::add($this->section, $type, $this->category).'">'.$createButtonText. '</a>';
                }
              }
        ?>
      <?php endif;?>
      <?php if(in_array($markup->get('menu.menu_ordering'), $this->user->getAuthorisedViewLevels()) && $this->items):?>
        <div class="dropdown order-by  pull-right">
          <a href="#" class="dropdown-toggle btn" data-toggle="dropdown">
            <!-- <?php echo JText::_($markup->get('menu.menu_ordering_label', 'Sort By'))?> -->
          </a>
          <ul class="dropdown-menu">
            <?php if(@$this->items[0]->searchresult):?>
              <li>
              <?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_ctime_icon') ? HTMLFormatHelper::icon('document-search-result.png'): null ).' '.JText::_('CORDERRELEVANCE'), 'searchresult', $listDirn, $listOrder); ?></li>
            <?php endif;?>

            <?php if(in_array($markup->get('menu.menu_order_ctime'),  $this->user->getAuthorisedViewLevels())):?>
              <li>
              <?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_ctime_icon') ? HTMLFormatHelper::icon('core-ctime.png'): null ).' '.JText::_($markup->get('menu.menu_order_ctime_label', 'Created')), 'r.ctime', $listDirn, $listOrder); ?></li>
            <?php endif;?>

            <?php if(in_array($markup->get('menu.menu_order_mtime'),  $this->user->getAuthorisedViewLevels())):?>
              <li>
              <?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_mtime_icon') ? HTMLFormatHelper::icon('core-ctime.png'): null ).' '.JText::_($markup->get('menu.menu_order_mtime_label', 'Modified')), 'r.mtime', $listDirn, $listOrder); ?></li>
            <?php endif;?>

            <?php if(in_array($markup->get('menu.menu_order_title'),  $this->user->getAuthorisedViewLevels())):?>
              <li>
              <?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_title_icon') ? HTMLFormatHelper::icon('edit.png'): null ).' '.JText::_($markup->get('menu.menu_order_title_label', 'Title')), 'r.title', $listDirn, $listOrder); ?></li>
            <?php endif;?>

            <?php if(in_array($markup->get('menu.menu_order_hits'),  $this->user->getAuthorisedViewLevels())):?>
              <li>
              <?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_hits_icon') ? HTMLFormatHelper::icon('hand-point-090.png'): null ).' '.JText::_($markup->get('menu.menu_order_hits_label', 'Hist')), 'r.hits', $listDirn, $listOrder); ?></li>
            <?php endif;?>

            <?php if(in_array($markup->get('menu.menu_order_votes_result'),  $this->user->getAuthorisedViewLevels())):?>
              <li>
              <?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_votes_result_icon') ? HTMLFormatHelper::icon('star.png'): null ).' '.JText::_($markup->get('menu.menu_order_votes_result_label', 'Votes')), 'r.votes_result', $listDirn, $listOrder); ?></li>
            <?php endif;?>

            <?php if(in_array($markup->get('menu.menu_order_comments'),  $this->user->getAuthorisedViewLevels())):?>
              <li>
              <?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_comments_icon') ? HTMLFormatHelper::icon('balloon-left.png'): null ).' '.JText::_($markup->get('menu.menu_order_comments_label', 'Comments')), 'r.comments', $listDirn, $listOrder); ?></li>
            <?php endif;?>

            <?php if(in_array($markup->get('menu.menu_order_favorite_num'),  $this->user->getAuthorisedViewLevels())):?>
              <li>
              <?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_favorite_num') ? HTMLFormatHelper::icon('bookmark.png'): null ).' '.JText::_($markup->get('menu.menu_order_favorite_num_label', 'Number of bookmarks')), 'r.favorite_num', $listDirn, $listOrder); ?></li>
            <?php endif;?>

            <?php if(in_array($markup->get('menu.menu_order_username'),  $this->user->getAuthorisedViewLevels())):?>
              <li>
              <?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_username') ? HTMLFormatHelper::icon('user.png'): null ).' '.JText::_($markup->get('menu.menu_order_username_label', 'user name')), $this->section->params->get('personalize.author_mode'), $listDirn, $listOrder); ?></li>
            <?php endif;?>

            <?php if(in_array($markup->get('menu.menu_order_fields'),  $this->user->getAuthorisedViewLevels())):?>
              <?php foreach ($this->sortable AS $field):?>
                <li>
                <?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_fields_icon') && ($icon = $field->params->get('core.icon')) ? HTMLFormatHelper::icon($icon): null ).' '.JText::_($field->label), FieldHelper::sortName($field), $listDirn, $listOrder); ?></li>
              <?php endforeach;?>
            <?php endif;?>
          </ul>
        </div>
      <?php endif;?>

      <?php if($markup->get('filters.filters')):?>
        <div class="search-form pull-left"<?php if(!$has_allowed_types){echo " style='margin-left:330px;'"; } ?>>
          <span style="display: none;">Search box</span>
          <?php if(in_array($markup->get('filters.show_search'), $this->user->getAuthorisedViewLevels())):?>

            <input type="text" style="max-width: 300px; min-width: 50px;" placeholder="<?php echo JText::sprintf('ASG_CSEARCHPLACEHOLDER', $this->escape($this->title));  ?>" name="filter_search" value="<?php echo  htmlspecialchars($this->state->get('records.search'));?>" />
            <button  class="icon icon-search"></button>
            <a id="reset-search-keyword" style="position:relative;display:none; left:-25px;cursor:pointer;"><img src="<?php echo JURI::root(TRUE)?>/media/mint/icons/16/cross-circle.png" alt="<?php echo JText::_('P_STOP')?>" align="absmiddle"></a>
            <script type="text/javascript">
              jQuery(function(){
                var $ = jQuery,search_keyword = $("input[name='filter_search']"),cross_button=$('#reset-search-keyword');

                function _showCross(){
                  if (!jQuery.browser.msie || !jQuery.browser.version>=10) {
                    $.trim($(this).val())?cross_button.show():cross_button.hide();
                  }
                }

                search_keyword.focus(function(){
                                _showCross.apply($(this));
                              })
                              .keyup(function(){
                                _showCross.apply($(this));
                              });

                _showCross.apply(search_keyword);

                cross_button.live("click",function(){
                  search_keyword.val("").parents('form').submit();
                });
              });
            </script>
          <?php endif;?>
        </div>
      <?php endif;?>

      <input type="hidden" name="option" value="com_cobalt" />
      <input type="hidden" name="section_id" value="<?php echo $this->state->get('records.section_id')?>" />
      <input type="hidden" name="cat_id" value="<?php echo $app->input->getInt('cat_id');?>" />
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="limitstart" value="0" />
      <input type="hidden" name="filter_order" value="<?php //echo $this->ordering; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php //echo $this->ordering_dir; ?>" />
      <?php echo JHtml::_( 'form.token' ); ?>
      <div class="clearfix"></div>
   </form>

   <?php endif; ?>




<?php if($this->items):?>
  <?php echo $this->loadTemplate('list_'.$this->list_template);?>

  <?php if ($this->tmpl_params['list']->def('tmpl_core.item_pagination', 1)) : ?>
    <form method="post">
      <div style="text-align: center;">
        <small>
          <?php if($this->pagination->getPagesCounter()):?>
            <?php echo $this->pagination->getPagesCounter(); ?>
          <?php endif;?>
          <?php  if ($this->tmpl_params['list']->def('tmpl_core.item_limit_box', 0)) : ?>
            <?php echo str_replace('<option value="0">'.JText::_('JALL').'</option>', '', $this->pagination->getLimitBox());?>
          <?php endif; ?>
          <?php echo $this->pagination->getResultsCounter(); ?>
        </small>
      </div>
      <?php if($this->pagination->getPagesLinks()): ?>
        <div style="text-align: center;" class="pagination">
          <?php echo str_replace('<ul>', '<ul class="pagination-list">', $this->pagination->getPagesLinks()); ?>
        </div>
        <div class="clearfix"></div>
      <?php endif; ?>
    </form>
  <?php endif; ?>


<?php endif;?>


<?php if(!$this->items):?>
   <style type="text/css">
      .no-search-result{
         min-height: 100px;
         text-align: center;
         line-height: 100px;
      }
   </style>
   <div class="no-search-result"><?php echo JText::sprintf('NO_RECORDS_FOUND'); ?></div>
<?php endif;?>