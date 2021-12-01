<?php
/**
 * @version     1.0.0
 * @package     com_request
 * @copyright   
 * @license     
 * @author      burtyu <ybt7755221@sohu.com> - http://burtyu.com
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_request', JPATH_ADMINISTRATOR);
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_request');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_request')) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">

        <ul class="fields_list">

            			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_REQUESTED_BY'); ?>:
			<?php echo $this->item->requested_by; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_PRODUCT'); ?>:
			<?php echo $this->item->product; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_PRODUCT_ID'); ?>:
			<?php echo $this->item->product_id; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_STATUS'); ?>:
			<?php echo $this->item->status; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_UPDATED'); ?>:
			<?php echo $this->item->updated; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_PLAN'); ?>:
			<?php echo $this->item->plan; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_PLAN_ID'); ?>:
			<?php echo $this->item->plan_id; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_ORG_ID'); ?>:
			<?php echo $this->item->org_id; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_USER_NOTE'); ?>:
			<?php echo $this->item->user_note; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_ADMIN_NOTE'); ?>:
			<?php echo $this->item->admin_note; ?></li>
			<li><?php echo JText::_('COM_REQUEST_FORM_LBL_LIST_CUSTOM'); ?>:
			<?php echo $this->item->custom; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_request&task=list.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_REQUEST_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_request')):
								?>
									<a href="javascript:document.getElementById('form-list-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_REQUEST_DELETE_ITEM"); ?></a>
									<form id="form-list-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_request&task=list.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="option" value="com_request" />
										<input type="hidden" name="task" value="list.remove" />
										<?php echo JHtml::_('form.token'); ?>
									</form>
								<?php
								endif;
							?>
<?php
else:
    echo JText::_('COM_REQUEST_ITEM_NOT_LOADED');
endif;
?>
