<?php
namespace Lgy\RPush;

use \Illuminate\Support\Facades\Facade;

class RPushFacade extends Facade {

    protected static function getFacadeAccessor() {
        return 'rpush';
    }
}
