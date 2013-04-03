<?php
/**
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/* The following line loads the MooTools JavaScript Library */
JHtml::_('behavior.framework', true);

/* The following line gets the application object for things like displaying the site name */
$doc = JFactory::getDocument();
$doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/template.css', $type = 'text/css', $media = 'screen,projection');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
	<head>
        <jdoc:include type="head" />
    </head>

	<body>
        <div class="page-holder">

            <div class="header-holder">
                <div class="header">
                    <div class="logo"><a href="<?php echo $this->baseurl ?>/"><img src="<?php echo $this->baseurl.'/templates/'.$this->template.'/images/logo.jpg'?>" alt="Бегериня"></a></div>
                    <div class="header-menu">
                        <jdoc:include type="modules" name="position-1" />
                    </div>
                </div>
            </div><!--header-holder-->

            <div class="content-holder">

                <jdoc:include type="modules" name="position-2" style="home"/>

                <div class="content-area">
                    <jdoc:include type="message" />
                    <jdoc:include type="component" />
                </div>

            </div><!--content-holder-->

            <div class="footer-holder">
                <div class="footer-menu">
                    <jdoc:include type="modules" name="position-3" />
                </div>
            </div><!--footer-holder-->

        </div><!--page-holder-->
    </body>
</html>
