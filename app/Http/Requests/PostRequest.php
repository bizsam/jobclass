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

namespace App\Http\Requests;

use App\Helpers\Number;
use App\Helpers\RemoveFromString;
use App\Models\Package;
use App\Models\Post;
use App\Rules\BetweenRule;
use App\Rules\BlacklistTitleRule;
use App\Rules\BlacklistWordRule;
use App\Rules\DateIsValidRule;
use App\Rules\MbAlphanumericRule;
use App\Rules\SluggableRule;
use App\Rules\UniquenessOfPostRule;
use Illuminate\Validation\Rule;
use Mews\Purifier\Facades\Purifier;

class PostRequest extends Request
{
	public static $packages;
	public static $paymentMethods;
	
	/**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		// Don't apply this to the Admin Panel
		if (isAdminPanel()) {
			return;
		}
		
		$input = $this->all();
		
		// title
		if ($this->filled('title')) {
			$input['title'] = $this->input('title');
			$input['title'] = strCleanerLite($input['title']);
			$input['title'] = onlyNumCleaner($input['title']);
			$input['title'] = RemoveFromString::contactInfo($input['title'], true);
		}
		
		// company.name
		if ($this->filled('company.name')) {
			$input['company']['name'] = $this->input('company.name');
			$input['company']['name'] = onlyNumCleaner($input['company']['name']);
			$input['company']['name'] = RemoveFromString::contactInfo($input['company']['name'], true);
		}
		
		// company.description
		if ($this->filled('company.description')) {
			$input['company']['description'] = $this->input('company.description');
			$input['company']['description'] = onlyNumCleaner($input['company']['description']);
			$input['company']['description'] = RemoveFromString::contactInfo($input['company']['description'], true);
		}
		
		// description
		if ($this->filled('description')) {
			$input['description'] = $this->input('description');
			$input['description'] = onlyNumCleaner($input['description']);
			if (config('settings.single.wysiwyg_editor') != 'none') {
				try {
					$input['description'] = Purifier::clean($input['description']);
				} catch (\Exception $e) {
				}
			} else {
				$input['description'] = strCleaner($input['description']);
			}
			$input['description'] = RemoveFromString::contactInfo($input['description'], true);
		}
		
		// salary_min
		if ($this->has('salary_min')) {
			if ($this->filled('salary_min')) {
				$input['salary_min'] = $this->input('salary_min');
				// If field's value contains only numbers and dot,
				// Then decimal separator is set as dot.
				if (preg_match('/^[\d.]*$/', $input['salary_min'])) {
					$input['salary_min'] = Number::formatForDb($input['salary_min'], '.');
				} else {
					$input['salary_min'] = Number::formatForDb($input['salary_min'], config('currency.decimal_separator', '.'));
				}
			} else {
				$input['salary_min'] = null;
			}
		}
		
		// salary_max
		if ($this->has('salary_max')) {
			if ($this->filled('salary_max')) {
				$input['salary_max'] = $this->input('salary_max');
				// If field's value contains only numbers and dot,
				// Then decimal separator is set as dot.
				if (preg_match('/^[\d.]*$/', $input['salary_max'])) {
					$input['salary_max'] = Number::formatForDb($input['salary_max'], '.');
				} else {
					$input['salary_max'] = Number::formatForDb($input['salary_max'], config('currency.decimal_separator', '.'));
				}
			} else {
				$input['salary_max'] = null;
			}
		}
		
		// contact_name
		if ($this->filled('contact_name')) {
			$input['contact_name'] = strCleanerLite($this->input('contact_name'));
			$input['contact_name'] = onlyNumCleaner($input['contact_name']);
		}
		
		// auth_field
		$input['auth_field'] = getAuthField();
		
		// phone
		if ($this->filled('phone')) {
			$input['phone'] = phoneE164($this->input('phone'), getPhoneCountry());
			$input['phone_national'] = phoneNational($this->input('phone'), getPhoneCountry());
		} else {
			$input['phone'] = null;
			$input['phone_national'] = null;
		}
		
		// tags
		if ($this->filled('tags')) {
			$input['tags'] = tagCleaner($this->input('tags'));
		}
		
		// application_url
		if ($this->filled('application_url')) {
			$input['application_url'] = addHttp($this->input('application_url'));
		}
		
		request()->merge($input); // Required!
		$this->merge($input);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$guard = isFromApi() ? 'sanctum' : null;
		$authFields = array_keys(getAuthFields());
		
		$rules = [];
		
		$rules['category_id'] = ['required', 'not_in:0'];
		$rules['post_type_id'] = ['required', 'not_in:0'];
		$rules['title'] = [
			'required',
			new BetweenRule(
				(int)config('settings.single.title_min_length', 2),
				(int)config('settings.single.title_max_length', 150)
			),
			new MbAlphanumericRule(),
			new SluggableRule(),
			new BlacklistTitleRule(),
		];
		if (config('settings.single.enable_post_uniqueness')) {
			$rules['title'][] = new UniquenessOfPostRule();
		}
		$rules['description'] = [
			'required',
			new BetweenRule(
				(int)config('settings.single.description_min_length', 5),
				(int)config('settings.single.description_max_length', 12000)
			),
			new MbAlphanumericRule(),
			new BlacklistWordRule(),
		];
		$rules['salary_type_id'] = ['required', 'not_in:0'];
		$rules['contact_name'] = ['required', new BetweenRule(2, 200)];
		$rules['auth_field'] = ['required', Rule::in($authFields)];
		$rules['phone'] = ['max:30'];
		$rules['phone_country'] = ['required_with:phone'];
		$rules['city_id'] = ['required', 'not_in:0'];
		
		if (!auth($guard)->check()) {
			$rules['accept_terms'] = ['accepted'];
		}
		
		// CREATE
		if (in_array($this->method(), ['POST', 'CREATE'])) {
			$rules['start_date'] = [new DateIsValidRule('future')];
			
			// Single Step Form
			if (config('settings.single.publication_form_type') == '2') {
				// Package & PaymentMethod
				if (
					isset(self::$packages, self::$paymentMethods)
					&& self::$packages->count() > 0
					&& self::$paymentMethods->count() > 0
				) {
					// Require 'package_id' if Packages are available
					$rules['package_id'] = 'required';
					
					// Require 'payment_method_id' if the selected package's price > 0
					if ($this->has('package_id')) {
						$package = Package::find($this->input('package_id'));
						if (!empty($package) && $package->price > 0) {
							$rules['payment_method_id'] = 'required|not_in:0';
						}
					}
				}
			}
			
			$rules = $this->captchaRules($rules);
		}
		
		// UPDATE
		if (in_array($this->method(), ['PUT', 'PATCH', 'UPDATE'])) {
			if ($this->filled('post_id')) {
				$post = Post::find($this->input('post_id'));
				$rules['start_date'] = [new DateIsValidRule('future', ($post->created_at ?? null))];
			} else {
				$rules['start_date'] = [new DateIsValidRule('future')];
			}
		}
		
		// COMMON
		
		// Location
		if (config('settings.single.city_selection') == 'select') {
			$adminType = config('country.admin_type', 0);
			if (in_array($adminType, ['1', '2'])) {
				$rules['admin_code'] = ['required', 'not_in:0'];
			}
		}
		
		$phoneIsEnabledAsAuthField = (config('settings.sms.enable_phone_as_auth_field') == '1');
		$phoneNumberIsRequired = ($phoneIsEnabledAsAuthField && $this->input('auth_field') == 'phone');
		
		// email
		$emailIsRequired = (!$phoneNumberIsRequired);
		if ($emailIsRequired) {
			$rules['email'][] = 'required';
		}
		$rules = $this->validEmailRules('email', $rules);
		
		// phone
		if ($phoneNumberIsRequired) {
			$rules['phone'][] = 'required';
		}
		$rules = $this->validPhoneNumberRules('phone', $rules);
		
		// Company
		if (!$this->filled('company_id') || empty($this->input('company_id'))) {
			$rules['company.name'] = ['required', new BetweenRule(2, 200), new BlacklistTitleRule()];
			$rules['company.description'] = ['required', new BetweenRule(5, 12000), new BlacklistWordRule()];
			
			// Check 'company.logo' is required
			if ($this->file('company.logo')) {
				$rules['company.logo'] = [
					'required',
					'image',
					'mimes:' . getUploadFileTypes('image'),
					'max:' . (int)config('settings.upload.max_image_size', 1000),
				];
			}
		} else {
			$rules['company_id'] = ['required', 'not_in:0'];
		}
		
		// Application URL
		if ($this->filled('application_url')) {
			$rules['application_url'] = ['url'];
		}
		
		// Tags
		if ($this->filled('tags')) {
			$rules['tags.*'] = [
				'regex:' . tagRegexPattern(),
				new BlacklistWordRule(),
			];
		}
		
		return $rules;
	}
	
	/**
	 * Get custom attributes for validator errors.
	 *
	 * @return array
	 */
	public function attributes(): array
	{
		$attributes = [];
		
		if ($this->file('company.logo')) {
			$attributes['company.logo'] = t('logo');
		}
		
		if ($this->filled('tags')) {
			$tags = $this->input('tags');
			if (is_array($tags) && !empty($tags)) {
				foreach ($tags as $key => $tag) {
					$attributes['tags.' . $key] = t('tag X', ['key' => ($key + 1)]);
				}
			}
		}
		
		return $attributes;
	}
	
