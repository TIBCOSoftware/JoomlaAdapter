<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die();
?>
<?php
$params = $this->params;
if ($this->value && in_array($params->get('params.view_mail', 1), $this->user->getAuthorisedViewLevels()))
{
	$fvalue = JHtml::_('content.prepare', $this->value);
	if($params->get('params.qr_code', 0))
	{
		$width = $this->params->get('params.qr_width', 60);
		$src = 'http://chart.apis.google.com/chart?chs='.$width.'x'.$width.'&cht=qr&chld=L|0&chl='.$this->value;

		echo JHtml::image($src, JText::_('E_QRCODE'), array( 'class' => 'qr-image', 'width' => $width, 'height' => $width, 'align' => 'absmiddle'));
	}

	echo $fvalue;
}

if (in_array($params->get('params.send_mail', 3), $this->user->getAuthorisedViewLevels()))
{
	if ($params->get('params.to') == 1 && !$this->value)
		return;
	if ($params->get('params.to') == 5 && !$params->get('params.custom'))
		return;

	$url_form = JRoute::_('index.php?option=com_cobalt&view=elements&layout=field&id=' . $this->id . '&section_id=' . $section->id . '&func=_getForm&record=' . $record->id . '&tmpl=component&Itemid=' . $this->request->getInt('Itemid').'&width=640');
	switch ($params->get('params.form_style', 1))
	{

	case 1 :?>
		<a href="javascript: void(0);" data-role="button" class="btn btn-primary btn-small" data-toggle="collapse" data-target="#email_form<?php echo $record->id;?>">
			<?php echo JText::_($this->params->get('params.popup_label', $this->label));?>
		</a>

		<div id="email_form<?php echo $record->id;?>" class="hide"></div>

		<script type="text/javascript">
			(function($){
				var ifrm = jQuery(document.createElement("iframe"))
					.attr({
						id: "email_frame<?php echo $this->id;?>",
						src: "<?php echo $url_form;?>",
						width: "100%",
						height: "100%",
						//height: "<?php echo $params->get('params.height', 600);?>px",
						frameborder:"0"
					});

				var box = jQuery('#email_form<?php echo $record->id;?>');

				box.on('show', function () {
					jQuery(this).html(ifrm);
				});

				window.iframe<?php echo $record->id;?>_loaded = function(height) {
					box.css('height', (height + 20) + 'px');
				}
			}(jQuery))
		</script>
	<?php break; ?>

	<?php case 2:?>
		<a class="btn btn-primary btn-small" href="#emailmodal<?php echo $this->id;?>" data-toggle="modal" role="button">
			<?php echo JText::_($this->params->get('params.popup_label', $this->label));?>
		</a>

		<div style="width:700px;" class="modal hide fade" id="emailmodal<?php echo $this->id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#215;</button>
				<h3 id="myModalLabel"><?php echo JText::_('E_SENDMSG');?></h3>
			</div>

			<div class="modal-body" style="overflow-x: hidden; max-height:500px; padding:0;">
				<iframe frameborder="0" width="100%" height="410px" src="<?php echo $url_form;?>"></iframe>
			</div>

			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('CCLOSE'); ?></button>
			</div>
		</div>
	<?php break; ?>

	<?php case 3 : ?>
		<div id="email_form<?php echo $this->id;?>">
			<h3><?php echo JText::_($this->params->get('params.popup_label', $this->label));?></h3>
			<iframe frameborder="0" src="<?php echo $url_form;?>" width="100%" height="<?php echo $params->get('params.height', 600);?>" />
		</div>
	<?php break; ?>
<?php
	}
}
?>

<?php
        if(in_array(10,JFactory::getUser()->getAuthorisedViewLevels())){
           $db = JFactory::getDbo();
           $db->setQuery('select `field_value` from #__js_res_record_values where `field_id`=77 and `record_id`='.$record->id);
                   $result1 = $db->loadColumn();

                   if(!empty($result1))
                   {
                       $db->setQuery('select `id` from #__users where `id`="'.$result1[0].'"');
                       $user_id = $db->loadColumn();
                       if(!empty($user_id))
                       {
                           $re_user = &JFactory::getUser($user_id[0]);
                           $token = $re_user->get('activation');
                           if($token)
                           {
                               echo '<p><br/><a href="'.JRoute::_('index.php?options=com_cobalt&task=ajaxmore.resendActiveEmail&id='.$record->id.'&'.JSession::getFormToken() .'=1').'">'. JText::_('BTN_RESEND_ACTIVATION_EMAIL').'</a></p>';
                           }
                       }
                   }
               }
?>

