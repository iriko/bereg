<?php
/**
 * ------------------------------------------------------------------------
 * JA K2 Filter Plg for J25 & J30
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

/**
 * Example K2 Plugin to render YouTube URLs entered in backend K2 forms to video players in the frontend.
 */

// Load the K2 Plugin API
JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'k2plugin.php');

// Initiate class to hold plugin events
class plgK2Jak2filter extends K2Plugin {

	// Some params
	var $pluginName = 'jak2filter';
	var $pluginNameHumanReadable = 'K2 Plugin - JA K2 Filter';

	function plgK2Jak2filter( & $subject, $params) {
		parent::__construct($subject, $params);
	}

	public function onBeforeK2Save(&$row, $isNew) {
		if($isNew) return;
		
		$params = JComponentHelper::getParams('com_jak2filter');
		
		$indexing = (int) $params->get('indexing_onsave', 1);
		if($indexing) {
			$this->indexingData('onBeforeK2Save', $row, $isNew);
		}
	}

	public function onAfterK2Save(&$row, $isNew) {
		$params = JComponentHelper::getParams('com_jak2filter');
		
		$indexing = (int) $params->get('indexing_onsave', 1);
		if($indexing) {
			$this->indexingData('onAfterK2Save', $row, $isNew);
		}
	}
	
	/*public function onContentAfterSave(&$row, $isNew) {
		$this->indexingData('onContentAfterSave', $row, $isNew);
	}*/
	
	/**
	 * Update indexing data
	 * - if context is cron then update indexing data for all items
	 * - otherwise, update indexing records that are related with article
	 *
	 * @param string $context
	 * @param TableK2Item $row
	 * @param boolean $isNew
	 */
	private function indexingData($context, $row, $isNew) {
		if($isNew && $context == 'onBeforeK2Save') {
			return;
		}
		//
		$db = JFactory::getDbo();
        $jnow = JFactory::getDate();
        $now = K2_JVERSION == '15' ? $jnow->toMySQL() : $jnow->toSql();
        $nullDate = $db->getNullDate();
		
        if($context == 'onBeforeK2Save') {
			$query = "SELECT * FROM #__k2_items WHERE id = ".$db->quote($row->id);
			$db->setQuery($query);
			$row2 = $db->loadObject();
			if(!$row2) return;
			
			$updateCatid = $row2->catid;
			$updateAuthor = $row2->created_by;
			$updateFields = json_decode($row2->extra_fields);
		} else {
			$updateCatid = $row->catid;
			$updateAuthor = $row->created_by;
			$updateFields = json_decode($row->extra_fields);
		}
		
		//UPDATE CATEGORY
		$query = "SELECT `value` FROM #__jak2filter WHERE `name` = 'indexing'";
		$db->setQuery($query);
		$result = $db->loadResult();
		$data = ($result) ? json_decode($result) : new stdClass();
		
		//extra fields list
		$types = array('select', 'multipleSelect', 'radio');
		$query = "SELECT id FROM #__k2_extra_fields WHERE `type` IN ('".implode("','", $types)."')";
		$db->setQuery($query);
		//$fields = $db->loadResultArray(0);
		
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
		   $fields = $db->loadColumn(0);
		
		}
		else if (version_compare(JVERSION, '2.5', 'ge'))
		{
		    $fields = $db->loadResultArray(0);
		
		}
		else
		{
		   $fields = $db->loadResultArray(0);
		
		}
		
    	if($context == 'onBeforeK2Save') {
    		//category
			$key = sprintf('c.%d', $updateCatid);
			$num_items = (isset($data->$key) && $data->$key > 1) ? intval($data->$key) - 1 : 0;
			$data->$key = $num_items;
    		//author
			$key = sprintf('a.%d', $updateAuthor);
			$num_items = (isset($data->$key) && $data->$key > 1) ? intval($data->$key) - 1 : 0;
			$data->$key = $num_items;
			//extra fields
			if(count($fields) && is_array($updateFields)) {
				foreach ($updateFields as $f) {
					if(in_array($f->id, $fields)) {
						$key = sprintf('f.%d.%d', $f->id, $f->value);
						$num_items = (isset($data->$key) && $data->$key > 1) ? intval($data->$key) - 1 : 0;
						$data->$key = $num_items;
					}
				}
			}
    	} else {
    		//category
			$key = sprintf('c.%d', $updateCatid);
			$num_items = (isset($data->$key)) ? intval($data->$key) + 1 : 1;
			$data->$key = $num_items;
    		//author
			$key = sprintf('a.%d', $updateAuthor);
			$num_items = (isset($data->$key)) ? intval($data->$key) + 1 : 1;
			$data->$key = $num_items;
			//extra fields
			if(count($fields) && is_array($updateFields)) {
				foreach ($updateFields as $f) {
					if(in_array($f->id, $fields)) {
						$key = sprintf('f.%d.%d', $f->id, $f->value);
						$num_items = (isset($data->$key)) ? intval($data->$key) + 1 : 1;
						$data->$key = $num_items;
					}
				}
			}
    	}
    	
    	$data = json_encode($data);
		$query = "
						INSERT INTO #__jak2filter
						SET 
							`name` = 'indexing',
							`updatetime` = ".$db->quote($now).",
							`value` = ".$db->quote($data)."
						ON DUPLICATE KEY UPDATE
							`updatetime` = ".$db->quote($now).",
							`value` = ".$db->quote($data)."
						";
		$db->setQuery($query);
		$db->query();
	}
	
	public function onAfterRender()
	{
		$app = JFactory::getApplication();
		if ($app->isAdmin()) {
			return;
		}
		
		$params = JComponentHelper::getParams('com_jak2filter');
		$indexing = (int) $params->get('indexing_cron', 1);
		$interval = (int) $params->get('indexing_interval', 900);
		$interval = $interval * 60;
		$cronkey = $params->get('indexing_cron_key', 'indexing');
		
		if(!$indexing) return;
		
		$db = JFactory::getDbo();
		$query = "SELECT updatetime FROM #__jak2filter WHERE `name` = 'cron'";
		$db->setQuery($query);
		$updatetime = $db->loadResult();
		$updatetime = !$updatetime ? 0 : strtotime($updatetime);
		
		$key = JRequest::getVar('jakey');
		$run = (($updatetime + $interval < time()) || ($key == $cronkey));
		
		if($run) {
			$body = JResponse::getBody();
			$url = JURI::root().'index.php?option=com_jak2filter&view=cron';
			$script = '
<script type="text/javascript">
window.addEvent("domready", function() {
new Request({url: "'.$url.'", method: "get"}).send();
});
</script>
';
			$body = str_replace('</head>', $script.'</head>', $body);
			JResponse::setBody($body);
		}
	}
}