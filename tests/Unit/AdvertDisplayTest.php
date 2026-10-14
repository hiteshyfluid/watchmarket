<?php

namespace Tests\Unit;

use App\Models\Advert;
use App\Models\AttributeOption;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class AdvertDisplayTest extends TestCase
{
    public function test_box_and_papers_label_matches_the_saved_selections(): void
    {
        $combinations = [
            ['Yes', 'Yes', 'Box & Papers'],
            ['Yes', 'No', 'Box only'],
            ['No', 'Yes', 'Papers only'],
            ['No', 'No', 'No box or papers'],
        ];

        foreach ($combinations as [$box, $papers, $expected]) {
            $advert = new Advert;
            $advert->setRelation('box', new AttributeOption(['name' => $box]));
            $advert->setRelation('paper', new AttributeOption(['name' => $papers]));

            $this->assertSame($expected, $advert->boxAndPapersLabel());
        }
    }

    public function test_location_prefers_the_listing_town_then_falls_back_to_the_seller(): void
    {
        $advert = new Advert(['city' => 'Swanscombe']);
        $advert->setRelation('user', new User(['city' => 'Gravesham']));

        $this->assertSame('Swanscombe', $advert->locationLabel());

        $advert->city = null;
        $this->assertSame('Gravesham', $advert->locationLabel());
    }
}
