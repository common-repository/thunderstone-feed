<?php

if (!defined('ABSPATH'))
exit; // Exit if accessed directly

if (current_user_can('administrator')) {

  require_once( 'simplexml.php' );

  if (!file_exists(wp_upload_dir()['basedir'] . '/thunderstone')) {
    wp_mkdir_p(wp_upload_dir()['basedir'] . '/thunderstone');
  }

  if (!file_exists(wp_upload_dir()['basedir'] . '/thunderstone/thunderstone.xml')) {
    touch(wp_upload_dir()['basedir'] . '/thunderstone/thunderstone.xml');
  }

  if (file_exists(wp_upload_dir()['basedir'] . '/thunderstone/thunderstone.xml')) {
    $xmlFile = wp_upload_dir()['basedir'] . '/thunderstone/thunderstone.xml';
  } else {
    echo "Could not create file.";
  }

  $xml = new thunderstonewpfeed_XMLExtended('<?xml version="1.0" encoding="utf-8"?><rss version="2.0"/>');
  $now = date('Y-n-j G:i');
  $products = $xml->addChild('channel');
  $products->addChild('title', get_bloginfo('name'));
  $products->addChild('generator', "wordpress");
  $products->addChild('language', "fr");

  $xml_rows = thunderstonewpxml_generate_products_xml_data();
  foreach($xml_rows as $prod_id=>$row){
    if (sizeof($row['sizesarr']) > 0) {
      foreach($row['sizesarr'] as $size){
        if (sizeof($row['colorsarr']) > 0) {
          foreach($row['colorsarr'] as $color){
            $product = $products->addChild('item');

            $product->sku = NULL;
            $product->sku->addCData(strtolower(str_replace(' ', '_', $row['title'])."_".str_replace(' ', '_', $size)."_".str_replace(' ', '_', $color)));

            // $product->addChild('uid', $prod_id);
            $product->title = NULL;
            $product->title->addCData($row['title']);
            $product->link = NULL;
            $product->link->addCData(get_permalink($prod_id));

            $product->image_1 = NULL;
            $product->image_1->addCData($row['image_ds']);

            $product->category = NULL;
            $product->category->addCData($row['category_path']);


            //$product->addChild('category_id', $cat_id);
            $product->addChild('price', $row['price']);

            $product->reference = NULL;
            $product->reference->addCData(trim($row['skus_ds']));
            $product->addChild('discount', '');
            $product->addChild('currency', 'euro');

            $product->description = NULL;
            $product->description->addCData($row['descr']);

            if (strcmp($row['stockstatus'], "instock") == 0) {
              $product->addChild('stock', '1');
            } else {

              if (strcmp($row['backorder'], "notify") == 0) {
                $product->addChild('stock', '1');
              } else if (strcmp($row['backorder'], "yes") == 0) {
                $product->addChild('stock', '1');
              } else {
                $product->addChild('stock', '0');
              }
            }

            $product->addChild('size', $size);

            $product->brand = NULL;
            $product->brand->addCData($row['manufacturer'] == "" ? get_bloginfo( 'name' ) : $row['manufacturer']);

            $product->color = NULL;
            $product->color->addCData($color);
          }
        } else {
          $product = $products->addChild('item');

          $product->sku = NULL;
          $product->sku->addCData(strtolower(str_replace(' ', '_', $row['title'])."_".str_replace(' ', '_', $size)));

          // $product->addChild('uid', $prod_id);
          $product->title = NULL;
          $product->title->addCData($row['title']);
          $product->link = NULL;
          $product->link->addCData(get_permalink($prod_id));

          $product->image_1 = NULL;
          $product->image_1->addCData($row['image_ds']);

          $product->category = NULL;
          $product->category->addCData($row['category_path']);


          //$product->addChild('category_id', $cat_id);
          $product->addChild('price', $row['price']);
          $product->reference = NULL;
          $product->reference->addCData(trim($row['skus_ds']));
          $product->addChild('discount', '');
          $product->addChild('currency', 'euro');

          $product->description = NULL;
          $product->description->addCData($row['descr']);

          if (strcmp($row['stockstatus'], "instock") == 0) {
            $product->addChild('stock', '1');
          } else {

            if (strcmp($row['backorder'], "notify") == 0) {
              $product->addChild('stock', '1');
            } else if (strcmp($row['backorder'], "yes") == 0) {
              $product->addChild('stock', '1');
            } else {
              $product->addChild('stock', '0');
            }
          }

          $product->addChild('size', $size);

          $product->brand = NULL;
          $product->brand->addCData($row['manufacturer'] == "" ? get_bloginfo( 'name' ) : $row['manufacturer']);

          $product->color = NULL;
        }
      }
    } else {
      $product = $products->addChild('item');

      $product->sku = NULL;
      $product->sku->addCData(trim($row['skus_ds']));

      // $product->addChild('uid', $prod_id);
      $product->title = NULL;
      $product->title->addCData($row['title']);
      $product->link = NULL;
      $product->link->addCData(get_permalink($prod_id));

      $product->image_1 = NULL;
      $product->image_1->addCData($row['image_ds']);

      $product->category = NULL;
      $product->category->addCData($row['category_path']);


      //$product->addChild('category_id', $cat_id);
      $product->addChild('price', $row['price']);
      $product->reference = NULL;
      $product->reference->addCData(trim($row['skus_ds']));
      $product->addChild('discount', '');
      $product->addChild('currency', 'euro');

      $product->description = NULL;
      $product->description->addCData($row['descr']);

      if (strcmp($row['stockstatus'], "instock") == 0) {
        $product->addChild('stock', '1');
      } else {

        if (strcmp($row['backorder'], "notify") == 0) {
          $product->addChild('stock', '1');
        } else if (strcmp($row['backorder'], "yes") == 0) {
          $product->addChild('stock', '1');
        } else {
          $product->addChild('stock', '0');
        }
      }

      $product->addChild('size', '');

      $product->brand = NULL;
      $product->brand->addCData($row['manufacturer'] == "" ? get_bloginfo( 'name' ) : $row['manufacturer']);

      $product->color = NULL;
    }
  }

  echo '</br>Thunderstone XML</br>';
  $xml->saveXML($xmlFile);
  echo 'Le fichier est disponible: <a href="' . wp_upload_dir()['baseurl'] . '/thunderstone/thunderstone.xml" target="_blank">' . wp_upload_dir()['baseurl'] . '/thunderstone/thunderstone.xml</a>';
}


function thunderstonewpformat_number($pa_size) {
  return str_replace(',', '.', $pa_size);
}

?>
