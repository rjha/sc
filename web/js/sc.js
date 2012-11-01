
/* + useful methods */

/* also see http://javascript.crockford.com/remedial.html for supplant */

String.prototype.supplant = function (o) {
    return this.replace(/{([^{}]*)}/g,
        function (a, b) {
            var r = o[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        });
};


/* JSON support for old browsers */
/* also see  https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/JSON  */

if (!window.JSON) {
    console.log("Old browser using imitation of native JSON object");
    window.JSON = {
        parse: function (sJSON) {return eval("(" + sJSON + ")");},
        stringify: function (vContent) {
            if (vContent instanceof Object) {
                var sOutput = "";
                if (vContent.constructor === Array) {
                    for (var nId = 0; nId < vContent.length; sOutput += this.stringify(vContent[nId]) + ",", nId++);
                    return "[" + sOutput.substr(0, sOutput.length - 1) + "]";
                }

                if (vContent.toString !== Object.prototype.toString) {
                    return "\"" + vContent.toString().replace(/"/g, "\\$&") + "\"";
                }
                for (var sProp in vContent) {
                    sOutput += "\"" + sProp.replace(/"/g, "\\$&") + "\":" + this.stringify(vContent[sProp]) + ",";
                }
                return "{" + sOutput.substr(0, sOutput.length - 1) + "}";
          }
          return typeof vContent === "string" ? "\"" + vContent.replace(/"/g, "\\$&") + "\"" : String(vContent);
        }
  };
}

/*
 * Base64 encoding in javascript *
 * also see http://my.opera.com/Lex1/blog/fast-base64-encoding-and-test-results
 * also see https://github.com/operasoftware/
 *
 */

function encodeBase64(str){
    var chr1, chr2, chr3, rez = '', arr = [], i = 0, j = 0, code = 0;
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/='.split('');

    while(code = str.charCodeAt(j++)){
        if(code < 128){
            arr[arr.length] = code;
        }
        else if(code < 2048){
            arr[arr.length] = 192 | (code >> 6);
            arr[arr.length] = 128 | (code & 63);
        }
        else if(code < 65536){
            arr[arr.length] = 224 | (code >> 12);
            arr[arr.length] = 128 | ((code >> 6) & 63);
            arr[arr.length] = 128 | (code & 63);
        }
        else{
            arr[arr.length] = 240 | (code >> 18);
            arr[arr.length] = 128 | ((code >> 12) & 63);
            arr[arr.length] = 128 | ((code >> 6) & 63);
            arr[arr.length] = 128 | (code & 63);
        }
    };

    while(i < arr.length){
        chr1 = arr[i++];
        chr2 = arr[i++];
        chr3 = arr[i++];

        rez += chars[chr1 >> 2];
        rez += chars[((chr1 & 3) << 4) | (chr2 >> 4)];
        rez += chars[chr2 === undefined ? 64 : ((chr2 & 15) << 2) | (chr3 >> 6)];
        rez += chars[chr3 === undefined ? 64 : chr3 & 63];
    };
    return rez;
};

/* + namepsaces */
webgloo = window.webgloo || {};
webgloo.sc = webgloo.sc || {};

webgloo.sc.util = {
    addTextCounter: function(inputId,counterId) {
        var max = $(inputId).attr("maxlength");
        $(inputId).keydown (function () {
            var text = $(inputId).val();
            var current = text.length;
            $(counterId).text(current + "/" + max);
        });
    },

    getCheckedItems : function(containerId) {
        var checkBoxes =  $(containerId).find("input:checkbox");
        var isChecked ;
        var itemIds = [] ;

        checkBoxes.each(function(i) {
            isChecked = $(this).prop("checked");
            if(isChecked) {
                itemIds.push($(this).attr('id'));
            }
        
        }) ;

        return itemIds ;
    },

    initPageCheckbox : function(containerId) {
        $('input:checkbox[id=page-checkbox]').click(function(event) {
            //@imp donot event.preventDefault();
            var checkBoxes =  $(containerId).find("input:checkbox");
            var state =  $('input:checkbox[id=page-checkbox]').prop("checked");
            checkBoxes.prop("checked", state);
            
        });
    },

    fixAlert : function () {
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 5000);
    }
}

webgloo.sc.toolbar = {
    add : function() {
        window.setTimeout(webgloo.sc.toolbar.closeOverlay,8000);
        //group browser
        $("a#nav-popup-group").click(function(event) {
            event.preventDefault();
            $targetUrl= "/group/popup/featured.php";
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.load($targetUrl);
        });

        //share popup
        $("a#nav-popup-share").click(function(event) {
            event.preventDefault();
            //get content of nav-share
            var content = $("#nav-share").html();
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.show(content);
        });

         //share popup
        $("a#nav-popup-join").click(function(event) {
            event.preventDefault();
            $targetUrl= "/user/popup/join-now.php";
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.load($targetUrl);
        });

        $("a#close-overlay").click(function(event) {
            event.preventDefault();
            $("#overlay-message").hide();
        });
    },

    closeOverlay : function() {
        $("#overlay-message").hide();
    }

}

webgloo.sc.home = {
    addTiles : function() {

        $('.tile .options').hide();

        var $container = $('#tiles');
        $container.imagesLoaded(function(){
            $container.isotope({
                itemSelector : '.tile',
                layoutMode : 'masonry'
                
            });

            //show tile options only after images has been loaded by
            //masonry layout. otherwise on mouse enter we see tile.option toolbar
            //displayed at top of page

            webgloo.sc.home.addTileOptions();
        });

        //Add item toolbar actions
        webgloo.sc.item.addActions();

    },

    addTileOptions : function () {
        $('.tile').live("mouseenter", function() {$(this).find('.options').show();});
        $('.tile').live("mouseleave", function() {$(this).find('.options').hide();});
    },

    addSmallTiles : function() {
        var $container = $('#tiles');
        $container.imagesLoaded(function(){
            $container.isotope({
                itemSelector : '.stamp',
                layoutMode : 'masonry'             
            });

        });
    }

}

/* +simple popup object */
webgloo.sc.SimplePopup = {
    init : function() {
        $(document).bind('keydown', function(e) {
            if (e.keyCode == 27) {
                webgloo.sc.SimplePopup.close();
            }
        });

        $("a#simple-popup-close").click(function(event) {
            event.preventDefault();
            webgloo.sc.SimplePopup.close();
        });

        $("#popup-mask").click(function() {
            webgloo.sc.SimplePopup.close();
        });

    },

    close : function() {
        $("#simple-popup #content").html('');
        $("#simple-popup").hide();
        $("#popup-mask").hide();
    },

    show : function(content) {
        this.removeSpinner();
        $("#simple-popup #content").html('');
        $("#simple-popup #content").html(content);

        /* show mask */
        var maskHeight = $(document).height();
        var maskWidth = $(window).width();

        $("#popup-mask").css({'width':maskWidth,'height':maskHeight});
        $("#popup-mask").show();

        /* show popup */
        $("#simple-popup").show();
    },

    addSpinner : function() {
        this.close();
        $("#block-spinner").html('');
        var content = '<div> Please wait...</div> '
            + '<div> <img src="/css/asset/sc/round_loader.gif" alt="loading ..." /> </div>' ;
        $("#block-spinner").html(content);

        /* show mask */
        var maskHeight = $(document).height();
        var maskWidth = $(window).width();
        $("#popup-mask").css({'width':maskWidth,'height':maskHeight});
        $("#popup-mask").show();

        /* show spinner */
        $("#block-spinner").show();
    },

    removeSpinner : function() {
        $("#block-spinner").html('');
        $("#popup-mask").hide();
        $("#block-spinner").hide();

    },

    redirect : function () {
        webgloo.sc.SimplePopup.removeSpinner();
        webgloo.sc.SimplePopup.close();
        window.location.replace(webgloo.sc.SimplePopup.gotoUrl);
    },

    processJson : function(response,settings,dataObj) {

        switch(response.code) {
            case 200 :
                //success
                if(settings.autoCloseInterval > 0 ) {
                    window.setTimeout(this.close,settings.autoCloseInterval);
                }

                if(settings.visible){
                    this.show(response.message);
                }

                if(!settings.reload && (typeof settings.onSuccess !== "undefined")) {
                    settings.onSuccess.call();
                }

                if(settings.reload){
                    window.location.reload(true);
                }

            break;

            case 401:
                // authentication failure
                // redirect to login page with pending session action
                // dataObj to complete session action should supply the following 
                // dataObj.endPoint
                // dataObj.params = {} ; 
                // dataObj.params.x  = xval ;
                // dataObj.params.y = yval ;
                // dataObj.params.action = "REMOVE" ;
                //  dataObj.params.{loginId} is a special parameter that will be substituted by
                // actual loginId after authentication
                
                // @imp  dataObj.params should be an object containing simple
                // key value pairs. Params keys or values can again be objects
                // but it is better to avoid that complexity.
                
                g_action_data =  encodeBase64(JSON.stringify(dataObj));
                //encode for use in URL query string
                qUrl = encodeURIComponent(window.location.href);
                webgloo.sc.SimplePopup.gotoUrl = '/user/login.php?q='
                    +qUrl + '&g_session_action=' + g_action_data;

                //change spinner message
                var message = '<div> Redirecting to login page... </div> ' +
                    '<div> <img src="/css/asset/sc/round_loader.gif" alt="loader" /> </div>' ;

                $("#block-spinner").html(message);
                window.setTimeout(this.redirect,3000);

            break;

            case 500:
                //error - keep open
                this.show(response.message);
            break;

            default:
                this.show(response.message);
            break;
        }
    },

    post:function (dataObj,options) {

        //@todo deal with undefined or NULL options
        //show spinner
        this.addSpinner();

        var defaults = {
            visible : true ,
            autoCloseInterval : -1,
            reload : false ,
            type : "POST",
            dataType : "text" ,
            onSuccess : undefined 

        }

        var settings = $.extend({}, defaults, options);
        
        //ajax call start
        $.ajax({
            url: dataObj.endPoint,
            type: settings.type ,
            dataType: settings.dataType,
            data :  dataObj.params,
            timeout: 9000,
            processData:true,
            //js errors callback
            error: function(XMLHttpRequest, response){
                //remove spinner
                webgloo.sc.SimplePopup.show(response);
            },

            //server script errors are reported inside success callback
            success: function(response){
                switch(settings.dataType) {
                    case 'json' :
                        webgloo.sc.SimplePopup.processJson(response,settings,dataObj);
                    break;

                    default:
                        webgloo.sc.SimplePopup.show(response);
                    break;
                }

            }
        }); //ajax call end
    },

    load: function(targetUrl) {

            var dataObj = {} ;
            dataObj.endPoint = targetUrl ;
            dataObj.params = {} ;
            webgloo.sc.SimplePopup.post(dataObj,{
                "dataType" : "text",
                "reload" : false,
                "visible" : true});

    }
}

webgloo.sc.item = {

    addAdminActions : function() {
        //feature posts
        $("a.feature-post-link").click(function(event){
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
        $("a.unfeature-post-link").click(function(event){
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


    },

    addActions : function() {
        //add like & save callbacks
        $("a.like-post-link").live("click",function(event){
            event.preventDefault();

            var dataObj = {} ;
            dataObj.params = {} ;
            dataObj.params.itemId  = $(this).attr("id");
            dataObj.params.action = "LIKE" ;
            dataObj.endPoint = "/qa/ajax/bookmark.php";

            //open popup
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.post(dataObj,{"dataType" : "json", "autoCloseInterval" : 3000});
        }) ;

        $("a.save-post-link").live("click", function(event){
            event.preventDefault();

            var dataObj = {} ;
            dataObj.params = {} ;
            dataObj.params.itemId  = $(this).attr("id");
            dataObj.params.action = "SAVE" ;
            dataObj.endPoint = "/qa/ajax/bookmark.php";

            //open popup
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.post(dataObj,{"dataType" : "json", "autoCloseInterval" : 3000});
        }) ;

        //unsave
        $("a.remove-post-link").live("click",function(event){
            event.preventDefault();

            var dataObj = {} ;
            dataObj.params = {} ;
            dataObj.params.itemId  = $(this).attr("id");
            dataObj.params.action = "REMOVE" ;
            dataObj.endPoint = "/qa/ajax/bookmark.php";

            //open popup
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.post(dataObj,{
                "dataType" : "json",
                "reload" : true,
                "visible" : false});
            //reload page

        }) ;

        $("a.follow-user-link").live("click",function(event){
            event.preventDefault();

            var id = $(this).attr("id");
            //parse id to get follower and following
            var ids = id.split('|');
            //u1 -> u2

            var dataObj = {} ;
            dataObj.params = {} ;
            // when there is no login session, params.followerId has a special value of
            // "{loginId}" that will be substituted by actual loginId after authentication.
            dataObj.params.followerId  = ids[0] ;
            dataObj.params.followingId  = ids[1] ;
            dataObj.params.action = "FOLLOW" ;
            dataObj.endPoint = "/qa/ajax/social-graph.php";

            //open popup
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.post(dataObj,{"dataType" : "json", "autoCloseInterval" : 3000});
        }) ;

        $("a.unfollow-user-link").live("click",function(event){

            event.preventDefault();

            var id = $(this).attr("id");
            //parse id to get follower and following
            var ids = id.split('|');
            //u1 -> u2

            var dataObj = {} ;
            dataObj.params = {} ;
            dataObj.params.followerId  = ids[0] ;
            dataObj.params.followingId  = ids[1] ;
            dataObj.params.action = "UNFOLLOW" ;
            dataObj.endPoint = "/qa/ajax/social-graph.php";

            //open popup
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.post(dataObj,
                {
                    "dataType" : "json",
                    "reload" : true,
                    "visible" : false
                }
            );

        }) ;

    }
}

webgloo.sc.Lists = {

    containerId : '' ,
    debug : false,
    imageDataObj : {} ,

    populateSubmitForm : function(itemIds,isNew,listId) {

        var frm = document.forms["list-form1"];
        frm.image_error.value = 
            (typeof webgloo.sc.Lists.imageError === 'undefined') ? 0 : webgloo.sc.Lists.imageError ;

        var items = [] , itemObj;
        for(var i = 0; i < itemIds.length; i++) {
            itemObj = {"id" : itemIds[i]};
            if(webgloo.sc.Lists.imageDataObj.hasOwnProperty(itemObj.id)) {
                itemObj.thumbnail =  webgloo.sc.Lists.imageDataObj[itemObj.id].thumbnail;
            }

            items.push(itemObj);
        }

        frm.items_json.value = JSON.stringify(items) ;

        
        if(isNew){
            //create new list
            frm.list_id.value = '' ;
            frm.is_new.value = 1 ;
        } else {
            frm.list_id.value = listId;
            frm.is_new.value = 0 ;
        }

        if(webgloo.sc.Lists.debug) {
            console.log("items json = " + frm.items_json.value);
            console.log("list Id = " + frm.list_id.value);
            console.log("is_new = " + frm.is_new.value);
            console.log("image_error = " + frm.image_error.value);
            return ;
        }

        $('#list-form1').submit();

    },

    openPopup : function() {
        
        var itemIds = webgloo.sc.util.getCheckedItems("#widgets");

        if(itemIds.length > 0 ) {
            // items selected
            $("#page-message").html('');
            $("#page-message").hide();
            $("#list-popup").show("slow");

        } else {
            var message = "You have not selected any item! Please select items.";
            $("#page-message").html(message);
            $("#page-message").show("slow");
            window.setTimeout(function () { $("#page-message").hide("slow");},5000);

        }
    },

    init : function(containerId) {
        webgloo.sc.Lists.containerId = containerId ;
        $("a#close-list-container").click(function(event) {
            $("#list-container").hide("slow");
        });

        $("a#open-list-edit").click(function(event) {
            $("#list-edit-form").show("slow");
        }) ;


        $("a#open-list-add").click(function(event) {
            $("#list-add-form").show("slow");
        }) ;


        $("a#open-list-delete").click(function(event) {
            $("#list-delete-form").show("slow");
        }) ;

        $("a#open-list-popup").click(function(event) {
            webgloo.sc.Lists.openPopup();
        }) ;

        $("#lists li a").live("click", function(event){
            var listId = $(this).attr("id");
            var itemIds = webgloo.sc.util.getCheckedItems(webgloo.sc.Lists.containerId);

            if(itemIds.length > 0 ) {
                webgloo.sc.Lists.populateSubmitForm(itemIds,false,listId);
            }

        }) ;

        $("#create-list").click(function(event){
            event.preventDefault();
            var name = jQuery.trim($("#new-list-name").val());
            if( name == '' )
                return ;

            var listId = $(this).attr("id");
            var itemIds = webgloo.sc.util.getCheckedItems(webgloo.sc.Lists.containerId);

            if(itemIds.length > 0 ) {
                webgloo.sc.Lists.populateSubmitForm(itemIds,true,'');
            }

        }) ;

    },

    initDetail : function() {

       


    }
}

webgloo.sc.admin = {

    addSlugPanelItems : function (itemInBox) {
        //split on commas
        var tokens = itemInBox.split(",");

        for (var i = 0; i < tokens.length; i++) {
           var token = jQuery.trim(tokens[i]);
           if(token == '') continue ;
           var buffer = '<tr>' + 
                        ' <td> <input type="checkbox" name="g[]" checked ="checked" value="' 
                        + token + '"/> </td> ' 
                        +'<td> &dash;</td> <td> <span class="comment-text">' 
                        + token 
                        + '</span> </td> </tr>';

            $("#slug-panel table").prepend(buffer);
            $("#new-item-box").val('');
        }

    },

    addSlugPanelEvents : function() {

        //capture ENTER
        $("#new-item-box").keydown(function(event) {
            //donot submit form
            if(event.which == 13) {
                event.preventDefault();
                var itemInBox = jQuery.trim($("#new-item-box").val());
                if( itemInBox == '' ) {
                    return ;
                } else {
                    webgloo.sc.admin.addSlugPanelItems(itemInBox);
                }

            }

        });

        $("#add-item-btn").click(function(event) {
            event.preventDefault();
            var itemInBox = jQuery.trim($("#new-item-box").val());
            if( itemInBox == '' ) {
                return ;
            } else {
                webgloo.sc.admin.addSlugPanelItems(itemInBox);
            }

        });

        $('input:checkbox[id=page-checkbox]').click(function(event) {
            //@imp donot event.preventDefault();
            var checkBoxes =  $("#slug-panel").find("input:checkbox");
            var state =  $('input:checkbox[id=page-checkbox]').prop("checked");
            checkBoxes.prop("checked", state);
            
        });

    }

  
}

/* + webgloo media object */

webgloo.media = {
    images : {} ,
    debug : false,
    mode : ["image", "link"],

    init : function (mode) {

        //make a copy of mode array
        webgloo.media.mode = mode.slice(0) ;
        frm = document.forms["web-form1"];

        if(jQuery.inArray("image",webgloo.media.mode) != -1) {
            var strImagesJson = frm.images_json.value ;
            var images = JSON.parse(strImagesJson);
            for(i = 0 ;i < images.length ; i++) {
                webgloo.media.addImage(images[i]);
            }
        }

        if(jQuery.inArray("link",webgloo.media.mode) != -1) {
            var strLinksJson = frm.links_json.value ;
            var links = JSON.parse(strLinksJson);
            for(i = 0 ;i < links.length ; i++) {
                webgloo.media.addLink(links[i]);
            }
        }

    },

    attachEvents : function() {

        $("#add-link").live("click", function(event){
            event.preventDefault();
            var link = jQuery.trim($("#link-box").val());
            if( link == '' )
                return ;
            else
                webgloo.media.addLink(link);
        }) ;

        //capture ENTER on link box
        $("#link-box").keydown(function(event) {
            //donot submit form
            if(event.which == 13) {
                event.preventDefault();
                var link = jQuery.trim($("#link-box").val());
                if( link == '' )
                    return ;
                else
                    webgloo.media.addLink(link);
            }

        });

        $("a.remove-link").live("click", function(event){
            event.preventDefault();
            webgloo.media.removeLink($(this));
        }) ;

        $("a.remove-image").live("click", function(event){
            event.preventDefault();
            webgloo.media.removeImage($(this));
        }) ;

        $('#web-form1').submit(function() {
            webgloo.media.populateHidden();
            return true;
        });

    },

    imageDiv : '<div class="container" id="image-{id}"> '
        + ' <img src="{srcImage}" alt="{originalName}" width="{width}" height="{height}"/> '
        + '<div class="link"> <a class="remove-image" id="{id}" href="">Remove</a> </div> </div>',

    imageDiv2 : '<div class="container" id="image-{id}"><img src="{srcImage}" /> '
        + '<div class="link"> <a class="remove-image" id="{id}" href="">Remove</a> </div> </div>',

    linkPreviewDIV : '<div class="previewLink">{link}&nbsp;<a class="remove-link" href="{link}">Remove</a></div> ' ,

    populateHidden : function () {

        var frm = document.forms["web-form1"];

        if(jQuery.inArray("image",webgloo.media.mode) != -1) {
            var images = new Array() ;

            $("div#image-preview").find('a').each(function(index) {
                 var imageId = $(this).attr("id");
                 images.push(webgloo.media.images[imageId]);
            });

            var strImages =  JSON.stringify(images);
            frm.images_json.value = strImages ;
        }

        if(jQuery.inArray("link",webgloo.media.mode) != -1) {
            var links = new Array() ;

            $("div#link-preview").find('a').each(function(index) {
                links.push($(this).attr("href"));
            });

            //Anything in the box?
            var linkInBox = jQuery.trim($("#link-box").val());
            if( linkInBox != '') {
               links.push(linkInBox);
            }

            var strLinks = JSON.stringify(links);
            frm.links_json.value = strLinks ;
        }

    },

    addLink : function(linkData) {
        var buffer = webgloo.media.linkPreviewDIV.supplant({"link" : linkData});
        $("#link-preview").append(buffer);
        //clear out the box
        $("#link-box").val('');
        
    },

    removeLink : function(linkObj) {
        $(linkObj).parent().remove();
    },

    removeImage : function(linkObj) {
        var id = $(linkObj).attr("id");
        var imageId = "#image-" +id ;
        $("#image-"+id).remove();
    },

    addImage : function(mediaVO) {

        webgloo.media.images[mediaVO.id] = mediaVO ;
        switch(mediaVO.store) {

            case "s3" :
                mediaVO.srcImage = 'http://' + mediaVO.bucket + '/' + mediaVO.thumbnail ;
                var buffer = webgloo.media.imageDiv.supplant(mediaVO);
                $("div#image-preview").append(buffer);
                break ;

            case "local" :
                mediaVO.srcImage = '/' + mediaVO.bucket + '/' + mediaVO.thumbnail ;
                var buffer = webgloo.media.imageDiv.supplant(mediaVO);
                $("div#image-preview").append(buffer);
                break ;

            default:
                break ;
        }
        
        // var position = $("#image-preview").offset();
        // scroll(0,position.top + 80);

    }
}

webgloo.sc.ImageSelector = {

    bucket : {},
    images : [],
    num_total: 0 ,
    num_added : 0 ,
    num_selected : 0 ,
    num_uploaded : 0 ,
    add_counter : 0 ,
    upload_counter : 0 ,
    debug : false ,
    description: '' ,
    website : '' ,

    extractEndpoint : "/qa/ajax/extract-image.php",
    uploadEndpoint : "/upload/image.php" ,
    nextEndpoint : "/qa/external/router.php" ,

    imageDiv : '<div id="image-{id}" class="container" >'
        + '<div class="options"> <div class="links"> </div> </div>'
        + '<img src="{srcImage}" /> </div>' ,

    addLink : '<a id="{id}" class="btn btn-mini add-image" href="">Select</a>' ,
    removeLink : '<i class="icon-ok"></i>&nbsp;&nbsp;'
        + '<a id="{id}" class="btn btn-mini remove-image" href="">Remove</a>' ,

    init:function() {
        //reset counters and buckets
        webgloo.sc.ImageSelector.num_total = 0 ;
        webgloo.sc.ImageSelector.num_added = 0 ;
        webgloo.sc.ImageSelector.add_counter = 0 ;
        webgloo.sc.ImageSelector.num_selected = 0 ;
        webgloo.sc.ImageSelector.num_uploaded = 0 ;
        webgloo.sc.ImageSelector.upload_counter = 0 ;
        webgloo.sc.ImageSelector.bucket = {} ;
        webgloo.sc.ImageSelector.images = [] ;
        webgloo.sc.ImageSelector.clearMessage();
    },

    attachEvents : function() {

        $('#image-preview .container .options').hide();
        $('#image-preview').hide();
        
        $("#fetch-button").live("click", function(event){
            event.preventDefault();
            $("#next-container").hide();
            var link = jQuery.trim($("#link-box").val());
            if( link == '' ){
                return ;
            } else {
                webgloo.sc.ImageSelector.fetch(link);
            }
        }) ;

        //capture ENTER on link box
        $("#link-box").keydown(function(event) {
            $("#next-container").hide();
            //donot submit form
            if(event.which == 13) {
                event.preventDefault();
                var link = jQuery.trim($("#link-box").val());
                if( link == '' ) {
                    return ;
                } else{
                    webgloo.sc.ImageSelector.fetch(link);
                }
            }

        });

        $('#next-button').live("click",function() {

            //initialize
            webgloo.sc.ImageSelector.clearMessage();
            webgloo.sc.ImageSelector.num_uploaded = 0  ;
            webgloo.sc.ImageSelector.upload_counter = 0  ;
            webgloo.sc.ImageSelector.images = new Array();

            if(webgloo.sc.ImageSelector.debug) {
                console.log("num_selected :: " + webgloo.sc.ImageSelector.num_selected);
            }

            if(webgloo.sc.ImageSelector.num_selected == 0 ) {
                webgloo.sc.ImageSelector.appendError("Please select an image first.");
                return false;

            } else {

                var tmpl = "uploading {total} images " ;
                var message = tmpl.supplant({"total" : webgloo.sc.ImageSelector.num_selected});
                webgloo.sc.ImageSelector.appendMessage(message,{});

                var spinner = '<div> <img src="/css/asset/sc/fb_loader.gif" alt="spinner"/></div>' ;
                webgloo.sc.ImageSelector.appendMessage(spinner,{});

                $("#image-preview").find('.container').each(function(index) {

                    var imageId = $(this).attr("id") ,
                    ids = imageId.split('-') ,
                    realId = ids[1] ,
                    imageObj = webgloo.sc.ImageSelector.bucket[realId] ;


                    if(imageObj.selected) {

                        $.ajaxQueue({
                            url: webgloo.sc.ImageSelector.uploadEndpoint ,
                            type: "POST",
                            dataType: "json",
                            data :  {"qqUrl" : imageObj.srcImage } ,
                            timeout: 9000,
                            processData:true,

                            error: function(XMLHttpRequest, response){
                                webgloo.sc.ImageSelector.processUploadError(response);
                            },

                            success: function(response){

                                if(webgloo.sc.ImageSelector.debug) {
                                    console.log("upload response for image :: " + imageObj.srcImage);
                                    console.log(response);
                                }

                                switch(response.code) {
                                    case 401 :
                                        webgloo.sc.ImageSelector.processUploadError(response.message);
                                    break ;

                                    case 200 :
                                        webgloo.sc.ImageSelector.processUpload(response);
                                    break ;

                                    default:
                                        webgloo.sc.ImageSelector.processUploadError(response.message);
                                    break ;
                                }
                            }

                        }); //ajax call end

                    }

                });  //each

                webgloo.sc.ImageSelector.removeSpinner();

            } //num_selected > 0

        });

        $('#image-preview .container').live("mouseenter",function() {
            //will get image-1, image-2 etc.
            var imageId = $(this).attr("id");
            //will split into image and 1
            var ids = imageId.split('-');
            var realId = ids[1] ;
            var imageObj = webgloo.sc.ImageSelector.bucket[realId] ;

            if(!imageObj.selected) {
                // show select button
                var buffer = webgloo.sc.ImageSelector.addLink.supplant({"id": realId } );
                $(this).find(".options .links").html(buffer);
            }

            $(this).find(".options").show();
        });

        $('#image-preview .container').live("mouseleave", function() {
            //will get image-1, image-2 etc.
            var imageId = $(this).attr("id");
            //will split into image and 1
            var ids = imageId.split('-');
            var realId = ids[1] ;
            var imageObj = webgloo.sc.ImageSelector.bucket[realId] ;

            //if this image is selected?
            if(!imageObj.selected) {
                $(this).find(".options").hide();
            }

        });

        $('.add-image').live("click", function(event) {
            event.preventDefault();
            var realId = $(this).attr("id");
            var imageId = "#image-" + realId ;

            var imageObj = webgloo.sc.ImageSelector.bucket[realId] ;
            //change selected state for imageObj
            imageObj.selected = true ;
            webgloo.sc.ImageSelector.bucket.realId = imageObj ;
            webgloo.sc.ImageSelector.num_selected++ ;

            // change display
            var buffer = webgloo.sc.ImageSelector.removeLink.supplant({"id":realId } );
            $(imageId).find(".options .links").html(buffer);

        });

        $('.remove-image').live("click", function(event) {
            event.preventDefault();
            var realId = $(this).attr("id");
            var imageId = "#image-" + realId ;

            var imageObj = webgloo.sc.ImageSelector.bucket[realId] ;
            //change selected state for imageObj
            imageObj.selected = false ;
            webgloo.sc.ImageSelector.bucket.realId = imageObj ;
            webgloo.sc.ImageSelector.num_selected-- ;
            $(imageId).find('.options').hide();
         });

    },

    appendMessage : function(message) {
        $("#ajax-message").append('<div class="normal"> ' + message + '</div>');
        $("#ajax-message").show();
    },

    appendError : function(message) {
        $("#ajax-message").append('<div class="error"> ' + message + '</div>');
        $("#ajax-message").show();
    },

    clearMessage : function() {
        $("#ajax-message").html('');
    },

    showMessage : function(message) {
        $("#ajax-message").html('');
        $("#ajax-message").html('<div class="normal">' + message + '</div>');
        $("#ajax-message").show();
    },

    showError : function(message) {
        $("#ajax-message").html('');
        $("#ajax-message").html('<div class="error">' + message + '</div>');
        $("#ajax-message").show();
    },

    loadImage : function(img) {
        if((img.width > 400) && (img.height > 200 )) {
            webgloo.sc.ImageSelector.addImage(img.src);
        }

    },

    makeLoadImage : function(img) {
        return function() {
            webgloo.sc.ImageSelector.loadImage(img);
        };
    },

    showNextButton : function() {

        if(webgloo.sc.ImageSelector.debug) {
            console.log("show_next_button with num_added= " + webgloo.sc.ImageSelector.num_added);
        }

        if(webgloo.sc.ImageSelector.num_added > 0 ) {
            $("#next-container").fadeIn("slow");
        }else {
            var message = "Error: No suitable images found";
            webgloo.sc.ImageSelector.showError(message);
        }

    },

    addImage : function(image) {

        var index = webgloo.sc.ImageSelector.num_added ;

        if(webgloo.sc.ImageSelector.debug) {
            console.log("Adding image : " + index + " : " + image);
        }

        var buffer = this.imageDiv.supplant({"srcImage":image, "id":index } );
        //logo, small icons etc. are first images in a page
        // what we are interested in will only come later.
        $("#image-preview").prepend(buffer);

        this.bucket[index] = { "id":index, "srcImage": image, "selected" : false} ;
        webgloo.sc.ImageSelector.num_added++ ;
    },

    processUrlFetch : function(response) {
        var images = response.images ;
        webgloo.sc.ImageSelector.num_total = images.length;
        webgloo.sc.ImageSelector.description = response.description;
        webgloo.sc.ImageSelector.website = $("#link-box").val();

        var tmpl1, message ;

        tmpl1 = " {total} images found.&nbsp;&nbsp;Place your mouse over an image to select it. "
            + "&nbsp;&nbsp;Click Next after selecting images." ;

        
        if(webgloo.sc.ImageSelector.num_total > 0 ) {
            message = tmpl1.supplant({"total" : webgloo.sc.ImageSelector.num_total}) ;
        } else {
            message = "No images found!" ;
        }
        
        webgloo.sc.ImageSelector.showMessage(message);

        for(i = 0 ; i < images.length ; i++) {
            var img = new Image();

            // @warning closure inside a loop
            // do not use outer function variables.
            img.onload = function() {
                webgloo.sc.ImageSelector.add_counter++ ;
                if(this.width == 0 || this.height == 0 ) {
                    thisonerror();
                }

                if((this.width > 100) && (this.height > 100 )) {
                    webgloo.sc.ImageSelector.addImage(this.src);
                }

                if(webgloo.sc.ImageSelector.add_counter == webgloo.sc.ImageSelector.num_total) {
                    webgloo.sc.ImageSelector.showNextButton();
                }
            }

            img.onerror = function() {
                webgloo.sc.ImageSelector.add_counter++ ;
                if(webgloo.sc.ImageSelector.add_counter == webgloo.sc.ImageSelector.num_total) {
                    webgloo.sc.ImageSelector.showNextButton();
                }
            }

            img.onabort = function() {
                webgloo.sc.ImageSelector.add_counter++ ;
                if(webgloo.sc.ImageSelector.add_counter == webgloo.sc.ImageSelector.num_total) {
                    webgloo.sc.ImageSelector.showNextButton();
                }
            }

            img.src = images[i] ;
        }

        $("#image-preview").fadeIn("slow");

    },

    fetch : function(target) {

        webgloo.sc.ImageSelector.init();

        var message = "fetching images from webpage..." ;
        var spinner = '<div> <img src="/css/asset/sc/fb_loader.gif" alt="spinner"/></div>' ;

        webgloo.sc.ImageSelector.clearMessage();
        webgloo.sc.ImageSelector.appendMessage(message,{});
        webgloo.sc.ImageSelector.appendMessage(spinner,{});

        $("#image-preview").fadeOut("slow");
        $("#image-preview").html('');

        var endPoint = webgloo.sc.ImageSelector.extractEndpoint ;
        params = {} ;
        params.target = target ;
        //ajax call start
        $.ajax({
            url: endPoint,
            type: "POST",
            dataType: "json",
            data :  params,
            timeout: 18000,
            processData:true,
            //js errors callback
            error: function(XMLHttpRequest, response){
                webgloo.sc.ImageSelector.showError(response);
            },

            // server script errors are also reported inside
            // ajax success callback
            success: function(response){
                if(webgloo.sc.ImageSelector.debug) {
                    console.log("server response for image fetch :: ") ;
                    console.log(response);
                }

                switch(response.code) {
                    case 401 :
                        webgloo.sc.ImageSelector.showError(response.message);
                        break ;

                    case 200 :
                        webgloo.sc.ImageSelector.processUrlFetch(response);
                        break ;

                    default:
                        webgloo.sc.ImageSelector.showError(response.message);
                        break ;
                }
            }

        }); //ajax call end


    },

    processUploadError : function(response) {

        webgloo.sc.ImageSelector.upload_counter++;
        var tmpl = " Error uploading image - {counter} : {message} " ;
        var message = tmpl.supplant({"counter":webgloo.sc.ImageSelector.upload_counter, "message":response });
        webgloo.sc.ImageSelector.appendError(message);

        if(webgloo.sc.ImageSelector.debug) {
            console.log(message);
        }

        webgloo.sc.ImageSelector.populateFormData();

    },

    processUpload : function(response) {
        webgloo.sc.ImageSelector.upload_counter++;
        var tmpl = "image - {counter} : uploaded successfully. " ;
        var message = tmpl.supplant({"counter" : webgloo.sc.ImageSelector.upload_counter });
        webgloo.sc.ImageSelector.appendMessage(message);

        if(webgloo.sc.ImageSelector.debug) {
            console.log(message);
        }

        mediaVO = response.mediaVO ;
        webgloo.sc.ImageSelector.images.push(mediaVO);
        webgloo.sc.ImageSelector.num_uploaded++ ;
        webgloo.sc.ImageSelector.populateFormData();
    },

    populateFormData : function(){
        if(webgloo.sc.ImageSelector.upload_counter == webgloo.sc.ImageSelector.num_selected) {
            webgloo.sc.ImageSelector.clearMessage();
            //Actual upload?
            if(webgloo.sc.ImageSelector.num_uploaded > 0 ) {
                //stringify images
                var strImagesJson =  JSON.stringify(webgloo.sc.ImageSelector.images);
                //bind to form
                var frm = document.forms["web-form1"];
                frm.images_json.value = strImagesJson ;
                frm.description.value = webgloo.sc.ImageSelector.description ;
                frm.link.value = webgloo.sc.ImageSelector.website ;
                $('#web-form1').submit();
            }

        }
    }

}


webgloo.addDebug = function(message) {
    $("#js-debug").append(message);
    $("#js-debug").append("<br>");
    console.log(message);

};

webgloo.clearDebug = function(message) {
    $("#js-debug").html("");
};

