/* wscms/newsletter/newsletter.js v.1.0.0. 02/08/2016 */
$(document).ready(function() { 
  
	$('#datatimeinsID').datetimepicker({
		locale: 'it',
		defaultDate: defaultdate,
		format: 'L HH:mm'
	});

});

$('.submittheform').click(function () {
	controlloTabHTML5();
});