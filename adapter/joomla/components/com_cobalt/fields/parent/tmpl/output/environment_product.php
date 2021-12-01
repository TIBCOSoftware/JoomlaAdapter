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
defined('_JEXEC') or die();
?>

<script type="text/javascript">

(function($) {

    $(function() {
        $('#a_remove_env').on('click', function(oEvent) {
            if(confirm('<?php echo JText::_("PRODUCT_ENVIRONMENT_REMOVAL_CONFIRMATION"); ?>')) {
                $.get('<?php echo JURI::root()."index.php?option=com_cobalt&task=ajaxmore.removeEnvFromProduct&record_id=".$record->id."&field_id=".$this->id."&return=".base64_encode(JRequest::getURI());?>', function(data, textStatus, jqXHR) {
                    if(data.success) {
                        DeveloperPortal.sendUpdateNotification(nProductId, DeveloperPortal.CONTENT_TYPE_MAP[nTypeId], oPublicEnvs, function(data) {
                            window.location.reload();
                        });
                    } else {
                        DeveloperPortal.storeErrMsgInCookie([data.result]);
                        window.location.reload();
                    }
                }, 'json').fail(function(jqXHR, testStatus, errorThrown) {
	                 DeveloperPortal.storeErrMsgInCookie([errorThrown]);
	                 window.location.reload();
                });
            }
        });
    });
    
}(jQuery));

</script>

<?php if ($this->show_btn_exist || isset($this->content['list'])): ?>
  <h3 class="env-title"><?php echo $this->id == 35?JText::_('PUBLIC_ENVIRONMENTS'):JText::_('PRIVATE_ENVIRONMENTS');?></h3>
<?php endif?>
<?php if(isset($this->content['list'])): ?>
<div class="well">
  <div class="asg-environment-list-product-wrap">
  <?php foreach($this->content['list'] as $key=>$env): ?>
    <a href="<?php echo $env->url;?>"><?php echo $env->title;?></a><br/>
    <?php echo $env->fields_by_id[14]->value[0]['url'];?>
    <?php //if($env->controls):?>
      <?php //echo $env->controls[0];?>
    <?php //endif;?>
    <a id="a_remove_env" href="javascript:void(0)">Remove</a>
    <br/>
  <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>



<?php if(!isset($this->content['list'])): ?>
<?php
      $this->content['list'] = array();
      $db = JFactory::getDbo();
      // Get the record id of environment so that we can get the information of those record
      $query = 'SELECT `record_id` FROM  #__js_res_record_values WHERE  `field_id`=34 AND `field_value`='.$record->id;
      $db->setQuery($query);
      $res_id1 = $db->loadColumn();
      if(!empty($res_id1))
      {
        $res_id = array();
        foreach ($res_id1 as $key => $id) {
          $item = ItemsStore::getRecord($id);
          if($item->published != 1) continue;
          $res_id[] = $id;
        }
        if(!empty($res_id)){
          // Get the particular field from the record_values table
          $db->setQuery('SELECT `field_value`,`record_id` FROM #__js_res_record_values WHERE `field_id`=14 AND `record_id` in('.implode(',', $res_id).')');
          $res = $db->loadObjectList();
          foreach($res as $k=>$v)
          {
            $new_item = array();
            foreach ($v as $fkey => $need_field) {
              $new_item[$fkey] = $fkey == 'record_id'?ItemsStore::getRecord($need_field)->title:$need_field;
            }
            $this->content['list'][] = $new_item;
          }
        }
      }
?>
  <?php if(count($this->content['list'])): ?>
  <h3 class="env-title"><?php echo $this->id == 35?JText::_('PUBLIC_ENVIRONMENTS'):JText::_('PRIVATE_ENVIRONMENTS');?></h3>
  <div class="well">
    <div class="asg-environment-list-product-wrap">
      <?php //pre($this->content['list']); ?>
    <?php foreach($this->content['list'] as $key=>$env): ?>
      <b><?php echo $env['record_id']; ?></b>
      <br/>
      <br/>
      <?php echo $env['field_value'];?>
    <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>
<?php endif; ?>


<?php

if($this->show_btn_new)
{
  $url = 'index.php?option=com_cobalt&view=form';
  $url .= '&section_id='.$section->id;
  $url .= '&type_id='.$type->id;
  $url .= '&fand='.$record->id;
  $url .= '&field_id='.$this->params->get('params.child_field');
  $url .= '&return='.Url::back();
  $url .= '&Itemid='.$section->params->get('general.category_itemid');
  $links[] = sprintf('<a href="%s" class="btn btn-small">%s</a>', JRoute::_($url), $this->params->get('params.invite_add_more'));
}

if($this->show_btn_exist)
{
  $doTask = JRoute::_('index.php?option=com_cobalt&view=elements&layout=records&tmpl=component&section_id='.$section->id.
    '&type_id='.$type->id.
    '&record_id='.$record->id.
    '&type='.$this->type.
    '&field_id='.$this->id.
    '&request_environment_for_product=1'.
    '&excludes='.implode(',', $this->content['ids']), false);

  $links[] = "<a data-toggle=\"modal\" role=\"button\" class=\"btn btn-small\" href=\"#modal{$this->id}\">\n".JText::_($this->params->get('params.add_existing_label'))."</a>\n";
  ?>
    <div style="width:770px;" class="modal hide fade" id="modal<?php echo $this->id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#215;</button>
        <h3 id="myModalLabel"><?php echo JText::_('FS_ATTACHEXIST');?></h3>
      </div>

      <div class="modal-body" style="overflow-x: hidden; max-height:500px; padding:0;">
        <iframe frameborder="0" width="100%" height="410px"></iframe>
      </div>

      <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
      </div>
    </div>
    <script>
    (function($){
      window['modal<?php echo $this->id;?>'] = $('#modal<?php echo $this->id;?>');
      $('#modal<?php echo $this->id;?>').on('show', function(){
        $("iframe", this).attr('src', '<?php echo $doTask;?>');
      });
    }(jQuery));
    </script>
  <?php
}

if($this->show_btn_all)
{
  $url = 'index.php?option=com_cobalt&view=records';
  $url .= '&section_id='.$section->id;
  $url .= '&task=records.filter';
  $url .= '&filter_name[0]=filter_type';
  $url .= '&filter_val[0]='.$type->id;
  $url .= '&filter_name[1]=filter_'.$this->field_key;
  $url .= '&filter_val[1]='.$record->id;
  $url .= '&Itemid='.$section->params->get('general.category_itemid');

  $links[] = sprintf('<a href="%s" class="btn btn-small">%s</a>', JRoute::_($url), $this->params->get('params.invite_view_more'));
}
?>

<?php if(!empty($links)): ?>
  <?php echo implode(' ', $links);?>
<?php endif; ?>