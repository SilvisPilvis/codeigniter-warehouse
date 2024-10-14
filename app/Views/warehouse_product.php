<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Warehouse Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center flex-wrap h-screen bg-gray-800">
    <?php include('navbar.php'); ?>
    
    <?php if($warehouse_product == null) : ?>
        <div class="flex flex-col my-auto">
            <p class="text-red-500 text-center text-3xl">No products in this warehouse</p>
            <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-2 text-center w-1/3 mx-auto mt-4">Go Back</a>
        </div>
    <?php endif; ?>

    <div class="flex flex-row flex-wrap gap-4 w-full box-border p-4">        
        <?php foreach ($warehouse_product as $product) : ?>
            <div class="flex flex-col items-center gap-2 bg-teal-100 p-4 rounded-md">
                <p>Warehouse: <?= $product['warehouse_name'] ?></p>
                <Address>Address: <?= $product['address'] ?></Address>
                <p>Product: <?= $product['product_count'] ?>x <?= $product['product_name'] ?></p>
                <label class="flex flex-col">
                    Updated at:
                    <input type="datetime" name="date" id="" value="<?= $product['updated_at'] ?>" readonly class="rounded-md bg-gray-200 text-center">
                </label>
                <div class="flex flex-row gap-4">
                    <form action="<?= base_url('warehouse/'.$product['warehouse_id'].'/product/'.$product['product_id'].'/delete') ?>" method="get">
                        <button type="submit" class="bg-red-300 rounded-md px-2">Delete</button>
                    </form>
                    <form action="<?= base_url('warehouse/'.$product['warehouse_id'].'/product/'.$product['product_id'].'/edit') ?>" method="get">
                        <button type="submit" class="bg-emerald-300 rounded-md px-2">Edit</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>