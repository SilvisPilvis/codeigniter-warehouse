<?php
function string2array($string){
    return json_decode($string, true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show products</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center h-screen bg-gray-800">
    <?php include('navbar.php'); ?>

    <?php if($products == null) : ?>
        <div class="flex flex-col">
            <p class="text-red-500 text-center text-3xl">No products have been created</p>
            <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-2 text-center w-1/3 mx-auto mt-4">Go Back</a>
        </div>
    <?php endif; ?>

    <div class="flex flex-row flex-wrap gap-4 w-full box-border p-4">
        <?php foreach ($products as $product) : ?>
            <a href="<?= base_url('product/' . $product->id) ?>" class="black decoration-none rounded-md bg-teal-100 p-4 min-h-[10rem] max-h-max">
                <div class="flex flex-col items-center gap-2">
                    <p>Id: <?= $product->id ?></p>
                    <h1>Name: <?= $product->name ?></h1>
                    <?php foreach (json_decode($product->images, true) as $image) : ?>
                        <img src="<?= base_url($image) ?>" alt="" class="min-w-26 min-h-26 max-w-52 max-h-52 rounded-md object-cover mx-auto m-2">
                    <?php endforeach; ?>
                    <label class="flex flex-col">
                        Manufacturer:
                        <input type="text" name="date" readonly value="<?= $product->manufacturer ?>" class="rounded-md w-min text-center">
                    </label>
                    <label class="flex flex-col">
                        Volume in Cm³:
                        <input type="text" min="0.01" max="100" step="0.01" name="size" readonly value="<?= $product->size ?>" class="rounded-md w-min text-center">
                    </label>
                    <label class="flex flex-col">
                        Weight in Kg:
                        <input type="text" name="date" readonly value="<?= $product->weight ?>" class="rounded-md w-min text-center">
                    </label>
                    <label class="flex flex-col">
                        Created at:
                        <input type="datetime" name="date" readonly value="<?= $product->created_at ?>" class="rounded-md w-min text-center">
                    </label>
                    <div class="flex flex-row gap-4 mt-4 mb-4 justify-center">
                        <form action="<?= base_url('product/delete/' . $product->id) ?>" method="post">
                            <button class="bg-red-300 rounded-md px-2">Delete</button>
                        </form>
                        <form action="<?= base_url('product/edit/' . $product->id) ?>" method="get">
                            <button class="bg-emerald-300 rounded-md px-2">Edit</button>
                        </form>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
        <a href="<?= base_url('product/create') ?>" class="bg-orange-300 rounded-md h-full flex justify-center items-center p-4 h-max">Create A Product</a>
    </div>
</body>
</html>