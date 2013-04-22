<?php
/************************************************************************************
 mod_aidanews2 for Joomla 1.7/2.5 by Danilo A.

 @author: Danilo A. - dan@cdh.it

 ----- This file is part of the AiDaNews2 Module. -----

    AiDaNews2 Module is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    AiDaNews2 is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this module.  If not, see <http://www.gnu.org/licenses/>.
************************************************************************************/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

class modAiDaNews2Helper{

	function imgpreflist_arrange ($p) {
		$i = 0;
		while ($i < 5) {
			if ($p[$i] != 0) {
				$j = $i+1;
				while ($j < 5) {
					if (($p[$j] == $p[$i]) || ($p[$i] == 9 && $p[$j] == 10) || ($p[$i] == 10 && $p[$j] == 9) || ($p[$i] == 2 && $p[$j] == 3) || 
					($p[$i] == 11 && $p[$j] == 12) || ($p[$i] == 12 && $p[$j] == 11) || ($p[$i] == 3 && $p[$j] == 2))  $p[$j] = 0;
					$j++;
				}
			}
			$i++;
		}
	}
	
	function imgpreflist_findlink ($p, $txt, $cimg, $art_images, $uid, $num) {
		$db	=& JFactory::getDBO();
		$i = 0;
		$imgurl="";
		while ($i < 5 && empty($imgurl)) {
			if ($p[$i] == 1) { //Default
				$imgurl = "modules/mod_aidanews2/img/aidadefault" . $num . ".jpg-default";
			}elseif ($p[$i] == 2) { //First IMG Tag
				$matches = array();
				if (preg_match("#<img[^>]+src=['|\"](.*?)['|\"][^>]*>#i", $txt, $matches)){$imgurl = $matches[1] . '-first';}
			}elseif ($p[$i] == 3) { //Last IMG Tag
				$matches = array();
				if (preg_match_all("#<img[^>]+src=['|\"](.*?)['|\"][^>]*>#i", $txt, $matches)){
					$hll = $matches[1]; $li = 0;
					foreach ($hll as $hl) {
						$li++;
					}
					$imgurl = $hll[$li-1] . '-last';
				}
			}elseif ($p[$i] == 4) { //Category IMG
				if ($cimg) { //"image":"images\/banners\/white.png"
					if (preg_match("#['|\"]image['|\"]:['|\"](.*?)['|\"]#i", $cimg, $matches)) {
						$imgurl = $matches[1];
						$imgurl = preg_replace( "#\\\#", '', $imgurl ) . '-cat';
					}
				}
			}elseif ($p[$i] == 5) { //JomSocial Avatar (Full)
				$query = 'SELECT avatar FROM #__community_users WHERE userid = ' . $uid;
					$db->setQuery($query);
					$imgurl = $db->loadResult() . '-joma';
			}elseif ($p[$i] == 13) { //JomSocial Avatar (Thumb)
				$query = 'SELECT thumb FROM #__community_users WHERE userid = ' . $uid;
					$db->setQuery($query);
					$imgurl = $db->loadResult() . '-jomt';
			}elseif ($p[$i] == 6) { // CB Avatar
				$query = 'SELECT avatar FROM #__comprofiler WHERE id = ' . $uid;
					$db->setQuery($query);
					$cbavatar = $db->loadResult();
					if ($cbavatar) { $imgurl = 'images/comprofiler/' . $cbavatar . 'cbav'; }
			}elseif ($p[$i] == 7) {
				$query = 'SELECT avatar FROM #__kunena_users WHERE userid = ' . $uid;
					$db->setQuery($query);
					$kavatar = $db->loadResult();
					if ($kavatar) { $imgurl = 'media/kunena/avatars/' . $kavatar . '-kavatar'; }
			}elseif ($p[$i] == 8) { //JSocialSuite Avatar
			}elseif ($p[$i] == 9) { //First YouTube URL
				$vid = "";
				$matches = array();
				if (preg_match("'{youtube}([^<]*){/youtube}'si", $txt, $matches)){
					$vid = $matches[1];
				}elseif(preg_match('~(http://www\.youtube\.com/watch\?v=[%&=#\w-]*)~',$txt,$matches)){
					$url = $matches[1];
					if (preg_match('%youtube\\.com/(.+)%', $url, $match)) {
						$match = $match[1];
						$replace = array("watch?v=", "v/", "vi/");
						$vid = str_replace($replace, "", $match);
					}
				}
				if ($vid) {
					if (strlen($vid) > 11) {
						$vid = substr($vid, 0, 11);
					}
					$imgurl = "http://img.youtube.com/vi/" . $vid . "/0.jpg-ftube";
				}
			}elseif ($p[$i] == 10) { //Last Youtube TAG
				$vid = "";
				$matches = array();
				if (preg_match_all("'{youtube}([^<]*){/youtube}'si", $txt, $matches)){
					$vhh = $matches[1]; $vc = 0;
					foreach ($vhh as $vh) {
						$vc++;
					}
					$vid = $vhh[$vc-1];
				}elseif(preg_match_all('~(http://www\.youtube\.com/watch\?v=[%&=#\w-]*)~',$txt,$matches)){
					$vhh = $matches[1]; $vc = 0;
					foreach ($vhh as $vh) {
						$vc++;
					}
					$url = $vhh[$vc-1];
					if (preg_match('%youtube\\.com/(.+)%', $url, $match)) {
						$match = $match[1];
						$replace = array("watch?v=", "v/", "vi/");
						$vid = str_replace($replace, "", $match);
					}
				}
				if ($vid) {
					if (strlen($vid) > 11) {
						$vid = substr($vid, 0, 11);
					}
					$imgurl = "http://img.youtube.com/vi/" . $vid . "/0.jpg-ltube";
				}
			}elseif ($p[$i] == 11) { // First Gallery TAG
				$gal = "";
				if (preg_match("'{gallery}([^<]*){/gallery}'si", $txt, $matches)){$gal = $matches[1];}
				if ($gal) {
					$fold = $params->get('basegal') . '/' . $gal;
					$d = dir($fold) or die("Wrong path: $fold");
					$gimages = array();
					while (false !== ($entry = $d->read())) {
						if($entry != '.' && $entry != '..' && !is_dir($entry)) {
							$gimages[] = $entry;
						}
					}
					$d->close();
					$gimgurl = $gimages[0];
					if (($gimgurl == "index.htm") || ($gimgurl == "index.html")) {
						$gimgurl = $gimages[1];
					}
					$imgurl = $fold . '/' . $gimgurl . '-fgal';
				}
			}elseif ($p[$i] == 12) { //Last Gallery TAG
				$gal = "";
				if (preg_match_all("'{gallery}([^<]*){/gallery}'si", $txt, $matches)){
					$hal = $matches[1]; $hac = 0;
					foreach ($hal as $ha) {
						$hac++;
					}
					$gal = $hal[$hac-1];
				}
				if ($gal) {
					$fold = $params->get('basegal') . '/' . $gal;
					$d = dir($fold) or die("Wrong path: $fold");
					$gimages = array();
					while (false !== ($entry = $d->read())) {
						if($entry != '.' && $entry != '..' && !is_dir($entry)) {
							$gimages[] = $entry;
						}
					}
					$d->close();
					$gimgurl = $gimages[0];
					if (($gimgurl == "index.htm") || ($gimgurl == "index.html")) {
						$gimgurl = $gimages[1];
					}
					$imgurl = $fold . '/' . $gimgurl . '-lgal';
				}
			}elseif ($p[$i] == 15) { //Joomla! Image Field #1 (intro)
				if ($art_images) { //"image":"images\/banners\/white.png"
					if (preg_match("#['|\"]image_intro['|\"]:['|\"](.*?)['|\"]#i", $art_images, $matches)) {
						$imgurl = $matches[1];
						$imgurl = preg_replace( "#\\\#", '', $imgurl ) . '-jimgi';						
					}
				}
			}elseif ($p[$i] == 14) { //Joomla! Image Field #1 (fulltext)
				if ($art_images) { //"image":"images\/banners\/white.png"
					if (preg_match("#['|\"]image_fulltext['|\"]:['|\"](.*?)['|\"]#i", $art_images, $matches)) {
						$imgurl = $matches[1];
						$imgurl = preg_replace( "#\\\#", '', $imgurl ) . '-jimgf';
					}
				}
			}
			$i++;
		}
		return $imgurl;
	}

