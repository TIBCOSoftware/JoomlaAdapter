<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.apiuser
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * An example custom apiuser plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  User.apiuser
 * @since       1.6
 */
class PlgUserApiuser extends JPlugin
{
  /**
   * Date of birth.
   *
   * @var    string
   * @since  3.1
   */
  private $_date = '';

  /**
   * Load the language file on instantiation.
   *
   * @var    boolean
   * @since  3.1
   */
  protected $autoloadLanguage = true;

  /**
   * Constructor
   *
   * @param   object  $subject  The object to observe
   * @param   array   $config   An array that holds the plugin configuration
   *
   * @since   1.5
   */
  public function __construct(& $subject, $config)
  {
    parent::__construct($subject, $config);
  }


  /**
   * @param   JForm    $form    The form to be altered.
   * @param   array    $data    The associated data for the form.
   *
   * @return  boolean
   * @since   1.6
   */
  public function onContentPrepareForm($form, $data)
  {
    if (!($form instanceof JForm))
    {
      $this->_subject->setError('JERROR_NOT_A_FORM');
      return false;
    }

    // Check we are manipulating a valid form.
    $name = $form->getName();

    if (!in_array($name, array('com_admin.apiuser', 'com_users.user', 'com_users.apiusers', 'com_users.registration')))
    {
      return true;
    }

    // Add the registration fields to the form.
    JForm::addFormPath(__DIR__ . '/apiusers');
    $form->loadFile('apiuser', false);
    
    $app = JFactory::getApplication();
    $user = JFactory::getUser();
    $doc = JFactory::getDocument();



    

    if ($app->isSite())
    {
      $validate = $this->params->get('register-require_validate',0);

      if(!$validate)
      {
        $form->removeField('validate', 'apiuser');
      
      }
      else if($validate == 2)
      {
        $form->setFieldAttribute('validate', 'required','required','apiuser');
        $form->setFieldAttribute('validate', 'class','required validate-is_validated','apiuser');
      }
    }

    return true;
  }


  /**
   * Method is called after user data is stored in the database
   *
   * @param   array    $data   Holds the data.
   * @param   boolean  $isnew  True if a new user is stored.
   * @param   array    $result   Holds the result.
   * @param   array    $error   Holds the errors.
   *
   * @return    boolean
   *
   * @since   3.1
   * @throws    InvalidArgumentException on invalid date.
   */
  public function onUserAfterSave($data, $isNew, $result, $error)
  {
    $isSuccessFromPing = $_SESSION["isSuccessFromPing"];
    if($isNew & !$isSuccessFromPing){

      include_once JPATH_BASE . "/includes/api.php"; 

      $orgName    =   $data['apiuser']['first-name'] . " " . $data['apiuser']["last-name"];
      $user_id    =   JArrayHelper::getValue($data, 'id', 0, 'int');
      $user       =   JFactory::getUser($user_id);
      
      if (!DeveloperPortalApi::getUserProfileId($user_id)) {
        $options = array(
            "title" => $orgName,
            "type" => "Individual",
            "user" => $user
        );

        $org_id = TibcoTibco::forceGetOrganizationId($options);

        $options2 = array(
            "org_id" => $org_id,
            "usertype" => "Manager",
            "lastName" => $data['apiuser']["last-name"],
            "firstName" => $data['apiuser']["first-name"],
            "user_id" => $user_id
        );

        TibcoTibco::createUserProfile($options2);

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $userGroupItem = new stdClass();
        $userGroupItem->user_id = $user_id;
        $userGroupItem->group_id = DeveloperPortalApi::getOrganizationIdByName("Organization ".$org_id." Manager");

        $db->insertObject("#__user_usergroup_map",$userGroupItem,'id');
        $this->bindResetPassword($user_id);
        $this->bindGuide($user_id);
      }
    }

    return true;
  }

  /**
   * Store reset password flag into database
   * @param  integer $user_id Id of the current user
   * @return void
   */
  private function bindResetPassword($user_id = 0){
    if(!$user_id)
    {
      return false;
    }

    $db = JFactory::getDbo();

    $userProfile_reset_password = new stdClass();
    $userProfile_reset_password->user_id = $user_id;
    $userProfile_reset_password->profile_key = "reset.password";
    $userProfile_reset_password->profile_value = 1;

    $db->insertObject("#__user_profiles",$userProfile_reset_password,'id');
  }

  /**
   * store guide flag into database
   * @param  integer $user_id Id of the current user
   * @return void
   */
  private function bindGuide($user_id=0){
    if(!$user_id)
    {
      return false;
    }

    $db = JFactory::getDbo();

    $userProfile_guide_flag = new stdClass();
    $userProfile_guide_flag->user_id = $user_id;
    $userProfile_guide_flag->profile_key = "guide.show";
    $userProfile_guide_flag->profile_value = 1;

    $userProfile_guide_step = new stdClass();
    $userProfile_guide_step->user_id = $user_id;
    $userProfile_guide_step->profile_key = "guide.step";
    $userProfile_guide_step->profile_value = 1;

    $db->insertObject("#__user_profiles",$userProfile_guide_flag,'id');
    $db->insertObject("#__user_profiles",$userProfile_guide_step,'id');
  }

}
