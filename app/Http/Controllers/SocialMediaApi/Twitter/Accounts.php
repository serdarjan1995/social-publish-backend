<?php

namespace App\Http\Controllers\SocialMediaApi\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Controllers\ApiController;
use App\Model\AccountManager\AccountManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class Accounts extends ApiController
{
    /**
     * @param Request $request (profile_id, action, params)
     * action: editProfileBanner, params: [banner, width, height, offset_left, offset_top]
     * action: removeProfileBanner, params: null
     * action: editAccountSettings, params: [sleep_time_enabled, start_sleep_time, end_sleep_time, time_zone, trend_location_woeid, lang]
     * action: editProfileInfo, params: [name, url, location, description, profile_link_color, include_entities, skip_status]
     * action: updateProfileImage, params: [image]
     * action: createSavedSearch, params: [query]
     * action: destroySavedSearch, params: [saved_search_id]
     * action: blockUser, params: [screen_name]
     * action: unblockUser params: [screen_name]
     * action: muteUser, params: [screen_name]
     * action: unmuteUser, params: [screen_name]
     * action: reportUserSpam, params: [screen_name, perform_block]
     * action: createFriendship, params: [screen_name]
     * action: unfriendUser, params: [screen_name]
     * action: updateFriendship, params: [screen_name]
     * action: getDirectMessages, params: []
     * action: sendDirectMessage, params: [target_id, message]
     * @return string
     */
    public function twitterAccountHandler(Request $request)
    {
        $connection = $this->connectToTwitter($request->account_id);

        switch ($request->action) {
            case 'editProfileBanner':
                $result = $this->editProfileBanner($request->params, $connection);
                break;
            case 'removeProfileBanner':
                $result = $this->removeProfileBanner($connection);
                break;
            case 'editAccountSettings':
                $result = $this->editAccountSettings($request->params, $connection);
                break;
            case 'editProfileInfo':
                $result = $this->editProfileInfo($request->params, $connection);
                break;
            case 'updateProfileImage':
                $result = $this->updateProfileImage($request->params, $connection);
                break;
            case 'createSavedSearch':
                $result = $this->createSavedSearch($request->params, $connection);
                break;
            case 'destroySavedSearch':
                $result = $this->destroySavedSearch($request->params, $connection);
                break;
            case 'blockUser':
                $result = $this->blockUser($request->params, $connection);
                break;
            case 'unblockUser':
                $result = $this->unblockUser($request->params, $connection);
                break;
            case 'muteUser':
                $result = $this->muteUser($request->params, $connection);
                break;
            case 'unmuteUser':
                $result = $this->unmuteUser($request->params, $connection);
                break;
            case 'reportUserSpam':
                $result = $this->reportUserSpam($request->params, $connection);
                break;
            case 'createFriendship':
                $result = $this->createFriendship($request->params, $connection);
                break;
            case 'unfriendUser':
                $result = $this->unfriendUser($request->params, $connection);
                break;
            case 'updateFriendship':
                $result = $this->updateFriendship($request->params, $connection);
                break;
            case 'getDirectMessages':
                $result = $this->getDirectMessages($connection);
                break;
            case 'sendDirectMessage':
                $result = $this->sendDirectMessage($request->params, $connection);
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
     * Uploads a profile banner on behalf of the authenticating user.
     * @param array $params (banner, width, height, offset_left, offset_top)
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function editProfileBanner(array $params, TwitterOAuth $connection)
    {
        return $result = $connection->post("account/update_profile_banner", [
            'banner' => $params['banner'],
            'width' => $params['width'],
            'height' => $params['height'],
            'offset_left' => $params['offset_left'],
            'offset_top' => $params['offset_top']
        ]);
    }

    /**
     * Removes the uploaded profile banner for the authenticating user. Returns HTTP 200 upon success.
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function removeProfileBanner(TwitterOAuth $connection)
    {
        return $result = $connection->post("account/remove_profile_banner");
    }

    /**
     * Updates the authenticating user's settings.
     * @param array $array
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function editAccountSettings(array $array, TwitterOAuth $connection)
    {
        return $account_settings = $connection->post("account/settings", [
            'sleep_time_enabled' => $array['sleep_time_enabled'],
            'start_sleep_time' => $array['start_sleep_time'],
            'end_sleep_time' => $array['end_sleep_time'],
            'time_zone' => $array['time_zone'],
            'trend_location_woeid' => $array['trend_location_woeid'],
            'lang' => $array['lang'],
        ]);
    }

    /**
     * Sets some values that users are able to set under the "Account" tab of their settings page.
     * Only the parameters specified will be updated.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function editProfileInfo(array $params, TwitterOAuth $connection)
    {
        return $profile = $connection->post("account/update_profile", [
            'name' => $params['name'],
            'url' => $params['url'],
            'location' => $params['location'],
            'description' => $params['description'],
            'profile_link_color' => $params['profile_link_color'],
            'include_entities' => $params['include_entities'],
            'skip_status' => $params['skip_status'],
        ]);
    }

    /**
     * Updates the authenticating user's profile image. Note that this method expects raw multipart data, not a URL to an image
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function updateProfileImage(array $params, TwitterOAuth $connection)
    {
        return $profile = $connection->post("account/update_profile_image", ['image' => $params['image'],]);
    }

    /**
     * Create a new saved search for the authenticated user. A user may only have 25 saved searches.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function createSavedSearch(array $params, TwitterOAuth $connection)
    {
        return $saved_search = $connection->post("saved_searches/create", ['query' => $params['query'],]);
    }

    /**
     * Destroys a saved search for the authenticating user. The authenticating user must be the owner of saved search id being destroyed.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function destroySavedSearch(array $params, TwitterOAuth $connection)
    {
        return $destroyed_saved_search = $connection->post("saved_searches/destroy", ['id' => $params['saved_search_id'],]);
    }

    /**
     * Blocks the specified user from following the authenticating user.
     * In addition the blocked user will not show in the authenticating users mentions or timeline (unless retweeted by another user).
     * If a follow or friend relationship exists it is destroyed.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function blockUser(array $params, TwitterOAuth $connection)
    {
        return $user = $connection->post("blocks/create", ['screen_name' => $params['screen_name']]);
    }

    /**
     * Un-blocks the user specified in the ID parameter for the authenticating user.
     * Returns the un-blocked user when successful.
     * If relationships existed before the block was instantiated, they will not be restored.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function unblockUser(array $params, TwitterOAuth $connection)
    {
        return $user = $connection->post("blocks/destroy", ['screen_name' => $params['screen_name']]);
    }

    /**
     * Mutes the user specified in the ID parameter for the authenticating user.
     * Returns the muted user when successful.
     * Returns a string describing the failure condition when unsuccessful.
     * Actions taken in this method are asynchronous. Changes will be eventually consistent.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function muteUser(array $params, TwitterOAuth $connection)
    {
        return $user = $connection->post("mutes/users/create", ['screen_name' => $params['screen_name']]);
    }

    /**
     * Un-mutes the user specified in the ID parameter for the authenticating user.
     * Returns the unmuted user when successful. Returns a string describing the failure condition when unsuccessful.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function unmuteUser(array $params, TwitterOAuth $connection)
    {
        return $user = $connection->post("mutes/users/destroy", ['screen_name' => $params['screen_name']]);
    }

    /**
     * Report the specified user as a spam account to Twitter.
     * Additionally, optionally performs the equivalent of POST blocks/create on behalf of the authenticated user.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function reportUserSpam(array $params, TwitterOAuth $connection)
    {
        return $user = $connection->post("mutes/users/destroy", [
            'screen_name' => $params['screen_name'],
            'perform_block' => $params['perform_block']
        ]);
    }

    /**
     * Allows the authenticating user to follow (friend) the user specified in the ID parameter.
     * Returns the followed user when successful.
     * Returns a string describing the failure condition when unsuccessful.
     * If the user is already friends with the user a HTTP 403 may be returned,
     * though for performance reasons this method may also return a HTTP 200 OK message even if the follow relationship already exists.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function createFriendship(array $params, TwitterOAuth $connection)
    {
        return $friend = $connection->post("friendships/create", ['screen_name' => $params['screen_name']]);
    }

    /**
     * Allows the authenticating user to unfollow the user specified in the ID parameter.
     * Returns the unfollowed user when successful. Returns a string describing the failure condition when unsuccessful.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function unfriendUser(array $params, TwitterOAuth $connection)
    {
        return $friend = $connection->post("friendships/destroy", ['screen_name' => $params['screen_name']]);
    }

    /**
     * Enable or disable Retweets and device notifications from the specified user.
     * @param array $params
     * @param TwitterOAuth $connection
     * @return array|object
     */
    private function updateFriendship(array $params, TwitterOAuth $connection)
    {
        return $relationship = $connection->post("friendships/update", ['screen_name' => $params['screen_name']]);
    }

    /**
     * Returns all Direct Message events (both sent and received) within the last 30 days. Sorted in reverse-chronological order.
     * @param Request $request
     * @return mixed
     */
    private function getDirectMessages(TwitterOAuth $connection)
    {
        /*$messages = new ArrayObject();
        $cursor = -1;

        do{
            $get_messages = $connection->get("direct_messages/events/list", ['cursor' => $cursor]);
            $cursor = $get_messages->next_cursor;
            $messages->append($get_messages);
        }while($get_messages->next_cursor != 0);

        return $this->success(null, ['messages' => $messages]);*/

        $messages = $connection->get('direct_messages/events/list', ['cursor'=>'MTI5ODE4NTY3OTU4MzgwOTU0MQ']);

        return $this->success(null, ['messages' => $messages]);

    }

    /**
     * Sends a direct message to a recipient
     * @param Request $request (target_id, message)
     * @return JsonResponse
     */
    public function sendDirectMessage(array $params, TwitterOAuth $connection)
    {
        $message_details = [
            'type' => 'message_create',
            'message_create' => [
                'target' => [
                    'recipient_id' => $params['target_id'],
                ],
                'message_data' => [
                    'text' => $params['message']
                ]
            ]
        ];

        $message = $connection->post("direct_messages/events/new", ['event' => $message_details], true);

        return $this->success(null, ['message' => $message]);
    }
}
