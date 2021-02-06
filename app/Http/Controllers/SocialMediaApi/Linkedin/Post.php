<?php

namespace App\Http\Controllers\SocialMediaApi\Linkedin;

use App\Http\Controllers\ApiController;
use App\Model\AccountManager\AccountManager;
use App\Model\FileManager;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Helpers\ImageToken;
use Locale;

class Post extends ApiController
{
    protected $app_id;
    protected $app_secret;
    protected $callback;
    protected $ssl;
    protected $profile_id;
    protected $auth_token;
    protected $action_url;
    protected $account_id;
    protected $category_id;
    protected $category_type_key;
    protected $category_type_value;


    public function __construct(bool $ssl = true)
    {
        $this->app_id = env("LINKEDIN_CONSUMER_ID");
        $this->app_secret = env("LINKEDIN_CONSUMER_SECRET");
        $this->callback = env("LINKEDIN_CALLBACK_URL");
        $this->ssl = $ssl;
    }

    public function sendPost($account, $request) {

        $this->profile_id = $account->profile_id;
        $this->auth_token = $account->auth_token;
        $this->account_id = $account->id;
        $this->category_id = $account->category_id;
        $this->category_type_value = $account->category_id == 7 ? 'person' : 'organization';
        $this->category_type_key = 'author';

        error_log("key: " . $this->category_type_key ." | value: " . $this->category_type_value .  " | category id :" .$this->category_id);
        // TEXT
        if ($request["post_type"] == "text") {
            $result = $this->linkedInTextPost($request["post_caption"]);

        // MEDIA
        } else if ($request["post_type"] == "media") {
            $file_list = $request['post_data']['files'];
            $file_count = count($file_list);

            // A MEDIA
            if ($file_count == 1) {
                $result = $this->linkedInPhotoPost($request["post_caption"], $file_list[0]);
            // MULTI MEDIA
            } else {
                $result = $this->linkedInMultiplePhotosPost($request["post_caption"], $file_list);
            }

        // LINK
        } else if ($request["post_type"] == "link") {
            $result = $this->linkedInLinkPost($request["post_caption"], $request["post_data"]);
        }
        error_log($result['body']);
    }

