@php
	$post ??= [];
@endphp
<div class="items-details">
	<div class="row pb-4">
		<div class="col-md-8 col-sm-12 col-12">
			<div class="items-details-info jobs-details-info enable-long-words from-wysiwyg">
				<h5 class="list-title"><strong>{{ t('ad_details') }}</strong></h5>
				
				{{-- Description --}}
				<div>
					{!! data_get($post, 'description') !!}
				</div>
				
				@if (!empty(data_get($post, 'company_description')))
					{{-- Company Description --}}
					<h5 class="list-title mt-5"><strong>{{ t('Company Description') }}</strong></h5>
					<div>
						{!! data_get($post, 'company_description') !!}
					</div>
				@endif
				
				{{-- Tags --}}
				@if (!empty(data_get($post, 'tags')))
					<div class="row mt-3">
						<div class="col-12">
							<h5 class="my-3 list-title"><strong>{{ t('Tags') }}</strong></h5>
							@foreach(data_get($post, 'tags') as $iTag)
								<span class="d-inline-block border border-inverse bg-light rounded-1 py-1 px-2 my-1 me-1">
									<a href="{{ \App\Helpers\UrlGen::tag($iTag) }}">
										{{ $iTag }}
									</a>
								</span>
							@endforeach
						</div>
					</div>
				@endif
			</div>
		</div>
		
		<div class="col-md-4 col-sm-12 col-12">
			<aside class="panel panel-body panel-details job-summery">
				<ul>
					@if (!empty(data_get($post, 'start_date')))
						<li>
							<p class="no-margin">
								<strong>{{ t('Start Date') }}:</strong>&nbsp;
								{{ data_get($post, 'start_date') }}
							</p>
						</li>
					@endif
					<li>
						<p class="no-margin">
							<strong>{{ t('Company') }}:</strong>&nbsp;
							@if (!empty(data_get($post, 'company_id')))
								<a href="{!! \App\Helpers\UrlGen::company(null, data_get($post, 'company_id')) !!}">
									{{ data_get($post, 'company_name') }}
								</a>
							@else
								{{ data_get($post, 'company_name') }}
							@endif
						</p>
					</li>
					<li>
						<p class="no-margin">
							<strong>{{ t('Salary') }}:</strong>&nbsp;
							@if (data_get($post, 'salary_min') > 0 || data_get($post, 'salary_max') > 0)
								@if (data_get($post, 'salary_min') > 0)
									{!! \App\Helpers\Number::money(data_get($post, 'salary_min')) !!}
								@endif
								@if (data_get($post, 'salary_max') > 0)
									@if (data_get($post, 'salary_min') > 0)
										&nbsp;-&nbsp;
									@endif
									{!! \App\Helpers\Number::money(data_get($post, 'salary_max')) !!}
								@endif
							@else
								{!! \App\Helpers\Number::money('--') !!}
							@endif
							@if (!empty(data_get($post, 'salaryType')))
								{{ t('per') }} {{ data_get($post, 'salaryType.name') }}
							@endif
							
							@if (data_get($post, 'negotiable') == 1)
								<br><small class="label bg-success"> {{ t('negotiable') }}</small>
							@endif
						</p>
					</li>
					<li>
						@if (!empty(data_get($post, 'postType')))
							<p class="no-margin">
								<strong>{{ t('Job Type') }}:</strong>&nbsp;
								<a href="{{ \App\Helpers\UrlGen::searchWithoutQuery() . '?type[]=' . data_get($post, 'postType.id') }}">
									{{ data_get($post, 'postType.name') }}
								</a>
							</p>
						@endif
					</li>
					<li>
						<p class="no-margin">
							<strong>{{ t('location') }}:</strong>&nbsp;
							<a href="{!! \App\Helpers\UrlGen::city(data_get($post, 'city')) !!}">
								{{ data_get($post, 'city.name') }}
							</a>
						</p>
					</li>
				</ul>
			</aside>
			
			<div class="posts-action">
				<ul class="list-border">
					@if (!empty(data_get($post, 'company')))
						<li>
							<a href="{{ \App\Helpers\UrlGen::company(null, data_get($post, 'company.id')) }}">
								<i class="far fa-building"></i> {{ t('More jobs by company', ['company' => data_get($post, 'company.name')]) }}
							</a>
						</li>
					@endif
					
					@if (isset($user) && !empty($user))
						<li>
							<a href="{{ \App\Helpers\UrlGen::user($user) }}">
								<i class="bi bi-person-rolodex"></i> {{ t('More jobs by user', ['user' => data_get($user, 'name')]) }}
							</a>
						</li>
					@endif
					
					@if (
						!auth()->check()
						|| (auth()->check() && auth()->id() != data_get($post, 'user_id'))
					)
						@if (isVerifiedPost($post))
							<li id="{{ data_get($post, 'id') }}">
								<a class="make-favorite" href="javascript:void(0)">
									@if (!auth()->check())
										<i class="far fa-bookmark"></i> {{ t('Save Job') }}
									@endif
									@if (auth()->check() && in_array(auth()->user()->user_type_id, [2]))
										@if (!empty(data_get($post, 'savedByLoggedUser')))
											<i class="fas fa-bookmark"></i> {{ t('Saved Job') }}
										@else
											<i class="far fa-bookmark"></i> {{ t('Save Job') }}
										@endif
									@endif
								</a>
							</li>
							<li>
								<a href="{{ \App\Helpers\UrlGen::reportPost($post) }}">
									<i class="far fa-flag"></i> {{ t('Report abuse') }}
								</a>
							</li>
						@endif
					@endif
				</ul>
			</div>
		</div>
	</div>
	
	<div class="content-footer text-start">
		@if (auth()->check())
			@if (auth()->user()->id == data_get($post, 'user_id'))
				<a class="btn btn-default" href="{{ \App\Helpers\UrlGen::editPost($post) }}">
					<i class="far fa-edit"></i> {{ t('Edit') }}
				</a>
			@else
				@if (in_array(auth()->user()->user_type_id, [2]))
					{!! genEmailContactBtn($post) !!}
				@endif
			@endif
		@else
			{!! genEmailContactBtn($post) !!}
		@endif
		{!! genPhoneNumberBtn($post) !!}
		&nbsp;<small>{{-- or. Send your CV to: foo@bar.com --}}</small>
	</div>
</div>
