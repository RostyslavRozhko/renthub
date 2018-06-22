var page = require('webpage').create();
var system = require('system');
page.injectJs('https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js');
page.settings.userAgent = 'SpecialAgent';
page.open(views.url, function(status) {
           phantom.exit();
});
