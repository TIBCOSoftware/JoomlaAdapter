<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
defined('_JEXEC') or die();

$class = ' class="' . $this->params->get('core.field_class', 'inputbox') . ($this->required ? ' required' : NULL) . '"';
$required = $this->required ? ' required="true" ' : NULL;
$style = ' style="max-width: ' . $this->params->get('params.width', '450') . 'px"';
?>

<select name="jform[fields][<?php echo $this->id;?>]" class="elements-list" id="form_field_list_<?php echo $this->id;?>" <?php echo $required . $style;?>>
  <option value=""><?php echo JText::_($this->params->get('params.label', 'S_CHOOSEVALUE'));?></option>
<?php
$selected = ($this->value ? $this->value : $this->params->get('params.selected'));

if(!is_array($this->value)) settype($this->value, 'array');

foreach($this->values as $key => $line):
  $atr = '';
  if (is_string($line))
    $val = explode($this->params->get('params.color_separator', "^"), $line);
  if (isset($val[1]))
  {
    $atr .= ' style="color:' . $val[1] . '"';
  }

  $v = is_string($line) ? $line : $line->id;
  if ($this->value && in_array($v, $this->value))
  {
    $atr .= ' selected="selected"';
  }
  if($this->params->get('params.sql_source'))
  {
    $value = $line->id;
    $text = $line->text;
  }
  else
  {
    $value = htmlspecialchars($line, ENT_COMPAT, 'UTF-8');
    $text = JText::_($val[0]);
  }

  switch (strtolower($text)) {

    case 'manager':
      $text = 'USER_MEMBER_TYPE_PRODUCTMGR';
      break;
    case 'contact':
      $text = 'USER_MEMBER_TYPE_CONTACT_MEMBER';
      break;
    case 'member':
      $text = 'USER_MEMBER_TYPE_DEVELOPER';
      break;
    default:
      $text = 'USER_MEMBER_TYPE_DEVELOPER';
      break;
  }
  ?>
  <option value="<?php echo $value;?>" <?php echo $atr;?>><?php echo JText::_($text);?></option>

<?php endforeach; ?>
</select>

<?php if (in_array($this->params->get('params.add_value', 2), $this->user->getAuthorisedViewLevels()) && !$this->params->get('params.sql_source')):?>
  <div class="clearfix"></div>
  <p>
  <div id="variant_<?php echo $this->id;?>">
    <a id="show_variant_link_<?php echo $this->id;?>"
      rel="{field_type:'<?php echo $this->type;?>', id:<?php echo $this->id;?>, inputtype:'option', limit:1}"
      href="javascript:void(0)" onclick="Cobalt.showAddForm(<?php echo $this->id;?>)"><?php echo JText::_($this->params->get('params.user_value_label', 'Your variant'));?></a>
  </div></p>
<?php endif;?>