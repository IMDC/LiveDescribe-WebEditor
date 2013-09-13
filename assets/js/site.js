/* 
 * functions dedicated to the main operations of the web pages
 * such as login form submission etc
 */

$(function() {

    ///
    //submit the login form using an ajax call to the server
    ///
    $('#loginForm').live('submit',function(){
        
        var error = $(this).find('#errorBox');

        var load = $('#loginLoad');

        var img = $('<img id="loadingImg" />');
        img.attr('src' , "http://imdc.ca/projects/livedescribe/LiveDescribe-WebEditor/assets/img/loading.gif");
        img.attr('width' , 40);
        img.appendTo('#loginLoad');
        
    }); 


});







