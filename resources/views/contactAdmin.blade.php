<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Admin</title>
</head>

<body>
    <h1>Nouveau contact</h1>
    <h2>Nom : {{ $param[0]['nom'] }}</h2>
    <h2>Email : {{ $param[0]['email'] }}</h2>
    <p>Message : {{ $param[0]['message'] }}</p>
</body>

</html>