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
require_once JPATH_BASE . "/components/com_cobalt/controllers/ajaxmore.php";
$app = JFactory::getApplication();
$app_id = $app->input->getInt('app_id');
$item = $this->item;
JFactory::getDocument()->setTitle($item->title);
$doc = JFactory::getDocument();

$product_id = JRequest::getVar("id");

if($product_id)
{
  $product_id = explode(":",$product_id);
  if(count($product_id) == 2)
  {
      $url = $item->url;
      if(isset($app_id)) {
          $url = $url.'&app_id='.$app_id;
      }
      $app->redirect(JRoute::_($url));
  }
}
$api_ids = $item->fields[7];
$old_operations_of_api = DeveloperPortalApi::getApis($api_ids);
$params = $this->tmpl_params['record'];
$icons = array();
$category = array();
$author = array();
$details = array();
$started = FALSE;
$i = $o = 0;
$fields = array();
if(JComponentHelper::getParams('com_emails')->get('enable_deleting_objects') == 1) {
  $tasks_to_hide = DeveloperPortalApi::isReferencedByDownstreamSubs($this->item->id, "product") ? array(DeveloperPortalApi::TASK_DELETE) : array();
} else {
  $tasks_to_hide = array(DeveloperPortalApi::TASK_DELETE);
}
$item_contact_email = '';
if(isset($item->fields_by_id)) {
    foreach($item->fields_by_id as $field_id => $field) {
        if($field-> id == 2 || $field->id ==3) {
            $fields[$field->label] = $field->result;
        }

		if($field->id == 32) {
			$item_contact_email = $field->user->email;
		}
    }
}
if($this->user->id > 0) {
    if(in_array(8, $this->user->groups) || count(DeveloperPortalApi::getUserByProductOrganization($this->item->id)) > 0) {
        $private_environments = DeveloperPortalApi::getPrivateEnvironments($this->item->id);
    }
}
$public_environments = DeveloperPortalApi::getPublicEnvironments($this->item->id);
$apps = DeveloperPortalApi::getActiveKeysOfCurOrgByProdId($this->item->id);



$toShow = TibcoTibco::getFlagForShow($this->item->id);

$isAdmin = false;
$show_plans = true;



if($this->user->id){
  $user_org = DeveloperPortalApi::getUserOrganization();
  $user_org = $user_org[0];

  $isAdmin = in_array(7, $this->user->getAuthorisedGroups()) || in_array(8, $this->user->getAuthorisedGroups()) || in_array($this->user->id, DeveloperPortalApi::getIdsOfOrganizationAdmin(68));
}


 if($toShow && !$isAdmin){
     if($app_id && in_array($item->id,DeveloperPortalApi::getProductIdsInApplication($app_id))) {
       $show_plans = false;;
     }else{
         $app->redirect(JUri::root().'index.php/products');
     }
 }
?>
<style>
	.dl-horizontal dd {
		margin-bottom: 10px;
	}

.tag_list li {
	display: block;
	float:left;
	list-style-type: none;
	margin-right: 5px;
}
.tag_list li#tag-first {
	line-height: 30px;
}
.tag_list li * {
	margin: 0px;
	padding: 0px;
	line-height: 30px;
}

.tag_list li a {
	color: #000;
	text-decoration: none;
	border: 1px solid #445D83;
	background-color: #F2F8FF;
	border-radius: 8px;
	padding: 5px 10px 5px 10px;
}

