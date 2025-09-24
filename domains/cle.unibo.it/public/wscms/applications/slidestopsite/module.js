/* slides-home-rev/module.js v.2.6.3. 11/04/2016 */
$(document).ready(function(){
	$("a[rel^='prettyPhoto']").prettyPhoto({
		social_tools: false,
		show_title: false    
		});

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
		
$(document).on('focusin', function(e) {
	if ($(e.target).closest(".mce-window").length) {
		e.stopImmediatePropagation();
		}
	});
	
tinymce.init({
    selector: "textarea",
    theme: "modern",
    height: 300,
    language: "it",
    plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor"
   ],
   content_css: "css/content.css",
   toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons", 
   style_formats: [
        {title: 'Bold text', inline: 'b'},
        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
        {title: 'Example 1', inline: 'span', classes: 'example1'},
        {title: 'Example 2', inline: 'span', classes: 'example2'},
        {title: 'Table styles'},
        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
    ]
 }); 
