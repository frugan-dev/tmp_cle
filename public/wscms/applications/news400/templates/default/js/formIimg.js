/* wscms/news/formBimg.js v.3.5.2. 20/08/2017 */
$(document).ready(function() { 
 	
	});
	
$('#applicationForm').bootstrapValidator({
	excluded: [':disabled'],
	feedbackIcons: {
		valid: 'glyphicon glyphicon-ok',
		invalid: 'glyphicon glyphicon-remove',
		validating: 'glyphicon glyphicon-refresh'
		}
	})
	.on('status.field.bv', function(e, data) {
		var $form = $(e.target),
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


$(document).on('focusin', function(e) {
	if ($(e.target).closest(".mce-window").length) {
		 e.stopImmediatePropagation();
    	}
	});

tinymce.init({
	selector: ".editorHTML",
	theme: "modern",
	height: 300,
	language: user_lang,
	relative_urls: false,
	remove_script_host : false,
	convert_urls : true,
	document_base_url: siteUrl,
	filemanager_title:"Responsive Filemanager",
	external_filemanager_path: siteUrl+"/filemanager/",
	external_plugins: { "filemanager" : siteUrl+"/filemanager/plugin.min.js"},
	image_advtab: true,
	plugins: [
		"advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
		"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
		"save table contextmenu directionality emoticons template paste textcolor responsivefilemanager"
   ],
	toolbar: " undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | anchor link image responsivefilemanager | print preview media fullpage | forecolor backcolor emoticons",   
    content_css: [
    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
    '//www.tinymce.com/css/codepen.min.css']
 }); 