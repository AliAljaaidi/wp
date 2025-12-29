<?php
/**
 * WorkScout Theme Functions
 * Cleaned + Optimized Version (Option A)
 */



/* =========================================================
   GLOBAL CONSTANTS
========================================================= */
define('VERIFY_PAGE_URL', site_url('/verify-account'));
define('EMPLOYER_DASHBOARD_URL', site_url('/employer-dashboard'));
define('JOBSEEKER_DASHBOARD_URL', site_url('/jobseeker-dashboard'));
define('JOBSEEKER_HOME_URL', site_url('/jobseeker-homepage'));
define('EMPLOYER_HOME_URL', site_url('/employer-homepage'));



remove_action( 'workscout_header', 'workscout_render_header' );
remove_action( 'workscout_page_wrapper', 'workscout_page_wrapper_open' );



/* ----------------------------------------------------
   Licensing + Core Includes
------------------------------------------------------ */
update_option('WorkScout_lic_Key', 'activated');
remove_filter('the_title', 'add_breadcrumb_to_the_title');

include_once(get_template_directory() . '/kirki/kirki.php');

/* ----------------------------------------------------
   Kirki Configuration
------------------------------------------------------ */
function workscout_kirki_update_url($config) {
    $config['url_path'] = get_template_directory_uri() . '/kirki/';
    return $config;
}
add_filter('kirki/config', 'workscout_kirki_update_url');

