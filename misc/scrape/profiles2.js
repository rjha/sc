var casper = require('casper').create({
    pageSettings: {
        loadImages:  false,
        loadPlugins: false 
    }
});

var nextPage = 2 ;


function print_links(links) {
    // return ;
    //print array
    for(var i = 0;i < links.length; i++){
        if(links[i].indexOf("profile") != -1){
            console.log(links[i]);
        }
    } 
}


// Fetch all <a> elements from the page and return
// the ones which contains a href starting with 'http://'
function searchLinks() {
    var filter, map;
    filter = Array.prototype.filter;
    map = Array.prototype.map;
    return map.call(filter.call(document.querySelectorAll("a"), function(a) {
        return (/^http:\/\/.*/i).test(a.getAttribute("href"));
    }), function(a) {
        return a.getAttribute("href");
    });
}

function addLinks(){
    // console.log(this.getCurrentUrl());
    this.then(function() {
        var found = this.evaluate(searchLinks);
        print_links(found);
    });

}

function check() {
    //extra space at end 
    var nextPageTitle = "Page: "+ nextPage + " " ;
    var selector = '#searchResultPage_t a[title="' + nextPageTitle + '"]' ;
    //this.click(selector);
    
    
    this.waitUntilVisible(selector,function(){
        //this.click(selector);
        this.clickLabel('Next', 'a');
    }) ; 

    addLinks.call(this);
    nextPage++ ;
    this.run(check);

}


casper.start('http://toostep.com/', function() {
    //login 
    this.fill('form[id="loginForm"]', { 
        j_username: 'sri_saurabh2000@yahoo.com' ,
        j_password : 'Jantu211' ,
        rememberMe : '1' ,
        jobsiteId : ''
    }, true);


});


casper.thenOpen('http://toostep.com/searchUser.html?q=seo', function() {
    var links = this.evaluate(searchLinks);
    print_links(links);
});


casper.then(check);
casper.run();

