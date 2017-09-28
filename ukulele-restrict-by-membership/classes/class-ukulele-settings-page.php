<?php

class UkuleleSettingsPage
{
    /**
     *  This class creates the Settings Page which is located in Settings -> Ukulele Membership
     *  Settings. On the settings Page the user can add all the information that will be stored on
     *  the database as settings for the Plugin like MailChimp API, Subscribe/Unsubscribe PayPal
     *  buttons, Activate/Deactivate PayPal Debug mode, among other Settings.
     */

    private $options;
    private $tags;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'ukulele_add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'ukulele_init_settings_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'ukulele_enqueue_scripts' ) );
    }

    public function ukulele_add_settings_page() {
        add_options_page(
            __( 'Ukulele Membership Settings', 'ukulele' ), 
            __( 'Ukulele Membership Settings', 'ukulele' ), 
            'manage_options', 
            'ukulele-membership-settings', 
            array( $this, 'ukulele_create_settings_page' )
        );
    }

    public function ukulele_create_settings_page() {
        $this->options = get_option( 'ukulele_settings' );
        require_once UKULELE_PLUGIN_DIR . UKULELE_VIEWS_DIR . 'ukulele-settings-view.php';
    }

    public function ukulele_init_settings_page() {

        /*  Register Setting ukulele_settings */
        register_setting(
            'ukulele_settings_group',
            'ukulele_settings',
            array( $this, 'ukulele_sanitize' )
        );

        /*  Section for Setting the Restrictions and the Landing Pages for each Tag */
        add_settings_section(
            'ukulele_settings_section',
            __( 'Tags', 'ukulele' ),
            array( $this, 'ukulele_settings_section_info' ),
            'ukulele-membership-settings'
        );

        /*  Section for the PayPal Settings */
        add_settings_section(
            'ukulele_settings_section_paypal',
            __( 'PayPal Settings', 'ukulele' ),
            array( $this, 'ukulele_settings_section_paypal_info' ),
            'ukulele-membership-settings'
        );

        /*  Section for the MailChimp Settings */
        add_settings_section(
            'ukulele_settings_section_mailchimp',
            __( 'MailChimp Settings', 'ukulele' ),
            array( $this, 'ukulele_settings_section_mailchimp_info' ),
            'ukulele-membership-settings'
        );

        /**
         *  Check all declared Tags for Posts and Pages and declare the Setting Field dynamically
         *  for each Tag. Every Tag row contains the minimum Membership Level to access the content
         *  and the link to the Landing Page.
         */
        $this->options = get_option( 'ukulele_settings' );
        $all_tags = get_terms( array( 'post_tag', 'page_tag' ), array( 'get' => 'all' ) );

        if ( empty( $all_tags ) ) {
            add_settings_field(
                '',
                __( 'There are no Tags added yet', 'ukulele' ),
                function() {},
                'ukulele-membership-settings',
                'ukulele_settings_section'
            );
        }

        /*  For each tag for Posts and Pages, declare a Setting Field with 2 values */
        foreach ( $all_tags as $tag ) {
            /*  $value is the variable that contains the minimum access level needed */
            $value = isset( $this->options[$tag->slug] ) ? esc_attr( $this->options[$tag->slug] ) : '0';

            /*  $value_lp is the variable that contains the Landing Page for the level */
            $value_lp = isset( $this->options[$tag->slug . '-lp'] ) ? esc_attr( $this->options[$tag->slug . '-lp'] ) : '';

            /*  Anonymous function that dynamycally sets the field view for each tag */
            $anonymous_callback = function() use ( $tag, $value, $value_lp ) {
                require UKULELE_PLUGIN_DIR . UKULELE_VIEWS_DIR . 'ukulele-input-field-view.php';
            };

            /*  Declare the field and call the anonymous and dynamic function */
            add_settings_field(
                $tag->slug,
                $tag->name,
                $anonymous_callback,
                'ukulele-membership-settings',
                'ukulele_settings_section'
            );
        }

        /*  Field to determine whether to use the Sandbox or the Live accounts of PayPal */
        $value = isset( $this->options['ukulele-debug'] ) ? ' checked' : '';
        add_settings_field(
            'ukulele-debug',
            __( 'Debug', 'ukulele' ),
            function() use( $value ) {
                echo '<input type="checkbox" id="ukulele-debug" name="ukulele_settings[ukulele-debug]"' . $value . '>';
            },
            'ukulele-membership-settings',
            'ukulele_settings_section_paypal'
        );

        /*  Field to set the PayPal PDT. Check PayPal documentation for more information */
        $value = isset( $this->options['ukulele-pdt-token'] ) ? $this->options['ukulele-pdt-token'] : '';
        add_settings_field(
            'ukulele-pdt-token',
            __( 'PDT Identity Token', 'ukulele' ),
            function() use( $value ) {
                echo '<input type="text" id="ukulele-pdt-token" name="ukulele_settings[ukulele-pdt-token]" value="' . $value . '">';
            },
            'ukulele-membership-settings',
            'ukulele_settings_section_paypal'
        );

        /*  Field to set the HTML code of the Subscribe button generated by PayPal Business Account */
        $value = isset( $this->options['ukulele-subscribe-button'] ) ? $this->options['ukulele-subscribe-button'] : '';
        add_settings_field(
            'ukulele-subscribe-button',
            __( 'PayPal Subscribe Button HTML', 'ukulele' ),
            function() use( $value ) {
                echo '<textarea rows="5" id="ukulele-subscribe-button" name="ukulele_settings[ukulele-subscribe-button]">' . $value . '</textarea>';
            },
            'ukulele-membership-settings',
            'ukulele_settings_section_paypal'
        );

        /*  Same as previous field but for Unsubscribe button */
        $value = isset( $this->options['ukulele-unsubscribe-button'] ) ? $this->options['ukulele-unsubscribe-button'] : '';
        add_settings_field(
            'ukulele-unsubscribe-button',
            __( 'PayPal Unsubscribe Button HTML', 'ukulele' ),
            function() use( $value ) {
                echo '<textarea rows="5" id="ukulele-unsubscribe-button" name="ukulele_settings[ukulele-unsubscribe-button]">' . $value . '</textarea>';
            },
            'ukulele-membership-settings',
            'ukulele_settings_section_paypal'
        );

        /*  Same as previous field but for Donate button */
        $value = isset( $this->options['ukulele-donate-button'] ) ? $this->options['ukulele-donate-button'] : '';
        add_settings_field(
            'ukulele-donate-button',
            __( 'PayPal Donate Button HTML', 'ukulele' ),
            function() use( $value ) {
                echo '<textarea rows="5" id="ukulele-donate-button" name="ukulele_settings[ukulele-donate-button]">' . $value . '</textarea>';
            },
            'ukulele-membership-settings',
            'ukulele_settings_section_paypal'
        );

        /*  Field to set the MailChimp API Key for the account to manage campaigns */
        $value = isset( $this->options['ukulele-mc-api-key'] ) ? $this->options['ukulele-mc-api-key'] : '';
        add_settings_field(
            'ukulele-mc-api-key',
            __( 'MailChimp API Key', 'ukulele' ),
            function() use( $value ) {
                echo '<input type="text" id="ukulele-mc-api-key" name="ukulele_settings[ukulele-mc-api-key]" value="' . $value . '">';
            },
            'ukulele-membership-settings',
            'ukulele_settings_section_mailchimp'
        );
    }

    public function ukulele_sanitize( $input_array ) {

        /**
         *  Function to sanitize the input fields to ensure all fields are valid values for each
         *  purpose.
         */

        $sanitized_input_array = array();

        foreach ( $input_array as $key => $value) {
            if (    preg_match( '/-lp/', $key ) || 
                    preg_match( '/-pdt-token/', $key ) || 
                    preg_match( '/subscribe/', $key ) ||
                    preg_match( '/mc-api-key/', $key ) ||
                    preg_match( '/donate/', $key ) ) {
                $sanitized_input_array[$key] = $value;
            }
            else {
                $sanitized_input_array[$key] = absint( $value ) <= UKULELE_LEVEL_LIMIT ? absint( $value ) : UKULELE_LEVEL_LIMIT;
            }
        }

        return $sanitized_input_array;
    }

    public function ukulele_settings_section_info() {
        require_once UKULELE_PLUGIN_DIR . UKULELE_VIEWS_DIR . 'ukulele-settings-section-info-view.php';
    }

    public function ukulele_settings_section_paypal_info() {
        require_once UKULELE_PLUGIN_DIR . UKULELE_VIEWS_DIR . 'ukulele-settings-section-paypal-info-view.php';
    }

    public function ukulele_settings_section_mailchimp_info() {
        require_once UKULELE_PLUGIN_DIR . UKULELE_VIEWS_DIR . 'ukulele-settings-section-mailchimp-info-view.php';
    }

    public function ukulele_enqueue_scripts( $hook ) {
        if ( $hook != 'settings_page_ukulele-membership-settings' )
            return;

        wp_enqueue_style( 'ukulele_custom_admin_css', plugins_url( '../assets/ukulele-admin-style.css', __FILE__ ) );
    }
}

if( is_admin() )
    $ukulele_settings_page = new UkuleleSettingsPage();