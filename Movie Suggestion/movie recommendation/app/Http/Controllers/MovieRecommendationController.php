<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ArrayTrait;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;


class MovieRecommendationController extends Controller
{

    use ArrayTrait;

    public function getMovieList(Request $request)
    {
        //TODO: in real development save this list in cache!
        /** check if movie_id is in list - can be moved to validation as well **/
        $genres = $this->getMovieGenres();
        if (!array_key_exists($request->genre_id, $genres)) {
            return sprintf(
                "genre id %s is not in genre's list \nplease choose from the following:\n%s",
                $request->genre_id,
                print_r($genres, true)
            );
        }

        $url = sprintf("%s/movie/%s/lists", config('movieRecommendation.api.url'), $request->genre_id);
        $response = Curl::to($url)
            ->withData([
                'api_key'  => config('movieRecommendation.api.key'),
                'language' => 'en-US',
                'page'     => 1
            ])
            ->asJson(true)
            ->get();

        return $response['results'];
    }

    public function getMovieGenres()
    {
        $response = Curl::to(config('movieRecommendation.api.url') . '/genre/movie/list')
            ->withData(['api_key' => config('movieRecommendation.api.key'), 'language' => 'en-US'])
            ->asJson(true)
            ->get();

       return ArrayTrait::reindexArrayByElementName($response['genres'], 'id');
    }

    public function getRecommendationForMovie(Request $request)
    {

        $url = config('movieRecommendation.api.url') . "/movie/%s/recommendations";
        $url = sprintf($url, $request->movie_id);

        $response = Curl::to($url)
            ->withData([
                'api_key'  => config('movieRecommendation.api.key'),
                'language' => 'en-US',
                'page'     => 1
            ])
            ->asJson(true)
            ->get();

        if (empty($response['results'])) {
            return [];
        }

        $depth = $request->depth ?: config('movieRecommendation.depth');

        return ArrayTrait::formatRecommendationResponse($response['results'], $depth);
    }

    public function getRecommendationForMovieRecursive(Request $request)
    {
        $outerResults = $this->getrecommendationformovie($request);

        foreach ($outerResults as &$outerResult) {
            $secondRequest = new Request();
            $secondRequest->replace([
                'movie_id' => $outerResult['id'],
                'depth'    => $request->depth
            ]);

            $innerResult = $this->getRecommendationForMovie($secondRequest);
            $outerResult['recommendations'] = $innerResult;
        }

        return $outerResults;
    }

}
