<?php
/**
 * Plugin Name: Connect Paid Memberships Pro to Subtext
 * Description: Instantly connects the Subtext text messaging service with Paid Memberships Pro.
 * Version: 1.0.1 
 * Author: Fourth Estate
 * Author URI: https://www.fourthestate.org
 * Text Domain: fe-subtext-pmp
 * Domain Path: /languages
 */
 /*

 * Copyright 2022 Fourth Estate®
 * (email : support@fourthestate.org)
 * GPLv2 Full license details in license.txt

  You must have your Subtext API key, and Campaign ID set in the Subtext Connector for this plugin to work.
	The plugin will automatically add and remove members to subtext on subscription, unsubscription, or manually opt-in and opt-out.
	You do not need to activate this plugin with Subtext.

	This plugin will only work if you have an active Subtext publisher account.

  This plugin requires the following plugins to function:
	* Paid Memberships Pro
	* Paid Memberships Pro - Shipping Add On
*/

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

define( 'FE_SUBTEXT_VERSION', dirname( __FILE__ ) );
define( 'FE_SUBTEXT_DIR', dirname( __FILE__ ) );
define( 'FE_SUBTEXT_BASENAME', plugin_basename( __FILE__ ) );
define( 'FE_SUBTEXT_URL', plugins_url( '', __FILE__ ) );

function pluginprefix_activate() {
    if ( is_plugin_active( 'paid-memberships-pro/paid-memberships-pro.php') ) {

    }
}
register_activation_hook( __FILE__, 'pluginprefix_activate' );

function fe_subtext_activate( $network_wide ) {
    //replace this with your dependent plugin
    $dependencies = array(
        'paid-memberships-pro/paid-memberships-pro.php' => 'Paid Memberships Pro',
        'pmpro-shipping/pmpro-shipping.php' => 'Paid Memberships Pro - Shipping Add On',
    );

    foreach ( $dependencies as $dependency => $label ) {
        $pmp_plugin_error = false;

        if ( ! file_exists( WP_PLUGIN_DIR . '/' . $dependency ) ) {
            $pmp_plugin_error = true;
        }

        if ( ! is_plugin_active( $dependency ) ) {
            $pmp_plugin_error = true;
        }

        if ( $pmp_plugin_error ) {
            echo wp_kses(
                '<h3>' . __( 'You need to install ', 'fe-subtext-pmp' ) . $label . __( ' to use this plugin.', 'fe-subtext-pmp' ) . '</h3>',
                array(
                    'h3' => array(),
                )
            );

            //Adding @ before will prevent XDebug output
            @trigger_error(__('You need to install and activate ', 'fe-subtext-pmp') . $label . __( ' to use this plugin.', 'fe-subtext-pmp'), E_USER_ERROR );
        }
    }
}

register_activation_hook(__FILE__, 'fe_subtext_activate');

class Subtext_For_PMP {
    public function init()
    {
        require_once FE_SUBTEXT_DIR . '/includes/functions.php';

        /**
         * Subtext API
         */
        require_once FE_SUBTEXT_DIR . '/includes/api/abstract-subtext-api.php';
        require_once FE_SUBTEXT_DIR . '/includes/api/class-external-subscribers.php';
        require_once FE_SUBTEXT_DIR . '/includes/api/class-subtext-subscriber.php';

        require_once FE_SUBTEXT_DIR . '/includes/admin/class-settings.php';
        require_once FE_SUBTEXT_DIR . '/includes/class-edit-profile.php';

        $this->register_hooks();
    }

    public function register_hooks()
    {
        add_filter( 'fespmp_settings', array( $this, 'get_plugin_settings' ), 0 );
    }

    public function get_plugin_settings()
    {
        return get_option( 'fe_subtext_pmp_settings' );
    }
}

(new Subtext_For_PMP())->init();
