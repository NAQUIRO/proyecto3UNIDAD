<?php

namespace App\Http\Controllers;

use App\Models\Congress;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Display the agenda page
     */
    public function index(Congress $congress)
    {
        return view('agenda.index', compact('congress'));
    }
}

