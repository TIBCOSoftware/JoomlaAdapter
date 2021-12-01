<?php
  /* @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */
?>
<?php

defined('_JEXEC') or die;
 
/**
 * TIBCO OpenAPI Library autoload plugin
 */
class plgSystemTibco extends JPlugin
{
    /**
     * Register TIBCO OpenAPI library automatically.
     *
     * return  void
     */
    public function onAfterInitialise()
    {
        JLoader::registerPrefix('Tibco', JPATH_LIBRARIES . '/tibco');
    }

    
}