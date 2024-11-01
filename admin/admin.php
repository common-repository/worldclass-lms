<?php

/*require_once WCIO_PLUGIN_DIR . '/admin/includes/admin-functions.php';
require_once WCIO_PLUGIN_DIR . '/admin/includes/help-tabs.php';
require_once WCIO_PLUGIN_DIR . '/admin/includes/tag-generator.php';*/

function url_get($url) {
    // is cURL installed yet?
    if (!function_exists('curl_init')){
        die('This plugin requires the cURL extension to be installed');
    }

    // OK cool - then let's create a new cURL resource handle
    $ch = curl_init();

    // Now set some options (most are optional)

    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $url);

    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);

    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // Download the given URL, and return output
    $output = curl_exec($ch);

    // Close the cURL resource, and free system resources
    curl_close($ch);

    return $output;
}

/**
 * Displays a formatted message after options page submission.
 *
 * @param string $message: should already be internationlized.
 * @param string $type: error, warning, or updated.
 */
function wcio_options_message( $message, $type = 'updated' ) {
    ?>
    <div id="wcio-options-message">
        <div class="<?php echo $type; ?>">
            <p><?php echo $message; ?></p>
        </div>
    </div>
    <?php
}

/**
 * Gets the standard wcio settings from the database and return as an array.
 */
function wcio_load_settings() {
    // wcio_set_default_settings();
    $settings = get_option( 'wcio_settings' );
    return unserialize( $settings );
}

/**
 * Saves the settings array
 *
 * @param array $settings: 'option_name' => 'value'
 */
function wcio_save_settings( $settings ) {
    $settings = serialize( $settings );
    update_option( 'wcio_settings', $settings );
}

/**
 * Markup for main academy admin page
 */
