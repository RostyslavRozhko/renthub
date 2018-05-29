<?php 
    $ava = get_the_author_meta( 'user_avatar', $author_id );
    if( !$ava ) $ava = get_stylesheet_directory_uri() .'/img/no-avatar.png'; 
?>
<div class="hide call-feedback__container" id="callFeedback">

    <div class="call-feedback__tab" id="mainTab">
        <div class="call-feedback__author">
            <img src="<?php echo $ava; ?>" />
            <div class="call-feedback__name">
                <?php echo the_author_posts_link(); ?>
                <!-- <div class="contact-ad__author-city" id="city"></div> -->
            </div>
        </div> 
        <div class="call-feedback__number">
            <a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a>
        </div>
        <div class="call-feedback__text">
            <ul>
                <li><?php _e('trust salespeople with a verified phone number', 'prokkat'); ?></li>
                <li><?php _e('do not settle for an advance payment', 'prokkat'); ?></li>
                <li><?php _e('do not disclose bank card details', 'prokkat'); ?></li>
            </ul>
        </div>
        <div class="call-feedback__buttons">
            <button id="call-feedback__success"><?php _e('I phoned', 'prokkat'); ?></button>
            <button id="call-feedback__fail"><?php _e('Complain', 'prokkat'); ?></button>
        </div>
    </div>

    <div class="call-feedback__tab hide" id="failTab">   
        <div class="call-feedback__author">
            <img src="<?php echo $ava; ?>" />
            <div class="call-feedback__name">
                <?php echo the_author_posts_link(); ?>
                <!-- <div class="contact-ad__author-city" id="city"></div> -->
            </div>
        </div> 
        <div class="call-feedback__number call-feedback__red"><?php _e('Complain', 'prokkat'); ?></div>
        <div class="call-feedback__text call-feedback__reasons">
            <ul>
                <li><?php _e('The property has already been sold / leased', 'prokkat'); ?></li>
                <li><?php _e('Wrong contact information', 'prokkat'); ?></li>
                <li><?php _e('The author behaves suspiciously', 'prokkat'); ?></li>
                <li><?php _e('The ad contains incorrect information', 'prokkat'); ?></li>
                <li><?php _e('Another reason', 'prokkat'); ?></li>
            </ul>
        </div>
    </div>

    <div class="call-feedback__tab hide" id="successTab"> 
        <div class="call-feedback__success-container">
            <img src="">
            <div class="bold-text"><?php _e('Thanks for the feedback!', 'prokkat') ?></div>
            <div class="additional-text hide"><?php _e('Your message has been sent and will</br> be reviewed shortly.', 'prokkat'); ?></div>
        </div>
    </div>
    <input type="hidden" id="post_id" value="<?php echo $post->ID ?>" >
</div>