/* wscms/pages/formIblo.js v.3.5.4. 28/03/2019 */
$(document).ready(function() { 
});

$('.custom-file-input').on('change', function () {
	let fileName = $(this).val().split('\\').pop();
	$(this).next('.custom-file-label').addClass("selected").html(fileName);
})

$('.submittheform').click(function () {
	controlloTabHTML5();
	/*
	var content = tinyMCE.get('content_itID').getContent(); // msg = textarea id
	if( content == "" || content == null) {
		alert(messages['Devi inserire un contenuto!']);
		$('a[href="#datibaseit-tab"]').tab('show');
		return false;
	}
	*/
});