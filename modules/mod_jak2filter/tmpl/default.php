<?php
/**
 * ------------------------------------------------------------------------
 * JA K2 Filter Module for J25 & J30
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// No direct access.
defined('_JEXEC') or die;

$formid = 'jak2filter-form-'.$module->id;
?>
<form id="<?php echo $formid; ?>" method="POST" action="<?php echo JRoute::_('index.php'); ?>">
<input type="hidden" name="option" value="com_jak2filter" />
<input type="hidden" name="view" value="itemlist" />
<input type="hidden" name="task" value="search" />
<input type="hidden" name="Itemid" value="<?php echo $params->get('set_itemid',0)?$params->get('set_itemid',0):JRequest::getInt('Itemid'); ?>" />
<ul id="jak2filter<?php echo $module->id; ?>" class="ja-k2filter <?php echo $ja_stylesheet;?>">
<?php 
$j = 0;
$clear = '';
$style = '';
if($filter_by_keyword):
if($params->get('ja_column') >0 && (($j) % $params->get('ja_column')) == 0){
	$clear = " clear:both;";
}
if($ja_column || $clear){
	$style ='style="'.$ja_column.$clear.'"';
}
$j++;

?>
	<li <?php echo $style?>>
		<label class="group-label"><?php echo JText::_('JAK2_KEYWORD'); ?></label>
		<input type="text" name="jakeyword" id="jakeyword" class="inputbox" value="<?php echo JRequest::getVar('jakeyword','');?>" />
	</li>
<?php 
$clear = '';
endif; ?>
<?php if($filter_by_category): 
$style = '';
if($params->get('ja_column') >0 && (($j) % $params->get('ja_column')) == 0){
	$clear = " clear:both;";
}
if($ja_column || $clear){
	$style ='style="'.$ja_column.$clear.'"';
}
$j++;
?>
	<li <?php echo $style;?>>
		<label class="group-label"><?php echo JText::_('JAK2_CATEGORY'); ?></label>
		<?php echo $categories; ?>
	</li>
<?php 
$clear = '';
endif; ?>
<?php if($filter_by_author): 
$style = '';
if($params->get('ja_column') >0 && (($j) % $params->get('ja_column')) == 0){
	$clear = " clear:both;";
}
if($ja_column || $clear){
	$style ='style="'.$ja_column.$clear.'"';
}
$j++;

?>
	<li <?php echo $style;?>>
		<label class="group-label"><?php echo JText::_('JAK2_AUTHOR'); ?></label>
		<?php echo $authors; ?>
	</li>
<?php 
$clear = '';
endif; ?>

<?php if($list): ?>
<?php 
$i = 0;
foreach ($list as $key => $exfield):
$magicSelect = '';
$fieldTypes = explode("_",$key);
if($fieldTypes[0] == 'magicSelect'){
	$magicSelect = ' class="magic-select"';
}
$last = ($i == count($list)-1)?'class="li-last"':'';
$i++;
$style = '';
if($params->get('ja_column') >0 && (($j) % $params->get('ja_column')) == 0){
	$clear = " clear:both;";
}
if($ja_column || $clear){
	$style ='style="'.$ja_column.$clear.'"';
}
$j++;

?>
	<li <?php echo $style;?> <?php echo $magicSelect;?>>
		<?php echo $exfield; ?>
	</li>
<?php 
$clear = '';
endforeach; ?>
<?php endif; ?>
<?php
$style='';
if($params->get('ja_column') >0 && (($j) % $params->get('ja_column')) == 0){
	$clear = " clear:both;";
}
if($ja_column || $clear){
	$style ='style="'.$ja_column.$clear.'"';
}
$j++;
if($params->get('auto_filter',1)==0):
?>
	<li <?php echo $style;?> class="last-item">
		<input class="btn" type="submit" name="btnSubmit" value="<?php echo JText::_('SEARCH'); ?>" />
		<!--<input class="btn" type="button" name="btnReset" value="<?php echo JText::_('RESET'); ?>" onclick="$('<?php echo $formid;?>').reset();" />-->
	</li>
<?php 
endif;
$clear = '';
?>	
</ul>
</form>

<script type="text/javascript">
/*<![CDATA[*/
window.addEvent('domready', function(){
	if($('jak2filter<?php echo $module->id;?>').getElement('#category_id')){
		jak2DisplayExtraFields($('jak2filter<?php echo $module->id;?>').getElement('#category_id'));
	}
	<?php if($auto_filter): ?>
	var f = $('<?php echo $formid; ?>');
	f.getElements('input, select, textarea').each(function(el) {
		el.addEvent('change', function(){
			$('<?php echo $formid; ?>').submit();
		});
	});
	<?php endif; ?>
});
/*]]>*/
</script>