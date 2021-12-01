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

echo JHtml::_('mrelements.catselector', 'jform[category][]', $this->section->id, $this->default_categories, 
		($this->type->params->get('submission.multi_category', 0) ? $this->type->params->get('submission.multi_max_num', 1) : 1), $this->type->params->get('category_limit.category'));
?>