/* wscms/pages/pagesForm.js v.3.5.4. 30/07/2018 */
$(document).ready(function() { 
	pprefresh();
	
	inittypevars();
	changetypevars();
	
	$('.checknumchars').on('keyup',function(event) {
		var messagecontainer = $(this).data("messagecontainer");	
		var max = $(this).data("bv-stringlength-max");
		var len = $(this).val().length;
		var char = max - len;
		$('#'+messagecontainer).text(char);	
		});

	$('#id_templateID').change(function(){
		var id = $('#id_templateID').val();
		$.ajax({
			url: siteAdminUrl+moduleName+'/ajaxReloadTemplateDataItem/'+id,
			type: "POST",
			success: function(result) {
				var mess = result;
				$('#templateDataID').html(mess);
				pprefresh();
				}				
			});
		pprefresh();
		});

	$('#updatedID').datetimepicker({
		locale: 'it',
		defaultDate: defaultdate,
		format: 'L HH:mm'
	});
	
});

$('.custom-file-input').on('change', function () {
	let fileName = $(this).val().split('\\').pop();
	$(this).next('.custom-file-label').addClass("selected").html(fileName);
})

$('.submittheform').click(function () {
	controlloTabHTML5();
});

function inittypevars() {
	var typevars = $('#typeID :selected').val();
	refreshtypevars(typevars);
}

function changetypevars() {
	$('#typeID').change(function () {
		var typevars = $('#typeID :selected').val();
		refreshtypevars(typevars);
	});
}

function refreshtypevars(typevars) {
	$('#sectionurlID').hide();
	$('#sectionmodulelinkID').hide();	
	 
	if (typevars== 'label') {

	} else if (typevars== 'default') {
		
	} else if (typevars== 'module-link') {
		$('#sectionmodulelinkID').show();
	} else {
		$('#sectionurlID').show();
	}
}
	



function pprefresh(){
	if(!jQuery('#lightbox').length) { lightbox.init(); }
}