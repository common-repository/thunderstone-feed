<?php

/**
*  Plugin Name: Thunderstone Feed
*  Plugin URI: https://www.thunderstone.io/solution/
*  Description: XML feed creator for Thunderstone
*  Version: 1.0.0
*  Author: Thunderstone
*  Author URI: https://www.thunderstone.io/mention-legale/
*  License: GPLv3 or later
*/

if (!defined('ABSPATH'))
exit; // Exit if accessed directly

function thunderstonewpxml_admin_menu() {

  /* add new top level */
  add_menu_page(
    __('Thunderstone', 'thunderstonewpwoocommerce-feed'), __('Thunderstone', 'thunderstonewpwoocommerce-feed'), 'manage_options', 'thunderstonewpxml_admin_menu', 'thunderstonewpxml_admin_page', plugins_url('/', __FILE__) . '/images/xml-icon.png'
  );

  /* add the submenus */
  add_submenu_page(
    'thunderstonewpxml_admin_menu', __('Créer un Flux', 'thunderstonewpwoocommerce-feed'), __('Créer un Flux', 'thunderstonewpwoocommerce-feed'), 'manage_options', 'thunderstonewpxml_create_page', 'thunderstonewpxml_create_page'
  );
}

add_action( 'plugins_loaded', 'thunderstonewpxml_get_user_info' );

function thunderstonewpxml_get_user_info(){
  $current_user = wp_get_current_user();

  if ( !($current_user instanceof WP_User) )
    return;

  if (current_user_can('administrator')) {
    add_action('admin_menu', 'thunderstonewpxml_admin_menu');
    add_action('admin_init', 'thunderstonewpxml_register_mysettings');
  }
}

