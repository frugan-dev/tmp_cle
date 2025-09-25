jQuery(document).ready(function() {
	iFrameHeight();		
	})

function iFrameHeightaa() {
  var f = document.getElementById('blockrandom');
  f.style.height = '100px' ;
  var d = (f.contentWindow.document || f.contentDocument) ;
 
  var height = Math.max(d.documentElement.scrollHeight, d.body.scrollHeight) ;

  height += 20; // scrollbars?
  f.style.height = height + 'px' ;
  f.setAttribute("height", height) ;
   
}

function iFrameHeight() {
	var w = $("#wrapper");
	var d = $("#iframecontent");
	var ht = w.height();
	
	ht = ht / 2;
	if (ht <=400) ht = 400;
	d.height(ht);
	d.height(ht-20);
 
   
}