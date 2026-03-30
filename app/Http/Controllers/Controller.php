<?php

namespace App\Http\Controllers;

// 1. Thêm dòng này
use Illuminate\Routing\Controller as BaseController; 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

// 2. Sửa dòng class này: Thêm "extends BaseController"
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}