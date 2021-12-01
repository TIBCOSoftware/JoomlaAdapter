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
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {

        // Initialise variables.
        $app = JFactory::getApplication();

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_request');
        $this->setState('params', $params);
        

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $where = '';
        $order = '';
        $user = JFactory::getUser();
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );

        $query->from('`#__request_list` AS a');
        $session = JFactory::getSession();
        $stateSession = $session->get('select');
        $query->select('user.name AS username, user.email AS email');
        $query->join('LEFT', '#__users AS user ON user.id = a.created_by');
        $query->select('o.title AS org_title');
        $query->join('LEFT', '#__js_res_record AS o ON o.id = a.org_id');
        if( !in_array(8, $user->groups) ){
                $query->where('a.created_by ='.$user->id);
                $query->order('id DESC');
        } else {
                $query->order('org_id DESC, status ASC, id DESC');
        }
        if( isset($stateSession) && $stateSession != 0 ) {
                $query->where('a.status="'.$stateSession.'"');
        }
        
        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $org_id = $this->getOrgId($search);
            if ( empty($org_id) )
                $query->where('a.org_id = -1');
            else
                $query->where('a.org_id IN ('.$org_id.')');
            
            /*
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                
            }*/
        }
        
        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems() {
        return parent::getItems();
    }
    
    private function getOrgId($title){
        $db = JFactory::getDbo();
        $sql = 'SELECT id FROM `#__js_res_record` WHERE `title` LIKE "%'.$db->escape($title).'%"';
        $db->setQuery($sql);
        $result = $db->loadObjectList();
        $org_id = '';
        foreach( $result as $v ) {
            $org_id .= $v->id.',';
        }
        $org_ids = substr($org_id, 0, -1);
        return $org_ids;
    }
    
    public function getOrgInfo($id){
        $session = JFactory::getSession();
        $orgSession = $session->get('org_'.$id);
        if ( empty($orgSession) ) {
           $db = JFactory::getDbo();
            $sql = 'SELECT field_type, field_label, field_value, value_index FROM `#__js_res_record_values` WHERE `record_id` ='.$id;
            $db->setQuery($sql);
            $resObj = $db->loadObjectList();
            $contactArr = array();
            $email = '';
            foreach($resObj as $v){
            if ( $v->field_label == 'Contact details' || $v->field_label == 'Contact' ){
                    $contactArr[$v->value_index]= $v->field_value;
                }
                if ( $v->field_label == 'Email' ){
                    $email = $v->field_value;
                }
            }
            $street = $contactArr['0'].' '.$contactArr['city'];
            $country = $contactArr['state'].' '.$contactArr['country'];
            $orgInfo = array( 'street' => $street, 'country' => $country, 'email' => $email ); 
            $name = 'org_'.$id;
            $session->set($name,$orgInfo);
        } else {
            $orgInfo = $orgSession;
        }
        return $orgInfo;
    }
    
}
