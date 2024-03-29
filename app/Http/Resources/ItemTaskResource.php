<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'task_id' => $this->task_id,
            'done' => $this->done,
            'created_at' => (new DateTime($this->created_at))->format("Y-m-d H:i:s"),
            'updated_at' => (new DateTime($this->updated_at))->format("Y-m-d H:i:s"),
        ];
    }
}
