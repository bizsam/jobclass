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

<?php
$apiResult ??= [];
$resumes = (array)data_get($apiResult, 'data');
$totalResumes = (int)data_get($apiResult, 'meta.total', 0);
?>
@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
	<div class="main-container">
		<div class="container">
			<div class="row">
				
				@if (session()->has('flash_notification'))
					<div class="col-xl-12">
						<div class="row">
							<div class="col-xl-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif
				
				<div class="col-md-3 page-sidebar">
					@includeFirst([config('larapen.core.customizedViewPath') . 'account.inc.sidebar', 'account.inc.sidebar'])
				</div>
				<!--/.page-sidebar-->
				
				<div class="col-md-9 page-content">
					<div class="inner-box">
						<h2 class="title-2"><i class="far fa-building"></i> {{ t('My resumes') }} </h2>
						<div class="mb30">
							<a href="{{ url('account/resumes/create') }}" class="btn btn-default"><i class="fas fa-plus"></i> {{ t('Add a new resume') }}</a>
						</div>
						<br>
						
						<div class="table-responsive">
							<form name="listForm" method="POST" action="{{ url('account/resumes/delete') }}">
								{!! csrf_field() !!}
								<div class="table-action">
									<div class="btn-group hidden-sm" role="group">
										<button type="button" class="btn btn-sm btn-default pb-0">
											<input type="checkbox" id="checkAll" class="from-check-all">
										</button>
										<button type="button" class="btn btn-sm btn-default from-check-all">
											{{ t('Select') }}: {{ t('All') }}
										</button>
									</div>
									
									<button type="submit" class="btn btn-sm btn-default confirm-simple-action">
										<i class="fa fa-trash"></i> {{ t('Delete') }}
									</button>
									
									<div class="table-search float-end col-sm-7">
										<div class="row">
											<label class="col-sm-5 form-label text-end">{{ t('search') }} <br>
												<a title="clear filter" class="clear-filter" href="#clear">[{{ t('clear') }}]</a>
											</label>
											<div class="col-sm-7 searchpan px-3">
												<input type="text" class="form-control" id="filter">
											</div>
										</div>
									</div>
								</div>
								
								<table id="addManageTable" class="table table-striped table-bordered add-manage-table table demo"
									   data-filter="#filter" data-filter-text-only="true">
									<thead>
									<tr>
										<th data-type="numeric" data-sort-initial="true"></th>
										<th> {{ t('File') }}</th>
										<th data-sort-ignore="true"> {{ t('Name') }} </th>
										<th> {{ t('Option') }}</th>
									</tr>
									</thead>
									<tbody>
									
									<?php
									if (!empty($resumes) && $totalResumes > 0):
									foreach($resumes as $key => $resume):
									?>
									<tr>
										<td style="width:2%" class="add-img-selector">
											<div class="checkbox">
												<label><input type="checkbox" name="entries[]" value="{{ data_get($resume, 'id') }}"></label>
											</div>
										</td>
										<td style="width:14%" class="add-img-td">
											<a class="btn btn-default" href="{{ privateFileUrl(data_get($resume, 'filename')) }}" target="_blank">
												<i class="fas fa-paperclip"></i> {{ t('Download') }}
											</a>
										</td>
										<td style="width:58%" class="items-details-td">
											<div>
												<p>
													{{ str(data_get($resume, 'name'))->limit(40) }}
												</p>
											</div>
										</td>
										<td style="width:10%" class="action-td">
											<div>
												@if (data_get($resume, 'user_id') == $user->id)
													<p>
														<a class="btn btn-primary btn-sm" href="{{ url('account/resumes/' . data_get($resume, 'id') . '/edit') }}">
															<i class="fa fa-edit"></i> {{ t('Edit') }}
														</a>
													</p>
													<p>
														<a class="btn btn-danger btn-sm confirm-simple-action" href="{{ url('account/resumes/'.data_get($resume, 'id').'/delete') }}">
															<i class="fa fa-trash"></i> {{ t('Delete') }}
														</a>
													</p>
												@endif
											</div>
										</td>
									</tr>
									<?php endforeach; ?>
									<?php endif; ?>
									</tbody>
								</table>
							</form>
						</div>
						
						<div class="pagination-bar text-center">
							@include('vendor.pagination.api.bootstrap-4')
						</div>
					
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('after_styles')
	<style>
		.action-td p {
			margin-bottom: 5px;
		}
	</style>
@endsection

@section('after_scripts')
	<script src="{{ url('assets/js/footable.js?v=2-0-1') }}" type="text/javascript"></script>
	<script src="{{ url('assets/js/footable.filter.js?v=2-0-1') }}" type="text/javascript"></script>
	<script type="text/javascript">
		$(function () {
			$('#addManageTable').footable().bind('footable_filtering', function (e) {
				var selected = $('.filter-status').find(':selected').text();
				if (selected && selected.length > 0) {
					e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
					e.clear = !e.filter;
				}
			});
			
			$('.clear-filter').click(function (e) {
				e.preventDefault();
				$('.filter-status').val('');
				$('table.demo').trigger('footable_clear_filter');
			});
			
			$('.from-check-all').click(function () {
				checkAll(this);
			});
		});
	</script>
	{{-- include custom script for ads table [select all checkbox]  --}}
	<script>
		function checkAll(bx) {
			if (bx.type !== 'checkbox') {
				bx = document.getElementById('checkAll');
				bx.checked = !bx.checked;
			}
			
			var chkinput = document.getElementsByTagName('input');
			for (var i = 0; i < chkinput.length; i++) {
				if (chkinput[i].type == 'checkbox') {
					chkinput[i].checked = bx.checked;
				}
			}
		}
	</script>
@endsection
