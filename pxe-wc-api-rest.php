<?php

/**
 * Plugin Name:       PXE WC Api Rest
 * Plugin URI:        
 * Description:       Extends the Woocommerce Rest Api
 * Version:           1.0.0
 * Author:            Pixie
 * Author URI:        http://www.pixie.com.uy/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pxe-extend-api-rest
 * Domain Path:       /languages/
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!class_exists('PXE_WC_Api_Rest')) :
    class PXE_WC_Api_Rest
    {

        protected static $instance = NULL;

        public static function get_instance()
        {
            if (null === self::$instance) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        /**
         * __construct
         *
         * @return void
         */
        public function __construct()
        {
            //add_action( 'woocommerce_rest_check_permissions', __CLASS__ . '::pxe_wc_rest_check_permissions' );    
            add_action('rest_api_init', __CLASS__ . '::extend_product_endpoint');
            add_action('rest_api_init', __CLASS__ . '::create_order_endpoint');
            //add_filter('woocommerce_taxonomy_args_product_cat', __CLASS__ . '::extend_product_cat');
        }

        /**
         * create_order_endpoint
         *
         * @return void
         */
        public static function create_order_endpoint()
        {
            register_rest_route('wp/v2', '/create_order/', array(
                'methods' => 'POST',
                'callback' => __CLASS__ . '::create_order',
            ));
        }

        /**
         * create_order
         *
         * @return void
         */
        public static function create_order( $request )
        {
            $parameters = $request->get_params();

            return 'create_order';
        }

        /**
         * pxe_wc_rest_check_permissions
         *
         * @return void
         */
        /* public static function pxe_wc_rest_check_permissions() {
            return true;
        } */

        /**
         * extend_product_endpoint
         * 
         * Includes extra information in the Product endpoint
         *
         * @return void
         */
        public static function extend_product_endpoint()
        {
            // Product meta fields to include
            $fields = array(
                'product_image_gallery',
                'regular_price',
                'sale_price_dates_from',
                'sale_price_dates_to',
                'sale_price',
                'sku',
            );

            foreach ($fields as $field) {
                register_rest_field('product', $field, array(
                    'get_callback' => function ($object, $field) {
                        return get_post_meta($object['id'], '_' . $field, true);
                    }
                ));
            }

            // Product Categories
            register_rest_field('product', 'product_cat', array(
                'get_callback' => function ($object) {
                    $terms = get_the_terms($object['id'], 'product_cat');
                    foreach ($terms as $term) {
                        $product_categories[] = $term->term_id;
                    }
                    return $product_categories;
                }
            ));

            // Product Price
            register_rest_field('product', 'price', array(
                'get_callback' => function ($object) {
                    $product = wc_get_product($object['id']);

                    return $product->get_price();
                }
            ));

            // Featured Product
            register_rest_field('product', 'featured', array(
                'get_callback' => function ($object) {
                    $product = wc_get_product($object['id']);

                    return $product->get_featured();
                }
            ));

            // Rating Count of Product
            register_rest_field('product', 'rating_count', array(
                'get_callback' => function ($object) {
                    $product = wc_get_product($object['id']);

                    return $product->get_rating_counts();
                }
            ));

            // Average Rating of Product
            register_rest_field('product', 'average_rating', array(
                'get_callback' => function ($object) {
                    $product = wc_get_product($object['id']);

                    return $product->get_average_rating();
                }
            ));
        }

        /**
         * extend_product_cat
         *
         * @param  mixed $args
         * @return void
         */
        /* public static function extend_product_cat($args)
        {
            $args['show_in_rest'] = true;
            return $args;
        } */
    }

    $PXE_WC_Api_Rest = new PXE_WC_Api_Rest;

endif;
