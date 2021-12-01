<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.openapi
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
require_once JPATH_BASE . "/includes/api.php";
require_once JPATH_BASE . "/components/com_cobalt/controllers/ajaxmore.php";
// Getting params from template
$params = JFactory::getApplication()->getTemplate(true)->params;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;
$menus = $app->getMenu();
$currentMenu = $menus->getActive();

// Access Level View Permissions
$title = $currentMenu->params->page_title?$currentMenu->params->page_title:$currentMenu->title;
$doc->title = $title;

    /**
     *      * For overide the browser's title
     * @var [type]
     */
    $menu = $app->getMenu();
    if($active = $menu->getActive())
    {
        $title = $active->params->get('page_title');
        $doc->title = $title ? $title : $active->title;
    }



// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$titlePosition = $app->getCfg('sitename_pagetitles');
$sitename = $app->getCfg('sitename');

$title = $currentMenu->params->page_title?$currentMenu->params->page_title:$currentMenu->title;

//skip adding title if this is product detials page
if($view == "records"){
    $doc->title = $title;
}

$menu = $app->getMenu();
if($active = $menu->getActive())
{
   $title = $active->params->get('page_title');
   //$doc->title = $title ? $title : $active->title;
}

if ((int)$titlePosition===1) {
	$doc->title = $sitename.' - '.$doc->title;
}

if($task == "edit" || $layout == "form" )
{
  $fullWidth = 1;
}
else
{
  $fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
// Add Stylesheets
$doc->addStyleSheet('templates/'.$this->template.'/css/template.css');
$doc->addStyleSheet('templates/'.$this->template.'/css/home.css');
$doc->addStyleSheet('templates/'.$this->template.'/css/products.css');
$doc->addStyleSheet('templates/'.$this->template.'/css/support.css');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Add current user information
$user = JFactory::getUser();
$_orgid = DeveloperPortalApi::getUserOrganization();
$orgid = $_orgid[0];
$orgname = '';
$orguri = '';


DeveloperPortalApi::protalEngineMessage();

if(isset($orgid)):
    $org = DeveloperPortalApi::getRecordById($orgid);
    $orgname = $org->title;
    $orguri = JURI::root()."index.php/userorganizations/item/".$orgid;
endif;
// Adjusting content width
if ($this->countModules('position-7') && $this->countModules('position-8'))
{
  $span = "span6";
}
elseif ($this->countModules('position-7') && !$this->countModules('position-8'))
{
  $span = "span9";
}
elseif (!$this->countModules('position-7') && $this->countModules('position-8'))
{
  $span = "span9";
}
else
{
  $span = "span12";
}

// Logo file or site title param
if ($this->params->get('logoFile'))
{
  $logo = '<img src="'. JURI::root() . $this->params->get('logoFile') .'" alt="'. $sitename .'" />';
}
elseif ($this->params->get('sitetitle'))
{
  $logo = '<span class="site-title" title="'. $sitename .'">'. htmlspecialchars($this->params->get('sitetitle')) .'</span>';
}
else
{
  $logo = '<span class="site-title" title="'. $sitename .'">'. $sitename .'</span>';
}
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/public.js', 'text/javascript');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/vendor/jquery.als-1.1.min.js', 'text/javascript');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/vendor/js-xss/xss-0.1.20.js', 'text/javascript');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/vendor/uuid.core.js', 'text/javascript');

$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/developer_portal.js', 'text/javascript');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/support.js', 'text/javascript');

if(isset($doc->_scripts[juri::base(true)."/media/jui/js/jquery.min.js"])){
	$jui = $doc->_scripts[juri::base(true)."/media/jui/js/jquery.min.js"];
	unset($doc->_scripts[juri::base(true)."/media/jui/js/jquery.min.js"]);
	$doc->_scripts = array_merge(array_slice($doc->_scripts,0,0),array(juri::base(true)."/media/jui/js/jquery.min.js"=>$jui),array_slice($doc->_scripts,0));
}

// if(isset($doc->_scripts[juri::base(true)."/templates/" . $this->template . "/js/vendor/jqueryui/jquery-ui-1.10.3.custom.min.js"])){
//   $jui = $doc->_scripts[juri::base(true)."/templates/" . $this->template . "/js/vendor/jqueryui/jquery-ui-1.10.3.custom.min.js"];
//   unset($doc->_scripts[juri::base(true)."/templates/" . $this->template . "/js/vendor/jqueryui/jquery-ui-1.10.3.custom.min.js"]);
//   $doc->_scripts = array_merge(array_slice($doc->_scripts,0,2),array(juri::base(true)."/templates/" . $this->template . "/js/vendor/jqueryui/jquery-ui-1.10.3.custom.min.js"=>$jui),array_slice($doc->_scripts,2));

