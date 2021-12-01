<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$class[] = $this->params->get('core.field_class', 'inputbox');
$required = NULL;

if ($this->required)
{
	$class[] = 'required';
	$required = ' required="true" ';
}

$class = ' class="' . implode(' ', $class) . '"';
$size = $this->params->get('params.size') ? ' style="width:' . $this->params->get('params.size') . '"' : '';
$maxLength = $this->params->get('params.maxlength') ? ' maxlength="' . (int)$this->params->get('params.maxlength') . '"' : '';
$readonly = ((string)$this->params->get('readonly') == 'true') ? ' readonly="readonly"' : '';
$disabled = ((string)$this->params->get('disabled') == 'true') ? ' disabled="disabled"' : '';
$onchange = $this->params->get('onchange') ? ' onchange="' . (string)$this->params->get('onchange') . '"' : '';

$mask = $this->params->get('params.mask', 0);

$unit_arr = explode("/",$this->value);
?>

<?php echo $this->params->get('params.prepend');?>

<input type="text" placeholder="<?php echo $this->params->get('params.mask.mask')  ?>"
	   id="field_<?php echo $this->id;?>" value="<?php echo htmlspecialchars($unit_arr[0], ENT_COMPAT, 'UTF-8');?>"
	<?php echo $class . $size . $disabled . $readonly . $onchange . $maxLength . $required;?>>
     <span>calls per</span>
    <select id="select_<?php echo $this->id;?>" class="add-on" style="display: inline-block; width: 25%; height:32px;">
        <option value="1 day" <?php echo trim($unit_arr[1]) == "1 day" ? 'selected="selected"' : ""; ?>>1 day</option>
        <option value="1 week" <?php echo trim($unit_arr[1]) == "1 week" ? 'selected="selected"' : ""; ?>>1 week</option>
        <option value="30 days" <?php echo trim($unit_arr[1]) == "30 days" ? 'selected="selected"' : ""; ?>>30 days</option>
        <option value="90 days" <?php echo trim($unit_arr[1]) == "90 days" ? 'selected="selected"' : ""; ?>>90 days</option>
        <option value="1 year" <?php echo trim($unit_arr[1]) == "1 year" ? 'selected="selected"' : ""; ?>>1 year</option>
    </select>
    <input type="hidden" class="field_<?php echo $this->id;?>" name="jform[fields][<?php echo $this->id;?>]" value="">
    <script type="text/javascript">
        jQuery(function(){
            var field = jQuery("input[name='jform[fields][<?php echo $this->id;?>]']");
            var quotaVal = jQuery("#field_<?php echo $this->id;?>");
            var quotaUnit = jQuery("#select_<?php echo $this->id;?>");
            field.val(quotaVal.val() + '/' + quotaUnit.val());
            quotaVal.on("change", function(){
                field.val(quotaVal.val() + '/' + quotaUnit.val());
            });

            quotaUnit.on("change", function(){
                field.val(quotaVal.val()+ '/' + quotaUnit.val());
            });

        })
    </script>
<?php echo $this->params->get('params.append');?>

<?php if ($mask->mask_type) :?>
<script type="text/javascript">
	initMask(<?php echo $this->id;?>, "<?php echo $mask->mask;?>", "<?php echo $this->mask_type;?>");
</script>
<?php endif; ?>