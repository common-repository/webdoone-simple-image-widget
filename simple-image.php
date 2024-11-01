<?php
/*
Plugin Name: Webdoone Simple Image Widget
Plugin URI: https://wordpress.org/plugins/webdoone-simple-image-widget
Description: A simple plugin that adds a widget for insert image.
Version: 1.1.2
Author: Webdoone
Author URI: http://webdoone.com/
Text Domain: webdoone-simple-image-widget
Domain Path: /languages
License: GPL2
*/

class WebdooneSimpleImage_Widget extends WP_Widget {

  // widget constructor
  public function __construct(){
    parent::__construct('webdoonesimpleimage_widget', esc_html__('Webdoone Simple Image Widget', 'webdoone-simple-image-widget'), 
        array( 
            'class'         => 'webdoonesimpleimage_widget',
            'description'   => esc_html__('A simple plugin that adds a widget for insert image.', 'webdoone-simple-image-widget'),
            'customize_selective_refresh' => true, 
            )
        );

    load_plugin_textdomain( 'webdoone-simple-image-widget', false, basename( dirname( __FILE__ ) ) . '/languages/' );
  }

  /**  
    * Front-end display of widget.
    *
    * @see WP_Widget::widget()
    *
    * @param array $args     Widget arguments.
    * @param array $instance Saved values from database.
    */
  public function widget( $args, $instance ) {
    extract( $args );

    $title = $image = $description = $image_url = $image_size = $button_txt = null;

    if (! empty($instance['title'])) {
      $title = apply_filters('widget_title', $instance['title']);
    } 

    if( !empty($instance['image'])) {
      $image = esc_attr($instance['image']);
    }
    if (!empty($instance['image_url'])) {
      $image_url = esc_url($instance['image_url']);
    }
    if (!empty($instance['image_size'])) {
      $image_size = esc_attr($instance['image_size']);
    }

    if (! empty($instance['description'])) {
      $description = $instance['description'];
    }

    if (! empty($instance['button_txt'])) {
      $button_txt = $instance['button_txt'];
    }

    $allowed_tags = wp_kses_allowed_html( 'post' );
    echo wp_kses($args['before_widget'], $allowed_tags); 
    if (!empty($title)) {
      echo wp_kses($args['before_title'], $allowed_tags) . esc_html($title) . wp_kses($args['after_title'], $allowed_tags);
    } ?>
    <div class="image-container"><?php 
      if ($image) {
        $image_id = webdoone_get_attachment_id_from_src($image);
        $img = wp_get_attachment_image_src($image_id, $image_size);
        $img_srcset = wp_get_attachment_image_srcset($image_id, $image_size); 
        $thumb_id = get_post_thumbnail_id(get_the_ID());
        $alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);

        if ($image_url) { ?>
        <a href="<?php echo esc_url($image_url); ?>" <?php 
          if( 1 == $instance['new_window'] ) { 
            echo 'target="_blank"'; 
          } ?> >
          <img <?php
          if( 1 == $instance['checkbox'] ) { 
            echo 'style="max-width: 100%; height: auto;"'; 
          } ?>
          width="<?php echo esc_attr($img[1]); ?>" height="<?php echo esc_attr($img[2]); ?>" src=" <?php echo esc_url($img[0]);?>" srcset="<?php echo esc_attr($img_srcset);?>" sizes="(max-width:  <?php echo esc_attr($img[1]); ?>px) 100vw, <?php echo esc_attr($img[1]); ?>px" class="<?php echo esc_attr($image_size); ?>" alt="<?php if(count($alt)) echo $alt; ?>"></a><?php
        } else { ?>
          <img <?php
          if( 1 == $instance['checkbox'] ) { 
            echo 'style="max-width: 100%; height: auto;"'; 
          } ?>
          width="<?php echo esc_attr($img[1]); ?>" height="<?php echo esc_attr($img[2]); ?>" src=" <?php echo esc_url($img[0]);?>" srcset="<?php echo esc_attr($img_srcset);?>" sizes="(max-width:  <?php echo esc_attr($img[1]); ?>px) 100vw, <?php echo esc_attr($img[1]); ?>px" class="<?php echo esc_attr($image_size); ?>" alt="<?php if(count($alt)) echo $alt; ?>"><?php
          if( 1 == $instance['new_window'] ) {
            echo 'target="_blank"';
          } 
        }
      } ?>
    </div>
    <div class="description"><?php
    if ($description) { 
      echo '<p>'. esc_textarea($description) .'</p>';
    } 
    if ($button_txt) { ?>
      <a href="<?php echo esc_url($image_url); ?>" <?php if( 1 == $instance['new_window'] ) { echo 'target="_blank"'; } ?> >
      <?php echo esc_html($button_txt); ?></a><?php
    } ?>
    </div><?php

