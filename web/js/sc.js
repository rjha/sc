
/* + useful methods */

/* @see http://javascript.crockford.com/remedial.html for supplant */

String.prototype.supplant = function (o) {
    return this.replace(/{([^{}]*)}/g,
        function (a, b) {
            var r = o[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        });
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
        $('.tile').mouseenter(function() { $(this).find('.options').toggle(); });
        $('.tile').mouseleave(function() { $(this).find('.options').toggle(); }); 

    },
    addNavGroups : function() {
        //group browser
        $("a#nav-group-open").click(function(event) {
            event.preventDefault();
            $("#nav-group-browser").toggle();
        });

        $("a#nav-group-close").click(function(event) {
            event.preventDefault();
            $("#nav-group-browser").toggle();
        });
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
	    console.log(mediaVO);	
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

