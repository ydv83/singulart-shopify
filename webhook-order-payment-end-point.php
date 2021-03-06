<?php

  $acces_key = $_GET['access_key'];

  if ($acces_key != 'qwerty') {
    header('HTTP/1.0 403 Forbidden');
    exit();
  }

  require 'vendor/autoload.php';

  use Shopify\Enum\Fields\CollectFields;
  use Shopify\Enum\Fields\CustomCollectionFields;
  use Shopify\Enum\Fields\SmartCollectionFields;
  use Shopify\Enum\Fields\ProductFields;
  use Shopify\Enum\Fields\ProductVariantFields;

  $client = new Shopify\PrivateApi([
    'api_key' => '256dfdf2e9ce2e9088b806459307b656',
    'password' => 'dcb157469977d0b248c2f8a7972599ce',
    'shared_secret' => 'f98aab1448a877cb5ffa8120b7c828be',
    'myshopify_domain' => 'singulart.myshopify.com',
  ]);

  // Get webhook products.
  $webhook_content = '';
  $webhook = fopen('php://input' , 'rb');
  while (!feof($webhook)) {
    $webhook_content .= fread($webhook, 4096);
  }
  fclose($webhook);
  $json = json_decode($webhook_content, true);

  //Get settings.
  $settings = file_get_contents('settings.txt');
  $settings = explode(PHP_EOL, $settings);
  if (!empty($settings)) {
    foreach ($settings as $v) {
      $collections[$v] = $v;
    }
  }
  // Get products in acollections.
  if (!empty($collections)) {
    // Get products with collection.
    $service = new Shopify\Service\CollectService($client);
    $collects = $service->all();
    foreach ($collects as $collect) {
      $collectId = $collect->collection_id;
      if (isset($collections[$collectId])) {
        $products[$collect->product_id] = $collect->product_id;
      }
    }
  }

  $service = new Shopify\Service\ProductService($client);

  foreach ($json->line_items as $row) {
    if (!empty($products[$row->product_id])) {
      // Add out of stock tag.
      $product = $service->get($row->product_id);
      // Get variants.
      $variants = $product->variants;
      $update = FALSE;
      foreach ($variants as $variant) {
        // Get stock level.
        $inventory_quantity = $variant->inventory_quantity;
        if ($inventory_quantity <= 0) {
          $update = TRUE;
        }
      }
      if ($update) {
        // Add out of stock tag.
        $tags = trim($product->tags) . ', Out of stock';
        $product->tags = $tags;
        $service->update($product);
      }
    }
  }

  header('HTTP/1.0 200 OK');

  exit();
