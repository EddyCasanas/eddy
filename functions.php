<?php
/**
 * Sabino functions and definitions
 *
 * @package Sabino
 */
define( 'SABINO_THEME_VERSION' , '1.2.00' );

// Get help / Premium Page
require get_template_directory() . '/upgrade/upgrade.php';

// Load WP included scripts
require get_template_directory() . '/includes/inc/template-tags.php';
require get_template_directory() . '/includes/inc/extras.php';
require get_template_directory() . '/includes/inc/jetpack.php';

// Load Customizer Library scripts
require get_template_directory() . '/customizer/customizer-options.php';
require get_template_directory() . '/customizer/customizer-library/customizer-library.php';
require get_template_directory() . '/customizer/styles.php';
require get_template_directory() . '/customizer/mods.php';

// Load TGM plugin class
require_once get_template_directory() . '/includes/inc/class-tgm-plugin-activation.php';
// Add customizer Upgrade class
require_once( get_template_directory() . '/includes/sabino-pro/class-customize.php' );

if ( ! function_exists( 'sabino_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function sabino_setup() {
	
	/**
	 * Set the content width based on the theme's design and stylesheet.
	 */
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 900; /* pixels */
	}

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on sabino, use a find and replace
	 * to change 'sabino' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'sabino', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary-menu' => esc_html__( 'Primary Menu', 'sabino' ),
		'secondary-menu' => esc_html__( 'Header Secondary Menu', 'sabino' ),
        'footer-bar' => esc_html__( 'Footer Bar Menu', 'sabino' )
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );
	
	// The custom logo is used for the logo
	add_theme_support( 'custom-logo', array(
		'height'      => 280,
		'width'       => 145,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'sabino_custom_background_args', array(
		'default-color'      => 'F9F9F9',
		'default-image'      => get_template_directory_uri() . '/images/demo/sabino-background-image.jpg',
		'default-size'       => 'cover',
		'default-attachment' => 'fixed'
	) ) );
	
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
endif; // sabino_setup
add_action( 'after_setup_theme', 'sabino_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function sabino_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'sabino' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
	
	register_sidebar(array(
		'name' => __( 'Sabino Footer Standard', 'sabino' ),
		'id' => 'sabino-site-footer-standard',
        'description' => __( 'The footer will divide into however many widgets are placed here.', 'sabino' )
	));
}
add_action( 'widgets_init', 'sabino_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function sabino_scripts() {
	wp_enqueue_style( 'sabino-font-default', '//fonts.googleapis.com/css?family=Dosis:200,300,400,500,600,700,800|Open+Sans:300,300i,400,400i,600,600i,700,700i', array(), SABINO_THEME_VERSION );
	
	wp_enqueue_style( 'sabino-font-awesome', get_template_directory_uri().'/includes/font-awesome/css/all.min.css', array(), '6.1.1' );
	wp_enqueue_style( 'sabino-style', get_stylesheet_uri(), array(), SABINO_THEME_VERSION );
	
	if ( sabino_is_woocommerce_activated() ) :
		wp_enqueue_style( 'sabino-woocommerce-style', get_template_directory_uri().'/templates/css/woocommerce.css', array(), SABINO_THEME_VERSION );
	endif;
	
	wp_enqueue_script( 'caroufredsel', get_template_directory_uri() . "/js/caroufredsel/jquery.carouFredSel-6.2.1-packed.js", array('jquery'), SABINO_THEME_VERSION, true );
	wp_enqueue_script( 'sabino-custom-js', get_template_directory_uri() . "/js/custom.js", array('jquery'), SABINO_THEME_VERSION, true );
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'sabino_scripts' );

/**
 * Fix skip link focus in IE11. Too small to load as own script
 */
function sabino_custom_footer_scripts() {
	// The following is minified via 'terser --compress --mangle -- js/skip-link-focus-fix.js' ?>
	<script>
	/(trident|msie)/i.test(navigator.userAgent)&&document.getElementById&&window.addEventListener&&window.addEventListener("hashchange",function(){var t,e=location.hash.substring(1);/^[A-z0-9_-]+$/.test(e)&&(t=document.getElementById(e))&&(/^(?:a|select|input|button|textarea)$/i.test(t.tagName)||(t.tabIndex=-1),t.focus())},!1);
	</script><?php
}
add_action( 'wp_print_footer_scripts', 'sabino_custom_footer_scripts' );

