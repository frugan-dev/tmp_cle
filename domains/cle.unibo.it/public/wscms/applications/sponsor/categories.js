/* news/categories.js.php v.2.6.3. 06/04/2016 */
$(document).ready(function() {		
	$('#applicationForm')
	.bootstrapValidator({
		excluded: [':disabled'],
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
			}
		})
	.on('status.field.bv', function(e, data) {
		var $form     = $(e.target),
		validator = data.bv,
		$tabPane  = data.element.parents('.tab-pane'),
		tabId     = $tabPane.attr('id');
		if (tabId) {
			var $icon = $('a[href="#' + tabId + '"][data-toggle="tab"]').parent().find('i');
			// Add custom class to tab containing the field
			if (data.status == validator.STATUS_INVALID) {
				$icon.removeClass('fa-check').addClass('fa-times');
				} else if (data.status == validator.STATUS_VALID) {
					var isValidTab = validator.isValidContainer($tabPane);
					$icon.removeClass('fa-check fa-times')
					.addClass(isValidTab ? 'fa-check' : 'fa-times');
					}
			}
		});
	});