	/**
	 * @return array
	 */
	public function messages(): array
	{
		$messages = [];
		
		// Logo
		if ($this->file('company.logo')) {
			// uploaded
			$maxSize = (int)config('settings.upload.max_image_size', 1000); // In KB
			$maxSize = $maxSize * 1024;                                     // Convert KB to Bytes
			$msg = t('large_file_uploaded_error', [
				'field'   => t('logo'),
				'maxSize' => readableBytes($maxSize),
			]);
			
			$uploadMaxFilesizeStr = @ini_get('upload_max_filesize');
			$postMaxSizeStr = @ini_get('post_max_size');
			if (!empty($uploadMaxFilesizeStr) && !empty($postMaxSizeStr)) {
				$uploadMaxFilesize = (int)strToDigit($uploadMaxFilesizeStr);
				$postMaxSize = (int)strToDigit($postMaxSizeStr);
				
				$serverMaxSize = min($uploadMaxFilesize, $postMaxSize);
				$serverMaxSize = $serverMaxSize * 1024 * 1024; // Convert MB to KB to Bytes
				if ($serverMaxSize < $maxSize) {
					$msg = t('large_file_uploaded_error_system', [
						'field'   => t('logo'),
						'maxSize' => readableBytes($serverMaxSize),
					]);
				}
			}
			
			$messages['company.logo.uploaded'] = $msg;
		}
		
		// Category & Sub-Category
		if ($this->filled('parent_id') && !empty($this->input('parent_id'))) {
			$messages['category_id.required'] = t('The field is required', ['field' => mb_strtolower(t('Sub-Category'))]);
			$messages['category_id.not_in'] = t('The field is required', ['field' => mb_strtolower(t('Sub-Category'))]);
		}
		
		// Single Step Form
		if (config('settings.single.publication_form_type') == '2') {
			// Package & PaymentMethod
			$messages['package_id.required'] = trans('validation.required_package_id');
			$messages['payment_method_id.required'] = t('validation.required_payment_method_id');
			$messages['payment_method_id.not_in'] = t('validation.required_payment_method_id');
		}
		
		return $messages;
	}
}
