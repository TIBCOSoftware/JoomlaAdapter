<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

require_once JPATH_BASE . "/includes/api.php";
require_once JPATH_BASE . "/components/com_cobalt/library/php/helpers/developerportal.php";
$item = $this->item;

$app_id = $item->id;
$params = $this->tmpl_params['record'];
$icons = array();
$category = array();
$author = array();
$details = array();
$user_profile_id = DeveloperPortalApi::getUserProfileId();
$orgId = DeveloperPortalApi::valueForKeyFromJson($jsonStr, 60, $this->item->id);
$subscriptions = DeveloperPortalApi::subscriptionsOfOrgnazions($orgId);
$groupedItems = DeveloperPortalApi::classify_subscriptions($subscriptions);
$ownedSubscriptions = DeveloperPortalApi::subscriptionsInApplication($this->item->id);
$ownedProducts = DeveloperPortalApi::getProductIdsInApplication($this->item->id);
$started = FALSE;
$i = $o = 0;
$fields = array();
$tasks_to_hide = array();
$old_keys = DeveloperPortalApi::getKeysOfApplication($this->item->id);
$app_info = DeveloperPortalApi::getApplicationInformation($this->item->id);
$app_oauth = DeveloperPortalApi::getAppOAuth($this->item->id);
$user = JFactory::getUser();
$active_key = count($app_oauth) > 0 && $app_oauth[0]->use_oauth == 1 ? DeveloperPortalApi::getActiveOAuthKeyOfApp($this->item->id) : DeveloperPortalApi::getActiveKeyOfApplication($this->item->id);

$app = JFactory::getApplication();
$pathway  = $app->getPathway();
$positions    = $pathway->getPathWay();
if(count($positions)){
  $positions = $positions[0];
  $positions->name = stripslashes(htmlspecialchars($positions->name, ENT_COMPAT, 'UTF-8'));
  $positions->link = JRoute::_($positions->link);
}
?>
<style>
<?php echo $params->get('tmpl_params.css');?>
</style>

<?php
if($params->get('tmpl_core.item_categories') && $item->categories_links)
{
	$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', (count($item->categories_links) > 1 ? JText::_('CCATEGORIES') : JText::_('CCATEGORY')), implode(', ', $item->categories_links));
}
if($params->get('tmpl_core.item_user_categories') && $item->ucatid)
{
	$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', JText::_('CUCAT'), $item->ucatname_link);
}
if($params->get('tmpl_core.item_author') && $item->user_id)
{
	$a[] = JText::sprintf('CWRITTENBY', CCommunityHelper::getName($item->user_id, $this->section));
	if($params->get('tmpl_core.item_author_filter'))
	{
		$a[] = FilterHelper::filterButton('filter_user', $item->user_id, NULL, JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, array('nohtml' => 1))), $this->section);
	}
	$author[] = implode(' ', $a);
}
if($params->get('tmpl_core.item_ctime'))
{
	$author[] = JText::sprintf('CONDATE', JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format')));
}

if($params->get('tmpl_core.item_mtime'))
{
	$author[] = JText::_('CMTIME').': '.JHtml::_('date', $item->modify, $params->get('tmpl_core.item_time_format'));
}
if($params->get('tmpl_core.item_extime'))
{
	$author[] = JText::_('CEXTIME').': '.($item->expire ? JHtml::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : JText::_('CNEVER'));
}

if($params->get('tmpl_core.item_type'))
{
	$details[] = sprintf('%s: %s %s', JText::_('CTYPE'), $this->type->name, ($params->get('tmpl_core.item_type_filter') ? FilterHelper::filterButton('filter_type', $item->type_id, NULL, JText::sprintf('CSHOWALLTYPEREC', $this->type->name), $this->section) : NULL));
}
if($params->get('tmpl_core.item_hits'))
{
	$details[] = sprintf('%s: %s', JText::_('CHITS'), $item->hits);
}
if($params->get('tmpl_core.item_comments_num'))
{
	$details[] = sprintf('%s: %s', JText::_('CCOMMENTS'), CommentHelper::numComments($this->type, $this->item));
}
if($params->get('tmpl_core.item_favorite_num'))
{
	$details[] = sprintf('%s: %s', JText::_('CFAVORITED'), $item->favorite_num);
}
if($params->get('tmpl_core.item_follow_num'))
{
	$details[] = sprintf('%s: %s', JText::_('CFOLLOWERS'), $item->subscriptions_num);
}
?>

<style type="text/css">
	.detail_panel{
		overflow:hidden;
		padding:0;
	}

	.detail_panel ul{
		float:left;
		list-style:none;
		margin-bottom:15px;
		margin-left:10px;
		width:35%;
	}

	.detail_panel ul li span:first-child{
		display:inline-block;
		width:80px;
		text-align: left;
	}
	
	#prod-item-title{
		float:left;
	}
	
	#prod-item-title+span{
		margin-left:10px;
		font-weight:normal;
	}
	.requestKeyBtn{
		float:left;
		margin-left:9px;
		margin-top:9px;
	}
	.toolbar{
		height:45px;
		margin-top:10px;
		background-color:#eee;
		margin-bottom:20px;
		-webkit-box-shadow: inset 0 0 20px rgba(0,0,0,.1);
		-moz-box-shadow: inset 0 0 20px rgba(0,0,0,.1);
		box-shadow: inset 0 0 20px rgba(0,0,0,.1);
	}
	.toolbar .right-button{
		margin-top:12px;
		margin-right:10px;
	}
	.breadcrumb-app{
		color:#006699;
		margin:10px;
	}