	function shorten($txt, $cut, $type, $end){
		if ($cut > 0) {
			if ($type){
				$cut += 5;
				if (function_exists('mb_substr')) {
					$txt = mb_substr($txt, 0, $cut, 'UTF-8');
					if ($space_pos = mb_strrpos($txt," ")){
						$txt = mb_substr($txt, 0, $space_pos, 'UTF-8');
					}
				}else{
					$txt = substr($txt, 0, $cut);
					if ($space_pos = strrpos($txt," ")){
						$txt = substr($txt, 0, $space_pos);
					}
				}

				$txt .= $end;
			}else{
				$array = explode(" ", $txt);
				if (count($array)<= $cut) {
					//Do nothing
				}else{
					array_splice($array, $cut);
					$txt = implode(" ", $array) . $end;
				}
			}
		}
		//$txt = str_replace('"', '&quot;', $txt); // Moved below
		return $txt;
	}
	
	function creaThumb ($image, $params, $num, $id, $alias) {
	
		if (!class_exists("PhpThumbFactory"))
			require_once 'modules/mod_aidanews2/lib/ThumbLib.inc.php';
		
		$options = array('jpegQuality' => $params->get('quality'), 'resizeUp' => 1);
	
		//First of all, let's get and remove the tag!
		
		$ll = strrpos($image, '-');
		$imgtag = substr($image, $ll);
		$image = substr($image, 0, $ll);
		
		//Adjust Alias
		
		$alias = strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($alias, ENT_QUOTES, 'UTF-8'))), ' '));
		
		//Check if thumbnails folder exists - if not, create it
		
		$folder = 'cache/mod_aidanews2/thumbs/';
		
		if (!is_dir($folder)) {
			if(!is_dir('cache/mod_aidanews2')) {
				if(!is_dir('cache'))
					mkdir('cache');
				mkdir('cache/mod_aidanews2');
			}
			mkdir('cache/mod_aidanews2/thumbs/');
		}
		
		//If the module has a Thumb Suffix, get it and adjust it
		if ($params->get('tsubf')) {
			$folder .= $params->get('tsubf') . '/';
			if (!is_dir($folder)) {
				mkdir($folder);
			}
		}
		
		$newtb = $folder . $id . '-' . $alias . '-' . $num . $imgtag . ".jpg";
		

		if (!file_exists($newtb)) {
			
			$imageHeight = $params->get('img' . $num . 'H');
			$imageWidth = $params->get('img' . $num . 'W');
			if ($imageHeight == "auto") $imageHeight = 0;
			if ($imageWidth == "auto") $imageWidth = 0;
			
			try{
				$tb = PhpThumbFactory::create($image, $options);
			} catch (Exception $e) {
				echo "Errore nella creazione della thumbnail";
			}
			
			if(($imageHeight && empty($imageWidth)) || (empty($imageHeight) && $imageWidth)) {
				if (empty($imageWidth)) $imageWidth = $imageHeight;
				$tb->resize($imageWidth, $imageWidth);
			}elseif($imageHeight && $imageWidth) {
				$tb->adaptiveResize($imageWidth, $imageHeight);
			}else{
				$tb->resizePercent(100);
			}
			$tb->save($newtb, 'jpg');
		}
		
		return $newtb;
	}

	function getList(&$params) {
		global $mainframe;

		$db			=& JFactory::getDBO();
		$user		= JFactory::getUser();
		$userId		= (int) $user->get('id');

		$count		= (int) $params->get('count', 5);
		$offset		= (int) $params->get('offset', 0);
		
		
		$catid		= $params->get('catid');
		if ($params->get('incsub')) {
			for ($i = 0; $i <= count($catid)-1; $i++) {
				$query = 'SELECT id FROM #__categories WHERE extension = "com_content" AND parent_id = ' . $catid[$i];
				$db->setQuery($query);
				$subs = $db->loadResultArray();
				if($subs) {
					foreach ($subs as $s) {
						$catid[count($catid)] = $s;
					}
				}
			}
			if (is_array($catid)) $catid = implode(", ", $catid);
		}else{
			if (is_array($catid)) $catid = implode(", ", $catid);
		}
		
		
		$aid		= $user->get('access');
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$aid = 0;
		foreach ($authorised as $a) {
			if ($a > $aid) $aid = $a;
		};

		$nullDate	= $db->getNullDate();

		$date =& JFactory::getDate();
		$now = $date->toMySQL();
		
		$cssfields = false;
		if(($params->get('style') == 1) || ($params->get('style') == 2)) {
			$cssfields = true;
		}
		
		/* IMAGE CHECKS - Before activating everything concerning images or categories, make sure you need them! */
		
		$chpos = ' ' . $params->get("pos_head") . ' ' . $params->get("pos_topL") . ' ' . $params->get("pos_topR") . ' ' . $params->get("pos_mainL")
		 . ' ' . $params->get("pos_mainC") . ' ' . $params->get("pos_mainR") . ' ' . $params->get("pos_botL") . ' ' . $params->get("pos_botR")
		  . ' ' . $params->get("pos_foot");
		$chimg1 = strrpos ($chpos, '[image1]');
		$chimg2 = strrpos ($chpos, '[image2]');
		$chimg3 = strrpos ($chpos, '[image3]');
		$chcats = strrpos ($chpos, '[categories]');

		// User Filter
		$wauth = '';
		switch ($params->get( 'user_id' ))
		{
			case 'by_me':
				if ($userId)
				$wauth = '(a.created_by = ' . (int) $userId . ' OR a.modified_by = ' . (int) $userId . ')';
				if (!$userId) $wauth = ' AND (a.created_by = 0)';
				break;
			case 'not_me':
				if ($userId)
				$wauth = '(created_by <> ' . (int) $userId . ' AND modified_by <> ' . (int) $userId . ')';
				break;
			case 'sel':
				if (!$params->get('sauth')) {
					$authors = $params->get('authors');
					if (is_array($authors)) {
						$authors = implode(",", $authors);
					}
					$wauth = "a.created_by IN ( $authors )";
				}
				break;
		}
		
		// Options related to the article you're viewing at the moment
		
		if (($params->get('cco')) || ($params->get('sauth'))) {      
			$temp = JRequest::getVar('id');
			$cview = JRequest::getCmd('view');
				
			if($temp) {
				
				if ($cview == "article") {
				
				//Article View
				
					$temp = substr($temp,0,strpos($temp,':'));
					
					// Articles from the same category
					
					if ($params->get('cco')) {
						$query = 'SELECT catid FROM #__content WHERE id = ' . $temp;
							$db->setQuery($query);
							$catid = $db->loadResult();
					}
					
					// Articles by the same author
					
					if ($params->get('sauth')) {
						$query = 'SELECT created_by FROM #__content WHERE id = ' . $temp;
							$db->setQuery($query);
							$sauth = $db->loadResult();
							$wauth = " AND a.created_by = " . $sauth . " ";
					}
					
				}elseif ($cview == "category") {
				
				// Category View
				
					// Articles from the same category
				
					if ($params->get('cco')) {
						$catid = $temp;
					}
				
				}else{
					
					$temp = "";
					
				}
				
			}
		}
		
		// Prepare Comment table and column
		
		if ($params->get('ctab') == '3') {
			$ctable = '#__webeeComment_Comment';
			$cartcol = 'articleId';
		}elseif ($params->get('ctab') == '5') {
			$ctable = '#__yvcomment';
			$cartcol = 'parentid';
		}elseif ($params->get('ctab') == '6') {
			$ctable = '#__zimbcomment_comment';
			$cartcol = 'articleId';
		}elseif ($params->get('ctab') == '7') {
			$ctable = '#__rdbs_comment_comments';
			$cartcol = 'refid';
		}elseif ($params->get('ctab') == '8') {
			$ctable = '#__comments';
			$cartcol = 'cotid';
		}elseif ($params->get('ctab') == '9') {
			$ctable = '#__jomcomment';
			$cartcol = 'contentid';
		}elseif ($params->get('ctab') == '10') {
			$ctable = "#__kunena_messages AS m JOIN #__kunenadiscuss AS d ON m.thread = d.thread_id";
			$cartcol = "d.content_id";
		}elseif ($params->get('ctab') == '4') {
			$ctable = '#__jcomments';
			$cartcol = 'object_id';
		}elseif ($params->get('ctab') == '11') {
			$ctable = '#__udjacomments';
			$cartcol = 'comment_url';
		}
		
		
		
		
		
		
		
		
		
		
		
		// Last X days
		$recent 				= $params->get('recent', 0);
		$recentwo 				= $params->get('recentwo', 0);
		






		
		// Related Articles

		$relatedcond = '';
		if ($params->get('related')) {
			$remp				= JRequest::getString('id');
			$remp				= explode(':', $remp);
			$id					= $remp[0];
			if ($id) {
				if ($params->get('related') == 1) {
				$query = 'SELECT metakey' .
					' FROM #__content' .
					' WHERE id = '.(int) $id;
					$db->setQuery($query);
					$metakey = trim($db->loadResult());
					
					if ($metakey) {
						// explode the meta keys on a comma
						$keys = explode(',', $metakey);
						$likes = array ();

						// assemble any non-blank word(s)
						$relatedcond="";
						foreach ($keys as $key) {
							$key = trim($key);
							if ($key) {
								$likes[] = ',' . $db->getEscaped($key) . ','; // surround with commas so first and last items have surrounding commas
							}
							$glue = "%' OR CONCAT(',', REPLACE(a.metakey,', ',','),',') LIKE '%";
							$relatedcond = "\n AND ( CONCAT(',', REPLACE(a.metakey,', ',','),',') LIKE '%" . implode( $glue , $likes) . "%' )";
						}
						$relnorepeat = "\n AND a.id <> " . $id;
						
						if (empty($relatedcond) && empty($relnorepeat)) {
							$relatedcond = "";
							$relnorepeat = "";
						}
					}else{
						$relatedcond = "\n AND a.id = 'die'";
						$relnorepeat = "";
					}
					
				}elseif($params->get('related') == 2) {
					$query = 'SELECT title' .
					' FROM #__content' .
					' WHERE id = '.(int) $id;
					$db->setQuery($query);
					$metakey = trim($db->loadResult());
					$metakey = preg_replace('/[^a-zA-Z0-9 ]/','',$metakey);
					$metakey = preg_replace("/\b[^\s]{1,3}\b/", "", $metakey);
					$metakey = preg_replace('/\s+/', ' ', $metakey);
					$metakey = str_replace(" ", ", ", $metakey);
					
					if ($metakey) {
						// explode the meta keys on a comma
						$keys = explode(',', $metakey);
						$likes = array ();

						// assemble any non-blank word(s)
						foreach ($keys as $key) {
							$key = trim($key);
							if ($key) {
								$likes[] = ' ' . $db->getEscaped($key) . ' '; // surround with commas so first and last items have surrounding commas
							}
							$glue = "%' OR CONCAT(' ', REPLACE(a.title,' ',' '),' ') LIKE '%";
							$relatedcond = "( CONCAT(' ', REPLACE(a.title,' ',' '),' ') LIKE '%" . implode( $glue , $likes) . "%' )";
						}
						$relnorepeat = "a.id <> " . $id;
						
						if (empty($relatedcond) && empty($relnorepeat)) {
							$relatedcond = "";
							$relnorepeat = "";
						}
					}else{
						$relatedcond = "a.id = 'die'";
						$relnorepeat = "";
					}
				}
			}else{				
				$relatedcond = "a.id = 'die'";
				$relnorepeat = "";
			}
		}else{
			$relatedcond = "";
			$relnorepeat = "";
		}

		// Ordering
		
			//Order by Comments
			$oc = "";
			
			//Events Ordering
			$evcon = "";
		
		if ($params->get("dasc")) $dasc = "DESC"; else $dasc = "ASC";
		if ($params->get("sdasc")) $sdasc = "DESC"; else $sdasc = "ASC";
		
		/* Primary */
		if ($params->get('ordering') == 0)
			$ordering = 'a.modified ' . $dasc;
		elseif ($params->get('ordering') == 1)
			$ordering = 'a.created ' . $dasc;
		elseif ($params->get('ordering') == 2)
			$ordering = 'a.hits ' . $dasc;
		elseif ($params->get('ordering') == 3)
			$ordering = 'RAND() ';
		elseif ($params->get('ordering') == 4)
			$ordering = 'a.title ' . $dasc;
		elseif ($params->get('ordering') == 5)
			$ordering = 'r.rating_sum / r.rating_count ' . $dasc;
		elseif ($params->get('ordering') == 6)
			$ordering = 'r.rating_count ' . $dasc;
		elseif ($params->get('ordering') == 7)
			$ordering = 'a.id ' . $dasc;
		elseif ($params->get('ordering') == 8){
			if ($params->get('ctab') != '0') {
				$oc = "(SELECT COUNT(*) FROM " . $ctable . " WHERE " . $cartcol . " = a.id ) AS comen";
				$ordering = 'comen ' . $dasc;
			}else{
				echo '<span class="aidawarning">' . JText::_('COMORDWARNING') . '</span>';
				$ordering = " RAND()";
			}
		}elseif ($params->get('ordering') == 9) {
			$ordering = 'a.publish_down ASC';
			$evcon = "\n AND a.publish_down >= '$now' " ;
		}elseif ($params->get('ordering') == 10)
			$ordering = 'f.ordering ' . $dasc;
			
		/* Secondary */
		if ($params->get('sord') == 20)
			$ordering .= ', a.modified ' . $sdasc;
		elseif ($params->get('sord') == 1)
			$ordering .= ', a.created ' . $sdasc;
		elseif ($params->get('sord') == 2)
			$ordering .= ', a.hits ' . $sdasc;
		elseif ($params->get('sord') == 3)
			$ordering .= ', RAND() ';
		elseif ($params->get('sord') == 4)
			$ordering .= ', a.title ' . $sdasc;
		elseif ($params->get('sord') == 5)
			$ordering .= ', r.rating_sum / r.rating_count ' . $sdasc;
		elseif ($params->get('sord') == 6)
			$ordering .= ', r.rating_count ' . $sdasc;
		elseif ($params->get('sord') == 7)
			$ordering .= ', a.id ' . $sdasc;
		elseif ($params->get('sord') == 8){
			if ($params->get('ctab') != '0') {
				$oc = "(SELECT COUNT(*) FROM " . $ctable . " AS ordcom WHERE ordcom." . $cartcol . " = a.id ) AS comen";
				$ordering .= ", comen " . $sdasc;
			}else{
				echo '<span class="aidawarning">' . JText::_('COMORDWARNING') . '</span>';
				$ordering = " RAND()";
			}
		}elseif ($params->get('sord') == 10)
			$ordering .= ', f.ordering ' . $dasc;
			
		/* ----- SQL QUERIES BEGIN ----- */
		
		if ($params->get('aidaquick')) {
		
			// With this option we do more queries than usual, but they should be lighter as we use less joins
			
			/* STEP 1: group all the settings you'll need to use */
			
			// STICKY ARTICLES CATEGORIES
			
			if ($params->get('aidasticky')) {
			
				$stickers = $params->get('stickers');
				$stickers = str_replace (" ", '', $stickers);
			
				$query = 'SELECT DISTINCT catid' .
						' FROM #__content' . 
						' WHERE id IN (' . $stickers . ')';
					$db->setQuery($query);
					$scats = $db->loadResultArray();
				
				if (is_array($scats)) {
					$scats = implode($scats, ',');
				}
				$scats = ',' . $scats;
				
			}else $scats = '';
			
			// CATEGORIES
			
			if ($catid)	$catCondition = ' AND cc.id IN (' . $catid . $scats . ') ';
			else $catCondition = "";
			
			$query = 'SELECT cc.id, cc.title AS cattle, cc.params AS catparams, cc.alias AS category_alias, ' . 
					' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
					' FROM #__categories AS cc' .
					' WHERE cc.published = 1' .
					$catCondition;
					if($access) $query .= ' AND cc.access <= ' . $aid;
					$query .= ' ORDER BY cc.id';
				$db->setQuery($query);
				$cats = $db->loadObjectList();
			
			if (is_array($cats)) {
				$test = true;
				foreach($cats as $c) {
					if ($test) {
						$catlist = $c->id;
						$test = false;
					}else $catlist .= ',' . $c->id;
				}
			}
					
			/* STEP 2: Queries */
		
			// Featured Offset
			
			$feats = "";
			
			if ($params->get('foffset')) {
			
				$foffset = $params->get('foffset');
				$query = $db->getQuery(true);
				$query->select('a.id');
				$query->from('#__content AS a')
				->leftJoin('#__content_rating AS r ON r.content_id = a.id')
				->innerJoin('#__content_frontpage AS f ON f.content_id = a.id');
				$query->where('( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )')
				->where('( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )');
				if($wauth) $query->where($wauth);
				if($access && $params->get('sua')) $query->where('a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid);
				if($recent) $query->where('DATEDIFF('.$db->Quote($now).', a.created) < ' . $recent);
				if($recentwo) $query->where('DATEDIFF('.$db->Quote($now).', a.created) >= ' . $recentwo);
				if($relnorepeat) $query->where($relnorepeat);
				if($relatedcond) $query->where($relatedcond);
				if($params->get('show_trash') == 1) $query->where('a.state = 1');
				if($params->get('show_trash') == 2) $query->where('a.state <> 1');
				if($params->get('cco') == 2 && !$temp) $query->where("a.id = 'die'");
				$query->order($ordering);
				$db->setQuery($query, $offset, $foffset);
				$feats = $db->loadResultArray();
				$feats = implode(',', $feats);
				
			}
			
			// Sticky Articles
			
			$scount = 0;
			
			if ($params->get('aidasticky')) {
			
				$stickers = $params->get('stickers');
				$stickers = str_replace (" ", '', $stickers);
				$sticka = explode(",", $stickers);
				
				if ($sticka[0]) {
					
					$query = $db->getQuery(true);
					$query->select('a.*')
					->select('r.rating_count')
					->select('r.rating_sum');
					$query->from('#__content AS a')
					->leftJoin('#__content_rating AS r ON r.content_id = a.id');
					$query->where('a.id IN (' . $stickers . ')');
					if($access && $params->get('sua')) $query->where('a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid);
					// Ordering
					$ord = 'case a.id';
					for ($w = 0; $w < count($sticka); $w++)
						$ord .= " when '" . $sticka[$w] . "' then '" . ($w+1) . "'";
					$ord .= ' end';
					$query->order($ord);
					
					$db->setQuery($query);
					$stickies = $db->loadObjectList();
					
					$scount = count($stickies);
					$count -= $scount;
				
				}
			
			}

			if ($count) {
			
				// Normal Query
				
				$query = $db->getQuery(true);
				$query->select('a.*')
				->select('r.rating_count')
				->select('r.rating_sum');
				if ($oc) $query->select($oc);
				$query->from('#__content AS a')
				->leftJoin('#__content_rating AS r ON r.content_id = a.id');
				$query->where('( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )')
				->where('( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )');
				if($wauth) $query->where($wauth);
				if($access && $params->get('sua')) $query->where('a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid);
				if($recent) $query->where('DATEDIFF('.$db->Quote($now).', a.created) < ' . $recent);
				if($recentwo) $query->where('DATEDIFF('.$db->Quote($now).', a.created) >= ' . $recentwo);
				if($relnorepeat) $query->where($relnorepeat);
				if($relatedcond) $query->where($relatedcond);
				if($params->get('show_trash') == 1) $query->where('a.state = 1');
				if($params->get('show_trash') == 2) $query->where('a.state <> 1');
				if($params->get('cco') == 2 && !$temp) $query->where("a.id = 'die'");
				if($params->get('show_front') == 1) $query->where('f.content_id IS NULL ');
				if($params->get('foffset') && $feats) $query->where('a.id NOT IN (' . $feats . ')');
				if($params->get('aidasticky') && $stickers) $query->where('a.id NOT IN (' . $stickers . ')');
				$query->order($ordering);
				$db->setQuery($query, $offset, $count);
				$rows = $db->loadObjectList();
						
				/*if ($params->get('cco') == 2 && !$temp) $query .= " AND a.id = 'die'";;*/
				
			}else{ $rows = $stickies; }
			
			if ($scount && $count) {
				$rows = array_merge($stickies, $rows);
			}
			
			//Now we need to merge cats and rows!
			
			foreach ($rows as $row) {
				foreach ($cats as $cat) {
					if ($row->catid == $cat->id) {
						$row->catparams = $cat->catparams;
						$row->cattle = $cat->cattle;
						$row->category_alias = $cat->category_alias;
					}
				}
			}
	
		}else{
			
			/* STEP 1: group all the settings you'll need to use */
			
			// CATEGORIES
			
				if ($catid)	{
					$ids = explode( ',', $catid );
					JArrayHelper::toInteger( $ids );
					$catCondition = '(cc.id = ' . implode( ' OR cc.id = ', $ids ) . ')';
				}else $catCondition = "";
		
			/* STEP 2: Queries */
		
			// Featured Offset
			
			$feats = '';
			
			if ($params->get('foffset')) {
				$foffset = $params->get('foffset');
				$query = $db->getQuery(true);
				$query->select('a.*')
				->select('r.rating_count')
				->select('r.rating_sum')
				->select('cc.params AS catparams')
				->select('cc.title AS cattle')
				->select('cc.alias AS category_alias');
				if ($oc) $query->select($oc);
				$query->from('#__content AS a')
				->innerJoin('#__categories AS cc ON cc.id = a.catid')
				->leftJoin('#__content_rating AS r ON r.content_id = a.id');
				$query->innerJoin('#__content_frontpage AS f ON f.content_id = a.id');
				$query->where('cc.published = 1')
				->where('( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )')
				->where('( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )');
				if($wauth) $query->where($wauth);
				if($catid) $query->where($catCondition);
				if($params->get('show_front') == 1) $query->where('f.content_id IS NULL ');
				if($access && $params->get('sua')) $query->where('a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid);
				if($recent) $query->where('DATEDIFF('.$db->Quote($now).', a.created) < ' . $recent);
				if($recentwo) $query->where('DATEDIFF('.$db->Quote($now).', a.created) >= ' . $recentwo);
				if($relnorepeat) $query->where($relnorepeat);
				if($relatedcond) $query->where($relatedcond);
				if($params->get('show_trash') == 1) $query->where('a.state = 1');
				if($params->get('show_trash') == 2) $query->where('a.state <> 1');
				if($params->get('cco') == 2 && !$temp) $query->where("a.id = 'die'");
				$query->order($ordering);
				$db->setQuery($query, $offset, $foffset);
				$feats = $db->loadResultArray();
				$feats = implode(',', $feats);
			}
		
			// Sticky Articles
			
			$scount = 0;
		
			if ($params->get('aidasticky')) {
			
				$stickers = $params->get('stickers');
				$stickers = str_replace (" ", '', $stickers);
				$sticka = explode(",", $stickers);
				
				if ($sticka[0]) {
		
					$query = $db->getQuery(true);
					$query->select('a.*')
					->select('r.rating_count')
					->select('r.rating_sum')
					->select('cc.params AS catparams')
					->select('cc.title AS cattle')
					->select('cc.alias AS category_alias');
					$query->from('#__content AS a')
					->innerJoin('#__categories AS cc ON cc.id = a.catid')
					->leftJoin('#__content_rating AS r ON r.content_id = a.id');
					$query->where('a.id IN (' . $stickers . ')');
					if($access && $params->get('sua')) $query->where('a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid);
					// Ordering
					$ord = 'case a.id';
					for ($w = 0; $w < count($sticka); $w++)
						$ord .= " when '" . $sticka[$w] . "' then '" . ($w+1) . "'";
					$ord .= ' end';
					$query->order($ord);
					
					$db->setQuery($query);
					$stickies = $db->loadObjectList();
					
					$scount = count($stickies);
					$count -= $scount;
				
				}
				
			}
		
			if ($count) {
			
				// Normal Query
		
				$query = $db->getQuery(true);
				$query->select('a.*')
				->select('r.rating_count')
				->select('r.rating_sum')
				->select('cc.params AS catparams')
				->select('cc.title AS cattle')
				->select('cc.alias AS category_alias');
				if ($oc) $query->select($oc);
				$query->from('#__content AS a')
				->innerJoin('#__categories AS cc ON cc.id = a.catid')
				->leftJoin('#__content_rating AS r ON r.content_id = a.id');
				if($params->get('show_front')) {
					if($params->get('show_front') == 1) $query->leftJoin('#__content_frontpage AS f ON f.content_id = a.id');
					if($params->get('show_front') == 2) $query->innerJoin('#__content_frontpage AS f ON f.content_id = a.id');
				}elseif($params->get('ordering') == 10 || $params->get('sord') == 10){
					$query->leftJoin('#__content_frontpage AS f ON f.content_id = a.id');
				}
				$query->where('cc.published = 1')
				->where('( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )')
				->where('( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )');
				if($wauth) $query->where($wauth);
				if($catid) $query->where($catCondition);
				if($params->get('show_front') == 1) $query->where('f.content_id IS NULL ');
				if($access && $params->get('sua')) $query->where('a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid);
				if($recent) $query->where('DATEDIFF('.$db->Quote($now).', a.created) < ' . $recent);
				if($recentwo) $query->where('DATEDIFF('.$db->Quote($now).', a.created) >= ' . $recentwo);
				if($relnorepeat) $query->where($relnorepeat);
				if($relatedcond) $query->where($relatedcond);
				if($params->get('foffset') && $feats) $query->where('a.id NOT IN (' . $feats . ')');
				if($params->get('aidasticky') && $stickers) $query->where('a.id NOT IN (' . $stickers . ')');
				if($params->get('show_trash') == 1) $query->where('a.state = 1');
				if($params->get('show_trash') == 2) $query->where('a.state <> 1');
				if($params->get('cco') == 2 && !$temp) $query->where("a.id = 'die'");
				$query->order($ordering);
				$db->setQuery($query, $offset, $count);
				$rows = $db->loadObjectList();
			
			}else{ $rows = $stickies; }
			
			if ($scount && $count) {
				$rows = array_merge($stickies, $rows);
			}
			
		}

		$i		= 0;
		$lists	= array();
		
		
		
		
		
		
		
		
		/* GET IMAGE PREFERENCE LIST FOR IMAGE 1 */
		
		if ($chimg1) {
		
			$pref1 = array();
			
			$pref1[0] = $params->get("img1pref1");
			$pref1[1] = $params->get("img1pref2");
			$pref1[2] = $params->get("img1pref3");
			$pref1[3] = $params->get("img1pref4");
			$pref1[4] = $params->get("img1pref5");
			
			modAiDaNews2Helper::imgpreflist_arrange($pref1);
			
		}
			
		/* GET IMAGE PREFERENCE LIST FOR IMAGE 2 */
		
		if ($chimg2) {
		
			$pref2 = array();
			
			$pref2[0] = $params->get("img2pref1");
			$pref2[1] = $params->get("img2pref2");
			$pref2[2] = $params->get("img2pref3");
			$pref2[3] = $params->get("img2pref4");
			$pref2[4] = $params->get("img2pref5");
			
			modAiDaNews2Helper::imgpreflist_arrange($pref2);
		
		}
		
		/* GET IMAGE PREFERENCE LIST FOR IMAGE 3 */
		
		if ($chimg3) {
		
			$pref3 = array();
			
			$pref3[0] = $params->get("img3pref1");
			$pref3[1] = $params->get("img3pref2");
			$pref3[2] = $params->get("img3pref3");
			$pref3[3] = $params->get("img3pref4");
			$pref3[4] = $params->get("img3pref5");
			
			modAiDaNews2Helper::imgpreflist_arrange($pref3);
			
		}

		/* START WITH ITEMS */
		
		foreach ( $rows as $row ) {
		
		// Prepare some variables for each article...
			
			$row->slug = $row->id.':'.$row->alias;
			$row->catslug = $row->catid.':'.$row->category_alias;
		
		/* LINKS CREATION */
			
			/* ARTICLE LINK */
			
			$artlink = ContentHelperRoute::getArticleRoute($row->slug, $row->catslug);
			if($row->access <= $aid || !$params->get('toglog')) {
				if ($params->get('omid'))
					if (preg_match("'&Itemid=([^<]*)'si", $artlink))
						$artlink = JRoute::_(preg_replace("'&Itemid=([^<]*)'si", '&Itemid=' . $params->get('cmid'), $artlink));
					else
						$artlink = JRoute::_($artlink . '&Itemid=' . $params->get('cmid'));
				else
					$artlink = JRoute::_($artlink);
			} else {
				$artlink = JRoute::_('index.php?option=com_users&view=login');
			}

			
			
			
			
			
			
			
			/* CATEGORY LINK */
			
				$catlink = JRoute::_(ContentHelperRoute::getCategoryRoute($row->catid));
			
			
			
			
			
			/* COMMUNITY BUILDER LINK */
			
				$cblink = JRoute::_('index.php?option=com_comprofiler&task=userProfile&user=' . $row->created_by);
			
			/* JOMSOCIAL LINK */
			
				$jslink = JRoute::_('index.php?option=com_community&view=profile&userid=' . $row->created_by);
			
			/* KUNENA LINK */
			
				$kunlink = 'index.php?option=com_kunena&func=profile&userid=' . $row->created_by;
			
			/* JSOCIALSUITE LINK */
			
				$jsslink = JRoute::_('index.php?option=com_jsocialsuite&amp;task=profile.view&amp;id=' . $row->created_by);
			
		/* ELEMENTS */
		
			/* IMAGE 1 */
			
		if ($chimg1) {
				
				$img1url = "";
				$img1url = modAiDaNews2Helper::imgpreflist_findlink($pref1, $row->introtext . ' ' . $row->fulltext, $row->catparams, $row->images, $row->created_by, 1);
			if($img1url) {
				
				if ($params->get('usethumbs')) {
					$img1url = modAiDaNews2Helper::creaThumb($img1url, $params, 1, $row->id, $row->alias);
					list($w, $h) = getimagesize($img1url);
					$img1url = '<img src="' . $img1url . '" width="' . $w . '" height="' . $h . '" alt="' . $row->alias . '"/>';
				}else{
					$h = $params->get('img1H');
					$w = $params->get('img1W');
					$img1url = substr($img1url, 0, strrpos($img1url, '-'));
					$img1url = '<img src="' . $img1url . '"';
					if ($w && $w != "auto") $img1url .= ' width="' . $w . '"';
					if ($h && $h != "auto") $img1url .= ' height="' . $h . '"';
					$img1url .= ' alt="' . $row->alias . '"/>';
				}
				
				// CSS Field 1
				
				if ($cssfields && $params->get('cssfield1')) $style = ' style = "' . $params->get('cssfield1') . '"';
				else $style = "";
				
				//Insert Links
				
				if ($params->get('img1lnk') == 1) {
					$img1url = '<a class="aidanews2_img1"' . $style . ' href="' . $artlink . '">' . $img1url . '</a>';
				}elseif ($params->get('img1lnk') == 2) {
					$img1url = '<a class="aidanews2_img1"' . $style . ' href="' . $catlink . '">' . $img1url . '</a>';
				}elseif ($params->get('img1lnk') == 4) {
					$img1url = '<a class="aidanews2_img1"' . $style . ' href="' . $cblink . '">' . $img1url . '</a>';
				}elseif ($params->get('img1lnk') == 5) {
					$img1url = '<a class="aidanews2_img1"' . $style . ' href="' . $jslink . '">' . $img1url . '</a>';
				}elseif ($params->get('img1lnk') == 6) {
					$img1url = '<a class="aidanews2_img1"' . $style . ' href="' . $kunlink . '">' . $img1url . '</a>';
				}elseif ($params->get('img1lnk') == 7) {
					$img1url = '<a class="aidanews2_img1"' . $style . ' href="' . $jsslink . '">' . $img1url . '</a>';
				}
				
				$lists[$i]->img1 = $img1url;
				
			}else $lists[$i]->img1 = '';
			
		}else $lists[$i]->img1 = '';
			


			/* IMAGE 2 */
			
		if ($chimg2) {
		
				$img2url = "";
				$img2url = modAiDaNews2Helper::imgpreflist_findlink($pref2, $row->introtext . ' ' . $row->fulltext, $row->catparams, $row->images, $row->created_by, 2);
				
				//Thumbs?
				
			if($img2url) {
			
				if ($params->get('usethumbs')) {
					$img2url = modAiDaNews2Helper::creaThumb($img2url, $params, 2, $row->id, $row->alias);
					list($w, $h) = getimagesize($img2url);
					$img2url = '<img src="' . $img2url . '" width="' . $w . '" height="' . $h . '" alt="' . $row->alias . '"/>';
				}else{
					$h = $params->get('img2H');
					$w = $params->get('img2W');
					$img2url = substr($img1url, 0, strrpos($img1url, '-'));
					$img2url = '<img src="' . $img2url . '"';
					if ($w && $w != "auto") $img2url .= ' width="' . $w . '"';
					if ($h && $h != "auto") $img2url .= ' height="' . $h . '"';
					$img2url .= ' alt="' . $row->alias . '"/>';
				}
				
				// CSS Field 2
				
				if ($cssfields && $params->get('cssfield2')) $style = ' style = "' . $params->get('cssfield2') . '"';
				else $style = "";
				
				//Insert Links
				
				if ($params->get('img2lnk') == 1) {
					$img2url = '<a class="aidanews2_img2"' . $style . ' href="' . $artlink . '">' . $img2url . '</a>';
				}elseif ($params->get('img2lnk') == 2) {
					$img2url = '<a class="aidanews2_img2"' . $style . ' href="' . $catlink . '">' . $img2url . '</a>';
				}elseif ($params->get('img2lnk') == 4) {
					$img2url = '<a class="aidanews2_img2"' . $style . ' href="' . $cblink . '">' . $img2url . '</a>';
				}elseif ($params->get('img2lnk') == 5) {
					$img2url = '<a class="aidanews2_img2"' . $style . ' href="' . $jslink . '">' . $img2url . '</a>';
				}elseif ($params->get('img2lnk') == 6) {
					$img2url = '<a class="aidanews2_img2"' . $style . ' href="' . $kunlink . '">' . $img2url . '</a>';
				}elseif ($params->get('img2lnk') == 7) {
					$img2url = '<a class="aidanews2_img2"' . $style . ' href="' . $jsslink . '">' . $img2url . '</a>';
				}
				
				$lists[$i]->img2 = $img2url;
				
			}else $lists[$i]->img2 = '';
			
		}else $lists[$i]->img2 = '';
			


			/* IMAGE 3 */
			
		if ($chimg3) {
			
				$img3url = "";
				$img3url = modAiDaNews2Helper::imgpreflist_findlink($pref3, $row->introtext . ' ' . $row->fulltext, $row->catparams, $row->images, $row->created_by, 3);
				
				//Thumbs?
			
			if($img3url) {
			
				if ($params->get('usethumbs')) {
					$img3url = modAiDaNews2Helper::creaThumb($img3url, $params, 3, $row->id, $row->alias);
					list($w, $h) = getimagesize($img3url);
					$img3url = '<img src="' . $img3url . '" width="' . $w . '" height="' . $h . '" alt="' . $row->alias . '"/>';
				}else{
					$h = $params->get('img3H');
					$w = $params->get('img3W');
					$img3url = substr($img1url, 0, strrpos($img1url, '-'));
					$img3url = '<img src="' . $img3url . '"';
					if ($w && $w != "auto") $img3url .= ' width="' . $w . '"';
					if ($h && $h != "auto") $img3url .= ' height="' . $h . '"';
					$img3url .= ' alt="' . $row->alias . '"/>';
				}
				
				// CSS Field 3
				
				if ($cssfields && $params->get('cssfield3')) $style = ' style = "' . $params->get('cssfield3') . '"';
				else $style = "";
				
				//Insert Links
				
				if ($params->get('img3lnk') == 1) {
					$img3url = '<a class="aidanews2_img3"' . $style . ' href="' . $artlink . '">' . $img3url . '</a>';
				}elseif ($params->get('img3lnk') == 2) {
					$img3url = '<a class="aidanews2_img3"' . $style . ' href="' . $catlink . '">' . $img3url . '</a>';
				}elseif ($params->get('img3lnk') == 4) {
					$img3url = '<a class="aidanews2_img3"' . $style . ' href="' . $cblink . '">' . $img3url . '</a>';
				}elseif ($params->get('img3lnk') == 5) {
					$img3url = '<a class="aidanews2_img3"' . $style . ' href="' . $jslink . '">' . $img3url . '</a>';
				}elseif ($params->get('img3lnk') == 6) {
					$img3url = '<a class="aidanews2_img3"' . $style . ' href="' . $kunlink . '">' . $img3url . '</a>';
				}elseif ($params->get('img3lnk') == 7) {
					$img3url = '<a class="aidanews2_img3"' . $style . ' href="' . $jsslink . '">' . $img3url . '</a>';
				}
				
				$lists[$i]->img3 = $img3url;
				
			}else $lists[$i]->img3 = '';
			
		}else $lists[$i]->img3 = '';
			


			/* TITLE */
			
				/* GET TITLE */
			
				$tit = str_replace ('$', '\$', htmlspecialchars( $row->title ));
			
				/* LINK */
			
				$titlinkb = ""; $titlinke = "";
				if ($params->get('titnp')) { $titblank = ' target="_blank"'; }else{ $titblank = ""; }
				if ($params->get('lnktit')) { $titlinkb = '<a href="' . $artlink . '"' . $titblank . '>'; $titlinke = '</a>'; }
				
				// CSS Field 4
				
				if ($cssfields && $params->get('cssfield4')) $style = ' style = "' . $params->get('cssfield4') . '"';
				else $style = "";
				
				/* H1 H2 H3 Span */
				
				$hspanb = ""; $hspane = "";
				if ($params->get('hspan') == 0) {
					$hspanb = '<span class="aidanews2_title"' . $style . '>'; $hspane = '</span>';
				}elseif ($params->get('hspan') == 1) {
					$hspanb = '<h1 class="aidanews2_title"' . $style . '>'; $hspane = '</h1>';
				}elseif ($params->get('hspan') == 2) {
					$hspanb = '<h2 class="aidanews2_title"' . $style . '>'; $hspane = '</h2>';
				}elseif ($params->get('hspan') == 3) {
					$hspanb = '<h3 class="aidanews2_title"' . $style . '>'; $hspane = '</h3>';
				}
				
				/* SHORTEN TITLE */
				
				if ($params->get('titnum') && strlen($tit) > $params->get('titnum')) $tit = modAiDaNews2Helper::shorten($tit, $params->get('titnum'), $params->get('titsh'), $params->get("titend"));
			
			$lists[$i]->title = $hspanb . $titlinkb .  $tit . $titlinke . $hspane;
			
			/* TEXT */
			
				/* CHOOSE TEXT */
				
				if ($params->get('txtwhat') == 0) {
					$txt = str_replace ('$', '\$', $row->introtext);
				}elseif ($params->get('txtwhat') == 1) {
					$txt = str_replace ('$', '\$', $row->fulltext);
				}elseif ($params->get('txtwhat') == 2) {
					$txt = str_replace ('$', '\$', $row->introtext . $row->fulltext);
				}elseif ($params->get('txtwhat') == 3) {
					$txt = str_replace ('$', '\$', $row->metadesc);
				}
				
				/* STRIP TAGS */
				
				if ($params->get('txtstrip')) { $txtallow = $params->get('txtallow'); $txt = strip_tags(str_replace ("<br/>"," ",$txt), $txtallow); }
				
				/* STRIP PLUGINS */
				
				if ($params->get('txtplugs')) {
					$txt = preg_replace("'{.*?}([^<]*){/.*?}'si", '', $txt);
					$txt = preg_replace('#\{.*?\}#', '', $txt);
				}
				
				/* SHORTEN TEXT */
				
				if ($params->get('txtnum') && strlen($txt) > $params->get('txtnum')) $txt = modAiDaNews2Helper::shorten($txt, $params->get('txtnum'), $params->get('txtsh'), $params->get("txtend"));
			
				// CSS Field 5
				
				if ($cssfields && $params->get('cssfield5')) $style = ' style = "' . $params->get('cssfield5') . '"';
				else $style = "";
			
			$lists[$i]->text = '<span class="aidanews2_text"' . $style . '>' . $txt . '</span>';
			
			/* READ MORE */
			
			// CSS Field 6
				
				if ($cssfields && $params->get('cssfield6')) $style = ' style = "' . $params->get('cssfield6') . '"';
				else $style = "";
			
			if ($params->get('langload')) {
				$lists[$i]->rm = '<a href="' . $artlink . '" class="readon"' . $style . '><span class="aidanews2_readmore">' . JText::_('AIDAREADMORE') . '</span></a>';
			}else{
				$lists[$i]->rm = '<a href="' . $artlink . '" class="readon"' . $style . '><span class="aidanews2_readmore">' . $params->get('readmore') . '</span></a>';
			}
			
			/* HITS */
			
			// CSS Field 7
				
				if ($cssfields && $params->get('cssfield7')) $style = ' style = "' . $params->get('cssfield7') . '"';
				else $style = "";
			
			$lists[$i]->hits = '<span class="aidanews2_hits"' . $style . '>' . $row->hits . '</span>';
			
			/* RATING */
			
			if ($row->rating_count == 0) $row->rating_count = 1;
			
			// CSS Field 8
				
				if ($cssfields && $params->get('cssfield8')) $style = ' style = "' . $params->get('cssfield8') . '"';
				else $style = "";
			
			if ($params->get('rstars')) {
				$rate = round($row->rating_sum / $row->rating_count, 0);
				$lists[$i]->rating = '<div class="aidanews2_stars_rating"' . $style . '>';
				for ($rr = 0; $rr < 5; $rr++) {
					if ($rr < $rate) $lists[$i]->rating .= '<img src="modules/mod_aidanews2/img/default/rating.png" alt="' . $rate . '" title="' . $rate . '" width="16" height="16"/>';
					else $lists[$i]->rating .= '<img src="modules/mod_aidanews2/img/default/no-rating.png" alt="' . $rate . '" title="' . $rate . '" width="16" height="16"/>';
				}
				$lists[$i]->rating .= '</div>';
			}else{
				$lists[$i]->rating = '<span class="aidanews2_rating"' . $style . '>' . round($row->rating_sum / $row->rating_count, $params->get('rround')) . '</span>';
			}
			
			/* CATEGORY */
			
			// CSS Field 9
			
			if ($cssfields && $params->get('cssfield9')) $style = ' style = "' . $params->get('cssfield9') . '"';
				else $style = "";
			
			if ($params->get('caturl'))
				$lists[$i]->category = '<span class="aidanews2_category aidacat_' . $row->catid . '"' . $style . '><a href="' . $catlink . '">' . $row->cattle . '</a></span>';
			else
				$lists[$i]->category = '<span class="aidanews2_category aidacat_' . $row->catid . '"' . $style . '>' . $row->cattle . '</span>';
			
			/* CATEGORY SUFFIX (CSS) */
			
			$lists[$i]->catcss = 'aidacat_' . $row->catid;
			
			/* STICKY SUFFIX (CSS) */
			
			if ($i < $scount) $lists[$i]->sticky = 'aidasticky '; else $lists[$i]->sticky = '';
			
			/* CATEGORIES */
			
			// CSS Field 10
			
			if ($cssfields && $params->get('cssfield10')) $style = ' style = "' . $params->get('cssfield10') . '"';
				else $style = "";
			
			
			if ($chcats) {
				$catp = $row->catid;
				$divi = '<span class="aidacats_div">' . $params->get('catsdiv') . '</span>';
				$categories = "";
				while ($catp != 1) {
					$query = 'SELECT id, parent_id, title FROM #__categories WHERE id = ' . $catp . ' AND extension = "com_content"';
						$db->setQuery($query);
						$ccc = $db->loadObject();
					if ($params->get('catsurl'))
						$categories = '<span class="aidanews2_category aidacat_' . $catp . '"><a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($ccc->id)) . '">' . $ccc->title . '</a></span> ' . (($categories) ? $divi : '') . ' ' . $categories;
					else
						$categories = '<span class="aidanews2_category aidacat_' . $catp . '">' . $ccc->title . '</span> ' . (($categories) ? $divi : '') . ' ' . $categories;
					$catp = $ccc->parent_id;
				}
				$lists[$i]->categories = '<span class="aidanews2_categories"' . $style . '>' . $categories . '</span>';
			}else $lists[$i]->categories = '';
			
			/* DATE */
			
			$dto = $params->get('dto');
			$dst = false; $dnd = 0;
			if (strpos($dto, "[st]") !== false) $dst = true;
			
			if ($params->get('wdate') == 0) {
				$date = JHTML::_('date', $row->created, $dto);
				if ($dst) $dnd = JHTML::_('date', $row->created, "%d");
				$today = JHTML::_('date', $row->created, "%d-%b-%Y");
			} elseif ($params->get('wdate') == 1) {
				$date = JHTML::_('date', $row->modified, $dto);
				if ($dst) $dnd = JHTML::_('date', $row->modified, "%d");
				$today = JHTML::_('date', $row->modified, "%d-%b-%Y");
			} elseif ($params->get('wdate') == 2) {
				$date = JHTML::_('date', $row->publish_up, $dto);
				if ($dst) $dnd = JHTML::_('date', $row->publish_up, "%d");
				$today = JHTML::_('date', $row->publish_up, "%d-%b-%Y");
			} elseif ($params->get('wdate') == 3) {
				$date = JHTML::_('date', $row->publish_down, $dto);
				if ($dst) $dnd = JHTML::_('date', $row->publish_down, "%d");
				$today = JHTML::_('date', $row->publish_down, "%d-%b-%Y");
			}
			
			// Today CSS Class
				$todayc = JHTML::_('date', $now, "%d-%b-%Y");
				if ($todayc == $today) $lists[$i]->today = 'aidatoday ';
				else $lists[$i]->today = '';
			
			if ($dnd) {
				if ($dnd == 1) $date = str_replace('[st]', "st", $date);
				elseif ($dnd == 2) $date = str_replace('[st]', "nd", $date);
				elseif ($dnd == 3) $date = str_replace('[st]', "rd", $date);
				elseif ($dnd == 21) $date = str_replace('[st]', "st", $date);
				elseif ($dnd == 22) $date = str_replace('[st]', "nd", $date);
				elseif ($dnd == 23) $date = str_replace('[st]', "rd", $date);
				elseif ($dnd == 31) $date = str_replace('[st]', "st", $date);
				else $date = str_replace('[st]', "th", $date);
			}
			
			// CSS Field 11
			
			if ($cssfields && $params->get('cssfield11')) $style = ' style = "' . $params->get('cssfield11') . '"';
				else $style = "";
			
			$lists[$i]->date = '<span class="aidanews2_date"' . $style . '>' . $date . '</span>';
			
			/* AUTHOR */
			
				/* GET NAME OR USERNAME */
				
				$auth = "";
				$alias = "";
				
				if ($params->get('authtype') == 0) {
					$query = 'SELECT name FROM #__users WHERE id = ' . $row->created_by;
						$db->setQuery($query);
						$auth = $db->loadResult();
				}elseif ($params->get('authtype') == 1) {
					$query = 'SELECT username FROM #__users WHERE id = ' . $row->created_by;
						$db->setQuery($query);
						$auth = $db->loadResult();
				}elseif ($params->get('authtype') == 2) {
					if ($row->created_by_alias) {
						$alias = $row->created_by_alias;
					}else{
						$query = 'SELECT name FROM #__users WHERE id = ' . $row->created_by;
							$db->setQuery($query);
							$auth = $db->loadResult();
					}
				}elseif ($params->get('authtype') == 3) {
					if ($row->created_by_alias) {
						$alias = $row->created_by_alias;
					}else{
						$query = 'SELECT username FROM #__users WHERE id = ' . $row->created_by;
							$db->setQuery($query);
							$auth = $db->loadResult();
					}
				}
				
				/* LINK AUTHOR */
				
				if ($alias) {
					$aut = $alias;
				}else{
					if ($params->get('authlnk') == 1)
						$aut = '<a href="' . $cblink . '">' . $auth . '</a>';
					elseif ($params->get('authlnk') == 2)
						$aut = '<a href="' . $jslink . '">' . $auth . '</a>';
					elseif ($params->get('authlnk') == 3)
						$aut = '<a href="' . $jsslink . '">' . $auth . '</a>';
					else
						$aut = $auth;
				}
				
				// CSS Field 12
			
				if ($cssfields && $params->get('cssfield12')) $style = ' style = "' . $params->get('cssfield12') . '"';
				else $style = "";
			
			$lists[$i]->author = '<span class="aidanews2_author"' . $style . '>' . $aut . '</span>';
			
			/* COMMENTS */
			
			if ($params->get('ctab') != '0') {
				
				if ($params->get('ctab') == '11') {
					//Udjacomments support
					$query = 'SELECT COUNT(*) FROM ' . $ctable . ' WHERE ' . $cartcol . ' = "com_content:' . $row->id . '"';
					$db->setQuery($query);
					$comments = $db->loadResult();
				}else{
					$query = 'SELECT COUNT(*) FROM ' . $ctable . ' WHERE ' . $cartcol . ' = ' . $row->id ;
					$db->setQuery($query);
					$comments = $db->loadResult();
				}
				
				if ($params->get('ctab') == '10' && $comments) {
					$comments -= 1;
				}
				
				// CSS Field 13
			
				if ($cssfields && $params->get('cssfield13')) $style = ' style = "' . $params->get('cssfield13') . '"';
				else $style = "";
			
				$lists[$i]->comments = '<span class="aidanews2_comments"' . $style . '>' . $comments . '</span>';
				
			} else $lists[$i]->comments = '';
			
			/* TOOLTIPS */
			
			if (($params->get('tol_title')) || ($params->get('tol_img1')) || ($params->get('tol_img2')) || ($params->get('tol_img3'))) {
			
				/* TAGS THAT CAN BE INSERTED IN THE TOOLTIPS */
				
					/* Refresh patterns for every item */
					$patterns = array ('/\[title\]/', '/\[text\]/', '/\[empty\]/', '/\[author\]/', '/\[date\]/', '/\[category\]/');
					$replace = array ($row->title, $txt, '', $aut, $date, $row->cattle);
					
				/* EXCHANGE TAGS AND ADD TOOLTIPS TO ELEMENTS */
				
					$toltit = $params->get('tol_title');
				
					if ($toltit && $toltit != '[empty]') {
						$lists[$i]->title = '<span class="hasTip" title="' . preg_replace($patterns, $replace, $toltit) . '">' . $lists[$i]->title . '</span>';
					}
					
					$tolimg1 = $params->get('tol_img1');
					
					if ($tolimg1 && $tolimg1 != '[empty]') {
						$lists[$i]->img1 = '<span class="hasTip" title="' . preg_replace($patterns, $replace, $tolimg1) . '">' . $lists[$i]->img1 . '</span>';
					}
					
					$tolimg2 = $params->get('tol_img2');
					
					if ($tolimg2 && $tolimg2 != '[empty]') {
						$lists[$i]->img2 = '<span class="hasTip" title="' . preg_replace($patterns, $replace, $tolimg2) . '">' . $lists[$i]->img2 . '</span>';
					}
					
					$tolimg3 = $params->get('tol_img3');
					
					if ($tolimg3 && $tolimg3 != '[empty]') {
						$lists[$i]->img3 = '<span class="hasTip" title="' . preg_replace($patterns, $replace, $tolimg3) . '">' . $lists[$i]->img3 . '</span>';
					}
			}
				
			/* ...NEXT! */
			
			$i++;
		}

		return $lists;
	}
	
}