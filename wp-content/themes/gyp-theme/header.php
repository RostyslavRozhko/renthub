<?php
 nocache_headers();
 
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <meta name="google-site-verification" content="TjAYDXvFQmRdQGGoO6GtxyiSLPIAyfA6jbpXLqMZvyA" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/solid.css" integrity="sha384-Rw5qeepMFvJVEZdSo1nDQD5B6wX0m7c5Z/pLNvjkB14W6Yki1hKbSEQaX9ffUbWe" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/fontawesome.css" integrity="sha384-GVa9GOgVQgOk+TNYXu7S/InPTfSDTtBalSgkgqQ7sCik56N9ztlkoTr2f/T44oKV" crossorigin="anonymous">
        <title>
            <?php
            /*
             * Print the <title> tag based on what is being viewed.
             */
            global $page, $paged, $current_user;

            $title = wp_title('|', false, 'right');

            _e($title, 'prokkat');

            // Add the blog name.
            bloginfo('name');

            // Add the blog description for the home/front page.
            $site_description = get_bloginfo('description', 'display');
            if ($site_description && ( is_home() || is_front_page() ))
                echo " | $site_description";

            // Add a page number if necessary:
            if ($paged >= 2 || $page >= 2)
                echo ' | ' . sprintf(__(PAGE . ' %s', THEME_SLUG), max($paged, $page));
            ?>
        </title>
        <?php
        if (is_home()) {
            if (cc_get_option('cc_keyword') != '') {
                ?>
                <meta name="keywords" content="<?php echo cc_get_option('cc_keyword'); ?>" />
                <?php
            }
            ?>
            <?php if (cc_get_option('cc_description') != '') { ?>
                <meta name="description" content="<?php echo cc_get_option('cc_description'); ?>" />
                <?php
            }
            ?>
            <?php if (cc_get_option('cc_author') != '') { ?>
                <meta name="author" content="<?php echo cc_get_option('cc_author'); ?>" />
                <?php
            }
        }
        ?>
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		<link href="https://fonts.googleapis.com/css?family=Fira+Sans:300,400,700" rel="stylesheet">
       <!--[if IE]>
        <script src="<?php echo TEMPLATEURL; ?>/js/html5shiv.js"></script>
        <![endif]-->
        <?php
        wp_head();
        ?>
        <link rel='stylesheet' href='<?php echo get_stylesheet_directory_uri() . "/style.css"; ?>' type='text/css' />        
    </head>
    <body <?php body_class() ?>>
<header class="header">
    <div class="wrap-nav">
        <div class="container header__container">
                <div class="header__logo__container">                    
                    <a href="<?php echo site_url(); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo.png" alt="<?php bloginfo('name'); ?>" class="header__logo__image" />
                    </a>
                    <span class="logo-text__sublogo"><?php _e('Rent', 'prokkat') ?></span>
                </div>
				
                <div class="header__control-container">
                   <!-- <ul class="lang-switcher">
                        <?php //pll_the_languages( array( 'dropdown' => 0 ) ); ?>
                    </ul>-->
					
                <a href="<?php echo site_url('new/'); ?>" class="login-top login__item login__item__yellow">
                    <i style="line-height: inherit" class="fa fa-plus" id="old-ad-button" aria-hidden="true"></i>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/plus.svg" id="new-ad-button" style="display: none">
                    <span><?php echo __('Add Ad', 'prokkat'); ?></span>
                </a>
                    <?php if (!is_user_logged_in()) : ?>                
                        <a href="<?php echo site_url('login'); ?>" class="login__item login__item__grey"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/user.svg" style="padding-top: 14px"></a>
                        
						<?php  else: ?>
						    
                        <div class="dropopen">
                            <?php
                                $ava = get_the_author_meta( 'user_avatar', get_current_user_id() );
                                if( !$ava ) $ava = get_stylesheet_directory_uri() .'/img/no-avatar.svg';
                            ?>
                            <img class="header__user-image dropopen_link" id="dashDrop" src="<?php echo $ava; ?>" />
                             
                             <ul class="dashdrop mydropmenu">
                           
                            <li class="dropdown__item">
                                <a href="<?php echo site_url('new/'); ?>"><?php echo __('Add Ad', 'prokkat'); ?></a>
                            </li>
                            <li class="dropdown__item">
                                <a href="<?php echo add_query_arg( array( 'action'=>'view' ), site_url( 'dashboard/' )); ?>"><?php echo __('My Ads', 'prokkat'); ?></a>
                            </li>
                            <li class="dropdown__item">
                                <a href="<?php echo add_query_arg( array( 'action'=>'messagebox' ), site_url( 'dashboard/' )); ?>"><?php echo __('My messages', 'prokkat'); ?></a>
                            </li>
                            <li class="dashdrop__exit dropdown__item">
                                <a href="<?php echo add_query_arg( array( 'action'=>'view' ), site_url( 'dashboard/' )); ?>"><?php echo __('My Dashboard', 'prokkat'); ?></a>
                            </li>
                            <li class="dropdown__item">
                                <a href="<?php echo wp_logout_url(site_url()); ?>"><?php echo __('Log out', 'prokkat'); ?></a>
                            </li>
                        </ul>
                         </div>
                           
                       <?php endif; ?>
					   
                    </div>
        </div>
		
    </div>

</header>

<?php if (!is_user_logged_in()) get_template_part('ajax', 'auth'); ?>
