<?php
/**
 * YoutubeGallery Joomla! 2.5 Native Component
 * @version 3.2.9
 * @author DesignCompass corp< <admin@designcompasscorp.com>
 * @link http://www.joomlaboat.com
 * @license GNU/GPL
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by Youtube Gallery
$controller = JController::getInstance('YoutubeGallery');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();


?>


