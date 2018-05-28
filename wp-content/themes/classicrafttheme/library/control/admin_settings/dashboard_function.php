<?php

function cc_dashboard_sidebar() {
    ?>
    <div class="sidebar_dashboard">
        <h2 class="head"><?php echo __( 'Account Information', 'cc' ); ?></h2>
        <div class="author-meta">
            <?php            
            global $current_user;
            get_currentuserinfo();
            echo get_avatar($current_user->ID, 40);
            ?>
            <h4><?php echo __( 'Welcome', 'cc' ) .',&nbsp;'. $current_user->user_login; ?></h4>
            <small><?php echo __( 'Member Since :', 'cc' ) ."&nbsp;";
            $registered = ($current_user->user_registered . "\n");
            echo date("d/M/y", strtotime($registered));
            ?>
            </small>
        </div>                          
    </div>
    <div class="sidebar_dashboard">
        <h2 class="head"><?php echo __( 'User Options', 'cc' ); ?></h2>                         
        <ul class="dash-list">
            <li class="addnew"><a href="<?php echo site_url(CC_ADNEW); ?>"><?php echo __( 'Add New', 'cc' ); ?></a></li>
            <li class="view"><a href="<?php echo site_url(CC_DASHBOARD."?action=view"); ?>"><?php echo __( 'View Ads', 'cc' ); ?></a></li>
            <li class="comment"><a href="<?php echo site_url(CC_DASHBOARD."?action=comment"); ?>"><?php echo __( 'View comments', 'cc' ); ?></a></li>
            <li class="lead"><a href="<?php echo site_url(CC_DASHBOARD."?action=lead"); ?>"><?php echo __( 'View Leads', 'cc' ); ?></a></li>
            <li class="expire"><a href="<?php echo site_url(CC_DASHBOARD."?action=expire"); ?>"><?php echo __( 'View Expired Ads', 'cc' ); ?></a></li>
            <li class="profile"><a href="<?php echo site_url(CC_DASHBOARD."?action=profile"); ?>"><?php echo __( 'Edit Profile', 'cc' ); ?></a></li>
        </ul>
    </div>
    <?php
}
?>
