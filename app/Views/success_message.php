<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucess</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col flex-wrap h-screen bg-gray-800">
    <?php include('navbar.php'); ?>
    <div class="flex flex-col items-center justify-center flex-wrap gap-4 mt-4">
        <p class="text-green-500 font-bold text-3xl"><?= $message ?></p>
        <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-2 flex justify-center items-center">Go Back</a>
    </div>
</body>
</html>