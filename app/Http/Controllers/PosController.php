<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class PosController extends Controller
{
    public function index()
    {
        // Pass any initial data (e.g., recent products)
        return view('pos.index');
    }

    // These cart methods will be AJAX, but we will manage cart in JavaScript session/localStorage.
    // Actually we don't need server-side cart storage for simplicity.
    // The checkout will be handled by SaleController.
}
