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

jimport('joomla.application.component.controller');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
require_once JPATH_BASE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php';
require_once JPATH_BASE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'permissions.php';
require_once JPATH_BASE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php';
require_once JPATH_BASE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'item.php';
require_once JPATH_BASE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'itemlist.php';
class JAK2FilterControllerItemlist extends K2FilterController{

    function display($cachable = false, $urlparams = false) {
    	error_reporting(E_ALL ^ E_NOTICE);
		
        
        JRequest::setVar('task', 'search');
        JRequest::setVar('view', 'itemlist');
		
        $model=$this->getModel('Itemlist','JAK2FilterModel');
        
        $modelitems = new K2ModelItem();
       
        $modelitems->getData();
        
        $document = JFactory::getDocument();
        
        $viewType = $document->getType();
      
        $view = $this->getView('itemlist', $viewType,'K2View');
      
        $view->setModel($model);
        
        $view->setModel($modelitems);
        
        $user = JFactory::getUser();
        
        if ($user->guest){
            $cache = true;
        }
        else {
            $cache = false;
        }
        parent::display($cache);
    }
	function search(){
		$post   = JRequest::get('POST');
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$items	= $menu->getItems('link', 'index.php?option=com_jak2fiter&view=search');

		if(isset($items[0])) {
			$post['Itemid'] = $items[0]->id;
		} elseif (JRequest::getInt('Itemid') > 0) { //use Itemid from requesting page only if there is no existing menu
			$post['Itemid'] = JRequest::getInt('Itemid');
		}
		unset($post['task']);
		unset($post['submit']);
		$uri = JURI::getInstance();
		$uri->setQuery($post);
		$uri->setVar('option', 'com_jak2filter');
		$uri->setVar('view', 'itemlist');
		foreach($post AS $key=>$value){
			$uri->setVar($key, $value);
		}
		$this->setRedirect(JRoute::_('index.php'.$uri->toString(array('query', 'fragment')),false));
	}
}
