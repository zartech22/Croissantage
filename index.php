<?php
    session_start();

    function getStudent(int $id) : array
    {
        $dsn = 'mysql:host=127.0.0.1;dbname=croissantage;port=3308';
        $pdo = new PDO($dsn, 'root', '');

        $req = $pdo->prepare('SELECT * FROM student WHERE id = :id');

        $req->execute(['id' => $id]);

        return $req->fetch(PDO::FETCH_ASSOC);
    }

    $student = null;


    if(isset($_GET['studentId']))
    {
        $student = getStudent($_GET['studentId']);
        $_SESSION['studentId'] = $_GET['studentId'];
    }
    else if(isset($_SESSION['studentId']))
        $student = getStudent($_SESSION['studentId']);
    ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Test</title>
        <meta charset="utf-8" />
    </head>
    <body>
        <h1>Page de test</h1>
        <p>
            Pour afficher le nom d'un étudiant, donnez son id dans l'url : <br />
            monUrl?studentId=1<br />

            Si vous avez déjà regarder un étudiant, celui-ci sera réaffiché sur cette page.
        </p>
        <?php if($student !== null): ?>
            <p>L'étudiant est : <?php echo $student['alias']; ?></p>
        <?php endif; ?>

    </body>
</html>
