<?php

///////// SECTION START /////////
///////// This section of code was copied from the index.php file in the joomla folder. /////////
///////// It's used to initialize the session so that the current user can be captured. /////////
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

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');
///////// SECTION END /////////

$spotfire_domain = JComponentHelper::getComponent('com_emails')->params->get('spotfire_domain');
$user_session_id = JSession::getInstance(null, null)->getId();
$root_url = rtrim(JURI::root(), "/");
$apps            = JFactory::getApplication();
$templateName  = $apps->getTemplate('template')->template;
$spotfire_app_url = $root_url."/Analytics/";
$current_user_groups = JFactory::getUser()->getAuthorisedGroups();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr">
<head>
	<script src="media/jui/js/jquery.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="templates/<?php echo $templateName; ?>/css/template.css" type="text/css">
	<style>
	body{
		padding:10px;
	}
	
	h3{
		color:red;
	}
	</style>
</head>
<body>
	<?php if(empty($spotfire_domain) || empty($user_session_id) || empty($current_user_groups)):?>
		<h3>Request unavailable.</h3>
	<?php else:?>
		<script type="text/javascript" src="<?php echo $spotfire_app_url; ?>GetJavaScriptApi.ashx?Version=3.1"></script>
		<script type="text/javascript">
		if(typeof window.console !== 'object'){
			window.console = {
				log: function(message){alert(message)},
				error: function(message){alert(message)}
			};
		}

		function analyticsErrorHandler(errorCode, description){
			console.error("Error loading analtyics: code(" + errorCode + ")\n\t" + description);
		}

		jQuery(document).ready(function(){
			var appStarted = false,
				analyticsContent = jQuery('#analytics-content'),
				reloadBtn = jQuery('#btnReloadDashboard'),
				showHideBtn = jQuery('#btnShowHideDashboard'),
				dbModeSelect = jQuery('#selectDashboardMode');

			function buildAnalyticsDashboard(){
				var customization = new spotfire.webPlayer.Customization();
				customization.showClose = false;
				var app = new spotfire.webPlayer.Application("<?php echo $spotfire_app_url; ?>", customization);
				app.onError(analyticsErrorHandler);
				return app;
			}

			function loadAnalyticsDashboard(app){
				analyticsContent.css({
					'background-color':'#EFEFEF',
					'left':'0',
					'position':'relative',
					'height':'600',
					'width':'%100',
					'margin': '0'
				});
				app.open(dbModeSelect.length ? dbModeSelect.val():'/ASG/Partner', 'analytics-content', 'partner="anon";');
				appStarted = true;
			}

			function clearAnalyticsDashboard(keepHidden){
				appStarted = false;
				analyticsContent.remove();
				analyticsContent = jQuery('<div/>', { id: 'analytics-content', style: (keepHidden ? 'display:none':'') });
				jQuery('#analytics').append(analyticsContent);
			}

			function reloadAnalyticsDashboard(){
				clearAnalyticsDashboard();
				loadAnalyticsDashboard(buildAnalyticsDashboard());
			}

			//set the document domain according to the configuration setting to enable cross-site, same domain scripting
			try{
	 			document.domain = "<?php echo $spotfire_domain; ?>";
	 		}
	 		catch(err){
	 			analyticsErrorHandler(0, 'Failed setting of analytics domain to "<?php echo $spotfire_domain; ?>". Please check your settings and try again. [' + err + ']');
	 			return;
	 		}

			//ensure analytics api script was successfully fetched and the expected api element is available
			if(typeof spotfire === 'undefined' || !spotfire){
				analyticsErrorHandler(1, 'Spotfire JavaScript API failed to load.');
				return;
			};

			//show analytics section since the script was successfully fetched
			jQuery('#analytics').show();

			//set the cookie value for the analytics authentication proxy
			document.cookie = 'session-id=<?php echo $user_session_id; ?>; path=/';

			reloadBtn.bind('click', reloadAnalyticsDashboard);
			showHideBtn.bind('click', function(){
				if(analyticsContent.is(':hidden')){
					if(!appStarted){
						loadAnalyticsDashboard(buildAnalyticsDashboard());
					}
					analyticsContent.slideDown('slow');
					reloadBtn.css('visibility','visible');
					showHideBtn.text('Hide dashboard');
				}
				else{
					analyticsContent.slideUp('slow');
					reloadBtn.css('visibility','hidden');
					showHideBtn.text('Show dashboard');
				}
			});
			if(dbModeSelect.length){ //only add change handler if the element exists
				dbModeSelect.bind('change', function(){
					if(analyticsContent.is(':hidden')){
						clearAnalyticsDashboard(true);
						appStarted = false;
					}
					else{
						reloadAnalyticsDashboard();
					}
				});
			}
		
			showHideBtn.click();
		});
		</script>

		<div>
			<div id="analytics">
			<div id="analytics-control" style="display:inline-block; width:100%;">
				<?php if(in_array(7, $current_user_groups) || in_array(8, $current_user_groups)): //If user is an Administrator or a SuperUser... ?>
				<select id="selectDashboardMode" title="Select Dashboard Type to Display" class="pull-right" style="margin-bottom:0px;">
				<option value="/ASG/Host">Host</option>
				<option value="/ASG/Partner">Partner</option>
				</select>
				<?php endif;?>
				<button id="btnShowHideDashboard" title="Toggle Dashboard Display" class="btn pull-left" style="display:none;">Show dashboard</button>
				<div id="btnReloadDashboard" title="Refresh Dashboard" class="icon-refresh pull-left" style="visibility:hidden; margin: 7px; cursor: pointer; display:none;"></div>
			</div>
			<div id="analytics-content" style="display: none; clear: both; height:1000px;"></div>
			</div>
		</div>
	<?php endif;?>
	
</body>
</html>