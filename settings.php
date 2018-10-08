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
  if (!empty($_POST)) {
    $file = fopen('settings.txt', 'w+');
    ftruncate($file, 0);
    $content = implode(PHP_EOL, $_POST);
    fwrite($file , $content);
    fclose($file );
  }
