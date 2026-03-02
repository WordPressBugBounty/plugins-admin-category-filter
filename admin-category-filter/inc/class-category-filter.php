<?php
/**
 * Admin Category Filter main class.
 *
 * Forked from the original work by Javier Villanueva (jahvi).
 * Continued and maintained by Ivijan Stefan Stipic (INFINITUM FORM).
 *
 * @package Admin_Category_Filter
 * @license GPL-2.0-or-later
 *
 * Copyright (c) 2013-2018 Javier Villanueva
 * Copyright (c) 2025 Ivijan Stefan Stipic
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Post_Category_Filter' ) ) {

    class Post_Category_Filter {

        /**
         * Singleton instance.
         *
         * @var Post_Category_Filter|null
         */
        protected static $instance = null;

        /**
         * Get singleton instance.
         *
         * @return Post_Category_Filter
         */
        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor.
         */
        private function __construct() {
			// Add custom action link under plugin name in plugins list.
			add_filter( 'plugin_row_meta', array( $this, 'apcf_plugin_row_meta'), 10, 2 );
			
            // Admin scripts.
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

            // Localize settings.
            add_action( 'current_screen', array( $this, 'maybe_localize_screen' ) );
        }
		
		/**
		 * Modify plugin row meta links.
		 *
		 * @param string[] $links Existing meta links for the plugin row.
		 * @param string   $file  Plugin file basename (e.g. 'admin-category-filter/admin-category-filter.php').
		 *
		 * @return string[]
		 */
		public function apcf_plugin_row_meta( $links, $file ) {
			// Only modify links for this specific plugin.
			if ( isset( $file ) && $file === APCF_PLUGIN_BASENAME ) {

				// Build rating link HTML.
				$rating_link = '<a href="https://wordpress.org/support/plugin/admin-category-filter/reviews/?filter=5" target="_blank" rel="noopener noreferrer" class="apcf-plugins-action-vote" title="Give us five if you like!"><span style="color:#ffa000; font-size:15px; position:relative; bottom:-1px;">★★★★★</span> 5 Stars?</a>';

				$links[] = $rating_link;
			}

			return $links;
		}

        /**
         * Enqueue admin JS when on supported screens.
         *
         * @param string $hook Current admin page hook.
         * @return void
         */
        public function enqueue_admin_assets( $hook ) {
			$supported = array( 'post.php', 'post-new.php', 'edit.php' );
			if ( ! in_array( $hook, $supported, true ) ) {
				return;
			}

			wp_register_script(
				'pcf-admin',
				APCF_PLUGIN_URL . 'assets/js/admin.js',
				array( 'jquery' ),
				(string) APCF_VERSION,
				true
			);

			$settings = $this->get_plugin_settings();

			// Backward compatibility: old JS expects fc_plugin.
			wp_localize_script( 'pcf-admin', 'fc_plugin', array(
				'placeholder' => isset( $settings['placeholderFinal'] ) ? $settings['placeholderFinal'] : 'Filter Categories',
			) );

			// New JS object (current).
			wp_localize_script( 'pcf-admin', 'pcfPlugin', $settings );

			wp_enqueue_script( 'pcf-admin' );
		}

        /**
         * Localize after screen is set to correctly detect base.
         *
         * @return void
         */
        public function maybe_localize_screen() {
            // No-op, kept for backward compatibility with older hooks.
        }

        /**
         * Build localized settings.
         *
         * @return array
         */
        public function get_plugin_settings() {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
			$base   = $screen ? (string) $screen->base : '';

			// Provide both template and final string to avoid %s issues.
			$placeholder_template = __( 'Filter %s', 'admin-category-filter' );

			// Default to "Categories" unless we can infer better.
			$taxonomy_label = __( 'Categories', 'admin-category-filter' );

			// If we are on a post type screen, try to infer the taxonomy label.
			if ( $screen && ! empty( $screen->post_type ) ) {
				// WooCommerce products typically use "product_cat".
				$tax = ( 'product' === $screen->post_type ) ? 'product_cat' : 'category';

				$taxonomy_obj = get_taxonomy( $tax );
				if ( $taxonomy_obj && ! empty( $taxonomy_obj->labels->singular_name ) ) {
					$taxonomy_label = (string) $taxonomy_obj->labels->singular_name;
				}
			}

			$placeholder_final = sprintf( $placeholder_template, $taxonomy_label );

			return array(
				'placeholder'        => $placeholder_template,
				'placeholderFinal'   => $placeholder_final,
				'screenName'         => $base,
				'enableGutenberg'    => (bool) apply_filters( 'pcf_enable_gutenberg_filter', true ),
			);
		}
    }
}