</style>
<span class="pull-left breadcrumb-app"><a href="<?php echo JURI::root(); ?>index.php/applications">Applications</a> | <?php echo $item->title; ?></span>
<div class="clearfix"></div>
<article class="<?php echo $this->appParams->get('pageclass_sfx')?><?php if($item->featured) echo ' article-featured' ?>">
    <div class="app-details">
	<div class="toolbar">
	<button class="btn requestKeyBtn" <?php echo 'onclick="DeveloperPortal.requestKey(' . $this->item->id . ', ' . makeArrayString($old_keys) . ', ' . count($active_key) . ', function() {window.location.reload();}, function() {window.location.reload();})"'; ?>>Request Key</button>
	<?php if($this->user->authorise('core.admin')):?>
		<button class="btn requestKeyBtn btn-resync" onclick="DeveloperPortal.resync('<?php echo $this->item->id; ?>', 'Application');"><?php echo JText::_('RESYNC');?></button>
    <div class="hidden"><div id="system-message" class="alert alert-info"><a class="close" data-dismiss="alert">&#215;</a><h4 class="alert-heading"><?php echo JText::_('INFO');?></h4><div><p><?php echo JText::_('RESYNC_COMPLETE');?></p></div></div></div>
	<?php endif;?>
	
	<?php if(!$this->print):?>
		<div class="pull-right controls right-button">
			<div class="btn-group">
				<?php if($this->user->get('id')):?>
						<a href="#" data-toggle="dropdown" class="dropdown-toggle btn-mini">
								<?php echo HTMLFormatHelper::icon('gear.png');  ?></a>
						<ul class="dropdown-menu" style="margin-left:-90px;">
							<?php if($item->controls):?>
							<?php echo DeveloperPortalApi::list_controls($item->controls, $tasks_to_hide, $this->item->id, $this->item->type_id); ?>
							<?php endif;?>
							<?php if($params->get('tmpl_core.item_print')):?>
								<li><a rel="tooltip" data-original-title="<?php echo JText::_('CPRINT');?>" onclick="window.open('<?php echo JRoute::_($this->item->url.'&tmpl=component&print=1');?>','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
									<?php echo HTMLFormatHelper::icon('printer.png').'Print';  ?></a></li>
							<?php endif;?>
							<li><?php echo DeveloperPortalFormatHelper::bookmark_list($item, $this->type, $params);?></li>
							<li><?php echo DeveloperPortalFormatHelper::follow_list($item, $this->section);?></li>
							<li><?php echo HTMLFormatHelper::repost($item, $this->section);?></li>
						</ul>
				<?php endif;?>
			</div>
		</div>
	<?php else:?>
		<div class="pull-right controls right-button">
			<a href="#" class="btn btn-mini" rel="tooltip" data-original-title="<?php echo JText::_('CPRINT');?>" onclick="window.print();return false;"><?php echo HTMLFormatHelper::icon('printer.png');  ?></a>
		</div>
	<?php endif;?>
    </div>

		<div class="clearfix"></div>

        <div class="detail_panel">
            <div class="details_panel_left">
				<div class="app-image-box primary-color<?php echo rand(1,7); ?>">
                <?php echo $item->fields_by_id[119]->result;?><br/>
                <div class="newsflash-title" title="<?php echo $item->title; ?>"><?php echo $item->title; ?></div>
            	</div>
            </div>
            <div class="details_panel_right">
            <?php if(count($app_oauth) == 0 || $app_oauth[0]->use_oauth == -1): ?>
    			<div class="keyinfo ">
                    <span class="k_title">API key:</span><span id="span_api_key"><?php echo (isset($active_key[0]->apiKey) ? $active_key[0]->apiKey : JText::_("NO_ACTIVE_KEY")); ?></span>
                </div>
            <?php else: ?>
          <div class="clientinfo">
     				<span class="k_title">Client ID:</span><span class="value"><?php echo (isset($active_key[0]->apiKey) ? $active_key[0]->apiKey : JText::_("NO_ACTIVE_KEY")); ?></span><br/>
     				<span class="k_title">Client secret:</span><span class="value"><?php echo (isset($active_key[0]->secret) ? $active_key[0]->secret : JText::_("NO_ACTIVE_KEY")); ?></span><br/>
