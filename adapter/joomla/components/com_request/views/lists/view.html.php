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

jimport('joomla.application.component.view');

/**
 * View class for a list of Request.
 */
class RequestViewLists extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
        protected $params;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
        $app                = JFactory::getApplication();

        $this->state		= $this->get('State');
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->params           = $app->getParams('com_request');
        $this->statusArr        = array( 1 => 'Pending', 2 => 'Approved', 3 => 'Rejected', 4 => 'Cancelled' );
        $this->user             = JFactory::getUser();
        $session                = JFactory::getSession();
        $this->nowState         = $session->get('select');
        $this->count            = count($this->items);
        if ( $this->user->id != 129 ) {
        	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
        }
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {;
        	throw new Exception(implode("\n", $errors));
        }

        $this->_prepareDocument();
        parent::display($tpl);
	}


	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_REQUEST_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}



        public function showTime($time) {
            $timeStr = strtotime($time);
            return date('d M Y',$timeStr);
        }

        public function getClass($id){
            switch ($id) {
                case '1' :
                            $res = 'label-pending';
                            break;
                case '2' :
                            $res = 'label-approved';
                            break;
                case '3' :
                            $res = 'label-rejected';
                            break;
                case '4' :
                            $res = 'label-cancelled';
                            break;
                default  :
                            $res = 'label-pending';
                            break;
            }
            return $res;
        }

        public function getOrgInfo($org_id){
            $model = $this->getModel('Lists', 'RequestModel', array('ignore_request' => true));
            return $model->getOrgInfo($org_id);
        }

        public function showJson($json, $key){
            $obj = json_decode($json);
            return $obj->$key;
        }

         public function showLevel($plan_id) {
	           $db = JFactory::getDbo();
	           $sql = 'SELECT `field_value` FROM `#__js_res_record_values` WHERE `field_id` = 39 AND `record_id` = '.$plan_id;
	           $db->setQuery($sql);
	           $result = $db->loadObject();
	           return $result->field_value;
         }
}
