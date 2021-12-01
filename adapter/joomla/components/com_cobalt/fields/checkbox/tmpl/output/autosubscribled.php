<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die();?>

<?php if (count($this->values) > 1): ?>

	<ul style="display:inline-block">

		<li><?php echo implode("</li><li>", $this->values);?></li>

	</ul>

<?php else : ?>

	<?php echo $this->values[0]==1?JText::_('AUTO_SUBSCRIBLED'):$this->values[0];;?>

<?php endif;?>