.tag_list li a:HOVER {
	color: #000;
	text-decoration: underline;
}
.line-brk {
	margin-left: 0px !important;
}
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
    .plans {
        background-color: #F2F2F2;
        padding: 15px 0px 15px 15px;
        width: 98%;
    }

    .your-plan {
        background-color: #FFFFCC;
    }

    .your-plan-title {
        background-color: inherit;
        border: 1px solid #CFCFCF;
        font-weight: bold;
        height: 20px;
        left: 20px;
        line-height: 20px;
        position: absolute;
        right: 20px;
        text-align:  center;
        top: -10px;
        z-index: 10000;
    }

    .img-place-holder {
        background-color: #F2F2F2;
        display: block;
        height: 100px;
        width: 100%;
    }

    .round-corner-5px {
        -moz-border-radius: 5px;
        -ms-border-radius: 5px;
        -o-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
    }

    div.tab-content {
        min-height: 50px;
    }

	/*div.row div:first-child{*/
		/*float:left;*/
		/*width:75px;*/
		/*height:75px;*/
		/*background:#efefef;*/
		/*margin-bottom:15px;*/
	/*}*/

	/*div.row div:first-child img{*/
		/*width:100%;*/
		/*height:100%;*/
	/*}*/

	div.row ul{
		margin-left:20px;
		float:left;
		width:auto;
		height:auto;
		overflow:hidden;
	}

	div.row ul li{
		line-height:21px;
	}

	div.row ul li span:first-child{
		display:inline-block;
		width:70px;
		text-align: left;
	}

	div.row .btn-group{
		float:right;
		clear:left;
	}
	.newsflash-title{
		width:200px;
	}
	.toolbar{
		height:45px;
		margin-top:0px;
		background-color:#eee;
		-webkit-box-shadow: inset 0 0 20px rgba(0,0,0,.1);
		-moz-box-shadow: inset 0 0 20px rgba(0,0,0,.1);
		box-shadow: inset 0 0 20px rgba(0,0,0,.1);
		margin-bottom:20px;
	}
	.toolbar .right-button{
		margin-top:12px;
		margin-right:10px;
	}
	article h4{
		margin-left:20px;
	}
 .operations2 li{
    list-style: none; margin: 10px;
    border: 1px solid #dddddd;
    font: normal 16px Arial, Helvetica, sans-serif;
    background: #ffffff;
    height: 30px;
    line-height: 30px;
  }
  .operations2 li span{
    display: block;
    margin-right: 20px;
    padding:0 5px;
  }
  .operations2 li span.method{
    background: #aaa;
    color: white;
    width: 40px;
    float: left;
    font-size: 0.7em;
    text-align: center;
  }

  .operations2 li span.path{
    float: left;
    width: 40%;
    font-weight: bold;
  }
  .operations2 li span.desc{
    float: right;
    text-align: right;
    color: #0f6ab4;
    font-size:0.7em;
  }
  #message-bar{
    display: none;
  }
</style>
<!-- <link rel="stylesheet" type="text/css" href="libraries/swagger-ui/css/screen.css" /> -->
<!-- <link rel="stylesheet" type="text/css" href="libraries/swagger-ui/css/highlight.default.css" /> -->
<!-- <script src="libraries/swagger-ui/lib/shred.bundle.js" type="text/javascript"></script> -->
<!-- <script src="libraries/swagger-ui/lib/jquery.slideto.min.js" type="text/javascript"></script> -->
<!-- <script src="libraries/swagger-ui/lib/jquery.wiggle.min.js" type="text/javascript"></script> -->
<!-- <script src="libraries/swagger-ui/lib/jquery.ba-bbq.min.js" type="text/javascript"></script> -->
<!-- <script src="libraries/swagger-ui/lib/handlebars-1.0.0.js" type="text/javascript"></script> -->
<!-- <script src="libraries/swagger-ui/lib/underscore-min.js" type="text/javascript"></script> -->
<!-- <script src="libraries/swagger-ui/lib/backbone-min.js" type="text/javascript"></script> -->
<!-- <script src="libraries/swagger-ui/lib/swagger.js" type="text/javascript"></script> -->
<!-- <script src="libraries/swagger-ui/swagger-ui.js" type="text/javascript"></script> -->
<!-- <script src="libraries/swagger-ui/lib/highlight.7.3.pack.js" type="text/javascript"></script> -->

