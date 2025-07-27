<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class ShopifyHelper
{
    /**
     * Fetch the store name and main theme ID using GraphQL API
     * @return array ['storeName' => string|null, 'themeId' => string|null]
     */
    public static function getShopifyStoreDetails()
    {
        $query = '{
            shop {
                name
            }
            themes(first: 10) {
                edges {
                    node {
                        id
                        name
                        role
                    }
                }
            }
        }';
        $shop = Auth::user();
        $response = $shop->api()->graph($query);
        $storeName = $response['body']['data']['shop']['name'] ?? null;
        $themeId = null;
        foreach ($response['body']['data']['themes']['edges'] as $theme) {
            if (strtolower($theme['node']['role']) === 'main') {
                $themeId = str_replace('gid://shopify/OnlineStoreTheme/', '', $theme['node']['id']);
                break;
            }
        }
        return [
            'storeName' => $storeName,
            'themeId'   => $themeId,
        ];
    }

    /**
     * Fetch the store Name and Email using GraphQL API
     * @return array ['storeName' => string|null, 'email' => string|null]
     */
    public static function getShopifyStoreNameEmail()
    {
        $query = '{
            shop {
                name,
                email
            }
        }';
        $shop = Auth::user();
        $response = $shop->api()->graph($query);
        $storeName = $response['body']['data']['shop']['name'] ?? null;
        $email = $response['body']['data']['shop']['email'];
        return [
            'storeName' => $storeName,
            'email'   => $email,
        ];
    }
}
