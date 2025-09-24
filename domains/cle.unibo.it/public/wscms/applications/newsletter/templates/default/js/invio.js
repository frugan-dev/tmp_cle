/* newsletter/invio.js v.1.0.0. 18/07/2016 */
$(document).ready(function() {   
	getListAddressTemp();	
	});
	
$('#moveAddressToSendList').click(function(){	
	var ladd = $('#listAddressID').val();		
	var jqxhr = $.ajax({
		url: siteAdminUrl+CoreRequestAction+'ajaxMoveAddressToSendList',
		type: "POST",
		data: 'listAddress='+$('#listAddressID').val(),
		dataType: "text",})
		.done(function() {
			getListAddressTemp();
			})
		.fail(function() {
			})
		.always(function() {
			})				
	});
	
$('#removeFromSendList').click(function(){
	var ladd = $('#listAddressTempID').val();		
	var jqxhr = $.ajax({
		url: siteAdminUrl+CoreRequestAction+'ajaxDeleteAddressToSendList',
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


function getListAddressTemp() {
	$.ajax({
		url: siteAdminUrl+CoreRequestAction+'ajaxGetListAddressTemp',
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