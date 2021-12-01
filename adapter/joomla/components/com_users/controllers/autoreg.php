<?php
/* Portions copyright © 2015, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Registration controller class for Users.
 *
 * @package     Joomla.Site
 * @subpackage  com_users
 * @since       1.6
 */
class UsersControllerAutoreg extends UsersController
{
	/**
	 * Method to register a user.
	 *
	 * @return  boolean  True on success, false on failure.
	 * @since   1.6
	 */
	public function register()
	{

		// Check for request forgeries.
		JSession::checkToken() or AutoregHelper::error(JText::_('JINVALID_TOKEN'));

		// If registration is disabled - Redirect to login page.
		if (JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0)
		{
			AutoregHelper::error("Registration is disabled");
			return false;
		}

		$app	= JFactory::getApplication();
		$model	= $this->getModel('Registration', 'UsersModel');
		$is_self_register = false;

		// Get the user data.
		$requestData = $this->input->post->get('jform', array(), 'array');

        $requestData['password1'] = $requestData['apiuser']['password1'];
        $requestData['password2'] = $requestData['apiuser']['password2'];

		if($requestData["apiuser"] && is_array($requestData)) {
			$is_self_register = true;
		}

		if($is_self_register){
			$requestData['email1']   = $requestData["apiuser"]["user-email"];
			$requestData['email2']   = $requestData['email1'];
			$requestData["name"]     = $requestData["apiuser"]["first-name"] . " " . $requestData["apiuser"]["last-name"];
			$requestData["username"] = $requestData['email1'];
			$userFirstName           = $requestData["apiuser"]["first-name"];
		}

		if(!$is_self_register && isset($requestData['user_group_name'])){
			$requestData['groups'] = $this->_getUserGroupId($requestData['user_group_name']);
            array_push($requestData['groups'], 2);
		} else {
		    $requestData['groups'] = array(2);
		}

		// Validate the posted data.
		$form	= $model->getForm();

		if (!$form)
		{
			
			// Get the validation messages.
			$errors	= $model->getErrors();
			
			$msgv = array();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$msgv[] = $errors[$i]->getMessage();
				} else {
					$msgv[]=$errors[$i];
				}
			}
			AutoregHelper::error($msgv ? $msgv : 500);
			return false;
		}
		$data	= $model->validate($form, $requestData);

		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();
			$msg = array();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					if($is_self_register){
						$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
					}else{
						$msg[] = $errors[$i]->getMessage();
					}
				} else {
					if($is_self_register){
						$app->enqueueMessage($errors[$i], 'warning');
					}else{
						$msg[] = $errors[$i];
					}
				}
			}
			if($is_self_register){
				// Save the data in the session.
				$app->setUserState('com_users.registration.data', $requestData);

				// Redirect back to the registration screen.
				$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration', false));
			}else{
				AutoregHelper::error($msg);
			}
			
			return false;
		} else {
            $data['params'] = array('activation_time'=>date('Y-m-d H:i:s'));
            $data['params'] =$data['params'];
        }

		// Attempt to save the data.
		$return	= $model->register($data);
		// Check for errors.
		// if ($return === false)
		// {
		// 	// Redirect back to the edit screen.
		// 	AutoregHelper::error(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()));
		// 	return false;
		// }
		
		// Flush the data from the session.
		$app->setUserState('com_users.registration.data', null);
		
		$uname = $requestData;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'))
		->from($db->quoteName('#__users'))
		->where($db->quoteName('username') . ' = "' . $requestData['username'] . '"')
		->where($db->quoteName('name') . ' = "' . $requestData['name'] . '"')
		->where($db->quoteName('email') . ' = "' . $requestData['email1'] . '"');
		$db->setQuery($query);
		
		$newuser =  $db->loadColumn(0);

		if(!$is_self_register){
			$userProfile_guide_flag = new stdClass();
			$userProfile_guide_flag->user_id = $newuser[0];
			$userProfile_guide_flag->profile_key = "guide.show";
			$userProfile_guide_flag->profile_value = 1;

			$userProfile_guide_step = new stdClass();
			$userProfile_guide_step->user_id = $newuser[0];
			$userProfile_guide_step->profile_key = "guide.step";
			$userProfile_guide_step->profile_value = 1;


			$userProfile_reset_password = new stdClass();
			$userProfile_reset_password->user_id = $newuser[0];
			$userProfile_reset_password->profile_key = "reset.password";
			$userProfile_reset_password->profile_value = 1;

			$db->insertObject("#__user_profiles",$userProfile_reset_password,'id');
			$db->insertObject("#__user_profiles",$userProfile_guide_flag,'id');
			$db->insertObject("#__user_profiles",$userProfile_guide_step,'id');
			AutoregHelper::send($newuser,'userid');
		}
		
		$newOrganizationId = $app->getUserState('com_users.registration.new_org_id',0);
		
		// Redirect to the profile screen.
		if ($return === 'adminactivate'){
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
		} 
		elseif ($return === 'useractivate')
		{
			// $this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete&id='.$newuser[0].($userFirstName?'&uname='.$userFirstName:'').($newOrganizationId?'&organization='.$newOrganizationId:''), false));
		}
		else
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
		}
	}


    /**
     * Method to register a user.
     *
     * @return  boolean  True on success, false on failure.
     * @since   1.6
     */
    public function createMember()
    {

        // Check for request forgeries.
        JSession::checkToken() or AutoregHelper::error(JText::_('JINVALID_TOKEN'));

        $app	= JFactory::getApplication();
        $model	= $this->getModel('CreateMember', 'UsersModel');

        // Get the user data.
        $requestData = $this->input->post->get('jform', array(), 'array');

        $userFirstName            = $requestData["first-name"];

        $requestData['groups'] = array(2);

        // Validate the posted data.
        $form	= $model->getForm();

        if (!$form)
        {

            // Get the validation messages.
            $errors	= $model->getErrors();

            $msgv = array();
            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if ($errors[$i] instanceof Exception)
                {
                    $msgv[] = $errors[$i]->getMessage();
                } else {
                    $msgv[]=$errors[$i];
                }
            }
            AutoregHelper::error($msgv ? $msgv : 500);
            return false;
        }
        $data	= $model->validate($form, $requestData);

        // Check for validation errors.
        if ($data === false)
        {
            // Get the validation messages.
            $errors	= $model->getErrors();
            $msg = array();
            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if ($errors[$i] instanceof Exception)
                {
                    $msg[] = $errors[$i]->getMessage();
                } else {
                    $msg[] = $errors[$i];
                }
            }
            AutoregHelper::error($msg);

            return false;
        } else {
            $data['params'] = array('activation_time'=>date('Y-m-d H:i:s'));
            $data['params'] =$data['params'];
        }

        // Attempt to save the data.
        $return	= $model->create($data);
        // Check for errors.
        // if ($return === false)
        // {
        // 	// Redirect back to the edit screen.
        // 	AutoregHelper::error(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()));
        // 	return false;
        // }

        // Flush the data from the session.
        $app->setUserState('com_users.create_member.data', null);

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('id'))
            ->from($db->quoteName('#__users'))
            ->where($db->quoteName('username') . ' = "' . $requestData['username'] . '"')
            ->where($db->quoteName('name') . ' = "' . $requestData['name'] . '"')
            ->where($db->quoteName('email') . ' = "' . $requestData['email1'] . '"');
        $db->setQuery($query);

        $newuser =  $db->loadColumn(0);

        $userProfile_guide_flag = new stdClass();
        $userProfile_guide_flag->user_id = $newuser[0];
        $userProfile_guide_flag->profile_key = "guide.show";
        $userProfile_guide_flag->profile_value = 1;

        $userProfile_guide_step = new stdClass();
        $userProfile_guide_step->user_id = $newuser[0];
        $userProfile_guide_step->profile_key = "guide.step";
        $userProfile_guide_step->profile_value = 1;

        $userProfile_reset_password = new stdClass();
        $userProfile_reset_password->user_id = $newuser[0];
        $userProfile_reset_password->profile_key = "reset.password";
        $userProfile_reset_password->profile_value = 1;

        $db->insertObject("#__user_profiles",$userProfile_reset_password,'id');
        $db->insertObject("#__user_profiles",$userProfile_guide_flag,'id');
        $db->insertObject("#__user_profiles",$userProfile_guide_step,'id');
        AutoregHelper::send($newuser,'userid');

        $newOrganizationId = $app->getUserState('com_users.registration.new_org_id',0);

        // Redirect to the profile screen.
        if ($return === 'adminactivate'){
            $this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
            $this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
        }
        elseif ($return === 'useractivate')
        {
            // $this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
            $this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete&id='.$newuser[0].($userFirstName?'&uname='.$userFirstName:'').($newOrganizationId?'&organization='.$newOrganizationId:''), false));
        }
        else
        {
            $this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
            $this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
        }
    }


	protected function _getUserGroupId($groupName='')
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'))
		->from($db->quoteName('#__usergroups'))
		->where($db->quoteName('title') . ' = "' . $groupName . '"');
		$db->setQuery($query);
		$groupid = $db->loadColumn();
		return $groupid;
	}
}


// here was ItemsStore class

class AutoregHelper
{

	public static function error($msg)
	{
		$out = array(
				'success' => 0,
				'error' => $msg
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}

	public static function send($result, $key = 'result')
	{
		$out = array(
				'success' => 1,
				$key => $result
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}
}
