<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of Youtube Gallery component
 */
class YoutubeGalleryController extends JController
{
        /**
         * display task
         *
         * @return void
         */
        function display($cachable = false, $urlparams = null) 
        {
                // set default view if not set
                JRequest::setVar('view', JRequest::getCmd('view', 'linkslist'));
                
                // call parent behavior
                parent::display($cachable);
        }
}
