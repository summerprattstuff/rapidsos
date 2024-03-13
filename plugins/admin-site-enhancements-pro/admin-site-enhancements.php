<?php

/**
 * Plugin Name: Admin and Site Enhancements (ASE) Pro
 * Plugin URI:        https://www.wpase.com/
 * Description:       Easily enable enhancements and features that usually require multiple plugins.
 * Version:           6.9.3
 * Update URI: https://api.freemius.com
 * Author:            wpase.com
 * Author URI:        https://www.wpase.com/author-uri
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       admin-site-enhancements
 * Domain Path:       /languages
 * 
 * @fs_premium_only   /README.txt, /classes/class-php-validator.php, /classes/class-wp-config-transformer.php, /includes/premium/, /assets/premium/, /vendor/scssphp/
 */
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'admin-site-enhancements/admin-site-enhancements.php' ) ) {
    wp_die( 'Please deactivate the free version of ASE before activating the Pro version. You can safely delete the free version after activating the Pro version. <a href="/wp-admin/plugins.php">Return to plugins list &raquo;</a>' );
} else {
    define( 'ASENHA_VERSION', '6.9.3' );
    define( 'ASENHA_ID', 'asenha' );
    define( 'ASENHA_SLUG', 'admin-site-enhancements' );
    define( 'ASENHA_SLUG_U', 'admin_site_enhancements' );
    define( 'ASENHA_URL', plugins_url( '/', __FILE__ ) );
    // e.g. https://www.example.com/wp-content/plugins/this-plugin/
    define( 'ASENHA_PATH', plugin_dir_path( __FILE__ ) );
    // e.g. /home/user/apps/wp-root/wp-content/plugins/this-plugin/
    // define( 'ASENHA_BASE', plugin_basename( __FILE__ ) ); // e.g. plugin-slug/this-file.php
    // define( 'ASENHA_FILE', __FILE__ ); // /home/user/apps/wp-root/wp-content/plugins/this-plugin/this-file.php
    /**
     * Autoload classes defined by this plugin
     *
     * @param string $class_name e.g. \ASENHA\Classes\The_Name
     * @since 1.0.0
     */
    function asenha_autoloader( $class_name )
    {
        $namespace = 'ASENHA';
        // Only process classes within this plugin's namespace
        //
        
        if ( false !== strpos( $class_name, $namespace ) ) {
            // Assemble file path where class is defined
            // \ASENHA\Classes\The_Name => \Classes\The_Name
            $path = str_replace( $namespace, "", $class_name );
            // \Classes\The_Name => /classes/the_name
            $path = str_replace( "\\", DIRECTORY_SEPARATOR, strtolower( $path ) );
            // /classes/the_name =>  /classes/the-name.php
            $path = str_replace( "_", "-", $path ) . '.php';
            // /classes/the-name.php => /classes/class-the-name.php
            $path = str_replace( "classes" . DIRECTORY_SEPARATOR, "classes" . DIRECTORY_SEPARATOR . "class-", $path );
            // Remove first '/'
            $path = substr( $path, 1 );
            // Get /plugin-path/classes/class-the-name.php
            $path = ASENHA_PATH . $path;
            if ( file_exists( $path ) ) {
                require_once $path;
            }
        }
    
    }
    
    // Register autoloading classes
    spl_autoload_register( 'asenha_autoloader' );
    // Freemius SDK integration
    
    if ( function_exists( 'bwasenha_fs' ) ) {
        bwasenha_fs()->set_basename( true, __FILE__ );
    } else {
        
        if ( !function_exists( 'bwasenha_fs' ) ) {
            // Create a helper function for easy SDK access.
            function bwasenha_fs()
            {
                global  $bwasenha_fs ;
                
                if ( !isset( $bwasenha_fs ) ) {
                    // Include Freemius SDK.
                    require_once dirname( __FILE__ ) . '/freemius/start.php';
                    $bwasenha_fs = fs_dynamic_init( array(
                        'id'             => '12592',
                        'slug'           => 'admin-site-enhancements',
                        'premium_slug'   => 'admin-site-enhancements-pro',
                        'type'           => 'plugin',
                        'public_key'     => 'pk_63ac7b1231b9251da8088e519fbf8',
                        'is_premium'     => true,
                        'premium_suffix' => 'Pro',
                        'has_addons'     => false,
                        'has_paid_plans' => true,
                        'menu'           => array(
                        'slug'       => 'admin-site-enhancements',
                        'parent'     => array(
                        'slug' => 'tools.php',
                    ),
                        'first-path' => 'tools.php?page=admin-site-enhancements',
                        'account'    => false,
                        'contact'    => false,
                        'support'    => false,
                    ),
                        'navigation'     => 'tabs',
                        'is_live'        => true,
                    ) );
                }
                
                return $bwasenha_fs;
            }
            
            // Init Freemius.
            bwasenha_fs();
            // Signal that SDK was initiated.
            do_action( 'bwasenha_fs_loaded' );
        }
        
        /**
         * Code that runs on plugin activation
         * 
         * @since 1.0.0
         */
        function asenha_on_activation()
        {
            $activation = new ASENHA\Classes\Activation();
            $activation->create_failed_logins_log_table();
        }
        
        /**
         * Code that runs on plugin deactivation
         * 
         * @since 1.0.0
         */
        function asenha_on_deactivation()
        {
            $deactivation = new ASENHA\Classes\Deactivation();
            $deactivation->delete_failed_logins_log_table();
        }
        
        // Register code that runs on plugin activation
        register_activation_hook( __FILE__, 'asenha_on_activation' );
        // Register code that runs on plugin deactivation
        register_deactivation_hook( __FILE__, 'asenha_on_deactivation' );
        // Functions for setting up admin menu, admin page, the settings sections and fields and other foundational stuff
        require_once ASENHA_PATH . 'settings.php';
        // Other required functions
        require_once ASENHA_PATH . 'functions.php';
        // Load vendor libraries
        require_once ASENHA_PATH . 'vendor/autoload.php';
        // Bootstrap all the functionalities of this plugin
        require_once ASENHA_PATH . 'bootstrap.php';
    }

}
