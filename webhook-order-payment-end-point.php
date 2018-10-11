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

  foreach ($json->line_items as $row) {
    // Add out of stock tag.
    $service = new Shopify\Service\ProductService($client);
    $product = $service->get($row->product_id);
    // Get variants.
    $variants = $product->variants;
    foreach ($variants as $variant) {
      // Get stock level.
      $inventory_quantity = $variant->inventory_quantity;
      if ($inventory_quantity <= 0) {
        // Add out of stock tag.
        $tags = trim($product->tags) . ', Out of stock';
        $product->tags = $tags;
        $service->update($product);
      }
    }
  }

  header('HTTP/1.0 200 OK');

  exit();
