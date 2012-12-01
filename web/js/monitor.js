/* monitor.js : js specific to monitor app */
/* we do not want this on CDN */
/* declare required namespaces */

webgloo = window.webgloo || {}
webgloo.sc = webgloo.sc || {};
webgloo.sc.item = webgloo.sc.item || {}


webgloo.sc.item.addAdminActions : function() {
    //feature posts
    $("a.feature-post").click(function(event){
        event.preventDefault();

        var dataObj = {} ;
        dataObj.params = {} ;
        dataObj.params.postId  = $(this).attr("id");
        dataObj.params.action = "ADD" ;
        dataObj.endPoint = "/monitor/action/item/tag.php";

        //open popup
        webgloo.sc.SimplePopup.init();
        webgloo.sc.SimplePopup.post(dataObj,{
            dataType : "json",
            reload : false,
            onSuccess : function () {
                $("#fps-" + dataObj.params.postId).html('<span class="badge badge-warning">F</span>');
            }
        });


    }) ;

    //unfeature posts
    $("a.unfeature-post").click(function(event){
        event.preventDefault();

        var dataObj = {} ;
        dataObj.params = {} ;
        dataObj.params.postId  = $(this).attr("id");
        dataObj.params.action = "REMOVE" ;
        dataObj.endPoint = "/monitor/action/item/tag.php";

        //open popup
        webgloo.sc.SimplePopup.init();
        webgloo.sc.SimplePopup.post(dataObj,{
            "dataType" : "json",
            "reload" : false,
            onSuccess : function () {
                $("#fps-" + dataObj.params.postId).html("");
            }
        });
    }) ;

    //unfeature posts
    $("a.ban-user").click(function(event){
        event.preventDefault();

        var dataObj = {} ;
        dataObj.params = {} ;
        dataObj.params.loginId  = $(this).attr("id");
        dataObj.params.action = "BAN" ;
        dataObj.endPoint = "/monitor/action/user/tag.php";

        //open popup
        webgloo.sc.SimplePopup.init();
        webgloo.sc.SimplePopup.post(dataObj,{
            "dataType" : "json",
            "reload" : true
        });
    }) ;

     //unfeature posts
    $("a.unban-user").click(function(event){
        event.preventDefault();

        var dataObj = {} ;
        dataObj.params = {} ;
        dataObj.params.loginId  = $(this).attr("id");
        dataObj.params.action = "UNBAN" ;
        dataObj.endPoint = "/monitor/action/user/tag.php";

        //open popup
        webgloo.sc.SimplePopup.init();
        webgloo.sc.SimplePopup.post(dataObj,{
            "dataType" : "json",
            "reload" : true
        });
    }) ;

     //unfeature posts
    $("a.taint-user").click(function(event){
        event.preventDefault();

        var dataObj = {} ;
        dataObj.params = {} ;
        dataObj.params.userId  = $(this).attr("id");
        dataObj.params.action = "TAINT" ;
        dataObj.endPoint = "/monitor/action/user/tag.php";

        //open popup
        webgloo.sc.SimplePopup.init();
        webgloo.sc.SimplePopup.post(dataObj,{
            "dataType" : "json",
            "reload" : true
        });
    }) ;


}



