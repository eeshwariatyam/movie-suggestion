<?php

namespace App\Traits;

Trait ArrayTrait {

    public static function reindexArrayByElementName($array, $elementName)
    {
        $newArr = [];

        for( $i=0 ; $i < count($array) ; $i++ ) {
            $newArr[$array[$i][$elementName]] = $array[$i];
        }

        return $newArr;
    }

    public static function formatRecommendationResponse($array, $limit)
    {
        $returnArr = [];

        for ( $i=0 ; $i < $limit; $i++) {
            $returnArr[] = [
                'id'           => $array[$i]['id'],
                'name'         => $array[$i]['original_title'],
                'release_year' => $array[$i]['release_date']
            ];
        }

        return $returnArr;
    }

}
