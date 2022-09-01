<?php
/**
 * hcgProducts_options class to manage options
 *
 * @package hcgProducts
 * @author Jim Applegate <jim.applegate@hain.com>
 * 
 * This file is part of hcgProducts, a plugin for Wordpress.
 *
 **/

require_once HCGPRODUCTS_PLUGIN_DIR . '/includes/hcgproducts.class.php';
require_once HCGPRODUCTS_PLUGIN_DIR . '/includes/hcgproducts.helpers.php';


class hcgProducts_options
{

   public $debug_log_enabled = FALSE;
   
   private $result_msg = array();
   
   /**
    * Instansiate Custom Post Type class
    *
    * @var string
    * @access private
   **/
   public $hcgproducts_type;
   
   /**
    * Flag as whether hcgAPI plugin is installed
    */
   private $dependancies_active = TRUE;

   // ------------------------------------------------------------

   public function __construct()
   {
      // Registers the admin menu with WordPress
      add_action('admin_menu', array(&$this, 'admin_menu'));
      add_action('admin_footer', array(&$this, 'sync_javascript'));
      add_action('wp_ajax_hcgproducts_sync_products', array(&$this, 'sync_products'));
   }

   // ------------------------------------------------------------

   /**
    * Registers the admin menu with WordPress
    */
   public function admin_menu()
   {
      add_options_page(
         'hcgProducts Options', 
         'hcgProducts', 
         'manage_options', 
         'hcgproducts-options', 
         array(&$this, 'settings_page')
      );
   }

   // ------------------------------------------------------------

   /**
    * Implements the Settings page
    */
   public function settings_page()
   {
      $theme_path = get_template_directory().'/hcg-products';
      
      if ( ! current_user_can('manage_options'))
      {
         wp_die( __('You do not have sufficient permissions to access this page.'));
      }
      
      if ( ! function_exists('hcgapi_get_api_key'))
      {
         $this->dependancies_active = FALSE;
         $this->set_result_msg('<strong>The sync process requires that the hcgAPI plugin be installed and activated.</strong><br />The "Sync Product Data with the hcgWeb API" button below will remain inactive until the hcgAPI plugin is activated.', 'error');
      }
   
      $options = array();
      
      $options['use_groups'] = (get_option('hcgproducts_use_groups') != '') ? get_option('hcgproducts_use_groups') : '1';
      $options['restrict_to_categories'] = (get_option('hcgproducts_restrict_to_categories') != '') ? get_option('hcgproducts_restrict_to_categories') : '';
      $options['alias_category'] = (get_option('hcgproducts_alias_category') != '') ? get_option('hcgproducts_alias_category') : '1';
      $options['templates'] = (get_option('hcgproducts_templates') != '') ? get_option('hcgproducts_templates') : '1';

      
      // See if the user has posted us some information
      $updated = FALSE;
      if (isset($_POST['update_settings']) && $_POST['update_settings'] == 'Y')
      {
         if ( ! isset($_POST['alias_category']))
         {
            $_POST['alias_category'] = '0';
         }
            
         if ( ! isset($_POST['templates']))
         {
            $_POST['templates'] = '0';
         }
            
         foreach ($options AS $key => $value)
         {
            // Read their posted value
            $options[$key] = $_POST[$key];
            
            // double-check that the template directory exists
            if ($key == 'hcgproducts_templates' && $options[$key] == 1 && ! is_dir($theme_path))
            {
               $options['hcgproducts_templates'] = '0';
               $this->set_result_msg('hcgProducts theme templates can\'t be used because they don\'t exist.', 'error');
			}

            // Save the posted value in the database
            update_option('hcgproducts_'.$key, $options[$key]);

            // Put an settings updated message on the screen
            $updated = TRUE;
         }
      }
      include HCGPRODUCTS_PLUGIN_DIR . '/includes/settings.php';
      
   }

   // ------------------------------------------------------------

