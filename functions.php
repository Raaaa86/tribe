<?php
#-----------------------------------------------------------------#
# Load text domain
#-----------------------------------------------------------------#

add_action('after_setup_theme', 'respawn_lang');
/**
 * Register text domain
 */
function respawn_lang(){
    load_theme_textdomain('respawn', get_theme_file_path('lang'));
}

/* Custom code goes below this line. */

    require_once get_theme_file_path('includes.php');

    /*****ACTIONS*****/

    /*scripts*/
    add_action( 'wp_enqueue_scripts', 'respawn_scripts' );
    add_action( 'admin_enqueue_scripts', 'respawn_admin_scripts' );

    /*styles*/
    add_action( 'admin_enqueue_scripts', 'respawn_styles_admin');
    add_action( 'wp_enqueue_scripts', 'respawn_styles' );

    /*sidebars*/
    add_action( 'widgets_init', 'respawn_widgets_init' );

    /*redux*/
    if (is_admin()) {
        add_action( 'admin_menu', 'respawn_remove_redux_menu',12 );
    }


    /*menu**/
    add_action( 'init', 'respawn_register_my_menus' );

    /*plugins*/
    add_action( 'tgmpa_register', 'respawn_register_required_plugins' );

    /*categories*/
    add_action ('edited_category', 'respawn_save_extra_category_fileds');
    add_action('created_category', 'respawn_save_extra_category_fileds', 11, 1);
    add_action('category_edit_form_fields','respawn_extra_category_fields');
    add_action('category_add_form_fields', 'respawn_category_form_custom_field_add', 10 );

    /*gallery*/
    add_action( 'wp', 'respawn_grab_ids_from_gallery' );

    /*importer*/
    add_action( 'wbc_importer_after_content_import', 'respawn_import_additional_resources', 10, 2 );

    /*category images*/
    add_action('admin_init', 'respawn_cat_images_init');
    add_action('edit_term','respawn_ci_save_taxonomy_image');
    add_action('create_term','respawn_ci_save_taxonomy_image');

    // style the image in category list
    if(is_admin()){
        add_action('quick_edit_custom_box', 'respawn_ci_quick_edit_custom_box', 10, 3);
    }

    /*****FILTERS*****/

    /*menu*/
    add_filter( 'nav_menu_link_attributes', 'respawn_custom_nav_attributes', 10, 3 );

    /*gallery*/
    add_filter( 'rwmb_meta_boxes', 'respawn_gallery_meta_boxes' );

    /*metaboxes*/
    add_filter( 'rwmb_meta_boxes', 'respawn_page_meta_boxes' );

    /*body class*/
    add_filter( 'body_class','respawn_body_classes' );

    /*excerpt*/
    add_filter( 'excerpt_length', 'respawn_custom_excerpt_length', 999 );

    /*testimonials*/
    add_filter( 'cmb_meta_boxes', 'respawn_testimonial_setting_metaboxes' );

    global $respawn_version;
    $respawn_theme = wp_get_theme();
    $respawn_version = $respawn_theme->get( 'Version' );


#-----------------------------------------------------------------#
# Widget areas
#-----------------------------------------------------------------#

/**
 * Register sidebars
 */
