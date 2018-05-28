var page = require('webpage').create();
var system = require('system');
//var url = 'https://www.olx.ua/obyavlenie/prokat-arenda-avto-ford-focus-2012-2l-avtomat-sedan-IDuqgv0.html#05d220641f';
//var url = 'https://www.olx.ua/obyavlenie/svadebnyy-limuzin-avto-arenda-prokat-v-zaporozhe-foto-podarok-ot-firm-IDhzwHa.html#05d220641f';
//var url = 'https://www.olx.ua/obyavlenie/250-grn-chas-prokat-avtomobilya-arenda-avto-zakaz-mashiny-na-svadbu-IDzhDuO.html#05d220641f';
//var url = 'https://www.olx.ua/obyavlenie/avto-na-svadbu-svadebnoe-avto-prokat-avto-IDtDBeK.html#03b9554950';
//var url = 'https://www.olx.ua/obyavlenie/arenda-prokat-dolgosrochnaya-arenda-avto-IDuVYUo.html#03b9554950';
//var url = 'https://www.olx.ua/obyavlenie/prokat-avto-arenda-avto-hyundai-sonata-2015g-bez-zaloga-IDxPqek.html#03b9554950';
//var url = 'https://www.olx.ua/obyavlenie/prokat-arenda-avto-ford-focus-2012-2l-avtomat-sedan-IDuqgv0.html#86cf5562b4';
page.settings.userAgent = 'SpecialAgent';
page.open(params.url, function(status) {
    	var get_telephone = page.evaluate(function() {
			$('.spoiler').click();
  	});
	setTimeout(function () {
		var get_tel = page.evaluate(function() {
				var tel = $('.fnormal').text();
				if(tel.indexOf('+38') == 0){
					tel = tel.split('+38')[1];
					if(tel.match(/\D/g) || tel.match(/\(|\)/g)){
						tel = tel.replace(/\D/g, "");
                    				tel = tel.replace(/\(|\)/g, '');
					}
                    			return tel.substr(0 , 10);
				}
 				if(tel.indexOf('8') == 0){
                                        tel = tel.split('8')[1];
                                        if(tel.match(/\D/g) || tel.match(/\(|\)/g)){
                                                tel = tel.replace(/\D/g, "");
                                                tel = tel.replace(/\(|\)/g, '');
                                        }
                                        return tel.substr(0 , 10);
                                }
				if(tel.indexOf('38') == 0){
                                        tel = tel.split('38')[1];
                                        if(tel.match(/\D/g) || tel.match(/\(|\)/g)){
                                                tel = tel.replace(/\D/g, "");
                                                tel = tel.replace(/\(|\)/g, '');
                                        }
                                        return tel.substr(0 , 10);
                                }
				tel = $('.fnormal').text();
				if(tel.match(/\(|\)/g) || tel.match(/\D/g)){
					tel = tel.replace(/\D/g, "");
					tel = tel.replace(/\(|\)/g, '');
				}
				return tel.substr(0 , 10);
		//return $('.fnormal').html();
		});
		console.log(get_tel);
  		phantom.exit();
	}, 1000);
});

