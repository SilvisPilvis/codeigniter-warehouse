<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Warehouse Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center flex-wrap h-screen bg-gray-800">
    <?php include('navbar.php'); ?>
    <form action="<?= base_url('warehouse/'.$current_warehouse['id']."/product/".$current_product['id'].'/edit') ?>" method="post" class="flex flex-col gap-4 bg-teal-100 p-4 rounded-md h-fit my-auto">
        <label class="flex flex-col">
            Amount:
            <input type="number" name="product_count" id="" value="<?= $current_warehouse_product['product_count'] ?>" class="rounded-md bg-gray-200 text-center" min="1" max="100" step="1">
        </label>
        <label class="flex flex-col">
            Product:
            <select name="product_id" id="" class="rounded-md bg-gray-200 text-center">
            <?php foreach ($products as $product) : ?>
                    <?php if ($product['id'] == $current_product['id']) : ?>
                        <option selected value="<?= $product['id'] ?>"><?= $product['name'] ?></option>
                    <?php else : ?>
                        <option value="<?= $product['id'] ?>"><?= $product['name'] ?></option>
                    <?php endif; ?>
            <?php endforeach; ?>
            </select>
        </label>
        <label class="flex flex-col">
            Warehouse:
            <select name="warehouse_id" id="" class="rounded-md bg-gray-200 text-center">
            <?php foreach ($warehouses as $warehouse) : ?>
                    <?php if ($warehouse['id'] == $current_warehouse['id']) : ?>
                        <option selected value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?></option>
                    <?php else : ?>
                        <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?></option>
                    <?php endif; ?>
            <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="bg-emerald-300 rounded-md px-2">Save Changes</button>
    </form>
</body>
</html>