<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     3.7.0.1485
 * @date		2012-11-26
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die;

/**
 * Compatibility layer for 3rdt party plugins
 *
 *
 * @author yannick
 *
 */
class shRouter  {

  public static function &shGetConfig() {

    $config = & Sh404sefFactory::getConfig();

    return $config;
  }

  public static function &shPageInfo() {

    $config = & Sh404sefFactory::getPageInfo();

    return $config;
  }

}