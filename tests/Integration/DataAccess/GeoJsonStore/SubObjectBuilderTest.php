<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\DataAccess\GeoJsonStore;

use Maps\DataAccess\GeoJsonStore\SubObjectBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\DataAccess\GeoJsonStore\SubObjectBuilder
 */
class SubObjectBuilderTest extends TestCase {

//	public function testEmptyGeoJson() {
//		$objects = $this->newBuilder()->getSubObjectsFromGeoJson( '{"type": "FeatureCollection", "features": []}' );
//
//		$this->assertSame( [], $objects );
//	}

	private function newBuilder(): SubObjectBuilder {
		return new SubObjectBuilder( \Title::newFromText( 'GeoJson:TestGeoJson' ) );
	}

	public function testPoint() {
		$objects = $this->newBuilder()->getSubObjectsFromGeoJson(
			<<<'EOD'
{
    "type": "FeatureCollection",
    "features": [
        {
            "type": "Feature",
            "properties": {
                "title": "Berlin",
                "description": "The capital of Germany"
            },
            "geometry": {
                "type": "Point",
                "coordinates": [
                    13.388729,
                    52.516524
                ]
            }
        }
    ]
}
EOD

		);

		$this->assertCount( 1, $objects );
		$this->assertSame( 'GeoJsonPoint', $objects[0]->getName() );

		$this->assertEquals(
			[
				'HasCoordinates' => [ new \SMWDIGeoCoord( 13.388729, 52.516524 ) ],
				'HasTitle' => [ new \SMWDIBlob( 'Berlin' ) ],
				'HasDescription' => [ new \SMWDIBlob( 'The capital of Germany' ) ],
			],
			$objects[0]->getValues()
		);
	}

}
