/* wscms/site-pages/index.php v.1.0.1. 07/09/2016 */

$(document).ready(function() {	
	
})

$('.custom-file-input').on('change', function () {
	let fileName = $(this).val().split('\\').pop();
	$(this).next('.custom-file-label').addClass("selected").html(fileName);
})

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

$('#updatedID').datetimepicker({
	locale: user_lang,
	defaultDate: defaultdate,
	format: 'L HH:mm'
});


$('#id_templateID').change(function(){
	var id = $('#id_templateID').val();
	$.ajax({
		url: window.baseUrlAdmin+moduleName+'/ajaxReloadTemplateData/'+id,
		type: "POST",
		success: function(result) {
			var mess = result;
			$('#templateDataID').html(mess);
			pprefresh();
		}				
	});
});	

	
function loadpage(page_request, containerid){
	if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1)){
		document.getElementById(containerid).innerHTML=page_request.responseText;
		//$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'slow',theme:'dark_rounded',slideshow:4000, autoplay_slideshow: false});
	}
}