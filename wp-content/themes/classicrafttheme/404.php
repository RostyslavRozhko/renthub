<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>
        <?php
        /*

         * Print the <title> tag based on what is being viewed.

         */
        global $page, $paged;
        wp_title('|', true, 'right');
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
    <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet'
          type='text/css'>
    <link rel="stylesheet" type="text/css" media="all"href="<?php bloginfo('template_directory'); ?>/css/style.css"  />

    <!--[if IE]>

    <script src="<?php echo TEMPLATEURL; ?>/js/html5shiv.js"></script>

    <![endif]-->

    <?php

    wp_head();

    ?>

</head>

<body <?php body_class() ?>>

<header class="header">
    <div class="wrap-nav">
        <div class="container">
            <div class="top-nav">
                <div class="logo_grid">
                    <a href="#0" class="link btn btn_mobile btn_menu-mb fleft" id="menubtn"><i class="fa fa-bars"                                                                                aria-hidden="true"></i>menu</a>

                    <?php if (!is_user_logged_in()): ?>
                        <a href="#0" class="link btn btn_mobile btn_sign-in fright"><i class="fa fa-sign-in" aria-hidden="true"></i></a>
                    <?php endif; ?>
                    <?php if (is_user_logged_in()): ?>
                        <a href="#0" class="link btn btn_mobile btn_account fright"><i class="fa fa-user"aria-hidden="true"></i></i>Аккаунт
                        </a>
                    <?php endif; ?>

                    <a class="logo" href="<?php echo home_url(); ?>"><img src="<?php if (cc_get_option('cc_logo') != '') { ?><?php echo cc_get_option('cc_logo'); ?><?php } else { ?><?php echo get_template_directory_uri(); ?>/images/logo.png<?php } ?>" alt="<?php bloginfo('name'); ?>" class="img-responsive fleft" /></a>
                </div>

            </div>
        </div>

    </div>

</header>


    <!--Start Cotent Wrapper-->
    <div class="container">
        <div class="container_24">
            <div class="grid_24">
                <div class="grid_24 alpha">
                    <!--Start Cotent-->
                    <div class="content">
                        <h1 class="page_title">Error 404</h1>
                            <?php the_content(); ?>
                            The page your trying to load in currently not available.</br>
                            Please go back to the homepage and search another query.
                    </div>
                    <!--End Cotent-->
                </div>

            </div>
            <div class="clear"></div>
        </div>
    </div>
    <!--End Cotent Wrapper-->