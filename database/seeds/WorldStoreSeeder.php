<?php

use App\Store;
use App\StorePage;
use App\StorePageHotspot;
use App\ProductCategory;
use App\Product;
use Illuminate\Database\Seeder;

class WorldStoreSeeder extends Seeder
{
    public function run()
    {
        // Create the world store (or get if exists)
        $store = Store::firstOrCreate(
            ['slug' => 'world'],
            ['name' => 'World']
        );

        // Data for iconic places
        $places = [
            ['name' => 'Burj Khalifa', 'country' => 'United Arab Emirates', 'slug' => 'uae', 'x' => 45, 'y' => 35],
            ['name' => 'Eiffel Tower', 'country' => 'France', 'slug' => 'france', 'x' => 38, 'y' => 28],
            ['name' => 'Taj Mahal', 'country' => 'India', 'slug' => 'india', 'x' => 60, 'y' => 42],
            ['name' => 'Great Wall of China', 'country' => 'China', 'slug' => 'china', 'x' => 70, 'y' => 30],
            ['name' => 'Colosseum', 'country' => 'Italy', 'slug' => 'italy', 'x' => 42, 'y' => 32],
            ['name' => 'Sydney Opera House', 'country' => 'Australia', 'slug' => 'australia', 'x' => 75, 'y' => 65],
            ['name' => 'Statue of Liberty', 'country' => 'United States', 'slug' => 'usa', 'x' => 25, 'y' => 20],
            ['name' => 'Pyramids of Giza', 'country' => 'Egypt', 'slug' => 'egypt', 'x' => 50, 'y' => 50],
            ['name' => 'Machu Picchu', 'country' => 'Peru', 'slug' => 'peru', 'x' => 20, 'y' => 60],
            ['name' => 'Sagrada Família', 'country' => 'Spain', 'slug' => 'spain', 'x' => 35, 'y' => 25],
        ];

        // Create home page with hotspots
        $homePage = StorePage::create([
            'store_id' => $store->id,
            'name' => 'Home',
            'slug' => 'home',
            'background_image' => '',
            'menu_display_order' => 0,
            'hide_from_menu' => true
        ]);

        // Create pages (countries) and product categories
        $pages = [];
        $categories = [];

        foreach ($places as $place) {
            // Create page for country
            $page = StorePage::create([
                'store_id' => $store->id,
                'name' => $place['country'],
                'slug' => $place['slug'],
                'background_image' => '',
                'menu_display_order' => count($pages) + 1,
                'hide_from_menu' => false
            ]);
            $pages[$place['slug']] = $page;

            // Create product category for country
            $category = ProductCategory::create([
                'store_id' => $store->id,
                'name' => $place['country'] . ' Packages'
            ]);
            $categories[$place['slug']] = $category;

            // Create 10 products (packages) for each country
            for ($i = 1; $i <= 10; $i++) {
                $product = Product::create([
                    'store_id' => $store->id,
                    'name' => $place['country'] . ' Package ' . $i,
                    'description' => 'Experience ' . $place['country'] . ' - Package ' . $i,
                    'price' => 100 + ($i * 50)
                ]);
                $product->categories()->attach($category->id);
            }

            // Create hotspot linking to the country page
            StorePageHotspot::create([
                'store_page_id' => $homePage->id,
                'name' => $place['name'],
                'slug' => $place['slug'],
                'type' => 'PAGE',
                'value' => $page->slug,
                'x' => $place['x'],
                'y' => $place['y'],
                'display_order' => array_search($place, $places) + 1
            ]);
        }
    }
}
