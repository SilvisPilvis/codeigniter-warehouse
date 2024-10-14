<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show warehouses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center flex-wrap h-screen bg-gray-800">
    <?php include('navbar.php'); ?>
    <?php if($warehouses == null) : ?>
        <div class="flex flex-col my-auto">
            <p class="text-red-500 text-center text-3xl">No warehouses have been created</p>
            <div class="flex flex-row">
            <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-16 m-4 text-center flex justify-center items-center">Go Back</a>
            <a href="<?= base_url('warehouse/create') ?>" class="bg-orange-300 rounded-md flex justify-center items-center p-16 m-4">Create A Warehouse</a>
            </div>
        </div>
    <?php else : ?>
        <div class="flex flex-row flex-wrap">
        <?php foreach ($warehouses as $warehouse) : ?>
            <a href="<?= base_url('warehouse/' . $warehouse['id']) ?>" class="decoration-none rounded-md bg-teal-100 p-4 m-4">
                <div class="flex flex-col items-center gap-2">
                    <h1>Name: <?= $warehouse['name'] ?></h1>
                    <p>Id: <?= $warehouse['id'] ?></p>
                    <p>Address: <?= $warehouse['address'] ?></p>
                    <label class="flex flex-col">
                        Created at:
                        <input type="datetime" name="date" readonly value="<?= $warehouse['created_at'] ?>" class="rounded-md w-fit">
                    </label>
                    <div class="flex flex-row gap-4 mt-4 mb-4 justify-center">
                        <form action="<?= base_url('warehouse/delete/' . $warehouse['id']) ?>" method="post">
                            <button class="bg-red-300 rounded-md px-2">Delete</button>
                        </form>
                        <form action="<?= base_url('warehouse/edit/' . $warehouse['id']) ?>" method="get">
                            <button class="bg-emerald-300 rounded-md px-2">Edit</button>
                        </form>
                        <form action="<?= base_url('warehouse/' . $warehouse['id'] . '/product/add') ?>" method="get">
                            <button class="bg-orange-300 rounded-md px-2">Add product</button>
                        </form>
                        <form action="<?= base_url('warehouse/' . $warehouse['id'] . '/product') ?>" method="get">
                            <button class="bg-sky-300 rounded-md px-2">Show products</button>
                        </form>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
        <a href="<?= base_url('warehouse/create') ?>" class="bg-orange-300 rounded-md flex justify-center items-center p-16 m-4">Create A Warehouse</a>
        </div>
    <?php endif; ?>
</body>
</html>