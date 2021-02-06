<?php

namespace App\Http\Controllers\API\DirectMessage;

require(app_path('Http/libraries/vendor/autoload.php'));

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use InstagramAPI\Exception\InstagramException;
use InstagramAPI\Instagram;
use Exception;

class MessageController extends ApiController
{
    private function login()
    {
        try {

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

            $ig->login('username', 'password');
            return $ig;
        } catch (InstagramException $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function inbox($cursor = NULL)
    {
        try {

            $ig = $this->login();
            $inbox = $ig->direct->getInbox($cursor, 20)->getInbox();

            $messages = [];
            foreach (json_decode($inbox)->threads as $thread) {
                $messages[] = [
                    'id' => $thread->thread_id,
                    'last_activity' => $thread->last_activity_at,
                    'new_message' => $thread->read_state,
                    'message' => $thread->items,
                    'users' => $thread->users
                ];
            }
            return $this->success(null, ['chat_users' => $messages]);


        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function col_message(Request $request)
    {
        try {
            $ig = $this->login();
            $userdata = $ig->account->getCurrentUser();
            $chat = $ig->direct->getThread($request->id, $cursor = NULL)->getThread();

            $custom_arr = [
                'id' => json_decode($chat)->thread_id,
                'title' => json_decode($chat)->thread_title,
                'messages' => json_decode($chat)->items,
                'users' => json_decode($chat)->users,
                'user' => [
                    'pk' => json_decode($userdata)->user->pk,
                    'full_name' => json_decode($userdata)->user->full_name,
                    'profile_pic_url' => json_decode($userdata)->user->profile_pic_url
                ]
            ];
            return $this->success(null, ['messages' => $custom_arr]);

        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function send_message(Request $request)
    {
        if(strlen($request->threadID) > 15){
            $thread = ["thread" => $request->threadID];
        }
        else{
            $thread = ["users" => [$request->threadID]];
        }
        try {
            $ig = $this->login();

            $result = $ig->direct->sendText($thread, $request->message);
            $id = $result->getPayload()->getThreadId();
            return $this->success();
        }
        catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