/**
 * Add pingback to header
 */
function sabino_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
	}
}
add_action( 'wp_head', 'sabino_pingback_header' );

/**
 * To maintain backwards compatibility with older versions of WordPress
 */
function sabino_the_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}

/**
 * Add theme stying to the theme content editor
 */
function sabino_add_editor_styles() {
    add_editor_style( 'style-theme-editor.css' );
}
add_action( 'admin_init', 'sabino_add_editor_styles' );

/**
 * Enqueue admin styling.
 */
function sabino_load_admin_script() {
    wp_enqueue_style( 'sabino-admin-css', get_template_directory_uri() . '/upgrade/css/admin-css.css', array(), SABINO_THEME_VERSION );
}
add_action( 'admin_enqueue_scripts', 'sabino_load_admin_script' );

/**
 * Enqueue sabino custom customizer styling.
 */
function sabino_load_customizer_script() {
	wp_enqueue_script( 'sabino-customizer-js', get_template_directory_uri() . '/customizer/customizer-library/js/customizer-custom.js', array('jquery'), SABINO_THEME_VERSION, true );
    wp_enqueue_style( 'sabino-customizer-css', get_template_directory_uri() . '/customizer/customizer-library/css/customizer.css' );
}
add_action( 'customize_controls_enqueue_scripts', 'sabino_load_customizer_script' );

/**
 * Check if WooCommerce exists.
 */
if ( ! function_exists( 'sabino_is_woocommerce_activated' ) ) :
	function sabino_is_woocommerce_activated() {
	    if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
	}
endif; // sabino_is_woocommerce_activated

// If WooCommerce exists include ajax cart
if ( sabino_is_woocommerce_activated() ) {
	require get_template_directory() . '/includes/inc/woocommerce-cart.php';
}

/**
 * Add classed to the body tag from settings
 */
function sabino_add_body_class( $classes ) {
	// Blog Pages
	if ( get_theme_mod( 'sabino-blog-leftsidebar' ) ) {
		$classes[] = 'sabino-blog-leftsidebar';
	}
	if ( get_theme_mod( 'sabino-blog-archive-leftsidebar' ) ) {
		$classes[] = 'sabino-blog-archives-leftsidebar';
	}
	if ( get_theme_mod( 'sabino-blog-single-leftsidebar' ) ) {
		$classes[] = 'sabino-blog-single-leftsidebar';
	}
	
	// WooCommerce Pages
	if ( get_theme_mod( 'sabino-woocommerce-shop-leftsidebar' ) ) {
		$classes[] = 'sabino-shop-leftsidebar';
	}
	if ( get_theme_mod( 'sabino-woocommerce-shop-archive-leftsidebar' ) ) {
		$classes[] = 'sabino-shop-archives-leftsidebar';
	}
	if ( get_theme_mod( 'sabino-woocommerce-shop-single-leftsidebar' ) ) {
		$classes[] = 'sabino-shop-single-leftsidebar';
	}
	
	if ( get_theme_mod( 'sabino-woocommerce-shop-fullwidth' ) ) {
		$classes[] = 'sabino-shop-full-width';
	}
	if ( get_theme_mod( 'sabino-woocommerce-shop-archive-fullwidth' ) ) {
		$classes[] = 'sabino-shop-archives-full-width';
	}
	if ( get_theme_mod( 'sabino-woocommerce-shop-single-fullwidth' ) ) {
		$classes[] = 'sabino-shop-single-full-width';
	}
	
	return $classes;
}
add_filter( 'body_class', 'sabino_add_body_class' );

/**
 * If set, add inline style for page featured image
 */
