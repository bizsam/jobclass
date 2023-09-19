{{--
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
--}}
@extends('layouts.master')

@section('wizard')
	@includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.multiSteps.inc.wizard', 'post.createOrEdit.multiSteps.inc.wizard'])
@endsection

@php
	$postInput ??= [];
	$postCompany ??= [];
	
	$companies ??= [];
	$postTypes ??= [];
	$salaryTypes ??= [];
	$countries ??= [];
@endphp

@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
	<div class="main-container">
		<div class="container">
			<div class="row">
				
				@includeFirst([config('larapen.core.customizedViewPath') . 'post.inc.notification', 'post.inc.notification'])
				
				<div class="col-md-9 page-content">
					<div class="inner-box category-content" style="overflow: visible;">
						<h2 class="title-2">
							<strong><i class="far fa-edit"></i> {{ t('create_new_job') }}</strong>
						</h2>
						
						<div class="row">
							<div class="col-xl-12">

								<form class="form-horizontal" id="postForm" method="POST" action="{{ request()->fullUrl() }}" enctype="multipart/form-data">
									{!! csrf_field() !!}
									<fieldset>
										
										{{-- COMPANY --}}
										<div class="content-subheading mt-0">
											<i class="far fa-building"></i>
											<strong>{{ t('Company Information') }}</strong>
										</div>
										
										{{-- company_id --}}
										@php
											$companyIdError = (isset($errors) && $errors->has('company_id')) ? ' is-invalid' : '';
											$postCompanyId = data_get($postCompany, 'id', 0);
											$postCompanyId = data_get($postInput, 'company_id', $postCompanyId);
											$postCompanyId = data_get($postInput, 'company.id', $postCompanyId);
											$postCompanyId = old('company_id', $postCompanyId);
										@endphp
										<div class="row mb-3 required">
											<label class="col-md-3 col-form-label{{ $companyIdError }}">{{ t('Select a Company') }} <sup>*</sup></label>
											<div class="col-md-8">
												<select id="companyId" name="company_id" class="form-control selecter{{ $companyIdError }}">
													<option value="0" data-logo="" @selected(empty(old('company_id')))>
														[+] {{ t('New Company') }}
													</option>
													@if (!empty($companies))
														@foreach ($companies as $item)
															<option value="{{ data_get($item, 'id') }}"
																	data-logo="{{ data_get($item, 'logo_url_small') }}"
																	@selected($postCompanyId == data_get($item, 'id'))
															>
																{{ data_get($item, 'name') }}
															</option>
														@endforeach
													@endif
												</select>
											</div>
										</div>
										
										{{-- logo --}}
										<div id="logoField" class="row mb-3">
											<label class="col-md-3 col-form-label">&nbsp;</label>
											<div class="col-md-8">
												<div class="mb10">
													<div id="logoFieldValue"></div>
												</div>
												<small id="" class="form-text text-muted">
													<a id="companyFormLink" href="{{ url('account/companies/0/edit') }}" class="btn btn-default">
														<i class="far fa-edit"></i> {{ t('Edit the Company') }}
													</a>
												</small>
											</div>
										</div>
										
										@includeFirst([config('larapen.core.customizedViewPath') . 'account.company._form', 'account.company._form'], ['originForm' => 'post'])
										
									
										{{-- POST --}}
										<div class="content-subheading">
											<i class="far fa-building"></i> <strong>{{ t('ad_details') }}</strong>
										</div>
										
										{{-- category_id --}}
										<?php $categoryIdError = (isset($errors) && $errors->has('category_id')) ? ' is-invalid' : ''; ?>
										<div class="row mb-3 required">
											<label class="col-md-3 col-form-label{{ $categoryIdError }}">{{ t('category') }} <sup>*</sup></label>
											<div class="col-md-8">
												<div id="catsContainer" class="rounded{{ $categoryIdError }}">
													<a href="#browseCategories" data-bs-toggle="modal" class="cat-link" data-id="0">
														{{ t('select_a_category') }}
													</a>
												</div>
											</div>
											<input type="hidden" name="category_id" id="categoryId" value="{{ old('category_id', data_get($postInput, 'category_id', 0)) }}">
										</div>

										{{-- title --}}
										<?php $titleError = (isset($errors) && $errors->has('title')) ? ' is-invalid' : ''; ?>
										<div class="row mb-3 required">
											<label class="col-md-3 col-form-label{{ $titleError }}" for="title">{{ t('Title') }} <sup>*</sup></label>
											<div class="col-md-8">
												<input id="title" name="title" placeholder="{{ t('Job title') }}" class="form-control input-md{{ $titleError }}"
													   type="text" value="{{ old('title', data_get($postInput, 'title')) }}">
												<div class="form-text text-muted">{{ t('A great title needs at least 60 characters.') }}</div>
											</div>
										</div>

										{{-- description --}}
										@php
											$descriptionError = (isset($errors) && $errors->has('description')) ? ' is-invalid' : '';
											$postDescription = data_get($postInput, 'description');
											$descriptionErrorLabel = '';
											$descriptionColClass = 'col-md-8';
											if (config('settings.single.wysiwyg_editor') != 'none') {
												$descriptionColClass = 'col-md-12';
												$descriptionErrorLabel = $descriptionError;
											}
										@endphp
										<div class="row mb-3 required">
											<label class="col-md-3 col-form-label{{ $descriptionErrorLabel }}" for="description">
												{{ t('Description') }} <sup>*</sup>
											</label>
											<div class="{{ $descriptionColClass }}">
												<textarea class="form-control {{ $descriptionError }}"
														  id="description"
														  name="description"
														  rows="15"
														  style="height: 300px"
												>{{ old('description', $postDescription) }}</textarea>
												<div class="form-text text-muted">{{ t('Describe what makes your ad unique') }}</div>
											</div>
										</div>

										{{-- post_type_id --}}
										@php
											$postTypeIdError = (isset($errors) && $errors->has('post_type_id')) ? ' is-invalid' : '';
											$postTypeId = old('post_type_id', data_get($postInput, 'post_type_id'));
										@endphp
										<div id="postTypeBloc" class="row mb-3 required">
											<label class="col-md-3 col-form-label{{ $postTypeIdError }}">
												{{ t('Job Type') }} <sup>*</sup>
											</label>
											<div class="col-md-8">
												<select name="post_type_id" id="postTypeId" class="form-control selecter{{ $postTypeIdError }}">
													@foreach ($postTypes as $postType)
														<option value="{{ data_get($postType, 'id') }}" @selected($postTypeId == data_get($postType, 'id'))>
															{{ data_get($postType, 'name') }}
														</option>
													@endforeach
												</select>
											</div>
										</div>
										
										{{-- salary_min & salary_max --}}
										@php
											$salaryMinError = (isset($errors) && $errors->has('salary_min')) ? ' is-invalid' : '';
											$salaryMaxError = (isset($errors) && $errors->has('salary_max')) ? ' is-invalid' : '';
											$salaryMin = old('salary_min', data_get($postInput, 'salary_min'));
											$salaryMax = old('salary_max', data_get($postInput, 'salary_max'));
											$salaryMin = \App\Helpers\Number::format($salaryMin, 2, '.', '');
											$salaryMax = \App\Helpers\Number::format($salaryMax, 2, '.', '');
										@endphp
										<div id="salaryBloc" class="row mb-3">
											<label class="col-md-3 col-form-label" for="salary_min">{{ t('Salary') }}</label>
											<div class="col-md-4">
												<div class="row">
													<div class="input-group col-md-12">
														@if (config('currency')['in_left'] == 1)
															<span class="input-group-text">{!! config('currency')['symbol'] !!}</span>
														@endif
														<input id="salary_min"
															   name="salary_min"
															   class="form-control{{ $salaryMinError }}"
															   data-bs-toggle="tooltip"
															   title="{{ t('salary_min') }}"
															   placeholder="{{ t('salary_min') }}"
															   type="number"
															   min="0"
															   step="{{ getInputNumberStep((int)config('currency.decimal_places', 2)) }}"
															   value="{!! $salaryMin !!}"
														>
														@if (config('currency')['in_left'] == 0)
															<span class="input-group-text">{!! config('currency')['symbol'] !!}</span>
														@endif
													</div>
													<div class="input-group col-md-12">
														@if (config('currency')['in_left'] == 1)
															<span class="input-group-text">{!! config('currency')['symbol'] !!}</span>
														@endif
														<input id="salary_max"
															   name="salary_max"
															   class="form-control{{ $salaryMaxError }}"
															   data-bs-toggle="tooltip"
															   title="{{ t('salary_max') }}"
															   placeholder="{{ t('salary_max') }}"
															   type="number"
															   min="0"
															   step="{{ getInputNumberStep((int)config('currency.decimal_places', 2)) }}"
															   value="{!! $salaryMax !!}"
														>
														@if (config('currency')['in_left'] == 0)
															<span class="input-group-text">{!! config('currency')['symbol'] !!}</span>
														@endif
													</div>
												</div>
											</div>

											{{-- salary_type_id --}}
											@php
												$salaryTypeIdError = (isset($errors) && $errors->has('salary_type_id')) ? ' is-invalid' : '';
												$salaryTypeId = old('salary_type_id', data_get($postInput, 'salary_type_id'));
											@endphp
											<div class="col-md-4">
												<select name="salary_type_id" id="salaryTypeId" class="form-control selecter{{ $salaryTypeIdError }}">
													@foreach ($salaryTypes as $salaryType)
														<option value="{{ data_get($salaryType, 'id') }}" @selected($salaryTypeId == data_get($salaryType, 'id'))>
															{{ t('per') . ' ' . data_get($salaryType, 'name') }}
														</option>
													@endforeach
												</select>
												<div class="form-check form-check-inline px-0">
													<label class="form-check-label pt-2">
														<input id="negotiable"
															   name="negotiable"
															   type="checkbox"
															   value="1" @checked(old('negotiable', data_get($postInput, 'negotiable')) == '1')
														>&nbsp;{{ t('negotiable') }}
													</label>
												</div>
											</div>
										</div>

										{{-- start_date --}}
										<?php $startDateError = (isset($errors) && $errors->has('start_date')) ? ' is-invalid' : ''; ?>
										<div class="row mb-3">
											<label class="col-md-3 col-form-label{{ $startDateError }}" for="start_date">{{ t('Start Date') }} </label>
											<div class="col-md-9 col-lg-8 col-xl-6">
												<input id="startDate" name="start_date"
													   placeholder="{{ t('Start Date') }}"
													   class="form-control input-md{{ $startDateError }} cf-date"
													   type="text"
													   value="{{ old('start_date', data_get($postInput, 'start_date')) }}"
													   autocomplete="off"
												>
											</div>
										</div>
										
										{{-- country_code --}}
										@php
											$countryCodeError = (isset($errors) && $errors->has('country_code')) ? ' is-invalid' : '';
											$countryCodeValue = (!empty(config('ipCountry.code'))) ? config('ipCountry.code') : 0;
											$countryCodeValue = data_get($postInput, 'country_code', $countryCodeValue);
											$countryCodeValue = old('country_code', $countryCodeValue);
										@endphp
										@if (empty(config('country.code')))
											<div class="row mb-3 required">
												<label class="col-md-3 col-form-label{{ $countryCodeError }}" for="country_code">{{ t('your_country') }} <sup>*</sup></label>
												<div class="col-md-8">
													<select id="countryCode" name="country_code" class="form-control large-data-selecter{{ $countryCodeError }}">
														<option value="0" data-admin-type="0" @selected(empty(old('country_code')))>
															{{ t('select_a_country') }}
														</option>
														@foreach ($countries as $item)
															<option value="{{ data_get($item, 'code') }}"
																	data-admin-type="{{ data_get($item, 'admin_type', 0) }}"
																	@selected($countryCodeValue == data_get($item, 'code'))
															>
																{{ data_get($item, 'name') }}
															</option>
														@endforeach
													</select>
												</div>
											</div>
										@else
											<input id="countryCode" name="country_code" type="hidden" value="{{ config('country.code') }}">
										@endif

										{{-- contact_name --}}
										@if (auth()->check())
											<input id="contactName" name="contact_name" type="hidden" value="{{ auth()->user()->name }}">
										@else
											<?php $contactNameError = (isset($errors) && $errors->has('contact_name')) ? ' is-invalid' : ''; ?>
											<div class="row mb-3 required">
												<label class="col-md-3 col-form-label{{ $contactNameError }}" for="contact_name">
													{{ t('Contact Name') }} <sup>*</sup>
												</label>
												<div class="col-md-9 col-lg-8 col-xl-6">
													<div class="input-group">
														<span class="input-group-text"><i class="far fa-user"></i></span>
														<input id="contactName" name="contact_name"
															   placeholder="{{ t('Contact Name') }}"
															   class="form-control input-md{{ $contactNameError }}"
															   type="text"
															   value="{{ old('contact_name', data_get($postInput, 'contact_name')) }}"
														>
													</div>
												</div>
											</div>
										@endif
										
										{{-- auth_field (as notification channel) --}}
										@php
											$authFields = getAuthFields(true);
											$authFieldError = (isset($errors) && $errors->has('auth_field')) ? ' is-invalid' : '';
											$usersCanChooseNotifyChannel = isUsersCanChooseNotifyChannel();
											$authFieldValue = ($usersCanChooseNotifyChannel) ? (old('auth_field', getAuthField())) : getAuthField();
										@endphp
										@if ($usersCanChooseNotifyChannel)
											<div class="row mb-3 required">
												<label class="col-md-3 col-form-label" for="auth_field">{{ t('notifications_channel') }} <sup>*</sup></label>
												<div class="col-md-9">
													@foreach ($authFields as $iAuthField => $notificationType)
														<div class="form-check form-check-inline pt-2">
															<input name="auth_field"
																   id="{{ $iAuthField }}AuthField"
																   value="{{ $iAuthField }}"
																   class="form-check-input auth-field-input{{ $authFieldError }}"
																   type="radio" @checked($authFieldValue == $iAuthField)
															>
															<label class="form-check-label mb-0" for="{{ $iAuthField }}AuthField">
																{{ $notificationType }}
															</label>
														</div>
													@endforeach
													<div class="form-text text-muted">
														{{ t('notifications_channel_hint') }}
													</div>
												</div>
											</div>
										@else
											<input id="{{ $authFieldValue }}AuthField" name="auth_field" type="hidden" value="{{ $authFieldValue }}">
										@endif
										
										@php
											$forceToDisplay = isBothAuthFieldsCanBeDisplayed() ? ' force-to-display' : '';
										@endphp
									
										{{-- email --}}
										@php
											$emailError = (isset($errors) && $errors->has('email')) ? ' is-invalid' : '';
											$emailValue = (auth()->check() && isset(auth()->user()->email))
												? auth()->user()->email
												: data_get($postInput, 'email');
										@endphp
										<div class="row mb-3 auth-field-item required{{ $forceToDisplay }}">
											<label class="col-md-3 col-form-label{{ $emailError }}" for="email"> {{ t('Contact Email') }}
												@if (getAuthField() == 'email')
													<sup>*</sup>
												@endif
											</label>
											<div class="col-md-9 col-lg-8 col-xl-6">
												<div class="input-group">
													<span class="input-group-text"><i class="far fa-envelope"></i></span>
													<input id="email" name="email"
														   class="form-control{{ $emailError }}"
														   placeholder="{{ t('email_address') }}"
														   type="text"
														   value="{{ old('email', $emailValue) }}"
													>
												</div>
											</div>
										</div>
										
										{{-- phone --}}
										@php
											$phoneError = (isset($errors) && $errors->has('phone')) ? ' is-invalid' : '';
											$phoneValue = data_get($postInput, 'phone');
											$phoneCountryValue = data_get($postInput, 'phone_country', config('country.code'));
											if (
												auth()->check()
												&& isset(auth()->user()->country_code)
												&& isset(auth()->user()->phone)
												&& isset(auth()->user()->phone_country)
												// && auth()->user()->country_code == config('country.code')
											) {
												$phoneValue = auth()->user()->phone;
												$phoneCountryValue = auth()->user()->phone_country;
											}
											$phoneValue = phoneE164($phoneValue, $phoneCountryValue);
											$phoneValueOld = phoneE164(old('phone', $phoneValue), old('phone_country', $phoneCountryValue));
										@endphp
										<div class="row mb-3 auth-field-item required{{ $forceToDisplay }}">
											<label class="col-md-3 col-form-label{{ $phoneError }}" for="phone">{{ t('phone_number') }}
												@if (getAuthField() == 'phone')
													<sup>*</sup>
												@endif
											</label>
											<div class="col-md-9 col-lg-8 col-xl-6">
												<div class="input-group">
													<input id="phone" name="phone"
														   class="form-control input-md{{ $phoneError }}"
														   type="tel"
														   value="{{ $phoneValueOld }}"
													>
													<span class="input-group-text iti-group-text">
														<input id="phoneHidden" name="phone_hidden" type="checkbox" value="1" @checked(old('phone_hidden') == '1')>&nbsp;
														<small>{{ t('Hide') }}</small>
													</span>
												</div>
												<input name="phone_country" type="hidden" value="{{ old('phone_country', $phoneCountryValue) }}">
											</div>
										</div>
										
										@php
											$adminType = config('country.admin_type', 0);
										@endphp
										@if (config('settings.single.city_selection') == 'select')
											@if (in_array($adminType, ['1', '2']))
												{{-- admin_code --}}
												<?php $adminCodeError = (isset($errors) && $errors->has('admin_code')) ? ' is-invalid' : ''; ?>
												<div id="locationBox" class="row mb-3 required">
													<label class="col-md-3 col-form-label{{ $adminCodeError }}" for="admin_code">
														{{ t('location') }} <sup>*</sup>
													</label>
													<div class="col-md-8">
														<select id="adminCode" name="admin_code" class="form-control large-data-selecter{{ $adminCodeError }}">
															<option value="0" @selected(empty(old('admin_code')))>
																{{ t('select_your_location') }}
															</option>
														</select>
													</div>
												</div>
											@endif
										@else
											<input type="hidden" id="selectedAdminType" name="selected_admin_type" value="{{ old('selected_admin_type', $adminType) }}">
											<input type="hidden" id="selectedAdminCode" name="selected_admin_code" value="{{ old('selected_admin_code', 0) }}">
											<input type="hidden" id="selectedCityId" name="selected_city_id" value="{{ old('selected_city_id', 0) }}">
											<input type="hidden" id="selectedCityName" name="selected_city_name" value="{{ old('selected_city_name') }}">
										@endif
									
										{{-- city_id --}}
										<?php $cityIdError = (isset($errors) && $errors->has('city_id')) ? ' is-invalid' : ''; ?>
										<div id="cityBox" class="row mb-3 required">
											<label class="col-md-3 col-form-label{{ $cityIdError }}" for="city_id">
												{{ t('city') }} <sup>*</sup>
											</label>
											<div class="col-md-8">
												<select id="cityId" name="city_id" class="form-control large-data-selecter{{ $cityIdError }}">
													<option value="0" @selected(empty(old('city_id')))>
														{{ t('select_a_city') }}
													</option>
												</select>
											</div>
										</div>
										
										{{-- application_url --}}
										<?php $applicationUrlError = (isset($errors) && $errors->has('application_url')) ? ' is-invalid' : ''; ?>
										<div class="row mb-3">
											<label class="col-md-3 col-form-label" for="title">{{ t('Application URL') }}</label>
											<div class="col-md-8">
												<div class="input-group">
													<span class="input-group-text"><i class="fas fa-reply"></i></span>
													<input id="application_url"
														   name="application_url"
														   placeholder="{{ t('Application URL') }}"
														   class="form-control input-md{{ $applicationUrlError }}"
														   type="text"
														   value="{{ old('application_url', data_get($postInput, 'application_url')) }}"
													>
												</div>
												<div class="form-text text-muted">
													{{ t('Candidates will follow this URL address to apply for the job') }}
												</div>
											</div>
										</div>
										
										{{-- tags --}}
										@php
											$tagsError = (isset($errors) && $errors->has('tags.*')) ? ' is-invalid' : '';
											$tags = old('tags', data_get($postInput, 'tags'));
										@endphp
										<div class="row mb-3">
											<label class="col-md-3 col-form-label{{ $tagsError }}" for="tags">{{ t('Tags') }}</label>
											<div class="col-md-8">
												<select id="tags" name="tags[]" class="form-control tags-selecter" multiple="multiple">
													@if (!empty($tags))
														@foreach($tags as $iTag)
															<option selected="selected">{{ $iTag }}</option>
														@endforeach
													@endif
												</select>
												<div class="form-text text-muted">
													{!! t('tags_hint', [
															'limit' => (int)config('settings.single.tags_limit', 15),
															'min'   => (int)config('settings.single.tags_min_length', 2),
															'max'   => (int)config('settings.single.tags_max_length', 30)
														]) !!}
												</div>
											</div>
										</div>
										
										@if (!auth()->check())
											@if (in_array(config('settings.single.auto_registration'), [1, 2]))
												{{-- auto_registration --}}
												@if (config('settings.single.auto_registration') == 1)
													<?php $autoRegistrationError = (isset($errors) && $errors->has('auto_registration')) ? ' is-invalid' : ''; ?>
													<div class="row mb-3 required">
														<label class="col-md-3 col-form-label"></label>
														<div class="col-md-8">
															<div class="form-check">
																<input name="auto_registration" id="auto_registration"
																	   class="form-check-input{{ $autoRegistrationError }}"
																	   value="1"
																	   type="checkbox"
																	   checked="checked"
																>
																<label class="form-check-label" for="auto_registration">
																	{!! t('I want to register by submitting this ad') !!}
																</label>
															</div>
															<div class="form-text text-muted">{{ t('You will receive your authentication information by email') }}</div>
															<div style="clear:both"></div>
														</div>
													</div>
												@else
													<input type="hidden" name="auto_registration" id="auto_registration" value="1">
												@endif
											@endif
										@endif
										
										@include('layouts.inc.tools.captcha', ['colLeft' => 'col-md-3', 'colRight' => 'col-md-8'])
										
										@if (!auth()->check())
											{{-- accept_terms --}}
											@php
												$acceptTermsError = (isset($errors) && $errors->has('accept_terms')) ? ' is-invalid' : '';
												$acceptTerms = old('accept_terms', data_get($postInput, 'accept_terms'));
											@endphp
											<div class="row mb-3 required">
												<label class="col-md-3 col-form-label"></label>
												<div class="col-md-8">
													<div class="form-check">
														<input name="accept_terms" id="acceptTerms"
															   class="form-check-input{{ $acceptTermsError }}"
															   value="1"
															   type="checkbox" @checked($acceptTerms == '1')
														>
														
														<label class="form-check-label" for="acceptTerms" style="font-weight: normal;">
															{!! t('accept_terms_label', ['attributes' => getUrlPageByType('terms')]) !!}
														</label>
													</div>
													<div style="clear:both"></div>
												</div>
											</div>
											
											{{-- accept_marketing_offers --}}
											@php
												$acceptMarketingOffersError = (isset($errors) && $errors->has('accept_marketing_offers')) ? ' is-invalid' : '';
												$acceptMarketingOffers = old('accept_marketing_offers', data_get($postInput, 'accept_marketing_offers'));
											@endphp
											<div class="row mb-3 required">
												<label class="col-md-3 col-form-label"></label>
												<div class="col-md-8">
													<div class="form-check">
														<input name="accept_marketing_offers" id="acceptMarketingOffers"
															   class="form-check-input{{ $acceptMarketingOffersError }}"
															   value="1"
															   type="checkbox" @checked($acceptMarketingOffers == '1')
														>
														<label class="form-check-label" for="acceptMarketingOffers" style="font-weight: normal;">
															{!! t('accept_marketing_offers_label') !!}
														</label>
													</div>
													<div style="clear:both"></div>
												</div>
											</div>
										@endif

										{{-- Button  --}}
										<div class="row mb-3">
											<div class="col-md-12 text-center">
												<button id="nextStepBtn" class="btn btn-primary btn-lg"> {{ $nextStepLabel ?? t('Next') }} </button>
											</div>
										</div>

									</fieldset>
								</form>


							</div>
						</div>
					</div>
				</div>
				<!-- /.page-content -->

				<div class="col-md-3 reg-sidebar">
					@includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.right-sidebar', 'post.createOrEdit.inc.right-sidebar'])
				</div>
				
			</div>
		</div>
	</div>
	@includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.category-modal', 'post.createOrEdit.inc.category-modal'])
@endsection

@section('after_styles')
@endsection

@section('after_scripts')
@endsection

@includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.form-assets', 'post.createOrEdit.inc.form-assets'])
