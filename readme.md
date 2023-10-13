# Shoapi - Laravel Shopee API SDK (V2)

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
![StyleCI](https://github.styleci.io/repos/704592871/shield?branch=master)

![Laravel Shopee API SDK (V2)](https://muhanz.my.id/ShoAPI.svg)

This is a Shopee PHP Client for Laravel 9|10 and currently supported **API V2**
> ShoAPi lets you develop **Shopee Open API** in Laravel.
ShoAPi  is an HTTP-based interface created for developers interested **Shopee Open API**.
To learn more about the **Shopee Open API**, please consult the Introduction on the official [Shopee Open Platform](https://open.shopee.com/documents?module=87&type=2&id=64&version=2) site.

## Installation
Via Composer
``` bash
$ composer require muhanz/shoapi
```
## Before Start

Configure your variables in your `.env` (recommended) or you can publish the config file and change it there.
```
SHOPEE_PRODUCTION=false // Default
SHOPEE_HOST_URL='https://partner.shopeemobile.com'
SHOPEE_SANDBOX_HOST_URL='https://partner.test-stable.shopeemobile.com'
SHOPEE_CALLBACK_URL='/shopee/auth/get_access_token' // Example: for redirect after auth
SHOPEE_PARTNER_ID=<YOUR_PARTNER_ID>
SHOPEE_PARTNER_KEY=<YOUR_PARTNER_KEY>
```
(Optional) You can publish the config file via this command:
```bash
php artisan vendor:publish --provider="Muhanz\Shoapi\ShoapiServiceProvider" --tag="config"
```

## Authorization and Authentication
```bash
// Add this facades on top controller
use Muhanz\Shoapi\Facades\Shoapi;

public function auth_partner()
{
    return Shoapi::call('shop')->access('auth_partner')->redirect(); // auto redirect
    
    // or
    
    return Shoapi::call('shop')->access('auth_partner')->getUrl();
    
    // response:
    // https://partner.test-stable.shopeemobile.com/api/v2/shop/auth_partner?partner_id=partner_idtimestamp=1697215282&sign=sign_code&redirect=redirect_url   
}
```
For cancel/disconnet auth partner
```bash
Shoapi::call('shop')->access('cancel_auth_partner')->redirect();
```
## Get Access Token & Refresh Access  Token
The access_token is a dynamic token, and you need to pass access_token to call non-public APIs. Each access_token is valid for **4 hours** and can be used multiple times within **4 hours**. However, you need to refresh the access token by calling RefreshAccessToken before it expires in order to obtain a new access_token.

Refresh_token is a parameter used to refresh access_token. Each refresh_token is valid for **30 days**.

⚠️ Note:
The access_token and refresh_token of each shop_id and merchant_id need to be saved separately.

Get Access Token:
```bash
// After authorization, the front-end page will redirect to the redirect URL in your authorization link:
// https://open.shopee.com/?code=xxxxxxxxxx&shop_id=xxxxxx

$params =  [
	'code'  =>  "54494572544875766********", 
	'shop_id'  =>  (int)  000000,
];

$response = Shoapi::call('auth')
        ->access('get_access_token')
        ->shop(000000)
        ->request($params)
        ->response();
				
dd($response);
```
Get Refresh Access Token:
```bash
$params =  [
	'refresh_token'  =>  "527a424f54494572544875766*******",
	'shop_id'  =>  (int)  000000,
];

$response =  Shoapi::call('auth')
            ->access('refresh_access_token')
            ->shop(000000)
            ->request($params)
            ->response();

dd($response);
```
Example Response:
```bash
{
	api_status:"success",
	partner_id:000000,
	refresh_token:"527a424f54494572544875766*******",
	access_token:"786b4c74526e52426555616e*******",
	expire_in:14400,
	request_id:"84ec4d8971735d62dca40c0*******",
	shop_id:000000
}
```

## Basic Usage
```bash
// Add this facades on top controller
use Muhanz\Shoapi\Facades\Shoapi;

public function get_shop_info()
{
	
	// path api: /api/v2/shop(use in call)/get_shop_info(use in access)

  return Shoapi::call('shop')
        ->access('get_shop_info', YOUR_ACCESS_TOKEN)
        ->shop(YOUR_SHOP_ID)
        ->response();
}
```

Other Example:
```bash
// Add this facades on top controller
use Muhanz\Shoapi\Facades\Shoapi;

public  function  get_category()
{

	$params =  [
		'language'  =>  'id'  // en
	];

	$response = Shoapi::call('product')
    ->access('get_category',  session()->get('shoapi.access_token'))
    ->shop(session()->get('shoapi.shop_id'))
    ->request($params)
    ->response();
	dd($response);
}

```
Example Response:
```bash
{
	api_status:"success",
	category_list:[
		{
			category_id:100001,
			parent_category_id:0,
			original_category_name:"Health",
			display_category_name:"Kesehatan",
			has_children:true

		}
	]
}
```

## Support Methods

Currently supported all methods exclude **GlobalProduct(CB seller only)** please open [Shopee Open Platform](https://open.shopee.com/documents/v2/v2.product.get_category?module=89&type=1)

|Service *(call)*               |Method *(access)* |						|
|----------------|----------------------------------------------------|-------|
|Product		|`add_item, get_category and 35 others`           		|✅|
|Global Product(CB seller only)|`- Not Tested`           				|❌|
|MediaSpace		|`upload_image, upload_video, etc`           			|✅|
|Shop			|`get_info_shop, get_profile, update_profile, etc`		|✅|
|Merchant		|`get_merchant_info, get_shop_list_by_merchant, etc` 	|✅|
|Order			|`get_order_list, get_order_detail and 11 others`		|✅|
|Logistics		|`get_shipping_parameter, get_tracking_number, and 14 others` 	|✅|
|First Miles	|`get_unbind_order_list, get_detail, etc` 				|✅|
|Payment		|`get_escrow_detail, set_shop_installment_status, etc` 	|✅|
|Discount		|`add_discount, add_discount_item, etc` 				|✅|
|Bundle Deal	|`add_bundle_deal, add_bundle_deal_item, etc` 			|✅|
|Add-On Deal	|`add_add_on_deal, add_add_on_deal_main_item, etc` 		|✅|
|Voucher		|`add_voucher, delete_voucher, etc` 					|✅|
|Follow Prize	|`add_follow_prize, delete_follow_prize, etc` 			|✅|
|Top Picks		|`get_top_picks_list, add_top_picks, etc` 				|✅|
|Shop Category	|`add_shop_category, get_shop_category_list, etc` 		|✅|
|Returns		|`get_return_detail, get_return_list, etc` 				|✅|
|Account Health	|`shop_performance, shop_penalty` 						|✅|
|Public			|`get_shops_by_partner, get_access_token,refresh_access_token, etc` 	|✅|
|Push			|`set_app_push_config, get_app_push_config`				|✅|




## Full Documentation *(soon)*

Documentation for the SDK can be found on the `[soon]`


## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.


## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.


## Credits

- [Mu Hanz][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/muhanz/shoapi.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/muhanz/shoapi.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/muhanz/shoapi/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/muhanz/shoapi
[link-downloads]: https://packagist.org/packages/muhanz/shoapi
[link-travis]: https://travis-ci.org/muhanz/shoapi
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/muhanz
[link-contributors]: ../../contributors