<!--      				<span class="k_title">Redirect URIs:</span><span class="value">xxxxxx</span><br/>
     				<span class="k_title">Response Type:</span><span class="value">xxxxxx</span><br/>
     				<span class="k_title">Client type:</span><span class="value">xxxxxx</span> -->
     			</div>
     		<?php endif; ?>
			</div>
			<div class="clearfix"></div>
            <div class="app-basic-details">
				<div>
					Partner: <span class="value"><?php echo $app_info[0]->org; ?> </span><br/>
					Contact: <span class="value"><?php echo $app_info[0]->contact; ?></span>
				</div>
    			<div class="app-desc-full">
    				<?php echo $item->fields_by_id[57]->result; ?>
    			</div>
            </div>
        </div>
    </div>
    <!--Scopes list goes here-->
	<div style="display:none;">
    <?php $scopes = $item->fields_by_id['125'];?>
    <?php if($scopes):?>
      <?php if($scopes->params->get('core.show_lable') > 1):?>
          <?php echo $scopes->label; ?>
          <?php if($scopes->params->get('core.icon')):?>
            <?php echo HTMLFormatHelper::icon($scopes->params->get('core.icon'));  ?>
          <?php endif;?>
      <?php endif;?>
      <?php echo $scopes->result; ?>
    <?php endif;?>
	</div>
	
	<style>
	.app-products-row{
		padding:0;
		width:100%;
		border:none;
		float:none;
	}
	
	.app-products-row:last-child{
		border-bottom:2px solid #ababab;
	}
	
	.app-products-row div{
		margin:0;
		height:100px;
	}
	
	.app-products-row div:first-child{
		float:left;
		width:25%;
	}
	
	.app-products-row div:first-child .app-prod-title{
		overflow:hidden;
		border-top:2px solid #ababab;
		width:100%;
		margin:0 10px 0 0;
	}
	
	.app-products-row div:first-child .app-prod-title div:first-child{
		float:left;
		width:30%;
		height:100px;
		overflow:hidden;
		line-height:100px;
		text-align:center;
	}
	
	.app-products-row div:first-child .app-prod-title div:last-child{
		border:none;
		float:right;
		width:65%;
		overflow:hidden;
	}
	
	.app-products-row div:nth-child(2){
		float:right;
		width:72%;
	}
	
	.app-products-row div:nth-child(2) div{
		border-top:2px solid #ababab;
		width:100%;
	}
	
	.app-products-row div:nth-child(2) div span{
		display:inline-block;
		height:100%;
		float:left;
	}
	
	.app-products-row div:nth-child(2) div span:first-child{
		width:60%;
	}
	
	.app-products-row div:nth-child(2) div span:last-child{
		width:40%;
	}
	
	p.app-prod-detail{
		height:40px;
		line-height:14px;
		overflow:hidden;
		font-size:12px;
		color:#666;
	}
	
	#products-list p span{
		text-transform:uppercase;
		font-size:14px;
		color:#777;
		font-weight:bold;
	}
	
	#products-list p span:last-child{
		display:inline-block;
		float:right;
		width:72%;
		height:100%;
	}
	</style>

	<div id="list_panel">
		<a id="edit_products" class="btn pull-right" href="<?php echo JURI::root(); ?>index.php/applications?task=form.edit&amp;id=<?php echo $this->item->id; ?>:<?php echo urlencode($this->item->title); ?>">Edit</a>
		<h3>Products used by '<?php echo $item->title; ?>'</h3>
		<div id="products-list">
			<p><span><?php echo JText::_('APPLICATION_PRODUCTS_TITLE_HEADER'); ?></span><span><?php echo JText::_('APPLICATION_PLANS_TITLE_HEADER'); ?></span></p>
		<?php if (count($ownedProducts)==0): ?>
			<div class="app-products-row">
				<div><div class="app-prod-title"></div></div>
				<div><div><h5><?php echo JText::_("EDIT_PRODUCT_NOTE");?></h5></div></div>
				<div class="clearfix"></div>
			</div>
		</div>
		<?php endif ?>
			<?php foreach($groupedItems as $key => $value): ?>
				<?php if (!in_array($key,$ownedProducts)): ?>
					<?php continue;?>
				<?php endif ?>
				<div class="app-products-row">
                            <?php
                                $prod = ItemsStore::getRecord($key);
                                $prod_url = Url::record($key);
								$thumb = DeveloperPortalApi::valueForKeyFromJson($prod->fields,3);
                            ?>
							<div>
								<div class="app-prod-title">
									<div>
										<?php echo "<a href='".$prod_url."'>"."<img src='".JURI::base().$thumb->image."' />"."</a>"; ?>
									</div>
									<div>
										<h5>
											<?php echo "<a href='".$prod_url."&app_id=".$app_id."'>".$prod->title."</a>"; ?>
										</h5>
										<p class="app-prod-detail"><?php echo $prod->fieldsdata; ?></p>
									</div>
								</div>
							</div>
							<div>
								<?php foreach($value as $item): ?>
									<?php if (!in_array($item->id,$ownedSubscriptions)): ?>
										<?php continue; ?>
									<?php endif ?>
									<?php
										$plan = DeveloperPortalApi::getRecordById(DeveloperPortalApi::valueForKeyFromJson("", 69, $item->id));
										$limit = DeveloperPortalApi::valueForKeyFromJson($plan->fields,80);
                                        $burst = DeveloperPortalApi::valueForKeyFromJson($plan->fields,79);
										if(!empty(DeveloperPortalApi::valueForKeyFromJson($plan->fields,152))){
					                        $concurrent_calls = DeveloperPortalApi::valueForKeyFromJson($plan->fields,152) . " " . JText::_("CONCURRENT_CALLS");
					                      }else{
					                        $concurrent_calls="";
					                      }
										$i ++;
									?>	
								<div>
									<span>
										<h5 style="font-weight:normal"><?php echo $plan->title; ?></h5>
										<p class="app-prod-detail"><?php echo 'Quota limit: '.str_replace("/"," calls per ", $limit).' <br/>Burst limit: '.str_replace("/"," calls per ", $burst)."<br/> Concurrent Limit:".$concurrent_calls; ?></p>
									</span>
									<span>
										<h5 style="font-weight:normal">
											<?php echo DeveloperPortalApi::valueForKeyFromJson($plan->fields,120).' <small style="color:#666;">'.JText::_('APPLICATION_UNIT_MONTH').'</small>'; ?>
										</h5>
										<p class="app-prod-detail">
										<?php
										$statusValue = DeveloperPortalApi::valueForKeyFromJson($item->fields, 78);
										$startTime = DeveloperPortalApi::valueForKeyFromJson($item->fields, 71);
										$endTime = DeveloperPortalApi::valueForKeyFromJson($item->fields, 72);
										$status = $statusValue[0];
										if (!empty($endTime)) {
											$curTimestamp = mktime(0,0,0,date("m"),date("d"),date("Y"));
											$startTimestamp = mktime(0,0,0,((int)substr($startTime[0],5,2)),((int)substr($startTime[0],8,2)),((int)substr($startTime[0],0,4)));
											$desTimestamp = mktime(0,0,0,((int)substr($endTime[0],5,2)),((int)substr($endTime[0],8,2)),((int)substr($endTime[0],0,4)));
											if($curTimestamp-$desTimestamp>0){
												$status = "Expired";
											}
										
											if ($startTimestamp-$curTimestamp>0){
												$status = "Inactive";
											}
										}
										echo $status;
										?>
										</p>
									</span>
								</div>
								<?php endforeach; ?>
							</div>
							<div class="clearfix"></div>
						</div>
			<?php endforeach; ?>
	</div>


	<?php echo $this->loadTemplate('tags');?>
