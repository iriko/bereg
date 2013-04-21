<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/*jimport('joomla.form.formfield');
include_once(JPATH_LIBRARIES.'/joomla/form/fields/checkboxes.php');*/
/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since       11.1
 */
if( JFile::exists(JPATH_BASE.DS.'components'.DS.'com_k2'.DS.'k2.php')){
	class JFormFieldK2extrafield extends JFormField
	{
		/**
		 * The form field type.
		 *
		 * @var    string
		 *
		 * @since  11.1
		 */
		protected $type = 'K2extrafield';
		
		/**
		 * Method to get the field input markup for check boxes.
		 *
		 * @return  string  The field input markup.
		 *
		 * @since   11.1
		 */
		function getLabel(){
		}
		function legend(){
			return '<legend class="jalegend">' . JText::_($this->element['label']) .'</legend>';
		}
		protected function getInput()
		{
			
			// Initialize variables.
			$html = array();

			$this->getModuleParams();
			// Initialize some field attributes.
			$class = $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';

			// Start the checkbox field output.
			$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';
			$html[] = $this->legend();
			// Get the field options.
			$options = $this->getOptions();

			// Build the checkbox field output.
			
			$group = 0;
			$params = $this->getModuleParams();
			$groups = array();
			foreach ($options as $i => $option)
			{
				if($group != $option->group) {
					$group = $option->group;
					$groups[$option->group]=$option->gname;	
				}
			}
			foreach($groups AS $key=>$g){
				$html[] = '<h4 class="jagroup">'.JText::_('EXTRA_FIELDS_GROUP').$g.'</h4>';
				$html[] = '<ul>';
				$k=0;
				$class_li = '';
				foreach($options as $i => $option){
					if($option->group == $key){
						if($k%2 == 0){
							$class_li = 'class="even"';
						}else{
							$class_li = 'class="odd"';
						}
						$k++;
						// Initialize some option attributes.
						$checked = (in_array((string) $option->value, (array) $this->value) ? ' checked="checked"' : '');
						$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
						$disabled = !empty($option->disable) ? ' disabled="disabled"' : '';
						// Initialize some JavaScript option attributes.
						$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

						$html[] = '<li '.$class_li.'>';
						$html[] = '<input type="checkbox" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
							. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . '/>';
						$html[] = '<label ' . $class . '>' . JText::_($option->text) . '</label><br />';
						
						$html[] = $this->getFieldType($option, $params);
						$html[] = '</li>';
					}
				}
				$html[] = '</ul>';
			}
			
			// End the checkbox field output.
			$html[] = '</fieldset>';

			return implode($html);
		}
		
		protected function getModuleParams() {
			$modid = JRequest::getVar('id');
			$tableMod = JTable::getInstance('Module', 'JTable');
			$tableMod->load($modid);
			
			$data = isset($tableMod->params) ? $tableMod->params : '';
			$params = new JRegistry($data);
			return $params;
		}
		
		protected function getOptions()
		{
			$db = JFactory::getDbo();
			$query = "
				SELECT f.id, f.name AS fname, f.group, f.type, f.published, g.name AS gname
				FROM #__k2_extra_fields f
				INNER JOIN #__k2_extra_fields_groups g ON g.id = f.group
				WHERE f.published = 1
				AND f.type <> 'csv'
				ORDER BY f.group, f.ordering
				";
			$db->setQuery($query);
			$list = $db->loadAssocList();
			// Initialize variables.
			$options = array();

			if(count($list)) {
				foreach ($list as $option)
				{
		
					// Create a new option object based on the <option /> element.
					$tmp = JHtml::_(
						'select.option', $option['id'], $option['fname'] . ' (ID: '.$option['id'].') <span class="ja-field-note">'.JText::sprintf('K2_FIELD_TYPE', $option['type']).'</span>', 'value', 'text',
						($option['published'] == 0)
					);
		
					// Set some option attributes.
					$tmp->class = '';
		
					// Set some JavaScript option attributes.
					$tmp->onclick 	= '';
					$tmp->title		= $option['fname'];
					$tmp->type 		= $option['type'];
					$tmp->group 	= $option['group'];
					$tmp->gname 	= $option['gname'];
		
					// Add the option object to the result set.
					$options[] = $tmp;
				}
			}

			reset($options);

			return $options;
		}
		
		protected function getFieldType($option, $params) {
			//$name = $this->element['name'].'_type'.$option->value;
			$prefix = $option->value.':';
			$name = 'filter_by_fieldtype';
			$id = $name.'_'.$option->value;
			$typeOptions = array();
			$type = $option->type;
			
			$showRangeField = 0;
			$attribs = array();
			switch ($type) {
				case 'select':
				case 'multipleSelect':
				case 'radio':
					$typeOptions[] = JHTML::_('select.option', $prefix.'magicSelect', JText::_('FILTER_TYPE_MAGIC_SELECTION'));
					$typeOptions[] = JHTML::_('select.option', $prefix.'select', JText::_('FILTER_TYPE_DROPDOWN_SELECTION'));
					$typeOptions[] = JHTML::_('select.option', $prefix.'multipleSelect', JText::_('FILTER_TYPE_MULTISELECT_LIST'));
					$typeOptions[] = JHTML::_('select.option', $prefix.'radio', JText::_('FILTER_TYPE_RADIO_BUTTONS'));
					$typeOptions[] = JHTML::_('select.option', $prefix.'checkbox', JText::_('FILTER_TYPE_CHECKBOX'));
					break;
				case 'date':
					$typeOptions[] = JHTML::_('select.option', $prefix.'date', JText::_('FILTER_TYPE_DATE'));
					$typeOptions[] = JHTML::_('select.option', $prefix.'daterange', JText::_('FILTER_TYPE_DATERANGE'));
					break;
				case 'csv':
					break;
				case 'labels':
					$showRangeField = 1;
					$containerId = $id . '_vcontainer';
					$typeOptions[] = JHTML::_('select.option', $prefix.'textfield', JText::_('FILTER_TYPE_TEXT_FIELD'));
					break;	
				case 'textfield':
				case 'textarea':
				case 'link':
				
				default:
					$showRangeField = 1;
					$containerId = $id . '_vcontainer';
					$attribs['onchange'] = "if(this.value == '{$prefix}valuerange') { $('".$containerId."').show(); } else { $('".$containerId."').hide(); } return;";
					$typeOptions[] = JHTML::_('select.option', $prefix.'textfield', JText::_('FILTER_TYPE_TEXT_FIELD'));
					$typeOptions[] = JHTML::_('select.option', $prefix.'valuerange', JText::_('FILTER_TYPE_VALUE_RANGE'));
					break;
			}
			
			//$list = '<span>'.JText::_('SELECT_SEARCH_TYPE').'</span>';
			$values = $params->get($name);
			$val = $prefix.$type;
			$selectedIndex = -1;
			if(is_array($values) && count($values)) {
				foreach ($values as $index => $valSelected) {
					if (strpos($valSelected, $prefix) === 0) {
						$selectedIndex = $index;
						$val = $valSelected;
						break;
					}
				}
			}
			$css = ($val == $prefix.'valuerange') ? ' display:block;' : ' display:none;';
			
			$attribs['class'] = 'hasTip';
			$attribs['title'] = JText::sprintf('SELECT_SEARCH_TYPE', $option->title, $option->title);
			$list = JHTML::_('select.genericlist', $typeOptions, $this->getName($name), $attribs, 'value', 'text', $params->get($name, $val), $id);
			
			//Range field
			$rName = 'filter_by_fieldrange';
			$rId = $rName . '_' . $option->value;
			if($showRangeField) {
				$val = '';
				$values = $params->get($rName);
				if(is_array($values) && count($values) && isset($values[$selectedIndex])) {
					$val = $values[$selectedIndex];
				}
				$list .= '<div id="'.$containerId.'" style="clear:both; '.$css.'">';
				$list .= '<input class="hasTip" type="text" id="'.$rId.'" name="'.$this->getName($rName).'" value="'.$val.'" size="30" title="'.JText::_('JA_VALUE_RANGE_DESC').'" />';
				$list .= '</div>';
			} else {
				//must hidden field to get correct index
				$list .= '<input type="hidden" id="'.$rId.'" name="'.$this->getName($rName).'" value="" />';
			}
			return $list;
		}
	}
}