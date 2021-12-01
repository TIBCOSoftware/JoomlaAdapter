<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
$app = JFactory::getApplication();
$menu = $app->getMenu();
$menu->setActive(115);
?>
<style>
	.icon-login{
		padding:10px
	}
	#login-page-login-form{
		margin:0 auto;
	}
	#login-page-login-form .icon-username:before{
		font-family: 'IcoMoon';
		content: '\22';
		font-style: normal;
		margin-top:10px;
	}
	#login-page-login-form .icon-password:before{
		font-family: 'IcoMoon';
		content: '\23';
		font-style: normal;
	}
	#login-page-login-form .control-username, 
	#login-page-login-form .control-password{
		width:10px;
		height:10px;
		background-color:#EEEEEE;
		border: 1px solid #CCCCCC;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
	    transition: border 0.2s linear 0s, box-shadow 0.2s linear 0s;
	}
	#login-page-login-form .description{
		margin-bottom:20px;
	}
	#login-page-login-form .controls{
		margin-left:0px;
	}
	#login-page-login-form{
		width:500px
	}
	#username,
	#password{
		width:400px;
	}
	#login-page-login-form .btn[type="submit"]{
		float:right;
		margin-right:55px;
	}
	.links{
		width:500px;
		margin:0 auto;	
		margin-top:20px;	
	}
	.border{
		width:450px;
		margin-right:50px;
		border-top: 1px solid #CCCCCC;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
	    transition: border 0.2s linear 0s, box-shadow 0.2s linear 0s;
		
	}
	#login-page-login-form #form-login-remember{
		width:200px;
		float:left;
		margin-bottom:0px;
		margin-top:5px;
	}
	#login-page-login-form #modlgn-remember{
		/*left: -90px;
	    position: relative;
	    top: 4px;*/
		margin-top:-20px;
		
	}
	.forget-password{
		margin-left:140px;
	}
	.control-label-rem{
		
	}
</style>
<div class="login <?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	<div class="login-description">
	<?php endif; ?>

		<?php if ($this->params->get('logindescription_show') == 1) : ?>
			<?php echo $this->params->get('login_description'); ?>
		<?php endif; ?>

		<?php if (($this->params->get('login_image') != '')) :?>
			<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JTEXT::_('COM_USER_LOGIN_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	</div>
	<?php endif; ?>

	<form id="login-page-login-form" action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-horizontal">
        <h1><?php echo JText::_('DEFAULT_LOGIN_SIGN_IN_TEXT');?><?php echo $app->getCfg('sitename'); ?></h1>
        <div class="description">
          <?php echo JText::_('DEFAULT_LOGIN_SIGN_IN_TEXT_TERMS_PRE');?> <a target="_blank" href="index.php?option=com_content&view=article&id=8&catid=2&Itemid=107"><?php echo JText::_('DEFAULT_LOGIN_SIGN_IN_TEXT_TERMS_MID');?> </a> <?php echo JText::_('DEFAULT_LOGIN_SIGN_IN_TEXT_TERMS_POST');?>
        </div>
		<fieldset>
			<?php foreach ($this->form->getFieldset('credentials') as $field) : ?>
				<?php if (!$field->hidden) : ?>
					<div class="control-group">
						<div class="controls">
                            <div class="control-<?php echo $field->name; ?> icon-login"><span class="icon-<?php echo $field->name; ?>"></span></div>
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
			<div class="control-group">
				
				<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
				<div id="form-login-remember" class="control-group checkbox">
					<label for="modlgn-remember" class="control-label-rem"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label> <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
				</div>
				<?php endif; ?>
				<div class="controls">
					<button type="submit" class="btn btn-primary"><?php echo JText::_('JLOGIN'); ?></button>
					<?php
    			  $comEmail = JComponentHelper::getComponent('com_emails');
    			  $is_show_ping = $comEmail->params->get('is_show_ping');
	  			  $ping_url = $comEmail->params->get("ping_url");
	  			  if ($is_show_ping && $ping_url) {
  			  		echo '<a style="margin-left: 15px;" href="'.$ping_url.'"><strong>'.JText::_('JLOGINPING').'</strong></a>';
	  			  }
    			?>
				</div>
			</div>
			
<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
		<div class="border"></div>
	</form>
	
</div>

<div class="links">
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
		<?php echo JText::_('DEFAULT_LOGIN_REGISTER'); ?></a>
			<a class="forget-password" href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
			<!--  <a class="forget-username" href="<?php //echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
			<?php// echo JText::_('COM_USERS_LOGIN_REMIND');?></a> -->
</div>
