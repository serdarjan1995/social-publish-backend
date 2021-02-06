<?php

namespace App\Http\Controllers\SocialMediaApi\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Controllers\ApiController;
use App\Model\AccountManager\AccountManager;
use ArrayObject;
use Illuminate\Http\Request;

class Analytics extends ApiController
{
    /**
     * @param Request $request (account_id, action, params[])
     * action: getUserInfo, params: [requested_info = followers_count/following_count/favourites_count/friends_count/statuses_count]
     * action: getFollowersList, params: []
     * action: getFriendsList, params: []
     * action: showFriendship, params: [profile_id, target_name]
     * action: lookupUser, params: [target_name]
     * action: searchUsers, params: [search_query]
     * action: getAccountSetting, params: []
     * action: getSavedSearches, params: []
     * action: getBlockedUsers, params: []
     * action: getMutedUsers, params: []
     * action: getTweetInfo, params: [tweet_ids]
     * action: getHomeTimeline, params: []
     * action: getMentions, params: []
     * action: getUserTimeline, params: [screen_name]
     * action: searchTweets, params: [search_query]
     * action: getSingleTweetbyID, params: [tweet_id]
     * action: getOEmbedSingleTweet, params: [tweet_id]
     * action: getRetweetsbyTweetID, params: [tweet_id]
     * action: getMyRetweetsTimeline, params: []
     * action: getRetweetersIDs, params: [tweet_id]
     * action: getProfileBanner, params: [user_id]
     * action: getSavedSearchInfo, params: [search_query]
     * action: getTrendsbyWOEID, params: [woe_id]
     * action: getPlaceInfo, params: [place_id]
     * action: getTweetEngagement, params: [tweet_ids, engagement_types, groupings]
     * @return array|object
     */
    public function twitterAnalyticsHandler(Request $request)
    {
        $connection = $this->connectToTwitter($request->account_id);

        switch ($request->action) {
            case 'getStatisticalData':
                $result = $this->getStatisticalData($connection);
                break;
            case 'getUserInfo':
                $result = $this->getUserInfo($connection, $request->params);
                break;
            case 'getFollowersList':
                $result = $this->getFollowersList($connection);
                break;
            case 'getFriendsList':
                $result = $this->getFriendsList($connection);
                break;
            case 'showFriendship':
                $result = $this->showFriendship($connection, $request->params);
                break;
            case 'lookupUser':
                $result = $this->lookupUser($connection, $request->params);
                break;
            case 'searchUsers':
                $result = $this->searchUsers($connection, $request->params);
                break;
            case 'getAccountSetting':
                $result = $this->getAccountSetting($connection);
                break;
            case 'getSavedSearches':
                $result = $this->getSavedSearches($connection);
                break;
            case 'getBlockedUsers':
                $result = $this->getBlockedUsers($connection);
                break;
            case 'getMutedUsers':
                $result = $this->getMutedUsers($connection);
                break;
            case 'getTweetInfo':
                $result = $this->getTweetInfo($connection, $request->params);
                break;
            case 'getHomeTimeline':
                $result = $this->getHomeTimeline($connection);
                break;
            case 'getMentions':
                $result = $this->getMentions($connection);
                break;
            case 'getUserTimeline':
                $result = $this->getUserTimeline($connection, $request->params);
                break;
            case 'searchTweets':
                $result = $this->searchTweets($connection, $request->params);
                break;
            case 'getSingleTweetbyID':
                $result = $this->getSingleTweetbyID($connection, $request->params);
                break;
            case 'getOEmbedSingleTweet':
                $result = $this->getOEmbedSingleTweet($connection, $request->params);
                break;
            case 'getRetweetsbyTweetID':
                $result = $this->getRetweetsbyTweetID($connection, $request->params);
                break;
            case 'getMyRetweetsTimeline':
                $result = $this->getMyRetweetsTimeline($connection);
                break;
            case 'getRetweetersIDs':
                $result = $this->getRetweetersIDs($connection, $request->params);
                break;
            case 'getProfileBanner':
                $result = $this->getProfileBanner($connection, $request->params);
                break;
            case 'getSavedSearchInfo':
                $result = $this->getSavedSearchInfo($connection, $request->params);
                break;
            case 'getTrendsbyWOEID':
                $result = $this->getTrendsbyWOEID($connection, $request->params);
                break;
            case 'getPlaceInfo':
                $result = $this->getPlaceInfo($connection, $request->params);
                break;
            /*case 'getTweetEngagement':
                $result = $this->getTweetEngagement($connection, $request->params);
                break;*/
            default:
                $result = $this->success("No Such Action, Read the JDOCS in order to know what actions are available.");

        }

        return $result;
    }

