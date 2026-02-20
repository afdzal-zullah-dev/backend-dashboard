<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'description'    => $this->description,
            'file_name'      => $this->file_name,
            'file_path'      => $this->file_path,
            'file_type'      => $this->file_type,
            'file_size'      => $this->file_size,
            'access_level'   => $this->access_level,
            'download_count' => $this->download_count,

            'category_id'    => $this->category_id,
            'department_id'  => $this->department_id,
            'uploaded_by'    => $this->uploaded_by,

            // kalau load relationship, sini auto include
            'category'       => new CategoryResource($this->whenLoaded('category')),
            'department'     => new DepartmentResource($this->whenLoaded('department')),
            'uploader'       => new UserResource($this->whenLoaded('uploader')),

            'created_at'     => optional($this->created_at)->toISOString(),
            'updated_at'     => optional($this->updated_at)->toISOString(),
        ];
    }
}