/* ----------------------------------------------------
   Allow SVG Uploads
------------------------------------------------------ */
add_filter('upload_mimes', function($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

/* ----------------------------------------------------
   Theme Setup
------------------------------------------------------ */
function woocommerce_support() {
    add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'woocommerce_support');

function workscout_setup() {
    load_theme_textdomain('workscout', get_template_directory() . '/languages');

    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('resume-manager-templates');
    add_theme_support('job-manager-templates');
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(840, 430, true);

    add_image_size('workscout-small-thumb', 96, 105, true);
    add_image_size('workscout-small-blog', 498, 315, true);
    add_image_size('workscout-resume', 110, 110, true);

    register_nav_menus([
        'primary'   => esc_html__('Primary Menu', 'workscout'),
        'mobilemenu' => esc_html__('Mobile Menu', 'workscout'),
        'employer'  => esc_html__('Employer Dashboard Menu', 'workscout'),
        'candidate' => esc_html__('Candidate Dashboard Menu', 'workscout'),
    ]);

    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('post-formats', ['aside', 'image', 'video', 'quote', 'link']);

    add_theme_support('custom-background', [
        'default-color' => 'ffffff',
        'default-image' => '',
    ]);
}
add_action('after_setup_theme', 'workscout_setup');


/* ----------------------------------------------------
   Content Width
------------------------------------------------------ */
add_action('after_setup_theme', function() {
    $GLOBALS['content_width'] = apply_filters('workscout_content_width', 860);
}, 0);


/* ----------------------------------------------------
   Widgets
------------------------------------------------------ */
function workscout_widgets_init() {

    $widget_defaults = [
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ];

    register_sidebar([
        'name' => 'Sidebar',
        'id'   => 'sidebar-1'
    ] + $widget_defaults);

    register_sidebar(['name' => 'Jobs page sidebar', 'id' => 'sidebar-jobs'] + $widget_defaults);
    register_sidebar(['name' => 'Single job sidebar before', 'id' => 'sidebar-job-before'] + $widget_defaults);
    register_sidebar(['name' => 'Single job sidebar after', 'id' => 'sidebar-job-after'] + $widget_defaults);
    register_sidebar(['name' => 'Single task sidebar', 'id' => 'sidebar-task'] + $widget_defaults);
    register_sidebar(['name' => 'Single resume sidebar', 'id' => 'sidebar-resume'] + $widget_defaults);
    register_sidebar(['name' => 'Resumes page sidebar', 'id' => 'sidebar-resumes'] + $widget_defaults);
    register_sidebar(['name' => 'Shop page sidebar', 'id' => 'sidebar-shop'] + $widget_defaults);
    register_sidebar(['name' => 'Companies sidebar', 'id' => 'sidebar-companies'] + $widget_defaults);

    // Footer widgets
    for ($i = 1; $i <= 5; $i++) {
        register_sidebar([
            'id'   => "footer$i",
            'name' => "Footer Column $i"
        ] + $widget_defaults);
    }

    register_sidebar([
        'id'    => 'mobilemenu',
        'name'  => 'Mobile Menu Widget',
        'before_widget' => '<aside id="%1$s" class="mobile-menu-widget widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ]);
}
add_action('widgets_init', 'workscout_widgets_init');


/* ----------------------------------------------------
   Admin Scripts
------------------------------------------------------ */
function workscout_admin_scripts($hook) {
    $theme = wp_get_theme();
    $ver = $theme->get('Version');

    wp_enqueue_style('workscout-global-admin', get_template_directory_uri() . '/css/admin-global.css');

    if (in_array($hook, ['edit-tags.php','term.php','toplevel_page_workscout_settings'])) {
        wp_enqueue_style('workscout-admin', get_template_directory_uri() . '/css/admin.css');
        wp_enqueue_style('workscout-icons', get_template_directory_uri() . '/css/font-awesome.css');
        wp_enqueue_style('workscout-material-icons', get_template_directory_uri() . '/css/material-icons.css');
        wp_enqueue_style('workscout-all-icons', get_template_directory_uri() . '/css/icons.css');

        if (get_option('workscout_linear_icons_status') != 'hide') {
            wp_enqueue_style('workscout-line-icons', get_template_directory_uri() . '/css/line-awesome.css');
        }

        wp_enqueue_script('workscout-icon-selector', get_template_directory_uri() . '/js/iconselector.min.js', ['jquery'], $ver, true);
    }
}
add_action('admin_enqueue_scripts', 'workscout_admin_scripts');


/* ----------------------------------------------------
   Frontend Scripts & Styles
------------------------------------------------------ */
function workscout_scripts() {

    $theme = wp_get_theme();
    $ver = $theme->get('Version');

    wp_enqueue_style('workscout-base', get_template_directory_uri() . '/css/base.min.css', [], $ver);
    wp_enqueue_style('workscout-v2', get_template_directory_uri() . '/css/v2style.css', [], $ver);
    wp_enqueue_style('workscout-responsive', get_template_directory_uri() . '/css/responsive.min.css', [], $ver);
    wp_enqueue_style('workscout-font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css', [], $ver);

    if (get_option('workscout_linear_icons_status') != 'hide') {
        wp_enqueue_style('workscout-line-icons', get_template_directory_uri() . '/css/line-awesome.css');
    }

    wp_enqueue_style('workscout-all-icons', get_template_directory_uri() . '/css/icons.css');
    wp_enqueue_style('workscout-style', get_stylesheet_uri(), ['workscout-base','workscout-responsive','workscout-font-awesome'], $ver);
    wp_enqueue_style('workscout-woocommerce', get_template_directory_uri() . '/css/woocommerce.min.css', [], $ver);

    /* Remove unnecessary WPJM styles */
    wp_dequeue_style('wp-job-manager-frontend');
    wp_dequeue_style('wp-job-manager-job-listings');
    wp_dequeue_style('wp-job-manager-resume-frontend');

    /* Scripts */
    wp_enqueue_script('jquery-ui-autocomplete');
    wp_enqueue_script('slick-min', get_template_directory_uri() . '/js/slick.min.js', ['jquery'], $ver, true);
    wp_enqueue_script('workscout-custom', get_template_directory_uri() . '/js/custom.min.js', ['jquery'], time(), true);

    wp_localize_script('workscout-custom', 'ws', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'theme_url' => get_template_directory_uri(),
        'header_breakpoint' => Kirki::get_option('workscout','pp_alt_menu_width','1290'),
    ]);
}
add_action('wp_enqueue_scripts', 'workscout_scripts');


/* ----------------------------------------------------
   Remove Select2 from WPJM
------------------------------------------------------ */
add_action('wp_enqueue_scripts', function() {
    wp_dequeue_style('select2');
    wp_deregister_style('select2');
    wp_dequeue_style('wc-paid-listings-packages');
    wp_deregister_style('wc-paid-listings-packages');
}, PHP_INT_MAX);


/* ----------------------------------------------------
   Required Theme Includes
------------------------------------------------------ */
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/wp-job-manager.php';
require get_template_directory() . '/inc/wp-job-manager-maps.php';
require get_template_directory() . '/inc/ptshortcodes.php';
require get_template_directory() . '/inc/woocommerce.php';
require get_template_directory() . '/inc/tgmpa.php';
require get_template_directory() . '/inc/widgets.php';
require get_template_directory() . '/inc/wp-job-manager-colors-types.php';
require get_template_directory() . '/inc/cmb2-meta-boxes.php';
require get_template_directory() . '/inc/b372b0Base.php';
require get_template_directory() . '/inc/licenser.php';
require get_template_directory() . '/envato_setup/envato_setup.php';


/* ----------------------------------------------------
   Elementor Theme Builder Support
------------------------------------------------------ */
add_action('elementor/theme/register_locations', function($elementor_theme_manager) {
    $elementor_theme_manager->register_location('header');
    $elementor_theme_manager->register_location('footer');
});


/* ----------------------------------------------------
   Company Taxonomies
------------------------------------------------------ */
add_filter('mas_company_taxonomies_list', function($args) {
    $args['company_strength']['singular'] = __('Company Size', 'workscout');
    $args['company_strength']['plural'] = __('Company Size', 'workscout');
    $args['company_strength']['slug'] = __('company-size', 'workscout');
    return $args;
});



/* =========================================================
   ROLE-BASED LOGIN REDIRECTS
========================================================= */
add_filter('login_redirect', function ($redirect_to, $request, $user) {

    if (!$user || !isset($user->roles)) {
        return site_url('/login');
    }

    if (in_array('pending_user', $user->roles)) {
        return VERIFY_PAGE_URL;
    }

    if (in_array('job_seeker', $user->roles)) {
        return JOBSEEKER_HOME_URL;
    }

    if (in_array('employer', $user->roles)) {
        return EMPLOYER_HOME_URL;
    }

    return site_url('/');
}, 10, 3);



/* =========================================================
   FORCE PENDING USERS TO VERIFY PAGE
========================================================= */
add_action('template_redirect', function () {

    if (isset($_POST['register_with_verification'])) return;
    if (!is_user_logged_in()) return;

    if (current_user_can('pending_user') && !is_page('verify-account')) {
        wp_safe_redirect(VERIFY_PAGE_URL);
        exit;
    }
});




/* =========================================================
   START SESSION (OTP)
========================================================= */
add_action('init', function () {
    if (!session_id()) {
        session_start();
    }
});

/* =========================================================
   ENSURE ROLES EXIST
========================================================= */
add_action('init', function () {
    if (!get_role('job_seeker')) add_role('job_seeker', 'Job Seeker', ['read' => true]);
    if (!get_role('employer')) add_role('employer', 'Employer', ['read' => true]);
    if (!get_role('pending_user')) add_role('pending_user', 'Pending User', []);
});



/* =========================================================
   SEND OTP EMAIL 
========================================================= 
    wp_mail(
        $email,
        'Verify your account',
        "Your verification code is:\n\n{$otp}\n\nThis code expires in 10 minutes."
    );
}
 */

function acuc_get_job_salary_display($job_id) {

    $currency = get_post_meta($job_id, 'salary_currency', true);
    $amount   = get_post_meta($job_id, 'salary_amount', true);
    $period   = get_post_meta($job_id, 'salary_period', true);

    if ($currency && $amount && $period) {
        return "{$currency} {$amount} / {$period}";
    }

    // fallback for old jobs
    $legacy = get_post_meta($job_id, 'salary', true);
    return $legacy ?: '';
}



/* =========================================================
   JOB LISTING POST TYPE
========================================================= */
add_action('init', function () {
    register_post_type('job_listing', [
        'labels' => ['name' => 'Jobs'],
        'public' => true,
        'supports' => ['title', 'editor']
    ]);
});





/* =========================================================
   POST JOB (EMPLOYER ONLY)
========================================================= */
add_action('init', function () {

    if (!isset($_POST['post_job_form'])) return;
    if (!is_user_logged_in() || !acuc_user_has_role('employer')) return;


    $job_id = wp_insert_post([
        'post_type'    => 'job_listing',
        'post_title'   => sanitize_text_field($_POST['job_title']),
        'post_content' => wp_kses_post($_POST['job_description']),
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id()
    ]);

    update_post_meta($job_id, 'company_name', sanitize_text_field($_POST['company_name']));
	
    update_post_meta($job_id, 'job_location', sanitize_text_field($_POST['job_location']));
    update_post_meta($job_id, 'job_type', sanitize_text_field($_POST['job_type']));
	update_post_meta($job_id, 'employer_id', get_current_user_id());

$salary_amount   = sanitize_text_field($_POST['salary_amount'] ?? '');
$salary_currency = sanitize_text_field($_POST['salary_currency'] ?? '');
$salary_period   = sanitize_text_field($_POST['salary_period'] ?? '');

update_post_meta($job_id, 'salary_amount', $salary_amount);
update_post_meta($job_id, 'salary_currency', $salary_currency);
update_post_meta($job_id, 'salary_period', $salary_period);


/* Keep backward compatibility (VERY IMPORTANT) */
update_post_meta(
    $job_id,
    'salary',
    "{$salary_currency} {$salary_amount} / {$salary_period}"
);


    wp_safe_redirect(site_url('/employer-homepage?posted=1'));
exit;

});




/* =========================================================
   FRONT-END LOGIN
========================================================= */
add_action('init', function () {

    if (!isset($_POST['custom_login'])) return;

    $user = get_user_by('email', sanitize_text_field($_POST['login_email']))
         ?: get_user_by('login', sanitize_text_field($_POST['login_email']));

    if (!$user || !wp_check_password($_POST['login_password'], $user->user_pass, $user->ID)) {
        wp_safe_redirect('/login?login_error=Invalid email or password');
        exit;
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);







    wp_safe_redirect(apply_filters('login_redirect', '/', '', $user));
    exit;
});


/* =========================================================
   JOB LISTINGS SHORTCODE (FOR JOBSEEKER / EMPLOYER)
========================================================= */
add_shortcode('job_listings', function () {

$paged = max(1, get_query_var('paged'));

    ob_start();

    $args = [
        'post_type'      => 'job_listing',
        'post_status'    => 'publish',
        'posts_per_page' => 10,
		'paged'			 => $paged,
    ];

    $jobs = new WP_Query($args);

    if ($jobs->have_posts()) :
        while ($jobs->have_posts()) : $jobs->the_post();

            $company  = get_post_meta(get_the_ID(), 'company_name', true);
            $location = get_post_meta(get_the_ID(), 'job_location', true);
           $salary = acuc_get_job_salary_display(get_the_ID());


            $type     = get_post_meta(get_the_ID(), 'job_type', true);
            ?>
<?php
$employer_id = get_post_meta(get_the_ID(), 'employer_id', true);


?>



            <!-- JOB CARD (UNCHANGED DESIGN) -->
            <article 
  class="job-card"
  data-job-id="<?php the_ID(); ?>"
  data-title="<?php echo esc_attr(get_the_title()); ?>"
  data-company="<?php echo esc_attr($company); ?>"
  data-location="<?php echo esc_attr($location); ?>"
  data-salary="<?php echo esc_attr($salary); ?>"

  data-type="<?php echo esc_attr($type); ?>"
  data-updated="<?php echo esc_attr(get_the_modified_date()); ?>"
 data-description="<?php echo esc_attr(wp_strip_all_tags(get_the_content())); ?>"
>

 

                <?php if ($salary): ?>
                    <span class="job-badge">Hiring now</span>
                <?php endif; ?>

                <h3 class="job-title"><?php the_title(); ?></h3>

                <?php if ($company): ?>
                    <?php
$employer_id = get_post_meta(get_the_ID(), 'employer_id', true);
?>
<p class="job-company">
  <?php echo esc_html($company); ?>
</p>


                <?php endif; ?>

                <?php if ($location): ?>
                    <p class="job-location"><?php echo esc_html($location); ?></p>
                <?php endif; ?>

                <div class="job-meta">
<?php if ($salary !== ''): ?>
    <span class="job-pay"><?php echo esc_html($salary); ?></span>
<?php endif; ?>


                    <?php if ($type): ?>
                        <span class="job-type"><?php echo esc_html($type); ?></span>
                    <?php endif; ?>
                </div>

                <div class="job-footer">
                    <span class="easy-apply">Easily apply</span>
                    <button class="icon-btn">🔖</button>
                    <button class="icon-btn">🚫</button>
<form method="post" class="delete-job-form">
    <input type="hidden" name="action" value="delete_job">
    <input type="hidden" name="job_id" value="<?php echo get_the_ID(); ?>">
    <?php wp_nonce_field('delete_job_nonce', 'delete_job_nonce'); ?>

    <button type="submit" class="icon-btn danger" title="Delete job">
        🗑
    </button>
</form>

                </div>

            </article>

            <?php
        endwhile;
        wp_reset_postdata();
	$total_pages = $jobs->max_num_pages;

if ($total_pages > 1) : ?>
    <nav class="jobs-pagination">
        <?php
        echo paginate_links([
            'total'   => $total_pages,
            'current' => $paged,
            'prev_text' => '‹',
            'next_text' => '›',
        ]);
        ?>
    </nav>
<?php endif;

    else :
        echo '<p>No jobs found.</p>';
    endif;

    return ob_get_clean();
});




/* =========================================================
   AI JOB DESCRIPTION (AJAX)
========================================================= */
add_action('wp_ajax_generate_job_ai', function () {
    echo "AI-generated job description...";
    wp_die();
});



add_action('init', function () {

    if (!isset($_POST['register_with_verification'])) {
        return;
    }

    // Basic validation
    if (
        empty($_POST['email']) ||
        empty($_POST['password']) ||
        empty($_POST['full_name']) ||
        empty($_POST['user_type'])
    ) {
        return;
    }

    $email     = sanitize_email($_POST['email']);
    $password  = $_POST['password'];
    $full_name = sanitize_text_field($_POST['full_name']);
    $phone     = sanitize_text_field($_POST['full_phone'] ?? '');
    $user_type = sanitize_text_field($_POST['user_type']); // jobseeker | employer

    if (email_exists($email)) {
        wp_die('Email already registered.');
    }

    // Username
    $username = sanitize_user(strtolower(str_replace(' ', '_', $full_name)));

// PASSWORD STRENGTH CHECK
if (
    strlen($password) < 8 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[0-9]/', $password) ||
    !preg_match('/[\W_]/', $password)
) {
    wp_die('Password must contain at least 8 characters, including uppercase, lowercase, number, and symbol.');
}

// PHONE UNIQUE CHECK (GLOBAL)
if (acuc_phone_number_exists($phone)) {
    wp_die('This phone number is already registered.');
}



    // Create user
    $user_id = wp_create_user($username, $password, $email);


    if (is_wp_error($user_id)) {
        wp_die($user_id->get_error_message());
    }

		update_user_meta($user_id, 'user_type_pending', $user_type);
wp_update_user(['ID' => $user_id, 'role' => 'pending_user']);

update_user_meta($user_id, 'full_phone', $phone);

/* =========================================
   🔐 PENDING ACCOUNT TIMER (ADD HERE)
========================================= */
update_user_meta($user_id, 'pending_created_at', time());
update_user_meta($user_id, 'pending_expires_at', time() + (15 * 60)); // 15 minutes


// Generate OTP
$otp = random_int(100000, 999999);

// Store OTP (hashed)
update_user_meta($user_id, 'email_otp', password_hash($otp, PASSWORD_DEFAULT));

// OPTIONAL: store expiry (used in Issue #3)
update_user_meta($user_id, 'email_otp_expires', time() + (10 * 60)); // 10 minutes

// Email headers (IMPORTANT)
$headers = [
    'From: ACUC <no-reply@acuc.jobs>',
    'Content-Type: text/plain; charset=UTF-8'
];

// SEND OTP EMAIL (BEFORE LOGIN)
$sent = wp_mail(
    $email,
    'ACUC Verification Code',
    "Your verification code is:\n\n$otp\n\nThis code expires in 10 minutes.",
    $headers
);
	
	
	

// Log result (VERY IMPORTANT for debugging)
error_log('OTP mail sent: ' . ($sent ? 'YES' : 'NO'));


// Log result
error_log('OTP mail sent: ' . ($sent ? 'YES' : 'NO'));

// 🔐 NOW log user in (locked)
wp_set_current_user($user_id);
wp_set_auth_cookie($user_id);



    // Debug safety
    error_log('OTP sent: ' . ($sent ? 'YES' : 'NO'));

    // 🚀 Redirect to verification page
    wp_safe_redirect(site_url('/verify-account'));
    exit;
});

add_action('init', function () {

    if (!isset($_POST['verify_account'])) return;

    $user_id = get_current_user_id();
    if (!$user_id) wp_die('Not logged in.');

    $otp_input  = sanitize_text_field($_POST['otp_code']);
    $hashed_otp = get_user_meta($user_id, 'email_otp', true);
    $expires    = get_user_meta($user_id, 'email_otp_expires', true);
    $pending    = get_user_meta($user_id, 'user_type_pending', true);

    if (!$hashed_otp || !$expires || !$pending) {
        wp_die('Invalid verification attempt.');
    }

    if (time() > $expires) {
        wp_die('Verification code expired.');
    }

    if (!password_verify($otp_input, $hashed_otp)) {
        wp_die('Incorrect verification code.');
    }

    /* ======================
       SUCCESS — CLEANUP
    ====================== */
    delete_user_meta($user_id, 'email_otp');
    delete_user_meta($user_id, 'email_otp_expires');
    delete_user_meta($user_id, 'user_type_pending');

    /* ======================
       UNLOCK ACCOUNT
    ====================== */
	
	// Determine final role
$final_role = ($pending === 'employer') ? 'employer' : 'job_seeker';

// === EMPLOYER PROFILE BOOTSTRAP (on verification) ===
if ($final_role === 'employer') {

    // If your registration form has a company_name field, use it.
    // If not present, fallback to full_name.
    $company_name = sanitize_text_field($_POST['company_name'] ?? '');

    if ($company_name === '') {
        // fallback: the display name you used during registration
        $company_name = wp_get_current_user()->display_name ?: wp_get_current_user()->user_login;
    }

    // Store canonical employer company name
    update_user_meta($user_id, 'acuc_emp_company_name', $company_name);

    // Backward compatibility: your slug helper uses 'company_name'
    update_user_meta($user_id, 'company_name', $company_name);

    // Optional (recommended): make WP display name match company
    wp_update_user([
        'ID'           => $user_id,
        'display_name' => $company_name
    ]);
}


// Update user role
wp_update_user([
    'ID'   => $user_id,
    'role' => $final_role
]);


wp_safe_redirect(
    $final_role === 'employer'
        ? EMPLOYER_HOME_URL
        : JOBSEEKER_HOME_URL
);
exit;

});


add_action('template_redirect', function () {

    // Only target the main homepage
    if (!is_front_page()) {
        return;
    }

    // Only logged-in users
    if (!is_user_logged_in()) {
        return;
    }

    $user = wp_get_current_user();

    // Job Seeker → Jobseeker Homepage
    if (in_array('job_seeker', $user->roles)) {
        wp_safe_redirect(site_url('/jobseeker-homepage'));
        exit;
    }

    // Employer → Employer Homepage
    if (in_array('employer', $user->roles)) {
        wp_safe_redirect(site_url('/employer-homepage'));
        exit;
    }

});

add_action('template_redirect', function () {

    /* ==================================================
       BASIC CHECKS
    ================================================== */

    // Allow admins to do anything
    if (is_user_logged_in() && current_user_can('administrator')) {
        return;
    }

if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }
    $is_logged_in = is_user_logged_in();
    $user         = $is_logged_in ? wp_get_current_user() : null;

    /* ==================================================
       VISITORS (NOT LOGGED IN)
    ================================================== */

    if (!$is_logged_in) {

        // Protect role-based homepages
        if (
            is_page('jobseeker-homepage') ||
            is_page('employer-homepage') ||
            is_page('jobseeker-dashboard') ||
            is_page('employer-dashboard')
        ) {
            wp_safe_redirect(site_url('/login'));
            exit;
        }

        return;
    }

    /* ==================================================
       LOGGED-IN USERS
    ================================================== */

    $roles = $user->roles;

    // Allow verification page (VERY IMPORTANT)
    if (is_page('verify-account')) {
        return;
    }

    /* ==================================================
       FRONT PAGE REDIRECT
    ================================================== */

    if (is_front_page()) {

        if (in_array('job_seeker', $roles)) {
            wp_safe_redirect(site_url('/jobseeker-homepage'));
            exit;
        }

        if (in_array('employer', $roles)) {
            wp_safe_redirect(site_url('/employer-homepage'));
            exit;
        }
    }



    /* ==================================================
       JOB SEEKER PROTECTION
    ================================================== */

    if (
        is_page('jobseeker-homepage') ||
        is_page('jobseeker-dashboard')
    ) {
        if (!in_array('job_seeker', $roles)) {
            wp_safe_redirect(site_url('/'));
            exit;
        }
        return;
    }

    /* ==================================================
       EMPLOYER PROTECTION
    ================================================== */

    if (
        is_page('employer-homepage') ||
        is_page('employer-dashboard')
    ) {
        if (!in_array('employer', $roles)) {
            wp_safe_redirect(site_url('/'));
            exit;
        }
        return;
    }

    /* ==================================================
       FALLBACK AUTO-REDIRECT
       (prevents logged-in users seeing public homepage)
    ================================================== */

if (
    in_array('job_seeker', $roles) &&
    !(
        is_page([
            'jobseeker-homepage',
            'jobseeker-dashboard',
            'create-cv',
            'my-cv',
            'edit-cv',
            'application-form',
            'jobseeker-profile',
        ])
        || strpos($_SERVER['REQUEST_URI'], '/company/') === 0 // ✅ allow /company/{slug}
    )
) {
    wp_safe_redirect(site_url('/jobseeker-homepage'));
    exit;
}



});

  /* ==================================================
      homepage notification functions
    ================================================== */

add_action('wp_footer', function () {
    if (!is_user_logged_in()) return;

    $user = wp_get_current_user();
   $role = in_array('employer', $user->roles) ? 'employer' : 'job_seeker';


    echo "<script>document.body.dataset.userRole = '{$role}';</script>";
});


add_action('wp_footer', function () {
    if (!is_user_logged_in()) return;

    $user = wp_get_current_user();
    $role = in_array('employer', $user->roles) ? 'employer' : 'job_seeker';

    echo "<script>
      const el = document.getElementById('userName');
      if (el) {
        el.textContent = '" . esc_js($user->display_name) . "';
      }

      const userEl = document.getElementById('userEmail');
      if (userEl) {
        userEl.textContent = '" . esc_js($user->user_email) . "';
      }

      const roleEl = document.getElementById('userRole');
      if (roleEl) {
        roleEl.textContent = '" . esc_js($role) . "';
      }
    </script>";
});



add_action('after_setup_theme', function () {

    if (!current_user_can('manage_options')) {
        show_admin_bar(false);
    }

});



/* ==================================================
   EMPLOYER JOB POSTS SHORTCODE
================================================== */
add_shortcode('employer_job_posts', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('employer')) {
    return '<p>You do not have permission to view this.</p>';
}

	$company = get_post_meta(get_the_ID(), 'company_name', true);
    $current_user_id = get_current_user_id();

   $paged = max(1, get_query_var('paged'));

$jobs = new WP_Query([
    'post_type'      => 'job_listing',
    'post_status'    => 'publish',
    'author'         => $current_user_id,
    'posts_per_page' => 10,   // ✅ limit per page
    'paged'          => $paged
]);


    ob_start();
    ?>

    <section class="job-list">
        <h2 class="section-title">Your job posts</h2>

        <?php if ($jobs->have_posts()) : ?>
            <?php while ($jobs->have_posts()) : $jobs->the_post(); ?>

                <?php
                    $location = get_post_meta(get_the_ID(), 'job_location', true);
                   $salary = acuc_get_job_salary_display(get_the_ID());


                    $type     = get_post_meta(get_the_ID(), 'job_type', true);
                ?>

                <article
  class="job-card"
  data-job-id="<?php the_ID(); ?>"
  data-title="<?php echo esc_attr(get_the_title()); ?>"
  data-company="<?php echo esc_attr($company); ?>"
  data-location="<?php echo esc_attr($location); ?>"
  data-salary="<?php echo esc_attr($salary); ?>"

  data-type="<?php echo esc_attr($type); ?>"
  data-updated="<?php echo esc_attr(get_the_modified_date()); ?>"
data-description="<?php echo esc_attr(wp_strip_all_tags(get_the_content())); ?>"
>

                    <h3 class="job-title"><?php the_title(); ?></h3>

                    <?php if ($location): ?>
                        <p class="job-location"><?php echo esc_html($location); ?></p>
                    <?php endif; ?>

                    <div class="job-meta">
                       <?php if ($salary !== ''): ?>
    <span class="job-pay"><?php echo esc_html($salary); ?></span>
<?php endif; ?>

                     

                        <?php if ($type): ?>
                            <span class="job-type"><?php echo esc_html($type); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="job-footer">
                        <span class="applicant-count">
                            Applicants: <?php echo intval(get_post_meta(get_the_ID(), 'applicant_count', true)); ?>
                        </span>

                        <div class="job-actions">
                            <button class="icon-btn" title="Edit">✏️</button>
                            <button class="icon-btn" title="Hide">👁️</button>
                            <button 
  class="icon-btn delete-job-btn"
  data-job-id="<?php echo get_the_ID(); ?>"
  title="Delete job">
  🗑
</button>

                        </div>
                    </div>
                </article>

            <?php endwhile; wp_reset_postdata(); ?>

<?php
    $total_pages = $jobs->max_num_pages;

    if ($total_pages > 1) : ?>
        <nav class="jobs-pagination">
            <?php
            echo paginate_links([
                'total'     => $total_pages,
                'current'   => $paged,
                'prev_text' => '‹',
                'next_text' => '›',
            ]);
            ?>
        </nav>
<?php endif; ?>
        <?php else : ?>
            <p>No jobs posted yet.</p>
        <?php endif; ?>

    </section>

    <?php
    return ob_get_clean();
});




