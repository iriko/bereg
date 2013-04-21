<?php
/**
 * ------------------------------------------------------------------------
 * JA K2 Filter Component j25
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$controller = JRequest::getWord('view', 'itemlist');
$task = JRequest::getWord('task');


jimport('joomla.filesystem.file');
jimport('joomla.html.parameter');

if (JFile::exists(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php')) {
	if( JFile::exists(JPATH_BASE.DS.'components'.DS.'com_k2'.DS.'k2.php')){
		
		JLoader::register('K2FilterController', JPATH_COMPONENT.DS.'controllers'.DS.'controller.php');
		JLoader::register('JAK2FilterModel', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'model.php');
		JLoader::register('JAK2FilterView', JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'view.php');
		//load language from component k2
		$lang =JFactory::getLanguage();
		$lang->load('com_k2');
		
		require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
		$classname = 'JAK2FilterController'.$controller;
		$controller = new $classname();
		$controller->execute($task);
		$controller->redirect();
		
	}else{
		$app = JFactory::getApplication();
		$app->redirect('index.php',JText::_('COMPONENT_K2_NOT_FOUND'),'error');
		
	}
}
else {
	JError::raiseError(404, JText::_('COMPONENT_NOT_FOUND'));
}