function thunderstonewpxml_admin_page() {

  add_action('wp', 'thunderstonewpxml_setup_schedule');
  $skicon = plugins_url('/', __FILE__) . '/images/app_icon_th.png';
  echo '<div><img src="' . $skicon . '" height="150px">';

  echo '<h2>' . __('Créer des flux pour Thunderstone', 'thunderstonewpwoocommerce-feed') . '</h2>';
  echo '</div>';

  global $woocommerce;
  $attribute_taxonomies = wc_get_attribute_taxonomies();

  echo '<form method="post" action="options.php">';
  settings_fields('th-group');
  do_settings_sections('th-group');
  echo '<table class="form-table">
  <tr valign="top">
  <th scope="row">' . __('Disponibilité en stock', 'thunderstonewpwoocommerce-feed') . '</th><td>';

  $options = get_option('instockavailability');
  $items = array(
    __('Livraison immédiate / Livraison 1 à 3 jours', 'thunderstonewpwoocommerce-feed'),
    __('Livraison en 1 à 3 jours', 'thunderstonewpwoocommerce-feed'),
    __('Livraison sous 4 à 10 jours', 'thunderstonewpwoocommerce-feed'),
    __('Attribut: Disponibilité', 'thunderstonewpwoocommerce-feed'),
    __('Custom', 'thunderstonewpwoocommerce-feed')
  );
  echo "<select id='drop_down1' name='instockavailability'>";
  foreach ($items as $key => $item) {
    $selected = ($options['instockavailability'] == $key) ? 'selected="selected"' : '';
    echo "<option value='" . esc_html($key) . "' $selected>" . esc_html($item) . "</option>";
  }
  echo "</select>";
  echo " <em>" . __('Sélectionner un attribut: Disponibilité uniquement si vous avez ajouté un attribut de produit nommé "Disponibilité"', 'thunderstonewpwoocommerce-feed') . "</em>";
  echo '</td>
  </tr>
  </tr>

  <tr valign="top">
    <th scope="row">' . __('Si un produit est en rupture de stock', 'thunderstonewpwoocommerce-feed') . '</th>
  <td>';

  $options2 = get_option('ifoutofstock');
  $items = array(__('Inclure en rupture de stock ou sur demande', 'thunderstonewpwoocommerce-feed'), __('Exclure du flux', 'thunderstonewpwoocommerce-feed'));
  echo "<select id='drop_down2' name='ifoutofstock'>";
  foreach ($items as $key => $item) {
    $selected = ($options2['ifoutofstock'] == $key) ? 'selected="selected"' : '';
    echo "<option value='" . esc_html($key) . "' $selected>" . esc_html($item) . "</option>";
  }
  echo "</select>";
  echo '</td>        </tr>   ';
  //echo '  </tr>';
  echo "<tr>";
  echo '<th scope="row">' . __('Attributs Thunderstone', 'thunderstonewpwoocommerce-feed') . '</th>';
  echo "<td>";

  $attribute_terms = array();
  foreach ($attribute_taxonomies as $tax) {
    $term = wc_attribute_taxonomy_name($tax->attribute_name);
    $attribute_terms[$tax->attribute_id] = '';
    if (taxonomy_exists($term)) {
      $attribute_terms[$tax->attribute_id] = $term;
    }
  }

  $thunderstonewpatts_color = get_option('thunderstonewpatts_color', 'pa_color');
  $thunderstonewpatts_size = get_option('thunderstonewpatts_size', 'pa_size');
  $thunderstonewpatts_manuf = get_option('thunderstonewpatts_manuf', 'pa_brand');

  echo '<label>' . __('Taille', 'thunderstonewpwoocommerce-feed') . ': <select name="thunderstonewpatts_size[]" multiple="true">';

  foreach ($attribute_taxonomies as $tax) {
    $selected = false;
    if ($thunderstonewpatts_size == $attribute_terms[$tax->attribute_id]) {
      $selected = true;
    }

    echo "<option value='" . esc_html($attribute_terms[$tax->attribute_id]) . "' " . selected($selected, true, false) . ">" . esc_html($tax->attribute_label) . "</option>";
  }
  echo '</select></label>&nbsp;&nbsp;';
  echo '<br /><br /><label>' . __('Couleur', 'thunderstonewpwoocommerce-feed') . ': <select name="thunderstonewpatts_color">';
  foreach ($attribute_taxonomies as $tax) {
    $selected = false;
    if ($thunderstonewpatts_color == $attribute_terms[$tax->attribute_id]) {
      $selected = true;
    }

    echo "<option value='" . esc_html($attribute_terms[$tax->attribute_id]) . "' " . selected($selected, true, false) . ">" . esc_html($tax->attribute_label) . "</option>";
  }
  echo '</select></label>&nbsp;&nbsp;';
  echo '<br /><br /><label>' . __('Vendeur', 'thunderstonewpwoocommerce-feed') . ': <select name="thunderstonewpatts_manuf">';
  if ($thunderstonewpatts_manuf == '') {
    $selected = true;
  }
  echo "<option value='' " . selected($selected, true, false) . ">" . __('-Empty-', 'thunderstonewpwoocommerce-feed') . "</option>";
  foreach ($attribute_taxonomies as $tax) {
    $selected = false;
    if ($thunderstonewpatts_manuf == $attribute_terms[$tax->attribute_id]) {
      $selected = true;
    }

    echo "<option value='" . esc_html($attribute_terms[$tax->attribute_id]) . "' " . selected($selected, true, false) . ">" . esc_html($tax->attribute_label) . "</option>";
  }
  echo '</select></label>';
  echo "</td>";
  echo "</tr>";
  echo ' </table>';
  submit_button();
  echo '</form></div>';
}

function thunderstonewpxml_register_mysettings() { // whitelist options
  register_setting('th-group', 'instockavailability', 'thunderstonewpxml_sanitize_options');
  register_setting('th-group', 'ifoutofstock', 'thunderstonewpxml_sanitize_options');
  register_setting('th-group', 'thunderstonewpatts_color', 'thunderstonewpxml_sanitize_options');
  register_setting('th-group', 'thunderstonewpatts_manuf', 'thunderstonewpxml_sanitize_options');
  register_setting('th-group', 'thunderstonewpatts_size', 'thunderstonewpxml_sanitize_options_multi');
}

function thunderstonewpxml_sanitize_options($input) {

  return esc_html($input);
}

function thunderstonewpxml_sanitize_options_multi($input) {

  $output = array();

  foreach ($input as $in_value) {
    $output[] = esc_html($in_value);
  }

  return implode(",",$output);
}