// }


function getUuid($prefix = ''){
    $chars  =  md5(uniqid(mt_rand(), true));
    $uuid   =  substr ( $chars ,0,8).'-';
    $uuid  .=  substr ( $chars ,8,4).'-';
    $uuid  .=  substr ( $chars ,12,4).'-';
    $uuid  .=  substr ( $chars ,16,4).'-';
    $uuid  .=  substr ( $chars ,20,12);
    return $prefix.$uuid;
}

$comEmail = JComponentHelper::getComponent('com_emails');
$oauthState = $comEmail->params->get('enable_oauth');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script type="text/javascript">
      // Set a global variable for storing the context path of the current
      // Joomla deployment
      var _SESSION_ID = '<?php echo JSession::getInstance(null,null)->getId(); ?>',
          _USER_ID = '<?php echo JFactory::getUser()->id; ?>',
            GLOBAL_CONTEXT_PATH = '<?php echo JURI::root(); ?>',
            GENERIC_ERROR_MESSAGE = '<?php echo JText::_("GENERIC_ERROR_MESSAGE"); ?>',
            PORTAL_UNREACHABLE_ERROR_MESSAGE = '<?php echo JText::_("PORTAL_UNREACHABLE_ERROR_MESSAGE"); ?>',
            PORTAL_TIMEOUT_ERROR_MESSAGE = '<?php echo JText::_("PORTAL_TIMEOUT_ERROR_MESSAGE"); ?>',
            NO_VALID_JSON_DATA = '<?php echo JText::_("NO_VALID_JSON_DATA"); ?>',
            SUPPORT_INPUT_QUESTION = '<?php echo JText::_("SUPPORT_INPUT_QUESTION"); ?>',
			SUPPORT_INPUT_EMAIL = '<?php echo JText::_("SUPPORT_INPUT_EMAIL"); ?>',
			SUPPORT_USER_NAME = '<?php echo JFactory::getUser()->name;?>',
			SUPPORT_USER_EMAIL = '<?php echo JFactory::getUser()->email; ?>',
            SUPPORT_PREPOPULATED_TEXT_UUID = '<?php echo JText::_("SUPPORT_PREPOPULATED_TEXT_UUID"); ?>',
            ARCHIVE_FAILED = '<?php echo JText::_("ARCHIVE_FAILED"); ?>',
            DELETE_FAILED = '<?php echo JText::_("DELETE_FAILED"); ?>',
            DELETE_SUCCESS = '<?php echo JText::_("CMSG_RECDELETEDOK"); ?>',
            FORM_TOKENS_DIFFERENT = '<?php echo JText::_("FORM_TOKENS_DIFFERENT"); ?>',
            DISABLE_KEYS_FAILED = '<?php echo JText::_("DISABLE_KEYS_FAILED"); ?>',
            FAILED_TO_DEACTIVATE_USER_AFTER_ARCHIVE_USERPROFILE = '<?php echo JText::_("FAILED_TO_DEACTIVATE_USER_AFTER_ARCHIVE_USERPROFILE"); ?>',
            INVALID_SUBSCRIPTION_END_DATE = '<?php echo JText::_("INVALID_SUBSCRIPTION_END_DATE"); ?>',
            ERROR_GETTING_API_KEY = '<?php echo JText::_("ERROR_GETTING_API_KEY"); ?>',
            PORTAL_RESP_SUMMARY_POSTFIX_UUID = '<?php echo JText::_("PORTAL_RESP_SUMMARY_POSTFIX_UUID"); ?>',
            PORTAL_UUID = '<?php echo getUuid();?>',
            SUPPORT_PAGE_URL = GLOBAL_CONTEXT_PATH + 'index.php/support';
  </script>
  <jdoc:include type="head" />
  <?php
  // Use of Google Font
  if ($this->params->get('googleFont'))
  {
  ?>
    <link href='<?php echo JURI::getInstance()->getScheme(); ?>://fonts.googleapis.com/css?family=<?php echo $this->params->get('googleFontName');?>' rel='stylesheet' type='text/css' />
<!--    <style type="text/css">-->
<!--      h1,h2,h3,h4,h5,h6,.site-title{-->
<!--        font-family: '--><?php //echo str_replace('+', ' ', $this->params->get('googleFontName'));?><!--', sans-serif;-->
<!--      }-->
<!--    /*</style>*/-->
  <?php
  }
  ?>
  <?php
  // Template color
  if ($this->params->get('templateColor'))
  {
  ?>
  <style type="text/css">
    body.site
    {
      border-top: 0px solid <?php echo $this->params->get('templateColor');?>;
      background-color: <?php echo $this->params->get('templateBackgroundColor');?>
    }
    a
    {
      color: <?php echo $this->params->get('templateColor');?>;
    }
    .navbar-inner, .nav-list > .active > a, .nav-list > .active > a:hover, .dropdown-menu li > a:hover, .dropdown-menu .active > a, .dropdown-menu .active > a:hover, .nav-pills > .active > a, .nav-pills > .active > a:hover,
    .btn-primary
    {
    }
    .navbar-inner
    {
      -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
      -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
      box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
    }

  </style>
  <?php
  }
  ?>
  <!--[if lt IE 9]>
    <script src="<?php echo $this->baseurl ?>/media/jui/js/html5.js"></script>
  <![endif]-->
