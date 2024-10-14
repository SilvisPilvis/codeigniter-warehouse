<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit a product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center flex-wrap h-screen bg-gray-800">
    <?php include('navbar.php'); ?>

    <?php if(isset($errors)) : ?>
    <?php if (gettype($errors) == 'array') : ?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li class="text-red-500 font-bold text-3xl"><?= $error ?></li>
            <?php endforeach; ?>
            <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-2 flex justify-center items-center">Go Back</a>
        </ul>
    <?php else : ?>
        <p class="text-red-500 font-bold text-3xl"><?= $errors ?></p>
        <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-2 flex justify-center items-center">Go Back</a>
    <?php endif; ?>
    <?php else : ?>
    <form action="<?= base_url('product/edit/' . $product['id']) ?>" method="post" class="flex flex-col gap-4 bg-teal-100 p-4 rounded-md my-auto">
        <label class="flex flex-col">
            Product Id:
            <input type="number" name="id" id="" value="<?= $product['id'] ?>" readonly class="rounded-md bg-gray-200 text-center">
        </label>
        <label class="flex flex-col">
            Product Name:
            <input type="text" name="name" id="" value="<?= $product['name'] ?>" class="rounded-md bg-gray-200 text-center">
        </label>
        <?php if($product['metadata'] == null) : ?>
            <label class="flex flex-col">
                Image URL:
                <input type="text" name="image" id="" value="No Data" class="rounded-md bg-gray-200 text-center">
            </label>
            <label class="flex flex-col">
                Manufacturer:
                <input type="text" id="" name="manufacturer" value="No Data" class="rounded-md bg-gray-200 text-center">
            </label>
            <label class="flex flex-col">
                Weight:
                <div class="flex flex-row">
                    <input type="number" name="weight" id="" value="0" class="rounded-md bg-gray-200 text-center">
                    <p class="mx-2">Kg</p>
                </div>
            </label>
            <label class="flex flex-col">
                Size / Volume:
                <div class="flex flex-row">
                    <input type="number" name="size" id="" value="0" class="rounded-md bg-gray-200 text-center">
                    <p class="mx-2">Kg</p>
                </div>
            </label>
        <?php else : ?>
            <?php foreach (json_decode($product['metadata'], true) as $key => $value) : ?>
            <label class="flex flex-col">
                <?= ucfirst($key) ?>:
                <?php if($key == 'image' || $key == 'manufacturer') : ?>
                    <input type="text" id="" name="<?= $key ?>" value="<?= $value ?>" class="rounded-md bg-gray-200 text-center">
                <?php continue; ?>
                <?php endif; ?>
                <input type="text" id="" name="<?= $key ?>" value="<?= preg_replace("/[^0-9]/", '', $value) ?>" class="rounded-md bg-gray-200 text-center">
            </label>
            <?php endforeach; ?>
        <?php endif; ?>
        <label class="flex flex-col">
            Created at:
            <input type="datetime" name="date" value="<?= $product['created_at'] ?>" readonly class="rounded-md bg-gray-200 text-center">
        </label>
        <button class="bg-emerald-300 rounded-md flex justify-center items-center m-2">Save Changes</button>
    </form>
    <?php endif; ?>

</body>
</html>