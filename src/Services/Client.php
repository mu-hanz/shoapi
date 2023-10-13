<?php

namespace Muhanz\Shoapi\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class Client
{
    /**
     * The Host URL.
     *
     * @var string
     */
    protected $hostUrl;

    /**
     * The Path URL.
     *
     * @var string
     */
    protected $path;

    /**
     * The Partner ID.
     *
     * @var string
     */
    protected $partnerId;

    /**
     * The Partner KEY.
     *
     * @var string
     */
    protected $partnerKey;

    /**
     * The Time.
     *
     * @var string
     */
    protected $timeStamp;

    /**
     * The Credential.
     *
     * @var array
     */
    protected $credential = [];

    /**
     * Get Credential Request.
     */
    protected function credential(array $credential)
    {
        $this->credential = $credential;

        return $this;
    }

    /**
     * Get Methods for Requests HTTP Client.
     *
     * $method string
     *
     * @param array $params
     */
    protected function store(array $params = [], string $method)
    {
        if ($method === 'GET') {
            return $this->http_get($params);
        } elseif ($method === 'POST') {
            return $this->http_post($params);
        } elseif ($method === 'ATTACH') { // post with image/video
            return $this->http_attach($params);
        }

        throw new InvalidArgumentException('No method was specified. please open config_path.php');
    }

    /**
     * Making Requests HTTP Client.
     *
     * $method POST
     *
     * @param array $params
     *
     * @return Muhanz\Shoapi\Shoapi;
     */
    protected function http_post(array $params)
    {
        if (!config('shoapi.partner_id')) {
            throw new InvalidArgumentException('No Partner ID in shoapi config file.');
        }

        $body = array_merge(['partner_id' => (int) config('shoapi.partner_id')], $params);
        $url = $this->signature($params);
        $response = Http::post($url, $body);

        return $response->json();
    }

    /**
     * Making Requests HTTP Client.
     *
     * $method GET
     *
     * @param array $params
     *
     * @return Muhanz\Shoapi\Shoapi;
     */
    protected function http_get(array $params)
    {
        $extParams = $this->extParams($params);
        $url = $this->signature($params);
        $response = Http::get($url.'&'.$extParams);

        return $response->json();
    }

    /**
     * Making Requests HTTP Client.
     *
     * $method ATTACH|POST
     *
     * @param array $params
     *
     * @return Muhanz\Shoapi\Shoapi;
     */
    protected function http_attach(array $params)
    {
        if (isset($params['image'])) {
            $file = fopen($params['image'], 'r');
            $filename = 'image';
        }

        if (isset($params['part_content'])) {
            $file = fopen($params['part_content'], 'r');
            $filename = 'part_content';
        }

        $url = $this->signature($params);
        $response = Http::attach($filename, $file)->post($url, $params);

        return $this->dataCollect($response->json());
    }

    /**
     * Making signature API URL.
     *
     * @return string URL;
     */
    protected function signature(array $params)
    {
        if (!config('shoapi.partner_key')) {
            throw new InvalidArgumentException('No Partner KEY in shoapi config file.');
        }

        $this->timeStamp = time();
        $this->hostUrl = config('shoapi.production') ? config('shoapi.host_url') : config('shoapi.sandbox_host_url');
        $this->partnerId = config('shoapi.partner_id');
        $this->partnerKey = config('shoapi.partner_key');
        $this->path = config('shoapi.api_version').$this->credential['path'];

        $common_params = [
            'partner_id'    => $this->partnerId,
            'timestamp'     => $this->timeStamp,
            'sign'          => $this->hashSign(),
        ];

        if (isset($params['redirect'])) {
            $common_params['redirect'] = url($params['redirect']);
        }

        if (isset($this->credential['access_token'])) {
            $common_params = array_merge($common_params, $this->credential);
        }

        return  $this->hostUrl.$this->path.'?'.http_build_query(Arr::except($common_params, ['path']));
    }

    /**
     * Making Hash Mac sha256 signature API.
     *
     * @return string;
     */
    protected function hashSign()
    {
        $signString = [
            $this->partnerId,
            $this->path,
            $this->timeStamp,
        ];

        if (isset($this->credential['access_token'])) {
            array_push($signString, $this->credential['access_token']);
            if (isset($this->credential['shop_id'])) {
                array_push($signString, $this->credential['shop_id']);
            }
            if (isset($this->credential['merchant_id'])) {
                array_push($signString, $this->credential['merchant_id']);
            }
        }

        return hash_hmac('sha256', implode($signString), $this->partnerKey);
    }

    /**
     * Making Extended HTTP Query.
     *
     * @param array $params
     *
     * @return string;
     */
    protected function extParams(array $params)
    {
        if (Arr::exists($params, 'item_status')) {
            $item_status = Arr::get($params, 'item_status');
            $new_params = http_build_query(data_forget($params, 'item_status'));

            return $new_params.'&item_status='.implode('&item_status=', $item_status);
        }

        if (Arr::exists($params, 'item_id_list')) {
            $item_id_list = Arr::get($params, 'item_id_list');
            $new_params = http_build_query(data_forget($params, 'item_id_list'));

            return $new_params.'&item_id_list='.implode(',', $item_id_list);
        }

        return !empty($params) ? '&'.http_build_query($params) : '';
    }
}
