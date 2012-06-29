
/* + useful methods */

/* @see http://javascript.crockford.com/remedial.html for supplant */

String.prototype.supplant = function (o) {
    return this.replace(/{([^{}]*)}/g,
        function (a, b) {
            var r = o[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        });
};


/* JSON support for old browsers */
/* @see  https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/JSON  */

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
 * base64 encoding in javascript *
 * @see http://my.opera.com/Lex1/blog/fast-base64-encoding-and-test-results
 * @see https://github.com/operasoftware/
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
   }
}

webgloo.sc.home = {
    addTiles : function() {
        var $container = $('#tiles');
        $container.imagesLoaded(function(){
            $container.masonry({
                itemSelector : '.tile'
            });
        });

        //show options on hover
        $('.tile .options').hide();
        $('.tile').mouseenter(function() {$(this).find('.options').toggle();});
        $('.tile').mouseleave(function() {$(this).find('.options').toggle();});

        //Add item toolbar actions
        webgloo.sc.item.addActions();

    },
    addSmallTiles : function() {
        var $container = $('#tiles');
        $container.imagesLoaded(function(){
            $container.masonry({
                itemSelector : '.stamp'
            });
        });
    },
    addNavGroups : function() {
        //group browser
        $("a#nav-group-open").click(function(event) {
            event.preventDefault();
            $targetUrl= "/group/data/featured.php";
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.load($targetUrl);
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
    },
    close : function() {
        this.addContent('');
        $("#simple-popup").hide();
    },

    addContent : function(content) {
        this.removeSpinner();
        $("#simple-popup #content").html('');
        $("#simple-popup #content").html(content);
        $("#simple-popup").show();
    },
    addSpinner : function() {
        this.close();
        $("#block-spinner").html('');
        var content = '<img src="/css/images/ajax_loader.gif" alt="loading ..." />' ;
        $("#block-spinner").html(content);
        $("#block-spinner").show();
    },
    removeSpinner : function() {
        $("#block-spinner").html('');
        $("#block-spinner").hide();

    },
    processJson : function(response,options,dataObj) {
        console.log("keepOpen option :: " + options.keepOpen);
        console.log("reload option :: " + options.reload);

        switch(response.code) {
            case 200 :
                //success
                if(options.keepOpen){
                    this.addContent(response.message);
                }
                if(options.reload){
                    window.location.reload(true);
                }

                break;

            case 401:
                // authentication failure
                // redirect to login page with pending session action

                // @imp  dataObj.params should be an object containing simple
                // key value pairs. Params keys or values can again be objects
                // but it is better to avoid that complexity.
                // on the other end, this params object will be passed as it is to
                // PHP http_build_query method.

                dataObj.qUrl = window.location.href;
                dataObj.method = "POST";

                g_action_data =  encodeBase64(JSON.stringify(dataObj));
                //encode for use in URL query string
                qUrl = encodeURIComponent(window.location.href);
                gotoUrl = '/user/login.php?q='+qUrl + '&g_session_action=' + g_action_data;

                window.location.replace(gotoUrl);
                break;
            case 500:
                //error - keep open
                this.addContent(response.message);
                break;
            default:
                this.addContent(response.message);
                break;
        }
    },
    post:function (dataObj,options) {

        //@todo deal with undefined or NULL options
        //show spinner
        this.addSpinner();

        options.keepOpen = (typeof options.keepOpen === "undefined") ? true : options.keepOpen;
        options.reload = (typeof options.reload === "undefined") ? false : options.reload;
        options.type = (typeof options.type === "undefined") ? "POST" :  options.type ;
        options.dataType = (typeof options.dataType === "undefined") ? "text" :  options.dataType ;

        //ajax call start
        $.ajax({
            url: dataObj.endPoint,
            type: options.type ,
            dataType: options.dataType,
            data :  dataObj.params,
            timeout: 9000,
            processData:true,
            //js errors callback
            error: function(XMLHttpRequest, response){
                //remove spinner
                webgloo.sc.SimplePopup.removeSpinner();
                webgloo.sc.SimplePopup.addContent(response);
            },
            //server script errors are reported inside success callback
            success: function(response){
                webgloo.sc.SimplePopup.removeSpinner();
                switch(options.dataType) {
                    case 'json' :
                        webgloo.sc.SimplePopup.processJson(response,options,dataObj);
                        break;
                     default:
                        webgloo.sc.SimplePopup.addContent(response);
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
                "keepOpen" : true});

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
            dataObj.endPoint = "/monitor/ajax/feature.php";

            //open popup
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.post(dataObj,{"dataType" : "json"});
        }) ;

        //unfeature posts
        $("a.unfeature-post-link").click(function(event){
            event.preventDefault();

            var dataObj = {} ;
            dataObj.params = {} ;
            dataObj.params.postId  = $(this).attr("id");
            dataObj.params.action = "REMOVE" ;
            dataObj.endPoint = "/monitor/ajax/feature.php";

            //open popup
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.post(dataObj,{"dataType" : "json"});
        }) ;

    },
    addActions : function() {
        //add like & save callbacks
        $("a.like-post-link").click(function(event){
            event.preventDefault();

            var dataObj = {} ;
            dataObj.params = {} ;
            dataObj.params.itemId  = $(this).attr("id");
            dataObj.params.action = "LIKE" ;
            dataObj.endPoint = "/qa/ajax/bookmark.php";

            //open popup
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.post(dataObj,{"dataType" : "json"});
        }) ;

        $("a.favorite-post-link").click(function(event){
            event.preventDefault();

            var dataObj = {} ;
            dataObj.params = {} ;
            dataObj.params.itemId  = $(this).attr("id");
            dataObj.params.action = "SAVE" ;
            dataObj.endPoint = "/qa/ajax/bookmark.php";

            //open popup
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.post(dataObj,{"dataType" : "json"});
        }) ;

        //unfavorite
        $("a.remove-post-link").click(function(event){
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
                "keepOpen" : false});
            //reload page

        }) ;

        $("a.follow-user-link").click(function(event){
            event.preventDefault();

            var id = $(this).attr("id");
            //parse id to get follower and following
            var ids = id.split('|');
            //u1 -> u2

            var dataObj = {} ;
            dataObj.params = {} ;
            dataObj.params.followerId  = ids[0] ;
            dataObj.params.followingId  = ids[1] ;
            dataObj.params.action = "FOLLOW" ;
            dataObj.endPoint = "/qa/ajax/social-graph.php";

            //open popup
            webgloo.sc.SimplePopup.init();
            webgloo.sc.SimplePopup.post(dataObj,{"dataType" : "json"});
        }) ;

        $("a.unfollow-user-link").click(function(event){

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
                    "keepOpen" : false
                }
            );


        }) ;

    }
}

