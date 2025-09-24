/* wscms/news/formCategory.js v.3.5.4. 10/09/2019 */
$(document).ready(function(){	

});

$('.submittheform').click(function () {
	$('input:invalid').each(function () {
		// Find the tab-pane that this element is inside, and get the id
		var $closest = $(this).closest('.tab-pane');
		var id = $closest.attr('id');
		// Find the link that corresponds to the pane and have it show
		$('.nav a[href="#' + id + '"]').tab('show');
		// Only want to do it once
		return false;
	});
});

$('#selectTagsAll').click(function() {
	$('select#id_tagsID option').prop('selected', true);
});   

$('#deselectTagsAll').click(function() {
	$('select#id_tagsID option').prop('selected', false);
});
	
$('#selectAssociationsAll').click(function() {
	$('select#id_associationsID option').prop('selected', true);
});   

$('#deselectAssociationsAll').click(function() {
	$('select#id_associationsID option').prop('selected', false);
});
