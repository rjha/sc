
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
        parse: function (sJSON) { return eval("(" + sJSON + ")"); },
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
        $('.tile').mouseenter(function() { $(this).find('.options').toggle(); });
        $('.tile').mouseleave(function() { $(this).find('.options').toggle(); }); 

        //Add like + save events
        webgloo.sc.item.addLike();
        webgloo.sc.item.addSave();
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
            var options = {};
            webgloo.sc.SimplePopup.init(options);
            webgloo.sc.SimplePopup.load($targetUrl);
        });
    }
}

/* +simple popup object */
webgloo.sc.SimplePopup = {

    options : {},
    init : function(options) { 
        this.options.dataType = options.dataType || "text";
        this.options.type = options.type || "POST";

        $(document).bind('keydown', function(e) { 
            if (e.keyCode == 27) {
                webgloo.sc.SimplePopup.close();
            }
        }); 

        $("a#simple-popup-close").click(function(event) {
            event.preventDefault();
            webgloo.sc.SimplePopup.close();
        });
        webgloo.sc.SimplePopup.show();
    },
    show : function () {
        $("#simple-popup").show();
    },
    hide : function () {
        $("#simple-popup").hide();
    },
    close : function() {
        webgloo.sc.SimplePopup.addContent('');
        $("#simple-popup").hide();
    },
    
    addContent : function(content) {
        $("#simple-popup #content").html('');
        $("#simple-popup #content").html(content);
        $("#simple-popup").show();
    },
    addJsonContent : function(response) {
        switch(response.code) {
            case 200:
                webgloo.sc.SimplePopup.close();
                break;
            case 401:
                //redirect to login page
                qUrl = window.location.href;
                gotoUrl = '/user/login.php?q='+qUrl;
                window.location.replace(gotoUrl);
                break;
            case 500:
                //error - keep open
                webgloo.sc.SimplePopup.addContent(response.message);
                break;
            default:
                webgloo.sc.SimplePopup.addContent(response.message);
        }
    },
    post:function (targetUrl,dataObj) {
        //show spinner
        webgloo.sc.SimplePopup.addContent('<img src="/css/images/ajax_loader.gif" alt="spinner" />');

        //ajax call start
        $.ajax({
            url: targetUrl,
            type: webgloo.sc.SimplePopup.options.type,
            dataType: webgloo.sc.SimplePopup.options.dataType,
            data : dataObj,
            timeout: 9000,
            //js errors callback
            error: function(XMLHttpRequest, response){
                webgloo.sc.SimplePopup.addContent(response);
            },
            //server script errors are reported inside success callback
            success: function(response){
                switch(webgloo.sc.SimplePopup.options.dataType) {
                    case 'json' :
                        webgloo.sc.SimplePopup.addJsonContent(response);
                        break;
                     default:
                        webgloo.sc.SimplePopup.addContent(response);
                        break;
                }

            }
        }); //ajax call end
    },
    load: function(targetUrl) {
        var dataObj = {};
        this.post(targetUrl,dataObj);
    }
}

webgloo.sc.item = {
    addLike : function() {
        //add like & save callbacks
        $("a.like-post-link").click(function(event){
            event.preventDefault();
            var dataObj = {}
            dataObj.postId  = $(this).attr("id");
            dataObj.action = "LIKE" ;
            var targetUrl = "/qa/ajax/bookmark.php";
            //open popup
            var options = {};
            options.dataType = "json" ;
            webgloo.sc.SimplePopup.init(options);
            webgloo.sc.SimplePopup.post(targetUrl,dataObj);
        }) ;
    },
    addSave : function() {
        $("a.save-post-link").click(function(event){
            event.preventDefault();
            var dataObj = {}
            dataObj.postId  = $(this).attr("id");
            dataObj.action = "SAVE" ;
            var targetUrl = "/qa/ajax/bookmark.php";
            //open popup
            var options = {};
            options.dataType = "json" ;
            webgloo.sc.SimplePopup.init(options);
            webgloo.sc.SimplePopup.post(targetUrl,dataObj);
        }) ;
    }
}

webgloo.sc.groups = {
    addPanelEvents : function() {
        $("#add-group-btn").click(function(event) {
            event.preventDefault(); 
            var group = jQuery.trim($("#group-box").val());
			if( group == '' ) {return ; }
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
			if( link == '' ) {return ; }
			else {
				webgloo.media.addLink(link);

			}
		 	
        }) ;
        
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