</article>

<?php if($started):?>
	<script type="text/javascript">
		<?php if(in_array($params->get('tmpl_params.item_grouping_type', 0), array(1))):?>
			jQuery('#tabs-list a:first').tab('show');
		<?php elseif(in_array($params->get('tmpl_params.item_grouping_type', 0), array(2))):?>
			jQuery('#tab-main').collapse('show');
		<?php endif;?>
	</script>
<?php endif;?>

<script type="text/javascript">

    var _FORM_TOKEN = '<?php echo JFactory::getSession()->getFormToken(); ?>';
    (function($) {
        jQuery('document').ready(function() {
          var editAppLink = jQuery('.app-details .dropdown-menu li a[href*="form.edit"]');
          if(editAppLink.size() > 0) {
              var editAppHref = editAppLink.attr('href'), ret = editAppHref.substr(editAppHref.indexOf('return'));
              jQuery('#edit_products').attr('href', editAppHref + '&' + ret + '#2');
          }
        });
      if(!$.trim($(".dropdown-menu").text()))
      {
        $("[data-toggle='dropdown']").remove();
      }

      <?php 

      $belong_to_organization = DeveloperPortalApi::getUserOrganization();
      $app_organization = DeveloperPortalApi::getOranizationIdOfApplication($this->item->id);
      if(!(in_array(7, $user->getAuthorisedGroups()) || in_array(8, $user->getAuthorisedGroups())) && !in_array($app_organization, $belong_to_organization)):
      ?>
        $("[data-toggle='dropdown']").remove();
      <?php endif; ?>      

	
    }(jQuery));
</script>

<?php
function group_start($data, $label, $name)
{
	static $start = false;
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		//tab
		case 1:
			if(!$start)
			{
				echo '<div class="tab-content" id="tabs-box">';
				$start = TRUE;
			}
			echo '<div class="tab-pane" id="'.$name.'">';
			break;
		//slider
		case 2:
			if(!$start)
			{
				echo '<div class="accordion" id="accordion2">';
				$start = TRUE;
			}
			echo '<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#'.$name.'">
					     '.$label.'
					</a>
				</div>
				<div id="'.$name.'" class="accordion-body collapse">
					<div class="accordion-inner">';
			break;
		// fieldset
		case 3:
			echo "<legend>{$label}</legend>";
		break;
	}
}

function group_end($data)
{
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		case 1:
			echo '</div>';
		break;
		case 2:
			echo '</div></div></div>';
		break;
	}
}

function total_end($data)
{
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		//tab
		case 1:
			echo '</div>';
		break;
		case 2:
			echo '</div>';
		break;
	}
}

function makeArrayString($array) {
    $rv = '';
    foreach($array as $index => $item) {
        if($index != 0) {
            $rv .= ", ";
        }
        $rv .= $item;
    }
    return "[" . $rv . "]";
}
