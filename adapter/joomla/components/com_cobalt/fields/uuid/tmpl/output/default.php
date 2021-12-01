<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die();
?>

<?php echo $this->params->get('params.prepend');?>

<?php if($this->params->get('params.qr_code', 0)) : 
$width = $this->params->get('params.qr_width', 60); ?>
	<img src="http://chart.apis.google.com/chart?chs=<?php echo $width;?>x<?php echo $width;?>&cht=qr&chld=L|0&chl=<?php echo urlencode(strip_tags($this->value));?>" 
			title="<?php echo JText::_('TXT_QR');?>" class="qr-image" width="<?php echo $width;?>" height="<?php echo $width;?>" align="absmiddle">
<?php endif; ?>

<?php echo $this->value;?>
			
<?php if($this->readmore) : ?>
	<p><?php echo $this->readmore;?></p>
<?php endif; ?>

<?php echo $this->params->get('params.append');?>