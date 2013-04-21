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

class JAK2FilterControllerCron extends K2FilterController
{
	public function display($cachable = false, $urlparams = false) {
		header('Content-type: text/html; charset=ISO-8859-1');
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
			$now = date('Y-m-d H:i:s');
			$query = "
					INSERT INTO #__jak2filter
					SET 
						`name` = 'cron',
						`updatetime` = ".$db->quote($now).",
						`value` = 1
					ON DUPLICATE KEY UPDATE
						`updatetime` = ".$db->quote($now).",
						`value` = 1
					";
			$db->setQuery($query);
			$db->query();
			//
			$this->indexingData('cron');
		} else {
			$msg = JText::sprintf('The cron job will be run on %s', date('Y-m-d H:i:s', $updatetime + $interval));
			jexit($msg);
		}
	}


	/**
	 * Update indexing data for all items
	 *
	 * @param string $context
	 */
	private function indexingData($context) {
		@ignore_user_abort(true);
		
		/*var_dump('start');
		var_dump( time().'.'.microtime() );*/
		
		$this->checkUpdate();
		//
		$db = JFactory::getDbo();
		$jnow = JFactory::getDate();
		$now = K2_JVERSION == '15' ? $jnow->toMySQL() : $jnow->toSql();
		$nullDate = $db->getNullDate();

		//RE-INDEXING ALL ITEMS
		$where = array();
		$where[] = 'published=1';
		$where[] = 'trash=0';
		$where[] = "( publish_up = ".$db->Quote($nullDate)." OR publish_up <= ".$db->Quote($now)." )";
		$where[] = "( publish_down = ".$db->Quote($nullDate)." OR publish_down >= ".$db->Quote($now)." )";

		$whereItem = ' WHERE '.implode(' AND ', $where);


		//reset counter if does not have any active category
		/*$query = "DELETE FROM #__jak2filter_indexing";
		$db->setQuery($query);
		$db->query();*/

		$data = new stdClass();
		//INDEXING CATEGORY ITEMS
		$query = "SELECT catid, COUNT(id) AS num_items FROM #__k2_items {$whereItem} GROUP BY catid";
		$db->setQuery($query);
		$items = $db->loadObjectList('catid');

		if(count($items)) {
			foreach ($items as $catid => $item) {
				$key = sprintf('c.%d', $catid);
				$data->$key = $item->num_items;
			}
		}
		//GET PUBLISHED CATEGORY
		$query = "SELECT id AS ids FROM #__k2_categories WHERE published=1 AND trash=0";
		$db->setQuery($query);
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
		   $cids = $db->loadColumn(0);
		
		}
		else if (version_compare(JVERSION, '2.5', 'ge'))
		{
		    $cids = $db->loadResultArray(0);
		
		}
		else
		{
		   $cids = $db->loadResultArray(0);
		
		}
		

		if(!count($cids)) {
			jexit('Does not exists extrafields indexing');
		}

		$where[] = "catid IN (". implode(',', $cids) .")";
		$whereItem = ' WHERE '.implode(' AND ', $where);

		//INDEXING AUTHOR ITEMS
		$query = "SELECT created_by, COUNT(id) AS num_items FROM #__k2_items {$whereItem} GROUP BY created_by";
		$db->setQuery($query);
		$items = $db->loadObjectList('created_by');
		if(count($items)) {
			foreach ($items as $author_id => $item) {
				$key = sprintf('a.%d', $author_id);
				$data->$key = $item->num_items;
			}
		}

		//INDEXING EXTRA FIELD ITEMS

		//Only update for extra field group that has been assigned for published categories
		$types = array('select', 'multipleSelect', 'radio');
		$query = "
				SELECT DISTINCT f.id, f.`value` 
				FROM #__k2_extra_fields AS f
				INNER JOIN #__k2_categories AS c ON (c.extraFieldsGroup = f.group)
				WHERE f.published = 1
				AND f.`type` IN ('".implode("','", $types)."')
				AND c.id IN (". implode(',', $cids) .")";
		$db->setQuery($query);
		$fields = $db->loadObjectList('id');

		$aFields = array();
		if(count($fields)) {
			foreach ($fields as $id => $field) {
				$aFields[$id] = array();
				//[{"name":"Bebe","value":1,"target":null},{"name":"Guess","value":2,"target":null},...]
				$options = json_decode($field->value);
				foreach ($options as $opt) {
					if($opt->value) {
						$aFields[$id][$opt->value] = 0;
					}
				}
			}
		}

		if(!count($aFields)) {
			jexit('Does not exists extrafields indexing');
			
		}

		//updating indexing data
		$query = "
				SELECT id, extra_fields 
				FROM #__k2_items ".$whereItem." 
				AND extra_fields IS NOT NULL
				AND extra_fields <> ''
				";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		if(count($items)) {
			foreach ($items as $item) {
				//[{"id":"1","value":"4"},{"id":"2","value":"7"},{"id":"4","value":"6"},{"id":"3","value":"85.00"}]
				$values = json_decode($item->extra_fields);
				if(is_array($values) && count($values)) {
					foreach ($values as $val) {
						if(is_array($val->value)){
							foreach($val->value as $value){
								if(isset($aFields[$val->id]) && isset($aFields[$val->id][$value])) {
								$aFields[$val->id][$value]++;
								}
							}
						}else{
							if(isset($aFields[$val->id]) && isset($aFields[$val->id][$val->value])) {
								$aFields[$val->id][$val->value]++;
							}
						}
					}
				}
			}
		}

		foreach ($aFields as $fieldid => $options) {
			foreach ($options as $optid => $affectedItems) {
				if($affectedItems) {
					$key = sprintf('f.%d.%d', $fieldid, $optid);
					$data->$key = $affectedItems;
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

		/*var_dump('indexing extra fields');
		var_dump( time().'.'.microtime() );*/
		jexit('done');
	}
	
	function checkUpdate() {
		jimport('joomla.filesystem.file');
		$dbPath = JPATH_ADMINISTRATOR.'/components/com_jak2filter/installer/sql/';
		
		/**
         * Example code for upgrade to version x.x.x
         */
		$version = "1.0.2";
		$check = dirname(__FILE__)."/updated_{$version}.log";
		if (!JFile::exists($check)) {
			//processing code here
			$file = $dbPath.'upgrade_v'.$version.'.sql';
            $this->parseSQLFile($file);
			//end of update code
			
			$flag = 'Updated at '.date('Y-m-d H:i:s');
			JFile::write($check, $flag);
		}
	}
	
	function parseSQLFile($file) {
		jimport('joomla.filesystem.file');

		if(JFile::exists($file)) {
			$buffer = JFile::read($file);
			if($buffer) {
				$db = JFactory::getDbo();
				$queries = $db->splitSql($buffer);
				foreach ($queries as $query) {
					$db->setQuery($query);
					$db->query();
				}
			}
		}
	}
}