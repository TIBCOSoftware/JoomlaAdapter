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

<?php echo $this->params->get('params.prepend');?>

<?php if($this->params->get('params.qr_code', 0)) : 
$width = $this->params->get('params.qr_width', 60); ?>
	<img src="http://chart.apis.google.com/chart?chs=<?php echo $width;?>x<?php echo $width;?>&cht=qr&chld=L|0&chl=<?php echo urlencode(strip_tags($this->value));?>" 
			title="<?php echo JText::_('TXT_QR');?>" class="qr-image" width="<?php echo $width;?>" height="<?php echo $width;?>" align="absmiddle">
<?php endif; ?>

<div class="mapping-display"><?php echo $this->value;?></div>

<?php if($this->readmore) : ?>
	<p><?php echo $this->readmore;?></p>
<?php endif; ?>

<?php echo $this->params->get('params.append');?>
