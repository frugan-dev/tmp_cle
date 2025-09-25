/* newsletter/invio-email.js v.4.0.0. 28/03/2022  */
$(document).ready(function() {  

	$('#sendnewsletterID').on('click',function(){

		bootbox.confirm("Sei sicuro di voler inviare la newsletter!?", function(result) {
			if (result) {
				let url = siteAdminUrl+CoreRequestAction+'ajaxUpdatePanel';
				$('#panelInvioEmailID').load(url);
			}
		});    
		 
	});


});

