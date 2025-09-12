<?php
public const HOME = '/dashboard'; // keep this

// Then add this method:
public function boot()
{
    parent::boot();

    Route::middleware('web')
        ->group(function () {
            Route::get('/dashboard', function () {
                if (auth()->user()->role === 'admin') {
                    return redirect('/admin');
                } else {
                    return redirect('/blog');
                }
            });
        });
}
?>