function sabino_page_featured_image_inline_css() {
	wp_enqueue_style( 'sabino-style', get_stylesheet_uri() );
	
    $sabino_page_featured_image = '';
    
    if ( is_home() || is_archive() || is_search() || is_single() ) {
	    if ( sabino_is_woocommerce_activated() ) {
	        if ( is_woocommerce() ) {
	            $shop = get_option( 'woocommerce_shop_page_id' );
	            $sabino_page_featured_image = wp_get_attachment_url( get_post_thumbnail_id( $shop ) );
	        } else {
	        	$page = get_option( 'page_for_posts' );
	        	$sabino_page_featured_image = wp_get_attachment_url( get_post_thumbnail_id( $page ) );
	        }
	    } else {
	        $page = get_option( 'page_for_posts' );
	        $sabino_page_featured_image = wp_get_attachment_url( get_post_thumbnail_id( $page ) );
	    }
	} elseif ( is_page() ) {
	    $page = get_queried_object();
		$sabino_page_featured_image = wp_get_attachment_url( get_post_thumbnail_id( $page->ID ) );
	} else {
		$page_id = get_the_ID();
		$sabino_page_featured_image = wp_get_attachment_url( get_post_thumbnail_id( $page_id ) );
	}
    
    if ( $sabino_page_featured_image ) {
    	$sabino_page_featured_img = htmlspecialchars_decode( 'body { background-image: url(' . $sabino_page_featured_image . ') !important; }' );
    	wp_add_inline_style( 'sabino-style', $sabino_page_featured_img );
    }
}
add_action( 'wp_enqueue_scripts', 'sabino_page_featured_image_inline_css' );

/**
 * Add classes to the blog list for styling.
 */
function sabino_add_post_classes ( $classes ) {
	global $current_class;
	
	if ( is_home() || is_archive() || is_search() ) :
		$sabino_blog_layout = 'blog-left-layout';
		if ( get_theme_mod( 'sabino-set-blog-layout' ) ) {
		    $sabino_blog_layout = get_theme_mod( 'sabino-set-blog-layout' );
		}
		$classes[] = $sabino_blog_layout;
		
		$classes[] = $current_class;
		$current_class = ( $current_class == 'blog-alt-odd' ) ? sanitize_html_class( 'blog-alt-even' ) : sanitize_html_class( 'blog-alt-odd' );
	endif;
	
	return $classes;
}
global $current_class;
$current_class = sanitize_html_class( 'blog-alt-odd' );
add_filter ( 'post_class' , 'sabino_add_post_classes' );

/**
 * Adjust is_home query if sabino-blog-cats is set
 */
function sabino_set_blog_queries( $query ) {
    $blog_query_set = '';
    if ( get_theme_mod( 'sabino-blog-cats' ) ) {
        $blog_query_set = get_theme_mod( 'sabino-blog-cats' );
    }
    
    if ( $blog_query_set ) {
        // do not alter the query on wp-admin pages and only alter it if it's the main query
        if ( !is_admin() && $query->is_main_query() ){
            if ( is_home() ){
                $query->set( 'cat', $blog_query_set );
            }
        }
    }
}
add_action( 'pre_get_posts', 'sabino_set_blog_queries' );

/**
 * Display recommended plugins with the TGM class
 */
function sabino_register_required_plugins() {
	$plugins = array(
		// The recommended WordPress.org plugins.
		array(
			'name'      => __( 'Elementor Page Builder', 'sabino' ),
			'slug'      => 'elementor',
			'required'  => false,
		),
		array(
			'name'      => __( 'Blockons (WordPress Editor Blocks)', 'sabino' ),
			'slug'      => 'blockons',
			'required'  => false,
		),
		array(
			'name'      => __( 'WooCommerce', 'sabino' ),
			'slug'      => 'woocommerce',
			'required'  => false,
		),
		array(
			'name'      => __( 'StoreCustomizer', 'sabino' ),
			'slug'      => 'woocustomizer',
			'required'  => false,
		),
		array(
			'name'      => __( 'Breadcrumb NavXT', 'sabino' ),
			'slug'      => 'breadcrumb-navxt',
			'required'  => false,
		),
		array(
			'name'      => __( 'Hubspot', 'sabino' ),
			'slug'      => 'leadin',
			'required'  => false,
		)
	);
	$config = array(
		'id'           => 'sabino',
		'menu'         => 'tgmpa-install-plugins',
		'message'      => '',
	);

	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'sabino_register_required_plugins' );

/**
 * Register a custom Post Categories ID column
 */
function sabino_edit_cat_columns( $sabino_cat_columns ) {
    $sabino_cat_in = array( 'cat_id' => 'Category ID <span class="cat_id_note">For the Default Slider</span>' );
    $sabino_cat_columns = sabino_cat_columns_array_push_after( $sabino_cat_columns, $sabino_cat_in, 0 );
    return $sabino_cat_columns;
}
add_filter( 'manage_edit-category_columns', 'sabino_edit_cat_columns' );

/**
 * Print the ID column
 */
