<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param \Illuminate\Http\Request $request
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
		if (in_array('city', $embed)) {
			$entity['city'] = new CityResource($this->whenLoaded('city'));
		}
		
		$defaultLogo = config('larapen.core.picture.default');
		$defaultLogoUrl = imgUrl($defaultLogo);
		$entity['logo_url'] = [
			'full'  => $this->logo_url ?? $defaultLogoUrl,
			'small' => $this->logo_url_small ?? $defaultLogoUrl,
			'big'   => $this->logo_url_big ?? $defaultLogoUrl,
		];
		$entity['posts_count'] = $this->posts_count ?? 0;
		$entity['country_flag_url'] = $this->country_flag_url ?? null;
		
		return $entity;
	}
}
