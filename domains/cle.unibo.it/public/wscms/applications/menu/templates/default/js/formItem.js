/* wscms/menu/formItem.js v.3.5.4. 29/10/2019 */
$(document).ready(function() { 
	initmenutypevars();
	changemenutypevars();
	inittypevars();
	changetypevars();
});
	
function initmenutypevars() {
	var menutypevars = $('#menutypevarsID :selected').val();
	refreshmenutypevars(menutypevars);
}

function changemenutypevars() {
	$('#menutypevarsID').change(function () {
		var menutypevars = $('#menutypevarsID :selected').val();
		refreshmenutypevars(menutypevars);
	});
}

function refreshmenutypevars(menutypevars) {
	//console.log(menutypevars);
	$.ajax({
		url: siteAdminUrl+CoreRequestAction+'/ajaxGetSectionModuleLinkInfoItem/',
		data: { menutypevars: menutypevars },
		type: "POST",
		success: function(result) {
			var mess = result;
			$('#sectionmoulemenuinfoID span').html(mess);

		}				
	});
}

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
	$('#sectionmodulemenuID').hide();
	$('#sectionmodulelinkID').hide();	
	$('#sectiontargetlinkID').hide();	
	if (typevars== 'label') {
	} else if (typevars== 'module-link') {
		$('#sectionmodulelinkID').show();
		$('#sectiontargetlinkID').show();	
	} else if (typevars== 'module-menu') {
		$('#sectionmodulemenuID').show();
	} else {
		$('#sectionurlID').show();
		$('#sectiontargetlinkID').show();	
	}
}


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
	