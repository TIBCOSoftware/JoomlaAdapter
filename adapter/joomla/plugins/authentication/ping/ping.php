<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * @package     Ping.Plugin
 * @subpackage  Authentication.ping
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Ping Authentication plugin
 *
 * @package     Ping.Plugin
 * @subpackage  Authentication.ping
 * @since       1.5
 */
class plgAuthenticationPing extends JPlugin
{
  /**
   * This method should handle any authentication and report back to the subject
   *
   * @access  public
   * @param   array  Array holding the user credentials
   * @param   array  Array of extra options
   * @param   object  Authentication response object
   * @return  boolean
   * @since 1.5
   */
  public function onUserAuthenticate($credentials, $options, &$response)
  {
    if ($options["action"] != "core.login.site") {
      return true;
    }
    $response->type = 'Ping';
    $username = $credentials['username'];
    $isSuccessFromPing = $_REQUEST["isSuccessFromPing"];
    
    $db = JFactory::getDbo();
    $sql = 'SELECT * FROM `#__users` where username="'.$username.'"';
    $db->setQuery($sql);
    $record = $db->loadObject();
    
    //1. user valid from ping 
    //2. user doesnot existed or userexisted and password is empty 
    if (!strstr($username, '@')) {
      $response->status = JAuthentication::STATUS_FAILURE;
      $response->error_message = '';
      $response->email         = $username;
      return false;
    }else if ($isSuccessFromPing=="1" && (empty($record) || ($record->id && $record->password==""))){
      $response->status = JAuthentication::STATUS_SUCCESS;
      $response->error_message = '';
      $response->email         = $username;
      return true;
    }
  }
}
