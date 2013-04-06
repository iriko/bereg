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
$this->setGenerator('Beregynya site');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
	<head>
        <jdoc:include type="head" />
        <script type="text/javascript">
            jQuery(document).ready(function() {
                //Для все картинок на сайте
                jQuery("img").removeAttr("title");
                //Добавляем клас sub-menu
                jQuery('.header-menu .deeper > ul').addClass('sub-menu');
            })
        </script>
    </head>

	<body>
        <div class="wrapper-holder">

            <div class="header-holder">
                <div class="header">

                    <div class="logo"><a href="<?php echo $this->baseurl ?>/"><img src="<?php echo $this->baseurl.'/templates/'.$this->template.'/images/logo.jpg'?>" alt="Бегериня"></a></div>

                    <jdoc:include type="modules" name="position-1" />

                </div>
            </div><!--header-holder-->

            <div class="page-holder">
                <div class="content-holder">

                    <?php if (JURI::current() == JURI::base()) : ?>
                        <jdoc:include type="modules" name="position-2" style="home"/>
                    <?php endif; ?>

                    <jdoc:include type="modules" name="position-4"/>

                    <div class="content-area">
                        <jdoc:include type="message" />
                        <jdoc:include type="component" />
                    </div>

                </div>
            </div><!--page-holder-->

            <div class="footer-holder">
                <div class="footer">

                    <div class="copyright">Черкаський колегіум "Берегиня" <?php echo date('Y') ?></div>

                    <jdoc:include type="modules" name="position-3" />

                </div>
            </div><!--footer-holder-->

        </div><!--wrapper-holder-->
    </body>
</html>