function respawn_widgets_init()
{
    if (function_exists('register_sidebar')) {

        register_sidebar(array('name' => 'Blog Sidebar', 'id' => 'blog-sidebar', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>'));
        register_sidebar(array('name' => 'Page Sidebar', 'id' => 'page-sidebar', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>'));

        if (class_exists('WooCommerce')) {
            register_sidebar(array('name' => 'WooCommerce Sidebar', 'id' => 'woocommerce-sidebar', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>'));
        }

        register_sidebar(array('name' => 'Footer Widget Area 1', 'id' => 'footer_widget_one', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>'));
        register_sidebar(array('name' => 'Footer Widget Area 2', 'id' => 'footer_widget_two', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>'));
        register_sidebar(array('name' => 'Footer Widget Area 3', 'id' => 'footer_widget_three', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>'));
        register_sidebar(array('name' => 'Footer Widget Area 4', 'id' => 'footer_widget_four', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>'));

    }
}
#-----------------------------------------------------------------#
# Options panel
#-----------------------------------------------------------------#

/**
 * Return theme options
 * @return mixed|void
 */
function respawn_get_theme_options() {
    $current_options = get_option('respawn_redux');
    return $current_options;
}

if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
add_action('admin_enqueue_scripts', 'respawn_redux_deps');

    /**
     * Enqueue Redux styling
     */
    function respawn_redux_deps() {
    global $respawn_version;
    wp_enqueue_style('respawn-redux-admin', get_theme_file_uri('assets/css/respawn-redux-styling.css'), [], $respawn_version, 'all');
}
}


/**
 * Remove Redux menu
 */
function respawn_remove_redux_menu() {
    remove_submenu_page('tools.php','redux-about');
}


#-----------------------------------------------------------------#
# Styles
#-----------------------------------------------------------------#

/**
 * Register admin styles
 */
function respawn_styles_admin(){
    global $respawn_version;
    wp_enqueue_style( 'respawn-admin',  get_theme_file_uri('assets/css/admin.css'),  [], $respawn_version);
    wp_enqueue_style( 'fontawesome',  get_theme_file_uri('assets/css/fontawesome-all.css'), [], $respawn_version);
	
}

/**
 * Register theme styles
 */
function respawn_styles() {

    global $respawn_version, $post;
    $options = respawn_get_theme_options();

     wp_enqueue_style( 'respawn-style',  get_bloginfo( 'stylesheet_url' ),[], $respawn_version);
     wp_enqueue_style( 'fontawesome',  get_theme_file_uri('assets/css/fontawesome-all.css'), [], $respawn_version);

	 wp_enqueue_style( 'woo',  get_theme_file_uri('assets/css/woocommerce.css'), [], $respawn_version);
     wp_enqueue_style( 'bbpress',  get_theme_file_uri('assets/css/bbpress.css'), [], $respawn_version);

     wp_enqueue_style( 'respawn-main',  get_theme_file_uri('assets/css/main.css'), [], $respawn_version);
     wp_enqueue_style( 'respawn-responsive',  get_theme_file_uri('assets/css/responsive.css'), [], $respawn_version);
	 wp_enqueue_style( 'lineicons',  get_theme_file_uri('assets/css/simple-line-icons.css'), [], $respawn_version);

     wp_enqueue_style( 'effects',  get_theme_file_uri('assets/css/effects.css'), [], $respawn_version);

     if ( is_rtl() ){
        wp_register_style('respawn-rtl',   get_theme_file_uri('assets/css/rtl.css'),[],$respawn_version);
        wp_enqueue_style( 'respawn-rtl' );
     }

    /*page background*/
    $page_background_color_value = '';
    $page_background_color = '';
    if(isset($post->ID))
        $page_background_color = get_post_meta($post->ID, 'page-bck-color', true);
    if(!empty($page_background_color)) $page_background_color_value = $page_background_color;

    if(!empty($page_background_color_value)){

        $custom_css = "
     body{ background-color: $page_background_color_value !important; }";

        wp_add_inline_style( 'respawn-style', esc_html($custom_css) );

    }

    $respawn_dynamic_style = '';
    require_once (get_theme_file_path('assets/css/style_dynamic.php'));
    wp_add_inline_style( 'respawn-style', $respawn_dynamic_style);

    $categories = get_categories();

    foreach ($categories as $category) {

        $cat_data = get_option("category_$category->term_id");

        if (isset($cat_data['catBG']) && !empty($cat_data['catBG'])) {

            $cat_data_bg1 = $cat_data['catBG'];
            $cat_data_bg = str_replace("#", "", $cat_data['catBG']);

            $data = "

            .cat_color_" . esc_attr($cat_data_bg) . "_border{
             border: 2px solid " . esc_attr($cat_data_bg1) . ";
            }

            .cat_color_" . esc_attr($cat_data_bg) . "_background{
             background: " . esc_attr($cat_data_bg1) . ";
            }

            .cat_color_" . esc_attr($cat_data_bg) . "_background_color{
             background-color: " . esc_attr($cat_data_bg1) . " !important;
            }

            .cat_color_" . esc_attr($cat_data_bg) . "_color{
             color: " . esc_attr($cat_data_bg1) . ";
            }

            ";

            wp_add_inline_style('respawn-style', $data);

        } else {

            if (isset($options['general-settings-color-selector']) && !empty($options['general-settings-color-selector'])) {
                $cat_data['catBG'] = $options['general-settings-color-selector'];
            } else {
                $cat_data['catBG'] = '#696bff';
            }

            $cat_data_bg1 = $cat_data['catBG'];
            $cat_data_bg = str_replace("#", "", $cat_data['catBG']);

            $data = "

            .cat_color_" . esc_attr($cat_data_bg) . "_border{
             border: 2px solid " . esc_attr($cat_data_bg1) . ";
            }

            .cat_color_" . esc_attr($cat_data_bg) . "_background{
             background: " . esc_attr($cat_data_bg1) . ";
            }

            .cat_color_" . esc_attr($cat_data_bg) . "_background_color{
             background-color: " . esc_attr($cat_data_bg1) . " !important;
            }

            .cat_color_" . esc_attr($cat_data_bg) . "_color{
             color: " . esc_attr($cat_data_bg1) . ";
            }

            ";

            wp_add_inline_style('respawn-style', $data);
        }
    }

    $args = [
        'post_type' => 'player',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ];

    $players = get_posts( $args );

    foreach ($players as $player){

        $image = get_the_post_thumbnail_url($player->ID);

        if(isset($image) && !empty($image)) {

            $data = "

            .player_" . esc_attr($player->ID) . "{
             background-image: url(" . esc_url($image) . ");
            }
            ";

            wp_add_inline_style('respawn-style', $data);
        }
    }


    $kategorije = '';
    if(isset($options['home_slider_categories']) or !empty($options['home_slider_categories']))$kategorije = $options['home_slider_categories'];

    $orderby = 'date';
    if(isset($options['slider-post-orderby']) or !empty($options['slider-post-orderby']))$orderby = $options['slider-post-orderby'];

    $order = 'date';
    if(isset($options['slider-post-order']) or !empty($options['slider-post-order']))$order = $options['slider-post-order'];

    $selected_cats = array();

    if(!is_array($kategorije))$kategorije = array();

    foreach ($kategorije as $key => $cat) {
        if($cat == '1')$selected_cats[] = $key;
    }
    if(!isset($num_posts) && empty($num_posts))$num_posts =5;

    if(isset($selected_cats) && !empty($selected_cats)){
        $args = array(
            'posts_per_page'   => $num_posts,
            'category__in'    => $selected_cats,
            'orderby'          => $orderby,
            'order'            => $order,
        );
    }else{
        $args = array(
            'posts_per_page'   => $num_posts,
            'orderby'          => $orderby,
            'order'            => $order,
        );
    }

    $postovi = get_posts( $args );

    foreach ($postovi as $posti) {
       $image = get_the_post_thumbnail_url($posti->ID);
       if(empty($image)){ $image = get_theme_file_uri('assets/img/defaults/default.jpg');  }

       $data = "
            .slide__img.pid_".esc_attr($posti->ID)."{
             background-image: url(".esc_url($image).");
            }
       ";

        wp_add_inline_style( 'respawn-style', $data );
    }

    if(is_page()){
        global $post;
        $image = get_the_post_thumbnail_url($post->ID);

        if(!empty($image)) {
            $data = "
            body{
             background-image: url(" . esc_url($image) . ") !important;
            }
            ";

            wp_add_inline_style('respawn-style', $data);
        }

    }
    $shape_page = '';
    if(isset($post->ID))
    $shape_page = get_post_meta($post->ID, 'page-shape-type', true);

    $shape_global = '';
    if(isset($options['shape-type']))
    $shape_global = $options['shape-type'];

    $page_style = '';
    $shape_page_height = $shape_page_front = $shape_page_color= '';

    if(isset($post->ID)) {
        $shape_page_height = get_post_meta($post->ID, 'page-shape-height', true);
        $shape_page_color = get_post_meta($post->ID, 'page-shape-color', true);
        $shape_page_front = get_post_meta($post->ID, 'page-shape-front', true);
    }

    $shape_page_front_value = ($shape_page_front == '1') ? 'true' : 'false';

    if(!empty($shape_page_height)) $page_style .= "height: ".$shape_page_height."px; ";

    $global_style = '';
    $front_global = '';
    $front_page = '';

    $shape_global_height = '';
    if(isset($options['shape-height']))
    $shape_global_height = $options['shape-height'];

    $shape_global_color = '';
    if(isset($options['shape-color']))
    $shape_global_color = $options['shape-color'];

    $shape_global_front = '';
    if(isset($options['shape-front']))
    $shape_global_front = $options['shape-front'];


    if(!empty($shape_global_height)) $global_style .= "height: ".$shape_global_height."px; ";

    if($shape_global_front){
        $front_global =  ".elementor-shape-global{ z-index: 2; pointer-events: none;}";
    }

    if($shape_page_front_value == 'true'){
        $front_page =  ".elementor-shape-page{ z-index: 2; pointer-events: none;}";
    }

    if(!empty($shape_page) && $shape_page != 'none' ){

        $data = "
            .custom-shape.elementor-shape svg{
                ".esc_html($page_style).";
            }
            .elementor-shape-footer .elementor-shape-fill{
                fill: ".esc_html($shape_page_color)." !important;
            }
            ".esc_html($front_page)."
           ";

        wp_add_inline_style('respawn-style', $data);

    }elseif(!empty($shape_global) && $shape_global != 'none' ){

        $data = "
            .custom-shape.elementor-shape svg{
                ".esc_html($global_style).";
            }
            .elementor-shape-footer .elementor-shape-fill{
                fill: ".esc_html($shape_global_color)." !important;
            }
            ".esc_html($front_global)."
           ";

        wp_add_inline_style('respawn-style', $data);
    }

}

#-----------------------------------------------------------------#
# Scripts
#-----------------------------------------------------------------#

/**
 * Register scripts
 */
function respawn_scripts() {
    global $respawn_version;
    $options = respawn_get_theme_options();

    wp_enqueue_script( 'tippy',   get_theme_file_uri('assets/js/tippy.min.js'),['jquery'],$respawn_version,true);

    if(is_page_template('tmp-homepage.php')){
        wp_enqueue_script( 'respawn-home',  get_theme_file_uri('assets/js/respawn_home_tween_pslider.min.js'),['jquery'],$respawn_version,true);
    }

    wp_enqueue_script( 'respawn-minified', get_theme_file_uri('assets/js/respawn_minified.min.js'), ['jquery'],$respawn_version,true);

    $settingsGlobal = array(
        'fixedMenu' => $options['header-position'],
        'menuLayout' => $options['header-settings-layout-menu'],
        'onePage' => $options['general-settings-one-page'],
        'ajaxurl' => esc_url(admin_url( 'admin-ajax.php' )),
        'searchFor' => esc_html__('Search for...', 'respawn'),
        'blog_feed' => $options['blog-feed-template'],
        'megaMenuActive' => class_exists('Mega_Menu')
    );

    wp_enqueue_script( 'respawn-global', get_theme_file_uri('assets/js/global.js'), ['jquery','masonry','respawn-minified'],$respawn_version,true);
    wp_localize_script('respawn-global', 'settingsGlobal', $settingsGlobal);

}


/**
 * Register admin scripts
 */
function respawn_admin_scripts() {
    global $respawn_version;
    $post_id = get_the_ID();

    wp_enqueue_style( 'wp-color-picker');
    wp_enqueue_script( 'wp-color-picker');

    $settingsAdmin = array(
        'post_id' => esc_html($post_id)
    );
    wp_enqueue_script('respawn-admin',   get_theme_file_uri('assets/js/admin.js'),['jquery-ui-datepicker'],$respawn_version,false);
    wp_localize_script('respawn-admin', 'settingsAdmin', $settingsAdmin);
}


/**
 *Register theme location menu
 */
function respawn_register_my_menus() {
  register_nav_menus(
    array(
      'header-menu' => esc_html__( 'Header Menu' , 'respawn'),
      )
  );
}


/**
 * Menu data-hover title
 * @param $atts
 * @param $item
 * @return mixed
 */
function respawn_custom_nav_attributes ($atts, $item ) {
    $atts['data-hover'] = $item->post_title;
    return $atts;
}


/**
 * Color converter
 * @param $hex
 * @return array
 */
function respawn_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}


#-----------------------------------------------------------------#
# Random functions
#-----------------------------------------------------------------#


/**
 * Print array values
 * @param $array
 */
function respawn_print_array($array){
    if(is_array($array))
    foreach ($array as $key => $value) {
        echo esc_attr($value). ' ';
    }
}

/**
 * Image resize function
 * @param $url
 * @param null $width
 * @param null $height
 * @param null $crop
 * @param bool $single
 * @param bool $upscale
 * @return array|bool|string
 */
function respawn_aq_resize($url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {

    // Validate inputs.
    if ( ! $url || ( ! $width && ! $height ) ) return false;

    // Caipt'n, ready to hook.
    if ( true === $upscale ) add_filter( 'image_resize_dimensions', 'respawn_aq_upscale', 10, 6 );

    // Define upload path & dir.
    $upload_info = wp_upload_dir();
    $upload_dir = $upload_info['basedir'];
    $upload_url = $upload_info['baseurl'];

    $http_prefix = "https://";
    $https_prefix = "https://";

    /* if the $url scheme differs from $upload_url scheme, make them match
       if the schemes differe, images don't show up. */
    if(!strncmp($url,$https_prefix,strlen($https_prefix))){ //if url begins with https:// make $upload_url begin with https:// as well
        $upload_url = str_replace($http_prefix,$https_prefix,$upload_url);
    }
    elseif(!strncmp($url,$http_prefix,strlen($http_prefix))){ //if url begins with https:// make $upload_url begin with https:// as well
        $upload_url = str_replace($https_prefix,$http_prefix,$upload_url);
    }


    // Check if $img_url is local.
    if ( false === strpos( $url, $upload_url ) ) return false;

    // Define path of image.
    $rel_path = str_replace( $upload_url, '', $url );
    $img_path = $upload_dir . $rel_path;

    // Check if img path exists, and is an image indeed.
    if ( ! file_exists( $img_path ) or ! getimagesize( $img_path ) ) return false;

    // Get image info.
    $info = pathinfo( $img_path );
    $ext = $info['extension'];
    list( $orig_w, $orig_h ) = getimagesize( $img_path );

    // Get image size after cropping.
    $dims = image_resize_dimensions( $orig_w, $orig_h, $width, $height, $crop );
    $dst_w = $dims[4];
    $dst_h = $dims[5];

    // Return the original image only if it exactly fits the needed measures.
    if ( ! $dims && ( ( ( null === $height && $orig_w == $width ) xor ( null === $width && $orig_h == $height ) ) xor ( $height == $orig_h && $width == $orig_w ) ) ) {
        $img_url = $url;
        $dst_w = $orig_w;
        $dst_h = $orig_h;
    } else {
        // Use this to check if cropped image already exists, so we can return that instead.
        $suffix = "{$dst_w}x{$dst_h}";
        $dst_rel_path = str_replace( '.' . $ext, '', $rel_path );
        $destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

        if ( ! $dims || ( true == $crop && false == $upscale && ( $dst_w < $width || $dst_h < $height ) ) ) {
            // Can't resize, so return false saying that the action to do could not be processed as planned.
            return false;
        }
        // Else check if cache exists.
        elseif ( file_exists( $destfilename ) && getimagesize( $destfilename ) ) {
            $img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
        }
        // Else, we resize the image and return the new resized image url.
        else {

            // Note: This pre-3.5 fallback check will edited out in subsequent version.
            if ( function_exists( 'wp_get_image_editor' ) ) {

                $editor = wp_get_image_editor( $img_path );

                if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) )
                    return false;

                $resized_file = $editor->save();

                if ( ! is_wp_error( $resized_file ) ) {
                    $resized_rel_path = str_replace( $upload_dir, '', $resized_file['path'] );
                    $img_url = $upload_url . $resized_rel_path;
                } else {
                    return false;
                }

            } else {

                $editor = wp_get_image_editor( $img_path); // Fallback foo.

                if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) )
                    return false;

                $resized_img_path = $editor->save();

                if ( ! is_wp_error( $resized_img_path ) ) {
                    $resized_rel_path = str_replace( $upload_dir, '', $resized_img_path );
                    $img_url = $upload_url . $resized_rel_path;
                } else {
                    return false;
                }

            }

        }
    }

    // Return the output.
    if ( $single ) {
        // str return.
        $image = $img_url;
    } else {
        // array return.
        $image = array (
            0 => $img_url,
            1 => $dst_w,
            2 => $dst_h
        );
    }

    return $image;
}


/**
 * Image upscale function
 * @param $default
 * @param $orig_w
 * @param $orig_h
 * @param $dest_w
 * @param $dest_h
 * @param $crop
 * @return array|null
 */
function respawn_aq_upscale($default, $orig_w, $orig_h, $dest_w, $dest_h, $crop ) {
    if ( ! $crop ) return null; // Let the wordpress default function handle this.

    if( false )
        return $default;
    // Here is the point we allow to use larger image size than the original one.
    $aspect_ratio = $orig_w / $orig_h;
    $new_w = $dest_w;
    $new_h = $dest_h;

    if ( ! $new_w ) {
        $new_w = intval( $new_h * $aspect_ratio );
    }

    if ( ! $new_h ) {
        $new_h = intval( $new_w / $aspect_ratio );
    }

    $size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

    $crop_w = round( $new_w / $size_ratio );
    $crop_h = round( $new_h / $size_ratio );

    $s_x = floor( ( $orig_w - $crop_w ) / 2 );
    $s_y = floor( ( $orig_h - $crop_h ) / 2 );

    return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
}


/**
 * Custom excerpt
 * @param $limit
 * @param null $post_id
 * @return string
 */
function respawn_excerpt($limit, $post_id = null) {

    if(has_excerpt($post_id)) {
        $the_excerpt = get_the_excerpt($post_id);
        $the_excerpt = preg_replace('/\[[^\]]+\]/', '', $the_excerpt);
        return wp_trim_words($the_excerpt, $limit);
    } else {
        $the_content = get_the_content($post_id);
        $the_content = preg_replace('/\[[^\]]+\]/', '', $the_content);
        return wp_trim_words($the_content, $limit);
    }
}



#-----------------------------------------------------------------#
# Metaboxes
#-----------------------------------------------------------------#


/**
 * Gallery metaboxes
 * @param $meta_boxes
 * @return array
 */
function respawn_gallery_meta_boxes($meta_boxes ) {
    $meta_boxes[] = array(
        'title'      => esc_html__( 'Gallery Images', 'respawn' ),
        'post_types' => 'masonry_gallery',
        'fields'     => array(
            array(
                'id'               => 'image_upload',
                'name'             => esc_html__( 'Images Upload', 'respawn' ),
                'type'             => 'image_advanced',
                // Delete image from Media Library when remove it from post meta?
                // Note: it might affect other posts if you use same image for multiple posts
                'force_delete'     => false,
                // Display the "Uploaded 1/2 files" status
                'max_status'       => true,
                'multiple'       => true,
            ),

        ),
    );
    return $meta_boxes;
}


/**
 * Page metaboxes
 * @param $meta_boxes
 * @return array
 */
function respawn_page_meta_boxes($meta_boxes ) {

    /*MIXED*/
    $meta_boxes[] = array(
        'title'      => esc_html__( 'Page Header', 'respawn' ),
        'post_types' => array('page','player','team'),
        'fields'     => array(
        array(
                'type'    => 'select',
                'id'      => 'page_header',
                'name'    => esc_html__( 'Show Page Header', 'respawn' ),
                'options' => array(
                    '' => '',
                    'yes' => esc_html__( 'Yes', 'respawn' ),
                    'no' => esc_html__( 'No', 'respawn' )
                ),
                'flatten' => false,
                'desc'   => esc_html__( 'Use this option to turn header on/off.', 'respawn' ),
            ),
            array(
                'type'    => 'image_advanced',
                'id'      => 'page_header_image',
                'name'    => esc_html__( 'Header Image', 'respawn' ),
                'desc'      => esc_html__( 'Choose header image.', 'respawn' ),

            ),
           array(
                'type'    => 'select',
                'id'      => 'page_subtitle',
                'name'    => esc_html__( 'Page Subtitle Shows', 'respawn' ),
                'options' => array(
                    '' => '',
                   'nothing' => esc_html__( 'Nothing', 'respawn' ) ,
                   'subtitle'=> esc_html__( 'Subtitle', 'respawn' ) ,
                   'breadcrumbs' => esc_html__( 'Breadcrumbs', 'respawn' ),
                ),
                'flatten' => false,
            ),
            array(
                'type'    => 'input',
                'id'      => 'page_subtitle_text',
                'name'    => esc_html__( 'Page Subtitle Text', 'respawn' ),
                'desc'      => esc_html__( 'If you add value here it will override global settings.', 'respawn' ),

            ),


        ),
    );

    $meta_boxes[] = array(
        'title'      => esc_html__( 'Page Color', 'respawn' ),
        'post_types' => array('page'),
        'fields'     => array(
           array(
                'type'    => 'color',
                'id'      => 'page-bck-color',
                'name'    => esc_html__( 'Page Background Color', 'respawn' )
            ),
        ),
    );

    $meta_boxes[] = array(
        'title'      => esc_html__( 'Shape Divider', 'respawn' ),
        'post_types' => array('page','player','team','post'),
        'fields'     => array(
            array(
                'type'    => 'select',
                'id'      => 'page-shape-type',
                'name'    => esc_html__( 'Type', 'respawn' ),
                'options' => array(
                   'none'=> esc_html__( 'None', 'respawn' ),
                   'mountains' => esc_html__( 'Mountains', 'respawn' ),
                   'drops'=> esc_html__( 'Drops', 'respawn' ),
                   'clouds' => esc_html__( 'Clouds', 'respawn' ) ,
                   'zigzag' => esc_html__( 'Zigzag', 'respawn' ) ,
                   'pyramids' => esc_html__( 'Pyramids', 'respawn'  ),
                   'triangle' => esc_html__( 'Triangle', 'respawn'  ),
                   'triangle-asymmetrical' => esc_html__( 'Triangle Asymmetrical', 'respawn'  ),
                   'tilt' => esc_html__( 'Tilt', 'respawn' ),
                   'opacity-tilt' => esc_html__( 'Tilt Opacity', 'respawn' ),
                   'opacity-fan' => esc_html__( 'Fan Opacity', 'respawn'  ),
                   'curve-asymmetrical' => esc_html__( 'Curve Asymmetrical', 'respawn'  ),
                   'waves' => esc_html__( 'Waves', 'respawn'  ),
                   'wave-brush' => esc_html__( 'Waves Brush', 'respawn' ),
                   'waves-pattern' => esc_html__( 'Waves Pattern', 'respawn' ),
                   'arrow' => esc_html__( 'Arrow', 'respawn' ),
                   'split' => esc_html__( 'Split', 'respawn' ),
                   'book' => esc_html__( 'Book', 'respawn' ),

                ),
            ),
            array(
                'type'    => 'color',
                'id'      => 'page-shape-color',
                'name'    => esc_html__( 'Color', 'respawn' )
            ),
            array(
                'type'    => 'slider',
                'id'      => 'page-shape-height',
                'name'    => esc_html__( 'Height', 'respawn' ),
                'js_options' => array(
                    'min'   => 0,
                    'max'   => 500,
                    'step'  => 1,
                ),
            ),
            array(
                'type'    => 'switch',
                'id'      => 'page-shape-front',
                'name'    => esc_html__( 'Bring to Front', 'respawn' )
            ),
        ),
    );


    /*POSTS*/
    $meta_boxes[] = array(
        'title'      => esc_html__( 'Quote Settings', 'respawn' ),
        'post_types' => 'post',
        'fields'     => array(
            array(
                'type'    => 'input',
                'id'      => 'quote-author',
                'name'    => esc_html__( 'Author', 'respawn' ),
                'desc'      => esc_html__('Add author of the quote.', 'respawn' ),

            ),
            array(
                'type'    => 'input',
                'id'      => 'quote-value',
                'name'    => esc_html__( 'Quote', 'respawn' ),
                'desc'      => esc_html__( 'Add quote.', 'respawn' ),

            ),
        ),
    );


    $meta_boxes[] = array(
        'title'      => esc_html__( 'Video Settings', 'respawn' ),
        'post_types' => 'post',
        'fields'     => array(
            array(
                'type'    => 'file_upload',
                'id'      => 'video-mp4-file',
                'name'    => esc_html__( 'MP4 File', 'respawn' ),
                'desc'      => esc_html__( 'Add MP4 file here.', 'respawn' ),

            ),
            array(
                'type'    => 'file_upload',
                'id'      => 'video-ogv-file',
                'name'    => esc_html__( 'OGV File', 'respawn' ),
                'desc'      => esc_html__( 'Add OGV file here.', 'respawn' ),

            ),

            array(
                'type'    => 'image_advanced',
                'id'      => 'video-preview',
                'name'    => esc_html__( 'Preview Image', 'respawn' ),
                'desc'      => esc_html__( 'Add video preview image here.', 'respawn' ),

            ),
            array(
                'type'    => 'textarea',
                'id'      => 'video-embed',
                'name'    => esc_html__( 'Embed Code', 'respawn' ),
                'desc'      => esc_html__( 'Please add YouTube or Vimeo embed code format here.', 'respawn' ),

            ),
        ),
    );


    $meta_boxes[] = array(
        'title'      => esc_html__( 'Audio Settings', 'respawn' ),
        'post_types' => 'post',
        'fields'     => array(
            array(
                'type'    => 'file_upload',
                'id'      => 'audio-file-mp3',
                'name'    => esc_html__( 'MP3 File', 'respawn' ),
                'desc'      => esc_html__( 'Add MP3 file here.', 'respawn' ),

            ),
            array(
                'type'    => 'textarea',
                'id'      => 'audio-embed',
                'name'    => esc_html__( 'Embed Code', 'respawn' ),
                'desc'      => esc_html__( 'Please add SoundCloud embed code format here.', 'respawn' ),

            ),
        ),
    );

    $meta_boxes[] = array(
        'title'      => esc_html__( 'Gallery Settings', 'respawn' ),
        'post_types' => 'post',
        'fields'     => array(
           array(
                'type'    => 'checkbox',
                'id'      => 'gallery-slider',
                'name'    => esc_html__( 'Gallery Slider', 'respawn' ),
                'desc'      => esc_html__( 'Check this if you want to use gallery as a slider. You will need to add basic Wordpress gallery using "Add media" button in post above.', 'respawn' ),

            )
        ),
    );

     $meta_boxes[] = array(
        'title'      => esc_html__( 'Post Header', 'respawn' ),
        'post_types' => array('post'),
        'fields'     => array(
        array(
                'type'    => 'select',
                'id'      => 'post_header',
                'name'    => esc_html__( 'Show Post Header', 'respawn' ),
                'options' => array(
                    '' => '' ,
                   'yes'=> esc_html__( 'Yes', 'respawn' ) ,
                   'no'=> esc_html__( 'No', 'respawn' ),
                ),
                'flatten' => false,
                'desc'   => esc_html__( 'Use this option to turn header on/off.', 'respawn' ),
            ),
            array(
                'type'    => 'image_advanced',
                'id'      => 'post_header_image',
                'name'    => esc_html__( 'Header Image', 'respawn' ),
                'desc'      => esc_html__( 'Choose header image.', 'respawn' ),

            ),
        ),
    );

    return $meta_boxes;
}


#-----------------------------------------------------------------#
# Plugins
#-----------------------------------------------------------------#

/**
 * Include required functions
 */
function respawn_register_required_plugins() {
    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        // This is an example of how to include a plugin pre-packaged with a theme
        array(
            'name'                  => esc_html__('Yoast SEO', 'respawn'), // The plugin name
            'slug'                  => 'wordpress-seo', // The plugin slug (typically the folder name)
            'required'              => false, // If false, the plugin is only 'recommended' instead of required
        ),
        array(
            'name'                  => esc_html__('Woocommerce', 'respawn'), // The plugin name
            'slug'                  => 'woocommerce', // The plugin slug (typically the folder name)
            'required'              => false, // If false, the plugin is only 'recommended' instead of required
        ),
        array(
            'name'                  => esc_html__('Max Mega Menu', 'respawn'), // The plugin name
            'slug'                  => 'megamenu', // The plugin slug (typically the folder name)
            'required'              => false, // If false, the plugin is only 'recommended' instead of required
        ),
        array(
            'name'                  => esc_html__('Redux framework', 'respawn'), // The plugin name
            'slug'                  => 'redux-framework', // The plugin slug (typically the folder name)
            'required'              => true, // If false, the plugin is only 'recommended' instead of required
        ),
        array(
            'name'                  => esc_html__('Elementor', 'respawn'), // The plugin name
            'slug'                  => 'elementor', // The plugin slug (typically the folder name)
            'required'              => true, // If false, the plugin is only 'recommended' instead of required
        ),
        array(
            'name'                  => esc_html__('Meta Box', 'respawn'), // The plugin name
            'slug'                  => 'meta-box', // The plugin slug (typically the folder name)
            'required'              => true, // If false, the plugin is only 'recommended' instead of required
        ),
        array(
            'name'                  => 'Respawn types', // The plugin name
            'slug'                  => 'respawn-types', // The plugin slug (typically the folder name)
            'source'                => get_theme_file_uri('assets/plugins/respawn-types.zip'), // The plugin source
            'required'              => true, // If false, the plugin is only 'recommended' instead of required
            'version'               => '1.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
        ),
        array(
            'name'                  => 'Theplus Elementor addon', // The plugin name
            'slug'                  => 'theplus_elementor_addon', // The plugin slug (typically the folder name)
            'source'                => get_theme_file_uri('assets/plugins/theplus_elementor_addon.zip'), // The plugin source
            'required'              => true, // If false, the plugin is only 'recommended' instead of required
            'version'               => '3.0.5', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
        ),
        array(
            'name'                  => 'Layer Slider', // The plugin name
            'slug'                  => 'LayerSlider', // The plugin slug (typically the folder name)
            'source'                => get_theme_file_uri('assets/plugins/LayerSlider.zip'), // The plugin source
            'required'              => false, // If false, the plugin is only 'recommended' instead of required
        ),

    );

    tgmpa( $plugins );
}

#-----------------------------------------------------------------#
# Pagination
#-----------------------------------------------------------------#

/**
 * Pagination function
 * @param string $pages
 * @param int $range
 * @return string
 */
function respawn_kriesi_pagination($pages = '', $range = 1){

    $showitems = ($range * 1)+1;
    global $paged;
    global $paginate;
    $stranica = $paged;
    if(empty($stranica)) $stranica = 1;
    if($pages == ''){
        global $wp_query;
        $pages = $wp_query->max_num_pages;
        if(!$pages){
            $pages = 1;
        }
    }

    if(1 != $pages){
        $leftpager= '&laquo;';
        $rightpager= '&raquo;';
        $paginate.= '<div class="pagination"><ul>';

        if($stranica > 2 && $stranica > $range+1 && $showitems < $pages) $paginate.=  "";
        if($stranica > 1 ) $paginate.=  "<li><a class='page-selector' href='".get_pagenum_link($stranica - 1)."'>". $leftpager. "</a></li>";


        for ($i=1; $i <= $pages; $i++){
            if (1 != $pages &&( !($i >= $stranica+$range+1 || $i <= $stranica-$range-1) || $pages <= $showitems )){
                $paginate.=  ($stranica == $i)? "<li class='active'><a href='".get_pagenum_link($i)."'>".$i."</a></li>":"<li><a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a></li>";
            }
        }

        if ($stranica < $pages ) $paginate.=  "<li><a class='page-selector' href='".get_pagenum_link($stranica + 1)."' >". $rightpager. "</a></li>";

        $paginate.= '</ul></div>';
    }
    return $paginate;
}

#-----------------------------------------------------------------#
# Categories
#-----------------------------------------------------------------#

/**
 * Add New Field To Category
 * @param $tag
 */
function respawn_extra_category_fields($tag ) {
    $t_id = $tag->term_id;
    $cat_meta = get_option( "category_$t_id" );
?>
<tr class="form-field">
    <th scope="row" valign="top"><label for="meta-color"><?php esc_html_e('Category Color', 'respawn'); ?></label></th>

    <td>
        <div id="colorpicker">
            <input title="catcolorpicker" type="text" name="cat_meta[catBG]" class="catcolorpicker" size="3" value="<?php echo (isset($cat_meta['catBG'])) ? esc_attr($cat_meta['catBG']) : '#fff'; ?>" />
        </div>
            <br />
        <span class="description"> </span>
            <br />
    </td>
</tr>
<?php
}

/** Add Colorpicker Field to "Add New Category" Form **/
function respawn_category_form_custom_field_add() {
?>
<div class="form-field">
    <label for="category_custom_color"><?php esc_html_e('Color', 'respawn'); ?></label>
    <input title="catcolorpicker" name="cat_meta[catBG]" class="catcolorpicker" type="text" value="" />
    <p class="description"><?php esc_html_e('Pick a Category Color', 'respawn'); ?></p>
</div>
<?php
}


/**
 * Save extra category field
 * @param $term_id
 */
function respawn_save_extra_category_fileds($term_id ) {

    if ( isset( $_POST['cat_meta'] ) ) {
        $t_id = $term_id;
        $cat_meta = get_option( "category_$t_id");
        $cat_keys = array_keys($_POST['cat_meta']);
            foreach ($cat_keys as $key){
            if (isset($_POST['cat_meta'][$key])){
                $cat_meta[$key] = $_POST['cat_meta'][$key];
            }
        }
        //save the option array
        update_option( "category_$t_id", $cat_meta );
    }
}


/**
 * Get gallery ids
 * @param $post_id
 * @return array
 */
function respawn_grab_ids_from_gallery($post_id) {

    if(isset($post_id)){
        $post = get_post($post_id);
    }else{
        global $post;
    }

    if($post != null) {

        $pattern = '\[(\[?)(gallery)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
        $ids =[];
        if (preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) ) {

            $count=count($matches[3]);      //in case there is more than one gallery in the post.
            for ($i = 0; $i < $count; $i++){
                $atts = shortcode_parse_atts( $matches[3][$i] );
                if ( isset( $atts['ids'] ) ){
                    $attachment_ids = explode( ',', $atts['ids'] );
                    $ids = array_merge($ids, $attachment_ids);
                }
            }
        }

    return $ids;
  } else {
    $ids =[];
    return $ids;
  }


}


/**
 * Register custom pages, prep function
 * @param $page_slug
 * @return int|null
 */
function respawn_get_ID_by_slug($page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}

/**
 * Set menus and front page for importer
 * @param $demo_active_import
 * @param $demo_directory_path
 */
function respawn_import_additional_resources($demo_active_import, $demo_directory_path) {
    reset( $demo_active_import );
    $current_key = key( $demo_active_import );

    /************************************************************************
     * Import slider
     *************************************************************************/

    if ( class_exists( 'LS_Config' ) ) {
        include LS_ROOT_PATH.'/classes/class.ls.importutil.php';

        //If it's demo3 or demo5
        $wbc_sliders_array = array(
            'Player' => array('player-layer.zip','player-layer-mobile.zip'), //Set slider zip name
            'Shop' => 'shop-layer.zip', //Set slider zip name
            'Streamer' => 'streamer-layer.zip', //Set slider zip name
        );

        if ( isset( $demo_active_import[$current_key]['directory'] ) && !empty( $demo_active_import[$current_key]['directory'] ) && array_key_exists( $demo_active_import[$current_key]['directory'], $wbc_sliders_array ) ) {
            $wbc_slider_import = $wbc_sliders_array[$demo_active_import[$current_key]['directory']];

            if( is_array( $wbc_slider_import ) ){
                foreach ($wbc_slider_import as $slider_zip) {
                    if ( !empty($slider_zip) && file_exists( $demo_directory_path.$slider_zip ) ) {
                        $slider = new LS_ImportUtil($demo_directory_path.$slider_zip );
                    }
                }
            }else{
                if ( file_exists( $demo_directory_path.$wbc_slider_import ) ) {
                    $slider = new LS_ImportUtil($demo_directory_path . $wbc_slider_import);
                }
            }
        }

    }

    /************************************************************************
    * Setting Menus
    *************************************************************************/
    // If it's demo1 - demo6
    $wbc_menu_array = array( 'Esports', 'Magazine', 'Event', 'Player', 'Shop', 'Streamer', 'Matches');
    if ( isset( $demo_active_import[$current_key]['directory'] ) && !empty( $demo_active_import[$current_key]['directory'] ) && in_array( $demo_active_import[$current_key]['directory'], $wbc_menu_array ) ) {
        $top_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
        if ( isset( $top_menu->term_id ) ) {
            set_theme_mod( 'nav_menu_locations', array(
                    'header-menu' => $top_menu->term_id,
                )
            );
        }
    }
    /************************************************************************
    * Set HomePage
    *************************************************************************/
    // array of demos/homepages to check/select from
    $wbc_home_pages = array(
        'Esports' => esc_html__('Homepage', 'respawn'),
        'Magazine' =>esc_html__('Homepage Magazine','respawn'),
        'Event' => esc_html__('Event Homepage','respawn'),
        'Player' => esc_html__('Player home','respawn'),
        'Shop' => esc_html__('Shop Homepage','respawn'),
        'Streamer' => esc_html__('Homepage','respawn'),
        'Matches' => esc_html__('Homepage','respawn'),
    );
    if ( isset( $demo_active_import[$current_key]['directory'] ) && !empty( $demo_active_import[$current_key]['directory'] ) && array_key_exists( $demo_active_import[$current_key]['directory'], $wbc_home_pages ) ) {
        $page = get_page_by_title( $wbc_home_pages[$demo_active_import[$current_key]['directory']] );
        if ( isset( $page->ID ) ) {
            update_option( 'page_on_front', $page->ID );
            update_option( 'show_on_front', 'page' );
        }
    }

    /************************************************************************
     * Import games
     *************************************************************************/
    global $wpdb;
    $games = $wpdb->prefix."cw_games";
    $result = $wpdb->get_results("SELECT `id` from $games WHERE `id` IS NOT NULL");

    if(count($result) == 0)
    {
        $wpdb->query( $wpdb->prepare("INSERT INTO $games (`id`, `title`, `abbr`, `icon`, `g_banner_file`, `store_id`) VALUES 
          ( %d, %s, %s, %d, %d, %s),
          ( %d, %s, %s, %d, %d, %s),
          ( %d, %s, %s, %d, %d, %s),
          ( %d, %s, %s, %d, %d, %s),
          ( %d, %s, %s, %d, %d, %s)",
            1, 'Counter Strike: GO', 'CS:GO', 2987, 3231, '5b7b08775cab30001458827e',
            2, 'Fortnite', 'FN', 3016, 73467, '5b7b08385cab30001458827a',
            3, 'Dota 2', 'Dota 2', 2985, 2986, '5b7b081e5cab300014588278',
            4, 'League of Legends', 'LoL', 2990, 3086, '',
            5, 'Overwatch', 'OW', 2992, 3087, ''));

    }

    /************************************************************************
     * Import maps
     *************************************************************************/
    $maps = $wpdb->prefix."cw_maps";
    $result = $wpdb->get_results("SELECT `id` from $maps WHERE `id` IS NOT NULL");

    if(count($result) == 0)
    {
        $wpdb->query( $wpdb->prepare("INSERT INTO $maps (`id`, `game_id`, `title`, `screenshot`) VALUES 
          ( %d, %d, %s, %d),( %d, %d, %s, %d),( %d, %d, %s, %d),( %d, %d, %s, %d),
          ( %d, %d, %s, %d),( %d, %d, %s, %d),( %d, %d, %s, %d),( %d, %d, %s, %d),
          ( %d, %d, %s, %d),( %d, %d, %s, %d),( %d, %d, %s, %d),( %d, %d, %s, %d)",
            1, 1, 'Office', 2967, 2, 1, 'Cache', 2968, 3, 1, 'Cobblestone', 2969,4, 1, 'Dust2', 2970,5, 1, 'Inferno', 2971,
            6, 1, 'Mirage', 2973,7, 1, 'Nuke', 2974,9, 1, 'Overpass', 2976,18, 4, 'Howling Abyss', 3924,17, 2, 'Snow', 3018,
            12, 1, 'Train', 2978,13, 3, 'Main', 2981),14, 4, 'Summoner\'s Rift', 2994,15, 5, 'Hanamura', 2995,16, 5, 'Busan', 2996);
    }
}

/**
 * Add body classes
 * @param $classes
 * @return array
 */
function respawn_body_classes($classes ) {
    $options = respawn_get_theme_options();

    $header_position_class = '';
    if($options['header-position'] == 'fixed'){ $header_position_class = 'has-fixed-header';}


    $background_repeat_class = '';
    if(isset($options['body_background_repeat']) && $options['body_background_repeat'] == 1)
    $background_repeat_class = 'background-repeat';

    $background_fixed_class = '';
    if(isset($options['body_background_fixed']) && $options['body_background_fixed'] == 1)
    $background_fixed_class = 'background-fixed';

    $body_class = esc_attr($header_position_class).' '.esc_attr($background_repeat_class).' '.esc_attr($background_fixed_class);

    $classes[] = $body_class;

    return $classes;
}



/**
 * Return game banner
 * @param $game_id
 * @param bool $crop
 * @return array|bool|false|string
 */
function respawn_return_game_banner($game_id, $crop = false){
    global $wpdb;
    $games = $wpdb->prefix."cw_games";
    $game_img = $wpdb->get_results($wpdb->prepare("SELECT g_banner_file FROM $games WHERE `id`= %s",$game_id));
    $image = false;

    if(!empty($game_img)){
        $thumb = $game_img[0]->g_banner_file;
        $img_url = wp_get_attachment_url( $thumb); //get img URL
        if($crop) {
            $image = respawn_aq_resize($img_url, 1168, 230, true, true, true); //resize & crop img
        }else{
            $image = $img_url;
        }
    }

    return $image;

}


/**
 * Return game icon
 * @param $game_id
 * @param bool $crop
 * @return array|bool|false|string
 */
function respawn_return_game_icon($game_id, $crop = false){
    global $wpdb;
    $games = $wpdb->prefix."cw_games";
    $game_img = $wpdb->get_results($wpdb->prepare("SELECT `icon` FROM $games WHERE `id`= %s", $game_id));
    $image = false;

    if(!empty($game_img)){
        $image = wp_get_attachment_url( $game_img[0]->icon); //get img URL
    }

    if($crop)
        $image = respawn_aq_resize( $image, 85, 116, true, true, true ); //resize & crop img

    return $image;

}


/**
 * Return player image
 * @param $player_id
 * @param $width
 * @param $height
 * @return array|bool|string
 */
function respawn_return_player_image_fn($player_id, $width, $height){

    $imag = get_the_post_thumbnail_url( $player_id,'full'); //get img URL

    if(isset($imag)){
        $image = respawn_aq_resize( $imag, $width, $height, true, true, true ); //resize & crop img
    }

    if(empty($image)){ $image = get_theme_file_uri('assets/img/defaults/userdef.jpg');  }
    return $image;
}


/**
 * Return game image
 * @param $game_id
 * @return false|string
 */
function respawn_return_game_image_nocrop($game_id){
    global $wpdb;
    $games = $wpdb->prefix."cw_games";
    if(class_exists('WP_TeamMatches')){
        $game_img = $wpdb->get_results($wpdb->prepare("SELECT `icon` FROM $games WHERE `id`= %s", $game_id));
    }

    if(isset($game_img[0]->icon))
        $image = wp_get_attachment_url( $game_img[0]->icon); //get img URL

    if(empty($image)){ $image = get_theme_file_uri('assets/img/defaults/gamedef.png');  }
    return $image;

}

/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function respawn_custom_excerpt_length( $length ) {
    return 25;
}


/**
 * Testimonial box
 * @param array $meta_boxes
 * @return array
 */
function respawn_testimonial_setting_metaboxes(array $meta_boxes ) {

    $prefix = 'theplus_testimonial_';
    $post_name = theplus_testimonial_post_name();
    $meta_boxes[] = array(
        'id'         => 'testimonial_setting_metaboxes',
        'title'      => esc_html__('ThePlus Testimonial Options', 'respawn'),
        'pages'      => array($post_name),
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true,
        'fields'     => array(
            array(
                'name'	=> esc_html__('Author Text', 'respawn'),
                'desc'	=> '',
                'id'	=> $prefix . 'author_text',
                'type'	=> 'wysiwyg',
                'options' => array(
                    'wpautop' => false,
                    'media_buttons' => false,
                    'textarea_rows' => get_option('default_post_edit_rows', 7),
                ),
            ),
            array(
                'name'	=> esc_html__('Title', 'respawn'),
                'desc'	=>  esc_html__('Enter title of testimonial.', 'respawn'),
                'id'	=> $prefix . 'title',
                'type'	=> 'text',
            ),
            array(
                'name'	=> esc_html__('Logo Upload', 'respawn'),
                'desc'	=> '',
                'id'	=> $prefix . 'logo',
                'type'	=> 'file',
            ),
            array(
                'name'	=> esc_html__('Designation', 'respawn'),
                'desc'	=>  esc_html__('Enter author Designation', 'respawn'),
                'id'	=> $prefix . 'designation',
                'type'	=> 'text',
            ),
        ),
    );

    return $meta_boxes;
}


/**
 * Add megamenu theme
 * @param $themes
 * @return mixed
 */
function respawn_megamenu_add_theme_respawn($themes) {
    $themes["respawn_1574099674"] = array(
        'title' => 'Respawn',
        'container_background_from' => 'rgba(34, 34, 34, 0)',
        'container_background_to' => 'rgba(34, 34, 34, 0)',
        'container_padding_left' => '20px',
        'container_padding_right' => '20px',
        'menu_item_background_hover_from' => 'rgba(34, 34, 34, 0)',
        'menu_item_background_hover_to' => 'rgba(34, 34, 34, 0)',
        'menu_item_link_font_size' => '12px',
        'menu_item_link_height' => '80px',
        'menu_item_link_color' => 'rgba(255, 255, 255, 0.9)',
        'menu_item_link_weight' => 'bold',
        'menu_item_link_color_hover' => 'rgb(255, 255, 255)',
        'menu_item_link_padding_left' => '0px',
        'menu_item_link_padding_right' => '0px',
        'menu_item_divider_color' => 'rgba(255, 255, 255, 0.6)',
        'menu_item_divider_glow_opacity' => '1',
        'panel_background_from' => 'rgb(23, 23, 25)',
        'panel_background_to' => 'rgb(23, 23, 25)',
        'panel_width' => '.container',
        'panel_header_color' => 'rgb(255, 255, 255)',
        'panel_header_font_size' => '14px',
        'panel_header_padding_bottom' => '0px',
        'panel_header_margin_bottom' => '30px',
        'panel_header_border_color' => '#555',
        'panel_padding_left' => '50px',
        'panel_padding_right' => '50px',
        'panel_padding_top' => '50px',
        'panel_padding_bottom' => '30px',
        'panel_widget_padding_left' => '0px',
        'panel_widget_padding_right' => '0px',
        'panel_widget_padding_top' => '0px',
        'panel_widget_padding_bottom' => '0px',
        'panel_font_size' => '14px',
        'panel_font_color' => 'rgb(105, 107, 255)',
        'panel_font_family' => 'inherit',
        'panel_second_level_font_color' => 'rgb(255, 255, 255)',
        'panel_second_level_font_color_hover' => 'rgb(105, 107, 255)',
        'panel_second_level_text_transform' => 'uppercase',
        'panel_second_level_font' => 'inherit',
        'panel_second_level_font_size' => '14px',
        'panel_second_level_font_weight' => 'bold',
        'panel_second_level_font_weight_hover' => 'bold',
        'panel_second_level_text_decoration' => 'none',
        'panel_second_level_text_decoration_hover' => 'none',
        'panel_second_level_padding_bottom' => '25px',
        'panel_second_level_border_color' => '#555',
        'panel_third_level_font_color' => 'rgba(255, 255, 255, 0.6)',
        'panel_third_level_font_color_hover' => 'rgb(105, 107, 255)',
        'panel_third_level_text_transform' => 'uppercase',
        'panel_third_level_font' => 'inherit',
        'panel_third_level_font_size' => '12px',
        'panel_third_level_padding_bottom' => '20px',
        'flyout_width' => '260px',
        'flyout_menu_background_from' => 'rgb(23, 23, 25)',
        'flyout_menu_background_to' => 'rgb(23, 23, 25)',
        'flyout_padding_top' => '15px',
        'flyout_padding_right' => '15px',
        'flyout_padding_bottom' => '15px',
        'flyout_padding_left' => '15px',
        'flyout_link_padding_left' => '0px',
        'flyout_link_padding_right' => '0px',
        'flyout_link_padding_top' => '20px',
        'flyout_link_padding_bottom' => '20px',
        'flyout_background_from' => 'rgba(255, 255, 255, 0)',
        'flyout_background_to' => 'rgba(255, 255, 255, 0)',
        'flyout_background_hover_from' => 'rgba(255, 255, 255, 0)',
        'flyout_background_hover_to' => 'rgba(255, 255, 255, 0)',
        'flyout_link_size' => '12px',
        'flyout_link_color' => 'rgb(255, 255, 255)',
        'flyout_link_color_hover' => 'rgb(105, 107, 255)',
        'flyout_link_family' => 'inherit',
        'flyout_link_text_transform' => 'uppercase',
        'responsive_breakpoint' => '1000px',
        'line_height' => '5.7',
        'mobile_columns' => '2',
        'toggle_background_from' => 'rgba(34, 34, 34, 0)',
        'toggle_background_to' => 'rgba(255, 255, 255, 0)',
        'toggle_bar_height' => '80px',
        'mobile_background_from' => 'rgb(19, 19, 19)',
        'mobile_background_to' => 'rgb(19, 19, 19)',
        'mobile_menu_item_link_font_size' => '14px',
        'mobile_menu_item_link_color' => '#ffffff',
        'mobile_menu_item_link_text_align' => 'left',
        'mobile_menu_item_link_color_hover' => '#ffffff',
        'mobile_menu_item_background_hover_from' => 'rgb(14, 14, 14)',
        'mobile_menu_item_background_hover_to' => 'rgb(14, 14, 14)',
        'custom_css' => '/** Push menu onto new line **/ 
#{$wrap} { 
    clear: both; 
}

#mega-menu-wrap-header-menu #mega-menu-header-menu a.mega-menu-link{
	letter-spacing:1px;
	font-weight:600 !important;
    transition: all 0.35s ease-in-out;
    -moz-transition: all 0.35s ease-in-out;
    -webkit-transition: all 0.35s ease-in-out;
    -o-transition: all 0.35s ease-in-out;
    margin:0px 20px;
}
#mega-menu-wrap-header-menu #mega-menu-header-menu > li.mega-menu-item > a.mega-menu-link:after {
    content: \'\';
    position: absolute;
    right: -20px;
    top: 50%;
    height: 26px;
    width: 1px;
    display:block;
    margin-top: -10px;
    background: rgba(255, 255, 255, 0.06);
}

#mega-menu-wrap-header-menu #mega-menu-header-menu > li.mega-menu-item > a.mega-menu-link::before {
    content: \'\';
    height: 2px;
    position: absolute;
    bottom: 24px;
    width: 12px;
    display: block;
    left: 0px;
    -webkit-transition: all 0.32s ease-in-out;
    -moz-transition: all 0.32s ease-in-out;
    transition: all 0.32s ease-in-out;
    -webkit-transform: translateX(0%);
    -moz-transform: translateX(0%);
    transform: translateX(0%);
}

#mega-menu-wrap-header-menu #mega-menu-header-menu > li.mega-menu-item > a.mega-menu-link:focus::before,
#mega-menu-wrap-header-menu #mega-menu-header-menu > li.mega-menu-item > a.mega-menu-link:hover::before{
	width:100%;
}
.mega-sub-menu a.mega-menu-link {
    line-height: normal !important;
}
.mega-sub-menu{
	box-shadow: 0px 6px 15px rgba(0,0,0,0.3) !important;
}
.mega-sub-menu .mega-sub-menu{
	box-shadow:none !important;
    }
#mega-menu-wrap-header-menu.mega-keyboard-navigation #mega-menu-header-menu a:focus, #mega-menu-wrap-header-menu.mega-keyboard-navigation #mega-menu-header-menu input:focus{
	box-shadow:none !important;
}

.widget_sp_image{
	line-height:30px !important;
    padding: 0px 10px !important;
}

.widget_sp_image p {
    font-weight: 600;
}',
    );
    return $themes;
}
add_filter("megamenu_themes", "respawn_megamenu_add_theme_respawn");