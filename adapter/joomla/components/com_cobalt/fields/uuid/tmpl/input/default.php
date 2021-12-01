<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die();

?>

<input type="hidden" name="jform[fields][<?php echo $this->id;?>]" id="field_<?php echo $this->id;?>" value="<?php echo htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');?>"/>
<script type="text/javascript">
(function($){
  $("#fld-"+<?php echo $this->id;?>).hide();
})(jQuery);
</script>