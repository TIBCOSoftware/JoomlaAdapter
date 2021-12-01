<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
?>
<style>
  .tabbable ul li{
    display: none;
  }
  .tabbable .nav li a{
    color: #000;
      display: block;
  }
  .tabbable .nav li a[href="#tab-meta"], .tabbable .nav li a[href="#tab-special"]{
    display: none;
  }
  .view-form .container > .row-fluid #content > form > ul li a{
    color: #000;
  }
</style>
<ul class="asg-create-app-guide">
  <li class="active"><div>1</div><div><p><?php echo JText::_('CREATE_API_STEP1')?></p><p><?php echo JText::_('CREATE_API_STEP1_DES')?></p></div></li>
  <li><div>2</div><div><p><?php echo JText::_('CREATE_API_STEP2')?></p><p><?php echo JText::_('CREATE_API_STEP2_DES')?></p></div></li>
  <li><div>3</div><div><p><?php echo JText::_('CREATE_API_STEP3')?></p><p><?php echo JText::_('CREATE_API_STEP3_DES')?></p></div></li>
</ul>
