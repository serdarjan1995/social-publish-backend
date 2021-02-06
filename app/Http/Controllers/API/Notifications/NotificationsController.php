<?php

namespace App\Http\Controllers\API\Notifications;

use App\Http\Controllers\ApiController;
use App\Model\Notifications;
use App\Model\SocialNetwork;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationsController extends ApiController
{
    public function getAuthNotifications() {
        $notification       =   Notifications::orderByDesc('created_at')->where('user_id', Auth::id())->get();
        $endNotificationId  =   Auth::user()->notification_read;
        foreach ($notification as $data) {
            $data["urlpath"] = "";
            $data["body"] = date("d-m-Y", strtotime($data["created_at"]));
            $data["action"] = $this->notificationType($data["type"]);

            // Social Media User Profile Icon Get Api
            $data["sendUserImage"] = "https://1.bp.blogspot.com/-Vg16yzr6gik/XAKnw7yiKMI/AAAAAAAANoo/Bj11ov3KV3QpICBDS88D97bNugNzQr3VACLcBGAs/s1600/whatsapprofilresimleri%2B%25281%2529.jpg";
            $data["sendUserName"] = "Gürkan Güney";
            $data["sendUserId"] = "c790eeaa-2632-4acb-a4f8-ef30eef3c790";

            // Input Text
            $data["messageType"] = true;

            $data["avatar"] = [
                "icon" =>  $this->socialNetworkId($data["type"])->icon
            ];
            if ($data['id'] > $endNotificationId) {
                $data['new'] = true;
            } else {
                $data['new'] = false;
            }
        }
        /*
                "id": 1,
                "user_id": "33d967ad-af61-4269-afbf-2cab5cff01ed",
                "title": "Facebook",
                "desc": "Lorem ipsum dolor sit amet, consectetur.",
                "type": 1,
                "extra_details": "Facebook",
                "status": 0,
                "read": 0,
                "created_at": "2020-08-09T17:36:34.000000Z",
                "updated_at": null,
                "urlpath": "",
                "actions": {
                    "chip": "Facebook",
                    "style": {
                        "small": true,
                        "color": "#3B5998",
                        "textColor": "white"
                    }
                },
                "new": true,
                "created_text": "15 gün önce"
         *
            title: users[4].name,
            desc: 'Lorem ipsum dolor sit amet, consectetur.',
            urlpath: "demo/test",
            body: format(subDays(new Date(), 8), "MMM dd, yyyy"),
            avatar: {
                src: users[4].avatar,
            },
            action: {
                chip: "Instagram",
                style: {
                    small: true,
                    color: "green",
                    textColor: "white",
                },
            },
         */
        return $this->success('Success', ['notifications' => $notification, 'count' => count($notification) ]);
    }
    public function endNotification() {

    }
    public function socialNetworkId($id) {
        return SocialNetwork::where('id', $id)->first();
    }
    public function notificationType($type) {
        $details = $this->socialNetworkId($type);
        return [
            'chip' => $details->name,
            'style' => [
                'small' => true,
                'color' => $details->color,
                'textColor' => 'white',
                'icon' => $details->icon,
                'name' => $details->name,
                'id' => $details->id
            ]
        ];
    }
    public function getNotificationCount() {
        $get_all = Notifications::where('user_id', Auth::id())->where('read', 0)->get();
        return count($get_all);
    }
    public function readAuthNotification(){
        $update = Notifications::where('user_id', Auth::id())
            ->update([
                'read' => 1
            ]);
        if ($update) {
            return $this->success('Success');
        } else {
            $this->fail('Database not found');
        }
    }
    public function endAuthNotification(Request $request) {
        $endNotiId  = Notifications::where('user_id', Auth::id())->orderBy('desc')->first();
        $endNotification    =   User::where('id', Auth::id())->update([
            'notification_read' => $endNotiId->id
        ]);
        if ($endNotification) {
            return $this->success('Sucess');
        } else {
            return $this->fail('Failed');
        }
    }
    public function createNotification(Request $request){

        $isUser = User::where('id', Auth::id())->first();
        if ($isUser) {
            $createNotification = Notifications::create([
                'id' => Str::uuid(),
                'user_id' => $request->send_id,
                'send_id' => $isUser->id,
                'message' => $request->message,
                'extra_details' => "",
                'type' => $request->type,
            ]);
            if ($createNotification) {
                return $this->success('OK', ['create' => $createNotification]);
            } else {
                return $this->fail('Fail');
            }
        } else {
            return $this->fail("User not found");
        }
    }

    public function timeText($value) {
        $SaniyeHesaplamaBirSaniye = 1;
        $SaniyeHesaplamaBirDakika = 60;
        $SaniyeHesaplamaBirSaat = 3600;
        $SaniyeHesaplamaBirGun = 86400;
        $SaniyeHesaplamaBirAy = 2592000;
        $SaniyeHesaplamaBirYil = 31536000;

        //$baslangic          =   strtotime('2015-02-23 08:00:00');
        $baslangic = strtotime($value);
        $bitis = strtotime(date('d-m-Y H:i:s'));

        $fark = abs($bitis - $baslangic);

        // ceil()  : yukarı yuvarla..
        // floor() : aşağı yuvarla..

        //-> Bir dakikadan az..
        if ($fark < $SaniyeHesaplamaBirDakika) {
            $result = $fark . " saniye önce";

        //-> Bir saattan az..
        } else if ($fark < $SaniyeHesaplamaBirSaat) {
            $result = $fark / $SaniyeHesaplamaBirDakika;
            $result = ceil($result) . " dakika önce";

        //-> Bir günden daha az..
        } else if ($fark < $SaniyeHesaplamaBirGun) {
            $result = $fark / $SaniyeHesaplamaBirSaat;
            $result = floor($result) . " saat önce";

        //-> Bir aydan daha az..
        } else if ($fark < $SaniyeHesaplamaBirAy) {
            $result = $fark / $SaniyeHesaplamaBirGun;
            $result = floor($result) . " gün önce";

        //-> Bir yıldan daha az..
        } else if ($fark < $SaniyeHesaplamaBirYil) {
            $result = $fark / $SaniyeHesaplamaBirAy;
            $result = floor($result) . " ay önce";

        //-> Bir yıldan daha fazla..
        } else if ($fark >= $SaniyeHesaplamaBirYil) {
            //            $result         =   $fark / $SaniyeHesaplamaBirYil;
            //          $result         =   floor($result) . " yıldan daha fazla";
            $result = "Bir yıldan daha fazla..";
        } else {
            $result = "Tanımsız bir değer.";
        }

        return $result;
    }
}
