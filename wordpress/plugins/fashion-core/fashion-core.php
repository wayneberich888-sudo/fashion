<?php
/**
 * Plugin Name: Fashion Core
 * Description: Minimal catalog rules and product data contracts for FASHION.
 * Version: 0.1.0
 * Requires at least: 7.0
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * Text Domain: fashion-core
 */

defined( 'ABSPATH' ) || exit;

define( 'FASHION_CORE_VERSION', '0.1.0' );
define( 'FASHION_CORE_PATH', plugin_dir_path( __FILE__ ) );

foreach ( array( 'catalog-mode.php', 'product-identity.php', 'brand.php' ) as $fashion_core_include ) {
	require_once FASHION_CORE_PATH . 'inc/' . $fashion_core_include;
}
