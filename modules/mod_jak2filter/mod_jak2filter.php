<?php
/**
 * ------------------------------------------------------------------------
 * JA K2 Filter Module for J25 & J30
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';


$app	= JFactory::getApplication();
$menu	= $app->getMenu();
$active	= $menu->getActive();
$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
$path	= isset($active) ? $active->tree : array();
$showAll	= $params->get('showAllChildren');
$class_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));

/**
 * check if k2 component is installed
 */
if( JFile::exists(JPATH_BASE.DS.'components'.DS.'com_k2'.DS.'k2.php')){
	$filter_by_keyword   = $params->get('filter_by_keyword');
	$filter_by_category  = $params->get('filter_by_category');
	$filter_by_author    = $params->get('filter_by_author');

	$filter_by_extrafield	= $params->get('filter_by_extrafield');
	$filter_by_fieldtype 	= $params->get('filter_by_fieldtype');
	$filter_by_fieldrange	= $params->get('filter_by_fieldrange');
	$ja_stylesheet			= $params->get('ja_stylesheet');
	$display_counter		= (int) $params->get('display_counter');
	$auto_filter		= (int) $params->get('auto_filter');
	
	$ja_column = '';
	if($ja_stylesheet == 'horizontal-layout' && $params->get('ja_column') && $params->get('ja_column') > 0){
		$ja_column	= 'width:'.round(100/$params->get('ja_column'),2).'%;';
	}
	
	$document = JFactory::getDocument();
	
	$document->addStylesheet(JURI::base() . 'modules/mod_jak2filter/assets/css/style.css');
	$document->addScript(JURI::base() . 'modules/mod_jak2filter/assets/js/jak2filter.js');
	/**
	 * Override by template stylesheet
	 */
	if (is_file(JPATH_SITE . DS . 'templates' . DS . $app->getTemplate() . DS . 'css' . DS . $module->module . '.css')){
		$document->addStylesheet(JURI::base().'templates/' . $app->getTemplate() . '/css/'. $module->module .'.css');
	}
	
	$helper = new modJak2filterHelper($module);
	$helper->setCounter($display_counter);
	
	/**
	 * Get list
	 */
	$list	= $helper->getList($filter_by_extrafield, $filter_by_fieldtype, $filter_by_fieldrange);
	
	$categories = '';
	if($filter_by_category){
		$groupcategories = $params->get('k2catsid',null);
		$categories = $helper->getCategories($groupcategories);
	}

	$authors = '';
	if($filter_by_author){
		$filter_author_display = $params->get('filter_author_display','author_display_name');
		$authors = $helper->getAuthors($filter_author_display);
	}

	require JModuleHelper::getLayoutPath('mod_jak2filter', $params->get('layout', 'default'));
}else{
	echo JText::_('COMPONENT_K2_NOT_FOUND');
}