    /**
     * Connects a registered profile ID to twitter.
     * @param int $profile_id
     * @return TwitterOAuth
     */
    private function connectToTwitter(int $account_id)
    {
        $details = AccountManager::join('social_networks', 'account_manager.social_network_id', '=', 'social_networks.id')
            ->select('account_manager.profile_id', 'social_networks.id', 'account_manager.auth_token')
            ->where('social_networks.id', 2)
            ->where('account_manager.id', $account_id)
            ->first();

        $access_tokens = json_decode($details->auth_token);

        $access_oauth_token = $access_tokens->oauth_token;
        $access_oauth_token_secret = $access_tokens->oauth_token_secret;

        $connection = new TwitterOAuth(
            config('services.twitter.client_id'),
            config('services.twitter.client_secret'),
            $access_oauth_token,
            $access_oauth_token_secret);

        return $connection;
    }

    /**
     * Get authenticated account's info according to request info
     * @param TwitterOAuth $connection
     * @param array $params (requested_info = followers_count/following_count/favourites_count/friends_count/statuses_count)
     * @return string
     */
    private function getUserInfo(TwitterOAuth $connection, array $params)
    {
        $profile_info = $connection->get("account/verify_credentials");

        //GET FOLLOWERS COUNT
        if ($params['requested_info'] === "followers_count") {
            return $profile_info->followers_count;
        } //GET FOLLOWING COUNT
        elseif ($params['requested_info'] === "following_count") {
            return $profile_info->friends_count;
        } //GET FAVOURITES COUNT
        elseif ($params['requested_info'] === "favourites_count") {
            return $profile_info->favourites_count;
        } //GET FRIENDS COUNT (Friends are the people who follow you & You follow them at the same time)
        elseif ($params['requested_info'] === "friends_count") {
            return $profile_info->friends_count;
        } //GET TOTAL STATUSES COUNT
        elseif ($params['requested_info'] === "statuses_count") {
            return $profile_info->statuses_count;
        }
        return "SUCCESS";
    }

    /**
     * Get a list of Followers.
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function getFollowersList(TwitterOAuth $connection)
    {
        $followers_list = new ArrayObject();
        $cursor = -1;
        do {
            $get_followers = $connection->get("followers/list", ['cursor' => $cursor, 'count' => 200]);
            $followers_list->append($get_followers);
            $cursor = $get_followers->next_cursor;
        } while ($get_followers->next_cursor != 0);

        return $this->success(null, ['followers_list' => $followers_list]);
    }

    /**
     * Get a list of Friends; a list of friends are the people that a profile follows, and they are following back.
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function getFriendsList(TwitterOAuth $connection)
    {
        $friends_list = new ArrayObject();
        $cursor = -1;
        do {
            $get_friends = $connection->get("friends/list", ['cursor' => $cursor, 'count' => 20]);
            $friends_list->append($get_friends);
            $cursor = $get_friends->next_cursor;
        } while ($get_friends->next_cursor != 0);

        return $this->success(null, ['friends_list' => $friends_list]);
    }

    /**
     * Check the friendship between a profile & a target profile.
     * @param TwitterOAuth $connection
     * @param array $params (profile_id, target_name)
     * @return array|object
     */
    private function showFriendship(TwitterOAuth $connection, array $params)
    {
        $friendship_details = $connection->get("friendships/show", [
            'source_screen_name' => $params['source_screen_name'],
            'target_screen_name' => $params['target_name']
        ]);

        return $this->success(null, ['$friendship_details' => $friendship_details]);
    }

    /**
     * Show up to 100 user's details.
     * @param TwitterOAuth $connection
     * @param array $params (target_name)
     * @return array|object
     */
    private function lookupUser(TwitterOAuth $connection, array $params)
    {
        return $user = $connection->get("users/lookup", ['screen_name' => $params['target_name']]);
    }

    /**
     * Search users; get up to a 1000 search results per request.
     * @param TwitterOAuth $connection
     * @param array $params (search_query)
     * @return array|object
     */
    private function searchUsers(TwitterOAuth $connection, array $params)
    {
        return $search_result_users = $connection->get("users/search", ['q' => $params['search_query']]);
    }

