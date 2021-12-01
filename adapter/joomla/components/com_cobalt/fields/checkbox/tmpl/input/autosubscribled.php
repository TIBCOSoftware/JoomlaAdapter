<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die();
$k = 0;
$cols = $this->params->get('params.columns', 2);
$span = array(1 => 12, 2 => 6, 3 => 4, 4 => 3, 6 => 2);
?>
<?php if ($this->params->get('params.total_limit')):?>
	<div class="small"><?php echo JText::sprintf('F_OPTIONSLIMIT', $this->params->get('params.total_limit'));?></div>
	<br>
<?php endif; ?>
<style>
#elements-list-<?php echo $this->id;?> .row-fluid
{
	display: table;
}
</style>

<div id="elements-list-<?php echo $this->id;?>">
	<?php if($this->values):?>
		<?php foreach($this->values as $key => $line): ?>
			<?php
				if(is_string($line))
					$val = explode($this->params->get('params.color_separator', "^"), $line);
				$sel = '';
				$s = "";
				if (isset($val[1]))
				{
					$s .= ';color: ' . $val[1];
				}
				$text = is_string($line) ? $line : $line->text;
				if ($this->value && in_array($text, $this->value))
				{
					$sel = ' checked="checked"';
				}
				if($this->params->get('params.sql_source'))
				{
					$value = $line->id;
					$text = $line->text;
				}
				else
				{
					$value = htmlspecialchars($line, ENT_COMPAT, 'UTF-8');
					$text = $val[0];
				}
			?>
			<?php if($k % $cols == 0):?>
				<div class="row-fluid">
			<?php endif;?>

			<div class="span<?php echo $span[$cols]?>">
				<label style="<?php echo $s;?>" class="checkbox">
					<input type="checkbox" value="<?php echo $value;?>" name="jform[fields][<?php echo $this->id;?>][]" id="field_<?php echo $this->id;?>_<?php echo $key;?>"
						<?php echo $sel;?> onClick="Cobalt.countFieldValues(jQuery(this), <?php echo $this->id;?>, <?php echo $this->params->get('params.total_limit', 0);?>, 'checkbox')"/>
					<label for="field_<?php echo $this->id;?>_<?php echo $key;?>"><?php echo $text==1?JText::_('AUTO_SUBSCRIBLED'):JText::_($text);?></label>
				</label>
			</div>

			<?php if($k % $cols == ($cols - 1)):?>
				</div>
			<?php endif; $k++;?>
		<?php endforeach;?>


		<?php if($k % $cols != 0):?>
			</div>
		<?php endif;?>
	<?php endif;?>
</div>