/* =========================================================
   DELETE JOB (EMPLOYER ONLY – AJAX)
========================================================= */
add_action('wp_ajax_delete_job_post', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('employer')) {
    wp_send_json_error('Unauthorized');
}


    // Nonce check
    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')
    ) {
        wp_send_json_error('Invalid security token');
    }

    $job_id = intval($_POST['job_id'] ?? 0);

    if (!$job_id) {
        wp_send_json_error('Invalid job ID');
    }

    $job = get_post($job_id);

    if (!$job || $job->post_type !== 'job_listing') {
        wp_send_json_error('Invalid job');
    }

    if ((int) $job->post_author !== get_current_user_id()) {
        wp_send_json_error('You do not own this job');
    }

    wp_delete_post($job_id, true);

    wp_send_json_success('Job deleted');
});

add_action('init', function () {

    if (!isset($_POST['resend_otp'])) return;
    if (!is_user_logged_in()) return;

    $user_id = get_current_user_id();
    $user    = wp_get_current_user();

    // Only pending users
    if (!in_array('pending_user', $user->roles)) {
        wp_die('Unauthorized.');
    }

    $last_sent = (int) get_user_meta($user_id, 'otp_last_sent', true);
    $count     = (int) get_user_meta($user_id, 'otp_resend_count', true);

    /* ======================
       LIMITS
    ====================== */

    // Cooldown: 60 seconds
    if (time() - $last_sent < 60) {
        wp_die('Please wait before requesting another code.');
    }

    // Max resends
    if ($count >= 5) {
        wp_die('Maximum resend attempts reached.');
    }

    /* ======================
       GENERATE NEW OTP
    ====================== */

    $otp = random_int(100000, 999999);

    update_user_meta($user_id, 'email_otp', password_hash($otp, PASSWORD_DEFAULT));
    update_user_meta($user_id, 'email_otp_expires', time() + 600); // reset expiry
    update_user_meta($user_id, 'otp_last_sent', time());
    update_user_meta($user_id, 'otp_resend_count', $count + 1);

    wp_mail(
        $user->user_email,
        'ACUC Verification Code (Resent)',
        "Your new verification code is:\n\n{$otp}\n\nThis code expires in 10 minutes."
    );

    wp_safe_redirect(site_url('/verify-account?resent=1'));
    exit;
});


