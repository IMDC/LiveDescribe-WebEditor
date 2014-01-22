/* 
 * functions dedicated to the main operations of the web pages
 * such as login form submission etc
 */

$(function() {

    ///
    //submit the login form
    ///
    $('#loginForm').on('submit',function(){

        $('#loadImg').attr('style', "display: block;");
        return true;
    }); 

    ///
    //submit the register form 
    ///
    $('#regForm').on('submit',function(){

        $('#loadImg').attr('style', "display: block;");
        return true;
    }); 

}); 






