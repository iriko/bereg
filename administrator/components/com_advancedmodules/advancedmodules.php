<?php
/**
 * @package         Advanced Module Manager
 * @version         4.4.3
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2012 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @package        Joomla.Administrator
 * @subpackage     com_advancedmodules
 * @copyright      Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_advancedmodules')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$lang = JFactory::getLanguage();
$lang->load('com_modules', JPATH_ADMINISTRATOR);
if ($lang->getTag() != 'en-GB') {
	// Loads English language file as fallback (for undefined stuff in other language file)
	$lang->load('com_advancedmodules', JPATH_ADMINISTRATOR, 'en-GB');
}
$lang->load('com_advancedmodules', JPATH_ADMINISTRATOR, null, 1);

jimport('joomla.filesystem.file');

// return if NoNumber Framework plugin is not installed
if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php')) {
	JFactory::getApplication()->set('_messageQueue', '');
	$msg = JText::_('AMM_NONUMBER_FRAMEWORK_NOT_INSTALLED')
		. ' ' . JText::sprintf('AMM_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_ADVANCEDMODULES'));
	JFactory::getApplication()->enqueueMessage($msg, 'error');
	return;
}

// give notice if NoNumber Framework plugin is not enabled
$nnep = JPluginHelper::getPlugin('system', 'nnframework');
if (!isset($nnep->name)) {
	JFactory::getApplication()->set('_messageQueue', '');
	$msg = JText::_('AMM_NONUMBER_FRAMEWORK_NOT_ENABLED')
		. ' ' . JText::sprintf('AMM_EXTENSION_MAY_NOT_FUNCTION', JText::_('COM_ADVANCEDMODULES'));
	JFactory::getApplication()->enqueueMessage($msg, 'notice');
}

// load the NoNumber Framework language file
if ($lang->getTag() != 'en-GB') {
	// Loads English language file as fallback (for undefined stuff in other language file)
	$lang->load('plg_system_nnframework', JPATH_ADMINISTRATOR, 'en-GB');
}
$lang->load('plg_system_nnframework', JPATH_ADMINISTRATOR, null, 1);

require_once JPATH_PLUGINS . '/system/nnframework/helpers/versions.php';
$versions = NNVersions::getInstance();
$version = '';
$xml = JApplicationHelper::parseXMLInstallFile(JPATH_ADMINISTRATOR . '/components/com_advancedmodules/advancedmodules.xml');
if ($xml && isset($xml['version'])) {
	$version = $xml['version'];
}
if (JFactory::getApplication()->input->get('view', 'modules') == 'modules') {
	$versions->setMessage($version, 'advancedmodules', 'http://www.nonumber.nl/versions', 'http://www.nonumber.nl/advancedmodules/download');
}

$controller = JControllerLegacy::getInstance('AdvancedModules');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

// PRO Check
if (JFactory::getApplication()->input->get('view', 'modules') == 'modules') {
	require_once JPATH_PLUGINS . '/system/nnframework/helpers/licenses.php';
	$licenses = NNLicenses::getInstance();
	echo $licenses->getMessage('ADVANCED_MODULE_MANAGER');
}

// Copyright
if (in_array(JFactory::getApplication()->input->get('view', 'modules'), array('modules', 'module'))) {
	echo $versions->getCopyright(JText::_('ADVANCED_MODULE_MANAGER'), $version, 10307);
}
