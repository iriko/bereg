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
function JAK2FilterBuildRoute(&$query){
	$segments = array();
	
	if (isset($query['view'])) {
		unset($query['view']);
	}
	return $segments;
}

function JAK2FilterParseRoute($segments) {
	$vars = array();
	$vars['view'] = 'itemlist';
	return $vars;
}
