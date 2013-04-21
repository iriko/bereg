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
function jak2DisplayExtraFields (obj) {
	var sOption = obj.getSelected();
	var group = sOption.getProperty('rel');
	var value = sOption.get("value");
	
	var parent = obj.getParent('.ja-k2filter');
	var parentid = parent.id;
	
	$$('#'+parentid+' .exfield').each(function(item){
		magicid = $(item).get('id') .toString();
		if('m'+magicid){		
			if($(item).hasClass('opened')) {
				$(item).removeClass('opened');
				$(item).addClass('closed');
				$('m'+magicid).setStyle('display', 'none');
				$('m'+magicid+'-container').set('html','');
				$('m'+magicid).getElements('li.selected').each(function(el){
					$(el).removeClass('selected');
				});
			}else if($(item).hasClass('closed')){
				$('m'+magicid+'-container').set('html','');
				$('m'+magicid).getElements('li.selected').each(function(el){
					$(el).removeClass('selected');
				});
			}
		}
	});
	
	if(value != 0 && group != '') {
		//active only extra fields of group that assigned to selected category
		var css = 'exgroup' + group;
		$$('#'+parentid+' .exfield').set('disabled', true);
		$$('#'+parentid+' .exfield').each(function(item){    
		    if($($(item).getProperty('id')+'_img')){	       
		        $($(item).getProperty('id')+'_img').setStyle('display','none');
		    }
		});
		$$('#'+parentid+' .'+css).set('disabled', false);
		
		$$('#'+parentid+' .'+css).each(function(item){    
		    if($($(item).getProperty('id')+'_img')){	       
		        $($(item).getProperty('id')+'_img').setStyle('display','block');
		    }
		});
	} else {
	
		//active all extra fields
		$$('#'+parentid+' .exfield').set('disabled', false);
		$$('#'+parentid+' .exfield').each(function(item){
		    
		    if($($(item).getProperty('id')+'_img')){	       
		        $($(item).getProperty('id')+'_img').setStyle('display','block');
		    }
		});
	}
}

function jaMagicInit(lid, fid) {
	$$('#'+lid+' li').each(function(item){
		if(item.hasClass('selected')) {
			jaMagicAddElement(lid, fid, item.innerHTML, item.getProperty('rel'));
		}
	});
	
	$$('#'+lid+' li').each(function(item){
		item.addEvent('click', function() {
			var id = this.getProperty('rel');
			if(!id) return;
			
		    if(this.hasClass('selected')) {
		    	this.removeClass('selected');
		    	$(lid+'-'+id).dispose();
		    } else {
		    	this.addClass('selected');
		    	jaMagicAddElement(lid, fid, this.innerHTML, id);
		    }
		    var autofilter = $(lid).getProperty('data-autofilter');
		    if(autofilter == 1) {
		    	$(lid).getParent('form').submit();
		    }
		});
	    
	});
}

function jaMagicAddElement(lid, fid, label, id) {
	var container = $(lid+'-container');
	var el = new Element('span', {
			id: lid+'-'+id,
		    html: label + '<input type="hidden" name="xf_'+fid+'[]" value="'+id+'" />'
		});
	var elRemove = new Element('span', {
			title: 'Remove',
			'class': 'remove',
			rel: id,
		    html: '',
		    events: {
		        click: function(){
		        	var lid = (this.getParent().id).replace(/-\d+$/, '');
		        	$$('#'+lid+' li[rel="'+this.getProperty('rel')+'"]').removeClass('selected');
		        	this.getParent().dispose();
		        	//auto search
				    var autofilter = $(lid).getProperty('data-autofilter');
				    if(autofilter == 1) {
				    	$(lid).getParent('form').submit();
				    }
		        }
		    }
		});
	el.grab(elRemove);
	container.grab(el);
}

function jaMagicSelect(controller, lid) {
	controller = $(controller);
	if(controller.hasClass('opened')) {
		controller.removeClass('opened');
		controller.addClass('closed');
		$(lid).setStyle('display', 'none');
	} else {
		controller.removeClass('closed');
		controller.addClass('opened');
		$(lid).setStyle('display', 'block');
	}
}
function jaMagicSelectClose(controller, lid) {
	controller = $(controller);
	controllerparent = $(lid).getParent().getElement('.select');
	if(controllerparent.hasClass('opened')) {
		controllerparent.removeClass('opened');
		controllerparent.addClass('closed');
	} else {
		controllerparent.removeClass('closed');
		controllerparent.addClass('opened');
	}
	$(lid).setStyle('display', 'none');	
}