<link rel="stylesheet" type="text/css" href="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/css/typography.css" />
<link rel="stylesheet" type="text/css" href="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/css/screen.css" />
<!--<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/shred.bundle.js" type="text/javascript"></script>-->
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/jquery.slideto.min.js" type="text/javascript"></script>
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/jquery.wiggle.min.js" type="text/javascript"></script>
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/jquery.ba-bbq.min.js" type="text/javascript"></script>
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/handlebars-2.0.0.js" type="text/javascript"></script>
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/js-yaml.min.js" type="text/javascript"></script>
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/lodash.min.js" type="text/javascript"></script>
<!--<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/underscore-min.js" type="text/javascript"></script>-->
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/backbone-min.js" type="text/javascript"></script>
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/swagger-ui.js" type="text/javascript"></script>
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/highlight.9.1.0.pack.js" type="text/javascript"></script>
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/highlight.9.1.0.pack_extended.js" type="text/javascript"></script>
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/jsoneditor.min.js" type="text/javascript"></script>
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/marked.js" type="text/javascript"></script>
<script src="components/com_cobalt/views/record/tmpl/default_record_product/swagger-ui/lib/swagger-oauth.js" type="text/javascript"></script>

<article class="<?php echo $this->appParams->get('pageclass_sfx')?><?php if($item->featured) echo ' article-featured' ?>">
    <div class="container-fluid featured-items">
        <div class="row">
            <?php $dtitle = str_replace(">","&gt;",$item->title); ?>
            <script type="text/javascript">
                (function($){
                    $(function(){
                        jQuery('#banner-title-heading').text('Products');
                    });
                })(jQuery);
            </script>
            <h4><a href="index.php/products">Products</a><?php
                    if(isset($item->ucatname)):
                        echo " &gt; ".$item->ucatname;
                    endif;
            ;?> </h4>
        </div>
    <?php if($isAdmin): ?>
		<div class="toolbar">
		<?php if(!$this->print):?>
       		<div class="pull-right controls right-button">
       			<div class="btn-group">
       				<?php if($this->user->get('id')):?>
                  <?php if(DeveloperPortalApi::list_controls($item->controls, $tasks_to_hide, $this->item->id, $this->item->type_id)
                            || $params->get('tmpl_core.item_print')
                            || ($isAdmin && $toShow)
                            || ($isAdmin && !$toShow)
                            || $bookmark_list
                            || $follow_list
                            || $repost):
                  ?>
					<a href="#" data-toggle="dropdown" class="dropdown-toggle btn-mini">
						<?php echo HTMLFormatHelper::icon('gear.png');  ?></a>

       						<ul id="product-dropdown-menu" class="dropdown-menu" style="margin-left:-90px;">
       							<?php if($item->controls):?>
       							<?php echo DeveloperPortalApi::list_controls($item->controls, $tasks_to_hide, $this->item->id, $this->item->type_id);?>
       							<?php endif;?>
       							<?php if($params->get('tmpl_core.item_print')):?>
       								<li><a rel="tooltip" data-original-title="<?php echo JText::_('CPRINT'); ?>" onclick="window.open('<?php echo JRoute::_($this->item->url.'&tmpl=component&print=1');?>','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
       									<?php echo HTMLFormatHelper::icon('printer.png').'Print';  ?></a></li>
       							<?php endif;?>

                    <?php if($isAdmin):?>
                      <?php if($toShow):?>
                        <li><?php echo DeveloperPortalFormatHelper::approve_publish($item, true);?></li>
                      <?php elseif (!$toShow):?>
                        <li><?php echo DeveloperPortalFormatHelper::approve_publish($item, false);?></li>
                      <?php endif;?>
                    <?php endif;?>

                    <?php $bookmark_list = DeveloperPortalFormatHelper::bookmark_list($item, $this->type, $params); ?>
                    <?php if($bookmark_list):?>
       							  <li><?php echo $bookmark_list;?></li>
                    <?php endif;?>

                    <?php $follow_list = DeveloperPortalFormatHelper::follow_list($item, $this->section);?>
                    <?php if($follow_list):?>
                    <li><?php echo $follow_list;?></li>
       							<?php endif;?>

                    <?php $repost = HTMLFormatHelper::repost($item, $this->section);?>
                    <?php if($repost):?>
       							<li><?php echo $repost;?></li>
                    <?php endif;?>
       						</ul>
                <?php endif;?>
       				<?php endif;?>
       			</div>
       		</div>
       	<?php else:?>
       		<div class="pull-right controls right-button">
       			<a href="#" class="btn btn-mini" rel="tooltip" data-original-title="<?php echo JText::_('CPRINT');?>" onclick="window.print();return false;"><?php echo HTMLFormatHelper::icon('printer.png');  ?></a>
       		</div>
       	<?php endif;?>
		</div>
    <?php endif; ?>
        <div class="row">
			<div class="product-image-box primary-color<?php echo rand(1,5); ?>">
                <?php echo $item->fields_by_id[3]->result;?>
                <div class="newsflash-title"><?php echo $item->title;?></div>
            </div>
            <div class="product-description"><?php echo $fields['Description']; ?></div>
