<?php
/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
if (version_compare(PHP_VERSION, '5.3.10', '<'))
{
	die('Your host needs to use PHP 5.3.10 or higher to run this version of Joomla!');
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);
//Joomla base directory path.
define('JPATH_BASE', substr( __DIR__, 0, strpos( __DIR__, 'components')-1 ) );

require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');

$path = substr( JUri::base(), 0, strpos(JUri::base(), 'components')-1 );

$url = $path . '/templates/' . JFactory::getApplication()->getTemplate('template')->template;
//Policy url path.
define('JOOMLA_TMPL_PATH', $url);

$tmpl_path = substr( JUri::base(), 0, strpos(JUri::base(), 'partials')-1 );
//Policy js directory path.
define('TEXTAREA_OUTPUT_TMPL_PATH', $tmpl_path );