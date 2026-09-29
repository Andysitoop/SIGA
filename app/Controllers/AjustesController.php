<?php

namespace App\Controllers;

class AjustesController
{
    public function index(): void
    {
        $f3 = \Base::instance();
        $f3->set('title', 'Ajustes');
        $f3->set('content', 'ajustes/index.html');
        echo \Template::instance()->render('layouts/main.html');
    }
}
