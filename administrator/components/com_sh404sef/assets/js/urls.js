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

/*function shBoxResizer() {
	// get content
	var frame = SqueezeBox.content.getElementsByTagName('iframe')[0];
	if (typeof this.shCounter == 'undefined') {
		this.shCounter = 0;
	} else {
		this.shCounter++;
	}
	// frame.contentDocument.body.getElementById(
	// 'sh404sef-popup').offsetHeight;
	if (this.shCounter < 20) {
		if (typeof frame.contentDocument == 'undefined'
				|| typeof frame.contentDocument.body == 'undefined'
				|| !frame.contentDocument.body) {
			setTimeout('shBoxResizer();', 200);
		} else {
			if (typeof frame.contentDocument.body.getElementById != 'function') {
				setTimeout('shBoxResizer();', 200);
			} else {
				var mydiv = frame.contentDocument.body.getElementById('sh404sef-popup');
				if (typeof mydiv == 'undefined' || !mydiv) {
					setTimeout('shBoxResizer();', 200);
				} else {
					var size = {y:mydiv.offsetHeight+20};
					frame.contentDocument.body.height = size.y+20;
					SqueezeBox.resize( size, false);
					SqueezeBox.overlay.setStyles({
						height: size.y
					});
				}
			}
		}
	} else {
		alert('Counter reached');
	}
}*/

function shStopEvent(event) {

	// cancel the event
	new DOMEvent(event).stop();

}

function shProcessToolbarClick(id, pressbutton) {

	if (pressbutton != 'cancel') {
		var el = document.getElementById(id);
		if (typeof this.baseurl == 'undefined') {
			this.baseurl = [];
		}
		if (typeof this.baseurl[pressbutton] == 'undefined') {
			this.baseurl[pressbutton] = el.href;
		}
		var url = baseurl[pressbutton];
		var cid = document.getElementsByName('cid[]');
		var list = '';
		if (cid) {
			var length = cid.length;
			for ( var i = 0; i < length; i++) {
				if (cid[i].checked) {
					list += '&cid[]=' + cid[i].value;
				}
			}
		}
		url += list;
		el.href = url;
		window.parent.SqueezeBox.fromElement(el, {parse:'rel'});
	}

	return false;
}
