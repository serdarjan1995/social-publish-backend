<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CalendarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->post_schedule != NULL){
            $scheduleJsonDecoded = json_decode($this->post_schedule);
            $customDate = date('Y-m-d', strtotime($scheduleJsonDecoded->post_time));
        }
        else{
            $customDate = date('Y-m-d', strtotime($this->created_at));
        }
        return [
            'post_id' => $this->id,
            'social_id' => $this->social_network_id,
            'account_id'=> $this->account_id,
            'account_name'=> $this->ac_name,
            'account_url'=> $this->account_url,
            'icon'  => $this->icon,
            'color'  => $this->color,
            'title' => $this->post_caption,
            'name'  => $this->post_caption,
            'start' => $customDate,
            'end' => $customDate,
            'timed' => true,
            'description'=> $this->post_caption,
            'media'=> $this->post_data,
            'url'=> '',
            'likeCount'=> rand(0,50),
            'commentCount'=> rand(0,50),
            'shareCount'=> rand(0,50),
        ];
    }
}
