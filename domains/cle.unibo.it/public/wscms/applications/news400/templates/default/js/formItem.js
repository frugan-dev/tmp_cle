/* wscms/news/formItem.js v.3.5.4. 03/04/2019 */
$(document).ready(function () {

	$('.checknumchars').on('keyup', function (event) {
		var messagecontainer = $(this).data("messagecontainer");
		var max = $(this).data("bv-stringlength-max");
		var len = $(this).val().length;
		var char = max - len;
		$('#' + messagecontainer).text(char);
	});

	$('#datatimeinsDPID').datetimepicker({
		locale: user_lang,
		defaultDate: defaultdata,
		format: 'L HH:mm'
	});

	$('#datatimescainiDPID').datetimepicker({
		locale: user_lang,
		defaultDate: defaultdata1,
		format: 'L HH:mm'
	});

	$('#datatimescaendDPID').datetimepicker({
		locale: user_lang,
		defaultDate: defaultdata2,
		format: 'L HH:mm'
	});


	if ($('#scadenzaID').prop('checked') == true) {
		$('#datescadenzeID').css("visibility", "visible");
	} else {
		$('#datescadenzeID').css("visibility", "hidden");
	}

});

$('.custom-file-input').on('change', function () {
	let fileName = $(this).val().split('\\').pop();
	$(this).next('.custom-file-label').addClass("selected").html(fileName);
})


/* attiva disattiva il pannello scadenze */
$('#scadenzaID').on('click', function (e) {
	if ($('#scadenzaID').prop('checked') == true) {
		$('#datescadenzeID').css("visibility", "visible");
	} else {
		$('#datescadenzeID').css("visibility", "hidden");
	}
});

$('.submittheform').click(function () {
	controlloTabHTML5();
});
