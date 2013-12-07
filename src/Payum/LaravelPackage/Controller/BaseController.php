<?php
namespace Payum\LaravelPackage\Controller;

use Illuminate\Routing\Controllers\Controller;

class BaseController extends Controller
{
     /**
     * {@inheritDoc}
     */
    protected function setupLayout()
    {
        if (false == is_null($this->layout)) {
            $this->layout = \View::make($this->layout);
        }
    } 
}
