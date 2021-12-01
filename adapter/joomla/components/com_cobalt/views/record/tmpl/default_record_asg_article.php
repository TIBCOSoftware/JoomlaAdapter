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

require_once JPATH_BASE . "/components/com_cobalt/library/php/helpers/developerportal.php";
$item = $this->item;
$params = $this->tmpl_params['record'];
$icons = array();
$category = array();
$author = array();
$details = array();
$started = FALSE;
$i = $o = 0;
$fields = array();
$environments = array();
$item_contact_email = '';
if(isset($item->fields_by_groups[null])) {
    foreach($item->fields_by_groups[null] as $field_id => $field) {
        if($field-> id == 2 || $field->id ==3) {
            $fields[$field->label] = $field->result;
        }

		if($field->id == 35 || $field->id == 36) {
			foreach($field->content['list'] as $key => $value) {
				$environments[$value->title] = $value->fields[14][0]['url'];
			}
		}

		if($field->id == 32) {
			$item_contact_email = $field->user->email;
		}
    }
}


// pre($item);
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

    .plan {
        background-color: #FFFFFF;
        border: 1px solid #CFCFCF;
        display: inline-block;
        height: 91%;
        margin-right: 9px;
        padding: 15px 15px;
        position: relative;
        vertical-align: bottom;
        width: 20%;
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

    .custom-plan {
        background-color: #CCFFFF;
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

	div.row div:first-child{
		float:left;
		width:75px;
		height:75px;
		background:#efefef;
		margin-bottom:15px;
	}

    div.row div:first-child li img {
        background: transparent;
        height:auto;
        width:auto;
    }

	div.row div:first-child img{
		width:100%;
		height:100%;
	}

	div.row ul{
		margin-left:20px;
		float:left;
		width:auto;
		height:auto;
		overflow:hidden;
	}

    .featured-items .row:first-of-type {
        height:75px;
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

    div.row .nav-hover .btn-group {
        height:auto;
    }

    div.row .controls {
        padding-top: 20px;
    }
    div.row .controls .btn-group {
        background-color: transparent;
    }

    div.row .controls .btn-group button {
        float:right;
    }

	div#tab-overview div:first-child div{
		float:left;
		width:35%;
		height:auto;
		margin-bottom:15px;
		padding-right:12px;
		overflow:hidden;
		word-wrap: break-word;
	}

	div#tab-overview div:first-child div.d_mid{
		width:15%;
	}

	div#tab-overview div:first-child div span:first-child{
		color:#909090;
	}
	div#tab-overview div:first-child div dt{
		font-weight:normal;
	}