function thunderstonewpxml_create_page() {

  $skicon = plugins_url('/', __FILE__) . '/images/app_icon_th.png';
  echo '<div><img src="' . $skicon . '" height="150px">';
  echo '<h2>' . __('Créer des flux pour Thunderstone', 'thunderstonewpwoocommerce-feed') . '</h2>';
  echo '</div>';

  settings_fields('th-group');
  do_settings_sections('th-group');

  $active = 0; // get_option('activefeeds');
  if ($active == 0 | $active == 1) {
    require_once 'createsk.php';
  }
  echo '</br>';
  if (!wp_next_scheduled('thunderstonewpxml_hourly_event')) {
    wp_schedule_event(time(), 'hourly', 'thunderstonewpxml_hourly_event');
  }
}

add_action('thunderstonewpxml_hourly_event', 'thunderstonewpxml_do_this_hourly');

/**
* On the scheduled action hook, run a function.
*/
function thunderstonewpxml_do_this_hourly() {
  // do something every hour


  $active = 0; // get_option('activefeeds');
  if ($active == 0 | $active == 1) {
    require_once 'createsk.php';
  }
  if ($active == 0 | $active == 2) {
    require_once 'createbp.php';
  }

  if (!wp_next_scheduled('thunderstonewpxml_hourly_event')) {
    wp_schedule_event(time(), 'hourly', 'thunderstonewpxml_hourly_event');
  }
}

