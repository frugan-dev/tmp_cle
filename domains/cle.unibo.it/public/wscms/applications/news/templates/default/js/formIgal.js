/* wscms/pages/formIgal.js v.3.5.2. 14/02/2018 */
$(document).ready(function() { 
});

$('.custom-file-input').on('change', function () {
	let fileName = $(this).val().split('\\').pop();
	$(this).next('.custom-file-label').addClass("selected").html(fileName);
})

$('.submittheform').click(function () {
	$('form#applicationForm').submit();
});