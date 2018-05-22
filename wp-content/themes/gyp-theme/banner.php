<div class="search-list__banner">
                  <div class="search-list__banner-text">
                    <?php _e('Want to rent your things?', 'prokkat'); ?>
                  </div>
                  <a href="<?php echo site_url('new/'); ?>" class="login-top login__item login__item__yellow <?php if (!is_user_logged_in()) echo 'btnModal' ?>">
                    <i style="line-height: inherit" class="fa fa-plus" aria-hidden="true"></i>
                    <span><?php echo __('Add Ad', 'prokkat'); ?></span>
                  </a>
              </div>