function thunderstonewpxml_generate_products_xml_data() {
  $xml_rows = array();
  $instockavailability = get_option('instockavailability');
  $avaibilities = array("Livraison immédiate / Livraison 1 à 3 jours", "Livraison en 1 à 3 jours", "'Livraison sous 4 à 10 jours", "attribute");
  $availabilityST = $avaibilities[$instockavailability];
  $ifoutofstock = get_option('ifoutofstock');
  $format_price = false;
  if (function_exists('wc_get_price_decimal_separator') && function_exists('wc_get_price_thousand_separator') && function_exists('wc_get_price_decimals')) {
    $decimal_separator = wc_get_price_decimal_separator();
    $thousand_separator = wc_get_price_thousand_separator();
    $decimals = wc_get_price_decimals();
    $format_price = true;
  }
  $result = wc_get_products(array('status' => array('publish'), 'limit' => -1));
  foreach ($result as $index => $prod) {

    $attributes = $prod->get_attributes();
    $stockstatus_ds = $prod->get_stock_status();
    if ((strcmp($stockstatus_ds, "outofstock") == 0) & ($ifoutofstock == 1)) {
      continue;
    }
    $onfeed = $prod->get_meta('onfeed');
    if (strcmp($onfeed, "no") == 0) {
      continue;
    }
    $xml_rows[$prod->get_id()] = array(
      'onfeed' => $onfeed,
      'stockstatus' => $stockstatus_ds,
      'attributes' => $attributes
    );

    switch ($instockavailability) {
      case 3:
      //_product_attributes
      $_product_attributes_ser_ds = $attributes;
      if (is_serialized($_product_attributes_ser_ds)) {
        $_product_attributes = unserialize($_product_attributes_ser_ds);
        foreach ($_product_attributes as $key => $attr) {
          if ($attr['name'] == 'Διαθεσιμότητα') {
            $availabilityST = $attr['value'];
            break;
          }
        }
      }
      break;
      case 4:
      //_product_attributes
      $tmp_availability = $prod->get_meta('_custom_availability');
      if ($tmp_availability != '') {
        $availabilityST = $tmp_availability;
      }
      break;
      default:
      break;
    }
    $xml_rows[$prod->get_id()]['availabilityST'] = $availabilityST;
    $price = $prod->get_price();
    $xml_rows[$prod->get_id()]['price_raw'] = $price;
    $xml_rows[$prod->get_id()]['price'] = addslashes($price);
    $image_ds = get_the_post_thumbnail_url($prod->get_id(), 'shop_catalog');
    $xml_rows[$prod->get_id()]['image_ds'] = $image_ds;
    $skus_ds = $prod->get_sku();
    $xml_rows[$prod->get_id()]['skus_ds'] = $skus_ds;
    $categories_ds = $prod->get_category_ids();
    $_weight_ds = $prod->get_weight();
    $xml_rows[$prod->get_id()]['_weight_ds'] = $_weight_ds;
    $thunderstonewpatts_color = get_option('thunderstonewpatts_color', 'pa_color');
    $thunderstonewpatts_size = get_option('thunderstonewpatts_size', 'pa_size');
    $thunderstonewpatts_manuf = get_option('thunderstonewpatts_manuf', 'pa_brand');
    $sizestring = '';
    $sizesArr = [];
    $multiSizes = explode(',', $thunderstonewpatts_size);
    $xml_rows[$prod->get_id()]['sizes'] = array();
    foreach ($multiSizes as $multiSize) {
      if (isset($attributes[$multiSize]) && $attributes[$multiSize] != null) {
        $sizes = $attributes[$multiSize]->get_terms();
        foreach ($sizes as $i => $size_term) {
          $sizestring .= thunderstonewpformat_number($size_term->name) . ', ';
          $xml_rows[$prod->get_id()]['sizes'][] = thunderstonewpformat_number($size_term->name);
        }
      }
    }
    if (strlen($sizestring) > 2) {
      $sizestring = substr($sizestring, 0, -2);
    }
    $xml_rows[$prod->get_id()]['sizestring'] = $sizestring;
    $xml_rows[$prod->get_id()]['sizesarr'] = $xml_rows[$prod->get_id()]['sizes'];

    $man = '';
    if (isset($attributes[$thunderstonewpatts_manuf]) && $attributes[$thunderstonewpatts_manuf] != null) {
      $brands = $attributes[$thunderstonewpatts_manuf]->get_terms();
      foreach ($brands as $brand_term) {
        $man = $brand_term->name;
      }
    }
    $xml_rows[$prod->get_id()]['manufacturer'] = $man;
    $colorRes = '';
    $xml_rows[$prod->get_id()]['colors'] = array();
    if (isset($attributes[$thunderstonewpatts_color]) && $attributes[$thunderstonewpatts_color] != null) {
      $colors = $attributes[$thunderstonewpatts_color]->get_terms();
      foreach ($colors as $color_term) {
        $colorRes .= $color_term->name . ', ';
        $xml_rows[$prod->get_id()]['colors'][] = $color_term->name;
      }
    }
    if (strlen($colorRes) > 2) {
      $colorRes = substr($colorRes, 0, -2);
    }
    $xml_rows[$prod->get_id()]['colorstring'] = $colorRes;
    $xml_rows[$prod->get_id()]['colorsarr'] = $xml_rows[$prod->get_id()]['colors'];

    $xml_rows[$prod->get_id()]['terms'] = array();
    foreach ($attributes as $att_key => $prod_att) {
      $xml_rows[$prod->get_id()]['terms'][$att_key] = array();
      $prod_terms = $prod_att->get_terms();
      foreach ($prod_terms as $the_term) {
        $xml_rows[$prod->get_id()]['terms'][$att_key][] = $the_term->name;
      }
    }
    $prod_category_tree = array_map('get_term', array_reverse(wc_get_product_cat_ids($prod->get_id())));
    $xml_rows[$prod->get_id()]['categories'] = array();
    $category_path = '';
    for ($i = 0; $i < count($prod_category_tree); $i++) {
      if ($i == 0) {
        $xml_rows[$prod->get_id()]['category_id'] = $prod_category_tree[$i]->term_id;
      }
      $category_path.=$prod_category_tree[$i]->name;
      $xml_rows[$prod->get_id()]['categories'][] = $prod_category_tree[$i]->name;
      if ($i < count($prod_category_tree) - 1)
      $category_path.=', ';
    }
    $xml_rows[$prod->get_id()]['category_path'] = $category_path;
    $title = str_replace("'", " ", $prod->get_title());
    $title = str_replace("&", "+", $title);
    $title = strip_tags($title);
    $xml_rows[$prod->get_id()]['title'] = $title;
    $backorder = $prod->get_backorders();
    $xml_rows[$prod->get_id()]['backorder'] = $backorder;
    $xml_rows[$prod->get_id()]['descr'] = $prod->get_description();
  }
  return $xml_rows;
}
