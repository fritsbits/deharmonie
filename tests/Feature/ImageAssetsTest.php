<?php

namespace Tests\Feature;

use Tests\TestCase;

class ImageAssetsTest extends TestCase
{
    /**
     * Alle foto's die in views worden gebruikt moeten bestaan als bestand.
     * Als een bestand ontbreekt, faalt deze test — voeg het bestand toe.
     */
    public function test_all_referenced_images_exist(): void
    {
        $images = [
            // Homepage
            'photo-groep-tafel.webp',
            'photo-party.webp',
            'photo-groep-actief.webp',
            'photo-thumbsup.webp',
            'photo-samen.webp',
            'photo-visitors-2.webp',
            'photo-gebouw.webp',
            // Activiteiten overzicht
            'photo-petanque.webp',
            'photo-handwerk.webp',
            'photo-uitstap.webp',
            'photo-muzikanten.webp',
            'photo-verjaardag.webp',
            // Diensten
            'photo-harmonie-bus.webp',
            'photo-onthaal.webp',
            'photo-keuken-chefs.webp',
            'grote-kuis.webp',
            // Weekmenu
            'photo-chef-taart-2.webp',
            'photo-restaurant-bord.webp',
            'photo-feest-2.webp',
        ];

        foreach ($images as $image) {
            $this->assertFileExists(
                public_path('images/' . $image),
                "Afbeelding ontbreekt: public/images/{$image}"
            );
        }
    }

    /**
     * Bestanden die verwijderd moeten worden mogen niet meer bestaan.
     * Als een bestand nog bestaat, is de opruimstap nog niet uitgevoerd.
     */
    public function test_deprecated_images_are_removed(): void
    {
        $deprecated = [
            'photo-cake.jpg',
            'grote-kuis.jpg',
            'photo-chef-taart.webp',
            'photo-buiten-event.webp',
            'photo-petanque.jpg',
        ];

        foreach ($deprecated as $image) {
            $this->assertFileDoesNotExist(
                public_path('images/' . $image),
                "Verouderd bestand nog aanwezig: public/images/{$image}"
            );
        }
    }
}
