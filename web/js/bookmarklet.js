function isIE() {
    return /msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent)
}
function isSafari() {
    return /Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent)
}
function isIOS() {
    return navigator.userAgent.match(/iPad/i) != null || navigator.userAgent.match(/iPhone/i) != null || navigator.userAgent.match(/iPod/i) != null || navigator.userAgent.match(/iPod/i) != null
}(function () {
    function D() {
        var m = window,
            h = document;
        return ("" + (m.getSelection ? m.getSelection() : h.getSelection ? h.getSelection() : h.selection.createRange().text)).replace(/(^\s+|\s+$)/g, "")
    }
    function ad(x) {
        var n = "http://www.snatchly.com/snatches/create/bookmarklet/";
        var w = "http://www.snatchly.com/snatches/create/bookmarklet/";
        var h = (x.src == location.href) ? document.referrer || location.href : location.href;
        if (isSafari()) {
            h = encodeURI(h)
        }
        var aK = 0;
        var q = 0;
        if (!isNaN(x.h)) {
            aK = x.h
        }
        if (!isNaN(x.w)) {
            q = x.w
        }
        h = {
            media: x.src,
            url: h,
            alt: x.alt,
            title: document.title,
            is_video: x.type == "video",
            height: aK,
            width: q
        };
        if (E) {
            h.description = E
        }
        var o = [];
        o.push(n);
        o.push("?");
        for (var aI in h) {
            o.push(encodeURIComponent(aI));
            o.push("=");
            o.push(encodeURIComponent(h[aI]));
            o.push("&")
        }
        var m = [];
        m.push(w);
        m.push("?");
        for (var aI in h) {
            m.push(encodeURIComponent(aI));
            m.push("=");
            m.push(encodeURIComponent(h[aI]));
            m.push("&")
        }
        var s = m.join("");
        var aJ = o.join("");
        if (isIOS()) {
            setTimeout(function () {
                window.location = aJ
            }, 25);
            window.location = s
        } else {
            window.open(aJ, "snatch" + (new Date).getTime(), "status=no,resizable=no,scrollbars=yes,personalbar=no,directories=no,location=no,toolbar=no,menubar=no,width=620,height=760,left=0,top=0")
        }
    }
    function g(h) {
        if (Math.max(h.h, h.w) > 219) {
            if (h.h < h.w) {
                return "margin-top: " + parseInt(80 - 100 * (h.h / h.w)) + "px;"
            }
            return ""
        } else {
            return "margin-top: " + parseInt(80 - h.h / 2) + "px;"
        }
    }
    var R = [];
    if (!document.snatchlyLjs) {
        document.snatchlyLjs = 1;
        var l = /^https?:\/\/.*?\.?facebook\.com\//,
            ai = /^https?:\/\/.*?\.?google\.com\/reader\//,
            ae = /^https?:\/\/.*?\.?youjizz\.com\/videos\//,
            Q = /^https?:\/\/.*?\.?redtube\.com\/\d+/,
            c = /^https?:\/\/.*?\.?youporn\.com\/watch\//,
            aF = /^https?:\/\/.*?\.?pornhub\.com\/view_video/,
            t = /^https?:\/\/.*?\.?xtube\.com\/(watch|amateur_channels)/,
            L = /^https?:\/\/.*?\.?xhamster\.com\/movies\//,
            K = /^https?:\/\/.*?\.?tube8\.com.*?\/\d+(\/|\?)/,
            av = /^https?:\/\/.*?\.?vidz\.com.*?s=\d+/,
            O = /^https?:\/\/beeg\.com\/\d+/,
            ab = /^https?:\/\/.*?\.?spankwire.com.*?\/video(\d+)/,
            X = /^https?:\/\/.*?\.?xnxx\.com\/video\d+\//,
            b = /^https?:\/\/.*?\.?eporner\.com\/hd-porn\/\d+\//,
            az = /^https?:\/\/.*?\.?fapdu\.com\/[-\w#]+$/,
            W = /^https?:\/\/.*?\.?bigstar\.tv\/movie\//,
            H = /^https?:\/\/.*?\.?porntube\.com\/videos\//,
            A = /^https?:\/\/.*?\.?mastishare\.com\/video\//,
            P = /^https?:\/\/.*?\.?cliphunter\.com\/w\//,
            v = /^https?:\/\/.*?\.?3d-porntube\.net\/.*?\.html/,
            U = /^https?:\/\/.*?\.?alphaporno\.com\/videos\//,
            aE = /^https?:\/\/.*?\.?hardsextube\.com\/video\//,
            ar = /^https?:\/\/.*?\.?fuckingmotherfucker\.com\/.*?\.html/,
            a = /^https?:\/\/.*?\.?moofmoof\.com\/\d+\/\w+/,
            k = /^https?:\/\/.*?\.?madthumbs\.com\/videos\//,
            aH = /^https?:\/\/.*?\.?xvideos\.com\/video\d+(\/|\?)/;
        if (location.href.match(/^https?:\/\/.*?\.?snatchly\.com\//)) {
            window.alert("The bookmarklet is installed! Now you can click your Snatch It button to snatch videos and images as you browse sites around the web.")
        } else {
            if (location.href.match(l)) {
                window.alert("The bookmarklet can't snatch images directly from Facebook. Sorry about that.")
            } else {
                if (location.href.match(ai)) {
                    window.alert("The bookmarklet can't snatch images directly from Google Reader. Sorry about that.")
                } else {
                    if (location.href.match(ae) || location.href.match(Q) || location.href.match(c) || location.href.match(aF) || location.href.match(t) || location.href.match(L) || location.href.match(K) || location.href.match(av) || location.href.match(O) || location.href.match(ab) || location.href.match(X) || location.href.match(b) || location.href.match(az) || location.href.match(W) || location.href.match(H) || location.href.match(A) || location.href.match(P) || location.href.match(v) || location.href.match(U) || location.href.match(aE) || location.href.match(ar) || location.href.match(a) || location.href.match(k) || location.href.match(aH)) {
                        close_overlay = function () {
                            aj.parentNode.removeChild(aj);
                            af.parentNode.removeChild(af);
                            document.snatchlyLjs = 0;
                            if (isIE()) {
                                for (var h = 0; h < R.length; h++) {
                                    R[h].parent.insertBefore(R[h].player, R[h].sibling);
                                    return false
                                }
                            }
                        };
                        var aa = window;
                        if (location.href.match(av)) {
                            var ay = document.getElementsByTagName("ul");
                            var Y;
                            for (list_index in ay) {
                                if (ay[list_index].className.indexOf("playerpage_pic_list") > -1) {
                                    Y = ay[list_index];
                                    break
                                }
                            }
                            var ac = Y.getElementsByTagName("img");
                            aa.src = ac[ac.length - 1].src
                        } else {
                            if (location.href.match(aF)) {
                                var S = document.getElementById("video_1");
                                var aC = S.value;
                                while (aC.length < 9) {
                                    aC = "0" + aC
                                }
                                aa.src = "http://cdn1.image.pornhub.phncdn.com/thumbs/" + aC.substring(0, 3) + "/" + aC.substring(3, 6) + "/" + aC.substring(6, 9) + "/small2.jpg"
                            } else {
                                if (location.href.match(K)) {
                                    var C = /flashvars.*?"image_url":"(.*?)"/;
                                    var ag = C.exec(document.body.innerHTML);
                                    if (ag) {
                                        aa.src = ag[1]
                                    }
                                } else {
                                    if (location.href.match(X)) {
                                        var ak = document.getElementsByTagName("img");
                                        var T = 0;
                                        for (T in ak) {
                                            if (ak[T].width == 120 && ak[T].height == 90) {
                                                aa.src = ak[T].src;
                                                break
                                            }
                                        }
                                    } else {
                                        if (location.href.match(aH)) {
                                            var aA = document.getElementsByTagName("img");
                                            var ao = 0;
                                            for (ao in aA) {
                                                if (aA[ao].width == 120 && aA[ao].height == 90) {
                                                    aa.src = aA[ao].src;
                                                    break
                                                }
                                            }
                                        } else {
                                            if (location.href.match(c)) {
                                                var u = document.getElementById("tab-thumbnails");
                                                var e = u.getElementsByTagName("img");
                                                aa.src = e[0].src
                                            } else {
                                                if (location.href.match(b)) {
                                                    var r = document.getElementById("cutscenes");
                                                    var aB = r.getElementsByTagName("img");
                                                    aa.src = aB[0].src
                                                } else {
                                                    if (location.href.match(az)) {
                                                        var d = /link\s*rel="image_src"\s*href="(.*?)"/;
                                                        var ag = d.exec(document.head.innerHTML);
                                                        if (ag) {
                                                            aa.src = ag[1]
                                                        }
                                                    } else {
                                                        if (location.href.match(W)) {
                                                            var an = /(http:\/\/assets\d+\.bigstar\.tv\/content\/movies\/disk\d+\/media\/\d+\/thumbnails\/.*?\.jpg)/;
                                                            var ag = an.exec(document.body.innerHTML);
                                                            if (ag) {
                                                                aa.src = ag[1]
                                                            }
                                                        } else {
                                                            if (location.href.match(P)) {
                                                                var aG = /link.*?href="(.*?)"/;
                                                                var ag = aG.exec(document.head.innerHTML);
                                                                if (ag) {
                                                                    aa.src = ag[1]
                                                                }
                                                            } else {
                                                                if (location.href.match(U)) {
                                                                    var aD = /param\s*name="flashvars".*?preview_url=(http:\/\/contents\.alphaporno\.com\/videos_screenshots\/.*?\/preview\.jpg)/;
                                                                    var ag = aD.exec(document.body.innerHTML);
                                                                    if (ag) {
                                                                        aa.src = ag[1]
                                                                    }
                                                                } else {
                                                                    if (location.href.match(aE)) {
                                                                        var M = /startimg:\s*"(.*?)"/;
                                                                        var ag = M.exec(document.body.innerHTML);
                                                                        if (ag) {
                                                                            aa.src = ag[1]
                                                                        }
                                                                    } else {
                                                                        if (location.href.match(a)) {
                                                                            var j = document.getElementsByName("video_main_thumb")[0].value;
                                                                            var F = document.getElementsByName("video_thumbs")[0].value;
                                                                            var V = document.getElementsByName("video_thumbs_prefix_url")[0].value;
                                                                            aa.src = V + j + "?thumbs=[" + j + F + "]"
                                                                        } else {
                                                                            if (location.href.match(k)) {
                                                                                var Z = /property="og:image"\s*?content="(.*?)"/;
                                                                                var ag = Z.exec(document.head.innerHTML);
                                                                                if (ag) {
                                                                                    aa.src = ag[1]
                                                                                }
                                                                            } else {
                                                                                var au = ab.exec(location.href);
                                                                                if (au) {
                                                                                    var ax = au[1];
                                                                                    var am = new RegExp("http://cdn(\\d+).public.spankwire.phncdn.com/(.*?/" + ax + ")");
                                                                                    var ag = am.exec(document.body.innerHTML);
                                                                                    if (ag) {
                                                                                        var p = "http://cdn" + ag[1] + ".image.spankwire.phncdn.com/" + ag[2] + "/177X129/3.jpg";
                                                                                        aa.src = p
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        var B = "\n#snatchlyContainer {font-family: Helvetica, arial, sans-serif; position: absolute; padding-top: 37px; z-index: 100000002; top: 0; left: 0; background-color: transparent; opacity: 1; width: 100%;}\n#snatchlyOverlay {position: fixed; z-index: 999999; top: 0; right: 0; bottom: 0; left: 0; background-color: #666; opacity: .83;}\n#snatchlyControl img {left: 50%; margin: 0 0 0 -101px; position: fixed; padding: 0; display: block; -ms-interpolation-mode: bicubic;}\n#snatchlyControl a{position: fixed; z-index: 10001; right: 0; left: 0; height: 24px; padding: 12px 0 0; text-align: center; font-size: 14px; line-height: 1em; text-shadow: 0 1px #fff; color: #333; font-weight: bold; text-decoration: none; background: #f6f6f6 url(http://snatchly-assets-uncompressed.s3.amazonaws.com/images/global/full-gradient.png) 50% 50% repeat-x; border-bottom: 1px solid #ccc; box-shadow: 0 0 2px #333; -mox-box-shadow: 0 0 2px #333; -webkit-box-shadow: 0 0 2px #333;}\n#snatchlyControl a#snatchlyRemoveLink {top: 180px;}\n#snatchlyControl a#snatchlyAddLink {top: 130px;}\n#snatchlyControl a:hover {color: #fff; text-decoration: none; background: #0073ea url(http://snatchly-assets-uncompressed.s3.amazonaws.com/images/global/hover-gradient.png) 50% 50% repeat-x; border-color: #0073ea; text-shadow: 0 -1px rgba(0, 92, 187, .5);}\n#snatchlyControl a:active {height: 23px; color: #ff0084; padding-top: 13px; background-color: #fff; border-color: #ccc; background-image: url(http://snatchly-assets-uncompressed.s3.amazonaws.com/images/global/full-gradient-inverted.png);text-shadow: 0 1px #fff;}\n.snatchly-image-preview {position: relative; padding: 0; margin: 0; float: left; background-color: #fff; border: solid #e7e7e7; border-width: 0 1px 1px 0; height: 200px; width: 200px; opacity: 1; z-index: 10002; text-align: center;}\n.snatchly-image-preview .snatchly-img {border: none; height: 200px; width: 200px; opacity: 1; padding: 0;}\n.snatchly-image-preview .snatchly-img a {margin: 0; padding: 0; position: absolute; top: 0; bottom: 0; right: 0; left: 0; display: block; text-align: center;  z-index: 1;}\n.snatchly-image-preview .snatchly-img a:hover {background-color: #fcf9f9; border: none;}\n.snatchly-image-preview .snatchly-img .image-to-snatch {max-height: 200px; max-width: 200px; width: auto !important; height: auto !important;}\n.snatchly-image-preview img.snatchly-snatchit {border: none; position: absolute; top: 82px; left: 42px; display: none; padding: 0; background-color: transparent; z-index: 100;}\n.snatchly-image-preview img.snatchly_vidind {border: none; position: absolute; top: 75px; left: 75px; padding: 0; background-color: transparent; z-index: 99;}\n.snatchly-dimensions { position: relative; margin-top: 180px; text-align: center; font-size: 10px; z-index:10003; display: inline-block; background: white; border-radius: 4px; padding: 0 2px;}\n\n";
                        var at = document.createElement("style");
                        if (isIE()) {
                            at.type = "text/css";
                            at.media = "screen";
                            at.styleSheet.cssText = B;
                            document.getElementsByTagName("head")[0].appendChild(at)
                        } else {
                            if (navigator.userAgent.lastIndexOf("Safari/") > 0 && parseInt(navigator.userAgent.substr(navigator.userAgent.lastIndexOf("Safari/") + 7, 7)) < 533) {
                                at.innerText = "\n" + B + "\n"
                            } else {
                                at.innerHTML = "\n" + B + "\n"
                            }
                            document.body.appendChild(at)
                        }
                        var aj = document.createElement("div");
                        aj.setAttribute("id", "snatchlyOverlay");
                        document.keydown = close_overlay;
                        document.body.appendChild(aj);
                        var af = document.createElement("div");
                        af.setAttribute("id", "snatchlyContainer");
                        document.body.appendChild(af);
                        var al = document.createElement("div");
                        al.setAttribute("id", "snatchlyControl");
                        t_img = new Image;
                        t_img.src = "http://snatchly-assets-uncompressed.s3.amazonaws.com/images/global/snatchly-logo.png";
                        al.appendChild(t_img);
                        cancel_space = document.createElement("a");
                        cancel_space.href = "#";
                        cancel_space.id = "snatchlyRemoveLink";
                        cancel_space.appendChild(document.createTextNode("Cancel Snatch"));
                        al.appendChild(cancel_space);
                        add_space = document.createElement("a");
                        add_space.onclick = function () {
                            ad(aa);
                            aj.parentNode.removeChild(aj);
                            af.parentNode.removeChild(af);
                            document.snatchlyLjs = 0;
                            return false
                        };
                        add_space.href = "#";
                        add_space.id = "snatchlyAddLink";
                        add_space.appendChild(document.createTextNode("Snatch It"));
                        al.appendChild(add_space);
                        af.appendChild(al);
                        document.getElementById("snatchlyRemoveLink").onclick = close_overlay;
                        close_overlay = {}
                    } else {
                        var z = "http://www.snatchly.com/space/create/bookmarklet/",
                            y = "snatchit12://snatchly.com/snatches/create/bookmarklet/",
                            E = null;
                        if (D().length > 0) {
                            E = D()
                        }
                        var J = function () {
                            function q(aI) {
                                var x = new Image;
                                x.height = 360;
                                x.width = 480;
                                x.src = "http://img.youtube.com/vi/" + aI + "/0.jpg";
                                return o(x, "video")
                            }
                            function h(aJ) {
                                if (aJ.src && aJ.src != "") {
                                    var x = aJ.src.indexOf("?") > -1 ? "&" : "?";
                                    aJ.src += x + "autoplay=0";
                                    aJ.src += "&wmode=transparent"
                                }
                                aJ.setAttribute("wmode", "transparent");
                                x = aJ.parentNode;
                                var aI = aJ.nextSibling;
                                x.removeChild(aJ);
                                x.insertBefore(aJ, aI)
                            }
                            for (var s = [], o = function (aL, aI) {
                                aI = aI || "image";
                                var aK = aL.height,
                                    aM = aL.width,
                                    aJ = aL.src,
                                    x = new Image;
                                x.src = aJ;
                                return {
                                    w: aM,
                                    h: aK,
                                    src: aJ,
                                    img: aL,
                                    alt: "alt",
                                    im2: x,
                                    type: aI
                                }
                            }, w = document.getElementsByTagName("iframe"), n = 0; n < w.length; n++) {
                                var m = /^http:\/\/www\.youtube\.com\/embed\/([a-zA-Z0-9\-_]+)/;
                                if (m = m.exec(w[n].src)) {
                                    s.push(q(m[1]));
                                    h(w[n])
                                }
                            }
                            w = document.getElementsByTagName("embed");
                            for (n = 0; n < w.length; n++) {
                                m = /^http:\/\/www\.youtube\.com\/v\/([a-zA-Z0-9\-_]+)/;
                                if (m = m.exec(w[n].src)) {
                                    s.push(q(m[1]));
                                    h(w[n])
                                }
                            }
                            m = /^http:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9\-_]+)/;
                            if (m = m.exec(window.location.href)) {
                                s.push(q(m[1]));
                                h(document.getElementById("movie_player"))
                            }
                            for (n = 0; n < document.images.length; n++) {
                                w = document.images[n];
                                if (w.style.display != "none") {
                                    w = o(w);
                                    if (w.w > 159 && w.h > 159 && (w.h > 191 || w.w > 191)) {
                                        s.push(w)
                                    }
                                }
                            }
                            return s
                        }();
                        if (J.length == 0) {
                            window.alert("Sorry, we can't find any big images or videos on this page.");
                            document.snatchlyLjs = 0
                        } else {
                            i = function () {
                                G.parentNode.removeChild(G);
                                I.parentNode.removeChild(I);
                                document.snatchlyLjs = 0;
                                if (isIE()) {
                                    for (var h = 0; h < R.length; h++) {
                                        R[h].parent.insertBefore(R[h].player, R[h].sibling);
                                        return false
                                    }
                                }
                            };
                            var ap = "#snatchlyContainer {font-family: Helvetica, arial, sans-serif; position: absolute; padding-top: 37px; z-index: 100000002; top: 0; left: 0; background-color: transparent; opacity: 1;}\n#snatchlyOverlay {position: fixed; z-index: 999999; top: 0; right: 0; bottom: 0; left: 0; background-color: #f2f2f2; opacity: .94;}\n#snatchlyControl {position:relative; z-index: 100000; float: left; background-color: #f6f6f6; border: solid #ccc; border-width: 0 1px 1px 0; height: 200px; width: 220px; opacity: 1;}\n#snatchlyControl img {position: relative; padding: 10px 0 0 0; display: block; margin: 0 auto; -ms-interpolation-mode: bicubic;}\n#snatchlyControl a {position: fixed; z-index: 10001; right: 0; top: 0; left: 0; height: 24px; padding: 12px 0 0; text-align: center; font-size: 14px; line-height: 1em; text-shadow: 0 1px #fff; color: #333; font-weight: bold; text-decoration: none; background: #f6f6f6 url(http://snatchly-assets-uncompressed.s3.amazonaws.com/images/global/full-gradient.png) 50% 50% repeat-x; border-bottom: 1px solid #ccc; /*box-shadow: 0 0 2px #333; -mox-box-shadow: 0 0 2px #333; -webkit-box-shadow: 0 0 2px #333;*/}\n#snatchlyControl a:hover {color: #fff; text-decoration: none; background: url(http://snatchly-assets-uncompressed.s3.amazonaws.com/images/global/hover-gradient.png) 50% 50% repeat-x; border-color: #0073ea; text-shadow: 0 -1px rgba(0, 92, 187, .5);}\n#snatchlyControl a:active {height: 23px; color: #ff0084; padding-top: 13px; background-color: #fff; border-color: #ccc; background-image: url(http://snatchly-assets-uncompressed.s3.amazonaws.com/images/global/full-gradient-inverted.png); text-shadow: 0 1px #fff;}\n.snatchly-image-preview {position: relative; padding: 0; margin: 0; float: left; background-color: #fff; border: solid #e7e7e7; border-width: 0 1px 1px 0; height: 200px; width: 220px; opacity: 1; z-index: 10002; text-align: center;}\n.snatchly-image-preview .snatchly-img {border: none; height: 200px; width: 220px; opacity: 1; padding: 0;}\n.snatchly-image-preview .snatchly-img a {margin: 0; padding: 0; position: absolute; top: 0; bottom: 0; right: 0; left: 0; display: block; text-align: center;  z-index: 1;}\n.snatchly-image-preview .snatchly-img a:hover {background-color: #f6f6f6; border: none;}\n.snatchly-image-preview .snatchly-img .image-to-snatch {max-height: 200px; max-width: 220px; width: auto !important; height: auto !important;}\n.snatchly-image-preview img.snatchly-snatchit {border: none; position: absolute; top: 82px; left: 42px; display: none; padding: 0; background-color: transparent; z-index: 100;}\n.snatchly-image-preview img.snatchly_vidind {border: none; position: absolute; top: 75px; left: 75px; padding: 0; background-color: transparent; z-index: 99;}\n.snatchly-dimensions { position: relative; margin-top: 180px; text-align: center; font-size: 10px; z-index:10003; display: inline-block; background: white; border-radius: 4px; padding: 0 2px;}\n";
                            if (isIE()) {
                                f = document.createElement("style");
                                f.type = "text/css";
                                f.media = "screen";
                                f.styleSheet.cssText = ap;
                                document.getElementsByTagName("head")[0].appendChild(f)
                            } else {
                                if (navigator.userAgent.lastIndexOf("Safari/") > 0 && parseInt(navigator.userAgent.substr(navigator.userAgent.lastIndexOf("Safari/") + 7, 7)) < 533) {
                                    f = document.createElement("style");
                                    f.innerText = "\n" + ap + "\n"
                                } else {
                                    f = document.createElement("style");
                                    f.innerHTML = "\n" + ap + "\n"
                                }
                                document.body.appendChild(f)
                            }
                            var G = document.createElement("div");
                            G.setAttribute("id", "snatchlyOverlay");
                            document.keydown = i;
                            document.body.appendChild(G);
                            var I = document.createElement("div");
                            I.setAttribute("id", "snatchlyContainer");
                            document.body.appendChild(I);
                            f = document.createElement("div");
                            f.setAttribute("id", "snatchlyControl");
                            t_img = new Image;
                            t_img.src = "http://snatchly-assets-uncompressed.s3.amazonaws.com/images/global/snatchly-vert-180x180.png";
                            f.appendChild(t_img);
                            t_a = document.createElement("a");
                            t_a.href = "#";
                            t_a.id = "snatchlyRemoveLink";
                            t_a.appendChild(document.createTextNode("Cancel Snatch"));
                            f.appendChild(t_a);
                            I.appendChild(f);
                            document.getElementById("snatchlyRemoveLink").onclick = i;
                            i = {};
                            for (var N = 0; N < J.length; N++) {
                                if (!(i[J[N].src] || J[N].im2.height && J[N].im2.height < 159)) {
                                    i[J[N].src] = 1;
                                    (function (n) {
                                        var m;
                                        m = new Image;
                                        m.src = n.src;
                                        m.h = m.height;
                                        m.w = m.width;
                                        var h = document.createElement("div");
                                        if (isIE()) {
                                            h.className = "snatchly-image-preview"
                                        } else {
                                            h.setAttribute("class", "snatchly-image-preview")
                                        }
                                        var q = document.createElement("div");
                                        if (isIE()) {
                                            q.className = "snatchly-img"
                                        } else {
                                            q.setAttribute("class", "snatchly-img")
                                        }
                                        var o = document.createElement("span");
                                        o.innerHTML = m.width + " x " + m.height;
                                        if (isIE()) {
                                            o.className = "snatchly-dimensions"
                                        } else {
                                            o.setAttribute("class", "snatchly-dimensions")
                                        }
                                        h.appendChild(o);
                                        document.getElementById("snatchlyContainer").appendChild(h).appendChild(q);
                                        h = document.createElement("a");
                                        h.setAttribute("href", "#");
                                        h.onclick = function () {
                                            ad(m);
                                            G.parentNode.removeChild(G);
                                            I.parentNode.removeChild(I);
                                            document.snatchlyLjs = 0;
                                            return false
                                        };
                                        q.appendChild(h);
                                        o = document.createElement("img");
                                        if (isIE()) {
                                            q.className = "snatchly-img"
                                        } else {
                                            q.setAttribute("class", "snatchly-img")
                                        }
                                        o.setAttribute("style", "" + g(m));
                                        o.src = m.src;
                                        o.setAttribute("alt", "Snatch This");
                                        o.className = "image-to-snatch";
                                        h.appendChild(o);
                                        var s = document.createElement("img");
                                        if (isIE()) {
                                            s.className = "snatchly-snatchit"
                                        } else {
                                            s.setAttribute("class", "snatchly-snatchit")
                                        }
                                        s.src = "http://snatchly-assets-uncompressed.s3.amazonaws.com/images/global/snatch-it.png";
                                        s.setAttribute("alt", "Snatch This");
                                        if (isIE()) {
                                            h.attachEvent("onmouseover", function () {
                                                s.style.display = "block"
                                            });
                                            h.attachEvent("onmouseout", function () {
                                                s.style.display = "none"
                                            })
                                        } else {
                                            h.addEventListener("mouseover", function () {
                                                s.style.display = "block"
                                            }, false);
                                            h.addEventListener("mouseout", function () {
                                                s.style.display = "none"
                                            }, false)
                                        }
                                        h.appendChild(s);
                                        if (J[N].type == "video") {
                                            q = document.createElement("img");
                                            if (isIE()) {
                                                q.className = "snatchly_vidind"
                                            } else {
                                                q.setAttribute("class", "snatchly_vidind")
                                            }
                                            q.src = "http://snatchly-assets-uncompressed.s3.amazonaws.com/images/global/video-indicator.png";
                                            h.appendChild(q)
                                        }
                                    })(J[N])
                                }
                            }
                            if (isIE()) {
                                var ah = document.getElementsByTagName("object");
                                for (var aw = 0; aw < ah.length; aw++) {
                                    var aq = {
                                        player: ah[aw],
                                        parent: ah[aw].parentNode,
                                        sibling: ah[aw].nextSibling
                                    };
                                    aq.parent.removeChild(ah[aw]);
                                    R.push(aq)
                                }
                            }
                            scroll(0, 0);
                            return J
                        }
                    }
                }
            }
        }
    } else {}
})();
