<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

$app = JFactory::getApplication();
$org_id = $app->input->get("organization",0);
if($org_id):
 ?>
  <script type="text/javascript">
    _USER_ID = '<?php echo $app->input->get("id",0);?>';
    DeveloperPortal.sendCreateNotification('<?php echo $org_id;?>', DeveloperPortal.PORTAL_OBJECT_TYPE_ORGANIZATION, function(data){

    }, function(error){


    });
  </script>

<?php endif;?>



<div class="registration-complete<?php echo $this->pageclass_sfx;?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

  <div class="registration-complete-body">
    <h2 class="registration-complete-header"> <?php echo JText::_("REGISTRATION_COMPLETED_HEADER");?></h2>
    <div class="registration-complete-content">
      <?php $support=JUri::root()."index.php/support";?>
      <?php echo JText::sprintf("REGISTRATION_COMPLETED_CONTENT",$support);?>
    </div>
  </div>
</div>




