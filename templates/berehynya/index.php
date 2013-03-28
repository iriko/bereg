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
$app = JFactory::getApplication();
?>
<?php echo '<?'; ?>xml version="1.0" encoding="<?php echo $this->_charset ?>"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
	<head>

		<!-- The following line loads the template CSS file located in the template folder. -->
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
        <!-- The following JDOC Head tag loads all the header and meta information from your site config and content. -->
        <jdoc:include type="head" />

    </head>

	<body>
    <div id="header">
        <h1><a href="http://berehynya.loc" tppabs="http://berehynya.loc/">Берегиня</a></h1>
        <div class="menutop">
            <jdoc:include type="modules" name="position-0" />
        </div>
    </div>
    <div class="text">
        <jdoc:include type="modules" name="position-12"/>
    </div>
        <div class="slider">
            <jdoc:include type="modules" name="position-1"/>
        </div>

    </body>
</html>
