/* newsletter-ic/invio-cat.js v.2.6.2.1 03/03/2016 */
$(document).ready(function() {   
	getListAddressTemp();	
});
	
$('#moveAddressToSendList').click(function() {		
	var jqxhr = $.ajax({
		url: siteAdminUrl+CoreRequestAction+'ajaxMoveAddressCatToSendList',
		type: "POST",
		data: 'listAddressCat='+$('#listAddressCatID').val(),
		dataType: "text"
	})
	.done(function() {
		getListAddressTemp();
	})
	.fail(function() {
	})
	.always(function() {
	})			
});
	
$('#removeAddressFromSendList').click(function() {
	var ladd = $('#listAddressTempID').val();		
	var jqxhr = $.ajax({
		url: siteAdminUrl+CoreRequestAction+'ajaxDeleteAddressCatToSendList',
		type: "POST",
		data: 'listAddress='+$('#listAddressTempID').val(),
		dataType: "text",})
		.done(function() {
			getListAddressTemp();
		})
		.fail(function() {
		})
		.always(function() {
		})		
});
	
$('#deselAllAddressTempID').click(function(){
	$('#listAddressTempID option').prop('selected', false);
	});

$('#selAllAddressTempID').click(function(){
	$('#listAddressTempID option').prop('selected', true);
	});
	
$('#deselAllAddressID').click(function(){
	$('#listAddressID option').prop('selected', false);
	});

$('#selAllAddressID').click(function(){
	$('#listAddressID option').prop('selected', true);
	});
	
$('#sendNewsletterID').click(function(){
	if ($('#listAddressTempID option').length == 0){
		bootbox.alert("Devi selezionare almeno un indirizzo da inviare!");
		return false;
		} else {
			bootbox.confirm("Sei sicuro di voler inviare la newsletter!?");
			}	
	});

$('#myForm').submit(function(e) {
	if ($('#listAddressTempID option').length == 0){
		bootbox.alert("Devi selezionare almeno un indirizzo da inviare!");
		return false;
		} else {	
	     var currentForm = this;
	     e.preventDefault();
	     bootbox.confirm("Sei sicuro di voler inviare la newsletter!?", function(result) {
	         if (result) {
	             currentForm.submit();
	         }
	     });     
	  	}
});

function getListAddressTemp() 
{
	$.ajax({
		url: siteAdminUrl+CoreRequestAction+'ajaxGetListAddressCatTemp',
		type: "POST",
		dataType: "json",
		success: function(result) {
			var sel = $("#listAddressTempID");
			sel.empty();
			for (var i=0; i<result.length; i++) {
				sel.append('<option value="' + result[i].id + '">' + result[i].email + '</option>');
			}			
		}
	});
}