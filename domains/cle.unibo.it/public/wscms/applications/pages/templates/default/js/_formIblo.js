/* wscms/pages/formIblo.js v.3.5.4. 28/03/2019 */
$(document).ready(function() { 
	


$('.submittheform').click(function () {



	console.log('valida form');
	 var res = validateCKEditor();
	if ( res == false) {
        alert('Devi inserire un contenuto!')
		return false;
	}

});

function validateCKEditor() {
	console.log('valida editor');
	/**
     * Compatibility fix for trim()
     * Browsers 3.5+, Safari 5+, IE9+, Chome 5+ and Opera 10.5+ support trim() natively
     * So we're detecting it before we override it
     * This is here because CK Editor isn't playing nice with jQuery
     * Thanks to http://blog.stevenlevithan.com/archives/faster-trim-javascript
     */
	if(!String.prototype.trim) {  
        String.prototype.trim = function () {  
            var c;
                 for (var i = 0; i < this.length; i++) {
                     c = this.charCodeAt(i);
                     if (c == 32 || c == 10 || c == 13 || c == 9 || c == 12) continue; else break;
                 }
                 for (var j = this.length - 1; j >= i; j--) {
                     c = this.charCodeAt(j);
                     if (c == 32 || c == 10 || c == 13 || c == 9 || c == 12) continue; else break;
                 }
                 return this.substring(i, j + 1);
        };  
    }
	//var messageLength = CKEDITOR.instances['content_it'].getData().replace(/<[^>]*>/gi, '').trim().length;
	//console.log(messageLength);
	
	CKEDITOR.on('instanceReady', function() { alert('content'); });
	
	/*
	var content = CKEDITOR.instances.content_it.updateElement();
	alert(content);
	*/
	
}

function aaaavalidateCKEditor() {
	console.log('valida editor');
	/**
     * Compatibility fix for trim()
     * Browsers 3.5+, Safari 5+, IE9+, Chome 5+ and Opera 10.5+ support trim() natively
     * So we're detecting it before we override it
     * This is here because CK Editor isn't playing nice with jQuery
     * Thanks to http://blog.stevenlevithan.com/archives/faster-trim-javascript
     */
    if(!String.prototype.trim) {  
        String.prototype.trim = function () {  
            var c;
                 for (var i = 0; i < this.length; i++) {
                     c = this.charCodeAt(i);
                     if (c == 32 || c == 10 || c == 13 || c == 9 || c == 12) continue; else break;
                 }
                 for (var j = this.length - 1; j >= i; j--) {
                     c = this.charCodeAt(j);
                     if (c == 32 || c == 10 || c == 13 || c == 9 || c == 12) continue; else break;
                 }
                 return this.substring(i, j + 1);
        };  
    }
 
	//var messageLength = CKEDITOR.instances['content_it'].getData().replace(/<[^>]*>/gi, '').trim().length;
	var content = CKEDITOR.instances.content_it.updateElement();
	alert(content);
	
	//if( !messageLength ) {
		//alert( 'Please enter a message' );
		//return false
	//} else {
		return false;
	//}
}


});
