<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
?>
<?php
  define( '_JEXEC', 1 );
  define('JPATH_BASE', dirname(__FILE__));   // should point to joomla root
  define( 'DS', DIRECTORY_SEPARATOR );
  require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
  require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
  $mainframe =& JFactory::getApplication('site');
  $mainframe->initialise();
?>
<?php

  $SAMLResponse = $_POST["SAMLResponse"];
  if ($SAMLResponse) {
    $xml = base64_decode($SAMLResponse);
    $simplexml = new SimpleXMLElement($xml);
                  
    $simplexml->registerXPathNamespace('saml',     'urn:oasis:names:tc:SAML:2.0:assertion');
    $simplexml->registerXPathNamespace('ds',       'http://www.w3.org/2000/09/xmldsig#');
    $result = $simplexml->xpath("//saml:AttributeStatement/saml:Attribute[@Name='mail']/saml:AttributeValue");
    $email  = (string)$result[0]; 
    $result = $simplexml->xpath("//saml:AttributeStatement/saml:Attribute[@Name='organization']/saml:AttributeValue");
    $organization  = (string)$result[0]; 
    
    if ($email && $organization) {
      $_SESSION["isSuccessFromPing"] = "1";
      header("Location:".JURI::root()."index.php?option=com_cobalt&task=ajaxmore.answerPing&email=".$email."&org_name=".$organization);
      exit;
    }else{
      $_SESSION["isSuccessFromPing"] = "0";
    }
  }
?>
