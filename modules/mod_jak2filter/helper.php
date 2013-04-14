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

/**
 * @package		Joomla.Site
 * @subpackage	mod_jak2filter
 * @since		1.5
 */
class modJak2filterHelper
{
	const COUNT_ITEMS_TXT = ' (%s)';
	protected $module = 0;
	protected $params = null;
	protected $counter = 0;
	
	public function __construct($module) {
		$this->module = $module;
		$this->params = new JRegistry($module->params);
	}
	
	public function setCounter($state) {
		$this->counter = $state;
	}
	/**
	 * Get a list of the K2 Extra Fields.
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 * @since	1.5
	 */
	public function getList($fields, $fields_type, $fieldrange)
	{
		$items = array();
		if(is_array($fields) && count($fields)>0){	
			foreach ($fields_type as $index => $value){
				$field = explode(":", $value);
				if(count($field)>1){
					$fieldId = $field[0];
					$fieldType = $field[1];
					$fn = ucfirst(strtolower($fieldType));
					$func = 'get'.$fn;
					$funcLabel = 'getLabel'.$fn;
					
					if(!in_array($fieldId, $fields) || !method_exists($this, $func)) continue;
					
					$row = $this->getExtraField($fieldId);
					if(!$row) continue;
					
					if(method_exists($this, $funcLabel)) {
						$html = call_user_func_array(array($this, $funcLabel), array($row));
					} else {
						$html = "\n\t<label class=\"group-label\">{$row->name}</label>";
					}
					$html .= call_user_func_array(
								array($this, $func), 
								array($row, $fieldrange[$index])
								);
								
					$items[$fieldType.'_'.$fieldId] = $html;
				}
			}
		}
		return $items;
	}
	/*
	 * Get extra field from k2
	 * */
	public function getExtraField($id){
		$db	=	JFactory::getDbo();
		$query	=	"SELECT `id`, `name`, `value`, `group` FROM #__k2_extra_fields WHERE id = ".$db->quote($id);
		$db->setQuery($query);
		$rows	=	$db->loadObject();
		return $rows;
	}
	
	/*
	 * Return html of field
	 * */
	public function getTextfield($field,$ranges = NULL){
		$defaut_values = JRequest::getVar("xf_{$field->id}_txt",'');
		
		$values = json_decode($field->value);
		
		$html = '';
		foreach ($values as $f)
        {
           	if($defaut_values){
           		$f->value = $defaut_values;
           	}
        	$html .= "\n\t<input type=\"text\" class=\"exfield exgroup{$field->group}\" name=\"xf_{$field->id}_txt\" id=\"xf_{$field->id}_txt\" value=\"{$f->value}\" />";
        }
       
        return $html;
	}
	
	public function getValuerange($field, $ranges = NULL){
		
		$defaut_values = JRequest::getVar("xf_{$field->id}_range",null);
		
		$field->value = json_decode($field->value);
		if(isset($ranges)){
			$ranges = explode("|", $ranges);
			$ranges = array_filter($ranges);
			sort($ranges,SORT_NUMERIC);
			$options[] = JHTML::_('select.option', 0, JText::sprintf('JAK2_SELECT_OPTION', $field->name));
		
			for($i = 0; $i<count($ranges);$i++){
				if($i==0){
					$options[] = JHTML::_('select.option','|'.$ranges[$i],JText::_('LESS_THAN').' '.$ranges[$i] );
				}
				else if($i==(count($ranges)-1)){
					$options[] = JHTML::_('select.option',$ranges[$i-1].'|'.$ranges[$i],$ranges[$i-1].JText::_('JA_K2FILTER_TO').$ranges[$i]);
					$options[] = JHTML::_('select.option',$ranges[$i].'|',JText::_('MORE_THAN').' '.$ranges[$i] );
				}
				else{
					$options[] = JHTML::_('select.option',$ranges[$i-1].'|'.$ranges[$i],$ranges[$i-1].JText::_('JA_K2FILTER_TO').$ranges[$i]);
				}
				
			}
			
        	return JHTML::_('select.genericlist', $options, 'xf_'.$field->id.'_range', array('class'=>'exfield exgroup'.$field->group), 'value', 'text', $defaut_values);
		}
	}
	public function getCheckbox($field,$ranges = NULL) {
		
		$defaut_values = JRequest::getVar("xf_{$field->id}",array());
		if(!is_array($defaut_values)) {
			$defaut_values = array($defaut_values);
		}
		
		$values = json_decode($field->value);
       	$html = '';
       
		foreach ($values as $index => $f)
        {
           	$checked = '';
        	if(in_array($f->value, $defaut_values)){
        		$checked = 'checked="checked"';
        	}
			$num_items = $this->getNumItems('f', $field->id, $f->value);
        	$num_items = (!empty($num_items)) ? sprintf(self::COUNT_ITEMS_TXT, $num_items) : '';
        	$html .= "\n\t<input type=\"checkbox\" class=\"exfield exgroup{$field->group}\" name=\"xf_{$field->id}[]\" id=\"xf_{$field->id}_{$index}\" value=\"{$f->value}\" {$checked}/>";
         	$html .= "\n\t<label class=\"lb-checkbox\" for=\"xf_{$field->id}_{$index}\">{$f->name}{$num_items}</label>";
        }
        return $html;
		
	}
	
