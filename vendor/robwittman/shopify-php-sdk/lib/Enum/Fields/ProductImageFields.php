<?php

namespace Shopify\Enum\Fields;

class ProductImageFields extends AbstractObjectEnum
{
    const CREATED_AT = 'created_at';
    const ID = 'id';
    const POSITION = 'position';
    const PRODUCT_ID = 'product_id';
    const VARIANT_IDS = 'variant_ids';
    const SRC = 'src';
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const UPDATED_AT = 'updated_at';

    public function getFieldTypes()
    {
        return array(
            'created_at' => 'DateTime',
            'id' => 'integer',
            'position' => 'integer',
            'product_id' => 'integer',
            'variant_ids' => 'array',
            'src' => 'string',
            'height' => 'integer',
            'width' => 'integer',
            'updated_at' => 'DateTime'
        );
    }
}
