/* wscms/site-home/formItem.js.php v.3.3.0. 10/08/2017 */
$(document).ready(function(){
	
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