<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
defined('_JEXEC') or die();

?>

<style>
  .list-item {
    margin-bottom: 5px;
  }
</style>
<div id="parent_list<?php echo $this->id; ?>"></div>
<div class="input-append">
  <input type="text" disabled="disabled" value="<?php echo $this->uname;?>"  id="jform_user_id_name" class="input-medium"/>
    <a class="btn btn-primary modal_jform_user_id" data-toggle="modal" role="button" href="#modal<?php echo $this->id; ?>"><i class="icon-user"></i></a>
</div>
<input type="hidden" value="<?php echo $this->value;?>"  name="jform[fields][<?php echo $this->id; ?>]" id="jform_user_id_id">

<div style="width:770px;" class="modal hide fade" id="modal<?php echo $this->id;?>" tabindex="-1" role="dialog">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#215;</button>
    <h3 id="myModalLabel">Select Contact User</h3>
  </div>

  <div class="modal-body" style="overflow-x: hidden; max-height:650px; padding:0;">
  </div>

  <div class="modal-footer">
    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<script type="text/javascript">
  (function($){

    window.modal<?php echo $this->id; ?> = $('#modal<?php echo $this->id; ?>');
    window.elementslist<?php echo $this->id; ?> = $('#parent_list<?php echo $this->id; ?>');
    window.multi<?php echo $this->id; ?> = <?php echo $multi ? 'true' : 'false';?>;
    window.limit<?php echo $this->id; ?> = <?php echo $this->params->get('params.multi_limit', 0);?>;
    window.name<?php echo $this->id; ?> = '<?php echo $name; ?>';

    $('#modal<?php echo $this->id;?>').on('show', function(){
      var ids = [];
      $.each(elementslist<?php echo $this->id; ?>.children('div.alert'), function(k, v){
        ids.push($(v).attr('rel'));
      });
      console.log(ids.join(','));
      var iframe = $(document.createElement("iframe")).attr({
        frameborder:"0",
        width:"100%",
        height:"510px",
        src:'<?php echo JURI::root() . "administrator/index.php?option=com_users&view=users&layout=modal&tmpl=component&field=$this->id";?>&excludes='+ ids.join(',')
      });
      $(".modal-body").html(iframe);
    });

    window.list<?php echo $this->id; ?> = function(id, title)
    {
      <?php if(!$multi):?>
        elementslist<?php echo $this->id; ?>.html('');
      <?php else: ?>
        lis = elementslist<?php echo $this->id; ?>.children('div.alert');
        if(lis.length >= limit<?php echo $this->id; ?>) {
          alert('<?php echo JText::sprintf('CSELECTLIMIT', $this->params->get('params.multi_limit'));?>');
          return false;
        }
        error = 0;
        $.each(lis, function(k, v){
          if($(v).attr('rel') == id){
            alert('<?php echo JText::_('CALREADYSELECTED');?>');
            error = 1;
          }
        });
        if(error) return false;
      <?php endif;?>

      var el = $(document.createElement('div')).attr({
        'class': 'alert alert-info list-item',
        rel: id
      }).html('<a class="close" data-dismiss="alert" href="#">x</a><span>'+title+'</span><input type="hidden" name="<?php echo $name ?>" value="'+id+'">');
      elementslist<?php echo $this->id; ?>.append(el);
      return true;
    }

    window.updatelist<?php echo $this->id; ?> = function(list){
      var elementslist<?php echo $this->id; ?> = $('#parent_list<?php echo $this->id; ?>');
      elementslist<?php echo $this->id; ?>.empty();
      $.each(list, function(){
        elementslist<?php echo $this->id; ?>.append(this);
      });
    }
  }(jQuery));
  function jSelectUser_<?php echo $this->id;?>(id,name){
    jQuery(".close:button").click();
    jQuery("#jform_user_id_name").val(name);
    jQuery("#jform_user_id_id").val(id);
  }
</script>

