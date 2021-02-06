<?php

namespace App\Http\Controllers\SocialMediaApi\Instagram;
require (app_path('Http/libraries/vendor/autoload.php'));
use App\Http\Controllers\ApiController;
use App\Model\AccountManager\AccountManager;
use Illuminate\Support\Facades\Crypt;
use InstagramAPI\Instagram;
use Exception;
class Analytics extends ApiController
{


    private function auth()
    {
        try {
            $data = AccountManager::findOrFail(14);
            Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
            $ig = new Instagram(false, false,
                [
                    'storage' => env('DB_CONNECTION'),
                    'dbhost' => env('DB_HOST'),
                    'dbname' => env('DB_DATABASE'),
                    'dbusername' => env('DB_USERNAME'),
                    'dbpassword' => env('DB_PASSWORD'),
                    'dbtablename' => 'session_instagram'
                ]);

            $ig->login($data->username, Crypt::decryptString($data->auth_token));
            return $ig;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function get()
    {
//39956884041
        try {
            $ig = $this->auth();
            //$current = $ig->timeline->getUserFeed(39956884041); //timlinestat
           // $current = $ig->media->getLikers('2383501015187925919_39956884041');//likers count
           // $current = $ig->media->getComments('2383501015187925919_39956884041');//commit count
           // $current = $ig->media->getCommentLikers('17865464651001898');//commit like
           // $current = $ig->people->getInfoById('39956884041');//user info
           // $current = $ig->people->search('mrrashidov');//search user
           // $current = $ig->people->follow('544729744');//flow user
          //  $current = $ig->people->unfollow('544729744');//unflow user
            //$current = $ig->people->getBlockedList();//blacklist user
           // $current = $ig->people->getBlockedStoryList();//getBlockedStoryList user
           // $current = $ig->story->getStoryItemViewers('17877470071840937');//story view users
           // $current = $ig->story->getArchivedStoriesFeed();//archive story
           // $current = $ig->story->getReelsMediaFeed('archiveDay:17877470071840937');//archive story
          //  $current = $ig->discover->search('maltepe'); //search local tags
          //  $current =  $ig->discover->getSuggestedSearches('users');
          //  $current =  $ig->discover->getSuggestedSearches('hashtags');
            //$current =  $ig->discover->getSuggestedSearches('places');
            $current = $ig->location->findPlaces('eskişehir');//search location
            $profile_info = $ig->people->getSelfInfo();

            return response()->json($profile_info);
        }
        catch (Exception $e){
            return response()->json($e->getMessage());
        }
    }




}