    /**
     * Get a profile's account settings.
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function getAccountSetting(TwitterOAuth $connection)
    {
        $account_settings = $connection->get("account/settings");
        return $this->success(null, ['Account Settings' => $account_settings]);
    }

    /**
     * Get authenticated account's saved searches.
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function getSavedSearches(TwitterOAuth $connection)
    {
        return $saved_searches = $connection->get("saved_searches/list");
    }

    /**
     * Get authenticated account's blocked list.
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function getBlockedUsers(TwitterOAuth $connection)
    {
        $blocked_users = $connection->get("blocks/list");

        return $this->success(null, ['Blocked Users' => $blocked_users]);
    }

    /**
     * Get authenticated account's muted list.
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function getMutedUsers(TwitterOAuth $connection)
    {
        $muted_users = $connection->get("mutes/users/list");

        return $this->success(null, ['Muted Users' => $muted_users]);
    }

    /**
     * Get up to a 100 tweet's information, send tweet IDs separated by commas.
     * @param TwitterOAuth $connection
     * @param array $params (tweet_ids)
     * @return array|object
     */
    private function getTweetInfo(TwitterOAuth $connection, array $params)
    {
        return $tweet_info = $connection->get("statuses/lookup", ['id' => $params['tweet_ids']]);
    }

    /**
     * Get up to 200 tweets of authenticated account's home timeline
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function getHomeTimeline(TwitterOAuth $connection)
    {
        return $home_timline = $connection->get("statuses/home_timeline");
    }

    /**
     * Get authenticated accounts' recent 20 mentions
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function getMentions(TwitterOAuth $connection)
    {
        return $mentions_timline = $connection->get("statuses/mentions_timeline");
    }

    /**
     * Get a public user's recent tweets
     * @param TwitterOAuth $connection
     * @param array $params (screen_name)
     * @return array|object
     */
    private function getUserTimeline(TwitterOAuth $connection, array $params)
    {
        return $user_timline = $connection->get("statuses/user_timeline", ['screen_name' => $params['screen_name']]);
    }

    /**
     * Search tweets; Returns a collection of relevant Tweets matching a specified query.
     * @param TwitterOAuth $connection
     * @param array $params (search_query)
     * @return array|object
     */
    private function searchTweets(TwitterOAuth $connection, array $params)
    {
        $tweets = $connection->get("search/tweets", ['q' => $params['search_query']]);

        return $this->success(null, ['Tweets' => $tweets]);
    }

    /**
     * Returns a single Tweet, specified by the id parameter. The Tweet's author will also be embedded within the Tweet.
     * @param TwitterOAuth $connection
     * @param array $params (tweet_id)
     * @return array|object
     */
    private function getSingleTweetbyID(TwitterOAuth $connection, array $params)
    {
        $tweet = $connection->get("statuses/show", ['id' => $params['tweet_id']]);

        return $this->success(null, ['Tweet' => $tweet]);
    }

    /**
     * Returns a single Tweet, specified by either a Tweet web URL or the Tweet ID, in an oEmbed-compatible format.
     * The returned HTML snippet will be automatically recognized as an Embedded Tweet when Twitter's widget JavaScript is included on the page.
     * @param TwitterOAuth $connection
     * @param array $params (tweet_id)
     * @return array|object
     */
    private function getOEmbedSingleTweet(TwitterOAuth $connection, array $params)
    {
        return $oembed_tweet = $connection->get("statuses/oembed", ['id' => $params['tweet_id']]);
    }

    /**
     * Returns a collection of the 100 most recent retweets of the Tweet specified by the id parameter.
     * @param TwitterOAuth $connection
     * @param array $params (tweet_id)
     * @return array|object
     */
    private function getRetweetsbyTweetID(TwitterOAuth $connection, array $params)
    {
        return $retweeted = $connection->get("statuses/retweets", ['id' => $params['tweet_id']]);
    }

    /**
     * Returns the most recent Tweets authored by the authenticating user that have been retweeted by others.
     * This timeline is a subset of the user's GET statuses / user_timeline.
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function getMyRetweetsTimeline(TwitterOAuth $connection)
    {
        return $retweets_timeline = $connection->get("statuses/retweets_of_me");
    }

    /**
     * Returns a collection of up to 100 user IDs belonging to users who have retweeted the Tweet specified by the id parameter.
     * @param TwitterOAuth $connection
     * @param array $params (tweet_id)
     * @return array|object
     */
    private function getRetweetersIDs(TwitterOAuth $connection, array $params)
    {
        return $retweeters_ids = $connection->get("retweeters/ids", ['id' => $params['tweet_id']]);
    }

