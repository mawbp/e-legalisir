<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Registrasi;
use App\Models\Permohonan;
use App\Models\Pengaturan;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    	View::composer('admin.home', function($view){
    		$regis = Registrasi::where('status', 'Menunggu')->count();
    		$mohon = Permohonan::where('status_permohonan', 'Validasi Dokumen')->distinct('permohonan_id')->count();
    		$view->with([
    			'regis' => $regis,
    			'mohon' => $mohon,
    		]);
    	});

        View::composer('user.home', function($view){
            $ckey = Pengaturan::where('nama', 'ckey_midtrans')->value('nilai');
            $view->with([
                'ckey' => $ckey,
            ]);
        });

      
    }
}
