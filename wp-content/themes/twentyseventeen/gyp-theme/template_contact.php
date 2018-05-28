<?php
/**
 * Template Name: Templact Contact
 */

  $emailSent = false;
  $captcha1 = rand(0, 9);
  $captcha2 = rand(0, 9);
  $cc_sbs_captcha = $captcha1 + $captcha2;

  if( isset($_POST['submitted']) && isset($_POST['contactName']) && isset($_POST['email']) && isset($_POST['message'])) {
    $name = trim($_POST['contactName']);
    $email = trim($_POST['email']);
    $message = stripslashes(trim($_POST['message']));
    //$website = trim($_POST['website']);
    
	if( $name && $email && $message && fep_verify_nonce($_POST['token'], 'new_message')) {
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		$admin_email = get_option('admin_email');
        //$admin_email = 'anastasiya.tulparova@gmail.com';
	    $subject = 'Повідомлення з сайту';
		$headers = 'From: ' . $blogname . ' <' . $email . '>' . "\r\n";
        $message = "Ім'я: $name \n\nEmail: $email \n\nПовідомлення: $message";
		
		$emailSent = wp_mail($admin_email, $subject, $message, $headers);
	}
  }
  
  get_header();
?>
<div class="container">
    <div>
        <div>
            <div>
                <div class="full">
                    <div class="content">
                    <h1 class="page_title"><?php the_title(); ?></h1>
					
                    <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
					
                    <div>
                        <?php if( isset($emailSent) && $emailSent )
                            echo notification_markup(__( 'Thanks, your email was sent successfully.', 'cc' ));
					    ?>
                            <form action="<?php the_permalink(); ?>" class="contactform" method="post" id="contactForm">
                               
                               <div class="input-wrp input-wrp_block">
                                   <label for="contactName"><?php echo __( 'Your Name', 'cc' ); ?> <span class="required"><?php echo "*"; ?></span></label>                        
                                   <input  class="input input_add required" type="text" name="contactName" id="contactName" value="" />
                               </div>
                               
							   <div class="input-wrp input-wrp_block">
                                 <label for="eMail"><?php echo __( 'Your Email', 'cc' ); ?> <span class="required"><?php echo "*"; ?></span></label>                      
                                 <input class="input input_add required email" type="text" name="email" id="eMail" value="" />
                               </div>
							   
                               <div class="input-wrp input-wrp_block"> 
                                 <label for="message"><?php echo __( 'Your Message', 'cc' ); ?> <span class="required"><?php echo "*"; ?></span></label>
                                 <textarea name="message" id="message" class="textarea textarea_mess required"></textarea>
                               </div>
							   
							   <div class="input-wrp input-wrp_block">
                                 <div>
                                   <div class="input-wrp input-wrp_block">
                                     <div class="col6 nopaddingl">
                                       <input class="captcha required input input_add" id="captcha" placeholder="<?php echo __( 'Enter the answer', 'cc' ); ?>" type="text" name="captcha" value="" />
                                       <span id="show_captcha"><?php echo $captcha1 . ' + ' . $captcha2; ?></span>
                                     </div>
                                   </div>
                                 </div>
                               </div>
							   
                               <div class="input-wrp input-wrp_block">
							     <a href="#0" id="submit" class="btn btn_lblue"><i class="fa fa-paper-plane-o" aria-hidden="true"> </i> <?php echo __( 'Submit', 'cc' ); ?></a>
                                 <input type="hidden"  name="submitted" id="submitted" value="true" />
								 <input type="hidden" id="captchacode" name="captchacode" value="<?php echo $cc_sbs_captcha; ?>" />
								 <input type="hidden" name="token" value="<?php echo fep_create_nonce('new_message'); ?>" />
                               </div>
                                
                            </form>
                    </div>
                </div>
                <!--End Cotent-->         
                </div>
                
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>

<script>
(function($){
  $(document).ready(function(){
    $('#submit').on('click', function(e){
		$.extend($.validator.messages, {
          required: validate_object.required,
		  email: validate_object.email,
          equalTo: leads_object.equalTo,
		});
		$("#contactForm").validate({
			rules:{
			  captcha:{ equalTo:'#captchacode' }
		    }
		});
		if (!$('#contactForm').valid()) return false;
		e.preventDefault();
	    $('#contactForm').submit();
	});
  });
})(this.jQuery);
</script>

<?php get_footer(); ?>
