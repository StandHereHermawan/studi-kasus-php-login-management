<?php

namespace AriefKarditya\LocalDomainPhp\Controller;

use AriefKarditya\LocalDomainPhp\App\View;

class HomeController
{
    function index()
    {
        View::render('Home/index', [
            "title" => "PHP Login Management"
        ]);
    }
}
