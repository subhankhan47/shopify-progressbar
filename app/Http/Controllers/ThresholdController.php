<?php

namespace App\Http\Controllers;

use App\Models\Threshold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThresholdController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->plan){
            return \Redirect::tokenRedirect('plans.index');
        }
        $thresholds = Auth::user()->thresholds()->orderBy('priority')->get();
        $productGids = $thresholds
            ->where('reward_type', 'free_product')->pluck('product_id')->filter()
            ->map(fn($id) => "gid://shopify/ProductVariant/{$id}")
            ->unique()->values()->toArray();
        $titles = $this->fetchTitles(Auth::user(), $productGids);
        return view('thresholds.index', compact('thresholds', 'titles'));
    }

    public function create()
    {
        return view('thresholds.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'reward_type' => 'required|string|max:255',
            'product_id' => 'nullable|string',
            'priority' => 'required|integer|min:0',
            'auto_add_product' => 'boolean',
            'shipping_regions' => 'nullable|array',
        ]);

        $data['user_id'] = Auth::id();
        $data['shipping_regions'] = $request->has('shipping_regions') && !empty(array_filter($request->shipping_regions))
            ? array_filter($request->shipping_regions)
            : null;

        Threshold::create($data);
        return response()->json(['message' => 'Threshold created successfully.']);
    }

    public function edit($id)
    {
        $threshold = Auth::user()->thresholds()->findOrFail($id);
        $title = null;
        if ($threshold->reward_type === 'free_product' && $threshold->product_id) {
            $gid = "gid://shopify/ProductVariant/{$threshold->product_id}";
            $titles = $this->fetchTitles(Auth::user(), [$gid]);
            $title = $titles['ProductVariant/' . $threshold->product_id] ?? null;
        }
        return view('thresholds.edit', compact('threshold', 'title'));
    }

    public function update(Request $request, $id)
    {
        $threshold = Auth::user()->thresholds()->findOrFail($id);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'reward_type' => 'required|string|max:255',
            'product_id' => 'nullable|string',
            'priority' => 'required|integer|min:0',
            'auto_add_product' => 'boolean',
            'shipping_regions' => 'nullable|array',
        ]);

        $data['shipping_regions'] = $request->has('shipping_regions') && !empty(array_filter($request->shipping_regions))
            ? array_filter($request->shipping_regions)
            : null;
        $threshold->update($data);

        return response()->json(['message' => 'Threshold updated successfully.']);
    }

    public function destroy($id)
    {
        $threshold = Auth::user()->thresholds()->findOrFail($id);
        $threshold->delete();
        return response()->json(['message' => 'Threshold deleted successfully.']);
    }

    public function searchProducts(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
        $query = '';
        if (!empty($searchTerm)) {
            $query = 'query:"title:*' . $searchTerm . '*"';
        } else {
            $query = 'query:"title:*"';
        }

        $query = <<<GRAPHQL
    {
      products(first: 20, {$query}) {
        edges {
          node {
            id
            title
            variants(first: 20) {
              edges {
                node {
                  id
                  title
                }
              }
            }
          }
        }
      }
    }
    GRAPHQL;

        $response = auth()->user()->api()->graph($query);
        $products = $response['body']['data']['products']['edges'] ?? [];
        $productArray = [];
        foreach ($products as $productEdge) {
            $product = $productEdge['node'];
            foreach ($product['variants']['edges'] as $variantEdge) {
                $variant = $variantEdge['node'];
                $productArray[] = [
                    'id' => str_replace('gid://shopify/ProductVariant/', '', $variant['id']),
                    'text' => $variant['title'] === 'Default Title'
                        ? $product['title']
                        : "{$product['title']} - {$variant['title']}"
                ];
            }
        }
        return response()->json($productArray);
    }

    public function fetchTitles($shop, array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        $gids = implode('", "', $ids);
        $query = <<<GRAPHQL
    {
        nodes(ids: ["{$gids}"]) {
            ... on ProductVariant {
                id
                title
                product {
                    title
                }
            }
        }
    }
    GRAPHQL;
        $response = $shop->api()->graph($query);
        $nodes = $response['body']['data']['nodes'] ?? [];
        $titles = [];
        foreach ($nodes as $node) {
            if (isset($node['id']) && isset($node['title'])) {
                $typeAndId = explode('/', str_replace('gid://shopify/', '', $node['id']));
                $titles["{$typeAndId[0]}/{$typeAndId[1]}"] = ($node['title'] === 'Default Title'
                    ? ($node['product']['title'] ?? 'Product')
                    : (($node['product']['title'] ?? 'Product') . ' - ' . $node['title']));
            }
        }
        return $titles;
    }

}
