<?php
/**
 * @version 1.0.0
 * @package Additional Categories for K2
 * @author Thodoris Bgenopoulos <teobgeno@netpin.gr>
 * @link http://www.netpin.gr
 * @copyright Copyright (c) 2012 netpin.gr
 * @license GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.installer.installer');

$status = new JObject();
$status->plugins = array();

$src = $this->parent->getPath('source');

if(version_compare( JVERSION, '1.6.0', 'ge' )) {
	$plugins = &$this->manifest->xpath('plugins/plugin');
	foreach($plugins as $plugin){
		$pname = $plugin->getAttribute('plugin');
		$pgroup = $plugin->getAttribute('group');
		$path = $src.DS.'plugins'.DS.$pgroup;
		$installer = new JInstaller;
		$result = $installer->install($path);
		$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);
		$query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=".$db->Quote($pname)." AND folder=".$db->Quote($pgroup);
		$db->setQuery($query);
		$db->query();
	}
}
else {
	$plugins = &$this->manifest->getElementByPath('plugins');
	if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {

		foreach ($plugins->children() as $plugin) {
			$pname = $plugin->attributes('plugin');
			$pgroup = $plugin->attributes('group');
			$path = $src.DS.'plugins'.DS.$pgroup;
			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);

			$query = "UPDATE #__plugins SET published=1 WHERE element=".$db->Quote($pname)." AND folder=".$db->Quote($pgroup);
			$db->setQuery($query);
			$db->query();
		}
	}

}

?>
<h2><a target='_blank' href='http://www.netpin.gr/'>Additional Categories for K2 Plugin v1.0.0</a></h2>
<a target='_blank' href='http://www.netpin.gr/'>
   <img style='float:left;background:#fff;padding:2px;margin:0 0 8px 0px;' src='../media/k2additonalcategories/addcat_logo_219x125_24.png' border='0' alt='Additional Categories for K2'/>
</a>
<b>Additional Categories for K2</b> allows you to assign a K2 item to more than one K2 category.<br/><br/>You may also want to check out the following resources:

<ul>
 <li>
   <a target='_blank' href='http://www.netpin.gr/documentation/item/2-k2-additional-categories'>Additional Categories for K2 documentation</a>.
  </li>
</ul>
 
 <b>Additional Categories for K2</b> is a <a target='_blank' href='http://getk2.org/'>K2</a> plugin developed by <a target='_blank' title='netpin.gr' href='http://www.netpin.gr'>Thodoris Bgenopoulos</a>, released under the <a target='_blank' title='GNU General Public License' href='http://www.gnu.org/copyleft/gpl.html'>GNU General Public License</a>.<br/><br/>Copyright &copy; 2012 netpin.gr. All rights reserved.<br/><br/><i>(Last update: April 26th, 2012 - Version 1.0.0)</i>
 <br />
 <br />