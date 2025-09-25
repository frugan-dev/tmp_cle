$(function () {
	$("a.confirm").click(function(e) {
	    e.preventDefault();
	    var location = $(this).attr('href');
	    bootbox.confirm(messages['Sei sicuro?'],function(confirmed) {
	        if(confirmed) {
	        window.location.replace(location);
	        }
	    });
	});     
});