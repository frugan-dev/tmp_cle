$(document).on('focusin', function(e) {
	if ($(e.target).closest(".mce-window").length) {
		 e.stopImmediatePropagation();
    }
});
	
tinymce.init({
	selector: ".editorHTML",
	theme: "silver",
	height : "480",
	language: charset_date_lang,
	relative_urls: false,
	remove_script_host : false,
	convert_urls : true,
	document_base_url: siteUrl,

	filemanager_title:"Responsive Filemanager",
	external_filemanager_path: siteUrl+"/filemanager/",
	external_plugins: { "filemanager" : siteUrl+"/filemanager/plugin.min.js"},
	image_advtab: true,

	table_default_attributes: {
		class: 'table table-bordered'
	},
	table_class_list: [
    	{title: 'Base', value: 'table'},
    	{title: 'Striped rows', value: 'table table-striped'},
    	{title: 'Bordered table', value: 'table table-bordered'},
    	{title: 'Hover rows', value: 'table table-hover'},
    	{title: 'Condensed table', value: 'table table-condensed'}
	],
	table_cell_class_list: [
		{title: 'Active', value: 'active'},
		{title: 'Success', value: 'success'},
		{title: 'Warning', value: 'warning'},
		{title: 'Danger', value: 'danger'},
		{title: 'Info', value: 'info'}
	],
	table_row_class_list: [
		{title: 'Active', value: 'active'},
		{title: 'Success', value: 'success'},
		{title: 'Warning', value: 'warning'},
		{title: 'Danger', value: 'danger'},
		{title: 'Info', value: 'info'}
	],	

	plugins: [
		"advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
		"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
		"save table contextmenu directionality emoticons template paste textcolor responsivefilemanager"
   ],
	toolbar: " undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | anchor link image responsivefilemanager | print preview media fullpage | forecolor backcolor emoticons",   

	style_formats_merge: true, 
	style_formats: [
		{ title: 'Div', block: 'div'},
		{ title: 'Div Bootstrap table responsive', block: 'div', classes: 'table-responsive'},
	]
	
    
 }); 