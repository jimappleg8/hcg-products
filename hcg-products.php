<?php
/**
 * @package hcgProducts
 * @author Jim Applegate <jim.applegate@hain.com>
 * @version 1.0
 */
/*
Plugin Name: HCG Products
Plugin URI: http://www.hcgweb.net/
Description: HCG Products
Version: 0.1beta
Author: hcgWeb with help from Namith Jawahar
*/

// =======================================
// = Define constants used by the plugin =
// =======================================

if ( ! defined('HCGPRODUCTS_THEME_DIR'))
   define('HCGPRODUCTS_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());

if ( ! defined('HCGPRODUCTS_PLUGIN_NAME'))
   define('HCGPRODUCTS_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if ( ! defined('HCGPRODUCTS_PLUGIN_DIR'))
   define('HCGPRODUCTS_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . HCGPRODUCTS_PLUGIN_NAME);

if ( ! defined('HCGPRODUCTS_PLUGIN_URL'))
   define('HCGPRODUCTS_PLUGIN_URL', WP_PLUGIN_URL . '/' . HCGPRODUCTS_PLUGIN_NAME);

if ( ! defined('HCGPRODUCTS_VERSION_KEY'))
   define('HCGPRODUCTS_VERSION_KEY', 'hcgproducts_version');

if ( ! defined('HCGPRODUCTS_VERSION_NUM'))
   define('HCGPRODUCTS_VERSION_NUM', '0.1beta');

if ( ! defined('HCGPRODUCTS_DB_VERSION_NUM'))
   define('HCGPRODUCTS_DB_VERSION_NUM', '1.0');

add_option(HCGPRODUCTS_VERSION_KEY, HCGPRODUCTS_VERSION_NUM);

// The version constants above allow me to check the version in future
// upgrades and act accordingly. Sample code is below for when I need it.
// http://wp.smashingmagazine.com/2011/03/08/ten-things-every-wordpress-plugin-developer-should-know/
/*
$new_version = '2.0';

if (get_option(HCGPRODUCTS_VERSION_KEY) != $new_version)
{
   // Execute your upgrade logic here

   // Then update the version value
   update_option(HCGPRODUCTS_VERSION_KEY, $new_version);
}
*/

// ================================
// = Include libries and handlers =
// ================================

// Include admin portion of plugin
if ((include_once HCGPRODUCTS_PLUGIN_DIR . '/admin/admin.php') == FALSE)
{
   hcgproducts_error_log("Unable to load admin/admin.php");
   return;
}

// Include Custom Post Type portion of plugin
if ((include_once HCGPRODUCTS_PLUGIN_DIR . '/includes/hcgproducts.type.php') == FALSE)
{
   hcgproducts_error_log("Unable to load includes/hcgproducts.type.php");
   return;
}

// =================================
// = Define the hcgProducts_plugin class =
// =================================

class hcgProducts_plugin {

   /**
    * Instansiate Custom Post Type class
    *
    * @var string
    * @access private
   **/
   var $hcgproducts_type;

   /**
    * Instansiate option class - provides access to plugin options and debug logging
    *
    * @var string
    * @access private
   **/
   var $hcgproducts_admin;

   // ------------------------------------------------------------

   public function __construct()
   {
      // Create the Custom Post Type
      $this->hcgproducts_type = new hcgProducts_type();

      // Retrieve plugin options
      $this->hcgproducts_admin = new hcgProducts_options();
      $this->hcgproducts_admin->hcgproducts_type = $this->hcgproducts_type;
      
      // register style sheets
      add_action('wp_enqueue_scripts', array(&$this, 'register_styles'), 999);
      
      // Checks if the database needs to be updated
      add_action('plugins_loaded', array(&$this, 'update_db_check'));
      
      // Removes the New Product menu item from admin menu
      add_action('admin_menu', array(&$this, 'remove_submenu_links'), 999);
      
      // Removes "Products" from the +New menu at the top of the admin
      add_action('wp_before_admin_bar_render', array(&$this, 'remove_admin_bar_links'));
      
      // Hides the "Add New" buttons in the admin headings
      add_action('admin_head', array(&$this, 'remove_admin_head_links'));
      
      // Adds a settings link to the Plugins install page
      add_filter('plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2);
      
      // includes the default templates if needed
      add_filter('template_include', array(&$this, 'set_template'));

   }

   // ------------------------------------------------------------

   /**
    * Activate the plugin
    * 
    * Reference: http://codex.wordpress.org/Creating_Tables_with_Plugins
    */
   public static function activate()
   {
      global $wpdb;
      
      // ----------------------------------------------
      // install the hcgproducts_categories table
      // ----------------------------------------------

      $table_name = $wpdb->prefix . "hcgproducts_category";
      
      /*
       * We'll set the default character set and collation for this table.
       * If we don't do this, some characters could end up being converted 
       * to just ?'s when saved in our table.
       */
      $charset_collate = '';

      if ( ! empty( $wpdb->charset ) ) {
         $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
      }

      if ( ! empty( $wpdb->collate ) ) {
         $charset_collate .= " COLLATE {$wpdb->collate}";
      }

      $sql = 'CREATE TABLE '.$table_name.' ('.
             'term_id bigint(20) UNSIGNED NOT NULL, '.
             'category_id int(11) UNSIGNED NOT NULL, '.
             'UNIQUE KEY term_id (term_id) '.
             ') '.$charset_collate.';';

      require_once(ABSPATH.'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      // ----------------------------------------------
      // create default options
      // ----------------------------------------------

      add_option('hcgproducts_alias_category', '1');
      add_option('hcgproducts_db_version', HCGPRODUCTS_DB_VERSION_NUM);
      add_option('hcgproducts_restrict_to_categories', '');
      add_option('hcgproducts_templates', '1');
      add_option('hcgproducts_use_groups', '1');
      add_option('hcgproducts_version', HCGPRODUCTS_VERSION_NUM);
      
      // ----------------------------------------------
      // copy template files into the current theme directory
      // ----------------------------------------------

      $template_path = HCGPRODUCTS_PLUGIN_DIR.'/templates';
      $theme_path = get_template_directory().'/hcg-products';
      
      if ( ! file_exists($theme_path))
      {
         mkdir($theme_path, 0777, true);
      }
      
      hcgProducts_plugin::copy_templates($template_path, $theme_path);

      // ----------------------------------------------
      // create a page that will act as a taxonomy index
      // ----------------------------------------------

      $mypost = array(
         'post_content'   => '',
         'post_name'      => 'product-categories',
         'post_title'     => 'Products',
         'post_status'    => 'publish',
         'post_type'      => 'page',
         'page_template'  => 'hcg-products/index-hcg-product-categories.php'
      );

      $pageid = wp_insert_post($mypost, TRUE);

      if (is_wp_error($pageid))
      {
         $error_string = $pageid->get_error_message();
         echo "Error creating the category index page : " . $error_string;
         die;
      }
      
   }

   // ------------------------------------------------------------

   /**
    * Deactivate the plugin
    */     
   public static function deactivate()
   {
      global $wpdb;
      
      // ----------------------------------------------
      // drop the hcgproducts_categories table
      // ----------------------------------------------
      
      $wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'hcgproducts_category');

      // ----------------------------------------------
      // remove options
      // ----------------------------------------------

      delete_option('hcgproducts_alias_category');
      delete_option('hcgproducts_db_version');
      delete_option('hcgproducts_restrict_to_categories');
      delete_option('hcgproducts_templates');
      delete_option('hcgproducts_use_groups');
      delete_option('hcgproducts_version');
      delete_option('hcg-product-categories_children');

      // ----------------------------------------------
      // leave the template files in the theme directory
      // ----------------------------------------------

      // ----------------------------------------------
      // TODO: remove taxonomy index page
      // ----------------------------------------------

      // ----------------------------------------------
      // TODO: delete products and categories from the database
      // ----------------------------------------------

      // I might want to add code to delete the hcg-product entries
      //  and hcg-product-categories entries in the database.
      //  This is tricker than it seems:
      //  https://wordpress.org/support/topic/deleting-post-revisions-do-not-use-the-abc-join-code-you-see-everywhere
      
//      wp_delete_post( $postid, $force_delete );
      
   }

   // ------------------------------------------------------------

   /**
    * Checks, each time the plugin loads, whether the database used by 
    *  this plugin needs to updated.
    * 
    * Reference: http://codex.wordpress.org/Creating_Tables_with_Plugins
    */     
   public function update_db_check()
   {
      global $jal_db_version;
      if (get_site_option('hcgproducts_db_version') != HCGPRODUCTS_DB_VERSION_NUM)
      {
         $this->activate();
      }
   }

   // ------------------------------------------------------------

   /**
    * Registers the needed CSS styles with WordPress
    *
    */
   public function register_styles()
   {
      wp_register_style('hcgproducts-styles', HCGPRODUCTS_PLUGIN_URL.'/css/hcgproducts.css');
      wp_enqueue_style('hcgproducts-styles');
   }

   // ------------------------------------------------------------

   /**
    * Adds a settings link to the Plugins install page
    */
   public function plugin_action_links($links, $file)
   {
      static $this_plugin;

      if ( ! $this_plugin)
      {
         $this_plugin = plugin_basename(__FILE__);
      }

      if ($file == $this_plugin)
      {
         $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=hcgproducts-options">Settings</a>';
         array_unshift($links, $settings_link);
      }

      return $links;
   }

   // ------------------------------------------------------------

   /**
    * Removes the Add New Product menu from admin
    */
   public function remove_submenu_links()
   {
      $page = remove_submenu_page('edit.php?post_type=hcg-products', 'post-new.php?post_type=hcg-products');
   }

   // ------------------------------------------------------------

   /**
    * Removes "Products" from the +New menu at the top of the admin
    */
   public function remove_admin_bar_links()
   {
      global $wp_admin_bar;
      $wp_admin_bar->remove_menu('new-hcg-products');
   }

   // ------------------------------------------------------------

   /**
    * Hides the "Add New" buttons in the admin headings
    */
   public function remove_admin_head_links()
   {
      if (get_post_type() == 'hcg-products')
      {
         echo '<style type="text/css">
  #favorite-actions {display: none;}
  .add-new-h2 {display: none;}
  .tablenav {display: none;}
</style>';
      }
   }

   // ------------------------------------------------------------

   /**
    * Checks if a template is available in the theme and uses the default
    *  if it is not.
    *
    */   
   public function set_template($template)
   {
      // has template handling been turned off?
      if (get_option('hcgproducts_templates', '1') != '1')
      {
         return $template;
      }
      
      $theme_path = get_template_directory().'/hcg-products';
      
      if (is_singular('hcg-products') && ! $this->_is_product_template($template, 'single'))
      {
         $template = $theme_path . '/single-hcg-products.php';
      }

      if (is_tax('hcg-product-categories') && ! $this->_is_product_template($template, 'hcg-product-categories'))
      {
         $template = $theme_path . '/taxonomy-hcg-product-categories.php';
      }
      
      if (is_page('product-categories') && ! $this->_is_product_template($template, 'index'))
      {
         $template = $theme_path . '/index-hcg-product-categories.php';
      }

      return $template;
   }

   // ------------------------------------------------------------

   /**
    * Checks to see if the template that WordPress has picked from the 
    *  theme/child-theme is one of ours. If it is, then that means that
    *  Wordpress found an override template and we should let it use it.
    *
    */   
   private function _is_product_template($template_path, $context = '')
   {
      // Get template name
      $template = basename($template_path);

      switch ($context)
      {
         case 'single':
            return $template == 'single-hcg-products.php';
      
         // Check if template is taxonomy-hcg-product-categories.php
         // Check if template is taxonomy-hcg-product-categories-{term-slug}.php
         case 'hcg-product-categories':
            return (1 == preg_match('/^taxonomy-hcg-product-categories((-(\S*))?).php/', $template));

         case 'index':
            return $template == 'index-hcg-product-categories.php';
      }

      return FALSE;
   }

   // ------------------------------------------------------------

   /**
    * Copies the builtin template files to the active WordPress theme
    *
    * Handles copying the builting template files to the hcg-products/ 
    * directory of the currently active WordPress theme.  Strips out the 
    * header comment block which includes a warning about editing the 
    * builtin templates.
    *
    * Copied from the Shopp Wordpress plugin: https://shopplugin.net/
    *
    * @author Jonathan Davis, John Dillick
    *
    * @param string $src The source directory for the builtin template files
    * @param string $target The target directory in the active theme
    * @return void
    */
   public function copy_templates($src, $target)
   {
      $builtin = array_filter(scandir($src), 'hcgProducts_plugin::filter_dotfiles');
      foreach ($builtin as $template)
      {
         $target_file = $target.'/'.$template;
         if ( ! file_exists($target_file))
         {
            $src_file = file_get_contents($src . '/' . $template);
            $file = fopen($target_file, 'w');
            $src_file = preg_replace('/^<\?php\s\/\*\*\s+(.*?\s)*?\*\*\/\s\?>\s/', '', $src_file); // strip warning comments

            fwrite($file, $src_file);
            fclose($file);
            chmod($target_file, 0666);
        }
      }
   }

   // ------------------------------------------------------------

   /**
    * Callback to filter out files beginning with a dot
    *
    * Copied from the Shopp Wordpress plugin: https://shopplugin.net/
    
    * @author Jonathan Davis
    *
    * @param string $name The filename to check
    * @return boolean
    */
   public static function filter_dotfiles($name)
   {
      return (substr($name,0,1) != ".");
   }


} /* end hcgProducts_plugin class */


// ------------------------------------------------------------

function hcgproducts_error_log($msg)
{
	global $hcgproducts_errors;

	if ( ! is_array( $hcgproducts_errors ) ) {
		add_action('admin_footer', 'hcgproducts_error_log_display');
		$hcgproducts_errors = array();
	}
	
	array_push($hcgproducts_errors, HGCPRODUCTS_PLUGIN_NAME . $msg);
}

// ------------------------------------------------------------

/**
 * Display errors logged when the plugin options module is not available.
 */
function hcgproducts_error_log_display()
{
	echo "<div class='error'><p><a href='options-media.php'>" . HGCPRODUCTS_PLUGIN_NAME 
		. "</a> unable to initialize correctly.  Error(s):<br />";
	foreach ($hcgproducts_errors as $line) {
		echo "$line<br/>\n";
	}
	echo "</p></div>";
}

// =========================
// = Plugin initialization =
// =========================

// Installation and uninstallation hooks
register_activation_hook(__FILE__, array('hcgProducts_plugin', 'activate'));
register_deactivation_hook(__FILE__, array('hcgProducts_plugin', 'deactivate'));

// Instantiate the plugin class
$hcg_products = new hcgProducts_plugin();

