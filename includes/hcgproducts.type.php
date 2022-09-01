<?php
/**
 * Class that creates the hcg-products custom post type
 * 
 * It also creates a hcg-product-categories taxonomy
 *
 *
 */
class hcgProducts_type
{
   public $_fields = array(
      'ProductName' => array(
         'id' => 'product_name',
         'label' => 'Product Name',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'ProductCode' => array(
         'id' => 'product_code',
         'label' => 'Product Code',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'PackageSize' => array(
         'id' => 'package_size', 
         'label' => 'Package Size',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'AvailableIn' => array(
         'id' => 'available_in', 
         'label' => 'Available In',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'LongDescription' => array(
         'id' => 'long_description', 
         'label' => 'Long Description',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'Teaser' => array(
         'id' => 'teaser',        
         'label' => 'Teaser',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'Footnotes' => array(
         'id' => 'footnotes',  
         'label' => 'Footnotes',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'Ingredients' => array(
         'id' => 'ingredients',  
         'label' => 'Ingredients',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'ProductID' => array(
         'id' => 'product_id',
         'label' => 'Product ID',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'UPC' => array(
         'id' => 'upc', 
         'label' => 'UPC',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'UPC12' => array(
         'id' => 'upc12', 
         'label' => 'UPC12',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'Language' => array(
         'id' => 'language', 
         'label' => 'Language',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'FlagAsNew' => array(
         'id' => 'flag_as_new',  
         'label' => 'Flag As New',
         'allow_tags' => FALSE,
         'control' => 'select',
         'options' => array(
            array('value' => '0', 'text' => 'No'),
            array('value' => '1', 'text' => 'Yes'),
         ),
      ),
      'ProductGroup' => array(
         'id' => 'product_group', 
         'label' => 'Product Group',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'NutritionFacts' => array(
         'id' => 'nutrition_facts',
         'label' => 'Nutrition Facts',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'StoreSection' => array(
         'id' => 'store_section',  
         'label' => 'Store Section',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'StoreSectionPostfix' => array(
         'id' => 'store_section_postfix', 
         'label' => 'Store Section Postfix',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'StoreDetail' => array(
         'id' => 'store_detail',  
         'label' => 'Store Detail',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'LocatorCode' => array(
         'id' => 'locator_code', 
         'label' => 'Locator Code',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'BenefitsDisplay' => array(
         'id' => 'benefits_display',
         'label' => 'Benefits Display',
         'allow_tags' => FALSE,
         'control' => 'select',
         'options' => array(
            array('value' => 'none', 'text' => 'Display any or all'),
            array('value' => 'Benefits', 'text' => 'Display Benefits field only'),
            array('value' => 'SmartBenefits', 'text' => 'Display Smart Benefits only'),
            array('value' => 'NutritionScorecard', 'text' => 'Display Nutrition Scorecard only'),
         ),
      ),
      'Benefits' => array(
         'id' => 'benefits',
         'label' => 'Benefits',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'SmartBenefits' => array(
         'id' => 'smart_benefits',
         'label' => 'Smart Benefits',
         'allow_tags' => TRUE,
         'control' => 'text',
      ),
      'NSSodium' => array(
         'id' => 'ns_sodium', 
         'label' => 'NS Sodium',
         'allow_tags' => FALSE,
         'control' => 'select',
         'options' => array(
            array('value' => '0', 'text' => 'No'),
            array('value' => '1', 'text' => 'Yes'),
         ),
      ),
      'NSSodiumQuantity' => array(
         'id' => 'ns_sodium_quantity',
         'label' => 'NS Sodium Quantity',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'NSFat' => array(
         'id' => 'ns_fat', 
         'label' => 'NS Fat',
         'allow_tags' => FALSE,
         'control' => 'select',
         'options' => array(
            array('value' => '0', 'text' => 'No'),
            array('value' => '1', 'text' => 'Yes'),
         ),
      ),
      'NSFatQuantity' => array(
         'id' => 'ns_fat_quantity',
         'label' => 'NS Fat Quantity',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'NSFiber' => array(
         'id' => 'ns_fiber',  
         'label' => 'NS Fiber',
         'allow_tags' => FALSE,
         'control' => 'select',
         'options' => array(
            array('value' => '0', 'text' => 'No'),
            array('value' => '1', 'text' => 'Yes'),
         ),
      ),
      'NSFiberQuantity' => array(
         'id' => 'ns_fiber_quantity',
         'label' => 'NS Fiber Quantity',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'NSAntioxidants' => array(
         'id' => 'ns_antioxidants',
         'label' => 'NS Fat Quantity',
         'allow_tags' => FALSE,
         'control' => 'select',
         'options' => array(
            array('value' => '0', 'text' => 'No'),
            array('value' => '1', 'text' => 'Yes'),
         ),
      ),
      'NSAntioxidantChoice' => array(
         'id' => 'ns_antioxidant_choice',
         'label' => 'NS Antioxidant Choice',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'NSAntioxidantQuantity' => array(
         'id' => 'ns_antioxidant_quantity',
         'label' => 'NS Antioxidant Quantity',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'NSCalories' => array(
         'id' => 'ns_calories',
         'label' => 'NS Calories',
         'allow_tags' => FALSE,
         'control' => 'select',
         'options' => array(
            array('value' => '0', 'text' => 'No'),
            array('value' => '1', 'text' => 'Yes'),
         ),
      ),
      'NSCaloriesQuantity' => array(
         'id' => 'ns_calories_quantity',
         'label' => 'NS Calories Quantity',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'NSOther' => array(
         'id' => 'ns_other',
         'label' => 'NS Other',
         'allow_tags' => FALSE,
         'control' => 'select',
         'options' => array(
            array('value' => '0', 'text' => 'No'),
            array('value' => '1', 'text' => 'Yes'),
         ),
      ),
      'NSOtherChoice' => array(
         'id' => 'ns_other_choice',
         'label' => 'NS Other Choice',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'NSOtherQuantity' => array(
         'id' => 'ns_other_quantity',
         'label' => 'NS Other Quantity',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'KosherFile' => array(
         'id' => 'kosher_file',
         'label' => 'Kosher File',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'KosherWidth' => array(
         'id' => 'kosher_width',
         'label' => 'Kosher Width',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'KosherHeight' => array(
         'id' => 'kosher_height',
         'label' => 'Kosher Height',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'KosherAlt' => array(
         'id' => 'kosher_alt',
         'label' => 'Kosher Alt',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'OrganicFile' => array(
         'id' => 'organic_file',
         'label' => 'Organic File',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'OrganicWidth' => array(
         'id' => 'organic_width',
         'label' => 'Organic Width',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'OrganicHeight' => array(
         'id' => 'organic_height',
         'label' => 'Organic Height',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'OrganicAlt' => array(
         'id' => 'organic_alt',
         'label' => 'Organic Alt',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'OrganicStatement' => array(
         'id' => 'organic_statement',
         'label' => 'Organic Statement',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'AllNatural' => array(
         'id' => 'all_natural',
         'label' => 'All Natural',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'Gluten' => array(
         'id' => 'gluten',
         'label' => 'Gluten',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'Alergens' => array(
         'id' => 'alergens',
         'label' => 'Allergens',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'SpiceLevel' => array(
         'id' => 'spice_level',
         'label' => 'Spice Level',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'FlavorDescriptor' => array(
         'id' => 'flavor_descriptor',
         'label' => 'Flavor Descriptor',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'CaffeineAmount' => array(
         'id' => 'caffeine_amount',
         'label' => 'Caffeine Amount',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'CaffeineStatement' => array(
         'id' => 'caffeine_statement',
         'label' => 'Caffeine Statement',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'NutritionBlend' => array(
         'id' => 'nutrition_blend',
         'label' => 'Nutrition Blend',
         'allow_tags' => FALSE,
         'control' => 'textarea',
      ),
      'Standardization' => array(
         'id' => 'standardization',
         'label' => 'Standardization',
         'allow_tags' => FALSE,
         'control' => 'textarea',
      ),
      'Directions' => array(
         'id' => 'directions',
         'label' => 'Directions',
         'allow_tags' => FALSE,
         'control' => 'textarea',
      ),
      'Warning' => array(
         'id' => 'warning',
         'label' => 'Warning',
         'allow_tags' => FALSE,
         'control' => 'textarea',
      ),
      'ThumbFile' => array(
         'id' => 'thumb_file',
         'label' => 'Thumb File',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'ThumbWidth' => array(
         'id' => 'thumb_width',
         'label' => 'Thumb Width',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'ThumbHeight' => array(
         'id' => 'thumb_height',
         'label' => 'Thumb Height',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'ThumbAlt' => array(
         'id' => 'thumb_alt',
         'label' => 'Thumb Alt',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'SmallFile' => array(
         'id' => 'small_file',
         'label' => 'Small File',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'SmallWidth' => array(
         'id' => 'small_width',
         'label' => 'Small Width',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'SmallHeight' => array(
         'id' => 'small_height',
         'label' => 'Small Height',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'SmallAlt' => array(
         'id' => 'small_alt',
         'label' => 'Small Alt',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'LargeFile' => array(
         'id' => 'large_file',
         'label' => 'Large File',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'LargeWidth' => array(
         'id' => 'large_width',
         'label' => 'Large Width',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'LargeHeight' => array(
         'id' => 'large_height',
         'label' => 'Large Height',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'LargeAlt' => array(
         'id' => 'large_alt',
         'label' => 'Large Alt',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'Featured' => array(
         'id' => 'featured',
         'label' => 'Meta Descripton',
         'allow_tags' => FALSE,
         'control' => 'select',
         'options' => array(
            array('value' => '0', 'text' => 'No'),
            array('value' => '1', 'text' => 'Yes'),
         ),
      ),
      'FeatureFile' => array(
         'id' => 'feature_file',
         'label' => 'Feature File',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'FeatureWidth' => array(
         'id' => 'feature_width',
         'label' => 'Feature Width',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'FeatureHeight' => array(
         'id' => 'feature_height',
         'label' => 'Feature Height',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'FeatureAlt' => array(
         'id' => 'feature_alt',
         'label' => 'Feature Alt',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'BeautyFile' => array(
         'id' => 'beauty_file',
         'label' => 'Beauty File',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'BeautyWidth' => array(
         'id' => 'beauty_width',
         'label' => 'Beauty Width',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'BeautyHeight' => array(
         'id' => 'beauty_height',
         'label' => 'Beauty Height',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'BeautyAlt' => array(
         'id' => 'beauty_alt',
         'label' => 'Beauty Alt',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'MetaTitle' => array(
         'id' => 'meta_title',
         'label' => 'Meta Title',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'MetaDescription' => array(
         'id' => 'meta_description',
         'label' => 'Meta Descripton',
         'allow_tags' => FALSE,
         'control' => 'textarea',
      ),
      'MetaKeywords' => array(
         'id' => 'meta_keywords', 
         'label' => 'Meta Keywords',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'MetaAbstract' => array(
         'id' => 'meta_abstract', 
         'label' => 'Meta Abstract',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'MetaRobots' => array(
         'id' => 'meta_robots',
         'label' => 'Meta Robots',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),  
      'Verified' => array(
         'id' => 'verified',
         'label' => 'Verified',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'SortOrder' => array(
         'id' => 'sort_order',
         'label' => 'Sort Order',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'ProductType' => array(
         'id' => 'product_type',
         'label' => 'Product Type',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'ProductStatus' => array(
         'id' => 'product_status',
         'label' => 'Product Status',
         'allow_tags' => FALSE,
         'control' => 'select',
         'options' => array(
            array('value' => 'active', 'text' => 'Active'),
            array('value' => 'inactive', 'text' => 'Inactive'),
            array('value' => 'discontinued', 'text' => 'Discontinued'),
            array('value' => 'pending', 'text' => 'Pending'),
            array('value' => 'partial', 'text' => 'Partial')
         ),
      ),
      'DiscontinueDate' => array(
         'id' => 'discontinue_date',
         'label' => 'Discontinue Date',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'Replacements' => array(
         'id' => 'replacements',
         'label' => 'Replacements',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'LastModifiedDate' => array(
         'id' => 'last_modified_date',
         'label' => 'Last Modified Date',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'LastModifiedBy' => array(
         'id' => 'last_modified_by',
         'label' => 'Last Modified By',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
   );

   // ------------------------------------------------------------

   /**
    * The Constructor
    */
   public function __construct()
   {
      // register actions
      add_action('init', array(&$this, 'init'));
      add_action('admin_init', array(&$this, 'admin_init'));
   }

   // ------------------------------------------------------------

   /**
    * hook into WP's init action hook
    */
   public function init()
   {
      // Initialize Post Type
      $this->create_post_type();
      add_action('save_post', array(&$this, 'save_post'));
   }

   // ------------------------------------------------------------

   /**
    * Create the post type
    */
   public function create_post_type()
   {
      $labels = array( 
         'name' => 'Product Categories',
         'singular_name' => 'Product Category',
         'search_items' => 'Search Product Categories',
         'popular_items' => 'Popular Product Categories',
         'all_items' => 'All Product Categories',
         'parent_item' => 'Parent Product Category',
         'parent_item_colon' => 'Parent Product Category:',
         'edit_item' => 'Edit Product Category',
         'update_item' => 'Update Product Category',
         'add_new_item' => 'Add New Product Category',
         'new_item_name' => 'New Product Category',
         'separate_items_with_commas' => 'Separate categories with commas',
         'add_or_remove_items' => 'Add or remove categories',
         'choose_from_most_used' => 'Choose from the most used categories',
         'menu_name' => 'Categories',
      );

      $args = array( 
         'labels' => $labels,
         'public' => true,
         'show_in_nav_menus' => true,
         'show_ui' => true,
         'show_tagcloud' => true,
         'hierarchical' => true,
         'rewrite' => array('slug' => 'product-categories'),
         'query_var' => true
      );

      register_taxonomy('hcg-product-categories', array('hcg-products'), $args );
	
      $labels = array(
         'name' => 'Products',
         'singular_name' => 'Products',
         'add_new' => 'Add New Product',
         'add_new_item' => 'Add New Product',
         'edit_item' => 'Edit Product',
         'new_item' => 'New Product',
         'view_item' => 'View Product',
         'search_items' => 'Search Products',
         'not_found' => 'No Products found',
         'not_found_in_trash' => 'No Products found in Trash',
         'parent_item_colon' => 'Parent Product:',
         'menu_name' => 'Products',
      );

      $args = array( 
         'labels' => $labels,
         'hierarchical' => false,
         'description' => 'Products',
         'supports' => array('title', 'thumbnail'),
         'public' => true,
         'show_ui' => true,
         'show_in_menu' => true,
         'menu_position' => 5,
         'show_in_nav_menus' => true,
         'publicly_queryable' => true,
         'exclude_from_search' => false,
         'has_archive' => true,
         'query_var' => true,
         'can_export' => true,
         'rewrite' => array('slug' => 'products'),
         'capability_type' => 'post'
      );
      
      register_post_type('hcg-products', $args);

   }

   // ------------------------------------------------------------

   /**
    * Custom rewrites - this is incomplete and needs to be tied to the
    * hcgproducts_alias_category option
    * 
    * Reference: https://wordpress.org/support/topic/custom-post-types-permalinks
    */
   // add_action('init', 'my_rewrite');
   function my_rewrite()
   {
      global $wp_rewrite;
      $wp_rewrite->add_permastruct('typename', 'typename/%year%/%postname%/', true, 1);
      add_rewrite_rule('typename/([0-9]{4})/(.+)/?$', 'index.php?typename=$matches[2]', 'top');
      $wp_rewrite->flush_rules(); // !!!
   }

   // ------------------------------------------------------------

   /**
    * Save the metaboxes for this custom post type
    */
   public function save_post($post_id)
   {
      // verify if this is an auto save routine. 
      // If it is our form has not been submitted, so we dont want to do anything
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
      {
         return;
      }
      
      // check that the form is being submitted legitimately
      if ( ! wp_verify_nonce($_POST['hcg_products_nonce'], plugin_basename( __FILE__ )))
      {
         return;
      }

      if (isset($_POST['post_type']) && $_POST['post_type'] == 'hcg-products' && current_user_can('edit_post', $post_id))
      {
         foreach ($this->_fields as $field_name)
         {
            // Update the post's meta field
            update_post_meta($post_id, $field_name['id'], $_POST[$field_name]);
         }
      }
      else
      {
         return;
      }
   }

   // ------------------------------------------------------------

   /**
    * hook into WP's admin_init action hook
    */
   public function admin_init()
   {           
      // Add metaboxes
      add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
   }

   // ------------------------------------------------------------

   /**
    * hook into WP's add_meta_boxes action hook
    */
   public function add_meta_boxes()
   {
      global $post;
      
      $productData = array();
      foreach ($this->_fields as $field_name)
      {
         $productData[$field_name['id']] = get_post_meta($post->ID, $field_name['id'], true);
      }

      add_meta_box('meta_box_api_fields', 'API FIELDS (DO NOT EDIT)', array(&$this, 'meta_box_api_fields_content'), 'hcg-products', 'normal', 'default', $productData);

      add_filter("postbox_classes_hcg-products_meta_box_api_fields", array(&$this, 'minify_metabox'));
   }

   // ------------------------------------------------------------
   
   /*
    * Callback for add_meta_box() in $this->add_meta_boxes()
    */
   function meta_box_api_fields_content($post, $args)
   {
      wp_nonce_field(plugin_basename( __FILE__ ), 'hcg_products_nonce' );
      foreach ($this->_fields as $field_name)
      {
            $data = null;
            if ($field_name['control'] == 'select')
            {
               $data = $field_name['options'];
            }
            echo hcgproducts_get_control($field_name['control'], $field_name['label'], 'hcg_products_settings_'.$field_name['id'], $field_name['id'], ((isset($args['args'][$field_name['id']]))?$args['args'][$field_name['id']]:''), $data);
         }
   }

   // ------------------------------------------------------------
   
   /*
    * Sets the default state for meta boxes to closed.
    */
   function minify_metabox($classes)
   {
      array_push( $classes, 'closed' );
      return $classes;
   }


} // END class PostTypeTemplate
