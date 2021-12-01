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
defined('_JEXEC') or die('Restricted access');
$cats_model = JModelLegacy::getInstance('Categories', 'CobaltModel');
$cats_model->section = $this->section;
$cats_model->parent_id = 1;
$cats_model->order = $this->catsel_params->get('tmpl_params.cat_ordering', 'c.lft ASC');
$cats_model->levels = 1000;
$cats_model->all = 1;
$categories = $cats_model->getItems();
$options = array();
if($this->params->get('submission.first_category', 0))
{
	$options[] = JHtml::_('select.option', 0, $this->section->name, 'id', 'title');
}

$limit = $this->type->params->get('submission.multi_category', 0) ? $this->type->params->get('submission.multi_max_num', 1) : 1;

$options = array_merge($options, getCategoryOptions($categories, $this->type, $this->params->get('submission.first_category', 0)));

if(!$options)
{
	echo JText::_('CSECTIONNOCATEGORIES');
	return;
}

$size = count($options) > $this->catsel_params->get('tmpl_params.multiple_catselect_size', 20) ? $this->catsel_params->get('tmpl_params.multiple_catselect_size', 20) : count($options);

if($this->params->get('submission.multi_category', 0))
{
	$attr = ' size="'.$size.'" multiple="'.$this->params->get('submission.multi_category', 0).'"';
}
else 
{
	$attr = ' size="'.$this->catsel_params->get('tmpl_params.single_catselect_size', 1).'"';
}
$attr .= ' required="true"';
$attr .= ' class="inputbox required"';

if($limit > 1)
{
	echo '<div><small>'.JText::sprintf('CSELECTLIMIT', $limit).'</small></div>';
}	

if(!$this->default_categories) $this->default_categories = 0;

echo  JHtml::_('select.genericlist', $options, 'jform[category][]', $attr, 'id', 'title', $this->default_categories);


function getCategoryOptions($categories, $type, $fc = 0)
{
	$close = $level = $title = $options = array();
	
	$def = new stdClass();
	$def->category = array();
	$def->allow = 0;
	$def->category_limit_mode = 0;
	$def->show_restricted = 0;
	
	$category_limit = $type->params->get('category_limit', $def);
	if(!isset($category_limit->category))
	{
		$category_limit->category = array();
		$category_limit->allow = 0;
	}
	
	$excluded_parent_id = array();
	
	foreach ($categories as $cat)
	{
		if(end($close) && (end($level) >= $cat->level))
		{
			$options[] = end($title);
			
			array_pop($close);
			array_pop($level);
			array_pop($title);
		}
		$category_enable = true;
		if($category_limit->allow && isset($category_limit->category) && !in_array($cat->id, $category_limit->category))
		{
			$category_enable = false;
		}
		if(!$category_limit->allow && isset($category_limit->category) && in_array($cat->id, $category_limit->category))
		{
			$category_enable = false;
		}
		if(!$category_limit->allow && in_array($cat->parent_id, $excluded_parent_id))
		{
			$category_enable = false;
		}
		if( $category_limit->allow && in_array($cat->parent_id, $excluded_parent_id))
		{
			$category_enable = true;
		}
		if(((!$category_enable && !$category_limit->allow) || ($category_enable && $category_limit->allow)) && $category_limit->category_limit_mode)
		{
			$excluded_parent_id[] = $cat->id;
		}
		if($category_limit->category_limit_mode && !$category_limit->show_restricted && !$category_enable)
		{
			continue;
		}
		
		if($cat->params->get('submission', 1))
		{
			$disabled = false;
			if(!$category_enable && $category_limit->show_restricted)
			{
				$disabled = true;
			}
			if(!$category_enable && !$category_limit->show_restricted && !$category_limit->category_limit_mode)
			{
				$disabled = true;
			}
			$options[] = JHtml::_('select.option', $cat->id, str_repeat(' - ', ($cat->level - 1 + $fc)).$cat->title, 'id', 'title', $disabled);
		}
		else	
		{
			$options[] = JHtml::_('select.optgroup', str_repeat(' - ', ($cat->level - 1 + $fc)).$cat->title, 'id', 'title');
			
			$close[] = TRUE;
			$level[] = $cat->level;
			$title[] = JHtml::_('select.optgroup', str_repeat(' - ', ($cat->level - 1 + $fc)).$cat->title, 'id', 'title');
		}
		if(isset($cat->children) && count($cat->children))
		{
			$opts = getCategoryOptions($cat->children, $type, $fc);
			$options = array_merge($options, $opts);
		}
	}
	return $options;
}


?>