<!--			<ul>-->
<!--				<li><span>Created</span>--><?php //echo $author[0].' '.$author[1];?><!--</li>-->
<!--				<li><span>Modified</span>--><?php //echo $author[2];?><!--</li>-->
<!--				<li><span>Info</span>--><?php //echo implode(',  ', $details);?><!--</li>-->
<!--				<li><span>Email</span>--><?php //echo $item_contact_email;?><!--</li>-->
<!--			</ul>-->

        </div>
    </div>

	<div class="clearfix"></div>
    <?php echo str_replace("&amp;","&",$item->fields_by_id[35]->result);?>
    <div class="tabtable">
       <ul class="nav nav-tabs" id="tabs-list">
           <?php if($show_plans):?>
           <li class="active">
               <a class="category-<?php echo $item->ucatalias; ?>" href="#tab-overview" data-toggle="tab">Plans</a>
           </li>
            <?php endif;?>
           <li>
               <a class="category-<?php echo $item->ucatalias; ?>" href="#tab-api-explorer" data-toggle="tab">API Explorer</a>
           </li>
           <li <?php if(!$show_plans){echo ' class="active"';}?>>
               <a class="category-<?php echo $item->ucatalias; ?>" href="#tab-documentation" data-toggle="tab">Documentation</a>
           </li>
       </ul>
        <div class="nav-tab-title category-<?php echo $item->ucatalias; ?>"></div>
       <div id="tabs-box" class="tab-content">
           <?php if($show_plans):?>
           <div id="tab-overview" class="tab-pane active">
               <div class="article-info">
                   <div class="plans-area">
                   <?php echo $item->fields_by_id[54]->result;?>
                   </div>
               </div>
           </div>
           <?php endif;?>
           <div id="tab-api-explorer" class="tab-pane">
			   <div>

                   <div class="pull-left" style="margin-left: 10px; margin-top: 10px;">
                     <?php if($this->user->id > 0): ?>
                     <select id="apps">
                          <option value="">Select Application</option>
                       <?php foreach($apps as $app): ?>
						   <option value="<?php echo $app->active_key[0]->apiKey ?>"><?php echo $app->title ?></option>
                       <?php endforeach; ?>
                     </select>
                     <?php endif; ?>
                       <span>API Key:&nbsp;</span>
                       <input type="text" id="input_apiKey" size="40" />
                   </div>

                   <div class="pull-right" style="margin-right: 10px; margin-top: 10px;display:none;">
    				   <span>Environment:&nbsp;</span>
    				   <select id="select_environment" name="env">
    					   <!-- <option value='' selected><?php echo count($public_environments) == 0 && (!isset($private_environments) || count($private_environments) == 0) ? JText::_("NO_ENVIRONMENTS_ASSIGNED") : JText::_("CHOOSE_ENVIRONMENT"); ?></option> -->
    					   <?php if(count($public_environments) > 0): ?>
        					   <optgroup label="<?php echo JText::_("PUBLIC_ENVIRONMENTS"); ?>">
                      <?php $has_default_environment = false; ?>
        					   <?php foreach($public_environments as $env): ?>
        							<option <?php if(!$has_default_environment){echo ' selected="selected"';$has_default_environment=true; }?> value="<?php echo $env->base_path; ?>"><?php echo $env->title; ?></option>;
        					   <?php endforeach; ?>
        					   </optgroup>
    					   <?php endif; ?>
    					   <?php if(isset($private_environments) && count($private_environments) > 0): ?>
    					       <optgroup label="<?php echo JText::_("PRIVATE_ENVIRONMENTS"); ?>">
    					       <?php foreach($private_environments as $env): ?>
                                    <option value="<?php echo $env->base_path; ?>"><?php echo $env->title; ?></option>;
    					       <?php endforeach; ?>
    					       </optgroup>
    					   <?php endif; ?>
    				   </select>
                   </div>
                   <div class="clearfix"></div>
			   </div>
			   <div class="swagger-section">
			    <div id="message-bar" class="swagger-ui-wrap message-success"></div>
                </div>
                <script type="text/javascript">
                    jQuery.post(
                        GLOBAL_CONTEXT_PATH + "index.php?option=com_cobalt&task=ajaxMore.getAllswaggerJson",
                        {'id': '<?php echo $this->item->id;?>'},
                        function (data) {
                            if (data.success) {
                                var swaggerSpecList = data.result;
                                swaggerSpecList.forEach(function(swaggerSpec, n){
                                    jQuery('.swagger-section').append('<div id="swagger_ui_div_' + n + '" class="swagger-ui-wrap"></div>');
                                    console.log(n);
                                    swaggerSpec.path = 'uploads/swaggerSpecs/' + swaggerSpec.ctime.slice(0,7) + '/' + swaggerSpec.filename;
                                    jQuery(function () {
                                        window.swaggerUi = new SwaggerUi({
                                            url: GLOBAL_CONTEXT_PATH + swaggerSpec.path,
                                            dom_id: 'swagger_ui_div_' + n,
                                            useJQuery: true,
                                            showRequestHeaders: true,
                                            supportHeaderParams: true,
                                            supportedSubmitMethods: ['get', 'post', 'put'],
                                            highlightSizeThreshold: 1024 * 1024, // 1Mb
                                            onComplete: function(swaggerApi, swaggerUi){
                                                <?php if($app_id):?>
                                                jQuery("#swagger_ui_div a[href^='#']").each(function(i,ele){
                                                    var link = jQuery(ele).attr("href");
                                                    jQuery(ele).attr("href","<?php echo JUri::getInstance()->toString();?>" + link);
                                                });
                                                <?php endif;?>
                                                jQuery('pre code').each(function(i, e) {hljs.highlightBlock(e);});
                                            },
                                            onFailure: function(data) {
                                                //jQuery('#tabs-list a[href="#tab-api-explorer"]').removeAttr('data-toggle').addClass("tab-disabled");
                                                if(!window.first){
                                                    //portal engine is down
                                                    if(data.indexOf("50")!=-1){
                                                        handleError(data,"<?php echo JText::_('API_EXPLORER_ERROR'); ?>");
                                                        <?php foreach($old_operations_of_api as $idx=>$item): ?>
                                                        var poerations = jQuery("#swagger_ui_div").append("<h2><?php echo $item['title']; ?></h2>")
                                                        jQuery("#swagger_ui_div").append("<ul class='operations2 list<?php echo $idx; ?>'></ul>");
                                                        var lis = "";
                                                        <?php foreach($item['operations'] as $operation): ?>
                                                        lis = lis + "<li><span class='method'><?php echo $operation['REST method']; ?></span><span class='path'><?php echo $operation['URI path']; ?></span><span class='desc'><?php echo explode(':',$operation['Description'])[1]; ?></span></li>";
                                                        <?php endforeach; ?>
                                                        jQuery(".list<?php echo $idx; ?>").append(lis);
                                                        <?php endforeach; ?>
                                                    }else{
                                                        //portal engine is up, but no operations
                                                        Joomla._renderMessages(["<?php echo JText::_('NO_OPERATIONS'); ?>"]);
                                                    }
                                                    window.first = true;
                                                }

                                            },
                                            docExpansion: 'none',
                                            jsonEditor: false,
                                            defaultModelRendering: 'schema',
                                            showRequestHeaders: false
                                        }), setKeyInHeader = function() {
                                            var key = jQuery('#input_apiKey').val();
                                            if(window.swaggerUi.api) {
                                                if (key && key.trim && key.trim() !== "") {
                                                    window.swaggerUi.api.clientAuthorizations.add("api_key", new SwaggerClient.ApiKeyAuthorization("Apikey", key, "header"));
                                                } else {
                                                    if (window.swaggerUi.api.clientAuthorizations.authz && window.swaggerUi.api.clientAuthorizations.authz.api_key) {
                                                        delete window.swaggerUi.api.clientAuthorizations.authz.api_key;
                                                    }
                                                }
                                            }
                                        };
                                        jQuery('#input_apiKey').change(function() {
                                            setKeyInHeader();
                                        }).keyup(function() {
                                            setKeyInHeader();
                                        });
                                        jQuery('#input_apiKey').val(jQuery('#apps').val());
                                        setKeyInHeader();
                                        jQuery('#apps').change(function() {
                                            jQuery('#input_apiKey').val(jQuery('#apps').val());
                                            setKeyInHeader();
                                        });
                                        swaggerUi.load();
                                        if(jQuery('#apps').length > 0 && jQuery('#apps')[0].options.length > 1) {
                                            jQuery('#apps')[0].selectedIndex = 1;
                                            jQuery('#input_apiKey').val(jQuery('#apps').val());
                                            setKeyInHeader();
                                        }

                                    });
                                });
                            } else {
                                Joomla.showError(["Can not find the swagger JSON file."]);
                            }
                        },
                        'json'
                    )
                    function handleError(data,error){
                        var sUUID = UUID.generate(),
                            sErrMsg = PORTAL_UNREACHABLE_ERROR_MESSAGE;
                        //Joomla.showError(["The API Explorer is currently in non-interactive mode."]);
                        DeveloperPortal._saveLogInDatabase({
                            log_type: '',
                            status: '503',
                            statusText:'',
                            content: data,
                            summary: error,
                            entity_type: 'product',
                            entity_id: "<?php echo $product_id ?>",
                            event: 'Request',
                            event_status: 'error',
                            uuid: sUUID
                        }, function(sErrMsg) {
                            Joomla.showError([error,sErrMsg]);
                        }, [DeveloperPortal._urlifySupport(sUUID, sErrMsg) + sUUID]);
                    };

                </script>
           </div>

           <!-- <div id="tab-data-reference" class="tab-pane">
               <p>Data Reference tab</p>
           </div>
           <div id="tab-data-explorer" class="tab-pane">
               <p>Data Explorer tab</p>
           </div> -->

           <div id="tab-documentation" class="tab-pane<?php if(!$show_plans){ echo ' active';}?>">
               <p><?php echo $this->loadTemplate('record_asg_article_tab_doc');?></p>
           </div>
       </div>
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

	var oPublicEnvs = {
	    35: <?php echo makeEnvIdArrayString($public_environments); ?>
	}, nTypeId = <?php echo $this->item->type_id; ?>, nProductId = <?php echo $this->item->id; ?>;


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

function makeEnvIdArrayString($array = array()) {
    $rv = '';
    foreach($array as $index => $item) {
        if($index != 0) {
            $rv .= ", ";
        }
        $rv .= $item->id;
    }
    return "[" . $rv . "]";
}
