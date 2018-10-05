<?php

namespace Shopify\Service;

use Shopify\Object\Province;

class ProvinceService extends AbstractService
{
    /**
     * Receive a list of all Provinces
     *
     * @link   https://help.shopify.com/api/reference/province#index
     * @param  array $params
     * @return Province[]
     */
    public function all($countryId, array $params = array())
    {
        $endpoint = '/admin/countries/'.$countryId.'/provinces.json';
        $response = $this->request($endpoint, 'GET', $params);
        return $this->createCollection(Province::class, $response['provinces']);
    }

    /**
     * Receive a count of all Provinces
     *
     * @link   https://help.shopify.com/api/reference/province#count
     * @param  integer $countryId
     * @return integer
     */
    public function count($countryId)
    {
        $endpoint = '/admin/countries/'.$countryId.'/provinces/count.json';
        $response = $this->request($endpoint);
        return $response['count'];
    }

    /**
     * Receive a single province
     *
     * @link   https://help.shopify.com/api/reference/province#show
     * @param  integer $countryId
     * @param  integer $provinceId
     * @return Province
     */
    public function get($countryId, $provinceId)
    {
        $endpoint = '/admin/countries/'.$countryId.'/provinces/'.$provinceId.'.json';
        $response = $this->request($endpoint);
        return $this->createObject(Province::class, $response['province']);
    }

    /**
     * Modify an existing province
     *
     * @link   https://help.shopify.com/api/reference/province#update
     * @param  Province $province
     * @return void
     */
    public function update($countryId, Province &$province)
    {
        $data = $province->exportData();
        $endpoint = '/admin/countries/'.$countryId.'/provinces/'.$province->id.'.json';
        $response = $this->request(
            $endpoint, 'PUT', array(
            'province' => $data
            )
        );
        $province->setData($response['province']);
    }
}
