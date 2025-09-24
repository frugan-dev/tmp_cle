/* wscms/galleriesimages/formCate.js v.4.0.0. 07/12/2021 */

$(document).ready(function() {
});

$('.custom-file-input').on('change', function () {
	let fileName = $(this).val().split('\\').pop();
	$(this).next('.custom-file-label').addClass("selected").html(fileName);
})

$('.submittheform').click(function () {
	controlloTabHTML5();
});