    /**
     * Returns a map of the available size variations of the specified user's profile banner.
     * If the user has not uploaded a profile banner, a HTTP 404 will be served instead.
     * @param TwitterOAuth $connection
     * @param array $params (user_id)
     * @return array|object
     */
    private function getProfileBanner(TwitterOAuth $connection, array $params)
    {
        return $profile_banner = $connection->get("users/profile_banner", ['user_id' => $params['user_id']]);
    }

    /**
     * Retrieve the information for the saved search represented by the given id.
     * The authenticating user must be the owner of saved search ID being requested.
     * @param TwitterOAuth $connection
     * @param array $params (search_id)
     * @return array|object
     */
    private function getSavedSearchInfo(TwitterOAuth $connection, array $params)
    {
        return $saved_searches = $connection->get("saved_searches/show", ['id' => $params['search_id']]);
    }

    /**
     * Returns the top 50 trending topics for a specific WOEID (1 for global), if trending information is available for it.
     * @param TwitterOAuth $connection
     * @param array $params (woe_id)
     * @return array|object
     */
    private function getTrendsbyWOEID(TwitterOAuth $connection, array $params)
    {
        return $woe_id_trends = $connection->get("trends/place", ['id' => $params['woe_id']]);
    }

    /**
     * Returns all the information about a known place.
     * @param TwitterOAuth $connection
     * @param array $params (place_id)
     * @return array|object
     */
    private function getPlaceInfo(TwitterOAuth $connection, array $params)
    {
        return $place_info = $connection->get("geo/id", ['place_id' => $params['place_id']]);
    }

    /*    THIS ENDPOINT REQUIRE AN ENTERPRISE ACCOUNT FROM TWITTER !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    /**
     * Returns current total impressions and engagements for a collection of up to 250 Tweets at a time.
     * @param TwitterOAuth $connection
     * @param array $params (tweet_ids, engagement_types, groupings)
     * @return array|object
     *
    private function getTweetEngagement(TwitterOAuth $connection, array $params)
    {
        return $engagement = $connection->post("insights/engagement/totals",
            [
                'tweet_ids' => $params['tweet_ids'],
                'engagement_types' => $params['engagement_types'],
                'groupings' => $params['groupings'],
            ]);
    }*/

    private function getStatisticalData(TwitterOAuth $connection)
    {
        $profile_verifications = $connection->get("account/verify_credentials");

        $recent_tweets = $connection->get("statuses/home_timeline", ['count' => 50]);

        /* This Endpoint Requires Twitter Enterprise Account! */
        /*$tweet_ids = array();
        for($k = 0; $k < count($recent_tweets); $k++){
            array_push($tweet_ids, $recent_tweets[0]->id);
        }
        $engagement_types = array(
            'favorites',
            'impressions',
            'replies',
            'retweets'
        );
        $engagement = $connection->post("insights/engagement/totals",
            [
                'tweet_ids' => implode(', ', $tweet_ids),
                'engagement_types' => implode(', ', $engagement_types),
                'groupings' => ['engagements' => ['group_by' => ['tweet.id', 'engagement_type']]],
            ], true);*/

        $followers = new ArrayObject();
        $cursor = -1;
        do {
            $get_followers = $connection->get("followers/list", ['cursor' => $cursor, 'count' => 20]);
            $followers->append($get_followers->users);
            $cursor = $get_followers->next_cursor;
        } while ($get_followers->next_cursor != 0);

        $followers_location = [];
        foreach($followers as $follower){
            $followers_location = array_merge($followers_location, $follower);
        }

        for($p = 0; $p < count($followers_location); $p++){
            $followers_location[$p] = $followers_location[$p]->location;
        }

        $followers_location_count = array_count_values($followers_location);

        unset($followers_location_count[""]);


        $home_timline = $connection->get("statuses/user_timeline", ['user_id' => '1185839642769346560','count' => 10]);

        $statistical_data = [
            'followers_count' => $profile_verifications->followers_count,
            'following_count' => $profile_verifications->friends_count,
            'outgoing_likes_count' => $profile_verifications->favourites_count,
            'tweet_count' => $profile_verifications->statuses_count,
            'engagement' => "This Endpoints Requires Enterprise Twitter!",
            'followers_location' => (array)$followers_location_count,
            'recent_tweets' => $home_timline
        ];

        return $this->success(null, $statistical_data);
    }

}
