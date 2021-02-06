<?php

namespace App\Http\Controllers\API\AccountManager;

use App\Http\Controllers\ApiController;
use App\Http\Requests\AccountManager\GroupManagerCreateRequest;
use App\Model\AccountManager\AccountManager;
use App\Model\AccountManager\GroupManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupManagerController extends ApiController
{
    public function create(GroupManagerCreateRequest $request) {
        $group_new_create = GroupManager::create([
            'user_id' => Auth::id(),
            'list' => $request->list2,
            'group_name' => $request->groupName
        ]);
        if ($group_new_create) {
            return $this->success('OK', [$group_new_create]);
        } else {
            return $this->fail('Failed');
        }
    }

    public function getGroupName() {
        $group_name = GroupManager::select("group_name as title", "id as slug", "list as list2")->where("user_id", Auth::id())
            ->get();
        error_log($group_name);
        if ($group_name) {
            foreach ($group_name as $data) {
                $data["isActive"] = false;
            }
            return $this->success('OK', ['list' => $group_name]);
        } else {
            return $this->fail('Failed');
        }
    }

    public function getAvailableAccount(Request $request) {

        $accounts = AccountManager::select(
            'account_manager.id',
            'status',
            'account_manager.social_network_id',
            'account_manager.name',
            'username',
            'profile_id',
            'avatar_url',
            'account_url',
            'category',
            'data')
            ->where('user_id', Auth::id())
            ->join('account_category', 'category_id', '=', 'account_category.id')
            ->get();

        $group_manager_list = GroupManager::where("id", $request->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($group_manager_list) {
            $selection_list = $group_manager_list->list;

            foreach ($accounts as $key => $data) {
                foreach ($selection_list as $selection_data) {
                    if ($selection_data["id"] == $data["id"]) {
                        unset($accounts[$key]);
                    }
                }
            }
            $new_data = [];
            foreach ($accounts as $data_new) {
                array_push($new_data, $data_new);
            }
            return $new_data;
        } else {
            return $this->fail("Not found");
        }
    }

    public function update(Request $request) {
        $checkGroup = GroupManager::where('id', $request->id)->where("user_id", Auth::id())->first();
        if ($checkGroup) {
            $checkGroup->update([
                'group_name' => $request->groupName,
                'list' => $request->list2
            ]);
            return $this->success('OK');
        } else {
            return $this->fail('Group not found');
        }
    }

    public function delete(Request $request) {
        $checkGroup = GroupManager::where('id', $request->id)->where("user_id", Auth::id())->first();
        if ($checkGroup) {
            $checkGroup->delete();
            return $this->success('OK');
        } else {
            return $this->fail('Group not found');
        }
    }
}
