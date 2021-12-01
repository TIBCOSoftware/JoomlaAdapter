<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

defined('_JEXEC') or die();
?>

<?php
  $text = implode(', ', $this->values);
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
  $values = $text;
?>
<?php echo JText::_($values); ?>