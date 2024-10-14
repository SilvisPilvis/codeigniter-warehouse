<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucess</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center flex-wrap h-screen bg-gray-800">
    <?php include('navbar.php'); ?>
    <?php if (gettype($errors) == 'array') : ?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li class="text-red-500 font-bold text-3xl"><?= $error ?></li>
            <?php endforeach; ?>
            <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-2 flex justify-center items-center">Go Back</a>
        </ul>
    <?php else : ?>
        <p class="text-red-500 font-bold text-3xl"><?= $errors ?></p>
        <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-2 flex justify-center items-center">Go Back</a>
    <?php endif; ?>
</body>
</html>