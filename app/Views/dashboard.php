<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col h-full gap-4 bg-gray-800">
    <?php include('navbar.php'); ?>
    <div class="flex flex-row gap-4 h-10 p-2">
        <a href="<?= base_url('warehouse') ?>" class="bg-emerald-300 rounded-md w-full h-full flex justify-center items-center p-8">Warehouses</a>
        <a href="<?= base_url('product') ?>" class="bg-sky-300 rounded-md w-full h-full flex justify-center items-center p-8">Products</a>
    </div>
</body>
</html>