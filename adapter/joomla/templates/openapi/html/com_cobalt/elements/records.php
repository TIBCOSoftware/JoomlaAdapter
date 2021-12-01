<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die();
require_once JPATH_ROOT . "/includes/api.php";

$fid = (int)$_REQUEST['field_id'];
$k = 0;
$filter = JFilterInput::getInstance();
$schema_id_map = array(202=>216, 203=>216);

/**
 * Author Jackin
 * created 27/09/2013
 * Descrption Filter out the public environment shared by all the API's of the current product.
 * Paramter request_environment_for_product is used by deciding if the dialog is requesting the filter feature.
 * Paramter array(35,36) 35 & 36 are the field ids for public and private environment field of product
 */
if($_GET['request_environment_for_product'] && in_array($fid, array(35,36)))
{
  $this->items = array();
  $rid = $_GET['record_id'];

  $db = JFactory::getDbo();
  $query = 'select `record_id` from #__js_res_record_values where `field_id` IN (33,34) AND `field_value`='.$rid;
  $db->setQuery($query);
  $exclude = $db->loadColumn();

  $query = 'select `record_id` from #__js_res_record_values where `field_id`=6 AND `field_value`='.$rid;
  $db->setQuery($query);
  $apis = $db->loadColumn();
  $api_count = count($apis);

  $apis = implode(',', $apis);
  if(!empty($apis))
  {
      $query = 'select `record_id`, count(record_id) as record_num from #__js_res_record_values where `field_id`=25 AND `field_value` IN ('.$apis.') group by `record_id` order by record_num desc';
      $db->setQuery($query);
      $envs = $db->loadObjectList();
      foreach($envs as $key=>$val)
      {
        if($val->record_num>=$api_count && !in_array($val->record_id, $exclude))
        {
          $item = ItemsStore::getRecord($val->record_id);
          if($item->published != 1) continue;
          $this->items[] = $item;
        } 
      }
  }
}

if($_GET['filter_type'] == 7) { // Only for Plans
    function makeObjectString($array = array()) {
        $rv = '';
        if(count($array) > 0) {
            foreach($array as $key => $item) {
                $rv .= ", " . $key . ": " . $item;
            }
            $rv = substr($rv, 2);
        }
        return "{" . $rv . "}";
    }
        
    $plans_with_products = DeveloperPortalApi::getPlansWithProducts();
}
?>
<style>
	.list-item {
		margin-bottom: 5px;
	}
	#recordslist {
		margin-top: 20px;
	}
</style>

