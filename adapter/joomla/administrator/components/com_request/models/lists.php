<?php

/**
 * @version     1.0.0
 * @package     com_request
 * @copyright   
 * @license     
 * @author      burtyu <ybt7755221@sohu.com> - http://burtyu.com
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Request records.
 */
class RequestModelLists extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'created_by', 'a.created_by',
                'requested_by', 'a.requested_by',
                'product', 'a.product',
                'product_id', 'a.product_id',
                'status', 'a.status',
                'updated', 'a.updated',
                'plan', 'a.plan',
                'plan_id', 'a.plan_id',
                'org_id', 'a.org_id',
                'user_note', 'a.user_note',
                'admin_note', 'a.admin_note',
                'custom', 'a.custom',

            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        
		//Filtering created_by
		$this->setState('filter.created_by', $app->getUserStateFromRequest($this->context.'.filter.created_by', 'filter_created_by', '', 'string'));

		//Filtering requested_by
		$this->setState('filter.requested_by', $app->getUserStateFromRequest($this->context.'.filter.requested_by', 'filter_requested_by', '', 'string'));

		//Filtering product
		$this->setState('filter.product', $app->getUserStateFromRequest($this->context.'.filter.product', 'filter_product', '', 'string'));

		//Filtering status
		$this->setState('filter.status', $app->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '', 'string'));

		//Filtering updated
		$this->setState('filter.updated', $app->getUserStateFromRequest($this->context.'.filter.updated', 'filter_updated', '', 'string'));

		//Filtering org_id
		$this->setState('filter.org_id', $app->getUserStateFromRequest($this->context.'.filter.org_id', 'filter_org_id', '', 'string'));

		//Filtering admin_note
		$this->setState('filter.admin_note', $app->getUserStateFromRequest($this->context.'.filter.admin_note', 'filter_admin_note', '', 'string'));


        // Load the parameters.
        $params = JComponentHelper::getParams('com_request');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.status', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );
        $query->from('`#__request_list` AS a');

        

        

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.status LIKE '.$search.' )');
            }
        }

        

		//Filtering created_by

		//Filtering requested_by

		//Filtering product

		//Filtering status

		//Filtering updated

		//Filtering org_id

		//Filtering admin_note


        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
        return $items;
    }

}
