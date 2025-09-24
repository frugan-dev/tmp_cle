var newshome_categories_id = 0;
var newshome_page = 1;

$(document).ready(function () {
   getNewsHomeJson(newshome_categories_id,newshome_page);
});

$('.setHomeNewsCategory').click(function(){
    newshome_categories_id = $( this).attr('data-rif');
    console.log(newshome_categories_id);
    getNewsHomeJson(newshome_categories_id,newshome_page);
    $('.setHomeNewsCategory').removeClass( "active" );
    $('#homeNewsCategory'+newshome_categories_id+'ID').addClass( "active" );

});

function getNewsHomeJson(newshome_categories_id,newshome_page) {
    $('#imgWaitingID').css('visibility', 'visible');
    
    $.ajax({
        type: 'POST',
        url: siteUrl + "ajax/getHtmlNewsHometListFromDb.php",
        async: false,
        data: { 
            'categories_id': newshome_categories_id, 
            'page':newshome_page
        },
        success: function(res) {
            $('#imgWaitingID').css('visibility', 'hidden');
            $('#newsHomeContainerID').empty().append(res); 
            setHomeNewsPage();
        },
        error: function() {
            console.log("errore lettura newshome json");
        } 
    }); 
}

function setHomeNewsPage() {
    $('.setHomeNewsPage').click(function(){
        newshome_page = $( this).attr('data-rif');
        console.log(newshome_page);
        getNewsHomeJson(newshome_categories_id,newshome_page);
    });

}