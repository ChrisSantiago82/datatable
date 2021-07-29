<?php

namespace Chrissantiago82\Datatable;

use Chrissantiago82\Datatable\Http\Livewire\Main;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class DatatableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        Livewire::component('main', Main::class);

        $this->loadViewsFrom(__DIR__.'/../resources/views/datatable', 'table');

    }
    
}
