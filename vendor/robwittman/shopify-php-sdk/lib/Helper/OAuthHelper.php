<?php

namespace Shopify\Helper;

use Shopify\Api;
use Shopify\Storage\PersistentStorageInterface;
use Shopify\Exception\ShopifySdkException;
use GuzzleHttp\Psr7\Request;
use Shopify\Object\AccessToken;

class OAuthHelper
{
    protected $api;
    protected $storage;

    public function __construct(Api $api, PersistentStorageInterface $storage)
    {
        $this->api = $api;
        $this->storage = $storage;
    }

    public function getAuthorizationUrl($redirectUrl, $scope, $storeState = true)
    {
        $state = '';
        if ($storeState) {
            $state = $this->storage->get('state') ?: $this->getPseudoRandomString();
            $this->storage->set('state', $state);
        }

        $params = array(
            'client_id' => $this->api->getApiKey(),
            'redirect_uri' => $redirectUrl,
            'state' => $state,
            'scope' => $scope
        );

        return "https://{$this->api->getMyshopifyDomain()}/admin/oauth/authorize?".http_build_query($params);
    }

    public function getAccessToken($code, $state = null)
    {
        if (!is_null($state)) {
            $oldState = $this->storage->get('state');
            $this->validateCsrf($state, $oldState);
            $this->resetCsrf();
        }

        $params = array(
            'client_id' => $this->api->getApiKey(),
            'client_secret' => $this->api->getApiSecret(),
            'code' => $code
        );

        $request = new Request('POST', 'https://'.$this->api->getMyshopifyDomain().'/admin/oauth/access_token');
        $response = $this->api->getHttpHandler()->send($request, array('form_params' => $params));
        $token = json_decode($response->getBody()->getContents(), true);
        return new AccessToken($token);
    }

    public function validateCsrf($stateParam, $storedState)
    {
        if (\hash_equals($stateParam, $storedState)) {
            return;
        }
        throw new ShopifySdkException("CSRF Validation failed. Provided state and stored state do not match");
    }

    public function resetCsrf()
    {
        $this->storage->set('state', null);
    }

    public function getPseudoRandomString()
    {
        $length = 32;
        $secure = false;
        $string = openssl_random_pseudo_bytes($length, $secure);
        if ($string === false) {
            throw new ShopifySdkException('openssl_random_pseudo_bytes() returned an unknown error.');
        }
        if ($secure !== true) {
            throw new ShopifySdkException('openssl_random_pseudo_bytes() returned a pseudo-random string but it was not cryptographically secure and cannot be used.');
        }
        return $this->binToHex($string, $length);
    }

    public function binToHex($binaryData, $length)
    {
        return \substr(\bin2hex($binaryData), 0, $length);
    }
}
