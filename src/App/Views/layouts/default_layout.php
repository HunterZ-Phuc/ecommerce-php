<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?? 'Website' ?></title>
</head>

<body>
    <?php require_once 'header.php'; ?>

    <main>
        <?= $content ?>
    </main>

    <?php require_once 'footer.php'; ?>
</body>

</html>