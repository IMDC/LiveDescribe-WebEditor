/* 
 * functions dedicated to the main operations of the web pages
 * such as login form submission etc
 */

$(function() {

    ///
    //submit the login form using an ajax call to the server
    ///
    $('#loginForm').live('submit',function(){

        $('#loadImg').attr('style', "display: block;");
        return true;
    }); 


}); 






