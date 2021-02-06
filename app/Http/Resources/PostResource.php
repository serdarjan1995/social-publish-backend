<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'post_id' => $this->id,
            'user_id' => $this->user_id,
            'account_id' => $this->account_id,
            'account_name' => $this->name,
            'account_url' => $this->account_url,
            'social_id' => $this->social_network_id,
            'caption' => $this->post_caption,
            'media' => $this->post_data,
            'type' => $this->post_type,
            'timer' => $this->post_schedule,
            'status' => $this->post_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