   /**
    * Provides the javascript required to activate the Sync button.
    *
    * @access public
    * @return void
   **/
   public function sync_javascript()
   {
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {

  $('#loader').hide();
  <?php if ( ! $this->dependancies_active): ?>
  $("#sync-products").prop("disabled",true);
  <?php endif; ?>

  jQuery("#sync-products").click( function() {
    var data = {
      'action': 'hcgproducts_sync_products',
      'whatever': 1234
    };
    $(this).prop("disabled",true);
    $('#loader').show();
    $.post(ajaxurl, data, function(response) {
      $('#loader').hide();
      $("#sync-products").prop("disabled",false);
      $("#sync-message").show().html("Sync response: "+response);
    });
  });

});
</script>
<?php
   } /* end of sync_javascript() */

// -------------------------------------------------------------------

   /**
    * 
    *
    * @access public
    * @return array
   **/
   public function get_product_category_lookup($list, $api_key, $site_id)
   {
      $lookup = array();

      foreach ($list as $category)
      {
         // the first entry from the API is the root node and is not needed.
         if ($category->CategoryCode == 'root') {
            continue;
         }

         $category_id = _hcgproducts_entities_to_ascii($category->CategoryID);

         try
         {
            $hcgapi_url = get_option('hcgapi_url');
            $hcg = new HCGProducts($api_key, $site_id, $hcgapi_url);
            $hcg->setFormat('xml');
            $product_list_xml = $hcg->getProductList('id', $category->CategoryID);
         }
         catch (Exception $e)
         {
            continue;
         }

         // you can then use SimpleXML to convert the XML into a data object
         try
         {
            $product_list_obj = new SimpleXMLElement($product_list_xml);
         }
         catch (Exception $e) {
            continue;
         }

         if (isset($product_list_obj->response->Products->Product))
         {
            foreach ($product_list_obj->response->Products->Product AS $product)
            {
               if ( ! array_key_exists($product->ProductID.'', $lookup))
               {
                  $lookup[$product->ProductID.''] = array();
               }

               if ($this->_save_this_product($product) == TRUE)
               {
                  $lookup[$product->ProductID.''][] = $category_id;
               }
            }
         }
      }
      return $lookup;
   }

   // ------------------------------------------------------------

   /**
    * Sync the data from the products API.
    *
    * @access public
    * @return void
   **/
   public function sync_products()
   {
      global $wpdb; // this is how you get access to the database
      global $user;
           
      set_time_limit(0);
      
      $is_error = FALSE;

      // gets the proper API key from the hcgAPI plugin
      $api_key = hcgapi_get_api_key();
      $site_id = get_option('hcgapi_site_id', '');

      if ($api_key == '')
      {
         $this->set_result_msg('An API key needs to be set to sync with the API. Make sure a URL for this site level is configured properly (no trailing slash).', 'error');
         $is_error = TRUE;
      }
      if ($site_id == '')
      {
         $this->set_result_msg('A site ID needs to be set to sync with the API.', 'error');
         $is_error = TRUE;
      }

      if ( ! $is_error)
      {
         $existing_cats = $this->_get_category_lookup();
         
         // ---------------------------------------------------
         // first, get all the product categories from the API
         // ---------------------------------------------------
         try
         {
            $hcgapi_url = get_option('hcgapi_url');
            $hcg = new HCGProducts($api_key, $site_id, $hcgapi_url);
            $hcg->setFormat('xml');
            $category_list_xml = $hcg->getCategoryList();
         }
         catch (Exception $e)
         {
            $this->set_result_msg('CategoryList: '.$e->getMessage().'. Please try again.', 'error');
            $is_error = TRUE;
         }

         // you can then use SimpleXML to convert the XML into a data object
         try
         {
            $category_list_obj = new SimpleXMLElement($category_list_xml);
         }
         catch (Exception $e) {
            $this->set_result_msg('SimpleXML (categories): '.$e->getMessage().'. Please try again.', 'error');
            $is_error = TRUE;
         }

         // make sure we have some results
         if (count($category_list_obj->response->ProductCategories->ProductCategory) == 0)
         {
            $this->set_result_msg('No categories were found. Please check the Site ID and try again.', 'error');
            $is_error = TRUE;
         }
      }
      
      if ( ! $is_error)
      {
         // make a first pass over the api and get product->category information
         // this is done in case a product has moved categories.  It is also done up
         // front in the event a product is in multiple categories
         $product_category_lookup = $this->get_product_category_lookup($category_list_obj->response->ProductCategories->ProductCategory, $api_key, $site_id);

         // -----------------------------------------------------------
         // now, process each of the categories (and related products)
         // -----------------------------------------------------------
         
         // see if our update is limited to certain categories
         $allowed_categories = explode(",",  get_option('hcgproducts_restrict_to_categories', ''));
         if ((count($allowed_categories) == 1) && ( ! $allowed_categories[0]))
         {
            $allowed_categories = NULL;
         }
         
         foreach ($category_list_obj->response->ProductCategories->ProductCategory AS $category)
         {
            $category_code = _hcgproducts_entities_to_ascii($category->CategoryCode);
            
            // the first entry from the API is the root node and is not needed.
            if ($category_code == 'root')
            {
               continue;
            }         

            // Check and make sure the category is not discontinued.
            if (_hcgproducts_entities_to_ascii($category->Status) == 'discontinued')
            {
               continue;
            }         

            // if the settings indicate that only certain categories should be used
            if ($allowed_categories)
            {
               if ( ! in_array($category_code, $allowed_categories))
               {
                  $this->set_result_msg("Skipped ".$category_code);
                  continue;
               }
            }
    
            // insert or update the category (term) into the vocabulary
            $category_id = _hcgproducts_entities_to_ascii($category->CategoryID);
            $term_id = $this->_get_term_id($category_id);
            
            // Get a copy of the existing term to update or create a new object
            // so we can create a new term record.
            if ($term_id != 0)
            {
               $term = get_term($term_id, 'hcg-product-categories', ARRAY_A);
            }
            else
            {
               $term = array();
            }

            $term['name'] = _hcgproducts_entities_to_ascii($category->CategoryName);
            $term['description'] = _hcgproducts_entities_to_ascii($category->CategoryText);
            $term['parent'] = $this->_get_term_id(_hcgproducts_entities_to_ascii($category->CategoryParentID));
            $term['slug'] = _hcgproducts_entities_to_ascii($category->CategoryCode);

            /* Category order is not something that is built into WordPress
               taxonomy terms. If we want to preserve the order of categories
               we may need to require another plugin or add the functionality
               in this one.
            */
            // $term->weight = _hcgproducts_entities_to_ascii($category->CategoryOrder);

            $this->set_result_msg($term['name'], 'notice');

            if (isset($existing_cats["$category->CategoryID"]))
            {
               unset($existing_cats["$category->CategoryID"]);
            }

            // do the actual insert or update of the term record
            if ($term_id != 0)
            {
               $my_term = wp_update_term($term_id, 'hcg-product-categories', array(
                  'name' => $term['name'],
                  'description'=> $term['description'],
                  'slug' => $term['slug'],
                  'parent' => $term['parent']
               ));
            }
            else
            {
               $my_term = wp_insert_term($term['name'], 'hcg-product-categories', array(
                  'description'=> $term['description'],
                  'slug' => $term['slug'],
                  'parent' => $term['parent']
               ));
            }

            if (is_wp_error($my_term))
            {
               $error_string = $my_term->get_error_message();
               $this->set_result_msg("Error adding saving the category : ".$error_string, 'error');
               continue;
            }

            // if this is a new category, add it to our reference table
            if ($term_id == FALSE)
            {
               $table_name = $wpdb->prefix.'hcgproducts_category';
               $term_id = $my_term['term_id'];

               // make sure there is not already a record for this CategoryID
               $wpdb->delete($table_name, array('category_id' => _hcgproducts_entities_to_ascii($category->CategoryID)));

               // then save the new one
               $data = array(
                  'term_id' => $term_id,
                  'category_id' => _hcgproducts_entities_to_ascii($category->CategoryID)
               );
               $wpdb->insert($table_name, $data);
            }

            try
            {
               $hcgapi_url = get_option('hcgapi_url');
               $hcg = new HCGProducts($api_key, $site_id, $hcgapi_url);
               $hcg->setFormat('xml');
               $product_list_xml = $hcg->getProductList('id', $category->CategoryID);
            }
            catch (Exception $e)
            {
               $this->set_result_msg('ProductList: '.$e->getMessage().'. Please try again.', 'error');
               continue;
            }

            // you can then use SimpleXML to convert the XML into a data object
            try
            {
               $product_list_obj = new SimpleXMLElement($product_list_xml);
            }
            catch (Exception $e)
            {
               $this->set_result_msg('SimpleXML (products): '.$e->getMessage().'. Please try again.', 'error');
               continue;
            }

            if (isset($product_list_obj->response->Products->Product))
            {
               foreach ($product_list_obj->response->Products->Product AS $product)
               {
                  // Check and make sure the product is not discontinued.
                  if ($product->ProductStatus != 'active' && $product->ProductStatus != 'partial')
                  {
                     continue;
                  }
                  
                  try
                  {
                     $this->sync_product($product, $category_code, $product_category_lookup);
                  }
                  catch(Exception $e)
                  {
                     $this->set_result_msg("Error updating product : ".$e->getMessage(),'error');
                     continue;
                  }
               }
            }
         }
         $this->set_result_msg('The product data has been sync\'d with the hcgWeb API.', 'notice');
      }
      
      // display the results array
      foreach ($this->result_msg AS $msg)
      {
         echo '<p>'.$msg.'</p>';
      }
      
      die(); // this is required to return a proper result
   }

   // -------------------------------------------------------------------

   /**
    * Returns a term ID for a given API-supplied category ID if it exists
    *
    * @return  integer or FALSE
    */
   function _get_term_id($cat_id)
   {
      global $wpdb;
      
      $sql = 'SELECT * '.
             'FROM '.$wpdb->prefix.'hcgproducts_category '.
             'WHERE category_id = '.$cat_id;
             
      $mapping = $wpdb->get_row($sql, ARRAY_A);

      if ($mapping != null)
      {
         $term_id = $mapping['term_id'];

         // double-check that the term record actually exists
         if ($term_id)
         {
            $term = get_term($term_id, 'hcg-product-categories');
            $term_id = ($term == null) ? 0 : $term_id;
         }
      }
      else
      {
         return 0;
      }

      return $term_id;
   }

   // -------------------------------------------------------------------

   /**
    * Returns a post ID for a given API-supplied product ID if it exists
    */
   function _get_post_id($prod_id)
   {
      $args = array(
         'meta_key' => 'product_id',
         'meta_value' => $prod_id,
      );
      $existingProduct = get_posts($args);

      $post_id = $existingProduct[0]->ID;

      if ( ! $post_id) {
         return FALSE;
      }
      return $post_id;
   }

   // -------------------------------------------------------------------

   /**
    * Returns a lookup array of category IDs
    */
   function _get_category_lookup()
   {
      global $wpdb;
      
      $sql = 'SELECT * '.
             'FROM '.$wpdb->prefix.'hcgproducts_category';
             
      $cats = $wpdb->get_results($sql, ARRAY_A);

      $cats_lookup = array();
      foreach($cats as $cat)
      {
         $cats_lookup[$cat->category_id] = $cat->tid;
      }
      return $cats_lookup;
   }

   // -------------------------------------------------------------------

   /**
    * Checks settings and this product to see if it should be saved
    */
   function _save_this_product($product)
   {
      $use_groups = get_option('hcgproducts_use_groups', 'true');
      $use_groups = ($use_groups == 'true') ? TRUE : FALSE;

      if ($use_groups && $product->ProductGroup != 'none' && $product->ProductGroup != 'master')
      {
         return FALSE;
      }
      elseif ( ! $use_groups && $product->ProductGroup == 'master')
      {
         return FALSE;
      }
      return TRUE;
   }

   // -------------------------------------------------------------------

   /**
    * Does the leg work of syncing an individual product
    *
    */
   function sync_product($product, $category_code, $product_category_lookup = NULL)
   {
      global $user;

      if ($this->_save_this_product($product) == TRUE)
      {
         // check if the product record already exists
         $new = false;
         $post_id = $this->_get_post_id($product->ProductID);
         if ($post_id != FALSE)
         {
            $post = get_post($post_id, ARRAY_A); 
         }
         else
         {
            $new = true;
            $post = array();
            
            $post['post_status'] = 'publish';
            $post['post_type'] = 'hcg-products';
            $post['comment_status'] = 'closed';
         }

         // add/update the title
         $post['post_title'] = _hcgproducts_entities_to_ascii($product->ProductName);
         //remove weird chars from title
         $post['post_title'] = preg_replace("/�/", "", $post['post_title']);
         
         $post['post_name'] = _hcgproducts_entities_to_ascii($product->ProductCode);

         if ($product_category_lookup) // i.e. if updating all products...
         {
            $this->set_result_msg('- '.$post['post_title'], 'notice');
         }
         
         // now we create the post so we can add meta data
         $post_id = wp_insert_post($post, TRUE);

         if (is_wp_error($post_id))
         {
            $error_string = $post_id->get_error_message();
            $this->set_result_msg("Error adding saving the post : ".$error_string, 'error');
            return;
         }

         // assign the product to its categories
         // if this is being called from an "all products" request, the lookup
         // will be defined, but updating a single product will skip this.
         if ($product_category_lookup)
         {
            foreach($product_category_lookup[$product->ProductID.''] as $product_category_id)
            {
               $product_category_tid = $this->_get_term_id($product_category_id);
               if ($product_category_tid)
               {
                  $terms[] = $product_category_tid;
               }
            }
            wp_set_post_terms($post_id, $terms, 'hcg-product-categories');
         }
         
         // get additional fields and process them
         foreach($this->hcgproducts_type->_fields as $api_name => $field)
         {
            $field_value = _hcgproducts_entities_to_ascii($product->{$api_name});
            if ($field_value)
            {
               update_post_meta($post_id, $field['id'], $field_value);
            }
         }
      }
   }

   // ------------------------------------------------------------

   /**
    * Set a Result Message
    *
    * @access public
    * @return void
   **/
   public function set_result_msg($msg, $type)
   {
      if ($type == 'error')
      {
         $this->result_msg[] = '<div class="error">'.$msg.'</div>';
      }
      elseif ($type == 'warning')
      {
         $this->result_msg[] = '<div class="warning">'.$msg.'</div>';
      }
      else
      {
         $this->result_msg[] = '<div class="notice">'.$msg.'</div>';
      }
   }
	
  // ------------------------------------------------------------

   /**
    * Log a debug message
    *
    * @access public
    * @return void
   **/
   public function debug_log($msg)
   {
      if ($this->debug_log_enabled)
      {
         array_push($this->debug_log, date("Y-m-d H:i:s") . " " . $msg);
      }
   }
	
   // ------------------------------------------------------------

   /**
    * Save the error log if it's enabled.  Must be called before server code exits to preserve
    * any log messages recorded during session.
    *
    * @access public
    * @return void
   **/
   public function save_debug_log()
   {
      if ($this->debug_log_enabled)
      {
         $options = get_option('hcgproducts_plugin_settings');
         $options['debug_log'] = $this->debug_log;
         update_option('hcgproducts_plugin_settings', $options);
      }
   }
	
   // ------------------------------------------------------------

   /**
    * Log errors to server log and debug log
    *
    * @access public
    * @return void
   **/
   public function error_log($msg)
   {
      error_log(HCGPRODUCT_PLUGIN_NAME . ": " . $msg);
      $this->debug_log($msg);
   }

}  // End Class hcgStores_options