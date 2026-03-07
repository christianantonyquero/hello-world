<?php

use App\Store;
use App\StorePage;
use App\StorePageHotspot;
use Illuminate\Database\Seeder;

class WorldStoreHotspotsSeeder extends Seeder
{
    public function run()
    {
        // Get the world store
        $store = Store::where('slug', 'world')->first();
        if (!$store) return;

        // Get the home page
        $homePage = StorePage::where('store_id', $store->id)
            ->where('slug', 'home-page')
            ->first();
        if (!$homePage) return;

        // Delete existing hotspots on home page
        $homePage->hotspots()->delete();

        // Data for iconic places with coordinates
        $places = [
            ['name' => 'Burj Khalifa', 'slug' => 'uae', 'x' => 45, 'y' => 35],
            ['name' => 'Eiffel Tower', 'slug' => 'france', 'x' => 38, 'y' => 28],
            ['name' => 'Taj Mahal', 'slug' => 'india', 'x' => 60, 'y' => 42],
            ['name' => 'Great Wall of China', 'slug' => 'china', 'x' => 70, 'y' => 30],
            ['name' => 'Colosseum', 'slug' => 'italy', 'x' => 42, 'y' => 32],
            ['name' => 'Sydney Opera House', 'slug' => 'australia', 'x' => 75, 'y' => 65],
            ['name' => 'Statue of Liberty', 'slug' => 'usa', 'x' => 25, 'y' => 20],
            ['name' => 'Pyramids of Giza', 'slug' => 'egypt', 'x' => 50, 'y' => 50],
            ['name' => 'Machu Picchu', 'slug' => 'peru', 'x' => 20, 'y' => 60],
            ['name' => 'Sagrada Família', 'slug' => 'spain', 'x' => 35, 'y' => 25],
        ];

        // Create hotspots
        foreach ($places as $index => $place) {
            // Find the country page by slug
            $countryPage = StorePage::where('store_id', $store->id)
                ->where('slug', $place['slug'])
                ->first();

            if ($countryPage) {
                StorePageHotspot::create([
                    'store_page_id' => $homePage->id,
                    'name' => $place['name'],
                    'slug' => $place['slug'],
                    'type' => 'PAGE',
                    'value' => $countryPage->slug,
                    'x' => $place['x'],
                    'y' => $place['y'],
                    'display_order' => $index + 1
                ]);
            }
        }
    }
}
