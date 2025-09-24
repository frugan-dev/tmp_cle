/* wscms/site-users/formItem.js v.3.5.4. 28/03/2019 */
$(document).ready(function() {	

   /* controllo ajax username */
	$('#usernameID').change(function(){
		var username = $('#usernameID').val();
		var id = $('#idID').val();
		$.ajax({
			url:siteAdminUrl+CoreRequestAction+'checkUserAjaxItem/',
			type: "POST",
			data: 'username='+username+'&id='+id,
			success: function(result) {
				var mess = result;
				$('#usernameMessageID').html(mess);
				}				
			});
		});
		
	/* controllo ajax username */
	$('#emailID').change(function(){
		var email = $('#emailID').val();
		var id = $('#idID').val();
		$.ajax({
			url: siteAdminUrl+CoreRequestAction+'checkEmailAjaxItem/',
			type: "POST",
			data: 'email='+email+'&id='+id,
			success: function(result) {
				var mess = result;
				$('#emailMessageID').html(mess);
			}				
		});
	});
		
	/* controllo password */	
	$('#passwordCFID').change(function(){
		var pass = $('#passwordID').val();
		var passCF = $('#passwordCFID').val();
		if(pass !== passCF) {
			bootbox.alert(messages['password not match']);
		}
	});
		
});
	
$('.submittheform').click(function () {
	$('input:invalid').each(function () {
		// Find the tab-pane that this element is inside, and get the id
		var $closest = $(this).closest('.tab-pane');
		var id = $closest.attr('id');
		// Find the link that corresponds to the pane and have it show
		$('.nav a[href="#' + id + '"]').tab('show');
		// Only want to do it once
		return false;
	});
});

$('.custom-file-input').on('change', function () {
	let fileName = $(this).val().split('\\').pop();
	$(this).next('.custom-file-label').addClass("selected").html(fileName);
})
