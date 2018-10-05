<?php

  require 'vendor/autoload.php';

  use Shopify\Enum\Fields\CollectFields;
  use Shopify\Enum\Fields\CustomCollectionFields;
  use Shopify\Enum\Fields\SmartCollectionFields;
  use Shopify\Enum\Fields\ProductFields;
  use Shopify\Enum\Fields\ProductVariantFields;

  $formCollection = [];

  $client = new Shopify\PrivateApi([
    'api_key' => '256dfdf2e9ce2e9088b806459307b656',
    'password' => 'dcb157469977d0b248c2f8a7972599ce',
    'shared_secret' => 'f98aab1448a877cb5ffa8120b7c828be',
    'myshopify_domain' => 'singulart.myshopify.com',
  ]);

  // Get smart collection.
  $service = new Shopify\Service\SmartCollectionService($client);
  $smartCollections = $service->all();
  if (!empty($smartCollections)) {
    foreach ($smartCollections as $collection) {
      $formCollection[$collection->id] = $collection->title;
    }
  }

  // Get custom collection.
  $service = new Shopify\Service\CustomCollectionService($client);
  $customCollections = $service->all();
  if (!empty($customCollections)) {
    foreach ($customCollections as $collection) {
      $formCollection[$collection->id] = $collection->title;
    }
  }

  // Return form.
  include 'tpl/form.php';

  // Get form data.
  if (!empty($_POST)) {
    $collections = $products = [];
    foreach ($_POST as $v) {
      $collections[$v] = $v;
    }

    $service = new Shopify\Service\CollectService($client);
    $collects = $service->all();
    foreach ($collects as $collect) {
      $collectId = $collect->collection_id;
      if (isset($collections[$collectId])) {
        $products[] = $collect->product_id;
      }
    }

    // Add rules.
    if (!empty($products)) {
      $service = new Shopify\Service\ProductService($client);
      foreach ($products as $productId) {
        $product = $service->get($productId);
        // Get varioants.
        $variants = $product->variants;
        foreach ($variants as $variant) {
          $compare_at_price = $variant->compare_at_price;
          $price = $variant->price;
          // Compare price.
          if ($compare_at_price > $price) {
            // Add sale tag.
            $tags = trim($product->tags) . ', Sale';
            $product->tags = $tags;
            $service->update($product);
          }
          // Get stock level.
          $inventory_quantity = $variant->inventory_quantity;
          if ($inventory_quantity <= 0) {
            // Add sale tag.
            $tags = trim($product->tags) . ', Out of stock';
            $product->tags = $tags;
            $service->update($product);
          }
        }
      }
    }
    echo 'Done.';
  }
