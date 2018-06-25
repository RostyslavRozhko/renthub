<?php 
  if (is_user_logged_in() ) {
    wp_redirect ( site_url("/dashboard") );
    exit;
  }
?>

<?php
/**
 * Template Name: Template Login 
 */
  get_header();
  ajax_auth_init();
?>
<?php search_header_main(); ?>

<section class="login-section">
  <div class="container login-container">
    <div class="login-container__text">
      <div class="login-container__top-text">
        Почему</br> мы лучшие?
      </div>
      <div class="login-container__bot-text">
        <div class="login-container__bot-text__item">
          Morbi scelerisque ligula facilisis metus tincidunt, sed semper urna placerat.
        </div>
        <div class="login-container__bot-text__item">
          Morbi scelerisque ligula facilisis metus tincidunt, sed semper urna placerat.
        </div>
        <div class="login-container__bot-text__item">
          Morbi scelerisque ligula facilisis metus tincidunt, sed semper urna placerat.
        </div>
        <div class="login-container__bot-text__item">
          Morbi scelerisque ligula facilisis metus tincidunt, sed semper urna placerat.
        </div>
      </div>
    </div>
    <div class="login-container__form">
      <div class="modal__tabs" id="tabs">
        <a href="#tab1" class="modal-tab login__item login__item_modal link link_active" id="tab1-link">Вход</a>
        <a href="#tab2" class="modal-tab login__item login__item_modal link">Регистрация</a>
      </div>

      <div class="modal__login active-tab modal-form" id="tab1">
       <form id="login" class="form form_modal-login" action="login" method="post">
         <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>  
         <div class="modal__input-container">
           <input type="text" name="email" id="email" class="required email input input_modal" placeholder="E-Mail">
           <input type="password" name="password" id="password" class="required input input_modal" placeholder="<?php _e('Password', 'prokkat'); ?>">
         </div>
         <div class="login-container__center-btns">
           <a class="text-link modal__btn__forgot" data-tab="#forgot"><?php _e('Lost your password?', 'prokkat'); ?></a>          
         </div>
         <input class="submit_button btn btn_modal modal__btn modal__btn__login" type="submit" name="login" value="<?php _e('Log In', 'prokkat'); ?>"/>
         <div class="text-center">
         <a data-tab="#phone-login" class="modal-tab-phone-login">
           <div class="modal__btn modal__btn__phone">
             <div class="modal__btn__text">Войти с помощью телефона</div>
           </div>
         </a>
         <a  href="<?php echo str_replace('?lang=ru', '', site_url() . '/wp-login.php?loginfacebook=1&redirect='. site_url()); ?>" onclick="window.location = '<?php echo str_replace('?lang=ru', '', site_url() . '/wp-login.php?loginfacebook=1&redirect=' . site_url() .'/dashboard' ); ?>; return false;">
           <div class="modal__btn modal__btn__facebook">
             <div class="modal__btn__text">Войти с помощью Facebook</div>
           </div>
         </a>
       </div>
       </form>
     </div>

      <div class="hide modal__sign-in modal-form" id="tab2">
        <form id="registration" class="form form_modal-login"  action="register" method="post">
          <?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?>         
          <input id="nick" type="text" class="required input input_modal" name="nick" placeholder="Ваше имя">
          <input id="signonpassword" type="password" class="required input input_modal" name="signonpassword" placeholder="Пароль">
	  <input id="signonpasswordtwo" type="password" class="required input input_modal" name="signonpasswordtwo" placeholder="Повторить пароль">
          <input id="phone" class="required input input_modal" name="phone" placeholder="Номер телефона">
          <input id="emailreg" type="email" class="required email input input_modal" name="email" placeholder="E-Mail">
          <input id="cc_user_address" type="text" name="user_city" class="required input input_modal" placeholder="Город">
          <input type="hidden" id="cc_user_city_id" class="input input_add" name="user_city_id" value="" />
          <input class="submit_button btn btn_modal modal__btn modal__btn__login register-btn" type="submit" value="Зарегистрироваться" tabindex="103">
        </form>
      </div>

      <div class="hide modal-form" id="phone-tab">
      <div class="modal__sign-in">
          <p>
          Мы отправили SMS-сообщение с кодом на номер <strong>(000) 000-00-00</strong> 
          </p>
          <p>
          Введите код подтверждения для завершения регистрации
          </p>
          <form id="confirmation" class="form form_modal-login">
            <input id="conf_code" type="number" class="required input input_modal" name="phone" placeholder="Код">
         <input class="submit_button btn btn_modal modal__btn modal__btn__login register-btn" type="submit" value="Зарегистрироваться" tabindex="103">
          </form>
        </div>
        <div class="resend-sms_form">
          <p>
          Не приходит сообщение?
          </p>
          <a class="resend-sms text-link modal__btn_resend" href="">Отправить еще раз</a>
        </div>
        <a data-tab="#tab2" class="modal-tab-phone-login">
          <div class="modal-tab__back">←  Вернуться назад</div>
        </a>
      </div>

      <div class="hide modal-form" id="phone-login-tab">
      <div class="modal__sign-in">
          <p>
          Мы отправили SMS-сообщение с кодом на номер <strong>(000) 000-00-00</strong> 
          </p>
          <p>
          Введите код подтверждения для входа в личный кабинет
          </p>
          <form id="phone-login-conf" class="form form_modal-login">
            <input id="phone-code" type="number" class="required input input_modal" name="phone" placeholder="Код">
            <input class="submit_button btn btn_modal modal__btn modal__btn__login register-btn" type="submit" value="Войти" tabindex="103">
          </form>
        </div>
        <div class="resend-sms_form">
          <p>
          Не приходит сообщение?
          </p>
          <a class="resend-sms text-link modal__btn_resend" href="">Отправить еще раз</a>
        </div>
        <a data-tab="#phone-login" class="modal-tab-phone-login">
          <div class="modal-tab__back">←  Вернуться назад</div>
        </a>
      </div>

      <div class="hide modal-form" id="phone-login">
        <div class="modal__sign-in">
          <p>
          Введите свой номер телефона
          </p>
          <form id="phone_login" class="form form_modal-login">
            <?php wp_nonce_field('ajax-login-phone-nonce', 'securityphone'); ?>  
            <input id="phone_number" class="required input input_modal" name="tel" placeholder="Номер телефона">
            <input class="submit_button btn btn_modal modal__btn modal__btn__login register-btn" type="submit" value="Войти" tabindex="103">
          </form>
        </div>
        <a data-tab="#tab1" class="modal-tab-phone-login">
          <div class="modal-tab__back">←  Вернуться назад</div>
        </a>
      </div>

      <div class="hide modal-form" id="forgot">
        <div class="modal__sign-in">
          <p>
          Введите свой E-Mail
          </p>
          <form id="forgot-pass" class="form form_modal-login">
            <?php wp_nonce_field('ajax-forgot-nonce', 'securityforgot'); ?>  
            <input type="text" name="email" id="forgot-email" class="required email input input_modal" placeholder="E-Mail">
            <input class="submit_button btn btn_modal modal__btn modal__btn__login register-btn" type="submit" value="Восстановить доступ" tabindex="103">
          </form>
        </div>
        <a data-tab="#tab1" class="modal-tab-phone-login">
          <div class="modal-tab__back">←  Вернуться назад</div>
        </a>
      </div>

      <div class="hide modal-form" id="thank-tab">
        <div class="modal__thank">
          <div class="modal-tab__image-container">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/thx-icon.svg">
          </div>
          <div class="modal-tab__thank-text">Спасибо за регистрацию!</div>
          <p>
          Теперь вы можете использовать все функции RentHub в полном объеме!
          </p>
          <a href="<?php site_url('/dashboard') ?>" class="pointer">
           <div class="modal__btn modal__btn__phone thank">
             <div class="modal__btn__text">Личный кабинет</div>
           </div>
         </a>
        </div>
      </div>

    </div>
  </div>
</section>

<div class="fancybox-error">
</div>

<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.maskedinput.min.js"></script>
<script>
jQuery(document).ready(function(){
  $('#phone_number').mask("+38 (999) 999-99-99");
  $('#phone').mask("+38 (999) 999-99-99");
});
</script>

<?php 
  get_footer(); 
?>
  
