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
<style>
div.btn-group[data-toggle=buttons-radio] input[type=radio] {
  display:    block;
  position:   absolute;
  top:        0;
  left:       0;
  width:      100%;
  height:     100%;
  opacity:    0;
}
</style>

<div class="btn-group" data-toggle="buttons-radio">
    <input type="radio" name="jform[fields][<?php echo $this->id?>]" value="1" <?php echo ($this->value == 1 ? ' checked="checked"' : NULL);?> style="diplay: none;"/>
	<button id="bool-y<?php echo $this->id;?>" type="button" class="btn<?php echo $this->value == 1 ? ' active btn-primary' : NULL ?>">
	 	<?php if(in_array($this->params->get('params.view_what', 'both'), array('both', 'icon'))):?>
	 		<?php echo HTMLFormatHelper::icon($this->params->get('params.icon_true'))?>
	 	<?php endif;?>
		<?php echo $this->params->get('params.true')?>
	</button>
	<button id="bool-n<?php echo $this->id;?>" type="button" class="btn<?php echo $this->value == -1 ? ' active btn-primary' : NULL ?>">
	 	<?php if(in_array($this->params->get('params.view_what', 'both'), array('both', 'icon'))):?>
	 		<?php echo HTMLFormatHelper::icon($this->params->get('params.icon_false'))?>
	 	<?php endif;?>
		<?php echo $this->params->get('params.false')?>
	</button>
    <input type="radio" name="jform[fields][<?php echo $this->id?>]" <?php echo ($this->value == -1 ? ' checked="checked"' : NULL);?> value="-1" style="display: none;"/>
</div>

<script>
	DeveloperPortal.yesno('#bool-y<?php echo $this->id;?>', '#bool-n<?php echo $this->id;?>');
</script>