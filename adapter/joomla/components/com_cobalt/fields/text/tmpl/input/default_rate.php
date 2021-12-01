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
$unit_arr = explode("/",$this->value);

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
?>

<?php echo $this->params->get('params.prepend');?>
   <input style="width:100px" type="text" placeholder="<?php echo $this->params->get('params.mask.mask')  ?>"
   	   id="field_<?php echo $this->id;?>" value="<?php echo htmlspecialchars($unit_arr[0], ENT_COMPAT, 'UTF-8');?>"
   	<?php echo $class . $size . $disabled . $readonly . $onchange . $maxLength . $required;?>>
    <span>calls per</span>
      <select id="select_<?php echo $this->id;?>" class="add-on" style="display: inline-block;  width: 25%; height:32px;">
        <option value="1 second" <?php echo trim($unit_arr[1]) == "1 second" ? 'selected="selected"' : ""; ?>>1 second</option>
        <option value="10 seconds"  <?php echo trim($unit_arr[1]) == "10 seconds" ? 'selected="selected"' : ""; ?>>10 seconds</option>
        <option value="30 seconds" <?php echo trim($unit_arr[1]) == "30 seconds" ? 'selected="selected"' : ""; ?>>30 seconds</option>
        <option value="1 minute" <?php echo trim($unit_arr[1]) == "1 minute" ? 'selected="selected"' : ""; ?>>1 minute</option>
        <option value="5 minutes" <?php echo trim($unit_arr[1]) == "5 minutes" ? 'selected="selected"' : ""; ?>>5 minutes</option>
        <option value="30 minutes" <?php echo trim($unit_arr[1]) == "30 minutes" ? 'selected="selected"' : ""; ?>>30 minutes</option>
        <option value="1 hour" <?php echo trim($unit_arr[1]) == "1 hour" ? 'selected="selected"' : ""; ?>>1 hour</option></select>
    </select>
    <div class="field-hidden"><input type="hidden" name="jform[fields][<?php echo $this->id;?>]" value=""></div>
    <script type="text/javascript">
        jQuery(function(){
            var field = jQuery("input[name='jform[fields][<?php echo $this->id;?>]']");
            var rateVal = jQuery("#field_<?php echo $this->id;?>");
            var rateUnit = jQuery("#select_<?php echo $this->id;?>");

            fieldHidden();
            
            function fieldHidden(){
                if(rateVal.val() == ""){
                    jQuery("input[name='jform[fields][<?php echo $this->id;?>]']").remove();
                }else{
                    jQuery('.field-hidden').append(field);
                    field.val(rateVal.val() + '/' + rateUnit.val());
                }
            }

            rateVal.on("change", function(){
                fieldHidden();
            });

            rateUnit.on("change", function(){
               field.val(rateVal.val()+ '/' + rateUnit.val());
            });

        })
    </script>

<?php echo $this->params->get('params.append');?>
				
<?php if ($mask->mask_type) :?>
<script type="text/javascript">
	initMask(<?php echo $this->id;?>, "<?php echo $mask->mask;?>", "<?php echo $this->mask_type;?>");
</script>
<?php endif; ?>
