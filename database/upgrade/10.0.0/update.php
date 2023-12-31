<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

try {
	
	/* FILES */
	$oldLangBaseDir = rtrim(resource_path('lang'), '/') . '/';
	$newLangBaseDir = rtrim(base_path('lang'), '/') . '/';
	$oldLangDirsArray = array_filter(glob($oldLangBaseDir . '*'), 'is_dir');
	if (!empty($oldLangDirsArray)) {
		foreach ($oldLangDirsArray as $oldLangDir) {
			$newLangDir = $newLangBaseDir . basename($oldLangDir);
			if (!File::exists($newLangDir)) {
				File::moveDirectory($oldLangDir, $newLangDir, false);
			}
		}
	}
	
	$oldDir = resource_path('lang/');
	$newDir = resource_path('lang.backup/');
	if (File::exists($oldDir)) {
		File::moveDirectory($oldDir, $newDir, true);
	}
	
	File::deleteDirectory(resource_path('docs/'));
	
	File::delete(app_path('Http/Resources/SavedPostsResource.php'));
	
	
	/* DATABASE */
	
	
} catch (\Exception $e) {
	dump($e->getMessage());
	dd('in ' . str_replace(base_path(), '', __FILE__));
}
