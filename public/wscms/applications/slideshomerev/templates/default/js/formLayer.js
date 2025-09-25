/* wscms/slides-home-rev/formLayer.js v.3.5.4. 06/06/2019 */
$(document).ready(function() {
	
});

$('.custom-file-input').on('change', function () {
	let fileName = $(this).val().split('\\').pop();
	$(this).next('.custom-file-label').addClass("selected").html(fileName);
})

$('.submittheform').click(function () {
	controlloTabHTML5();
});