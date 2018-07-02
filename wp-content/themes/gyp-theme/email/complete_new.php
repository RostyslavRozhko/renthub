<?php
$title = get_the_title($post_id);
$get_post_author = get_post($post_id);
$name = get_user_meta($get_post_author->post_author ,'nickname' , true);
$site_name = mb_strtoupper(str_replace(array('https://' , 'http://') , '' , get_option('siteurl')));
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
			      width: 660px;
    			  height: 260px;
    			  background-color: #ffffff;
    			  margin: 10px auto;
		}
		.name {
			font-family: 'Fira Sans', sans-serif;
			font-size:24px;
			line-height:20px;
			color: #0a0f19;
			width: 520px;
			height: 60px;
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
		.your_ad > b {
			color: black;
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
		    margin: 40px auto;
		}
		.content_links >.links {
			font-family: 'Fira Sans',sans-serif;
		    font-size: 16px;
		    line-height: 20px;
		    color: black;
		    font-weight: bold;
		}
		.content_links {
			width: 660px;
    		height: 130px;
    		background-color: #ffffff;
    		margin: 10px auto;
		}
		.content_links > a {
			margin-right: 10px;
    		margin-left: 10px;
    		font-size: 16px;
		}
		.message_content {
			    margin-top: 50px;
		}
		.message , .date{
			    display: block;
    			text-align: center;
    			font-size: 16px;
    			margin-top: 10px;
		}
		.container_img {
			  width: 800px;
			  height: 70px;
			  background-color: #f4f6f9;
			  margin: 0 auto;
			  position: relative;
		}
		.date {
			display: block;
		    text-align: center;
		    font-size: 16px;
		    margin-top: 20px;
		}
</style>
</head>
<body paddingwidth="0" paddingheight="0" bgcolor="#d1d3d4"
      style="padding-top: 0; padding-bottom: 0; padding-top: 0; padding-bottom: 0; background-repeat: repeat; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased;"
      offset="0" toppadding="0" leftpadding="0">
      <div class="container_img">
      	<div class="header__logo">
            <a href="<?php echo site_url(); ?>" class="link_url" style="position: absolute;line-height: 70px; left: 70px;"><img src="<?php if (cc_get_option('cc_logo') != '') { ?><?php echo cc_get_option('cc_logo'); ?><?php } else { ?><?php echo get_template_directory_uri(); ?>/images/logo.png<?php } ?>" alt="<?php bloginfo('name'); ?>" class="logo__image" />
            </a>
            <span class="sublogo" style="line-height: 70px;font-size: 16px; position: absolute; right: 150px;"><?php _e('Rent', 'prokkat') ?></span>
        </div>
      </div>
		<div class="content">
			<span class="name">Здравствуйте,<?php echo $name;?></span>
				<span class="your_ad">Ваше обьявление <b><?php echo $title;?></b> было успешно опубликовано</span>
				<a href="<?php echo get_permalink($post_id); ?>" class="get_permalink">Перейти на страницу обьявления</a>
		</div>
		<div class="content_links">
			<span class="links">Полезные ссылки:</span><a href="<?php echo site_url();?>/dashboard">Мой профиль</a>|<a href="<?php echo site_url();?>/new">Добавить обьявление</a>|<a href="<?php echo site_url();?>/dashboard/?action=messagebox">Мои сообщения</a>
			<div class="message_content">
				<span class="message">Сообщение было отправлено автоматически. Пожалуйста, не отвечайте на него</span>
				<span class="date"><?php echo date('Y') . '&nbsp;' . $site_name;?></span>
			</div>
		</div>
</body>
</html>