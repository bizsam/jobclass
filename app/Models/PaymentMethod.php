<?php
/**
 * JobClass - Job Board Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com/jobclass
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Models;

use App\Models\Scopes\ActiveScope;
use App\Models\Scopes\CompatibleApiScope;
use App\Observers\PaymentMethodObserver;
use App\Http\Controllers\Admin\Panel\Library\Traits\Models\Crud;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PaymentMethod extends BaseModel
{
	use Crud;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'payment_methods';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	// protected $primaryKey = 'id';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var boolean
	 */
	public $timestamps = false;
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'name',
		'display_name',
		'description',
		'has_ccbox',
		'is_compatible_api',
		'countries',
		'active',
		'lft',
		'rgt',
		'depth',
		'parent_id',
	];
	
	/**
	 * The attributes that should be hidden for arrays
	 *
	 * @var array
	 */
	// protected $hidden = [];
	
	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	// protected $dates = [];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();
		
		PaymentMethod::observe(PaymentMethodObserver::class);
		
		static::addGlobalScope(new ActiveScope());
		static::addGlobalScope(new CompatibleApiScope());
	}
	
	public function getCountriesHtml()
	{
		$out = strtoupper(trans('admin.All'));
		if (isset($this->countries) && !empty($this->countries)) {
			$countriesCropped = str($this->countries)->limit(50, ' [...]');
			$out = '<div title="' . $this->countries . '">' . $countriesCropped . '</div>';
		}
		
		return $out;
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function payment()
	{
		return $this->hasMany(Payment::class, 'payment_method_id');
	}
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeActive($builder)
	{
		return $builder->where('active', 1);
	}
	
	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	protected function description(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (isset($this->name) && $this->name == 'offlinepayment') {
					if (mb_strlen(trans('offlinepayment::messages.payment_method_description')) > 0) {
						$value = trans('offlinepayment::messages.payment_method_description');
					}
				}
				
				return $value;
			},
		);
	}
	
	protected function countries(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$value = str_replace(',', ', ', strtoupper($value));
				
				return strtoupper($value);
			},
			set: function ($value) {
				// Get the MySQL right value
				$value = preg_replace('/(,|;)\s*/', ',', $value);
				$value = strtolower($value);
				
				// Check if the entry is removed
				if (empty($value) || $value == strtolower(trans('admin.All'))) {
					$value = null;
				}
				
				$this->attributes['countries'] = $value;
				
				return $value;
			},
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
