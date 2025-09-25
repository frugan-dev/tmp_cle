/* wscms/products/formCate.js.php v.3.5.3. 23/05/2018 */
$(document).ready(function(){

});

$('.custom-file-input').on('change', function () {
	let fileName = $(this).val().split('\\').pop();
	$(this).next('.custom-file-label').addClass("selected").html(fileName);
})

$('.submittheform').click(function () {
	controlloTabHTML5();
});