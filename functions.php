<?php
#-----------------------------------------------------------------#
# Load text domain
#-----------------------------------------------------------------#

add_action('after_setup_theme', 'esportz_lang');

/**
 * Register text domain
 */
function esportz_lang()
{
    load_theme_textdomain('esportz', get_theme_file_path('lang'));
}

/* Custom code goes below this line. */


/**
 * Theme version
 */
global $esportz_version;
$esportz_theme = wp_get_theme();
$esportz_version = $esportz_theme->get( 'Version' );

require_once get_theme_file_path('includes.php');

#-----------------------------------------------------------------#
# Widget areas
#-----------------------------------------------------------------#

add_action('widgets_init', 'esportz_widgets_init');


/**
 * Register sidebars
 */
function esportz_widgets_init()
{
    if (function_exists('register_sidebar')) {

        $args = [
            'post_type' => 'page',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'customSidebar',
                    'compare' => 'EXISTS',
                ],
            ]
        ];

        $pageSidebar = get_posts($args);
        if(!empty($pageSidebar)){
            foreach ($pageSidebar as $value){
                register_sidebar(['name' => $value->post_title, 'id' => 'sidebar'.$value->ID, 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);

            }
        }

        register_sidebar(['name' => 'Blog Sidebar', 'id' => 'blog-sidebar', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
        register_sidebar(['name' => 'Page Sidebar', 'id' => 'page-sidebar', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);

        register_sidebar(['name' => 'Homepage Left Sidebar', 'id' => 'homepage-left-sidebar', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
        register_sidebar(['name' => 'Homepage Right Sidebar', 'id' => 'homepage-right-sidebar', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);

        if (class_exists('WooCommerce')) {
            register_sidebar(['name' => 'WooCommerce Sidebar', 'id' => 'woocommerce-sidebar', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
        }

        register_sidebar(['name' => 'Footer Widget Area 1', 'id' => 'footer_widget_one', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
        register_sidebar(['name' => 'Footer Widget Area 2', 'id' => 'footer_widget_two', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
        register_sidebar(['name' => 'Footer Widget Area 3', 'id' => 'footer_widget_three', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
        register_sidebar(['name' => 'Footer Widget Area 4', 'id' => 'footer_widget_four', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
        register_sidebar(['name' => 'Footer Widget Area 5', 'id' => 'footer_widget_five', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
    }
}

#-----------------------------------------------------------------#
# Styles
#-----------------------------------------------------------------#

add_action('admin_enqueue_scripts', 'esportz_styles_admin');
add_action('wp_enqueue_scripts', 'esportz_styles');

/**
 * Register admin styles
 */
function esportz_styles_admin()
{
    global $esportz_version;
    wp_enqueue_style('esportz-admin', get_theme_file_uri('assets/css/admin.css'), [], $esportz_version);
    wp_enqueue_style('fontawesome', get_theme_file_uri('assets/css/fontawesome-all.css'), [], $esportz_version);
}


/**
 * Register theme styles
 */
function esportz_styles()
{

    global $esportz_version;
    $options = esportz_get_theme_options();

    wp_enqueue_style('esportz-style', get_bloginfo('stylesheet_url'), [], $esportz_version);

    if($options['env'] == '1') {
        wp_enqueue_style('esportz-main', get_theme_file_uri('assets/css/main.min.css'), [], $esportz_version);
    }else{
        wp_enqueue_style('esportz-main', get_theme_file_uri('assets/css/main.css'), [], $esportz_version);

    }

    wp_enqueue_style('esportz-minified', get_theme_file_uri('assets/css/minified.css'), [], $esportz_version);

    $c_id = get_current_user_id();

    $myteams = esportz_get_user_teams($c_id);
    if (is_array($myteams) AND (!empty($myteams))) {

        foreach ($myteams as $team) {

            $post = get_post($team);

            $custombck = get_post_meta($team, 'team_logo',true);
            if(empty($custombck))$custombck = esc_url(get_theme_file_uri('assets/img/defaults/default.jpg'));

            $custombck = esc_url($custombck);
            $team = esc_attr($team);
            $data = "
            #TeamChooserModalFooter .tim_bck$team{
            background:url($custombck);
            }";

            wp_add_inline_style( 'esportz-style', $data );

        }
    }

}


#-----------------------------------------------------------------#
# Scripts
#-----------------------------------------------------------------#

add_action('wp_enqueue_scripts', 'esportz_scripts');
add_action('admin_enqueue_scripts', 'esportz_admin_scripts');
add_action('init', 'esportz_register_my_menus');

/**
 * Register scripts
 */
function esportz_scripts()
{
    global $esportz_version;
    $options = esportz_get_theme_options();

    wp_enqueue_script('google-captcha', 'https://www.google.com/recaptcha/api.js', "", $esportz_version, true);
    wp_enqueue_script( 'esportz-minified', get_theme_file_uri('assets/js/minified.min.js'), ['jquery'],$esportz_version,true);


    $tournamentPage = false;
    if(is_page_template('tmp-single-pro-tournament.php'))
        $tournamentPage = true;

    $args = [
        'role' => 'cosplay',
        'orderby' => 'title',
        'order' => 'DESC',
        'meta_query' => [
            [
                'key' => 'profileCompletedCosplay',
                'compare' => '=',
                'value' => '1',
            ],
        ],
    ];

    // The Query
    $user_query = new \WP_User_Query($args);
    $total_user = $user_query->total_users;
    $total_pages=ceil($total_user/9);

    $settingsGlobal = [
        'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
        'security' => wp_create_nonce('esportz-security-nonce'),
        'pleaseWait' => esc_html__('Please Wait...', 'esportz'),
        'signUp' => esc_html__('SIGN UP NOW!', 'esportz'),
        'login' => esc_html__('LOGIN NOW', 'esportz'),
        'createAccount' => esc_html__('CREATE ACCOUNT', 'esportz'),
        'newsletterSubscribed' => esc_html__('Please check your email for confirmation link.', 'esportz'),
        'invalidType' => esc_html__('Invalid file type', 'esportz'),
        'submitForReview' => esc_html__('Submit for review', 'esportz') . ' ' . '<i class="fa fa-arrow-circle-right"></i>',
        'submitIdea' => esc_html__('Submit Idea', 'esportz'),
        'finishRegistration' => esc_html__('Finish registration', 'esportz'),
        'uid' => get_current_user_id(),
        'passwordMatch' =>  esc_html__('Password doesn\'t match!', 'esportz'),
        'isTournamentPage' => $tournamentPage,
        'loadMore' => esc_html__('Load more' ,'esportz'),
        'coinsPageUrl' => esc_url( esportz_return_template_url('tmp-coinshop.php') ),
        'singlePost' => is_single(),
        'alreadyLiked' => esc_html__('You already liked this!', "esportz"),
        'upgradeSt' => esc_html__('Upgrade', 'esportz'),
        'autocomplete' => json_encode('opa'),
        'posts' => json_encode( $user_query->query_vars ), // everything about your loop is here
        'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
        'max_page' => $total_pages,
        'loading' => esc_html__('Loading...', 'esportz'),
        'loadMore' => esc_html__('Load more', 'esportz'),
        'facebookAPP' => $options['facebook-social-login']

    ];


    if($options['env'] == '1') {
        wp_enqueue_script('esportz-global', get_theme_file_uri('assets/js/global.min.js'), ['jquery', 'esportz-minified', 'jquery-ui-autocomplete', 'prettyPhoto', 'imagesloaded'], $esportz_version, true);
    }else{
        wp_enqueue_script('esportz-global', get_theme_file_uri('assets/js/global.js'), ['jquery','esportz-minified', 'jquery-ui-autocomplete', 'prettyPhoto', 'imagesloaded'], $esportz_version, true);

    }

    wp_localize_script('esportz-global', 'settingsGlobal', $settingsGlobal);

    if (is_page_template('tmp-photovideo-submit.php') ) {
        wp_enqueue_script('esportz-ninja', get_theme_file_uri('assets/js/ninja-forms.js'), ['nf-front-end'], $esportz_version, true);
    }
}


/**
 * Register admin scripts
 */
function esportz_admin_scripts()
{
    global $esportz_version;
    $post_id = get_the_ID();

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    $settingsAdmin = array(
        'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
        'security' => wp_create_nonce('esportz-security-nonce'),
        'pleaseWait' => esc_html__('Please Wait...', 'esportz'),
        'post_id' => esc_html($post_id),
        'hired' => esc_html__('Hired', 'esportz'),
        'rejected' => esc_html__('Rejected', 'esportz'),
    );
    wp_enqueue_script('esportz-admin', get_theme_file_uri('assets/js/admin.js'), ['jquery-ui-datepicker'], $esportz_version, false);
    wp_localize_script('esportz-admin', 'settingsAdmin', $settingsAdmin);
}

/**
 *Register theme location menu
 */
function esportz_register_my_menus()
{
    register_nav_menus(
        array(
            'header-menu' => esc_html__('Header Menu', 'esportz'),
        )
    );
}


#-----------------------------------------------------------------#
# Random functions
#-----------------------------------------------------------------#

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
function esportz_aq_resize($url, $width = null, $height = null, $crop = null, $single = true, $upscale = false)
{

    // Validate inputs.
    if (!$url || (!$width && !$height)) return false;

    // Caipt'n, ready to hook.
    if (true === $upscale) add_filter('image_resize_dimensions', 'esportz_aq_upscale', 10, 6);

    // Define upload path & dir.
    $upload_info = wp_upload_dir();
    $upload_dir = $upload_info['basedir'];
    $upload_url = $upload_info['baseurl'];

    $http_prefix = "http://";
    $https_prefix = "https://";

    /* if the $url scheme differs from $upload_url scheme, make them match
       if the schemes differe, images don't show up. */
    if (!strncmp($url, $https_prefix, strlen($https_prefix))) { //if url begins with https:// make $upload_url begin with https:// as well
        $upload_url = str_replace($http_prefix, $https_prefix, $upload_url);
    } elseif (!strncmp($url, $http_prefix, strlen($http_prefix))) { //if url begins with http:// make $upload_url begin with http:// as well
        $upload_url = str_replace($https_prefix, $http_prefix, $upload_url);
    }


    // Check if $img_url is local.
    if (false === strpos($url, $upload_url)) return false;

    // Define path of image.
    $rel_path = str_replace($upload_url, '', $url);
    $img_path = $upload_dir . $rel_path;

    // Check if img path exists, and is an image indeed.
    if (!file_exists($img_path) or !getimagesize($img_path)) return false;

    // Get image info.
    $info = pathinfo($img_path);
    $ext = $info['extension'];
    list($orig_w, $orig_h) = getimagesize($img_path);

    // Get image size after cropping.
    $dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
    $dst_w = $dims[4];
    $dst_h = $dims[5];

    // Return the original image only if it exactly fits the needed measures.
    if (!$dims && (((null === $height && $orig_w == $width) xor (null === $width && $orig_h == $height)) xor ($height == $orig_h && $width == $orig_w))) {
        $img_url = $url;
        $dst_w = $orig_w;
        $dst_h = $orig_h;
    } else {
        // Use this to check if cropped image already exists, so we can return that instead.
        $suffix = "{$dst_w}x{$dst_h}";
        $dst_rel_path = str_replace('.' . $ext, '', $rel_path);
        $destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

        if (!$dims || (true == $crop && false == $upscale && ($dst_w < $width || $dst_h < $height))) {
            // Can't resize, so return false saying that the action to do could not be processed as planned.
            return false;
        } // Else check if cache exists.
        elseif (file_exists($destfilename) && getimagesize($destfilename)) {
            $img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
        } // Else, we resize the image and return the new resized image url.
        else {

            // Note: This pre-3.5 fallback check will edited out in subsequent version.
            if (function_exists('wp_get_image_editor')) {

                $editor = wp_get_image_editor($img_path);

                if (is_wp_error($editor) || is_wp_error($editor->resize($width, $height, $crop)))
                    return false;

                $resized_file = $editor->save();

                if (!is_wp_error($resized_file)) {
                    $resized_rel_path = str_replace($upload_dir, '', $resized_file['path']);
                    $img_url = $upload_url . $resized_rel_path;
                } else {
                    return false;
                }

            } else {

                $editor = wp_get_image_editor($img_path); // Fallback foo.

                if (is_wp_error($editor) || is_wp_error($editor->resize($width, $height, $crop)))
                    return false;

                $resized_img_path = $editor->save();

                if (!is_wp_error($resized_img_path)) {
                    $resized_rel_path = str_replace($upload_dir, '', $resized_img_path);
                    $img_url = $upload_url . $resized_rel_path;
                } else {
                    return false;
                }

            }

        }
    }

    // Return the output.
    if ($single) {
        // str return.
        $image = $img_url;
    } else {
        // array return.
        $image = array(
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
function esportz_aq_upscale($default, $orig_w, $orig_h, $dest_w, $dest_h, $crop)
{
    if (!$crop) return null; // Let the wordpress default function handle this.

    if (false)
        return $default;
    // Here is the point we allow to use larger image size than the original one.
    $aspect_ratio = $orig_w / $orig_h;
    $new_w = $dest_w;
    $new_h = $dest_h;

    if (!$new_w) {
        $new_w = intval($new_h * $aspect_ratio);
    }

    if (!$new_h) {
        $new_h = intval($new_w / $aspect_ratio);
    }

    $size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

    $crop_w = round($new_w / $size_ratio);
    $crop_h = round($new_h / $size_ratio);

    $s_x = floor(($orig_w - $crop_w) / 2);
    $s_y = floor(($orig_h - $crop_h) / 2);

    return array(0, 0, (int)$s_x, (int)$s_y, (int)$new_w, (int)$new_h, (int)$crop_w, (int)$crop_h);
}


/**
 * Return template link
 * @param $name
 * @return mixed
 */
function esportz_return_template_url($name)
{

    $args = [
        'post_type' => 'page',
        'fields' => 'ids',
        'nopaging' => true,
        'meta_key' => '_wp_page_template',
        'meta_value' => $name
    ];

    $pages = get_posts($args);

    $page_id = '';

    if (isset($pages[0]))
        $page_id = $pages[0];

    $link = get_permalink($page_id);

    return esc_url($link);
}

/**
 * Replacement for file_get_contents()
 * @param $url
 * @param string $useragent
 * @param bool $headers
 * @param bool $follow_redirects
 * @param bool $debug
 * @return bool|string
 */
function curl_get_contents($url, $useragent = 'cURL', $headers = false, $follow_redirects = false, $debug = false)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if($headers)
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);

    curl_close($ch);
    return $result;
}

add_action('init', 'esportz_do_output_buffer');

/**
 *Turn on output buffering
 */
function esportz_do_output_buffer()
{
    ob_start();
}


/**
 * Limit media to current user
 * @param $query
 * @return mixed
 */
function esportz_current_user_attachments($query)
{
    $user_id = get_current_user_id();
    if ($user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts
    ')) {
        $query['author'] = $user_id;
    }
    return $query;
}

add_filter('ajax_query_attachments_args', 'esportz_current_user_attachments');

#-----------------------------------------------------------------#
# Categories
#-----------------------------------------------------------------#


add_action('edited_category', 'esportz_save_extra_category_fileds');
add_action('created_category', 'esportz_save_extra_category_fileds', 11, 1);
add_action('category_edit_form_fields', 'esportz_extra_category_fields');
add_action('category_add_form_fields', 'esportz_category_form_custom_field_add', 10);


add_action('edited_media-category', 'esportz_save_extra_category_fileds');
add_action('created_media-category', 'esportz_save_extra_category_fileds', 11, 1);
add_action('media-category_edit_form_fields', 'esportz_extra_category_fields');
add_action('media-category_add_form_fields', 'esportz_category_form_custom_field_add', 10);


/**
 * Add color picker to category
 *
 */
function esportz_category_form_custom_field_add()
{
    ?>
    <div class="form-field">
        <label for="category_custom_color"><?php esc_html_e('Color', 'esportz'); ?></label>
        <input name="cat_meta[catBG]" class="catcolorpicker" type="text" value=""/>
        <p class="description"><?php esc_html_e('Pick a Category Color', 'esportz'); ?></p>
    </div>
    <?php
}


/**
 * Extra category fields
 * @param $tag
 */
function esportz_extra_category_fields($tag)
{
    $t_id = $tag->term_id;
    $cat_meta = get_option("category_$t_id");
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="meta-color"><?php esc_html_e('Category Color', 'esportz'); ?></label>
        </th>
        <td>
            <div id="colorpicker">
                <input type="text" name="cat_meta[catBG]" class="catcolorpicker" size="3" style="width:20%;"
                       value="<?php echo (isset($cat_meta['catBG'])) ? $cat_meta['catBG'] : '#fff'; ?>"/>
            </div>
            <br/>
            <span class="description"> </span>
            <br/>
        </td>
    </tr>
    <?php
}


/**
 * Save category meta fields
 * @param $term_id
 */
function esportz_save_extra_category_fileds($term_id)
{

    if (isset($_POST['cat_meta'])) {
        $t_id = $term_id;
        $cat_meta = get_option("category_$t_id");
        $cat_keys = array_keys($_POST['cat_meta']);
        foreach ($cat_keys as $key) {
            if (isset($_POST['cat_meta'][$key])) {
                $cat_meta[$key] = $_POST['cat_meta'][$key];
            }
        }
        //save the option array
        update_option("category_$t_id", $cat_meta);
    }
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
function esportz_kriesi_pagination($pages = '', $range = 1)
{

    $showitems = ($range * 1) + 1;
    global $paged;
    global $paginate;
    $stranica = $paged;
    if (empty($stranica)) $stranica = 1;
    if ($pages == '') {
        global $wp_query;
        $pages = $wp_query->max_num_pages;
        if (!$pages) {
            $pages = 1;
        }
    }

    if (1 != $pages) {
        $leftpager = '&laquo;';
        $rightpager = '&raquo;';
        $paginate .= '<div class="pagination"><ul>';

        if ($stranica > 2 && $stranica > $range + 1 && $showitems < $pages) $paginate .= "";
        if ($stranica > 1) $paginate .= "<li><a class='page-selector' href='" . get_pagenum_link($stranica - 1) . "'>" . $leftpager . "</a></li>";


        for ($i = 1; $i <= $pages; $i++) {
            if (1 != $pages && (!($i >= $stranica + $range + 1 || $i <= $stranica - $range - 1) || $pages <= $showitems)) {
                $paginate .= ($stranica == $i) ? "<li class='active'><a href='" . get_pagenum_link($i) . "'>" . $i . "</a></li>" : "<li><a href='" . get_pagenum_link($i) . "' class='inactive' >" . $i . "</a></li>";
            }
        }

        if ($stranica < $pages) $paginate .= "<li><a class='page-selector' href='" . get_pagenum_link($stranica + 1) . "' >" . $rightpager . "</a></li>";

        $paginate .= '</ul></div>';
    }
    return $paginate;
}


/**
 * Add body classes
 * @param $classes
 * @return array
 */
function esportz_body_classes($classes)
{
    $options = esportz_get_theme_options();

    $header_position_class = '';
    if ($options['header-position'] == 'fixed') {
        $header_position_class = 'has-fixed-header';
    }


    $background_repeat_class = '';
    if (isset($options['body_background_repeat']) && $options['body_background_repeat'] == 1)
        $background_repeat_class = 'background-repeat';

    $background_fixed_class = '';
    if (isset($options['body_background_fixed']) && $options['body_background_fixed'] == 1)
        $background_fixed_class = 'background-fixed';

    $body_class = esc_attr($header_position_class) . ' ' . esc_attr($background_repeat_class) . ' ' . esc_attr($background_fixed_class);

    $classes[] = $body_class;

    return $classes;
}


#-----------------------------------------------------------------#
# Fonts
#-----------------------------------------------------------------#

add_action('wp_enqueue_scripts', 'esportz_fonts');

/**
 * Register Google fonts
 * @return mixed
 */
function esportz_fonts_url()
{
    $font_url = '';

    /*
    Translators: If there are characters in your language that are not supported
    by chosen font(s), translate this to 'off'. Do not translate into your own language.
     */
    if ('off' !== _x('on', 'Google font: on or off', 'esportz')) {
        $font_url = add_query_arg('family', urlencode('Montserrat:200,300,400,500,600,700,800'), "//fonts.googleapis.com/css");
    }
    return esc_url($font_url);
}


/**
 * Enqueue Google fonts
 */
function esportz_fonts()
{
    wp_enqueue_style('esportz-fonts', esportz_fonts_url(), [], '1.0.0');
}

#-----------------------------------------------------------------#
# Options panel
#-----------------------------------------------------------------#


/**
 * Return theme options
 * @return mixed
 */
function esportz_get_theme_options()
{
    $current_options = get_option('esportz_redux');
    return $current_options;
}

if (class_exists('ReduxFrameworkPlugin')) {
    add_action('admin_enqueue_scripts', 'esportz_redux_deps');

    /**
     * Enqueue Redux styling
     */
    function esportz_redux_deps()
    {
        global $esportz_version;
        wp_enqueue_style('esportz-redux-admin', get_theme_file_uri('assets/css/esportz-redux-styling.css'), [], $esportz_version, 'all');
    }
}


/**
 * Set email content type
 * @return string
 */
function esportz_set_content_type()
{
    return "text/html";
}

add_filter('wp_mail_content_type', 'esportz_set_content_type');


/**
 * Change upload folder to cv
 * @param $dir
 * @return array
 */
function esportz_upload_dir($dir)
{
    return array(
            'path' => $dir['basedir'] . '/cv',
            'url' => $dir['baseurl'] . '/cv',
            'subdir' => '/cv',
        ) + $dir;
}



/**
 * Get all matches for selected game
 * @param $params
 * @return array|WP_Error
 */
function esportz_get_matches_for_game($params)
{

    $id = intval($params['id']);

    $result = [];

    if (count($result) == 0) {
        return new WP_Error('nothing_found', esc_html__('Nothing found', 'esportz'), ['status' => 422]);
    }

    return $result;
}


#-----------------------------------------------------------------#
# Social
#-----------------------------------------------------------------#
/**
 * Get Youtube subscribers
 */
function esportz_get_youtube_count()
{

    $options = esportz_get_theme_options();

    $channel_id = $options['youtube-channel'];
    $api_key = $options['youtube-api'];
    $api_response = curl_get_contents('https://www.googleapis.com/youtube/v3/channels?part=statistics&id=' . $channel_id . '&fields=items/statistics/subscriberCount&key=' . $api_key);
    $api_response_decoded = json_decode($api_response, true);

    if (isset($api_response_decoded['items'][0]['statistics']['subscriberCount'])) {
        update_option('youtubeCount', esc_html($api_response_decoded['items'][0]['statistics']['subscriberCount']));
    } else {
        update_option('youtubeCount',0);
    }

}

/**
 * Get Facebook likes
 */
function esportz_get_facebook_likes()
{

    $options = esportz_get_theme_options();

    $page = $options['facebook-page'];
    $token = $options['facebook-token'];
    $pageData = curl_get_contents('https://graph.facebook.com/' . $page . '?fields=fan_count&access_token=' . $token);
    if ($pageData) { // if valid json object
        $pageData = json_decode($pageData); // decode json object
        if (isset($pageData->likes)) { // get likes from the json object
            update_option('facebookLikes',  esc_html($pageData->likes));
        }
    } else {
        update_option('facebookLikes',  0);
    }

}

/**
 * Get Twitter followers
 */
function esportz_get_twitter_followers()
{

    $options = esportz_get_theme_options();

    $tw_username = $options['twitter-username'];
    $data = curl_get_contents('https://cdn.syndication.twimg.com/widgets/followbutton/info.json?screen_names=' . $tw_username);
    $parsed = json_decode($data, true);

    if (isset($parsed[0]['followers_count'])) {
        update_option('twitterFollowers',  esc_html($parsed[0]['followers_count']));
    } else {
        update_option('twitterFollowers', 0);
    }
}

/**
 * Get Instagram followers
 */
function esportz_get_instagram_followers()
{

    $options = esportz_get_theme_options();

    $insta_username = $options['instagram-username'];
    $data = curl_get_contents('https://www.instagram.com/' . $insta_username . '/');
    preg_match('/\"edge_followed_by\"\:\s?\{\"count\"\:\s?([0-9]+)/', $data, $m);

    if (isset($m[1])) {
        update_option('instagramFollowers',  esc_html($m[1]));
    } else {
        update_option('instagramFollowers',  0);
    }
}


/**
 * Get Twitch followers
 */
function esportz_get_twitch_followers()
{

    $options = esportz_get_theme_options();

    $twitch_channel = $options['twitch-channel'];
    $client_id = $options['twitch-client'];
    $followAPI = json_decode(curl_get_contents('https://api.twitch.tv/helix/users/follows?to_id=' . $twitch_channel, 'cURL', ["Client-ID: ".$client_id]),true);

    if (isset($followAPI['total'])) {
        update_option('twitchFollowers',  esc_html($followAPI['total']));
    } else {
        update_option('twitchFollowers',  0);
    }
}

/**
 * Get Discord members
 */
function esportz_get_discord_members()
{

    $options = esportz_get_theme_options();

    $discord_channel = $options['discord-channel'];
    $followAPI = json_decode(curl_get_contents('https://discordapp.com/api/guilds/' . $discord_channel . '/widget.json'), true);

    if (isset($followAPI['members'])) {
        update_option('discordMembers',  esc_html(count($followAPI['members'])));
    } else {
        update_option('discordMembers', 0);
    }
}

/**
 * Pull data for social widget
 */
function esportz_pull_social_widget_data(){

    esportz_get_youtube_count();
    esportz_get_facebook_likes();
    esportz_get_twitter_followers();
    esportz_get_instagram_followers();
    esportz_get_twitch_followers();
    esportz_get_discord_members();
}

#-----------------------------------------------------------------#
# Abios
#-----------------------------------------------------------------#

add_filter('query_vars', 'esportz_query_vars');
add_action('template_redirect', 'esportz_trigger_check');


/**
 * Get Abios authorization token
 * @return bool
 */
function esportz_return_abios_access_token()
{

    $options = esportz_get_theme_options();

    $clientSecret = $options['abiosClientSecret'];
    $clientId = $options['abiosClientID'];

    $data = array('grant_type' => 'client_credentials', 'client_id' => $clientId, 'client_secret' => $clientSecret);
    $response = wp_remote_post('https://api.abiosgaming.com/v2/oauth/access_token', array('body' => $data));

    sleep(1);
    if (isset($response['response']['code']) && $response['response']['code'] == 200) {
        $body = json_decode(wp_remote_retrieve_body($response), true);
        update_option('abios_access_token', $body["access_token"], 'no');

    } else {
        return false;
    }

}


/**
 * Add cron job query var
 * @param $vars
 * @return array
 */
function esportz_query_vars($vars)
{
    $vars[] = 'cron_job';
    return $vars;
}

/**
 * Fire up crons
 */
function esportz_trigger_check()
{

    switch (get_query_var('cron_job')) {

        case 'token':
            esportz_return_abios_access_token();
            exit;
            break;
        case 'games':

            //time: 0:05
            esportz_load_pro_games_into_db();
            exit;
            break;
        case 'tournaments':

            //time: 0:14
            esportz_get_tournaments();
            exit;
            break;
        case 'clearAbiosCache':

            //time: 0:14
            esportz_clear_abios_cache();
            exit;
            break;

        case 'pullSocialWidgetData':

            //time: 0:14
            esportz_pull_social_widget_data();
            exit;
            break;

    }

}

/**
 * Remove item from abios database
 */
function esportz_clear_abios_cache(){

    $cache = new \esportz\addons\abios\Cache();

    $cache->clearCache();

}

/**
 * Adds Abios games to the database. Should be triggered only once to do initial database loading
 */
function esportz_load_pro_games_into_db()
{

    $token = get_option('abios_access_token');

    $games = ['legends', 'counter', 'dota', 'overwatch', 'smash'];

    foreach ($games as $game) {
        $response = curl_get_contents('https://api.abiosgaming.com/v2/games?q=' . $game . '&access_token=' . $token);
        $data = json_decode($response, true);

        if (isset($data['data'][0])) {

            $args = [
                'meta_key' => 'id',
                'meta_value' => $data['data'][0]['id'],
                'post_type' => 'pro-game',
            ];
            $posts = get_posts($args);

            if (empty($posts)) {

                $my_post = array(
                    'post_title' => $data['data'][0]['long_title'],
                    'post_status' => 'publish',
                    'post_type' => 'pro-game',
                    'post_author' => 1,
                );

                $pid = wp_insert_post($my_post);

                update_post_meta($pid, 'id', $data['data'][0]['id']);
                update_post_meta($pid, 'title', $data['data'][0]['title']);
                update_post_meta($pid, 'color', $data['data'][0]['color']);
                update_post_meta($pid, 'default_match_type', $data['data'][0]['default_match_type']);
                update_post_meta($pid, 'square_image', $data['data'][0]['images']['square']);
                update_post_meta($pid, 'circle_image', $data['data'][0]['images']['circle']);
                update_post_meta($pid, 'rectangle_image', $data['data'][0]['images']['rectangle']);

            }

        }

    }

}


/**
 * Return array of pro game ids
 * @return array
 */
function esportz_return_pro_games_ids()
{

    $ids = [];

    $args = [
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'post_type' => 'pro-game',
    ];
    $games = get_posts($args);

    foreach ($games as $game) {
        $id = get_post_meta($game->ID, 'id', true);
        $ids[] = (int)$id;
    }

    return $ids;
}

/**
 * Get tournaments from Abios and add the to WP structure
 */
function esportz_get_tournaments()
{

    //disable auto commit to speed up the process
    global $wpdb;
    $wpdb->query('START TRANSACTION;');
    wp_defer_term_counting(true);
    wp_defer_comment_counting(true);

    //grab token
    $token = get_option('abios_access_token');

    $games = esportz_return_pro_games_ids();
    $gameParams = [];
    foreach ($games as $game) {
        $gameParams[] = 'games[]=' . $game;
    }

    $response = curl_get_contents('https://api.abiosgaming.com/v2/tournaments?access_token=' . $token . '&' . implode('&', $gameParams));

    $data = json_decode($response, true);

    $tournaments = '';

    if (isset($data['data']))
        $tournaments = $data['data'];

    if (is_array($tournaments))
        foreach ($tournaments as $tournament) {

            $args = [
                'post_type' => 'pro-tournament',
                'meta_query' => [
                    [
                        'key' => 'data',
                        'value' => serialize(intval($tournament['id'])),
                        'compare' => 'LIKE',
                    ],

                ]
            ];
            $posts = get_posts($args);

            if (empty($posts)) {

                $my_post = array(
                    'post_title' => $tournament['title'],
                    'post_status' => 'publish',
                    'post_type' => 'pro-tournament',
                    'post_author' => 1,
                );

                $pid = wp_insert_post($my_post);

                update_post_meta($pid, 'data', $tournament);
                update_post_meta($pid, 'abios_id', $tournament['id']);
                update_post_meta($pid, 'start', strtotime($tournament['start']));
                update_post_meta($pid, 'end', strtotime($tournament['end']));
                update_post_meta($pid, 'game_id', $tournament['game']['id']);

            }

        }


    $totalPages = $data['last_page'];
    $i = 2;

    while ($i <= $totalPages) {

        $response = curl_get_contents('https://api.abiosgaming.com/v2/tournaments?access_token=' . $token . '&' . implode('&', $gameParams) . '&page=' . $i);
        $data = json_decode($response, true);

        //put to sleep if we reach limit
        if (isset($data["error_code"]) && $data["error_code"] == 429) {
            sleep(2);
            continue;
        }

        //check if token is expired
        if (isset($data["error_code"]) && $data["error_code"] == 401) {
            esportz_return_abios_access_token();
            $token = get_option('abios_access_token');
            continue;
        }


        $tournaments = '';

        if (isset($data['data']))
            $tournaments = $data['data'];

        if (is_array($tournaments))
            foreach ($tournaments as $tournament) {

                $args = [
                    'post_type' => 'pro-tournament',
                    'meta_query' => [
                        [
                            'key' => 'data',
                            'value' => serialize(intval($tournament['id'])),
                            'compare' => 'LIKE',
                        ],

                    ]
                ];
                $posts = get_posts($args);

                if (empty($posts)) {

                    $my_post = array(
                        'post_title' => $tournament['title'],
                        'post_status' => 'publish',
                        'post_type' => 'pro-tournament',
                        'post_author' => 1,
                    );

                    $pid = wp_insert_post($my_post);

                    update_post_meta($pid, 'data', $tournament);
                    update_post_meta($pid, 'abios_id', $tournament['id']);
                    update_post_meta($pid, 'start', strtotime($tournament['start']));
                    update_post_meta($pid, 'end', strtotime($tournament['end']));
                    update_post_meta($pid, 'game_id', $tournament['game']['id']);

                }

            }

        $i++;
    }

    //restore autocommit
    $wpdb->query('COMMIT;');
    wp_defer_term_counting(false);
    wp_defer_comment_counting(false);

}

/**
 * @param $array_a
 * @param $array_b
 * @return mixed
 */
function esportz_multi_array_diff($array_a, $array_b)
{

    foreach ($array_a as $key_a => $value_a) {
        if (in_array($value_a, $array_b)) {
            unset($array_a[$key_a]);
        }
    }
    return $array_a;
}




#-----------------------------------------------------------------#
# Mailchimp
#-----------------------------------------------------------------#


/**
 * Check user status
 *
 * @param $email
 * @param $status
 * @param $list_id
 * @param $api_key
 * @return bool|string
 */
function esportz_mailchimp_subscriber_status($email, $status, $list_id, $api_key)
{
    $data = array(
        'apikey' => $api_key,
        'email_address' => $email,
        'status' => $status
    );
    $mch_api = curl_init(); // initialize cURL connection

    curl_setopt($mch_api, CURLOPT_URL, 'https://' . substr($api_key, strpos($api_key, '-') + 1) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5(strtolower($data['email_address'])));
    curl_setopt($mch_api, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic ' . base64_encode('user:' . $api_key)));
    curl_setopt($mch_api, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
    curl_setopt($mch_api, CURLOPT_RETURNTRANSFER, true); // return the API response
    curl_setopt($mch_api, CURLOPT_CUSTOMREQUEST, 'PUT'); // method PUT
    curl_setopt($mch_api, CURLOPT_TIMEOUT, 10);
    curl_setopt($mch_api, CURLOPT_POST, true);
    curl_setopt($mch_api, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($mch_api, CURLOPT_POSTFIELDS, json_encode($data)); // send data in json

    $result = curl_exec($mch_api);

    return $result;
}


#-----------------------------------------------------------------#
# Roles
#-----------------------------------------------------------------#

/**
 * Add new roles
 */
function esportz_add_role_function()
{
    $roles_set = get_option('my_roles');
    if (!$roles_set) {
        add_role('reporter', 'Reporter', array(
            'read' => true, // True allows that capability, False specifically removes it.
            'edit_posts' => true,
            'delete_posts' => true,
            'upload_files' => true,
            'edit_published_pages' => true,
            'edit_others_pages' => true,
            'edit_others_posts' => true,

        ));

        add_role('pending', 'Pending Reporter', []);
        add_role('pending_cosplay', 'Pending Cosplay', []);
        add_role('pending_photvid', 'Pending Photographer/Videographer', []);

        add_role( 'gamer', 'Gamer',  array(
            'read'         => true,  // true allows this capability
            'edit_posts'   => true,
            'delete_posts' => true,
            'publish_posts' => true,
            'upload_files'  => true,
            'edit_others_pages' => true,
            'edit_published_posts' => true,
            'delete_published_posts' => true,
        ) );

        add_role( 'cosplay', 'Cosplay',  array(
            'read'         => true,  // true allows this capability
            'edit_posts'   => true,
            'delete_posts' => true,
            'publish_posts' => true,
            'upload_files'  => true,
            'edit_others_pages' => true,
            'edit_published_posts' => true,
            'delete_published_posts' => true,
        ) );

        add_role( 'photvid', 'Photographer/Videographer',  array(
            'read'         => true,  // true allows this capability
            'edit_posts'   => true,
            'delete_posts' => true,
            'publish_posts' => true,
            'upload_files'  => true,
            'edit_others_pages' => true,
            'edit_published_posts' => true,
            'delete_published_posts' => true,
        ) );

        update_option('my_roles', true);
    }
}
add_action('after_setup_theme', 'esportz_add_role_function');


/**
 * Set default role
 * @return string
 */
function esportz_defaultrole(){
    return 'gamer'; // This is changed
}
add_filter( 'pre_option_default_role', 'esportz_defaultrole' );



#-----------------------------------------------------------------#
# ACF
#-----------------------------------------------------------------#

/**
 * Hook for custom sidebar creation
 * @param $value
 * @param $post_id
 * @return mixed
 */
function esportz_acf_page_create_sidebar($value, $post_id)
{

    if($value == '1'){
        update_post_meta($post_id, 'customSidebar', true);
    }else{
        delete_post_meta($post_id, 'customSidebar');
    }

    return $value;
}

add_filter('acf/update_value/name=create_custom_sidebar', 'esportz_acf_page_create_sidebar',10, 2);


/**
 * Load games
 * @param $field
 * @return mixed
 */
function esportz_acf_load_games_field_choices( $field ) {

    // reset choices
    $field['choices'] = array();

    global $wpdb;
    $table = $wpdb->prefix."cw_games";
    $games = $wpdb->get_results( "SELECT * FROM $table ORDER BY `id`");

    // loop through array and add to field 'choices'
    if( is_array($games) ) {

        foreach( $games as $game ) {
            $field['choices'][ $game->id ] = $game->title;

        }

    }


    // return the field
    return $field;

}
add_filter('acf/load_field/name=tournament_games', 'esportz_acf_load_games_field_choices');


/**
 * Load maps
 * @param $field
 * @return mixed
 */
function esportz_acf_load_maps_field_choices($field ) {

    // reset choices
    $field['choices'] = array();
    global $wpdb;
    $table = $wpdb->prefix."cw_maps";
    $maps = $wpdb->get_results("SELECT * FROM $table ORDER BY `id`");

    // loop through array and add to field 'choices'
    if( is_array($maps) ) {

        foreach( $maps as $map ) {
            $field['choices'][ $map->id ] = $map->title;

        }

    }


    // return the field
    return $field;

}
add_filter('acf/load_field/name=tournament_maps', 'esportz_acf_load_maps_field_choices');


#-----------------------------------------------------------------#
# Posts
#-----------------------------------------------------------------#

/**
 * Get # of views for post
 * @return string
 */
function esportz_get_post_view($post_id) {
    $count = get_post_meta( $post_id, 'post_views_count', true );

    if(empty($count)){
        echo 0;
    }else{
        echo esc_html($count);
    }

}

/**
 * Set # of post views
 */
function esportz_set_post_view($post_id) {
    $key = 'post_views_count';
    $post_id = get_the_ID();
    $count = (int) get_post_meta( $post_id, $key, true );
    $count++;
    update_post_meta( $post_id, $key, $count );
}


/**
 * Add class for prev and next links
 * @return string
 */
function esportz_posts_link_attributes() {
    return 'class="gilgame"';
}
add_filter('next_posts_link_attributes', 'esportz_posts_link_attributes');
add_filter('previous_posts_link_attributes', 'esportz_posts_link_attributes');


#-----------------------------------------------------------------#
# User ranks
#-----------------------------------------------------------------#


/**
 * Set rank on sign up.
 * Experience points adds by mycred default hooks.
 * @param $user_id
 */
function esportz_update_user_rank( $user_id ) {

    if(!class_exists( 'Esportz_Rank' ) ){
        return false;
    }
    $rank = new Esportz_Rank;
    $rank->update_user_rank( $user_id );
}

add_action( 'user_register', 'esportz_update_user_rank' );


/**
 * Update user rank after experience balance changing
 * @param $result
 * @param $request
 * @return mixed
 */
function esportz_update_rank_on_exp_changing( $result, $request ) {
    if ( class_exists( 'Esportz_Badge' )  &&  class_exists( 'Esportz_Rank' )  && $result ) {
        $rank = new Esportz_Rank;
        $badge = new Esportz_Badge;
        $user_id = get_current_user_id();

        $rank->update_user_rank( $user_id );

        $ref = $request['ref'];

        if ( $ref === 'daily_visit' || $ref === 'comments' || $ref === 'social_sharing' || $ref === 'won_tournaments' ) {

            $old_value = $badge->get_user_badge_level( $user_id, $ref );
            $new_value = $badge->update_user_badge_level( $user_id, $ref, 'inc' );

            if ( $new_value[$ref] ) {
                $badge->is_new_badge( $user_id, $ref, $old_value, $new_value );
            }

        } else if ( $ref === 'profile_fill' ) {

            $badge_status = $badge->get_user_badge_level( $user_id, 'profile_fill' );

            if ( $badge_status < 1 ) {
                $badge->update_user_badge_level( $user_id, 'profile_fill', 'inc' );
                if(class_exists('myCRED_Core'))
                    mycred_add( 'new_badge', $user_id, 10, 'Reward for new badge', null, null, 'mycred_exp' );
            }

        }

    }

    return $result;
}

add_filter( 'mycred_add_finished', 'esportz_update_rank_on_exp_changing', 10, 3 );

/**
 * Saves all unique user logins.
 *
 * @return bool
 */
function esportz_user_save_logins() {
    $user_id = get_current_user_id();

    if ( $user_id && ! isset( $_COOKIE['esportz_daily_visit'] ) ) {
        setcookie('esportz_daily_visit', true,  strtotime( '+1 days' ) );
        $logins = get_user_meta( $user_id, 'esportz_user_logins', true ) ?: array();
        $today = date( 'Y-m-d' );

        if ( ! in_array( $today, $logins ) ) {
            $logins[] = $today;
            $update_result = update_user_meta( $user_id, 'esportz_user_logins', $logins );

            if ( class_exists( 'Esportz_Badge' ) ) {
                $badge = new Esportz_Badge;
                $badge->update_user_badge_level( $user_id, 'site_visit', 'inc' );
            }

            return $update_result ? true : false;
        } else {
            return false;
        }

    }

}

add_action( 'wp_footer', 'esportz_user_save_logins' );

/**
 * Check streak status and change it if user broke his rank streak
 *
 * @param int $user_id
 * @param int $visits Count of visits per week
 *
 * @return void
 */
function check_rank_daily_visits_streak( $user_id, $visits ) {

    if(!class_exists( 'Esportz_Rank' ) ){
        return false;
    }

    $rank = new Esportz_Rank;
    $user_rank = $rank->get_user_rank( $user_id );
    $logins = get_user_meta( $user_id, 'esportz_user_logins', true ) ?: array();
    $week_logins = 0;

    $now = date( 'Y-m-d' );
    $week_ago = date( 'Y-m-d', strtotime( '-7 days' ) );

    foreach ( $logins as $login ) {
        if ( $login >= $week_ago && $login <= $now ) {
            $week_logins++;
        }
    }

    if ( $week_logins >= $visits ) {
        wp_schedule_single_event(
            strtotime( '+7 days' ),
            'rank_daily_visits_streak',
            array( 'user_id' => $user_id, 'visits' => $user_rank['logins_per_week'] )
        );
    } else {
        $broke_streak_points = $user_rank['points_streak_dropped'];

        $exp = '';
        if(class_exists('myCRED_Core'))
            $exp = mycred_get_users_balance( $user_id, 'mycred_exp' );
        $exp_difference = $exp - $broke_streak_points;

        if(class_exists('myCRED_Core'))
            mycred_subtract(
                'streak_broken',
                $user_id,
                $exp_difference,
                'Subtracting points for steak braking',
                null,
                null,
                'mycred_exp'
            );

        $rank->update_user_rank( $user_id );
    }

}

add_action( 'rank_daily_visits_streak', 'check_rank_daily_visits_streak', 10, 2 );


#-----------------------------------------------------------------#
# Account levels
#-----------------------------------------------------------------#

function esportz_remove_account_level( $user_id ) {

    if(!class_exists( 'Esportz_Account_Level' ) ){
        return false;
    }

    $level = new Esportz_Account_Level;
    $level->set_user_basic_account_level( $user_id );
}

add_action( 'remove_account_level', 'esportz_remove_account_level', 10, 1 );

/**
 * Set account level on sign up.
 * @param $user_id
 * @return bool
 */
function esportz_set_user_basic_account_level( $user_id ) {

    if(!class_exists( 'Esportz_Account_Level' ) ){
        return false;
    }

    $level = new Esportz_Account_Level;
    $level->set_user_basic_account_level( $user_id );
}

add_action( 'user_register', 'esportz_set_user_basic_account_level' );



#-----------------------------------------------------------------#
# User activity
#-----------------------------------------------------------------#

/**
 * Returns activity case's HTML
 *
 * @param string $type Points ref from mycred log
 * @param string $points Count of points
 * @param string $points_type myCred's points type
 * @param string $additional_content myCred's "data" field (useful for setting activity subtext or use it in title)
 *
 * @return void
 */
function get_activity_case_markup( $type, $points, $points_type, $additional_content = null ) {
    $user_id = get_query_var( 'author' );
    $current_user = get_user_by('id', $user_id);
    $user_name = $current_user->display_name;

    $options = get_option( 'esportz_redux' );

    $gender = get_user_meta($user_id, 'gender', true);

    if($gender == 'male'){
        $pronoun = esc_html__('his', 'esportz');
    }else{
        $pronoun = esc_html__('hers', 'esportz');
    }


    $i18n = array(
        'social_sharing' => sprintf(__( '<b>%s</b> shared an article', 'esportz' ), esc_html( $user_name ) ),
        'account_level_buying' => sprintf( __( '<b>%s</b> bought a new account level', 'esportz' ), esc_html( $user_name ) ),
        'site_visit' => sprintf( __( '<b>%s</b> got bonus for site visit', 'esportz' ), esc_html( $user_name ) ),
        'comments' => sprintf( __( '<b>%s</b> commented on an article', 'esportz' ), esc_html( $user_name ) ),
        'new_badge' => sprintf( __( '<b>%s</b> earned a new badge!', 'esportz' ), esc_html( $user_name ) ),
        'new_user_level' => sprintf( __( '<b>%s</b> reached Level %d!', 'esportz' ), esc_html( $user_name ), esc_html( $additional_content ) ),
        'join_team' => sprintf( __( '<b>%s</b> joined a team!', 'esportz' ), esc_html( $user_name ) ),
        'profile_fill' => sprintf( __( '<b>%s</b> completed ', 'esportz' ), esc_html( $user_name ) ). $pronoun . esc_html__(' profile!', 'esportz'),
        'registration' => sprintf( __( '<b>%s</b> signed up!', 'esportz' ), esc_html( $user_name ) ),
        'tournament_won' => sprintf( __( '<b>%s</b> won tournament!', 'esportz' ), esc_html( $user_name ) ),
        'join_tournament' => sprintf( __( '<b>%s</b> joined tournament!', 'esportz' ), esc_html( $user_name ) ),
    );

    $src = $options[ $type . '_activity_icon']['url'];

    if ( $points > 0 ) {
        $points = '+' . $points;
    }

    if ( $points_type === 'mycred_default' ) {
        $points_type = 'Coins';
    } else if ( $points_type === 'mycred_exp' ) {
        $points_type = 'Exp';
    } else {
        $points_type = '';
    }

    ?>

    <img src="<?php echo esc_url( $src ); ?>">
    <h4><?php echo wp_kses_post($i18n[$type]); ?></h4>
    <span class="plus-exp"><?php echo esc_html( $points . ' ' . $points_type ); ?></span>

    <?php if ( $additional_content && $type !== 'new_user_level' ) { ?>
        <p class="full-width-description"><?php echo esc_html( $additional_content ); ?></p>
    <?php  }

}

#-----------------------------------------------------------------#
# User badges
#-----------------------------------------------------------------#

/**
 * Updates user account age and creates next cron task for this.
 *
 * @param int $user_id
 * @return bool
 */
function esportz_update_account_age_badge( $user_id ) {

    if(!class_exists( 'Esportz_Badge' ) ){
        return false;
    }

    $badge = new Esportz_Badge;
    $age = $badge->get_account_age( $user_id );
    $badge->set_user_badge_value( $user_id, 'account_age', $age );

    wp_schedule_single_event(
        strtotime( '+1 month' ),
        'esportz_update_user_account_age',
        array( $user_id )
    );
}

add_action( 'esportz_update_user_account_age' , 'esportz_update_account_age_badge', 10, 1 );

/**
 * Set basic badges state on sign up and a cron task for current user
 * to update its account age in 1 month.
 *
 * @param int $user_id
 * @return bool
 */
function esportz_set_user_basic_badges( $user_id ) {

    if(!class_exists( 'Esportz_Badge' ) ){
        return false;
    }

    $badge = new Esportz_Badge;
    $badge->set_basic_user_settings( $user_id );

    wp_schedule_single_event(
        strtotime( '+1 month' ),
        'esportz_update_user_account_age',
        array( $user_id )
    );

}

add_action( 'user_register', 'esportz_set_user_basic_badges', 10, 1 );

/**
 * Set reward for post commenting.
 * @param $comment_id
 * @param $comment_approved
 * @param $commentdata
 */
function esportz_update_comment_badge( $comment_id, $comment_approved, $commentdata ) {

    $user_id = $commentdata['user_id'];
    $comment_content = $commentdata['comment_content'];
    $reward = (int) get_option( 'esportz_redux' )['new-comment-exp'];

    if(class_exists('myCRED_Core'))
        mycred_add( 'comments', $user_id, $reward, 'Reward for post commenting', null, $comment_content, 'mycred_exp' );

}

add_action( 'comment_post', 'esportz_update_comment_badge', 10, 3 );


#-----------------------------------------------------------------#
# Email
#-----------------------------------------------------------------#
/**
 * Emails header
 * @return string
 */
function esportz_email_header(){
    return '
    <table data-module="noti-3" class="full selected-table" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tbody><tr>
      <td data-bgcolor="Main BG" data-bg="Main BG" background="'.get_theme_file_uri('assets/img/bg-1.jpg').'" bgcolor="#494c50" valign="top" style="background-size: cover; background-position: center top; background-image: url("'.get_theme_file_uri('assets/img/bg-1.jpg').'");">
        <table class="table-inner ui-resizable" width="550" style="max-width: 550px;" border="0" align="center" cellpadding="0" cellspacing="0">
          <tbody><tr>
            <td height="50"></td>
          </tr>
          <tr>
            <td data-bgcolor="Header Bar" bgcolor="#ff343b" style="border-radius:4px;" align="center" width="500">
              <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tbody><tr>
                  <td height="15"></td>
                </tr>
                <tr>
                  <td align="center">
                    <!--logo-->
                    <table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 100%;">
                      <tbody><tr>
                        <td align="center" style="line-height: 0px;"><a href="'.get_site_url().'" target="_blank"><img mc:edit="noti-3-1" editable="" label="logo" src="'.get_theme_file_uri('assets/img/logo.jpg').'" style="display: block; line-height: 0px; font-size: 0px; border: 0px; max-width: 200px;" class=""></a></td>
                      </tr>
                    </tbody></table>
                    <!--end logo-->
                    
                  </td>
                </tr>
                <tr>
                  <td height="15" class="selected-element" contenteditable="true"></td>
                </tr>
              </tbody></table>
            </td>
          </tr>
          <tr>
            <td height="15"></td>
          </tr>
          <tr>
            <td data-bgcolor="Content BG" bgcolor="#FFFFFF" style="border-top-left-radius: 4px; border-top-right-radius: 4px;" align="center">
    ';
}

/**
 * Emails footer
 * @return string
 */
function esportz_email_footer(){
    return '
              </td>
          </tr>
        </tbody><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div></table>
        
        <table class="table-inner ui-resizable" width="550" border="0" align="center" cellpadding="0" cellspacing="0">
          <tbody><tr>
            <td height="15"></td>
          </tr>
          <tr>
            <td align="center">
              <!--copyright-->
              <table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0">
                <tbody><tr>
                  <td mc:edit="noti-3-9" data-color="copyright" data-size="footer" align="center" style="font-family: \'Open Sans\', Arial, sans-serif; font-size:12px; color:#ffffff; ">
                    <singleline label="copyright">© '.date('Y').' Esportz Network All Rights Reserved</singleline>
                  </td>
                </tr>
              </tbody></table>
              <!-- end copyright -->
              <!--[if (gte mso 9)|(IE)]></td><td><![endif]-->
              <table class="table-full" width="15" align="left" border="0" cellpadding="0" cellspacing="0">
                <tbody><tr>
                  <td height="15"></td>
                </tr>
              </tbody></table>
              <!--[if (gte mso 9)|(IE)]></td><td><![endif]-->
              <!-- preference -->
              <table class="table-full" border="0" align="right" cellpadding="0" cellspacing="0">
                <tbody><tr>
                  <td mc:edit="noti-3-10" data-color="preference" data-size="footer" align="center" style="font-family: \'Open Sans\', Arial, sans-serif; font-size:12px; color:#ffffff; ">

                  </td>
                </tr>
              </tbody></table>
              <!--end preference-->
            </td>
          </tr>
          <tr>
            <td height="45"></td>
          </tr>
        </tbody><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div></table>
      </td>
    </tr>
  </tbody></table>
    ';
}

#-----------------------------------------------------------------#
# WP Admin
#-----------------------------------------------------------------#
/**
 * Hide WP admin
 */
function esportz_block_users_backend() {
    if ( is_admin() && ! current_user_can( 'administrator' ) && ! wp_doing_ajax() ) {
        wp_redirect( home_url() );
        exit;
    }
}
add_action( 'init', 'esportz_block_users_backend' );


function esportz_remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'esportz_remove_admin_bar');

/**
 * Change author slug WP
 */
function esportz_author_base() {
    global $wp_rewrite;
    $author_slug = 'user'; // change slug name
    $wp_rewrite->author_base = $author_slug;
}
add_action('init', 'esportz_author_base');


/**
 * Add admin menus
 */
function esportz_add_admin_menu()
{

    $iconData = file_get_contents( get_theme_file_uri('addons/wp-team-matches/images/plugin-icon.svg') );
    $iconDataURI = 'data:image/svg+xml;base64,' . base64_encode($iconData);

    //Pro team wars
    add_menu_page(
        esc_html__('Pro Team Wars', 'esportz'),
        esc_html__('Pro Team Wars', 'esportz'),
        'manage_options', 'pro_team_wars',
        '',
        $iconDataURI,
        25
    );

    //Ranking
    add_submenu_page(
        'edit.php?post_type=article',
        esc_html__('Ranking', 'esportz'),
        esc_html__('Ranking', 'esportz'),
        'manage_options',
        'ranking',
        'esportz_ranking_admin_page'
    );

}

add_action('admin_menu', 'esportz_add_admin_menu');


/**
 * Add ranking page
 */
function esportz_ranking_admin_page()
{ ?>
    <div class="wrap">
        <h2><?php esc_html_e('Ranking', 'esportz'); ?></h2>
    </div>
    <?php

    $options = esportz_get_theme_options();
    $rating_params = $options['rating-options'];

    $args = [
        'role' => ['reporter', 'photvid'],
        'orderby' => 'meta_value',
        'meta_key' => 'overall_rating',
        'order' => 'DESC',
        'meta_query' => [
            [
                'key' => 'overall_rating',
                'type' => 'numeric',
            ],
        ],
    ];

    // The Query
    $user_query = new WP_User_Query($args);
    $authors = $user_query->get_results();

    ?>

    <table class="wp-list-table widefat fixed striped pages">

        <thead>
        <tr>
            <th scope="col" class="manage-column column-reporter column-primary sortable desc">
                <a>
                    <span>#</span>
                </a>
            </th>
            <th scope="col" class="manage-column column-reporter column-primary sortable desc">
                <span><?php esc_html_e('Reporter', 'esportz'); ?></span>
            </th>

            <th scope="col" class="manage-column column-email column-primary sortable desc">
                <span><?php esc_html_e('Email', 'esportz'); ?></span>
            </th>

            <th scope="col" class="manage-column column-overall-rating column-primary sortable desc">
                <span><?php esc_html_e('Overall rating', 'esportz'); ?></span>
            </th>

            <?php
            if (is_array($rating_params))
                foreach ($rating_params as $param) :

                    echo '<th scope="col" class="manage-column column-'.esc_html(strtolower($param)).' column-primary sortable desc"><span>'.esc_html($param).'</span></th>';

                endforeach;
            ?>

        </tr>
        </thead>

        <tbody id="the-list">

        <?php
        $i = 0;
        if (is_array($authors))
            foreach ($authors as $i => $author) :

                $overall_rating = get_user_meta($author->ID, 'overall_rating', true);
                $user_info = get_userdata($author->ID);
                $first_name = $user_info->first_name;
                $last_name = $user_info->last_name;
                $email = $user_info->user_email;


                switch ($overall_rating){

                    case '0':
                        $overall_rating = 'D';
                        break;
                    case '0.5':
                        $overall_rating = 'C';
                        break;
                    case '1':
                        $overall_rating = 'C+';
                        break;
                    case '1.5':
                        $overall_rating = 'B';
                        break;
                    case '2':
                        $overall_rating = 'B+';
                        break;
                    case '2.5':
                        $overall_rating = 'A';
                        break;
                    case '3':
                        $overall_rating = 'A+';
                        break;

                }
                ?>

                <tr  class="iedit author-self type-page status-publish hentry">

                    <td class="date column-date">
                        <div>
                            <span><?php echo esc_html($i); ?> </span>
                        </div>
                    </td>
                    <td class="date column-date">
                        <div>
                            <span><a href="<?php echo get_edit_user_link($author->ID); ?>"><?php echo esc_html($first_name).' '.esc_html($last_name); ?></a></span>
                        </div>
                    </td>

                    <td class="date column-email">
                        <div>
                            <span><?php echo esc_html($email); ?></span>
                        </div>
                    </td>

                    <td class="date column-rating-all">
                        <div>
                            <span><?php echo esc_html($overall_rating) ?></span>
                        </div>
                    </td>

                    <?php
                    if (is_array($rating_params))
                        foreach ($rating_params as $param) :
                            $param_escaped = str_replace(' ', '', strtolower($param));
                            $meta = get_user_meta($author->ID, $param_escaped, true);

                            switch ($meta){

                                case '0':
                                    $meta = 'D';
                                    break;
                                case '0.5':
                                    $meta = 'C';
                                    break;
                                case '1':
                                    $meta = 'C+';
                                    break;
                                case '1.5':
                                    $meta = 'B';
                                    break;
                                case '2':
                                    $meta = 'B+';
                                    break;
                                case '2.5':
                                    $meta = 'A';
                                    break;
                                case '3':
                                    $meta = 'A+';
                                    break;

                            }

                            echo '<td class="date column-rating-all"><div><span>'.esc_html($meta).'</span></div></td>';

                        endforeach;
                    ?>
                </tr>

                <?php $i++;  endforeach; ?>

        </tbody>
    </table>
    <?php

}


#-----------------------------------------------------------------#
# RSS
#-----------------------------------------------------------------#
/**
 * Create new rss feed
 */
function esportz_reuters_rss_article(){
    add_feed('reuters-article', 'esportz_reuters_rss_article_function');
}
add_action('init', 'esportz_reuters_rss_article');

function esportz_reuters_rss_article_function(){
    get_template_part('rss', 'reutersRSSFeedArticle');
}

function esportz_reuters_rss_picture(){
    add_feed('reuters-picture', 'esportz_reuters_rss_picture_function');
}
add_action('init', 'esportz_reuters_rss_picture');

function esportz_reuters_rss_picture_function(){
    get_template_part('rss', 'reutersRSSFeedPicture');
}

function esportz_reuters_rss_video(){
    add_feed('reuters-video', 'esportz_reuters_rss_video_function');
}
add_action('init', 'esportz_reuters_rss_video');

function esportz_reuters_rss_video_function(){
    get_template_part('rss', 'reutersRSSFeedVideo');
}

/**
 * Add attachment extra fields
 * @param $form_fields
 * @param $post
 * @return mixed
 */
function esportz_attachment_extra_field( $form_fields, $post ) {

    $form_fields['pic-photographer-name'] = array(
        'label' => 'Photographer Name',
        'input' => 'text',
        'value' => get_post_meta( $post->ID, 'pic_photographer_name', true ),
    );

    $form_fields['pic-credits'] = array(
        'label' => 'Credits',
        'input' => 'text',
        'value' => get_post_meta( $post->ID, 'pic_credits', true ),
    );

    $form_fields['video-keywords'] = array(
        'label' => 'Video keywords',
        'input' => 'text',
        'value' => get_post_meta( $post->ID, 'video_keywords', true ),
    );


    $media_to_reuters = (bool) get_post_meta($post->ID, 'media_to_reuters_rss', true);
    $checked = '';
    if($media_to_reuters){
        $checked = 'checked';
    }
    $form_fields['media-reuters-rss'] = array(
        'label' => 'Send to Reuters RSS',
        'input' => 'html',
        'html' => '<label for="attachments-'.$post->ID.'-foo"> '.
            '<input type="checkbox" id="attachments-'.$post->ID.'-foo" name="attachments['.$post->ID.'][media-reuters-rss]" value="1" '.$checked.' /> </label>  ',
        'value' => $media_to_reuters,
    );

    return $form_fields;
}

add_filter( 'attachment_fields_to_edit', 'esportz_attachment_extra_field', 10, 2 );

/**
 * Save attachment extra fields
 * @param $post
 * @param $attachment
 * @return mixed
 */
function esportz_attachment_extra_field_save( $post, $attachment ) {
    if( isset( $attachment['pic-photographer-name'] ) )
        update_post_meta( $post['ID'], 'pic_photographer_name', $attachment['pic-photographer-name'] );

    if( isset( $attachment['pic-credits'] ) )
        update_post_meta( $post['ID'], 'pic_credits', $attachment['pic-credits'] );

    if( isset( $attachment['video-keywords'] ) )
        update_post_meta( $post['ID'], 'video_keywords', $attachment['video-keywords'] );

    update_post_meta( $post['ID'], 'media_to_reuters_rss', $attachment['media-reuters-rss'] );

    return $post;
}

add_filter( 'attachment_fields_to_save', 'esportz_attachment_extra_field_save', 10, 2 );

define( 'NINJA_FORMS_UPLOADS_USE_PUBLIC_URL', true );
/**
 * Photo/video form submit action
 * @param $form_data
 */
function esportz_photovid_form_submit( $form_data){

    $form_id       = $form_data[ 'form_id' ];
    $form_fields   =  $form_data[ 'fields' ];
    update_user_meta(get_current_user_id(), 'dropboxfiles', $form_data);

}
add_action( 'ninja_forms_after_submission', 'esportz_photovid_form_submit');




