<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language.
     *
     * @param string $lang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang($lang)
    {
        if (array_key_exists($lang, ['en' => 'English', 'bn' => 'Bengali'])) {
            Session::put('applocale', $lang);
        }
        return redirect()->back();
    }
}