webgloo.sc.groups = {
    addPanelEvents : function() {
        $("#add-group-btn").click(function(event) {
            event.preventDefault();
            var group = jQuery.trim($("#group-box").val());
            if( group == '' ) {return ;}
            //split on commas
            var tokens = group.split(",");
            for (var i = 0; i < tokens.length; i++) {
               var token = jQuery.trim(tokens[i]);
               if(token == '') continue ;
                var node = ' <li> <input type="checkbox" name="g[]" checked ="checked" value="' + token + '"/>' + token + '</li> ' ;
                //new groups are added to first panel
                $(".group-panel .wrapper ul:first").append(node);
            }

            $("#group-box").val('');

        });

        $("a#uncheck-all-groups").click(function(event) {
            event.preventDefault();
            $(".group-panel").find(":checkbox").removeAttr("checked");
        });

        $("a#check-all-groups").click(function(event) {
            event.preventDefault();
            $(".group-panel").find(":checkbox").attr("checked","checked");
        });

    },
    addCloudBox : function() {
        $(".fancy-box").fancybox({
            'type':'iframe',
            'width' : '75%',
            'height' : '75%'
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
    imagePreviewDIV : '<div class="stackImage" id="image-{id}"><img src="{srcImage}" class="thumbnail-1" alt="{originalName}" width="{width}" height="{height}"/> '
        + '<div> <a class="remove-image" id="{id}" href="">Remove</a> </div> </div>',

    linkPreviewDIV : '<div class="previewLink"> {link} &nbsp; <a class="remove-link" href="{link}"> Remove</a> </div> ' ,

    populateHidden : function () {

        frm = document.forms["web-form1"];

        if(jQuery.inArray("image",webgloo.media.mode) != -1) {
            var images = new Array() ;

            $("div#image-data").find('a').each(function(index) {
                 var imageId = $(this).attr("id");
                 images.push(webgloo.media.images[imageId]);
            });

            var strImages =  JSON.stringify(images);
            frm.images_json.value = strImages ;
        }

        if(jQuery.inArray("link",webgloo.media.mode) != -1) {
            var links = new Array() ;

            $("div#link-data").find('a').each(function(index) {
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
        $("#link-data").append(buffer);
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
        //console.log(mediaVO);
        webgloo.media.images[mediaVO.id] = mediaVO ;
        if(mediaVO.store == 's3'){
            mediaVO.srcImage = 'http://' + mediaVO.bucket + '/' + mediaVO.thumbnail ;
        } else {
            mediaVO.srcImage = '/' + mediaVO.bucket + '/' + mediaVO.thumbnail ;
        }

        var buffer = webgloo.media.imagePreviewDIV.supplant(mediaVO);
        $("div#image-data").append(buffer);

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