add_action('wp_footer', function () {
    if (!is_user_logged_in()) return;

    echo '<script>
        window.ACUC = {
            ajaxUrl: "' . admin_url('admin-ajax.php') . '",
            nonce: "' . wp_create_nonce('acuc_ajax_nonce') . '"
        };
    </script>';
});



add_action('wp_ajax_ajax_register_user', 'acuc_ajax_register');
add_action('wp_ajax_nopriv_ajax_register_user', 'acuc_ajax_register');

function acuc_ajax_register() {

if (!acuc_validate_phone_number($phone)) {
    wp_send_json_error([
        'message' => 'Invalid phone number format.'
    ]);
}

    if (
        empty($_POST['email']) ||
        empty($_POST['password']) ||
        empty($_POST['full_name']) ||
        empty($_POST['user_type'])
    ) {
        wp_send_json_error(['message' => 'All fields are required.']);
    }

    $email     = sanitize_email($_POST['email']);
    $password  = $_POST['password'];
    $full_name = sanitize_text_field($_POST['full_name']);
    $phone     = sanitize_text_field($_POST['full_phone'] ?? '');
    $user_type = sanitize_text_field($_POST['user_type']);

    /* EMAIL UNIQUE */
    if (email_exists($email)) {
        wp_send_json_error(['message' => 'This email is already registered.']);
    }



if (!acuc_validate_phone_number($phone)) {
    wp_die('Invalid phone number format.');
}

    /* PHONE UNIQUE */
 if (acuc_phone_number_exists($phone)) {
    wp_send_json_error([
        'message' => 'This phone number is already registered.'
    ]);
}


    /* PASSWORD STRENGTH */
    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W_]/', $password)
    ) {
        wp_send_json_error([
            'message' => 'Password must contain uppercase, lowercase, number, symbol and be at least 8 characters.'
        ]);
    }

    /* USERNAME */
    $username = sanitize_user(strtolower(str_replace(' ', '_', $full_name)));

    if (username_exists($username)) {
        $username .= rand(100, 999);
    }

    /* CREATE USER */
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => $user_id->get_error_message()]);
    }

    update_user_meta($user_id, 'full_phone', $phone);
    update_user_meta($user_id, 'user_type_pending', $user_type);

    wp_update_user([
        'ID'   => $user_id,
        'role' => 'pending_user'
    ]);

    /* OTP */
    $otp = random_int(100000, 999999);
    update_user_meta($user_id, 'email_otp', password_hash($otp, PASSWORD_DEFAULT));
    update_user_meta($user_id, 'email_otp_expires', time() + 600);

update_user_meta($user_id, 'otp_last_sent', time());
update_user_meta($user_id, 'otp_resend_count', 0);


    wp_mail(
        $email,
        'ACUC Verification Code',
        "Your verification code is:\n\n{$otp}\n\nThis code expires in 10 minutes."
    );

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    wp_send_json_success([
        'redirect' => site_url('/verify-account')
    ]);
}





add_action('init', function () {

    if (!is_user_logged_in()) return;

    $user = wp_get_current_user();

    if (!in_array('pending_user', $user->roles)) return;

    $expires = get_user_meta($user->ID, 'pending_expires_at', true);

    if (!$expires) return;

    if (time() > (int) $expires) {

        // Logout user
        wp_logout();

        // Delete user completely
        require_once ABSPATH . 'wp-admin/includes/user.php';
        wp_delete_user($user->ID);

        // Redirect to registration with message
        wp_safe_redirect(site_url('/register?expired=1'));
        exit;
    }
});


add_action('init', function () {
    if (isset($_GET['reason']) && $_GET['reason'] === 'wrong-email') {
        wp_logout();
        wp_safe_redirect(site_url('/register'));
        exit;
    }
});

function acuc_phone_number_exists($phone) {

    if (empty($phone)) {
        return false;
    }

    $users = get_users([
        'meta_key'   => 'full_phone',
        'meta_value' => $phone,
        'number'     => 1,
        'fields'     => 'ID'
    ]);

    return !empty($users);
}

function acuc_validate_phone_number($phone) {

    if (empty($phone)) {
        return false;
    }

    // Allow optional leading +
    if (!preg_match('/^\+?[0-9]+$/', $phone)) {
        return false;
    }

    $digits = preg_replace('/\D/', '', $phone);

    // Enforce 10–20 digits
    if (strlen($digits) < 10 || strlen($digits) > 20) {
        return false;
    }

    return true;
}



/* =========================================================
   PUBLISH CV (JOBSEEKER ONLY)
========================================================= */
add_action('wp_ajax_acuc_publish_cv', function () {

    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in.');
    }

    if (!acuc_user_has_role('job_seeker')) {
        wp_send_json_error('Unauthorized.');
    }

    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')

    ) {
        wp_send_json_error('Invalid security token.');
    }

    $cv_raw = json_decode(stripslashes($_POST['cv'] ?? ''), true);

    if (!$cv_raw || empty($cv_raw['personal']['name'])) {
        wp_send_json_error('Invalid CV data.');
    }

    $user_id = get_current_user_id();
    $cvs = get_user_meta($user_id, 'acuc_cvs', true);

    if (!is_array($cvs)) {
        $cvs = [];
    }

    /* LIMIT: MAX 2 CVS */
    if (count($cvs) >= 2) {
        wp_send_json_error('You can only create up to 2 CVs.');
    }

    $cvs[] = [
        'id' => uniqid('cv_'),
        'title' => sanitize_text_field($cv_raw['cv_title']['personal']['role'] ?: 'My CV'),
	    'content'   => wp_kses_post($_POST['cv_content']),
        'thumbnail' => esc_url_raw($_POST['cv_thumbnail']), // 👈 ADD THIS
        'data' => $cv_raw,
        'created_at' => time()
    ];

    update_user_meta($user_id, 'acuc_cvs', $cvs);

    wp_send_json_success('CV published.');
});






/* =========================================================
   APPLY-FROM-COMPANY: GET MY CVS (JOBSEEKER ONLY)
========================================================= */
add_action('wp_ajax_acuc_get_my_cvs_for_apply', function () {

    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in.');
    }

    if (!function_exists('acuc_user_has_role') || !acuc_user_has_role('job_seeker')) {
        wp_send_json_error('Unauthorized.');
    }

    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')
    ) {
        wp_send_json_error('Invalid security token.');
    }

    $cvs = get_user_meta(get_current_user_id(), 'acuc_cvs', true);
    if (!is_array($cvs)) $cvs = [];

    // return minimal safe info
    $out = [];
    foreach ($cvs as $cv) {
        $out[] = [
            'id'        => sanitize_text_field($cv['id'] ?? ''),
            'title'     => sanitize_text_field($cv['title'] ?? 'My CV'),
            'thumbnail' => esc_url_raw($cv['thumbnail'] ?? ''),
            'created'   => (int) ($cv['created_at'] ?? 0),
        ];
    }

    wp_send_json_success($out);
});

/**
 * Core application logic (SOURCE OF TRUTH)
 */
function acuc_apply_to_job_core($job_id, $cv_id, $user_id, $cv_file_id = null) {

    /* -------------------------
       Validate job
    ------------------------- */
    if (get_post_type($job_id) !== 'job_listing') {
        return new WP_Error('invalid_job', 'Invalid job.');
    }

    /* -------------------------
       Verify CV ownership
    ------------------------- */
    $cvs = get_user_meta($user_id, 'acuc_cvs', true);
    if (!is_array($cvs)) {
        $cvs = [];
    }

/* -------------------------
   Validate CV
------------------------- */

// Uploaded-on-apply CV → skip lookup
if ($cv_id === 'uploaded_pdf') {

    if (empty($cv_file_id)) {
        return new WP_Error('cv_not_found', 'Uploaded CV missing.');
    }

} else {

    // Stored CVs validation
    $cvs = get_user_meta($user_id, 'acuc_cvs', true);
    if (!is_array($cvs)) {
        return new WP_Error('cv_not_found', 'CV not found.');
    }

    $cv_found = false;
    foreach ($cvs as $cv) {
        if (!empty($cv['id']) && $cv['id'] === $cv_id) {
            $cv_found = true;
            break;
        }
    }

    if (!$cv_found) {
        return new WP_Error('cv_not_found', 'CV not found.');
    }
}


    /* -------------------------
       Load + normalize applications (ONCE)
    ------------------------- */
    $apps = get_post_meta($job_id, 'acuc_applications', true);
    if (!is_array($apps)) {
        $apps = [];
    }

    // Normalize legacy / corrupted entries
    $apps = array_values(array_filter($apps, function ($a) {
        return is_array($a)
            && isset($a['user_id'], $a['cv_id'], $a['applied_at']);
    }));

    /* -------------------------
       Prevent duplicate application
    ------------------------- */
    foreach ($apps as $app) {
        if ((int) $app['user_id'] === $user_id) {
            return new WP_Error('already_applied', 'You already applied to this job.');
        }
    }

    /* -------------------------
       Append canonical application
    ------------------------- */
    $apps[] = [
        'user_id'    => $user_id,
        'cv_id'      => $cv_id,
		'cv_file_id' => $cv_file_id ? (int) $cv_file_id : 0,
        'applied_at' => time(),
        'status'     => 'pending',
    ];

    update_post_meta($job_id, 'acuc_applications', $apps);
    update_post_meta($job_id, 'applicant_count', count($apps));

    return true;
}



/* =========================================================
   APPLY-FROM-COMPANY: APPLY TO JOB USING A CV (JOBSEEKER ONLY)
   Stores inside job meta: acuc_applications[]
========================================================= */
add_action('wp_ajax_acuc_apply_to_job_with_cv', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('job_seeker')) {
        wp_send_json_error('Unauthorized.');
    }

    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')
    ) {
        wp_send_json_error('Invalid security token.');
    }

    $job_id = (int) ($_POST['job_id'] ?? 0);
    $cv_id  = sanitize_text_field($_POST['cv_id'] ?? '');

    if (!$job_id || !$cv_id) {
        wp_send_json_error('Missing job or CV.');
    }

    $result = acuc_apply_to_job_core(
        $job_id,
        $cv_id,
        get_current_user_id()
    );

    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }

    wp_send_json_success('Applied.');
});




