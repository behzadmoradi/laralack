<?php
namespace App\Http\Controllers;

class GeneralController extends Controller
{
    public function homepage()
    {
        return view('welcome');
    }

    public function workspace()
    {
        return view('workspace.index');
    }

    public function markdownGuide()
    {
        return view('pages.markdown');
    }

    public function help()
    {
        return view('pages.help');
    }
}
