<?php

use App\Helpers\DBTool;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

try {
	
	/* FILES */
	File::delete(app_path('Http/Controllers/Web/Post/DetailsController.php'));
	File::delete(resource_path('views/post/details.blade.php'));
	
	
	
	/* DATABASE */
	
	
	
} catch (\Exception $e) {
	dump($e->getMessage());
	dd('in ' . str_replace(base_path(), '', __FILE__));
}
