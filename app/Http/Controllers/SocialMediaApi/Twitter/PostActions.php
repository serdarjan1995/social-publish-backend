<?php


namespace App\Http\Controllers\SocialMediaApi\Twitter;


use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Controllers\ApiController;
use App\Model\AccountManager\AccountManager;
use Illuminate\Http\Request;

class PostActions extends ApiController
{
    /**
     * @param Request $request
     * action: destroyTweet, params: [tweet_id]
     * action: retweet, params: [tweet_id]
     * action: unretweet, params: [tweet_id]
     * action: addFavourite, params: [tweet_id]
     * action: removeFavourite, params: [tweet_id]
     * @return string
     */
    public function twitterPostActionsHandler(Request $request)
    {
        $connection = $this->connectToTwitter($request->account_id);

        switch ($request->action) {
            case 'destroyTweet':
                $result = $this->destroyTweet($request->params, $connection);
                break;
            case 'retweet':
                $result = $this->retweet($request->params, $connection);
                break;
            case 'unretweet':
                $result = $this->unretweet($request->params, $connection);
                break;
            case 'addFavourite':
                $result = $this->addFavourite($request->params, $connection);
                break;
            case 'removeFavourite':
                $result = $this->removeFavourite($request->params, $connection);
                break;
            default:
                $result = $this->success("No Such Action, Read the JDOCS in order to know what actions are available.");

        }
        return $result;
    }

    /**
     * Connects a registered profile ID to twitter.
     * @param int $account_id
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
     * Destroys the status specified by the required ID parameter.
     * The authenticating user must be the author of the specified status.
     * Returns the destroyed status if successful.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function destroyTweet(array $params, TwitterOAuth $connection)
    {
        return $destroyed_tweet = $connection->post("statuses/destroy", ['id' => $params['tweet_id']]);
    }

    /**
     * Retweets a tweet. Returns the original Tweet with Retweet details embedded.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function retweet(array $params, TwitterOAuth $connection)
    {
        return $retweeted = $connection->post("statuses/retweet", ['id' => $params['tweet_id']]);
    }

    /**
     * Untweets a retweeted status. Returns the original Tweet with Retweet details embedded.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function unretweet(array $params, TwitterOAuth $connection)
    {
        return $unretweeted = $connection->post("statuses/unretweet", ['id' => $params['tweet_id']]);
    }

    /**
     * Note: favorites are now known as likes.
     * Favorites (likes) the Tweet specified in the ID parameter as the authenticating user.
     * Returns the favorite Tweet when successful. The process invoked by this method is asynchronous.
     * The immediately returned Tweet object may not indicate the resultant favorited status of the Tweet.
     * A 200 OK response from this method will indicate whether the intended action was successful or not.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function addFavourite(array $params, TwitterOAuth $connection)
    {
        return $favourite = $connection->post("favorites/create", ['id' => $params['tweet_id']]);
    }

    /**
     * Note: favorites are now known as likes.
     * Unfavorites (un-likes) the Tweet specified in the ID parameter as the authenticating user.
     * Returns the un-liked Tweet when successful. The process invoked by this method is asynchronous.
     * he immediately returned Tweet object may not indicate the resultant favorited status of the Tweet.
     * A 200 OK response from this method will indicate whether the intended action was successful or not.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function removeFavourite(array $params, TwitterOAuth $connection)
    {
        return $destroyed_favourite = $connection->post("favorites/destroy", ['id' => $params['tweet_id']]);
    }

}
