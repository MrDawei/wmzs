<?php

Route::middleware(['web', 'Wmzs\\WebWeChat\\Middleware\\LoginMiddleware'])
    ->namespace('Wmzs\\WebWeChat\\Controllers')
    ->group(__DIR__.'/routes/web.php');
	
	
