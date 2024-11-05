<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product</title>
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

    <?php form_open_multipart('product/create') ?>
    <form action="<?= base_url('product/create') ?>" method="post" enctype="multipart/form-data" class="flex flex-col gap-4 bg-teal-100 p-4 rounded-md my-auto">
        <label class="flex flex-col">
            Product Name:
            <input type="text" name="name" class="rounded-md bg-gray-200 text-center">
        </label>
        <label class="flex flex-col">
            Image:
            <!-- <input type="text" name="image" class="rounded-md bg-gray-200 text-center"> -->
             <input type="file" name="image[]" multiple id="" class="rounded-md bg-gray-200" accept="image/*" size="10">
        </label>
        <label class="flex flex-col">
            Manufacturer:
            <input type="text" name="manufacturer" class="rounded-md bg-gray-200 text-center">
        </label>
        <label class="flex flex-col">
            Weight:
            <div class="flex flex-row">
                <input type="number" min="0.01" max="100" step="0.01" name="weight" class="rounded-md bg-gray-200 text-center">
                <p class="mx-2">Kg</p>
            </div>
        </label>
        <label class="flex flex-col">
            Size / Volume:
            <div class="flex flex-row">
                <input type="number" min="1" max="10000" name="size" class="rounded-md bg-gray-200 text-center">
                <p class="mx-2">Cm³</p>
            </div>
        </label>
        <button type="submit" class="bg-emerald-300 rounded-md px-2">Create</button>
    </form>
</body>
</html>