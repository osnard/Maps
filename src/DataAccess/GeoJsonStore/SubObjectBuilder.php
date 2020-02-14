<?php

declare( strict_types = 1 );

namespace Maps\DataAccess\GeoJsonStore;

use GeoJson\Feature\FeatureCollection;
use GeoJson\Geometry\Point;
use Title;

class SubObjectBuilder {

	private $subjectPage;

	public function __construct( Title $subjectPage ) {
		$this->subjectPage = $subjectPage;
	}

	/**
	 * @return SubObject[]
	 */
	public function getSubObjectsFromGeoJson( string $jsonString ) {
		$json = json_decode( $jsonString );
		$geoJson = \GeoJson\GeoJson::jsonUnserialize( $json );

		return iterator_to_array( $this->featureCollectionToSubObjects( $geoJson ) );
	}

	private function featureCollectionToSubObjects( FeatureCollection $featureCollection ) {
		foreach ( $featureCollection->getFeatures() as $feature ) {
			$geometry = $feature->getGeometry();

			if ( $geometry instanceof Point ) {
				yield $this->pointToSubobject( $geometry, $feature->getProperties() );
			}
		}
	}

	private function pointToSubobject( Point $point, array $properties ): SubObject {
		$subObject = new SubObject( 'GeoJsonPoint' );

		$subObject->addPropertyValuePair(
			'HasCoordinates',
			new \SMWDIGeoCoord( $point->getCoordinates()[0], $point->getCoordinates()[1] )
		);

		if ( array_key_exists( 'description', $properties ) ) {
			$subObject->addPropertyValuePair(
				'HasDescription',
				new \SMWDIBlob( $properties['description'] )
			);
		}

		if ( array_key_exists( 'title', $properties ) ) {
			$subObject->addPropertyValuePair(
				'HasTitle',
				new \SMWDIBlob( $properties['title'] )
			);
		}

		return $subObject;
	}


}
