<?php

namespace Muhanz\Shoapi;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Muhanz\Shoapi\Services\Client;

class Shoapi extends Client
{
    /**
     * Main called method.
     *
     * @var string
     */
    protected $called;

    /**
     * Common parameters method.
     *
     * @var string
     */
    protected $access;

    /**
     * The Access Token.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * The Shop ID.
     *
     * @var string
     */
    protected $shopId;

    /**
     * The Merchant ID.
     *
     * @var string
     */
    protected $merchantId;

    /**
     * The Path URL.
     *
     * @var string
     */
    protected $path;

    /**
     * The Parameters Request.
     *
     * @var array
     */
    protected $request = [];

    /**
     * Get and Set Called Request.
     */
    public function call(string $value)
    {
        $this->called = $value;

        return $this;
    }

    /**
     * Get and Set Access or Token Request.
     *
     * @param string $value
     * @param string $access_token
     */
    public function access(string $value, string $access_token = '')
    {
        $this->accessToken = $access_token;
        $this->access = $value;

        return $this;
    }

    /**
     * Get and Set Shop ID.
     *
     * @param int $value
     */
    public function shop($value)
    {
        $this->shopId = (int) $value;

        return $this;
    }

    /**
     * Get and Set Shop ID.
     *
     * @param int $value
     */
    public function merchant($value)
    {
        $this->merchantId = (int) $value;

        return $this;
    }

    /**
     * Get and Set Parameters Request.
     *
     * @param array $request
     */
    public function request(array $request = [])
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get response.
     *
     * @return array
     */
    public function response()
    {
        return $this->dataCollect($this->getResponse());
    }

    /**
     * Redirect Auth Parter.
     *
     * @return array
     */
    public function redirect()
    {
        return new RedirectResponse($this->createAuthUrl());
    }

    /**
     * Get Auth Parter.
     *
     * @return string url
     */
    public function getUrl()
    {
        return $this->createAuthUrl();
    }

    /**
     * Get response.
     *
     * @return Muhanz\Shoapi\Client;
     */
    protected function getResponse()
    {
        $credential = [
            'path'            => $this->getSpecificPath(),
            'access_token'    => $this->accessToken,
            'shop_id'         => (int) $this->shopId,
            'merchant_id'     => (int) $this->merchantId,
        ];

        $credential = collect($credential)->reject(function ($value) {
            return empty($value);
        });

        return  $this->credential($credential->toArray())->store(
            $this->request,
            $this->getHttpMethod()
        );
    }

    /**
     * Get Specific Path of API.
     *
     * @return string;
     */
    protected function getSpecificPath()
    {
        if ($this->called === 'auth') {
            if ($this->access === 'get_access_token') {
                return $this->called.'/token/get';
            } else {
                return $this->called.'/access_token/get';
            }
        }

        return $this->called.'/'.$this->access;
    }

    /**
     * Get Method of API.
     *
     * @return string;
     * Muhanz\shoapi\config\shoapi_path;
     */
    protected function getHttpMethod()
    {
        $getMethod = Arr::get(config('shoapi_path'), $this->called.'.'.$this->access);

        if ($getMethod) {
            return $getMethod;
        }

        throw new InvalidArgumentException('No Method was specified in shoapi_path file.');
    }

    /**
     * Create auth URL.
     */
    protected function createAuthUrl()
    {
        if (!config('shoapi.callback_url')) {
            throw new InvalidArgumentException('No callback URL in shoapi config file.');
        }

        return  $this->credential([
            'path' => $this->getSpecificPath(),
        ])->signature([
            'redirect'  => config('shoapi.callback_url'),
        ]);
    }

    /**
     * Create data response.
     *
     * @return array;
     */
    protected function dataCollect($response)
    {
        $data = collect($response)->reject(function ($value) {
            return empty($value);
        });

        if ($data->has(['response'])) {
            return (object) array_merge(['api_status' => 'success'], $data->get('response'));
        }

        if ($data->has(['error'])) {
            return $data->isNotEmpty() ? (object) array_merge(['api_status' => 'error'], $data->all()) : [];
        }

        return $data->isNotEmpty() ? (object) array_merge(['api_status' => 'success'], $data->all()) : [];
    }
}
