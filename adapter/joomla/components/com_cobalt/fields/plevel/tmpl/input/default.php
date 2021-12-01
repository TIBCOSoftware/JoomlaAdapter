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

  $text = is_string($line) ? $line : $line->text;
  if ($this->value && in_array($text, $this->value))
  {
    $atr .= ' selected="selected"';
  }

  $value = htmlspecialchars($line, ENT_COMPAT, 'UTF-8');
  $text = (int)$val[0] > 0 ? JText::_('PLEVEL_PREFIX') . JText::_($val[0]) : JText::_('PLEVEL_CUSTOM');

  ?>
  <option value="<?php echo $value;?>" <?php echo $atr;?>><?php echo JText::_($text);?></option>
<?php endforeach; ?>
</select>