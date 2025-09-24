/* wscms/galleriesimages/formImage.js v.4.0.0. 06/12/2021 */
$(document).ready(function() {
		
});

$('.submittheform').click(function () {
	controlloTabHTML5();
});

$('#selectTagsAll').click(function() {
	$('select#id_tagsID option').prop('selected', true);
});   

$('#deselectTagsAll').click(function() {
	$('select#id_tagsID option').prop('selected', false);
});

$('.custom-file-input').on('change', function () {
	let fileName = $(this).val().split('\\').pop();
	$(this).next('.custom-file-label').addClass("selected").html(fileName);
})
