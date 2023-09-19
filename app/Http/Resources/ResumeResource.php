<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResumeResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param $request
	 * @return array
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function toArray($request): array
	{
		$entity = [
			'id' => $this->id,
		];
		$columns = $this->getFillable();
		foreach ($columns as $column) {
			$entity[$column] = $this->{$column};
		}
		
		$embed = explode(',', request()->get('embed'));
		
		if (in_array('user', $embed)) {
			$entity['user'] = new UserResource($this->whenLoaded('user'));
		}
		
		$entity['country_flag_url'] = $this->country_flag_url ?? null;
		
		return $entity;
	}
}
