<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InstagramAccountsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'user_id' => $this->user->pk,
            'username' => $this->user->username,
            'full_name' => $this->user->full_name,
            'profile_pic_url' => $this->user->profile_pic_url,
            'biography' => $this->user->biography,
            'birthday' => $this->user->birthday,
            'phone_number' => $this->user->phone_number,
            'country_code' => $this->user->country_code,
            'national_number' => $this->user->national_number,
            'gender' => $this->user->gender,
            'email' => $this->user->email,
            'is_private' => $this->user->is_private,
            'is_business' => $this->user->is_business,
            'account_type' => $this->user->account_type,
        ];
    }
}