/* =========================================================
   MY CVS SHORTCODE
========================================================= */
add_shortcode('my_cvs', function () {

   if (!is_user_logged_in() || !acuc_user_has_role('job_seeker')) {
    return '<p>Unauthorized.</p>';
}


    $cvs = get_user_meta(get_current_user_id(), 'acuc_cvs', true);

    if (empty($cvs)) {
        return '<p>No CVs created yet.</p>';
    }

    ob_start(); ?>

    <div class="job-list">

        <?php foreach ($cvs as $cv): ?>



            <article class="job-card">

                <h3 class="job-title">
                    <?php echo esc_html($cv['title']); ?>
                </h3>

                <p class="job-company">
                    Created on <?php echo date('d M Y', $cv['created_at']); ?>
                </p>

                <div class="job-footer">

                    <button 
    class="easy-apply view-cv-btn"
    data-cv-id="<?php echo esc_attr($cv['id']); ?>">
    View
</button>


                    <a href="/edit-cv?cv=<?php echo esc_attr($cv['id']); ?>" class="easy-apply">
                        Edit
                    </a>

                    <button 
                        class="icon-btn delete-cv-btn"
                        data-cv-id="<?php echo esc_attr($cv['id']); ?>">
                        🗑
                    </button>

                </div>

            </article>

        <?php endforeach; ?>

    </div>

    <?php return ob_get_clean();
});


add_action('wp_ajax_acuc_delete_cv', function () {

   if (!is_user_logged_in() || !acuc_user_has_role('job_seeker')) {
  wp_send_json_error('Unauthorized');
}


    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')

    ) {
        wp_send_json_error('Invalid security token.');
    }

    $cv_id = sanitize_text_field($_POST['cv_id'] ?? '');
    $user_id = get_current_user_id();

    $cvs = get_user_meta($user_id, 'acuc_cvs', true);

    if (!is_array($cvs)) {
        wp_send_json_error('No CVs found.');
    }

    $cvs = array_values(array_filter($cvs, fn($cv) => $cv['id'] !== $cv_id));

    update_user_meta($user_id, 'acuc_cvs', $cvs);

    wp_send_json_success('CV deleted.');
});


add_action('wp_ajax_acuc_apply_with_uploaded_cv', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('job_seeker')) {
        wp_send_json_error('Unauthorized');
    }

    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')
    ) {
        wp_send_json_error('Invalid nonce');
    }

    $job_id = intval($_POST['job_id'] ?? 0);

    if (!$job_id || empty($_FILES['cv_pdf'])) {
        wp_send_json_error('Missing data');
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    // Upload PDF
    $attachment_id = media_handle_upload('cv_pdf', 0);

    if (is_wp_error($attachment_id)) {
        wp_send_json_error($attachment_id->get_error_message());
    }

    // 🔑 IMPORTANT: use SAME STRUCTURE as core
    $apps = get_post_meta($job_id, 'acuc_applications', true);
    if (!is_array($apps)) $apps = [];

    // Prevent duplicate application
    foreach ($apps as $app) {
        if ((int)$app['user_id'] === get_current_user_id()) {
            wp_send_json_error('You already applied to this job.');
        }
    }

    $apps[] = [
        'user_id'     => get_current_user_id(),
        'cv_id'       => 'uploaded_pdf',
        'cv_file_id'  => (int) $attachment_id,
        'applied_at'  => time(),
        'status'      => 'pending',
    ];

    update_post_meta($job_id, 'acuc_applications', $apps);
    update_post_meta($job_id, 'applicant_count', count($apps));

    wp_send_json_success('Application submitted');
});




/* =========================================================
   FETCH CV (MODAL VIEW)
========================================================= */
add_action('wp_ajax_acuc_get_cv', function () {

    if (!is_user_logged_in()) {
        wp_send_json_error('Unauthorized');
    }

    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')

    ) {
        wp_send_json_error('Invalid nonce');
    }

    $cv_id = sanitize_text_field($_POST['cv_id'] ?? '');
    if (!$cv_id) {
        wp_send_json_error('Invalid CV');
    }

    $current_user = wp_get_current_user();

    /**
     * CASE 1: Jobseeker viewing their own CV
     */
    if (acuc_user_has_role('job_seeker')) {

        $cvs = get_user_meta($current_user->ID, 'acuc_cvs', true);
        if (!is_array($cvs)) {
            wp_send_json_error('No CVs');
        }

        foreach ($cvs as $cv) {
            if ($cv['id'] === $cv_id) {
                wp_send_json_success(render_acuc_cv_html($cv));
            }
        }

        wp_send_json_error('CV not found');
    }

    /**
     * CASE 2: Employer viewing applicant CV
     */
    if (current_user_can('employer')) {

        // Find which job this CV was applied to
        $jobs = get_posts([
            'post_type'   => 'job_listing',
            'post_status' => 'publish',
            'author'      => $current_user->ID,
            'numberposts' => -1
        ]);

        foreach ($jobs as $job) {

            $applications = get_post_meta($job->ID, 'acuc_applications', true);
            if (!is_array($applications)) continue;

            foreach ($applications as $app) {

                if ($app['cv_id'] === $cv_id) {

                    $cvs = get_user_meta($app['user_id'], 'acuc_cvs', true);
                    if (!is_array($cvs)) break;

                    foreach ($cvs as $cv) {
                        if ($cv['id'] === $cv_id) {
                            wp_send_json_success(render_acuc_cv_html($cv));
                        }
                    }
                }
            }
        }

        wp_send_json_error('Access denied');
    }

    wp_send_json_error('Invalid role');
});


add_action('wp_ajax_acuc_search_jobs', 'acuc_search_jobs');
add_action('wp_ajax_nopriv_acuc_search_jobs', 'acuc_search_jobs');



function acuc_search_jobs() {

    $keyword  = sanitize_text_field($_POST['keyword'] ?? '');
    $location = sanitize_text_field($_POST['location'] ?? '');

    // ----------------------------
    // 1) Base filters (LOCATION stays separate, unchanged)
    // ----------------------------
    $base_meta = [
        'relation' => 'AND',
    ];

    if ($location !== '') {
        $base_meta[] = [
            'key'     => 'job_location',
            'value'   => $location,
            'compare' => 'LIKE',
        ];
    }

    // ----------------------------
    // 2) Build "meta OR" keyword search (company / salary / job_type)
    //    + normalize full time / full-time / fulltime
    // ----------------------------
    $keyword = trim($keyword);

    $kw_lower      = strtolower($keyword);
    $kw_dash       = str_replace(' ', '-', $kw_lower);          // "full time" -> "full-time"
    $kw_space      = str_replace('-', ' ', $kw_lower);          // "full-time" -> "full time"
    $kw_compact    = str_replace([' ', '-'], '', $kw_lower);    // "full time" -> "fulltime"

    $meta_keyword_or = [
        'relation' => 'OR',
        [
            'key'     => 'company_name',
            'value'   => $keyword,
            'compare' => 'LIKE',
        ],
        [
            'key'     => 'salary',
            'value'   => $keyword,
            'compare' => 'LIKE',
        ],
        // job type variations
        [
            'key'     => 'job_type',
            'value'   => $keyword,
            'compare' => 'LIKE',
        ],
        [
            'key'     => 'job_type',
            'value'   => $kw_dash,
            'compare' => 'LIKE',
        ],
        [
            'key'     => 'job_type',
            'value'   => $kw_space,
            'compare' => 'LIKE',
        ],
        [
            'key'     => 'job_type',
            'value'   => $kw_compact,
            'compare' => 'LIKE',
        ],
    ];

    // ----------------------------
    // 3) Do UNION search:
    //    A) title/content search (WP's 's')
    //    B) meta search (company/salary/type)
    //    Merge IDs, then fetch final list.
    // ----------------------------
    $ids = [];

    // A) title + content
    if ($keyword !== '') {
        $q1 = new WP_Query([
            'post_type'      => 'job_listing',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'fields'         => 'ids',
            's'              => $keyword,
            'meta_query'     => $base_meta,
        ]);
        if (!empty($q1->posts)) {
            $ids = array_merge($ids, $q1->posts);
        }
    }

    // B) meta fields (company/salary/type)
    if ($keyword !== '') {
        $meta_args = $base_meta;
        $meta_args[] = $meta_keyword_or;

        $q2 = new WP_Query([
            'post_type'      => 'job_listing',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'fields'         => 'ids',
            'meta_query'     => $meta_args,
        ]);
        if (!empty($q2->posts)) {
            $ids = array_merge($ids, $q2->posts);
        }
    }

    // If keyword is empty => normal listing (only location filter)
    if ($keyword === '') {
        $jobs = new WP_Query([
            'post_type'      => 'job_listing',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'meta_query'     => $base_meta,
        ]);
    } else {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        // nothing matched
        if (empty($ids)) {
            echo '<p>No jobs found.</p>';
            wp_die();
        }

        // final fetch by matched IDs (keeps it ONLY job_listing)
        $jobs = new WP_Query([
            'post_type'      => 'job_listing',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'post__in'       => $ids,
            'orderby'        => 'post__in',
        ]);
    }

    // ----------------------------
    // 4) Output (UNCHANGED from your existing loop)
    //    Keeps: "hide job if user already applied"
    // ----------------------------
    if ($jobs->have_posts()) :
        while ($jobs->have_posts()) : $jobs->the_post();

            /* =========================================
               HIDE JOB IF USER ALREADY APPLIED
            ========================================= */
            $apps = get_post_meta(get_the_ID(), 'acuc_applications', true);
            $applied = false;

            if (is_user_logged_in() && is_array($apps)) {
                foreach ($apps as $app) {
                    if ((int) $app['user_id'] === get_current_user_id()) {
                        $applied = true;
                        break;
                    }
                }
            }

            if ($applied) continue;
            /* ========================================= */

$salary_display = acuc_get_job_salary_display(get_the_ID());




            $company  = get_post_meta(get_the_ID(), 'company_name', true);
            $location = get_post_meta(get_the_ID(), 'job_location', true);
           // backward compatibility only
$salary_legacy = get_post_meta(get_the_ID(), 'salary', true);

            $type     = get_post_meta(get_the_ID(), 'job_type', true);
            ?>
<?php
$employer_id = get_post_meta(get_the_ID(), 'employer_id', true);

?>


            <article
              class="job-card"
              data-job-id="<?php the_ID(); ?>"
              data-title="<?php echo esc_attr(get_the_title()); ?>"
              data-company="<?php echo esc_attr($company); ?>"
              data-location="<?php echo esc_attr($location); ?>"
              data-salary="<?php echo esc_attr($salary_display); ?>"


              data-type="<?php echo esc_attr($type); ?>"
              data-description="<?php echo esc_attr(wp_strip_all_tags(get_the_content())); ?>"
            >

              <?php if ($salary_display): ?>
                <span class="job-badge">Hiring now</span>
              <?php endif; ?>

              <h3 class="job-title"><?php the_title(); ?></h3>

              <?php if ($company): ?>
                <p class="job-company"><?php echo esc_html($company); ?></p>
              <?php endif; ?>

              <?php if ($location): ?>
                <p class="job-location"><?php echo esc_html($location); ?></p>
              <?php endif; ?>

              <div class="job-meta">
                <?php if ($salary_display): ?>
                  <span class="job-pay"><?php echo esc_html($salary_display); ?></span>
                <?php endif; ?>
                <?php if ($type): ?>
                  <span class="job-type"><?php echo esc_html($type); ?></span>
                <?php endif; ?>
              </div>

              <div class="job-footer">
                <span class="easy-apply">Easily apply</span>
              </div>

            </article>

            <?php
        endwhile;
        wp_reset_postdata();
    else :
        echo '<p>No jobs found.</p>';
    endif;

    wp_die();
}




