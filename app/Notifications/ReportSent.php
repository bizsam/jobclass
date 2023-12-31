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

namespace App\Notifications;

use App\Helpers\UrlGen;
use App\Models\Post;
use App\Models\ReportType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReportSent extends Notification implements ShouldQueue
{
	use Queueable;
	
	protected $post;
	protected $report;
	
	public function __construct(Post $post, $report)
	{
		$this->post = $post;
		$this->report = $report;
	}
	
	public function via($notifiable)
	{
		if (isDemoDomain()) {
			return [];
		}
		
		return ['mail'];
	}
	
	public function toMail($notifiable)
	{
		$postUrl = UrlGen::post($this->post);
		
		$reportType = ReportType::find($this->report->report_type_id);
		
		$mailMessage = (new MailMessage)
			->replyTo($this->report->email, $this->report->email)
			->subject(trans('mail.post_report_sent_title', [
				'appName'     => config('app.name'),
				'countryCode' => $this->post->country_code,
			]))
			->line(trans('mail.Post URL') . ': <a href="' . $postUrl . '">' . $postUrl . '</a>');
		
		if (!empty($reportType)) {
			$mailMessage->line(t('Reason') . ': ' . $reportType->name);
		}
		
		$mailMessage->line(t('Message') . ': <br>' . nl2br($this->report->message))
			->salutation(trans('mail.footer_salutation', ['appName' => config('app.name')]));
		
		return $mailMessage;
	}
}
