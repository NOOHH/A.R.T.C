<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->batch_id,
            'batch_name' => $this->batch_name,
            'program_id' => $this->program_id,
            'learning_mode' => $this->learning_mode,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'max_students' => $this->max_students,
            'current_students' => $this->students_count ?? 0,
            'is_archived' => $this->is_archived,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
