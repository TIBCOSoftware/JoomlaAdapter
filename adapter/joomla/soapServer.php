<?php
if (version_compare(PHP_VERSION, '5.3.10', '<'))
{
	die('Your host needs to use PHP 5.3.10 or higher to run this version of Joomla!');
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

// This function is a duplicate of the one in the TibcoTibco class.
// The reason why we have a ducplicate here is that the inclusiong of the JPATH_LIBRARIES . '/tibco/tibco/tibco.php' file will fail the application.
function getWSDLSubfolder() {

    $ret = '';
    $db = JFactory::getDbo();
    $sql = 'SELECT params FROM #__js_res_fields WHERE id=127';
    $db->setQuery($sql);

    if($result = $db->loadObject()) {

        $wsdl_params = json_decode($result->params);
        $ret = $wsdl_params->params->subfolder;
    }

    return $ret;
}

$wsdl_subfolder = getWSDLSubfolder();
// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;
$fileName = JPATH_BASE . "/uploads/$wsdl_subfolder/wsdlName.json";
$handle = fopen($fileName, 'r');
$name = fread($handle,filesize($fileName));
fclose($handle);
// Instantiate the application.
$app = JFactory::getApplication('site');
$objSoapServer = new SoapServer(JPATH_BASE ."/uploads/$wsdl_subfolder/". $name);
$objSoapServer->handle();

