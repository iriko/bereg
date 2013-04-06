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

// Security check to ensure this file is being included by a parent file.
defined( '_JEXEC' ) or die;

class Sh404sefHelperLanguage {

  /**
   * Find a language family
   *
   * @param object $language a Joomla! language object
   * @return string a 2 or 3 characters language family code
   */
  public static function getFamily( $language = null) {


    if (!is_object($language)) {

      // get application db instance
      $language = JFactory::getLanguage();

    }

    $code = $language->get( 'lang');
    $bits = explode( '-', $code);
    return empty($bits[0]) ? 'en' : $bits[0];
  }

}