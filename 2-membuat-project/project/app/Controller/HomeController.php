<?php

namespace AriefKarditya\LocalDomainPhp\Controller;

use AriefKarditya\LocalDomainPhp\App\View;

class HomeController
{
    function index()
    {
        $model = [
            "title" => "Belajar PHP MVC",
            "content-title" => "Selamat Belajar PHP MVC dari Arief Karditya Hermawan",
            "content" => "Lorem ipsum dolor sit, amet consectetur adipisicing elit. Ab iure enim quae inventore illum qui molestiae? Tenetur eaque veniam necessitatibus adipisci iusto, porro corrupti esse nisi obcaecati! Et, esse autem.",
        ];

        View::render("Home/index", $model);
    }

    function hello()
    {
        echo "HomeController.hello()";
    }

    function world()
    {
        echo "HomeController.world()";
    }

    function About()
    {
        echo "Author: Arief Karditya Hermawan";
    }

    function login()
    {
        $model = [
            "title" => "Login",
            "content-title" => "Selamat Belajar PHP MVC dari Arief Karditya Hermawan",
            "content" => "Lorem ipsum dolor sit, amet consectetur adipisicing elit. Ab iure enim quae inventore illum qui molestiae? Tenetur eaque veniam necessitatibus adipisci iusto, porro corrupti esse nisi obcaecati! Et, esse autem.",
        ];

        View::render("Login/index", $model);

        $request = [
            "username" => $_POST['username'],
            "password" => $_POST['password'],
        ];

        $user = [];

        $response = [
            "message" => "Login Sukses",
        ];

        # Kirim response ke View
    }
}
