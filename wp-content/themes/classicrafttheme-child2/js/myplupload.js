jQuery.fn.exists = function() {
    return jQuery(this).length > 0;
}
jQuery(document).ready(function($) {

    if ($(".plupload-upload-uic").exists()) {
        var pconfig = false;
        $(".plupload-upload-uic").each(function() {
            var $this = $(this);
            var id1 = $this.attr("id");
            var imgId = id1.replace("plupload-upload-ui", "");

            plu_show_thumbs(imgId);

            pconfig = JSON.parse(JSON.stringify(base_plupload_config));

            pconfig["browse_button"] = imgId + pconfig["browse_button"];
            pconfig["container"] = imgId + pconfig["container"];
            pconfig["file_data_name"] = imgId + pconfig["file_data_name"];
            pconfig["multipart_params"]["imgid"] = imgId;
            pconfig["multipart_params"]["_ajax_nonce"] = $this.find(".ajaxnonceplu").attr("id").replace("ajaxnonceplu", "");

            if ($this.hasClass("plupload-upload-uic-multiple")) {
                pconfig["multi_selection"] = true;
            }

            if ($this.find(".plupload-resize").exists()) {
                var w = parseInt($this.find(".plupload-width").attr("id").replace("plupload-width", ""));
                var h = parseInt($this.find(".plupload-height").attr("id").replace("plupload-height", ""));
                pconfig["resize"] = {
                    width: w,
                    height: h,
                    quality: 90
                };
            }

            plupload.addFileFilter('min_width', function(maxwidth, file, cb) {
                var self = this, img = new o.Image();
            
                function finalize(result) {
                    // cleanup
                    img.destroy();
                    img = null;
            
                   // if rule has been violated in one way or another, trigger an error
                    if (!result) {
                        self.trigger('Error', {
                            code : plupload.IMAGE_DIMENSIONS_ERROR,
                            message : "Image width should be more than " + maxwidth  + " pixels.",
                            file : file
                        });

                        $('.photo-error').show()
                 }
                    cb(result);
                }
                img.onload = function() {
                    // check if resolution cap is not exceeded
                    finalize(img.width >= maxwidth);
                };
                img.onerror = function() {
                    finalize(false);
                };
                img.load(file.getSource());
            });

            pconfig['filters'] = {
                min_width: 800
            }

            var uploader = new plupload.Uploader(pconfig);

            uploader.bind('Init', function(up) {

            });

            uploader.init();

            // a file was added in the queue
            uploader.bind('FilesAdded', function(up, files) {
                $('.photo-error').hide()
                
                $.each(files, function(i, file) {
                //$this.find('.filelist').append('<div class="file" id="' + file.id + '"><b>' +  file.name + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' + '<div class="fileprogress"></div></div>');
			      if( imgId != 'ava' ) {
				    $this.find('.filelist').append('<div class="file" id="' + file.id + '">(<span>' + plupload.formatSize(0) + '</span>/' +
				      plupload.formatSize(file.size) + ') ' + '<div class="fileprogress"></div></div>');
				  }
				  else {
					  $this.find('.filelist').append('<div class="file" id="' + file.id + '"><div class="fileprogress"></div></div>');
				  }
				  //$this.find('.filelist').append('<div class="file" id="' + file.id + '"><img src="http://sweetico.com/wp-content/themes/classicrafttheme-child/img/loading.gif" alt="" /></div>');
                });
             
                up.refresh();
                up.start();
            });

            uploader.bind('UploadProgress', function(up, file) {

                $('#' + file.id + " .fileprogress").width(file.percent + "%");
                $('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
            });

            // a file was uploaded
            uploader.bind('FileUploaded', function(up, file, response) {


                $('#' + file.id).fadeOut();
                response = response["response"]
                // add url to the hidden field
                if ($this.hasClass("plupload-upload-uic-multiple")) {
                    // multiple
                    var v1 = $.trim($("#" + imgId).val());
                    if (v1) {
                        v1 = v1 + "," + response;
                    } else {
                        v1 = response;
                    }
                    $("#" + imgId).val(v1);
                } else {
                    // single
                    $("#" + imgId).val(response + "");
                }
                // show thumbs 
                plu_show_thumbs(imgId);
            });
        });
    }
});

function UrlExists(url)
{
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    return http.status!=404;
}

var saved_url_img1 = $("#img1").val();
var saved_url_img2 = $("#img2").val();
var saved_url_img3 = $("#img3").val();

function plu_show_thumbs(imgId) {
    var $ = jQuery;
	var thumb = '';
	var pconfig = JSON.parse(JSON.stringify(base_plupload_config));
    var thumbsC = $("#" + imgId + "plupload-thumbs");
    thumbsC.html("");
    // get urls
    var imagesS = $("#" + imgId).val();
    var images = imagesS.split(",");
	
    for (var i = 0; i < images.length; i++) {
        if (images[i]) {

	        if( imgId != 'ava' ) {
				thumbsC.removeClass("hide");
			    $("#" + imgId + "plupload-browse-button").addClass("hide");
		    }
			
			var makeMain = mainImgCaption = '';
			if( imgId == 'img1' ) {
				mainImgCaption = '<div class="upload__control-img upload__control-img_pr"><div class="upload__control-label">' + pconfig["main_img_caption"] + '</div></div>';
			}
	        else if(  imgId == 'img2' || imgId == 'img3') {
		      makeMain = '<div class="upload__control-label upload__control-label_add"><a href="#0" class="link makemain">' + pconfig["make_main_text"] + '</a></div>';
	        }
         
		    var filename = images[i].substring(images[i].lastIndexOf('/')+1);
			filename = filename.split(".")[0];
			  
			if( imgId == 'img1' || imgId == 'img2' || imgId == 'img3' ) {
			  var path = images[i].replace(/(.*)\/.*(\.(png|gif|jpe?g)$)/i, '$1/'+ filename +'_515X515$2');
			  if (UrlExists(path)) images[i] = path;
			}
			
			if( imgId == 'ava' & images[i].indexOf(window.location.host) > -1) {
			  var path = images[i].replace(/(.*)\/.*(\.(png|gif|jpe?g)$)/i, '$1/'+ filename +'_152X152$2');
			  if (UrlExists(path)) images[i] = path;
			}
        
            thumb = $('<div class="thumb" id="thumb' + imgId + i + '"><img src="' + images[i] + '" alt="" />' +
			'<div class="upload__control-img">' + makeMain + '<div class="upload__control-del"><a id="thumbremovelink' + imgId + i + '" href="#0">' +
            '<svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">' +
            '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>' +
            '<path d="M0 0h24v24H0z" fill="none"/></svg></a></div></div></div>' + mainImgCaption);
			thumbsC.append(thumb);
			
            thumb.find(".upload__control-del a").click(function() {
                var ki = $(this).attr("id").replace("thumbremovelink" + imgId, "");
                ki = parseInt(ki);
                var kimages = [];
                imagesS = $("#" + imgId).val();
                images = imagesS.split(",");
                for (var j = 0; j < images.length; j++) {
                    if (j != ki) {
                        kimages[kimages.length] = images[j];
                    }
                }
                $("#" + imgId).val(kimages.join());
                plu_show_thumbs(imgId);
				
				if( imgId == 'img1' & saved_url_img1 == imagesS ) return false;
				if( imgId == 'img2' & saved_url_img2 == imagesS ) return false;
				if( imgId == 'img3' & saved_url_img3 == imagesS ) return false;

				  jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: commonjs_object.ajaxurl,
                    data: {
                      'action': 'delete_image',
					  'type': imgId,
					  'img_url': imagesS
                    },
                    success: function (data) {
				      //alert(data.message);
                    }
                  });

                return false;
            });
			
			thumb.find(".makemain").click(function() {
                var mainImage = $("#img1").val();
				$("#img1").val($("#" + imgId).val());
				$("#" + imgId).val(mainImage);
                
				plu_show_thumbs(imgId);
				plu_show_thumbs("img1");
				
                return false;
            });
        }
		else if( imgId != 'ava' ) {
			thumbsC.addClass("hide");
			$("#" + imgId + "plupload-browse-button").removeClass("hide");
		}
		else {
			thumb = $('<div class="thumb" id="thumb' + imgId + i + '"><img src="' + pconfig["no-avatar"] + '" alt="" /></div>');
			thumbsC.append(thumb); 
		}
    }
    if (images.length > 1) {
        thumbsC.sortable({
            update: function(event, ui) {
                var kimages = [];
                thumbsC.find("img").each(function() {
                    kimages[kimages.length] = $(this).attr("src");
                    $("#" + imgId).val(kimages.join());
                    plu_show_thumbs(imgId);
                });
            }
        });
        thumbsC.disableSelection();
    }
}