/* =========================================================
   UPLOAD CV THUMBNAIL
========================================================= */
add_action('wp_ajax_acuc_upload_cv_thumbnail', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('job_seeker')) {
        wp_send_json_error('Unauthorized');
    }

    if (!isset($_FILES['thumbnail'])) {
        wp_send_json_error('No file');
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $upload = media_handle_upload('thumbnail', 0);

    if (is_wp_error($upload)) {
        wp_send_json_error($upload->get_error_message());
    }

    $url = wp_get_attachment_url($upload);
    wp_send_json_success($url);
});

add_action('wp_ajax_acuc_apply_to_job', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('job_seeker')) {
        wp_send_json_error('Unauthorized');
    }

    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')
    ) {
        wp_send_json_error('Invalid nonce');
    }

    $job_id = (int) ($_POST['job_id'] ?? 0);
    $cv_id  = sanitize_text_field($_POST['cv_id'] ?? '');

    if (!$job_id || !$cv_id) {
        wp_send_json_error('Missing data');
    }

    // 🔁 Delegate to source-of-truth logic
    $result = acuc_apply_to_job_core(
        $job_id,
        $cv_id,
        get_current_user_id()
    );

    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }

    wp_send_json_success('Applied successfully');
});


add_action('wp_ajax_acuc_get_job_applicants', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('employer')) {
        wp_send_json_error('Unauthorized');
    }

    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')
    ) {
        wp_send_json_error('Invalid nonce');
    }

    $job_id = intval($_POST['job_id'] ?? 0);
    if (!$job_id) {
        wp_send_json_error('Invalid job');
    }

    $job = get_post($job_id);
    if (!$job || (int) $job->post_author !== get_current_user_id()) {
        wp_send_json_error('Not your job');
    }

    $applications = get_post_meta($job_id, 'acuc_applications', true);
    if (!is_array($applications) || empty($applications)) {
        wp_send_json_success([]);
    }

    $results = [];

    foreach ($applications as $app) {

        $user_id = (int) ($app['user_id'] ?? 0);
        if (!$user_id) continue;

        $user = get_user_by('id', $user_id);
        if (!$user) continue;

        $applied_ts = (int) ($app['applied_at'] ?? 0);
        $status     = sanitize_text_field($app['status'] ?? 'pending');
        $cv_id      = sanitize_text_field($app['cv_id'] ?? '');
        $cv_file_id = (int) ($app['cv_file_id'] ?? 0);

        // =========================
        // CASE 1: Uploaded PDF CV
        // =========================
        if ($cv_id === 'uploaded_pdf' && $cv_file_id) {

            $results[] = [
                'user_id'     => (int) $user->ID,
                'name'        => $user->display_name,
                'email'       => $user->user_email,
                'avatar'      => get_avatar_url($user->ID, ['size' => 48]),

                'cv_title'    => 'Uploaded CV (PDF)',
                'cv_id'       => 'uploaded_pdf',
                // Secure download through your AJAX endpoint (validated by job + user below)
                'cv_download' => admin_url(
                    'admin-ajax.php?action=acuc_download_cv'
                    . '&cv_id=uploaded_pdf'
                    . '&job_id=' . $job_id
                    . '&user_id=' . (int) $user->ID
                ),

                'status'      => $status,
                'applied_at'  => $applied_ts ? date('d M Y', $applied_ts) : '',
                'applied_ts'  => $applied_ts,
            ];

            continue;
        }

        // =========================
        // CASE 2: Saved CV (acuc_cvs)
        // =========================
        $cvs = get_user_meta($user->ID, 'acuc_cvs', true);
        if (!is_array($cvs)) continue;

        foreach ($cvs as $cv) {
            if (!is_array($cv)) continue;

            if (isset($cv['id']) && $cv['id'] === $cv_id) {

                $results[] = [
                    'user_id'     => (int) $user->ID,
                    'name'        => $user->display_name,
                    'email'       => $user->user_email,
                    'avatar'      => get_avatar_url($user->ID, ['size' => 48]),

                    'cv_title'    => sanitize_text_field($cv['title'] ?? 'CV'),
                    'cv_id'       => sanitize_text_field($cv['id']),
                    'cv_download' => admin_url(
                        'admin-ajax.php?action=acuc_download_cv'
                        . '&cv_id=' . urlencode($cv['id'])
                        . '&job_id=' . $job_id
                        . '&user_id=' . (int) $user->ID
                    ),

                    'status'      => $status,
                    'applied_at'  => $applied_ts ? date('d M Y', $applied_ts) : '',
                    'applied_ts'  => $applied_ts,
                ];

                break;
            }
        }
    }

    wp_send_json_success($results);
});





add_action('wp_ajax_acuc_download_cv', function () {

    if (!is_user_logged_in()) wp_die();

    $cv_id   = sanitize_text_field($_GET['cv_id'] ?? '');
    $job_id  = intval($_GET['job_id'] ?? 0);
    $user_id = intval($_GET['user_id'] ?? 0);

    if (!$cv_id || !$job_id || !$user_id) wp_die();

    $current_user = wp_get_current_user();

    // Only employer downloads here (your UI uses employer side)
    if (!acuc_user_has_role('employer')) wp_die();

    $job = get_post($job_id);
    if (!$job || $job->post_type !== 'job_listing') wp_die();

    // Must own the job
    if ((int) $job->post_author !== (int) $current_user->ID) wp_die();

    $apps = get_post_meta($job_id, 'acuc_applications', true);
    if (!is_array($apps)) wp_die();

    foreach ($apps as $app) {

        if ((int) ($app['user_id'] ?? 0) !== $user_id) continue;

        // Uploaded PDF download (only if requested)
        if ($cv_id === 'uploaded_pdf') {
            if (($app['cv_id'] ?? '') === 'uploaded_pdf' && !empty($app['cv_file_id'])) {
                $url = wp_get_attachment_url((int) $app['cv_file_id']);
                if ($url) {
                    wp_safe_redirect($url);
                    exit;
                }
            }
            wp_die();
        }

        // Saved CV download (only if requested id matches)
        if (($app['cv_id'] ?? '') === $cv_id) {

            $cvs = get_user_meta($user_id, 'acuc_cvs', true);
            if (!is_array($cvs)) wp_die();

            foreach ($cvs as $cv) {
                if (is_array($cv) && ($cv['id'] ?? '') === $cv_id) {

                    // If you store a PDF file in your CV structure, redirect it here.
                    // If you only store HTML CVs, you may want to render HTML instead.
                    // For now, keep your existing behavior or implement as needed.
                    wp_die('This CV type does not have a PDF file attached.');
                }
            }

            wp_die();
        }
    }

    wp_die();
});




function render_acuc_cv_html($cv) {

    if (!isset($cv['data']) || !is_array($cv['data'])) {
        return '<p>Invalid CV data.</p>';
    }

    $data = $cv['data'];

    ob_start(); ?>

    <div class="cv-view">

        <h2><?php echo esc_html($data['personal']['name'] ?? ''); ?></h2>
        <h3><?php echo esc_html($data['personal']['role'] ?? ''); ?></h3>

        <p class="muted">
            <?php echo esc_html($data['personal']['address'] ?? ''); ?>
            <?php if (!empty($data['personal']['contact'])): ?>
                | <?php echo esc_html($data['personal']['contact']); ?>
            <?php endif; ?>
        </p>

        <hr>

        <?php if (!empty($data['personal']['summary'])): ?>
            <h4>Summary</h4>
            <p><?php echo nl2br(esc_html($data['personal']['summary'])); ?></p>
        <?php endif; ?>

        <?php if (!empty($data['experience']) && is_array($data['experience'])): ?>
            <h4>Experience</h4>
            <?php foreach ($data['experience'] as $exp): ?>
                <p>
                    <strong><?php echo esc_html($exp['company'] ?? ''); ?></strong>
                    <?php if (!empty($exp['location'])): ?>
                        — <?php echo esc_html($exp['location']); ?>
                    <?php endif; ?>
                    <?php if (!empty($exp['dates'])): ?>
                        (<?php echo esc_html($exp['dates']); ?>)
                    <?php endif; ?>
                </p>
                <?php if (!empty($exp['description'])): ?>
                    <p class="muted"><?php echo esc_html($exp['description']); ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($data['education']) && is_array($data['education'])): ?>
            <h4>Education</h4>
            <?php foreach ($data['education'] as $edu): ?>
                <p>
                    <?php echo esc_html($edu['institution'] ?? ''); ?>
                    <?php if (!empty($edu['certificate'])): ?>
                        — <?php echo esc_html($edu['certificate']); ?>
                    <?php endif; ?>
                    <?php if (!empty($edu['year'])): ?>
                        (<?php echo esc_html($edu['year']); ?>)
                    <?php endif; ?>
                </p>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($data['skills']) && is_array($data['skills'])): ?>
            <h4>Skills</h4>
            <ul>
                <?php foreach ($data['skills'] as $skill): ?>
                    <li><?php echo esc_html($skill); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    </div>

    <?php
    return ob_get_clean();
}

function acuc_user_has_role($role) {
    if (!is_user_logged_in()) return false;
    $user = wp_get_current_user();
    return in_array($role, (array) $user->roles, true);
} 

/* =========================================================
   PROFILE HELPERS (Job Seeker profiles)
   These are INTERNAL helper functions.
   They do NOT output anything on their own.
========================================================= */

function acuc_profile_can_view_jobseeker($viewer_user, $profile_user) {
    if (!$viewer_user || !$profile_user) return false;

    $viewer_roles  = (array) $viewer_user->roles;
    $profile_roles = (array) $profile_user->roles;

    // Only job_seeker profiles are viewable
    if (!in_array('job_seeker', $profile_roles, true)) return false;

    // Only logged-in job_seekers and employers can view
    if (in_array('job_seeker', $viewer_roles, true)) return true;
    if (in_array('employer', $viewer_roles, true)) return true;

    return false;
}

