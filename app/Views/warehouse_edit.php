<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit a warehouse</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center flex-wrap h-screen bg-gray-800">
    <?php include('navbar.php'); ?>
    <form action="<?= base_url('warehouse/edit/' . $warehouse['id']) ?>" method="post" class="flex flex-col gap-4 bg-teal-100 p-4 rounded-md my-auto">
        <label class="flex flex-col">
            Warehouse Name:
            <input type="text" name="name" id="" value="<?= $warehouse['name'] ?>" class="rounded-md bg-gray-200 text-center">
        </label>
        <label class="flex flex-col">
            Warehouse Id:
            <input type="number" name="id" id="" value="<?= $warehouse['id'] ?>" readonly class="rounded-md bg-gray-200 text-center">
        </label>
        <label class="flex flex-col">
            Warehouse Address:
            <input type="text" name="address" id="" value="<?= $warehouse['address'] ?>" class="rounded-md bg-gray-200 text-center">
        </label>
        <label class="flex flex-col">
            Created at:
            <input type="datetime" name="date" value="<?= $warehouse['created_at'] ?>" readonly class="rounded-md bg-gray-200 text-center">
        </label>
        <button class="bg-emerald-300 rounded-md px-2">Save Changes</button>
    </form>
</body>
</html>