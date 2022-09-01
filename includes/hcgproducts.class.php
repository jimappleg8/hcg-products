<?php
/**
 * HCG Products API Class
 * 
 * 
 * Copyright 2010 Jim Applegate
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * Author: Jim Applegate
 * Website: http://www.hcgweb.net/
 * Version: 0.1
 */

class HCGProducts {

   private $api_key;
   private $site_id;
   private $format = 'xml';


   # CHANGED: Added an internal api url property that can be overwritten in the
   #  constructor. -j
   private $api_url = 'http://api.hcgweb.net'; # No trailing slash

   
   // -----------------------------------------------------------------

   /**
    * Constructor
    */
   public function __construct($api_key = '', $site_id = '', $api_url = null)
   {
      if ($api_key != '')
      {
         $this->setApiKey($api_key);
      }
      if ($site_id != '')
      {
         $this->setSiteId($site_id);
      }
      if ($api_url)
      {
        $this->api_url = $api_url;
      }
   }

   // -----------------------------------------------------------------

   /**
    * Sets the API Key
    *
    * @param  string  the API key
    */
   public function setApiKey($api_key)
   {
      $this->api_key = $api_key;
      return TRUE;
   }

   // -----------------------------------------------------------------

   /**
    * Sets the Site ID
    *
    * @param  string  the Site ID
    */
   public function setSiteId($site_id)
   {
      $this->site_id = strtolower($site_id);
      return TRUE;
   }

   // -----------------------------------------------------------------

   /**
    * Sets the response format
    *
    * @param  string  the format (either 'xml' or 'json')
    */
   public function setFormat($format)
   {
      if ( ! in_array(strtolower($format), array('xml', 'json')))
      {
         throw new Exception('The supplied format is not allowed. Please specify either "xml" or "json".');
      }
         
      $this->format = strtolower($format);
      return TRUE;
   }

   // -----------------------------------------------------------------

   /**
    * Gets the specified product detail data
    *
    * @param    string  the type of product ID being supplied (id, code or upc)
    * @param    mixed   the product ID
    * @returns  string  (xml or json)
    */
   public function getProductDetail($id_type, $product_id)
   {
      if ($this->api_key == '' || $this->site_id == '')
      {
         throw new Exception('The API Key and/or Site ID have not been set.');
      }

      $url  = "{$this->api_url}/v1/products/productDetail/";
      $url .= $this->api_key.'/';
      $url .= $this->format.'/';
      $url .= $this->site_id.'/';
      $url .= strtolower($id_type).'/'.strtolower($product_id);

      return $this->_getRemoteData($url);
   }

   // -----------------------------------------------------------------

   /**
    * Gets the specified product NLEA data
    *
    * @param    string  the type of product ID being supplied (id, code or upc)
    * @param    mixed   the product ID
    * @returns  string  (xml or json)
    */
   public function getProductNlea($id_type, $product_id)
   {
      if ($this->api_key == '' || $this->site_id == '')
      {
         throw new Exception('The API Key and/or Site ID have not been set.');
      }

      $url  = "{$this->api_url}/v1/products/productNLEA/";
      $url .= $this->api_key.'/';
      $url .= $this->format.'/';
      $url .= $this->site_id.'/';
      $url .= strtolower($id_type).'/'.strtolower($product_id);

      return $this->_getRemoteData($url);
   }

   // -----------------------------------------------------------------

   /**
    * Gets the specified product list data
    *
    * @param    string  the type of category ID being supplied (id or code)
    * @param    mixed   the category ID
    * @returns  string  (xml or json)
    */
   public function getProductList($id_type, $category_id)
   {
      if ($this->api_key == '' || $this->site_id == '')
      {
         throw new Exception('The API Key and/or Site ID have not been set.');
      }

      $url  = "{$this->api_url}/v1/products/productList/";
      $url .= $this->api_key.'/';
      $url .= $this->format.'/';
      $url .= $this->site_id.'/';
      $url .= strtolower($id_type).'/'.strtolower($category_id);

      return $this->_getRemoteData($url);
   }

   // -----------------------------------------------------------------

   /**
    * Gets the specified categoryDetail data
    *
    * @param    string  the type of category ID being supplied (id or code)
    * @param    mixed   the category ID
    * @returns  string  (xml or json)
    */
   public function getCategoryDetail($id_type, $category_id)
   {
      if ($this->api_key == '' || $this->site_id == '')
      {
         throw new Exception('The API Key and/or Site ID have not been set.');
      }

      $url  = "{$this->api_url}/v1/products/categoryDetail/";
      $url .= $this->api_key.'/';
      $url .= $this->format.'/';
      $url .= $this->site_id.'/';
      $url .= strtolower($id_type).'/'.strtolower($category_id);

      return $this->_getRemoteData($url);
   }

   // -----------------------------------------------------------------

   /**
    * Gets the specified categoryList data
    *
    * @returns  string  (xml or json)
    */
   public function getCategoryList()
   {
      if ($this->api_key == '' || $this->site_id == '')
      {
         throw new Exception('The API Key and/or Site ID have not been set.');
      }

      $url  = "{$this->api_url}/v1/products/categoryList/";
      $url .= $this->api_key.'/';
      $url .= $this->format.'/';
      $url .= $this->site_id.'/';

      return $this->_getRemoteData($url);
   }

   // -----------------------------------------------------------------

   /**
    * Gets the remote data using cURL and the supplied URL
    *
    * This function could easily be modified to use the PEAR HTTP_Request
    *  library instead of cURL to access the remote data.
    *
    * @param    string  the url
    * @returns  string  (xml or json)
    */
   private function _getRemoteData($url)
   {
      $ch = curl_init();
      $timeout = 5;
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
      $data = curl_exec($ch);
      curl_close($ch);

      return $data;
   }

}


// end of HCGProducts class