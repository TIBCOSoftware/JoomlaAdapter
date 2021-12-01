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
?>

<?php echo $this->params->get('params.prepend');?>

    <div id="show-mapper">
        <button type="button" class="btn show-mapper">Show</button>
        <div id="mapper2" style="margin: 10px 0;">

        </div>
        <div class="mapperBtn">
            <button type="button" class="btn visualMapperXsltGenerator" style="left:0; top:0;">Show Transformation</button>
        </div>
    </div>
    <div class="clearfix"></div>

    <textarea readonly="readonly" class="visualMapperOutputDisplay mapping-textarea" placeholder="<?php echo $this->params->get('params.show_mask', 1) ? $this->params->get('params.mask.mask') : NULL; ?>" name="jform[fields][<?php echo $this->id;?>]"
	   id="field_<?php echo $this->id;?>"
	<?php echo $class . $size . $disabled . $readonly . $onchange . $maxLength . $required;?>><?php echo $this->value;?></textarea>
<?php echo $this->params->get('params.append');?>
				
<?php if ($mask->mask_type) :?>
<script type="text/javascript">
	initMask(<?php echo $this->id;?>, "<?php echo $mask->mask;?>", "<?php echo $this->mask_type;?>");
</script>
<?php endif; ?>