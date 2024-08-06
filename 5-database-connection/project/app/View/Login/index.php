<?php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $model['title'] ?></title>
</head>

<body>
    <div>
        <h1><?= $model['title-content'] ?></h1>
        <p><?= $model['content'] ?></p>
    </div>
    <!-- form:post>(label{Username : }>input:text)+br+(label{Password : }>input:password)+br+input:submit -->
    <form action="" method="post">
        <label>Username :
            <input type="text" name="username-login" id="username-login" />
        </label>
        <br />
        <label>Password :
            <input type="password" name="password-login" id="password-login" />
        </label>
        <br />
        <input type="submit" value="Login" />
    </form>
</body>

</html>