<script type="text/javascript">
(function ($) {
<?php if($_GET['filter_type'] == 7): ?>
    $(function() {
        var aSelectedProducts = $('input[name="jform[fields][114]"]', window.parent.document),
            dTRs = $('tr[id^="object_"]'),
            oPlansWithProducts = <?php echo makeObjectString($plans_with_products); ?>;
            
        function parseId(sId) {
            return sId.match(/object_([0-9]+)/)[1];
        }
        
        dTRs.each(function(index, item) {
            var id = parseId(item.id);
            if(aSelectedProducts.length === 0 || oPlansWithProducts[id] != aSelectedProducts[0].value) {
                $(item).hide();
            }
        });
    });
<?php endif; ?>
    window.DeveloperPortal = window.top.DeveloperPortal;
    window.Joomla.showError = window.top.Joomla.showError;
	window.closeWindow = function() {
		list = $('#recordslist').children('div.alert');
		parent['updatelist<?php echo $fid?>'](list);
		if(parent['modal<?php echo $fid; ?>'] && parent['modal<?php echo $fid; ?>'].modal('hide')) {
		    parent['modal<?php echo $fid; ?>'].modal('hide')
		} else {
		    jQuery('#modal<?php echo $fid; ?>');
		}
	};
	window.attachRecord = function(id, title, content) {
		<?php if(JRequest::getVar('mode') == 'form'):?>
			var multi = parent['multi<?php echo $fid; ?>'];
			var limit = parent['limit<?php echo $fid; ?>'];
			var inputname = parent['name<?php echo $fid; ?>'];
            var contentname = 'content'+'_<?php echo $fid; ?>';

			list = $('#recordslist');
			if(!multi)
			{
				list.html('');
			}
			else
			{
				lis = list.children('div.alert');
				if(lis.length >= limit) {
					alert('<?php echo JText::_("CERRJSMOREOPTIONS");?>');
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
			}
			var el = $(document.createElement('div'))
				.attr({
					'class': 'alert alert-info list-item',
					rel: id
				})
				.html('<a class="close" data-dismiss="alert" href="#">x</a><span>'+title+'</span><input type="hidden" name="'+inputname+'" value="'+id+'">' +
                        '<input type="hidden" name="'+ contentname +'" value=\'' +DeveloperPortal.java7DecodeURIComponent(content)+ '\'>')
				.appendTo(list);


		<?php else: ?>
            $.ajax({
              url: Cobalt.field_call_url,
              dataType: 'json',
              type: 'POST',
              data:{
                field_id: <?php echo JRequest::getInt('field_id');?>,
                func:'onAttachExisting',
                field:'<?php echo JRequest::getVar('type');?>',
                record_id:<?php echo JRequest::getInt('record_id');?>,
                attach:id
              }
            }).done(function(json) {
                if(!json.success) {
                    DeveloperPortal.storeErrMsgInCookie([json.error]);
                    parent.location.reload();
                } else {
                    if(window.top.oPublicEnvs !== undefined && window.top.DeveloperPortal !== undefined) {
                        DeveloperPortal.sendUpdateNotification(<?php echo $_GET["record_id"]; ?>, DeveloperPortal.CONTENT_TYPE_MAP[window.top.nTypeId], window.top.oPublicEnvs, function(data) {
                            if(parent['modal<?php echo $fid; ?>'] && parent['modal<?php echo $fid; ?>'].modal('hide')) {
                    		    parent['modal<?php echo $fid; ?>'].modal('hide')
                    		} else {
                    		    jQuery('#modal<?php echo $fid; ?>');
                    		}
                            parent.location.reload();
                        }, function(errorThrown) {
                    		if(parent['modal<?php echo $fid; ?>'] && parent['modal<?php echo $fid; ?>'].modal('hide')) {
                    		    parent['modal<?php echo $fid; ?>'].modal('hide')
                    		} else {
                    		    jQuery('#modal<?php echo $fid; ?>');
                    		}
                            parent.location.reload();
                        });
                    } else {
                		if(parent['modal<?php echo $fid; ?>'] && parent['modal<?php echo $fid; ?>'].modal('hide')) {
                		    parent['modal<?php echo $fid; ?>'].modal('hide')
                		} else {
                		    jQuery('#modal<?php echo $fid; ?>');
                		}
                        parent.location.reload();
                    }
                }
            });

		<?php endif;?>
	};
}(jQuery));
</script>

<br>
<form name="adminForm" id="adminForm" method="post">
	<div class="container-fluid">
		<div id="row-fluid">
			<div class="pull-left input-append">

				<input type="text" name="filter_search" id="filter_search" value="<?php echo $filter->clean($this->state->get('records.search'), 'alnum'); ?>" />
				<button class="btn" type="submit">
					<?php echo HTMLFormatHelper::icon('document-search-result.png');  ?>
				<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button class="btn" type="button" onclick="document.id('filter_search').value='';this.form.submit();">
					<?php echo HTMLFormatHelper::icon('eraser.png');  ?>
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<?php if(JRequest::getVar('mode') == 'form'):?>
			<div class="pull-right">
				<button type="button" class="btn" onclick="closeWindow()">
					<?php echo HTMLFormatHelper::icon('tick-button.png');  ?>
				<?php echo JText::_('CAPPLY');?></button>
				<?php endif;?>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>

	<div class="container-fluid">
	<?php if(JRequest::getVar('mode') == 'form'):?>
		<div class="row-fluid">
			<div class="span8">

	<?php endif;?>

		<table class="table">
			<thead>
				<th width="1%">
					<?php echo JText::_('CNUM'); ?>
				</th>
				<th>
					<?php echo JText::_('CTITLE')?>
				</th>
			</thead>
			<tbody>
				<?php foreach ($this->items AS $i => $item):?>
					<tr class="cat-list-row<?php echo $k = 1 - $k; ?>" id="object_<?php echo $item->id; ?>">
						<td><?php echo $this->pagination->getRowOffset($i); ?></td>
						<td><a href="javascript:void(0)" onclick="attachRecord(<?php echo $item->id?>, '<?php echo htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8')?>' , '<?php echo json_decode($item->fields,true)[$schema_id_map[$fid]] ; ?>')"><?php echo $item->title?></a></td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		<div class="pull-right"><?php echo $this->pagination->getPagesCounter(); ?></div>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>

	<?php if(JRequest::getVar('mode') == 'form'):?>
			</div>
			<div class="span4">
				<div id="recordslist">

				</div>
			</div>
		</div>
		<script type="text/javascript">
			(function($){
				var listofselected = $(parent['elementslist<?php echo JRequest::getInt('field_id')?>'])
				.children('div.alert')
				.each(function(){
					attachRecord($(this).attr('rel'), $(this).children('span').text());
				});
			}(jQuery));
		</script>
	<?php endif;?>
	</div>


	<input type="hidden" name="option" value="com_cobalt" />
	<input type="hidden" name="section_id" value="<?php echo JRequest::getInt('section_id')?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="limitstart" value="0" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