    public function linkedInTextPost($message, $visibility = "PUBLIC")
    {
        $header = "key:".$this->category_type_key . " value:" . $this->category_type_value;
        error_log($header);
        $request = [
            "author" => "urn:li:". $this->category_type_value .":" . $this->profile_id,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $message
                    ],
                    "shareMediaCategory" => "NONE",
                ],
            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => $visibility,
            ]
        ];

        $response = Http::withToken($this->auth_token)->post("https://api.linkedin.com/v2/ugcPosts", $request);
        return [
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }

    public function linkedInLinkPost($message, $link_url, $visibility = "PUBLIC")
    {
        $link_desc = '';
        $link_title = '';

        $request = [
            "author" => "urn:li:". $this->category_type_value .":" . $this->profile_id,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $message
                    ],
                    "shareMediaCategory" => "ARTICLE",
                    "media" => [[
                        "status" => "READY",
                        "description" => [
                            "text" => substr($link_desc, 0, 200),
                        ],
                        "originalUrl" => $link_url,

                        "title" => [
                            "text" => $link_title,
                        ],
                    ]],
                ],
            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => $visibility,
            ]
        ];

        $response = Http::withToken($this->auth_token)->post("https://api.linkedin.com/v2/ugcPosts", $request);
        return [
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }

    private function imageUrl($id) {
        return FileManager::where('user_id', Auth::id())->where('id', $id)->first();
    }

    public function linkedInPhotoPost($message, $file_id, $visibility = "PUBLIC")
    {
        $accessToken = $this->auth_token;
        $person_id = $this->profile_id;
        $file_info = $this->imageUrl($file_id);
        $image_path = ImageToken::getToken($file_info->url);
        $image_title = "";
        $image_description = "";

        $prepareRequest = [
            "registerUploadRequest" => [
                "recipes" => [
                    "urn:li:digitalmediaRecipe:feedshare-image"
                ],
                "owner" => "urn:li:". $this->category_type_value .":" . $person_id,
                "serviceRelationships" => [
                    [
                        "relationshipType" => "OWNER",
                        "identifier" => "urn:li:userGeneratedContent"
                    ],
                ],
            ],
        ];
        $prepareReponse = Http::withToken($this->auth_token)->post("https://api.linkedin.com/v2/assets?action=registerUpload", $prepareRequest)->body();
        error_log($prepareReponse);
        $uploadURL = json_decode($prepareReponse)->value->uploadMechanism->{"com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest"}->uploadUrl;
        $asset_id = json_decode($prepareReponse)->value->asset;
        error_log("uploadURL: " . $uploadURL);
        error_log("asset_id: ". $asset_id);
        $client = new Client();
        $client->request('PUT', $uploadURL, [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'body' => fopen($image_path, 'r'),
        ]);

        $request = [
            "author" => "urn:li:". $this->category_type_value .":" . $person_id,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $message,
                    ],
                    "shareMediaCategory" => "IMAGE",
                    "media" => [[
                        "status" => "READY",
                        "description" => [
                            "text" => substr($image_description, 0, 200),
                        ],
                        "media" => $asset_id,
                        "title" => [
                            "text" => $image_title,
                        ],
                    ]],
                ],
            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => $visibility,
            ]
        ];

        $post = Http::withHeaders(['Content-Type' => 'application/json'])->withToken($this->auth_token)->post("https://api.linkedin.com/v2/ugcPosts", $request);
        error_log("post: ".$post->body());
        return [
            'status' => $post->status(),
            'body' => $post->body(),
        ];

    }

    public function linkedInMultiplePhotosPost($message, array $images, $visibility = "PUBLIC")
    {
        $person_id = $this->profile_id;
        // Posting
        $request = [
            "author" => "urn:li:". $this->category_type_value .":" . $person_id,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $message
                    ],
                    "shareMediaCategory" => "IMAGE",
                    "media" => [],
                ],

            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => $visibility,
            ]
        ];

        // Adding Medias
        $media = [];
        foreach ($images as $key => $image) {

            $file_info = $this->imageUrl($image);
            $image_path = ImageToken::getToken($file_info->url);

            // Preparing Request
            $prepareRequest =  [
                "registerUploadRequest" => [
                    "recipes" => [
                        "urn:li:digitalmediaRecipe:feedshare-image"
                    ],
                    "owner" => "urn:li:". $this->category_type_value .":" . $person_id,
                    "serviceRelationships" => [
                        [
                            "relationshipType" => "OWNER",
                            "identifier" => "urn:li:userGeneratedContent"
                        ],
                    ],
                ],
            ];
            $prepareReponse = Http::withToken($this->auth_token)->post("https://api.linkedin.com/v2/assets?action=registerUpload", $prepareRequest)->body();
            $uploadURL = json_decode($prepareReponse)->value->uploadMechanism->{"com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest"}->uploadUrl;

            $asset_id = json_decode($prepareReponse)->value->asset;
            error_log("asset_id ". $asset_id);
            $client = new Client();
            $client->request('PUT', $uploadURL, [
                'headers' => ['Authorization' => 'Bearer ' . $this->auth_token],
                'body' => fopen($image_path, 'r'),
                'verify' => $this->ssl
            ]);
            $media[$key]["status"] = "READY";
            $media[$key]["description"]["text"] = '';
            $media[$key]["media"] = $asset_id;
            $media[$key]["title"]["text"] = md5(rand(1,9));
        }
        $request['specificContent']['com.linkedin.ugc.ShareContent']["media"] = array_values($media);
        $post = Http::withToken($this->auth_token)->post("https://api.linkedin.com/v2/ugcPosts", $request);

        error_log("post ". $post);
        return [
            'status' => $post->status(),
            'body' => $post->body(),
        ];
    }

    public function getPerson($accessToken)
    {
        $post = Http::withHeaders(['Content-Type' => 'application/json'])->withToken($accessToken)->get("https://api.linkedin.com/v2/me");
        return [
            'status' => $post->status(),
            'body' => json_decode($post->body()),
        ];
    }
    public function getPersonProfileImage($accessToken)
    {
        $post = Http::withHeaders(['Content-Type' => 'application/json'])->withToken($accessToken)->get("https://api.linkedin.com/v2/me?projection=(id,profilePicture(displayImage~digitalmediaAsset:playableStreams))");
        return [
            'status' => $post->status(),
            'body' => json_decode($post->body()),
        ];
    }

    private function actionDetails($id) {
        $action = AccountManager::where("id", $id)->first();
        if ($action) {
            $this->auth_token = $action->auth_token;
            return $action;
        } else {
            return null;
        }
    }

    public function companyPageStatistics(Request $request) {

        $auth_user_manager = $this->actionDetails($request->action_id);
        if (!$auth_user_manager) {
            return $this->fail('User not found');
        }

        $post = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withToken($this->auth_token)
            ->get("https://api.linkedin.com/v2/organizationalEntityShareStatistics?q=organizationalEntity&organizationalEntity=urn:li:organization:".$request->organization);

        $body = json_decode($post->body());
        $body = (array) $body->elements[0];
        $body = (array) $body["totalShareStatistics"];

        $organization = (string) $request->organization;

        $followerCountsByCountry = $this->companyFollowerStatistics($organization);
        $newFollowerCountry = [];
        $newFollowerCount = 0;

        $followers_count = $this->companyFollowersCount($organization);

        foreach ($followerCountsByCountry as $data) {
            $dizi = explode (":",$data->country);
            $key_name = Locale::getDisplayRegion('-US', end($dizi));
            $newFollowerCount += $data->followerCounts->organicFollowerCount;

            array_push($newFollowerCountry, [
                $key_name => $data->followerCounts->organicFollowerCount
            ]);
        }

        array_push($newFollowerCountry, [
            "Others" => $followers_count - $newFollowerCount
        ]);

        $new_data = [
            "followers_count" => $followers_count,
            "likes_count" => $body["likeCount"],
            "seen_count" => $body["impressionCount"],
            "comments_count" => $body["commentCount"],
            "shares_count" => $body["shareCount"],
            "company_posts" => $this->companyPosts($organization),
            "followerCountsByCountry" => $newFollowerCountry
        ];
        return [
            'status' => $post->status(),
            'body' => $new_data,
        ];
    }

    public function companyFollowersCount($organization) {
        $post = Http::withHeaders(['Content-Type' => 'application/json'])->withToken($this->auth_token)->get("https://api.linkedin.com/v2/networkSizes/urn:li:organization:". $organization . "?edgeType=CompanyFollowedByMember");
        $body = $post["firstDegreeSize"];
        return $body;
    }

    public function companyPosts($organization) {
        $post = Http::withHeaders(['Content-Type' => 'application/json'])->withToken($this->auth_token)->get("https://api.linkedin.com/v2/shares?q=owners&owners=urn:li:organization:".$organization."&sortBy=LAST_MODIFIED&sharesPerOwner=100");

        $post = json_decode($post->body());
        $post = (array)$post->elements;

        $statictics_data = [];

        $profile_image = $this->getPersonProfileImage($this->auth_token)["body"];
        $profile_image = (array)$profile_image->profilePicture;
        $profile_image = $profile_image["displayImage~"]->elements[0]->identifiers[0]->identifier;

        foreach ($post as $data) {
            array_push($statictics_data,
            [
                'actor' => $data->created->actor,
                'id' => $data->id,
                'text' => $data->text->text,
                'profile_image' => $profile_image
            ]);
        }
        return $statictics_data;
    }

    public function companyFollowerStatistics($organization) {
        $post = Http::withHeaders(['Content-Type' => 'application/json'])->withToken($this->auth_token)->get("https://api.linkedin.com/v2/organizationalEntityFollowerStatistics?q=organizationalEntity&organizationalEntity=urn:li:organization:".$organization);

        $body = json_decode($post->body());
        $body = (array) $body->elements;

        return $body[0]->followerCountsByCountry;
    }

    public function companyPageDetailStatistics(Request $request) {
        $auth_user_manager = $this->actionDetails($request->action_id);

        if ($auth_user_manager) {
            $this->auth_token = $auth_user_manager->auth_token;
        } else {
            return $this->fail('User not found');
        }


    }

    public function companyPageCommentDelete() {

        $accessToken = "AQUQh6BczJPR-cxMRaZ8VY1sLASxPMLcCc6qix3AbZvfP_NZHIZQ3-oaFCRAaf0dxkWt3mn7TpuxQp0w6ADclHm-BcpCWYdR2HmL_KoaB0UXrNXbSIgrXTWdubEZiAd3vzJPA4cVc3_dRYF2WVBO1sVs711d3rlGMHHT-Jhm7z9wIiBPK34SeuvFkqLDDCWVSdS0zsdPoqpaEosTda6cmE7y8V_ymw9Xf47YT8Z5Cw2_3vodPX40aI2bEZINpWP0Rxlkkg9NewjBECwisSgPtFDje3IfOxip3ru4GZJ7XrcFODtKel0IoWAH1ZxarckcYk7E1tPReJsDuQ1dws81D_OFTdpkqw";
        $post = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withToken($accessToken)
            ->delete("https://api.linkedin.com/v2/socialActions/urn:li:activity:6703977323793805312/comments/6706462811935019008", array_values(["actor"=>"urn:li:organization:67885139"]));
        return [
            'status' => $post->status(),
            'body' => json_decode($post->body()),
        ];
    }

    public function companyPostCommentWrite(Request $request) {

        $auth_user_manager = $this->actionDetails($request->action_id);
        if (!$auth_user_manager) {
            return $this->fail('User not found');
        }

        $request = [
            "actor" => $request->organization, // urn:li:organization:67885139
            "object" => $request->activity, // urn:li:activity:6703977323793805312
            "message" => [
                "text" => $request->message
            ]
        ];

        $post = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withToken($this->auth_token)
            ->post("https://api.linkedin.com/v2/socialActions/urn:li:activity:6703977323793805312/comments", $request);

        return [
            'status' => $post->status(),
            'body' => json_decode($post->body()),
        ];
    }

    public function companyPostCommentSub($activityId) {

        $accessToken = "AQUQh6BczJPR-cxMRaZ8VY1sLASxPMLcCc6qix3AbZvfP_NZHIZQ3-oaFCRAaf0dxkWt3mn7TpuxQp0w6ADclHm-BcpCWYdR2HmL_KoaB0UXrNXbSIgrXTWdubEZiAd3vzJPA4cVc3_dRYF2WVBO1sVs711d3rlGMHHT-Jhm7z9wIiBPK34SeuvFkqLDDCWVSdS0zsdPoqpaEosTda6cmE7y8V_ymw9Xf47YT8Z5Cw2_3vodPX40aI2bEZINpWP0Rxlkkg9NewjBECwisSgPtFDje3IfOxip3ru4GZJ7XrcFODtKel0IoWAH1ZxarckcYk7E1tPReJsDuQ1dws81D_OFTdpkqw";
        $post = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withToken($accessToken)
            ->get("https://api.linkedin.com/v2/socialActions/". $activityId ."/comments");
        $body = json_decode($post->body());
        $body = $body->elements;

        return [
            'status' => $post->status(),
            'body' => $body,
        ];
    }

    public function companyPostComments(Request $request) {
        $auth_user_manager = $this->actionDetails($request->action_id);
        if (!$auth_user_manager) {
            return $this->fail('User not found');
        }

        $post = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withToken($this->auth_token)
            ->get("https://api.linkedin.com/v2/socialActions/".$request->activity."/comments"); // urn:li:activity:6703977323793805312
        $body = json_decode($post->body());
        $body = $body->elements;

        foreach ($body as $data) {
            $datas = (array)$data;
            $data->comment_sub = $this->companyPostCommentSub($datas["\$URN"]);
        }
        return [
            'status' => $post->status(),
            'body' => $body,
        ];
    }
}
