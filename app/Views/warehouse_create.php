<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Warehouse</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center flex-wrap h-screen bg-gray-800">
    <?php include('navbar.php'); ?>
    <form action="<?= base_url('warehouse/create') ?>" method="post" class="flex flex-col gap-4 bg-teal-100 p-4 rounded-md my-auto">
        <label class="flex flex-col">
            Warehouse Name:
            <input type="text" name="name" class="rounded-md bg-gray-200 text-center">
        </label>
        <label class="flex flex-col">
            Warehouse Address:
            <input type="text" name="address" class="rounded-md bg-gray-200 text-center">
        </label>
        <button type="submit" class="bg-emerald-300 rounded-md px-2 text-center">Create</button>
    </form>
</body>
</html>