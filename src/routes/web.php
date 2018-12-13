<?php

Route::get('/test-package', function(){
	return 'test path =>'. base_path().'/tests';
});
