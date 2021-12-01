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
<div class="schema-map">

</div>
<button type="button" class="btn addSchemaMap">Add</button>
<input type="hidden" placeholder="<?php echo $this->params->get('params.show_mask', 1) ? $this->params->get('params.mask.mask') : NULL; ?>" name="jform[fields][<?php echo $this->id;?>]"
	   id="field_<?php echo $this->id;?>" value="<?php echo htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');?>"
	<?php echo $class . $size . $disabled . $readonly . $onchange . $maxLength . $required;?>>
<?php echo $this->params->get('params.append');?>

<script>
    jQuery(function () {
        var SCHEMA_MAP_BODY = '<div class="schema-map-body">' +
            '<input type="text" name="uri" class="span12" placeholder="Input URI" style="width: 80%; margin-right: 5px; margin-bottom:10px;" aria-invalid="false" value="${uriVal}" />' +
            '<button type="button" class="btn btn-danger removeSchemaMap" style="margin-bottom:10px;">Delete</button> ' +
            '<textarea name="schemaMapXML" class="mapping-textarea" placeholder="Input SchemaMap XML" aria-invalid="false">${schemaMapXMLVal}</textarea> </div>';
        var schemaMap = jQuery('input[name="jform[fields][208]"]');


        jQuery('.addSchemaMap').on('click', function () {
            var schemaMapBody = jQuery(SCHEMA_MAP_BODY.replace(/\$\{uriVal\}/, '').replace(/\$\{schemaMapXMLVal\}/, ''));
            jQuery('.schema-map').append(schemaMapBody);
            jQuery(".removeSchemaMap").on('click', function () {
                jQuery(this).parent().remove();
            });
        });

        decodeSchemaMap();

        function decodeSchemaMap() {
            schemaMap.val(DeveloperPortal.java7DecodeURIComponent(schemaMap.val()));
        }

        var schemaMapVal = jQuery.parseJSON(schemaMap.val());
        var sSchemaMap = '';

        for (var key in schemaMapVal) {
            sSchemaMap += SCHEMA_MAP_BODY.replace(/\$\{uriVal\}/, key).replace(/\$\{schemaMapXMLVal\}/, schemaMapVal[key]);
        }
        jQuery('.schema-map').append(jQuery(sSchemaMap));

    });




</script>
				
<?php if ($mask->mask_type) :?>
<script type="text/javascript">
	initMask(<?php echo $this->id;?>, "<?php echo $mask->mask;?>", "<?php echo $this->mask_type;?>");
</script>
<?php endif; ?>