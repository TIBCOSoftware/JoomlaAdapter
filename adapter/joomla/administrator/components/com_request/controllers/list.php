<?php
/**
 * @version     1.0.0
 * @package     com_request
 * @copyright   
 * @license     
 * @author      burtyu <ybt7755221@sohu.com> - http://burtyu.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * List controller class.
 */
class RequestControllerList extends JControllerForm
{

    function __construct() {
        $this->view_list = 'lists';
        parent::__construct();
    }

}