<?php
$title = get_the_title($post_id);
$get_post_author = get_post($post_id);
$name = get_user_meta($get_post_author->post_author ,'nickname' , true);
$site_name = str_replace( "http://", "" , get_option('siteurl'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo get_option('blogname');?></title>
<style type="text/css">
		body {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            margin: 0 !important;	
            width: 100% !important;
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
            -webkit-font-smoothing: antialiased !important;
        }
		.content {
			  width: 800px;
			  height: 400px;
			  background-color: #ffffff;
		}
		.name {
			font-family: 'Fira Sans', sans-serif;
			font-size:24px;
			line-height:20px;
			color: #0a0f19;
			width: 520px;
			height: 70px;
			display: block;
			font-weight: 700;
			top: 20px;
    		position: relative;
		}
		.your_ad {
			font-family: 'Fira Sans', sans-serif;
			font-size: 16px;
			line-height: 20px;
			color: #63666c;
		}
		.get_permalink {
			  text-decoration: none;
			    box-sizing: border-box;
			    height: 50px;
			    line-height: 50px;
			    vertical-align: middle;
			    border-radius: 25px;
			    border: none;
			    padding: 0 30px;
			    font-size: 14px;
			    color: black;
			    font-weight: bold;
			    text-transform: none;
			    background-color: #ffd400;
			    display: block;
			    width: 300px;
			    top: 50px;
    			position: relative;
		}

</style>
</head>
<body paddingwidth="0" paddingheight="0" bgcolor="#d1d3d4"
      style="padding-top: 0; padding-bottom: 0; padding-top: 0; padding-bottom: 0; background-repeat: repeat; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased;"
      offset="0" toppadding="0" leftpadding="0">
		<div class="content">
			<span class="name">Здравствуйте,<?php echo $name;?></span>
				<span class="your_ad">Ваше обьявление <b><?php echo $title;?></b> было успешно опубликовано</span>
				<a href="<?php echo get_permalink($post_id); ?>" class="get_permalink">Перейти на страницу обьявления</a>
				<span class="links">Полезные ссылки</span><a href="<?php echo site_url();?>/dashboard"></a><a href="<?php echo site_url();?>/new"></a><a href="<?php echo site_url();?>/dashboard/?action=messagebox"></a>
		</div>
</body>
</html>