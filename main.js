/*
  get product links json file
*/

var page = require('webpage').create();
var fs = require('fs');

page.settings.userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_7) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.68 Safari/534.24';

page.open('https://www.amazon.com/gp/bestsellers', function(status){

  var categories_links_json = page.evaluate(function(){

    var all_links = [];

    var links = document.querySelectorAll('.a-section a');

    [].forEach.call(links, function(link){
      if(link.title.indexOf('stars') > -1){
        all_links.push({
          'title': link.text,
          'href': link.getAttribute('href')
        });
      }
    });

    // for(var i = 0; i < 3; i++){
      // var all_links_json = "http://amazon.com" + all_links[i].href;
      // console.log(all_links_json);      
    // }

    var all_links_json = JSON.stringify(all_links); 

    return all_links_json;

  });

  fs.write('product_links.json', categories_links_json);

  phantom.exit();

});
