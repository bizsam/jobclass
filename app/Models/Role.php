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

use App\Helpers\DBTool;
use App\Observers\RoleObserver;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\Panel\Library\Traits\Models\Crud;
use Spatie\Permission\Models\Role as OriginalRole;

class Role extends OriginalRole
{
	use Crud;
	
	protected $fillable = ['name', 'guard_name', 'updated_at', 'created_at'];
	
	/*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
	protected static function boot()
	{
		parent::boot();
		
		Role::observe(RoleObserver::class);
	}
	
	public static function getSuperAdminRole(): string
	{
		return 'super-admin';
	}
	
	/**
	 * Check Super Admin role
	 * NOTE: Must use try {...} catch {...}
	 *
	 * @return bool
	 */
	public static function checkSuperAdminRole(): bool
	{
		try {
			$role = Role::where('name', Role::getSuperAdminRole())->first();
			if (empty($role)) {
				return false;
			}
		} catch (\Exception $e) {
		}
		
		return true;
	}
	
	/**
	 * Reset default roles
	 * NOTE: Must use try {...} catch {...}
	 *
	 * @return \App\Models\Role|\Illuminate\Database\Eloquent\Model
	 */
	public static function resetDefaultRole()
	{
		try {
			// Remove all current roles & their relationship
			$roles = Role::all();
			$roles->each(function ($item, $key) {
				if ($item->permissions()) {
					$item->permissions()->detach();
				}
				$item->delete();
			});
			
			// Reset roles table ID auto-increment
			DB::statement('ALTER TABLE ' . DBTool::table(config('permission.table_names.roles')) . ' AUTO_INCREMENT = 1;');
			
			// Create the default Super Admin role
			$role = Role::where('name', Role::getSuperAdminRole())->first();
			if (empty($role)) {
				$role = Role::create(['name' => Role::getSuperAdminRole()]);
			}
		} catch (\Exception $e) {
			return null;
		}
		
		return $role;
	}
	
	public function updateBtn($xPanel = false): string
	{
		$out = '';
		
		if (strtolower($this->name) == strtolower(Role::getSuperAdminRole())) {
			return $out;
		}
		
		$url = admin_url('roles/' . $this->id . '/edit');
		
		$out = '<a href="' . $url . '" class="btn btn-xs btn-primary">';
		$out .= '<i class="fa fa-edit"></i> ';
		$out .= trans('admin.edit');
		$out .= '</a>';
		
		return $out;
	}
	
	public function deleteBtn($xPanel = false): string
	{
		$out = '';
		
		if (strtolower($this->name) == strtolower(Role::getSuperAdminRole())) {
			return $out;
		}
		
		$url = admin_url('roles/' . $this->id);
		
		$out = '<a href="' . $url . '" class="btn btn-xs btn-danger" data-button-type="delete">';
		$out .= '<i class="fa fa-trash"></i> ';
		$out .= trans('admin.delete');
		$out .= '</a>';
		
		return $out;
	}
	
	/*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
	
	/*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
	
	/*
    |--------------------------------------------------------------------------
    | ACCESSORS | MUTATORS
    |--------------------------------------------------------------------------
    */
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
