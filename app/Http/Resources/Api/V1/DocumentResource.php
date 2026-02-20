<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'file_name'     => $this->file_name,
            'file_type'     => $this->file_type,
            'file_size'     => $this->file_size,
            'access_level'  => $this->access_level,
            'download_count'=> $this->download_count,

            'category' => $this->whenLoaded('category', function () {
                return [
                    'id'    => $this->category->id,
                    'title' => $this->category->title,
                ];
            }),

            'department' => $this->whenLoaded('department', function () {
                return [
                    'id'   => $this->department->id,
                    'name' => $this->department->name,
                ];
            }),

            'uploaded_by' => $this->whenLoaded('uploader', function () {
                return [
                    'id'   => $this->uploader->id,
                    'name' => $this->uploader->name,
                    'email'=> $this->uploader->email,
                ];
            }),

            'created_at'   => optional($this->created_at)->toIso8601String(),
            'updated_at'   => optional($this->updated_at)->toIso8601String(),
        ];
    }
}