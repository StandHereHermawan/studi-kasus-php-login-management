<?php

namespace AriefKarditya\LocalDomainPhp\App {
    function header(string $value)
    {
        echo $value;
    }
}

namespace AriefKarditya\LocalDomainPhp\Service {
    function setcookie(string $name, string $value)
    {
        echo "$name: $value";
    }
}
