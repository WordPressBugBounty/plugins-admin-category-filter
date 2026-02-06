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
            // Only on post editor and list tables where categories checklists appear.
            $supported = array( 'post.php', 'post-new.php', 'edit.php' );
            if ( ! in_array( $hook, $supported, true ) ) {
                return;
            }

            // Script.
            wp_register_script(
                'pcf-admin',
                APCF_PLUGIN_URL . 'assets/js/admin.js',
                array( 'jquery' ),
                (string)APCF_VERSION,
                true
            );

            wp_localize_script( 'pcf-admin', 'pcfPlugin', $this->get_plugin_settings() );
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
            $base   = $screen ? $screen->base : '';

            /* translators: %s: taxonomy singular name. */
            $placeholder = esc_html__( 'Filter %s', 'admin-category-filter' );

            return array(
                'placeholder' => $placeholder,
                'screenName'  => $base,
				'enableGutenberg' => (bool) apply_filters('pcf_enable_gutenberg_filter', true),
            );
        }
    }
}