function wcio_academy_page_callback() {

    $wcio_options = wcio_load_settings();

    if ( isset( $_POST['wcio_options_nonce'] ) ) {
        // We got a submission
        $nonce = sanitize_text_field( $_POST['wcio_options_nonce'] );
        $valid = wp_verify_nonce( $nonce, 'wcio_save_options' );

        if ( $valid === FALSE ) {
            // Nonce verification failed.
            wcio_options_message( __('Nonce verification failed', 'wcio'), 'error' );
        }
        else {
            wcio_options_message( __('Options updated', 'wcio') );

            // Validate API key
            $api_key = sanitize_text_field( $_POST['wc_api_key'] );
            $wcio_options['wc_api_key'] = $api_key;

            // Validate Domain
            $domain = sanitize_text_field( $_POST['wc_domain'] );
            $domain = preg_replace("/^https?:\/\//", "", $domain);
            $domain = untrailingslashit($domain);
            $wcio_options['wc_domain'] = $domain;

            $disable_https = isset($_POST['wc_disable_https']) && $_POST['wc_disable_https'] == 'yes' ? 'yes' : 'no';
            $wcio_options['wc_disable_https'] = $disable_https;

            wcio_save_settings( $wcio_options );
        }
    }

    if ($wcio_options['wc_domain']) {
        $domain = $wcio_options['wc_domain'];
        // We have a domain, let's check if it's a custom domain
        // or a worldclass.io domain
        $data_url = null;
        if (strpos($domain, '.worldclass.io') > -1) {
            // This is a standard worldclass.io domain
            $data_url = "https://$domain/index.php/api/appGet";
        }
        else {
            // This is a custom domain
            $dashed = str_replace('.', '-', $domain);
            $data_url = 'https://custom-domain-' . $dashed . '.worldclass.io/index.php/api/appGet';
        }

        // Fetch data from academy
        $data = url_get($data_url);

        if ($data) {
            try {
                $data = json_decode($data);
                $school_data = $data->data;
            }
            catch (Exception $e) {
                wcio_options_message( $e->getMessage(), 'error' );
            }
        }
        else {
            wcio_options_message( __( 'Failed to get school data. Please make sure your academy domain is correct'), 'error' );
        }
    }

    $email = urlencode(wp_get_current_user()->user_email);
    $site_url = urlencode(site_url());


    ?>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="wcio_general_settings">
        <div class="wrap">
            <h2><?php _e( 'Worldclass Academy Management', 'wcio' ); ?></h2>

        <?php if (!$school_data): ?>
            <div class="no-school-data-instructions">
                <p><?php _e( 'To start using <b>Worldclass</b>, please enter your academy domain in the field below and hit "Save".', 'wcio'); ?>
                </p>

                <a href="http://studio.worldclass.io/#!/signup?src=wp&user=<?php echo $email; ?>&site=<?php echo $site_url; ?>" target="_blank" class="button button-primary" style="margin-bottom: 20px;">
                    <?php _e( "No academy? Click here to create one", 'wcio' ); ?>
                </a>

                <div>
                    <h3><?php _e( 'Worldclass for WordPress offers:' ); ?></h3>

                    <ul style="list-style-type: disc;
                            padding-left: 20px;
                            margin-top: 0;
                            margin-bottom: 30px;
                            font-size: 15px">
                        <li><?php _e( 'Unlimited storage' ); ?></li>
                        <li><?php _e( 'Free up to 150 active users' ); ?></li>
                        <li><?php _e( 'No credit card required' ); ?></li>
                    </ul>
                </div>

                <div>
                    <h3><?php _e( 'What is Worldclass?' ); ?></h3>
                    <iframe width="420" height="236" src="https://www.youtube.com/embed/fq3bEUaT6ts" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($school_data): ?>
            <div class="no-school-data-instructions">
                <h4>How to use Worldclass:</h4>
                <p><?php _e( "To display your academy on the site, simply enter the shortcode <code>[worldclass]</code> inside a page or a post.", 'wcio' ); ?>

                <!-- <br/>To display a specific course, simply add the course ID parameter to the shortcode like so <code>[worldclass course=MY_COURSE_ID]</code>.<br/><small>Note: You can find all your course IDs listed in the courses table below.</small></p> -->

            </div>
        <?php endif; ?>


            <hr/>

            <table class="form-table wc-admin-table">
                <tr>
                    <td>
                        <label for="wc_domain"><?php _e( 'Academy Domain', 'wcio' ); ?></label>
                        <p class="description"><?php _e( 'Please enter your academy domain', 'wcio' ); ?></p>
                    </td>
                    <td>
                        <input name="wc_domain" type="text" id="wc_domain"
                               value="<?php echo $wcio_options['wc_domain']; ?>"
                               placeholder="<?php _e( 'example: myschool.worldclass.io', 'wcio' ); ?>"
                               class="regular-text">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="wc_domain"><?php _e( 'Disable SSL/HTTPS', 'wcio' ); ?></label>
                        <p class="description"><?php _e( 'Only disable SSL/HTTPS if you are using a custom domain and your domain does NOT support SSL/HTTPS', 'wcio' ); ?><br/><br/><?php _e('Not sure? contact support <a href="mailto:support@worldclass.io" target="_blank">here</a>', 'wcio'); ?></p>
                    </td>
                    <td>
                        <input name="wc_disable_https" type="checkbox" id="wc_disable_https"
                               value="yes"
                            <?php echo (isset($wcio_options['wc_disable_https']) && $wcio_options['wc_disable_https'] == 'yes' ? "checked" : ""); ?>> Yes, disable SSL/HTTPS

                    </td>
                </tr>
            </table>

            <?php submit_button( __( 'Save' ) ); ?>
            <?php wp_nonce_field( 'wcio_save_options', 'wcio_options_nonce' ); ?>
        </div>
    </form>

    <?php /*var_dump($school_data); */?>

    <?php if ($school_data): ?>

        <hr/>
        <div class="wc-admin-school-wrapper">
            <h2 style="margin-bottom: 5px;">
                <?php echo $school_data->school->name; ?>
            </h2>
            <?php if ($school_data->school->tagline1): ?>
                <p style="font-weight: 300; margin-top: 0">
                    <?php echo $school_data->school->tagline1; ?>
                </p>
            <?php endif; ?>

            <?php if (count($school_data->courses) == 0): ?>

                <?php _e( 'There are no courses in this academy yet. Please visit <a href="studio.worldclass.io" target="_blank">Worldclass Studio</a> to create some.'); ?>

            <?php else: ?>

                <h4><?php _e( 'School Courses:' ); ?></h4>

                <table class="wp-list-table widefat fixed striped pages">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>ID</th>
                    </tr>
                    <?php foreach ($school_data->courses as $course): ?>
                        <tr>
                            <td>
                                <?php echo $course->name; ?>
                            </td>
                            <td>
                                <?php echo $course->description; ?>
                            </td>
                            <td>
                                <?php echo $course->id; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            <?php endif; ?>
        </div>

    <?php endif; ?>

    <?php
}


/* Styles and scripts */
function wcio_plugin_admin_styles() {
    wp_enqueue_style( 'wcio-admin-main', WCIO_PLUGIN_URL . '/admin/css/styles.css', array());
}

?>