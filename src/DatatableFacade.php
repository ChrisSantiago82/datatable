<?php

namespace Chrissantiago82\Datatable;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Chrissantiago82\Datatable\Skeleton\SkeletonClass
 */
class DatatableFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'datatable';
    }
}