function acuc_profile_payload_for_user($viewer_id, $profile_user) {

    $is_owner = ($viewer_id === (int) $profile_user->ID);

$viewer = wp_get_current_user();
$can_employer_decide = false;

if (in_array('employer', (array) $viewer->roles, true)) {

    $job_id = intval($_POST['job_id'] ?? 0);

    if ($job_id) {
        $job = get_post($job_id);

        if ($job && (int) $job->post_author === (int) $viewer->ID) {

            $apps = get_post_meta($job_id, 'acuc_applications', true);

            if (is_array($apps)) {
                foreach ($apps as $app) {
                    if ((int) $app['user_id'] === (int) $profile_user->ID) {
                        $can_employer_decide = true;
                        break;
                    }
                }
            }
        }
    }
}


    $full_name = $profile_user->display_name ?: $profile_user->user_login;
    $email     = $profile_user->user_email ?: '';

    // Phone saved during registration
    $phone = get_user_meta($profile_user->ID, 'full_phone', true);

    // Profile meta
    $location   = get_user_meta($profile_user->ID, 'acuc_profile_location', true);
    $headline   = get_user_meta($profile_user->ID, 'acuc_profile_headline', true);
    $about      = get_user_meta($profile_user->ID, 'acuc_profile_about', true);
    $searchable = (int) get_user_meta($profile_user->ID, 'acuc_profile_searchable', true);

    // Avatar
    $avatar_id  = (int) get_user_meta($profile_user->ID, 'acuc_profile_avatar', true);
    $avatar_url = $avatar_id ? wp_get_attachment_image_url($avatar_id, 'thumbnail') : '';

    return [
        'full_name'  => $full_name,
        'email'      => $email,
        'phone'      => $phone ?: '',
        'location'   => $location ?: '',
        'headline'   => $headline ?: '',
        'about'      => $about ?: '',
        'searchable' => $searchable ? 1 : 0,
        'avatar_url' => $avatar_url ?: '',
        'is_owner'   => $is_owner ? 1 : 0,
		'can_employer_decide' => $can_employer_decide,

    ];
}

/* -------------------------
   GET PROFILE
------------------------- */
add_action('wp_ajax_acuc_get_profile', function () {

    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')) {
        wp_send_json_error('Invalid security token');
    }

    $viewer    = wp_get_current_user();
    $viewer_id = get_current_user_id();

    // Resolve profile user FIRST
    if (!empty($_POST['user_id'])) {
        $profile_user = get_user_by('id', absint($_POST['user_id']));
    } else {
        $profile_user = $viewer;
    }

    if (!$profile_user) {
        wp_send_json_error('Profile not found');
    }

    // Owner check
    $is_owner = ($viewer_id === (int) $profile_user->ID);

    // Permission check
    if (!$is_owner && !acuc_profile_can_view_jobseeker($viewer, $profile_user)) {
        wp_send_json_error('Unauthorized');
    }

    /* ======================================================
       EMPLOYER DECISION LOGIC (THIS WAS MISSING)
    ====================================================== */
    $can_employer_decide = false;

    if (in_array('employer', (array) $viewer->roles, true)) {

        $job_id = intval($_POST['job_id'] ?? 0);

        if ($job_id) {
            $job = get_post($job_id);

            // Employer must own the job
            if ($job && (int) $job->post_author === (int) $viewer->ID) {

                $apps = get_post_meta($job_id, 'acuc_applications', true);

                if (is_array($apps)) {
                    foreach ($apps as $app) {
                        if ((int) $app['user_id'] === (int) $profile_user->ID) {
                            $can_employer_decide = true;
                            break;
                        }
                    }
                }
            }
        }
    }

    /* ======================================================
       BUILD PAYLOAD (THIS IS THE KEY)
    ====================================================== */
    $payload = acuc_profile_payload_for_user($viewer_id, $profile_user);

    // 👇 THIS LINE IS WHAT YOU WERE ASKING ABOUT
    $payload['can_employer_decide'] = $can_employer_decide;

    wp_send_json_success($payload);
});



/* -------------------------
   SAVE PROFILE (Owner only)
------------------------- */
add_action('wp_ajax_acuc_save_profile', function () {

    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')) {
        wp_send_json_error('Invalid security token');
    }

    $viewer_id = get_current_user_id();

    // Only owner can save their own profile
    if (!empty($_POST['user_id']) && absint($_POST['user_id']) !== $viewer_id) {
        wp_send_json_error('Unauthorized');
    }

    $headline   = sanitize_text_field($_POST['headline'] ?? '');
    $about      = wp_kses_post($_POST['about'] ?? '');
    $searchable = !empty($_POST['searchable']) ? 1 : 0;

    // OPTIONAL (if you later add a location input)
    if (isset($_POST['location'])) {
        $location = sanitize_text_field($_POST['location']);
        update_user_meta($viewer_id, 'acuc_profile_location', $location);
    }

    update_user_meta($viewer_id, 'acuc_profile_headline', $headline);
    update_user_meta($viewer_id, 'acuc_profile_about', $about);
    update_user_meta($viewer_id, 'acuc_profile_searchable', $searchable);

    $profile_user = get_user_by('id', $viewer_id);
    wp_send_json_success(acuc_profile_payload_for_user($viewer_id, $profile_user));
});



/* -------------------------
   UPLOAD AVATAR (Owner only)
------------------------- */
add_action('wp_ajax_acuc_upload_profile_avatar', function () {

    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')) {
        wp_send_json_error('Invalid security token');
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    if (empty($_FILES['avatar'])) {
        wp_send_json_error('No file');
    }

    // Upload to Media Library
    $id = media_handle_upload('avatar', 0);

    if (is_wp_error($id)) {
        wp_send_json_error($id->get_error_message());
    }

    update_user_meta(get_current_user_id(), 'acuc_profile_avatar', (int) $id);

    $url = wp_get_attachment_image_url($id, 'thumbnail');

    wp_send_json_success([
        'avatar_url' => $url ?: ''
    ]);
});

/* =========================================================
   TOPBAR USER AVATAR (Jobseeker Homepage)
========================================================= */
add_action('wp_ajax_acuc_get_topbar_user', function () {

    if (!is_user_logged_in()) {
        wp_send_json_error();
    }

    $user_id = get_current_user_id();
    $user    = get_user_by('id', $user_id);

    $avatar_id  = (int) get_user_meta($user_id, 'acuc_profile_avatar', true);
    $avatar_url = $avatar_id
        ? wp_get_attachment_image_url($avatar_id, 'thumbnail')
        : '';

    wp_send_json_success([
        'name'       => $user->display_name,
        'email'      => $user->user_email,
        'role'       => in_array('employer', $user->roles, true) ? 'Employer' : 'Jobseeker',
        'avatar_url' => $avatar_url
    ]);
});


add_action('wp_ajax_acuc_get_job_meta', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('employer')) {
        wp_send_json_error('Unauthorized');
    }

    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')
    ) {
        wp_send_json_error('Invalid nonce');
    }

    $job_id = intval($_POST['job_id']);
    $job = get_post($job_id);

    if (!$job || $job->post_type !== 'job_listing') {
        wp_send_json_error('Invalid job');
    }

    if ((int)$job->post_author !== get_current_user_id()) {
        wp_send_json_error('Not your job');
    }

    wp_send_json_success([
        'title'    => $job->post_title,
        'location' => get_post_meta($job_id, 'job_location', true),
    ]);
});

