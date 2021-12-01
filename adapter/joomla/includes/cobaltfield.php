<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
?>
<?php

defined('_JEXEC') or die;
class autoCreationCobaltField
{
  
  function __construct($record_id, $field_id, $field_type, $field_label, $field_value,$value_index=0)
  {
    $this->record_id      =    $record_id;
    $this->field_id       =    $field_id;
    $this->field_type     =    $field_type;
    $this->field_label    =    $field_label;
    $this->field_value    =    $field_value;

    $this->field_key      =    'k'.md5($field_label.'-'.$field_type);
    $this->user_id        =    '129';
    $this->type_id        =    '8';
    $this->section_id     =    '4';
    $this->params         =    '';
    $this->ip             =    $_SERVER['REMOTE_ADDR'];
    $this->ctime          =    JFactory::getDate()->toSql();
    $this->value_index    =    $value_index;
  }
}



?>