</head>

<body class="site <?php echo $option
  . ' view-' . $view
  . ($layout ? ' layout-' . $layout : ' no-layout')
  . ($task ? ' task-' . $task : ' no-task')
  . ($itemid ? ' itemid-' . $itemid : '')
  . ($params->get('fluidContainer') ? ' fluid' : '');
?>">
  <!-- Body -->
<style>
.copyright p a
{
	color:#fff;
	margin-left:0px;
}
.get-started-page .trial-guide{
	display:none;
}
.get-started-page .featured-items{
	margin-top:0;
}
</style>
    <a id="top" style="height:0px;"></a>
  <div class="body">
      <div class="header-wrapper">
          <div class="container">
            <div class="header">
                <nav class="top-nav">
                    <?php if ($this->countModules('position-4')) : ?>
                    <div class="header-login<?php if(!$user->guest){echo ' registered-user';}else{echo ' guest-user';}?>">
                        <?php if($user->guest):?>
                         <input type="button" value="Sign in" id="login-action-button" />
                         <?php endif;?>
                         <jdoc:include type="modules" name="position-4" style="none" />
                    </div>
                    <?php endif; ?>
                </nav>
              <div class="header-inner clearfix">
                <!-- <a class="logo" href="<?php echo $this->baseurl; ?>"><div class="logo-img"></div></a> -->
                <a class="logo" href="<?php
                                                  if($this->params->get('goHere')){
                                                    echo $this->params->get('goHere');
                                                  }else{
                                                    echo $this->baseurl;
                                                  }
                                                ?>">

                  <?php echo $logo;?> <?php if ($this->params->get('sitedescription')) { echo '<div class="site-description">'. htmlspecialchars($this->params->get('sitedescription')) .'</div>'; } ?>
                </a>
                  <nav class="main-nav">
                  		<ul class="nav pull-left">
                  		</ul>
                  </nav>
                  <div class="header-search pull-right">
                    <jdoc:include type="modules" name="position-0" style="none" />
                </div>
              </div>
            </div>
          </div>
      </div>
      <?php if ($this->countModules('position-1')) : ?>
      <div class="navigation-wrapper">
          <div class="container">
              <div class="navigation">
  				<?php
  				// Display position-1 modules
  				$this->navmodules = JModuleHelper::getModules('position-1');
  				foreach ($this->navmodules as $navmodule)
  				{
  					$output = JModuleHelper::renderModule($navmodule, array('style' => 'none'));
  					$params = new JRegistry;
  					$params->loadString($navmodule->params);
					$alias = $app->getMenu()->getItem(140)->alias;

					if ($oauthState==3) {
						$output = preg_replace('#<li class="item-126">(.*?)</li>#', '', $output);
					}
  					echo str_replace($alias,$alias.'/item/'.$orgid,$output);
  				}
  				?>
              </div>
          </div>
      </div>
      <?php endif; ?>
      <div class="banner">
                  <div class="banner-title container">
<!--                      <img class="banner-img" src="templates/--><?php //echo $this->template; ?><!--/css/banner.png">-->
                      <h1 id="banner-title-heading">
                      </h1>
                  </div>
      </div>
    <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : '');?>">
      <!-- Header -->
      <jdoc:include type="modules" name="banner" style="xhtml" />
      <div class="row-fluid">
        <?php if ($this->countModules('position-8')) : ?>
        <!-- Begin Sidebar -->
        <div id="sidebar" class="span3">
          <div class="sidebar-nav">
            <jdoc:include type="modules" name="position-8" style="xhtml" />
          </div>
        </div>
        <!-- End Sidebar -->
        <?php endif; ?>
        <div id="content" class="<?php echo $span;?>">
          <!-- Begin Content -->
          <jdoc:include type="modules" name="position-3" style="xhtml" />
          <jdoc:include type="message" />
          <jdoc:include type="component" />
          <jdoc:include type="modules" name="position-2" style="none" />
          <!-- End Content -->
        </div>
        <?php if ($this->countModules('position-7')) : ?>
        <div id="aside" class="span3">
          <!-- Begin Right Sidebar -->
          <jdoc:include type="modules" name="position-7" style="well" />
          <!-- End Right Sidebar -->
        </div>
        <?php endif; ?>
      </div>
        <p class="pull-right"><a href="#top" id="back-top"><?php echo JText::_('TPL_OPENAPI_BACKTOTOP'); ?></a></p>
    </div>
  </div>
  <!-- Footer -->