	public function getSelect($field,$ranges = NULL){
		$defaut_values = JRequest::getVar("xf_{$field->id}",null);
		$values = json_decode($field->value);
		$html[] = JHTML::_('select.option', 0, JText::sprintf('JAK2_SELECT_OPTION', $field->name));
        foreach ($values as $f)
        {
			$num_items = $this->getNumItems('f', $field->id, $f->value);
        	$num_items = (!empty($num_items)) ? sprintf(self::COUNT_ITEMS_TXT, $num_items) : '';
            $html[] = JHTML::_('select.option', $f->value, $f->name . $num_items);
        }
		
        return JHTML::_('select.genericlist', $html, 'xf_'.$field->id, array('class'=>'exfield exgroup'.$field->group), 'value', 'text', $defaut_values);
	}
	
	public function getMultipleSelect($field,$ranges = NULL){
		$values = json_decode($field->value);
     	$options = array();
        foreach ($values as $f)
        {
			$num_items = $this->getNumItems('f', $field->id, $f->value);
        	$num_items = (!empty($num_items)) ? sprintf(self::COUNT_ITEMS_TXT, $num_items) : '';
            $options[] = JHTML::_('select.option', $f->value, $f->name . $num_items);
        }
		$defaut_values = JRequest::getVar("xf_{$field->id}",null);
	
        return JHTML::_('select.genericlist', $options, 'xf_'.$field->id.'[]', array('class'=>'exfield multiple exgroup'.$field->group, 'multiple' => 'multiple'), 'value', 'text', $defaut_values);
	}
	
	public function getLabelMagicSelect($field) {
     	$id = $this->module->id.'-'.$field->id;
		$label = "\n\t<label class=\"group-label\">".$field->name;
     	$label .= '<button id="g-'.$id.'" class="select closed exfield exgroup'.$field->group.'" href="#" onclick="jaMagicSelect(this, \'mg-'.$id.'\'); return false;" title="'.JText::_('JAADD', true).' '.$field->name.'">'.JText::_('JAADD').' '.$field->name.'</button>';
     	$label .= '</label>';
		return $label;
	}
	
