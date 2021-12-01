<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
defined('_JEXEC') or die();
?>
<?php 
$values = (int)implode(', ', $this->values);
if($values == -1){
  $values = JText::_('PLEVEL_CUSTOM');
}else{
  $values = JText::_('PLEVEL_PREFIX') . $values;
}
echo  $values;
?>