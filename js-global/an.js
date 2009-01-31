var timer = 0;
var windowHeight = 0;
var pageHeight = 0;

window.onscroll = scrolled;
window.onresize = function () {
	getWindowHeight();
	scrolled();
}

function scrolled() {
	clearTimeout(timer);
	timer = setTimeout("animateBg()", 100);
}

function animateBg() {
	var endTop = parseInt(getScrollHeight() / (pageHeight - windowHeight) * pageHeight) + 100;
	if (endTop > pageHeight)
		endTop = pageHeight;
	if (endTop > (parseInt($('wine').getStyle('top')))) {
		var endHeight = (pageHeight - endTop);
		$('wine').morph('top:' + endTop + 'px; height:' + endHeight + 'px;');
	}
}

function fixHeight(id) {
	var top = parseInt($(id).getStyle('top'));
	$(id).setStyle({
		height: ((pageHeight - top) + 'px')
	});
}

function getScrollHeight() {
	if( typeof( window.pageYOffset ) == 'number' ) {
	  //Netscape compliant
	  scrOfY = window.pageYOffset;
	} else if( document.body && document.body.scrollTop ) {
	  //DOM compliant
	  scrOfY = document.body.scrollTop;
	} else if( document.documentElement && document.documentElement.scrollTop ) {
	  //IE6 standards compliant mode
	  scrOfY = document.documentElement.scrollTop;
	}
	return scrOfY
}

function getWindowHeight() {
	if( typeof( window.innerWidth ) == 'number' ) {
		  //Non-IE
		  windowHeight = window.innerHeight;
		} else if( document.documentElement && document.documentElement.clientHeight ) {
		  //IE 6+ in 'standards compliant mode'
		  windowHeight = document.documentElement.clientHeight;
		} else if( document.body && document.body.clientHeight ) {
		  //IE 4 compatible
		  windowHeight = document.body.clientHeight;
		}
}

function getPageHeight(){
	if (window.innerHeight && window.scrollMaxY) {// Firefox
		pageHeight = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		pageHeight = document.body.scrollHeight;
	} else { // works in Explorer 6 Strict, Mozilla (not FF) and Safari
		pageHeight = document.body.offsetHeight;
  	}
 }