    echo wp_kses($args['after_widget'], $allowed_tags);
  }

  /**
    * Back-end widget form.
    *
    * @see WP_Widget::form()
    *
    * @param array $instance Previously saved values from database.
    */
  public function form( $instance ) {
    /* Set up some default widget settings. */
    $defaults = array( 
      'title' => '', 
      'image' => '', 
      'image_url' => '', 
      'image_size' => '', 
      'description' => '', 
      'new_window' => '',
      'checkbox' => '',
      'button_txt' => '' 
    );
    $instance = wp_parse_args( (array) $instance, $defaults );
    $isimage = $instance['image'];
    $image_size = esc_attr($instance['image_size']); ?>

    <div class="webdoone-simple-img">
      <p>
        <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'webdoone-simple-image-widget'); ?></label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
      </p>
      <p>
        <img class="webdoone-si-custom-media-img" src="<?php if (!empty($instance['image'])) {
        echo esc_url($instance['image']); } ?>" style="margin:auto;padding:0;max-width:100%;display:block" />
      </p>
      <p>
        <input type="text" class="hidden widefat webdoone-si-custom-media-url" name="<?php echo esc_attr($this->get_field_name('image')); ?>" id="<?php echo esc_attr($this->get_field_id('image')); ?>" value="<?php echo esc_attr($instance['image']); ?>"  style="margin-bottom:5px;" /><?php 
        if (!$isimage) { ?>
          <a href="#" class="button webdoone-si-custom-media-upload"><?php esc_html_e('Upload image', 'webdoone-simple-image-widget'); ?></a>
          <a href="#" class="button webdoone-si-clear-field hidden"><?php esc_html_e('Remove image', 'webdoone-simple-image-widget'); ?></a><?php
        } else { ?>
          <a href="#" class="button webdoone-si-custom-media-upload hidden"><?php esc_html_e('Upload image', 'webdoone-simple-image-widget'); ?></a>
          <a href="#" class="button webdoone-si-clear-field"><?php esc_html_e('Remove image', 'webdoone-simple-image-widget'); ?></a><?php
        }; ?>
      </p>
      <p>
        <label for="<?php echo esc_attr($this->get_field_id('image_size')); ?>"><?php esc_html_e('Choose image size from all registerd:', 'webdoone-simple-image-widget'); ?></label>
        <select class="widefat" id="<?php echo esc_attr($this->get_field_id('image_size')); ?>" name="<?php echo esc_attr($this->get_field_name('image_size')); ?>">
          <option selected="selected"><?php echo esc_html__('Choose size or use oryginal','webdoone-simple-image-widget'); ?></option><?php
            $array = get_image_sizes();
            foreach ( $array as $key => $value ) { 
            if ( $value['height'] == '0' ) { $value['height'] = ' auto '; }
            if ( $value['width'] == '0' ) { $value['width'] = ' auto '; }
            if ( $value['crop'] =='1') { $value['crop'] = 'Crop'; } ?>
              <option value="<?php echo esc_attr($key); ?>" <?php echo esc_attr($image_size==$key)?'selected':''; ?>><?php echo esc_html( $key ); ?> (<?php echo esc_html( $value['height'] );?>&#215;<?php echo esc_html( $value['width'] );?>) <?php echo esc_html( $value['crop'] ); ?></option><?php
            }; ?>
        </select>
      </p>
      <p>
        <label>
          <input type="checkbox" id="<?php echo $this->get_field_id('checkbox'); ?>" name="<?php echo $this->get_field_name('checkbox'); ?>" value="1" <?php checked( 1, ($instance['checkbox']), true); ?> /><?php
            esc_html_e('Make responsive img', 'webdoone-simple-image-widget'); ?>
        </label>
      </p>
      <p>
        <label for="<?php echo esc_attr($this->get_field_id('image_url')); ?>"><?php esc_html_e('Link URL:', 'webdoone-simple-image-widget'); ?></label>
        <input class="widefat" type="url" id="<?php echo esc_attr($this->get_field_id('image_url')); ?>" name="<?php echo esc_attr($this->get_field_name('image_url')); ?>" value="<?php echo esc_url($instance['image_url']); ?>" />
      </p>
      <p>
        <label>
          <input type="checkbox" id="<?php echo $this->get_field_id('new_window'); ?>" name="<?php echo $this->get_field_name('new_window'); ?>" value="1" <?php checked( 1, ($instance['new_window']), true); ?> /><?php
            esc_html_e('Open in new window', 'webdoone-simple-image-widget'); ?>
        </label>
      </p>
      <p>
        <label for="<?php echo esc_attr($this->get_field_id('description')); ?>"><?php esc_html_e('Description:', 'webdoone-simple-image-widget'); ?></label>
        <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('description')); ?>" name="<?php echo esc_attr($this->get_field_name('description')); ?>" value="<?php echo esc_attr($instance['description']); ?>" rows="6"><?php echo esc_textarea($instance['description']); ?></textarea>
      </p>
      <p>
        <label for="<?php echo esc_attr($this->get_field_id('button_txt')); ?>"><?php esc_html_e('Button text:', 'webdoone-simple-image-widget'); ?></label>
        <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('button_txt')); ?>" name="<?php echo esc_attr($this->get_field_name('button_txt')); ?>" value="<?php echo esc_attr($instance['button_txt']); ?>" />
      </p>
    </div><?php
  }

  /**
    * Sanitize widget form values as they are saved.
    *
    * @see WP_Widget::update()
    *
    * @param array $new_instance Values just sent to be saved.
    * @param array $old_instance Previously saved values from database.
    *
    * @return array Updated safe values to be saved.
    */
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['image'] = strip_tags($new_instance['image']);
    $instance['image_url'] =  strip_tags($new_instance['image_url']);
    $instance['image_size'] =  strip_tags($new_instance['image_size']);
    $instance['description'] = strip_tags($new_instance['description']);
    $instance['button_txt'] = strip_tags($new_instance['button_txt']);
    $instance['new_window'] = $new_instance['new_window'];
    $instance['checkbox'] = $new_instance['checkbox'];
    return $instance;
  }
}
add_action('widgets_init', create_function('', 'register_widget( "WebdooneSimpleImage_Widget" );'));

// Enqueue needed scripts
if ( !function_exists('webdoone_admin_script') ) {
  function webdoone_admin_script()
  {
    wp_enqueue_media();
    wp_enqueue_script('webdoone-si', plugin_dir_url( __FILE__ ) . '/js/admin-scripts.js');
  }
  add_action('admin_enqueue_scripts', 'webdoone_admin_script');
};

// Get attachment ID from src
function webdoone_get_attachment_id_from_src($image_src) {
  global $wpdb;
  $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
  $id = $wpdb->get_var($query);
  return $id;
}

/**
 * Get size information for all currently-registered image sizes.
 *
 * @global $_wp_additional_image_sizes
 * @uses   get_intermediate_image_sizes()
 * @return array $sizes Data for all currently-registered image sizes.
 */
function get_image_sizes() {
  global $_wp_additional_image_sizes;

  $sizes = array();

foreach ( get_intermediate_image_sizes() as $_size ) {
    if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
      $sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
      $sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
      $sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
    } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
      $sizes[ $_size ] = array(
        'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
        'height' => $_wp_additional_image_sizes[ $_size ]['height'],
        'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
      );
    }
  }
  return $sizes;
}
