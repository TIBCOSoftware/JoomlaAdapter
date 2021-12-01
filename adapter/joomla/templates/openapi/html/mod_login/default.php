<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');


if($login_form_data = JFactory::getSession()->get('registry')->get('users.login.form.data'))
{
    if(isset($login_form_data['return']))
    {
        $return = base64_encode($login_form_data['return']);
    }
}

?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-inline">
	<?php if ($params->get('pretext')) : ?>
		<div class="pretext">
		<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; ?>
	<div class="userdata">
		<div id="form-login-username" class="control-group">
			<div class="controls">
				<?php if (!$params->get('usetext')) : ?>
					<div class="input-prepend input-append">
						<span class="add-on">
							<span class="icon-user tip" title="<?php echo JText::_('LOGIN_VALUE_USERNAME') ?>"></span>
							<label for="modlgn-username" class="element-invisible"><?php echo JText::_('LOGIN_VALUE_USERNAME'); ?></label>
						</span>
						<input id="modlgn-username" type="text" name="username" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('LOGIN_VALUE_USERNAME') ?>" />
					</div>
				<?php else: ?>
					<label for="modlgn-username"><?php echo JText::_('LOGIN_VALUE_USERNAME') ?></label>
						<input id="modlgn-username" type="text" name="username" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('LOGIN_VALUE_USERNAME') ?>" />
				<?php endif; ?>
			</div>
		</div>
		<div id="form-login-password" class="control-group">
			<div class="controls">
				<?php if (!$params->get('usetext')) : ?>
					<div class="input-prepend input-append">
						<span class="add-on">
							<span class="icon-lock tip" title="<?php echo JText::_('JGLOBAL_PASSWORD') ?>">
							</span>
								<label for="modlgn-passwd" class="element-invisible"><?php echo JText::_('JGLOBAL_PASSWORD'); ?>
							</label>
						</span>
						<input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
				</div>
				<?php else: ?>
					<label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
					<input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
				<?php endif; ?>

			</div>
		</div>
        <div id="form-login-submit" class="control-group">
      			<div class="controls">
      				<button type="submit" tabindex="0" name="Submit" class="login-button"><?php echo JText::_('JLOGIN') ?></button>
      			</div>
      			<?php
      			  $comEmail = JComponentHelper::getComponent('com_emails');
      			  $is_show_ping = $comEmail->params->get('is_show_ping');
      			  $ping_url = $comEmail->params->get("ping_url");
      			  if ($is_show_ping && $ping_url) {
      		  		echo '<br/><a href="'.$ping_url.'"><strong>'.JText::_('JLOGINPING').'</strong></a>';
      			  }
      			?>
      		</div>
		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<div id="form-login-remember" class="control-group checkbox">
			<label for="modlgn-remember" class="control-label"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?></label> <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
		</div>
		<?php endif; ?>
        <div class="clearfix"></div>
        <?php if ($params->get('posttext')) : ?>
       		<div class="posttext">
       		<p><?php echo $params->get('posttext'); ?></p>
       		</div>
       	<?php endif; ?>
        <div class="spacer"></div>
		<?php
			$usersConfig = JComponentHelper::getParams('com_users');
			if ($usersConfig->get('allowUserRegistration')) : ?>
			<ul class="unstyled">
<!-- 		Disabled the retrive username link
			<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
					  <?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
 				</li> -->
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
				</li>
                <li class="empty-spacer"><?php echo JText::_('LOGIN_NOT_REGISTERED'); ?></li>
                <li>
            					<a class="btn btn-primary login-register" href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
            					<?php echo JText::_('LOGIN_REGISTER'); ?></a>
            				</li>

			</ul>
		<?php endif; ?>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
