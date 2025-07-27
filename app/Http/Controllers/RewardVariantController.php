<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RewardVariantController extends Controller
{
    public function createRewardVariant(Request $request)
    {
        $userName = $request->get('shop');
        $shop = User::where('name', $userName)->first();
        $sourceVariantId = $request->input('source_variant_id');
        \Log::info('sourceVariantId: ' . $sourceVariantId);

        if (!$sourceVariantId) {
            return response()->json(['error' => 'Missing source variant id'], 400);
        }
        $rewardVariantId = $this->getOrCreateClonedVariant($shop, $sourceVariantId);

        return response()->json(['variant_id' => $rewardVariantId['variantID'],
            'product_handle' => $rewardVariantId['productHandle']]);
    }

    protected function getOrCreateClonedVariant($shop, $sourceVariantId)
    {
        \Log::info("[Reward Clone] Start for sourceVariantId: {$sourceVariantId}");

        $sourceVariantGid = "gid://shopify/ProductVariant/{$sourceVariantId}";

        // Step 1: Fetch source variant & product
        $fetchQuery = <<<GRAPHQL
    {
        node(id: "{$sourceVariantGid}") {
            ... on ProductVariant {
                id
                title
                selectedOptions {
                    name
                    value
                }
                product {
                    id
                    handle
                    options {
                        id
                        name
                        values
                        optionValues { name }
                    }
                    variants(first: 100) {
                        edges {
                            node {
                                id
                                selectedOptions {
                                    name
                                    value
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    GRAPHQL;

        $fetchResp = $shop->api()->graph($fetchQuery);
        $variantData = $fetchResp['body']['data']['node'] ?? null;

        if (!$variantData) {
            throw new \Exception("Source variant not found");
        }
        Log::info('VariantData= '. json_encode($variantData));
        $productId = $variantData['product']['id'];
        $productHandle = $variantData['product']['handle'];
        $selectedOptions = $variantData['selectedOptions'];
        $productOptions = $variantData['product']['options'];
        $allVariants = $variantData['product']['variants']['edges'];
        Log::info('$selectedOptions= '. json_encode($selectedOptions));
        Log::info('$productOptions= '. json_encode($productOptions));
        Log::info('$allVariants= '. json_encode($allVariants));

        // Step 2: Build FREE version of selected options
        $freeOptionValues = collect($selectedOptions)->map(function ($opt) {
            return [
                'name' => $opt['name'],
                'value' => "{$opt['value']} - FREE"
            ];
        })->toArray();

        $targetMap = collect($freeOptionValues)->pluck('value', 'name')->all();

        // Step 3: Check if FREE variant already exists
        foreach ($allVariants as $variant) {
            $currentMap = collect($variant['node']['selectedOptions'])->pluck('value', 'name')->all();
            if ($currentMap == $targetMap) {
                \Log::info("[Reward Clone] Already exists: " . $variant['node']['id']);
                return ['variantID' => str_replace('gid://shopify/ProductVariant/', '', $variant['node']['id']),
                    'productHandle' => $productHandle
                ];
            }
        }

        // Step 4: Title-only product
        $onlyTitleOption = count($productOptions) === 1 && $productOptions[0]['name'] === 'Title';
        if ($onlyTitleOption) {
            $originalValue = $selectedOptions[0]['value']; // Default Title
            $newValue = "{$originalValue} - FREE"; // Default Title - FREE

            $existingValues = collect($productOptions[0]['optionValues'])->pluck('name')->toArray();
            if (!in_array($newValue, $existingValues)) {
        $mutationOptionsValue = <<<MUTATION
            mutation createOptionsValue(\$productId: ID!, \$options: OptionUpdateInput!, \$optionValuesToAdd: [OptionValueCreateInput!]) {
                productOptionUpdate(
                    variantStrategy: MANAGE,
                    productId: \$productId,
                    option: \$options,
                    optionValuesToAdd: \$optionValuesToAdd
                ) {
                    product {
                        variants(first: 1, sortKey: POSITION, reverse: true) {
                            edges {
                                node {
                                    id
                                    selectedOptions {
                                        name
                                        value
                                    }
                                }
                            }
                        }
                        options {
                            name
                            optionValues {
                                id
                                name
                            }
                        }
                    }
                    userErrors {
                        field
                        message
                        code
                    }
                }
            }
            MUTATION;

                $optionResp = $shop->api()->graph($mutationOptionsValue, [
                    'productId' => $productId,
                    'options' => [
                        'id' => $productOptions[0]['id'],
                    ],
                    'optionValuesToAdd' => [['name' => $newValue]]
                ]);

                \Log::info('[Reward Clone] OptionValue add response: ' . json_encode($optionResp));

                $newVariant = $optionResp['body']['data']['productOptionUpdate']['product']['variants']['edges'][0]['node'] ?? null;
                if (!$newVariant) {
                    throw new \Exception("No new variant returned after adding FREE option");
                }

                // Step 5: Update new FREE variant's price
                $updateMutation = <<<MUTATION
            mutation productVariantsBulkUpdate(\$productId: ID!, \$variants: [ProductVariantsBulkInput!]!) {
                productVariantsBulkUpdate(productId: \$productId, variants: \$variants) {
                    product { id }
                    productVariants {
                        id
                        title
                        price
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
            MUTATION;

                $updateResp = $shop->api()->graph($updateMutation, [
                    'productId' => $productId,
                    'variants' => [[
                        'id' => $newVariant['id'],
                        'price' => "0.00",
                        "compareAtPrice"    => null,
                        'inventoryPolicy' => "CONTINUE"
                    ]]
                ]);

                \Log::info('[Reward Clone] Variant update response: ' . json_encode($updateResp));

                $updated = $updateResp['body']['data']['productVariantsBulkUpdate']['productVariants'][0] ?? null;
                if (!$updated) {
                    $errs = $updateResp['body']['data']['productVariantsBulkUpdate']['userErrors'] ?? [];
                    throw new \Exception("Failed to update FREE variant price: " . json_encode($errs));
                }

                return ['variantID' => str_replace('gid://shopify/ProductVariant/', '', $updated['id']),
                    'productHandle' => $productHandle
                    ];
            }

            throw new \Exception("FREE option value exists but no variant matched — mismatch");
        }

        // Step 6: Multi-option case — create variant
        $optionValues = collect($selectedOptions)->map(function ($opt) use ($productOptions) {
            $option = collect($productOptions)->firstWhere('name', $opt['name']);
            return [
                'optionId' => $option['id'],
                'name' => "{$opt['value']} - FREE"
            ];
        })->toArray();

        $createMutation = <<<GRAPHQL
    mutation CreateProductVariants(\$productId: ID!, \$variantsInput: [ProductVariantsBulkInput!]!) {
        productVariantsBulkCreate(productId: \$productId, variants: \$variantsInput) {
            productVariants {
                id
                title
                price
            }
            userErrors {
                field
                message
            }
        }
    }
    GRAPHQL;

        $createResp = $shop->api()->graph($createMutation, [
            'productId' => $productId,
            'variantsInput' => [[
                'price' => "0.00",
                'optionValues' => $optionValues,
                'inventoryPolicy' => "CONTINUE"
            ]]
        ]);

        \Log::info('[Reward Clone] Variant create response: ' . json_encode($createResp));

        $created = $createResp['body']['data']['productVariantsBulkCreate']['productVariants'][0] ?? null;
        if (!$created) {
            $errs = $createResp['body']['data']['productVariantsBulkCreate']['userErrors'] ?? [];
            throw new \Exception("Failed to create FREE variant: " . json_encode($errs));
        }
        return ['variantID' => str_replace('gid://shopify/ProductVariant/', '', $created['id']),
            'productHandle' => $productHandle
        ];
    }
}