</style>
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
            <h3><?php echo "Products > ".$item->title; ?></h3>
	<?php if(!$this->print):?>
		<div class="pull-right controls">
			<div class="btn-group">
				<?php if($this->user->get('id')):?>
						<button data-toggle="dropdown" class="btn" style="width:120px;height:40px;">Action</button>
						<ul class="dropdown-menu" style="margin-left:-50px;">
							<?php if($item->controls):?>
							<?php echo list_controls($item->controls);?>
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
		<div class="pull-right controls">
			<a href="#" class="btn btn-mini" rel="tooltip" data-original-title="<?php echo JText::_('CPRINT');?>" onclick="window.print();return false;"><?php echo HTMLFormatHelper::icon('printer.png');  ?></a>
		</div>
	<?php endif;?>
        </div>
        <div class="row">
			<div><?php echo $item->fields_by_id[3]->result;?></div>
			<ul>
				<li><span>Created</span><?php echo $author[0].' '.$author[1];?></li>
				<li><span>Modified</span><?php echo $author[2];?></li>
				<li><span>Info</span><?php echo implode(',  ', $details);?></li>
				<li><span>Email</span><?php echo $item_contact_email;?></li>
			</ul>
        </div>
    </div>

	<div class="clearfix"></div>

    <div class="tabtable" style="margin-top:10px;">
       <ul class="nav nav-tabs" id="tabs-list">
           <li class="active">
               <a href="#tab-overview" data-toggle="tab">Overview</a>
           </li>
           <li>
               <a href="#tab-api-explorer" data-toggle="tab">API Explorer</a>
           </li>
           <li>
               <a href="#tab-documentation" data-toggle="tab">Documentation</a>
           </li>
       </ul>
       <div id="tabs-box" class="tab-content">
           <div id="tab-overview" class="tab-pane active">
               <div class="well article-info" style="margin-top:15px;overflow:hidden;">
	               <div><span>Product Name</span><br/><span><?php echo $item->title; ?></span></div>
				   <div class="d_mid"><span><?php if($category):?>
								<?php echo implode(' ', $category);?>
							<?php endif;?></span></div>
				   <div><span>Created</span><br/><span><?php echo $author[0].' '.$author[1];?></span></div>
				   <div style="clear:left;"><span>Description</span><br/><span><?php echo $fields['Description']; ?></span></div>
				   <div class="d_mid"><span>Featured</span><br/><span><?php echo $item->featured==1?'Yes':'No';?></span></div>
				   <div><span>Modified</span><br/><span><?php echo $author[2];?></span></div>
				   <div style="clear:both;width:100%;">Rating<br/>
					   <?php if($params->get('tmpl_core.item_rating')):?>
					   <?php echo $item->rating;?>
					   <?php endif;?>
				   </div>
			   </div>
			   <h3 style="margin-top: 30px;">Plans</h3>
               <div class="well article-info">
                   <?php echo $item->fields_by_id[54]->result;?>
               </div>
           </div>
           <div id="tab-api-explorer" class="tab-pane">
			   <div>
                   <div class="pull-left" style="margin-left: 10px; margin-top: 10px;">
                       <span>API Key:&nbsp;</span>
                       <input type="text" id="input_apiKey" size="40" />
                   </div>
                   <div class="pull-right" style="margin-right: 10px; margin-top: 10px;">
    				   <span>Environment:&nbsp;</span>
    				   <select id="select_environment" name="env">
    					   <option value='' selected><?php echo count($environments) == 0 ? 'Unassigned' : 'Please choose' ?></option>
    					   <?php
    					   	foreach($environments as $key => $value) {
    							echo '<option value="'.$value.'">'.$key.'</option>';
    						}
    					   ?>
    				   </select>
                   </div>
			   </div>
                <div id="swagger_ui_div" class="swagger-ui-div"></div>
                <script type="text/javascript">
                	jQuery(function () {
                	    var swaggerUi = new SwaggerUi({
                            url: GLOBAL_CONTEXT_PATH + 'asg/internal/product/<?php echo $item->id; ?>',
                            dom_id: 'swagger_ui_div',
                            supportHeaderParams: true,
                            supportedSubmitMethods: ['get', 'post', 'put'],
                            onComplete: function(swaggerApi, swaggerUi){
                              jQuery('pre code').each(function(i, e) {hljs.highlightBlock(e);});
                            },
                            onFailure: function(data) {
                            	if(console) {
                                    console.log('Unable to Load SwaggerUI');
                                    console.log(data);
                                }
                            },
                            docExpansion: 'none'
                        });
                        jQuery('#input_apiKey').change(function() {
                          var key = jQuery('#input_apiKey').val();
                          console.log("key: " + key);
                          if(key && key.trim() != "") {
                            console.log("added key " + key);
                            window.authorizations.add("key", new ApiKeyAuthorization("apikey", key, "header"));
                          }
                        });

                        swaggerUi.load();
                        window.swaggerUis && window.swaggerUis.splice ? window.swaggerUis.push(swaggerUi) : window.swaggerUis = [swaggerUi];
                    });

                </script>
           </div>

           <!-- <div id="tab-data-reference" class="tab-pane">
               <p>Data Reference tab</p>
           </div>
           <div id="tab-data-explorer" class="tab-pane">
               <p>Data Explorer tab</p>
           </div> -->

           <div id="tab-documentation" class="tab-pane">
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