	public function getMagicSelect($field,$ranges = NULL){
		$auto_filter		= (int) @$this->params->get('auto_filter');
		$defaut_values = JRequest::getVar("xf_{$field->id}",null);
		if(!is_array($defaut_values)) {
			$defaut_values = array($defaut_values);
		}
		$values = json_decode($field->value);
     	$options = array();
     	$id = $this->module->id.'-'.$field->id;
     	$html = '<div class="ja-magic-select" id="mg-'.$id.'" data-autofilter="'.$auto_filter.'"><ul>';
        foreach ($values as $f)
        {
			$num_items = $this->getNumItems('f', $field->id, $f->value);
        	$num_items = (!empty($num_items)) ? sprintf(self::COUNT_ITEMS_TXT, $num_items) : '';
        	$cls = '';
        	if(is_array($defaut_values) && in_array($f->value, $defaut_values)) {
        		$cls = 'selected';
        		$selected .= '<span id="mg-'.$id.'-'.$f->value.'">'.$f->name . $num_items.'<input type="hidden" name="xf_'.$field->id.'[]" value="'.$f->value.'" /><span title="Remove" class="remove"></span></span>';
        	}
        	$html .= '<li rel="'.$f->value.'" class="'.$cls.'">'.$f->name . $num_items.'</li>';
        }
        $html .= '</ul>';
		$html .= '<span class="btn-close" onclick="jaMagicSelectClose(this, \'mg-'.$id.'\'); return false;">Close</span>';
		$html .= '<span class="arrow">&nbsp;</span>';
		$html .= '</div>';
        $html .= '<div id="mg-'.$id.'-container" class="ja-magic-select-container"></div>';
        
        $html .= '
<script type="text/javascript">
/*<![CDATA[*/
window.addEvent("domready", function(){
	jaMagicInit(\'mg-'.$id.'\', \''.$field->id.'\');
});
/*]]>*/
</script>';
	
        return $html;
	}
	
	public function getRadio($field,$ranges = NULL){
		$values = json_decode($field->value);
        $options = array();
		foreach ($values as $f)
        {
			$num_items = $this->getNumItems('f', $field->id, $f->value);
        	$num_items = (!empty($num_items)) ? sprintf(self::COUNT_ITEMS_TXT, $num_items) : '';
        	array_push($options, JHtml::_('select.option', $f->value, $f->name . $num_items));
        }
		$defaut_values = JRequest::getVar("xf_{$field->id}",null);
		
        return JHtml::_('select.radiolist', $options, 'xf_'.$field->id, array('class'=>'exfield exgroup'.$field->group), 'value', 'text',$defaut_values);
	}
	
	public function getDate($field,$ranges = NULL){
		JHTML::_('behavior.calendar');
		$field->value = json_decode($field->value);
		
		$defaut_values = JRequest::getVar("xf_{$field->id}",null);
		
		return '<p>'.JHtml::calendar($defaut_values, 'xf_'.$field->id, 'xf_'.$field->id, '%Y-%m-%d', array('size' => '10', 'class'=>'date exfield exgroup'.$field->group)).'</p>';
	}
	public function getDaterange($field,$ranges = NULL){
		JHTML::_('behavior.calendar');
		$field->value = json_decode($field->value);
		
		$defaut_values_from = JRequest::getVar("xf_{$field->id}_from",null);
		$defaut_values_to   = JRequest::getVar("xf_{$field->id}_to",null);
		$datefrom = JHtml::calendar($defaut_values_from, 'xf_'.$field->id.'_from', 'xf_'.$field->id.'_from', '%Y-%m-%d', array('size' => '10', 'class'=>'date exfield exgroup'.$field->group));
		$dateto   = JHtml::calendar($defaut_values_to, 'xf_'.$field->id.'_to', 'xf_'.$field->id.'_to', '%Y-%m-%d', array('size' => '10', 'class'=>'date exfield exgroup'.$field->group));
		return '<p><label>'.JText::_('JA_K2_FROM').'</label>'.$datefrom.'</p><div class="clr"></div><p><label>'.JText::_('JA_K2_TO').'</label>'.$dateto.'</p>';
	}

	/*
	 * Get extra field groups
	 * */
	public function getextraFieldsGroups($extrafields){
		$db = JFactory::getDbo();
		if(is_array($extrafields) && count($extrafields)>0){	
			$extrafields = implode(',', $extrafields);
		}
		$query = "SELECT `group` FROM #__k2_extra_fields WHERE `id` IN ($extrafields) GROUP BY `group`";
		
		$db->setQuery($query);
		$result = $db->loadColumn();
		return $result;
	}
	/*
	 * Get parent category Associated "Extra Fields Group"
	 * */
	public function categoriesTree($groupcategories=NULL,$row = NULL, $hideTrashed = false, $hideUnpublished = true)
    {

        $db = JFactory::getDBO();
        if (isset($row->id))
        {
            $idCheck = ' AND id != '.( int )$row->id;
        }
        else
        {
            $idCheck = null;
        }
        if (!isset($row->parent))
        {
            if (is_null($row))
            {
                $row = new stdClass;
            }
            $row->parent = 0;
        }
        $query = "SELECT m.* FROM #__k2_categories m WHERE id > 0 {$idCheck}";

        if ($hideUnpublished)
        {
            $query .= " AND published=1 ";
        }

        if ($hideTrashed)
        {
            $query .= " AND trash=0 ";
        }
		if($groupcategories){
			if(is_array($groupcategories)){
				if(!in_array('0', $groupcategories)){
					$query .= " AND id IN (".implode(',', $groupcategories).")";
				}
			}
		}
        $query .= " ORDER BY parent, ordering";
        
        $db->setQuery($query);
        $mitems = $db->loadObjectList();
        $children = array();
        if ($mitems)
        {
            foreach ($mitems as $v)
            {
                if (K2_JVERSION != '15')
                {
                    $v->title = $v->name;
                    $v->parent_id = $v->parent;
                }
                $pt = $v->parent;
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        $mitems = array();
        foreach ($list as $item)
        {
            $item->treename = JString::str_ireplace('&#160;', '- ', $item->treename);

            if ($item->trash)
                $item->treename .= ' [**'.JText::_('K2_TRASHED_CATEGORY').'**]';
            if (!$item->published)
                $item->treename .= ' [**'.JText::_('K2_UNPUBLISHED_CATEGORY').'**]';

            //$mitems[] = JHTML::_('select.option', $item->id, $item->treename);
			$num_items = $this->getNumItems('c', $item->id);
        	$num_items = (!empty($num_items)) ? sprintf(self::COUNT_ITEMS_TXT, $num_items) : '';
			$mitems[] = JHTML::_('select.option', $item->id, $item->treename.$num_items,array('option.attr' => 'rel', 'attr'=>array('rel' => $item->extraFieldsGroup)));
        }
        return $mitems;
    }

	/*
	 * Return category list
	 * */
	public function getCategories($groupcategories){
		
		$cat_id = JRequest::getVar('category_id',0);
		
		//$categories_option[]=JHTML::_('select.option', 0, JText::_('SELECT_CATEGORY_FRONT'));
		
		if(is_array($groupcategories)){
			if(($key = array_search(0, $groupcategories)) !== false) {
				unset($groupcategories[$key]);
			}
			$categories_option[]=JHTML::_('select.option', implode(",",$groupcategories), JText::_('SELECT_CATEGORY_FRONT'));
		}else{
			$categories_option[]=JHTML::_('select.option', 0, JText::_('SELECT_CATEGORY_FRONT'));
		}
		
		$categories = $this->categoriesTree($groupcategories,NULL, true, true);
		
		$categories_options=@array_merge($categories_option, $categories);
		
		$attribs = array('option.attr' => 'rel', 'option.key' => 'value', 'option.text' => 'text', 
						'list.attr' => array('class' => 'inputbox', 'onchange'=>'jak2DisplayExtraFields(this);'),
						'list.select' => $cat_id);
		
		//Important Note: do not add more parametter after $attribs, add them into $attribs
		$categories_html = JHTML::_('select.genericlist',  $categories_options, 'category_id', $attribs);
		
		return $categories_html;
	}
	/*
	 * Return author list selected
	 * */
	public function getAuthors($filter_author_display){
		$db    =JFactory::getDBO();
		$query = 'SELECT DISTINCT u.*
		    FROM #__users AS u INNER JOIN #__k2_items AS i ON u.id = i.created_by
	        WHERE i.published = 1
			ORDER BY u.id';
		
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		$html = array();
		$html[] = JHTML::_('select.option',  0, JText::_('JAK2_FILTER_SELECT_AUTHOR') );
		if(count($rows)>0){
			foreach ($rows as $row) {
				$num_items = $this->getNumItems('a', $row->id);
	        	$num_items = (!empty($num_items)) ? sprintf(self::COUNT_ITEMS_TXT, $num_items) : '';
				$html[] = JHTML::_('select.option',  $row->id, ($filter_author_display == 'author_display_name')?$row->name.$num_items:$row->username.$num_items );
			}
		}
		$attribs = array('class' => 'inputbox');
		 
		$authors_id = JRequest::getInt('created_by',0);
		
		$author_html = JHTML::_('select.genericlist',  $html, 'created_by', $attribs, 'value', 'text',$authors_id);
		return $author_html;
	}
	
	public function getNumItems($type, $id, $optid = 0) {
		static $data = null;
		if(!$this->counter) return '';
		if(is_null($data)) {
			$db = JFactory::getDbo();
			$query = "SELECT `value` FROM #__jak2filter WHERE `name` = 'indexing'";
			$db->setQuery($query);
			$result = $db->loadResult();
			$data = ($result) ? json_decode($result) : null;
		}
		if(is_object($data)) {
			$key = ($type == 'f') ? sprintf('%s.%d.%d', $type, $id, $optid) : sprintf('%s.%d', $type, $id);
			if(isset($data->$key)) {
				return $data->$key;
			}
		}
		return '';
	}
}