add_action('wp_ajax_acuc_bulk_applicant_action', 'acuc_bulk_applicant_action');
function acuc_bulk_applicant_action() {

    if (!is_user_logged_in() || !acuc_user_has_role('employer')) {
        wp_send_json_error('Unauthorized');
    }

    if (
        empty($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')
    ) {
        wp_send_json_error('Invalid nonce');
    }

    $job_id = (int) ($_POST['job_id'] ?? 0);
    $users  = array_map('intval', $_POST['users'] ?? []);
    $action = sanitize_text_field($_POST['bulk_action'] ?? '');

    if (!$job_id || empty($users) || !in_array($action, ['accepted','declined'], true)) {
        wp_send_json_error('Invalid request');
    }

    $job = get_post($job_id);
    if (!$job || (int) $job->post_author !== get_current_user_id()) {
        wp_send_json_error('Not your job');
    }

    $apps = get_post_meta($job_id, 'acuc_applications', true);
    if (!is_array($apps)) $apps = [];

    foreach ($apps as &$app) {

        if (!isset($app['user_id'])) continue;

        if (in_array((int)$app['user_id'], $users, true)) {
            $app['status']     = $action;
            $app['decided_at'] = time();
        }
    }
    unset($app);

    update_post_meta($job_id, 'acuc_applications', array_values($apps));

    // 🔔 notify users
    foreach ($users as $uid) {
        acuc_add_notification($uid, [
            'type'      => 'application_status',
            'job_id'    => $job_id,
            'job_title' => $job->post_title,
            'status'    => $action,
            'time'      => time(),
            'read'      => false,
        ]);
    }

    wp_send_json_success();
}




/* =========================================================
   EMPLOYER PROFILE (AJAX + USER META)
   - Employer edits own profile
   - Logged-in users can view employer profile (public fields)
   - Consistent meta keys; backward compatibility kept
========================================================= */

/** Canonical employer meta keys */
function acuc_emp_meta_key_map() {
    return [
        'company_name'   => 'acuc_emp_company_name',
        'company_email'  => 'acuc_emp_company_email',
        'company_phone'  => 'acuc_emp_company_phone',
        'website'        => 'acuc_emp_company_website',
        'location'       => 'acuc_emp_company_location',
        'industry'       => 'acuc_emp_company_industry',
        'company_size'   => 'acuc_emp_company_size',
        'about'          => 'acuc_emp_company_about',
        'logo_id'        => 'acuc_emp_company_logo_id',
        'cover_id'       => 'acuc_emp_company_cover_id',
    ];
}

/** Who can view an employer profile */
function acuc_can_view_employer_profile($viewer_user, $profile_user) {
    if (!$viewer_user || !$profile_user) return false;

    $viewer_roles  = (array) $viewer_user->roles;
    $profile_roles = (array) $profile_user->roles;

    // Profile must be employer
    if (!in_array('employer', $profile_roles, true)) return false;

    // Only logged in users (job_seeker/employer/admin) can view
    if (in_array('job_seeker', $viewer_roles, true)) return true;
    if (in_array('employer', $viewer_roles, true)) return true;
    if (in_array('administrator', $viewer_roles, true)) return true;

    return false;
}

/** Build payload */
function acuc_employer_profile_payload($viewer_id, $profile_user) {

    $keys = acuc_emp_meta_key_map();
    $is_owner = ($viewer_id === (int) $profile_user->ID);

    $company_name = get_user_meta($profile_user->ID, $keys['company_name'], true);
    if ($company_name === '') {
        // fallback to old meta, then to WP display name
        $company_name = get_user_meta($profile_user->ID, 'company_name', true);
    }
    if ($company_name === '') {
        $company_name = $profile_user->display_name ?: $profile_user->user_login;
    }

    $logo_id  = (int) get_user_meta($profile_user->ID, $keys['logo_id'], true);
    $cover_id = (int) get_user_meta($profile_user->ID, $keys['cover_id'], true);

    $payload = [
        'user_id'      => (int) $profile_user->ID,
        'is_owner'     => $is_owner ? 1 : 0,

        // Public fields
        'company_name' => $company_name,
        'industry'     => get_user_meta($profile_user->ID, $keys['industry'], true) ?: '',
        'company_size' => get_user_meta($profile_user->ID, $keys['company_size'], true) ?: '',
        'location'     => get_user_meta($profile_user->ID, $keys['location'], true) ?: '',
        'about'        => get_user_meta($profile_user->ID, $keys['about'], true) ?: '',
        'website'      => get_user_meta($profile_user->ID, $keys['website'], true) ?: '',

        // Media
        'logo_url'     => $logo_id ? (wp_get_attachment_image_url($logo_id, 'thumbnail') ?: '') : '',
        'cover_url'    => $cover_id ? (wp_get_attachment_image_url($cover_id, 'large') ?: '') : '',
    ];

    // Private fields only for owner
    if ($is_owner) {
        $payload['company_email'] = get_user_meta($profile_user->ID, $keys['company_email'], true) ?: ($profile_user->user_email ?: '');
        $payload['company_phone'] = get_user_meta($profile_user->ID, $keys['company_phone'], true) ?: '';
    }

    return $payload;
}




/* -------------------------
   SAVE EMPLOYER PROFILE (Owner only)
------------------------- */
add_action('wp_ajax_acuc_save_employer_profile', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('employer')) {
        wp_send_json_error('Unauthorized');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')) {
        wp_send_json_error('Invalid security token');
    }

    $user_id = get_current_user_id();

    // Owner only
    if (!empty($_POST['user_id']) && absint($_POST['user_id']) !== $user_id) {
        wp_send_json_error('Unauthorized');
    }

    $keys = acuc_emp_meta_key_map();

    $company_name  = sanitize_text_field($_POST['company_name'] ?? '');
    $company_email = sanitize_email($_POST['company_email'] ?? '');
    $company_phone = sanitize_text_field($_POST['company_phone'] ?? '');
    $website       = esc_url_raw($_POST['website'] ?? '');
    $location      = sanitize_text_field($_POST['location'] ?? '');
    $industry      = sanitize_text_field($_POST['industry'] ?? '');
    $company_size  = sanitize_text_field($_POST['company_size'] ?? '');
    $about         = wp_kses_post($_POST['about'] ?? '');

    /* -------------------------
       SAVE EMPLOYER PROFILE (SOURCE OF TRUTH)
    ------------------------- */
    update_user_meta($user_id, $keys['company_name'], $company_name);
    update_user_meta($user_id, $keys['company_email'], $company_email);
    update_user_meta($user_id, $keys['company_phone'], $company_phone);
    update_user_meta($user_id, $keys['website'], $website);
    update_user_meta($user_id, $keys['location'], $location);
    update_user_meta($user_id, $keys['industry'], $industry);
    update_user_meta($user_id, $keys['company_size'], $company_size);
    update_user_meta($user_id, $keys['about'], $about);

    /* -------------------------
       BACKWARD COMPATIBILITY
    ------------------------- */
    if ($company_name !== '') {
        update_user_meta($user_id, 'company_name', $company_name);
        wp_update_user([
            'ID'           => $user_id,
            'display_name' => $company_name
        ]);
    }

    /* -------------------------
       AUTO-SYNC ALL EMPLOYER JOBS
    ------------------------- */
    $jobs = get_posts([
        'post_type'      => 'job_listing',
        'post_status'    => 'any',
        'author'         => $user_id,
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);

    foreach ($jobs as $job_id) {
        acuc_sync_employer_profile_to_job($job_id, $user_id);
    }

    /* -------------------------
       RETURN UPDATED PROFILE
    ------------------------- */
    $profile_user = get_user_by('id', $user_id);
    wp_send_json_success(
        acuc_employer_profile_payload($user_id, $profile_user)
    );
});


/* -------------------------
   UPLOAD EMPLOYER LOGO (Owner only)
------------------------- */
add_action('wp_ajax_acuc_upload_employer_logo', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('employer')) {
        wp_send_json_error('Unauthorized');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')) {
        wp_send_json_error('Invalid security token');
    }

    if (empty($_FILES['logo'])) wp_send_json_error('No file');

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $id = media_handle_upload('logo', 0);
    if (is_wp_error($id)) wp_send_json_error($id->get_error_message());

    $keys = acuc_emp_meta_key_map();
    update_user_meta(get_current_user_id(), $keys['logo_id'], (int) $id);

    wp_send_json_success([
        'logo_url' => wp_get_attachment_image_url($id, 'thumbnail') ?: '',
    ]);
});

/* -------------------------
   UPLOAD EMPLOYER COVER (Owner only)
------------------------- */
add_action('wp_ajax_acuc_upload_employer_cover', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('employer')) {
        wp_send_json_error('Unauthorized');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')) {
        wp_send_json_error('Invalid security token');
    }

    if (empty($_FILES['cover'])) wp_send_json_error('No file');

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $id = media_handle_upload('cover', 0);
    if (is_wp_error($id)) wp_send_json_error($id->get_error_message());

    $keys = acuc_emp_meta_key_map();
    update_user_meta(get_current_user_id(), $keys['cover_id'], (int) $id);

    wp_send_json_success([
        'cover_url' => wp_get_attachment_image_url($id, 'large') ?: '',
    ]);
});
/**
 * Employer → Job company meta mapping
 */
function acuc_employer_to_job_company_map() {
    return [
        'acuc_emp_company_name'     => 'company_name',
        'acuc_emp_company_industry' => 'company_industry',
        'acuc_emp_company_location' => 'company_location',
        'acuc_emp_company_size'     => 'company_size',
        'acuc_emp_company_website'  => 'company_website',
        'acuc_emp_company_logo_id'  => 'company_logo_id',
    ];
}
/**
 * Sync employer profile data into a job post
 */
function acuc_sync_employer_profile_to_job($job_id, $employer_id = null) {

    if (get_post_type($job_id) !== 'job_listing') return;

    if (!$employer_id) {
        $employer_id = (int) get_post_field('post_author', $job_id);
    }

    if (!$employer_id) return;

    $map = acuc_employer_to_job_company_map();

    foreach ($map as $user_meta_key => $job_meta_key) {

        $value = get_user_meta($employer_id, $user_meta_key, true);

        if ($value !== '' && $value !== null) {
            update_post_meta($job_id, $job_meta_key, $value);
        }
    }

    // Absolute fallback for company name
    if (!get_post_meta($job_id, 'company_name', true)) {
        $fallback = get_user_meta($employer_id, 'company_name', true);
        if ($fallback) {
            update_post_meta($job_id, 'company_name', $fallback);
        }
    }
}
/**
 * Auto-sync company data whenever a job is saved
 */
add_action('save_post_job_listing', function ($post_id, $post, $update) {

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;

    // Only employer-owned jobs
    $author = (int) $post->post_author;
    if (!$author) return;

    acuc_sync_employer_profile_to_job($post_id, $author);

}, 10, 3);

add_action('wp_ajax_acuc_apply_with_uploaded_cv', function () {

    if (!is_user_logged_in() || !acuc_user_has_role('job_seeker')) {
        wp_send_json_error('Unauthorized');
    }

    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')
    ) {
        wp_send_json_error('Invalid nonce');
    }

    if (empty($_FILES['cv_pdf'])) {
        wp_send_json_error('No CV uploaded');
    }

    $job_id  = intval($_POST['job_id'] ?? 0);
    $user_id = get_current_user_id();

    if (!$job_id) {
        wp_send_json_error('Invalid job');
    }

    /* =========================
       Upload PDF
    ========================= */
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $attachment_id = media_handle_upload('cv_pdf', 0);

    if (is_wp_error($attachment_id)) {
        wp_send_json_error($attachment_id->get_error_message());
    }

    /* =========================
       Apply (custom CV marker)
    ========================= */
    $result = acuc_apply_to_job_core(
        $job_id,
        'uploaded_pdf',
        $user_id,
        $attachment_id
    );

    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }

    wp_send_json_success('Applied with uploaded CV');
});

function acuc_add_notification($user_id, $data) {

    $notes = get_user_meta($user_id, 'acuc_notifications', true);
    if (!is_array($notes)) $notes = [];

    $data['id']   = uniqid('n_');
    $data['read'] = false;

    $notes[] = $data;

    update_user_meta($user_id, 'acuc_notifications', $notes);
}




add_action('wp_ajax_acuc_mark_notification_read', function () {

    if (!is_user_logged_in()) {
        wp_send_json_error('Unauthorized');
    }

    if (
        empty($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'acuc_ajax_nonce')
    ) {
        wp_send_json_error('Invalid nonce');
    }

    $notif_id = sanitize_text_field($_POST['notification_id'] ?? '');
    if (!$notif_id) {
        wp_send_json_error('Invalid notification');
    }

    $user_id = get_current_user_id();
    $notifications = get_user_meta($user_id, 'acuc_notifications', true);

    if (!is_array($notifications)) {
        wp_send_json_error('No notifications');
    }

    foreach ($notifications as &$n) {
        if (!empty($n['id']) && $n['id'] === $notif_id) {
            $n['read'] = true;
            break;
        }
    }

    update_user_meta($user_id, 'acuc_notifications', $notifications);

    wp_send_json_success();
});



add_action('wp_ajax_acuc_get_notifications', function () {

    if (!is_user_logged_in()) wp_send_json_error();

    $notes = get_user_meta(get_current_user_id(), 'acuc_notifications', true);
    if (!is_array($notes)) $notes = [];

    $unread = array_filter($notes, fn($n) => empty($n['read']));

    wp_send_json_success([
        'unread' => count($unread),
        'items'  => array_reverse($notes)
    ]);
});

add_action('wp_ajax_acuc_mark_notifications_read', function () {

    if (!is_user_logged_in()) wp_send_json_error();

    $notes = get_user_meta(get_current_user_id(), 'acuc_notifications', true);
    if (!is_array($notes)) $notes = [];

    foreach ($notes as &$n) {
        $n['read'] = true;
    }
    unset($n);

    update_user_meta(get_current_user_id(), 'acuc_notifications', $notes);

    wp_send_json_success();
});