<!--  <div class="footer">-->
<!--      <jdoc:include type="modules" name="footer" style="none" />-->
<!--  </div>-->
    <footer>
    	<div class="parbase footercopyright">
    	<div id="footerLegal" class="copyright">
            <div class="container"><p><?php echo $sitename; ?>&nbsp;&nbsp;|&nbsp;&nbsp;<span><a target="_blank" href="index.php?option=com_content&view=article&id=9&catid=2&Itemid=107">Privacy Policy</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="index.php?option=com_content&view=article&id=8&catid=2&Itemid=107">Terms of Use</a></span></p>
                </div>
    	</div>
    </div>
    </footer>
  <jdoc:include type="modules" name="debug" style="none" />
  <?php if($callback=$app->input->getString('callback')):?>
  <script type="text/javascript">
    if(Joomla && Joomla["<?php echo $callback; ?>"]){
      Joomla["<?php echo $callback; ?>"]();
    }

  </script>
  <?php endif;?>
  <script type="text/javascript">
      jQuery.ajaxSetup({
          cache: false
      });
    var _renderMessages = function(messages) {
      	var container = document.id('system-message-container');

      	Object.each(messages, function (item, type) {
      		var div = new Element('div', {
      			id: 'system-message',
      			'class': 'alert alert-' + type
      		});
      		div.inject(container);
      		var a = new Element('a', {
      		    'class': 'close',
      		    'data-dismiss': 'alert',
      		    html: 'x'
      		});
      		a.inject(div);
      		var h4 = new Element('h4', {
      			'class' : 'alert-heading',
      			html: Joomla.JText._(type)
      		});
      		h4.inject(div);
      		var divList = new Element('div');
      		Array.each(item, function (item, index, object) {
      			var p = new Element('p', {
      				html: item
      			});
      			p.inject(divList);
      		}, this);
      		divList.inject(div);
      	}, this);

        jQuery('body').animate({
            scrollTop: 0
        }, 'fast');
};

    Joomla.JText.load({
        "error": "Error",
        "warning": "Warning",
        "success": "Success",
        "info": "Info"
    });

    Joomla.showError = function(errorText) {
        jQuery('.btn-submit').removeAttr('disabled');

        ////////////////////////////////////////////////////////////////////
        ////////// Show the error messages at the top of the page //////////
        ////////////////////////////////////////////////////////////////////
        _renderMessages({
            "error": errorText
        });
    };
    Joomla._renderMessages = function(infoText) {
        _renderMessages({
            "info": infoText
        });
    };
    Joomla.showWarning = function(warningText) {
        _renderMessages({
            "warning": warningText
        });
    };
    
    Joomla.showSuccess = function(successText) {
        _renderMessages({
            "success": successText
        });
    };

      var div = jQuery(".login-greeting");
      var user_profile_link = "<?php echo JURI::root().'index.php/userprofile';?>";
      div.html('<a href="'+user_profile_link+'">' + div.text() + '</a>');


      jQuery(function() {
          if(window === window.top) {
              var oCookieValues = DeveloperPortal.getCookieValues(), sErrMsgs, sWarningMsgs, sSuccessMsgs, oMsgs = {};
              if(oCookieValues[DeveloperPortal.KEY_HAS_ERRORS] === 'true') {
                  sErrMsgs = oCookieValues[DeveloperPortal.KEY_ERROR_MESSAGES];
                  oMsgs.error = [sErrMsgs];
                  DeveloperPortal.removeErrMsgFromCookie();
              }
              if(oCookieValues[DeveloperPortal.KEY_HAS_WARNINGS] === 'true') {
                  sWarningMsgs = oCookieValues[DeveloperPortal.KEY_WARNING_MESSAGES];
                  oMsgs.warning = [sWarningMsgs];
                  DeveloperPortal.removeWarningMsgFromCookie();
              }
              if(oCookieValues[DeveloperPortal.KEY_HAS_SUCCESS] === 'true') {
                  sSuccessMsgs = oCookieValues[DeveloperPortal.KEY_SUCCESS_MESSAGES];
                  oMsgs.success = [sSuccessMsgs];
                  DeveloperPortal.removeSuccessMsgFromCookie();
              }
              if(sErrMsgs !== undefined || sWarningMsgs !== undefined || sSuccessMsgs !== undefined) {
                  _renderMessages(oMsgs);
              }
          }
      });
  </script>
</body>
</html>