function sabino_cat_custom_columns( $value, $name, $cat_id ) {
    if( 'cat_id' == $name ) 
        echo $cat_id;
}
add_filter( 'manage_category_custom_column', 'sabino_cat_custom_columns', 10, 3 );

/**
 * Insert an element at the beggining of the array
 */
function sabino_cat_columns_array_push_after( $src, $sabino_cat_in, $pos ) {
    if ( is_int( $pos ) ) {
        $R = array_merge( array_slice( $src, 0, $pos + 1 ), $sabino_cat_in, array_slice( $src, $pos + 1 ) );
    } else {
        foreach ( $src as $k => $v ) {
            $R[$k] = $v;
            if ( $k == $pos )
                $R = array_merge( $R, $sabino_cat_in );
        }
    }
    return $R;
}

/**
 * Admin notice for dashboard help
 */
function sabino_add_license_notice() {
    global $pagenow;
	global $current_user;
    $sabino_user_id = $current_user->ID;
    $sabinopage = isset( $_GET['page'] ) ? $pagenow . '?page=' . $_GET['page'] : $pagenow;

	if ( !get_user_meta( $sabino_user_id, 'sabino_flash_notice_ignore' ) ) : ?>
		<div class="notice notice-info sabino-admin-notice sabino-notice-add">
			<h4><?php esc_html_e( 'Thank you for trying out Sabino!', 'sabino' ); ?></h4>
            <p>
				<?php printf( __( 'We\'re here to help... Please <a href="%s">read through our help notes</a> on getting started with Sabino.', 'sabino' ) , admin_url( 'themes.php?page=theme_info' ) ); ?>
				<?php
					/* translators: %s: 'Upgrade to Premium' */
					printf( esc_html__( '%1$s for only $25', 'sabino' ), wp_kses( __( '<a href="themes.php?page=theme_info">Upgrade to Premium</a>', 'sabino' ), array( 'a' => array( 'href' => array() ) ) ) );
					?>
			</p>

            <?php if ( $sabinopage == 'themes.php?page=theme_info' ) : ?>
                <div class="sabino-admin-notice-blocks">
                    <div class="sabino-admin-notice-block">
                        <h5><?php esc_html_e( 'About Sabino:', 'sabino' ); ?></h5>
                        <p>
                            <?php esc_html_e( 'Sabino is a widely used and much loved WordPress theme which gives you lots of different customization settings... so you can easily change the look of your site any time.', 'sabino' ); ?>
                        </p>
                        <p>
                            <?php
                            /* translators: %s: 'Recommended Resources' */
                            printf( esc_html__( 'Read through our %1$s and %2$s and we\'ll help you build a professional website easily.', 'sabino' ), wp_kses( __( '<a href="https://kairaweb.com/support/wordpress-recommended-resources/" target="_blank">Recommended Resources</a>', 'sabino' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ), wp_kses( __( '<a href="https://kairaweb.com/documentation/" target="_blank">Kaira Documentation</a>', 'sabino' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ) );
                            ?>
                        </p>
                        <a href="<?php echo esc_url( admin_url( 'themes.php?page=theme_info' ) ) ?>" class="sabino-admin-notice-btn">
                            <?php esc_html_e( 'Read More About Sabino', 'sabino' ); ?>
                        </a>
                    </div>
                    <div class="sabino-admin-notice-block">
                        <h5><?php esc_html_e( 'Using Sabino:', 'sabino' ); ?></h5>
                        <p>
                            <?php
                            /* translators: %s: 'set up your site in WordPress' */
                            printf( esc_html__( 'See our recommended %1$s and how to get ready before you start building your website after you\'ve %2$s.', 'sabino' ), wp_kses( __( '<a href="https://kairaweb.com/documentation/our-recommended-wordpress-basic-setup/" target="_blank">WordPress basic setup</a>', 'sabino' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ), wp_kses( __( '<a href="https://kairaweb.com/wordpress-hosting/" target="_blank">setup WordPress Hosting</a>', 'sabino' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ) );
                            ?>
                        </p>
                        <a href="<?php echo esc_url( 'https://kairaweb.com/support/wordpress-recommended-resources/' ) ?>" class="sabino-admin-notice-btn-in" target="_blank">
                            <?php esc_html_e( 'Recommended Resources', 'sabino' ); ?>
                        </a>
                        <p>
                            <?php esc_html_e( 'We\'ve neatly built most of the Sabino settings into the WordPress Customizer so you can see all your changes happen as you built your site.', 'sabino' ); ?>
                        </p>
                        <a href="<?php echo esc_url( admin_url( 'customize.php' ) ) ?>" class="sabino-admin-notice-btn-grey">
                            <?php esc_html_e( 'Start Customizing Your Website', 'sabino' ); ?>
                        </a>
                    </div>
                    <div class="sabino-admin-notice-block sabino-nomargin">
                        <h5><?php esc_html_e( 'Popular FAQ\'s:', 'sabino' ); ?></h5>
                        <p>
                        <?php esc_html_e( 'See our list of popular help links for building your website and/or any issues you may have.', 'sabino' ); ?>
                        </p>
                        <ul>
                            <li>
                                <a href="https://kairaweb.com/wordpress-theme/sabino/#premium-features" target="_blank"><?php esc_html_e( 'What does Sabino Premium offer', 'sabino' ); ?></a>
                            </li>
                            <li>
                                <a href="https://kairaweb.com/documentation/setting-up-the-default-slider/" target="_blank"><?php esc_html_e( 'Setup the Sabino default slider', 'sabino' ); ?></a>
                            </li>
                            <li>
                                <a href="https://kairaweb.com/documentation/adding-custom-css-to-wordpress/" target="_blank"><?php esc_html_e( 'Adding Custom CSS to WordPress', 'sabino' ); ?></a>
                            </li>
                            <li>
                                <a href="https://kairaweb.com/documentation/mobile-menu-not-working/" target="_blank"><?php esc_html_e( 'Mobile Menu is not working', 'sabino' ); ?></a>
                            </li>
                        </ul>
                        <a href="<?php echo esc_url( 'https://kairaweb.com/documentation/' ) ?>" class="sabino-admin-notice-btn-grey" target="_blank">
                            <?php esc_html_e( 'See More Documentation', 'sabino' ); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

			<a href="?sabino_add_license_notice_ignore=" class="sabino-notice-close"><?php esc_html_e( 'Dismiss Notice', 'sabino' ); ?></a>
		</div><?php
	endif;
}
add_action( 'admin_notices', 'sabino_add_license_notice' );
/**
 * Function to dismiss notice
 */
function sabino_add_license_notice_ignore() {
    global $current_user;
	$sabino_user_id = $current_user->ID;

    if ( isset( $_GET['sabino_add_license_notice_ignore'] ) ) {
		update_user_meta( $sabino_user_id, 'sabino_flash_notice_ignore', true );
    }
}
add_action( 'admin_init', 'sabino_add_license_notice_ignore' );

/**
 * Admin notice for Blockons
 */
function sabino_blockons_notice() {
	global $current_user;
	$sabino_user_id = $current_user->ID;

	if ( !get_user_meta( $sabino_user_id, 'sabino_blockons_dismiss' ) ) : ?>
		<div class="notice notice-info sabino-admin-notice sabino-notice-blockons">
			<div>
				<a href="<?php echo esc_url(admin_url('/plugin-install.php?s=blockons&tab=search&type=term')); ?>">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/images/blockons-logo.png'); ?>" alt="Blockons" />
				</a>
			</div>
			<div>
				<h4><?php esc_html_e( 'Try out the new Blockons Plugin !', 'sabino' ); ?></h4>
				<p><?php esc_html_e( 'Blockons offers advanced WordPress blocks for your Editor, as well as Site Addons such as page loader, page scroll indicator & back to top button... Great for building beautiful pages! More features coming soon!', 'sabino' ); ?></p>
				<a href="<?php echo esc_url(admin_url('/plugin-install.php?s=blockons&tab=search&type=term')); ?>"><?php esc_html_e( 'View the Blockons plugin', 'sabino' ); ?></a>
			</div>
			<a href="?sabino_blockons_notice_ignore=" class="sabino-notice-close"><?php esc_html_e( 'Dismiss Notice', 'sabino' ); ?></a>
		</div><?php
	endif;
}
add_action( 'admin_notices', 'sabino_blockons_notice' );
/**
 * Admin notice dismiss for Blockons
 */
function sabino_blockons_notice_ignore() {
    global $current_user;
	$sabino_user_id = $current_user->ID;

    if ( isset( $_GET['sabino_blockons_notice_ignore'] ) ) {
		update_user_meta( $sabino_user_id, 'sabino_blockons_dismiss', true );
    }
}
add_action( 'admin_init', 'sabino_blockons_notice_ignore' );