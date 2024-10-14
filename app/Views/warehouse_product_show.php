<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Warehouse Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center flex-wrap h-screen bg-gray-800">
    <?php include('navbar.php'); ?>

    <?php if (!empty($errors)): ?>
        <pre>
            <?php foreach ($errors as $error) : ?>
                <p class="text-red-500"><?= $error ?></p>
            <?php endforeach; ?>
        </pre>
        <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-2 text-center w-1/3 mx-auto mt-4">Go Back</a>
    <?php endif; ?>
    
    <form action="<?= base_url('warehouse/'.$warehouse['id'].'/product/add') ?>" method="post" class="flex flex-col gap-4 bg-teal-100 p-4 rounded-md my-auto">
        <p class="text-3xl">Warehouse <?= $warehouse['name'] ?></p>
        <label class="flex flex-col">
            Choose a product to add to warehouse <?= $warehouse['name'] ?>
            <select name="product_id" id="" class="rounded-md bg-gray-200 text-center">
                <?php foreach ($products as $product) : ?>
                    <option value="<?= $product['id'] ?>" class="text-center"><?= $product['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="flex flex-col">
            Choose the amount to add
            <input type="number" name="product_count" id="" min="1" max="100" step="1" class="rounded-md bg-gray-200 text-center" value="1">
        </label>
        <button type="submit" class="bg-emerald-300 rounded-md px-2">Add</button>
    </form>

</body>
</html>