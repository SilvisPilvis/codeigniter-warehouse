<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Template</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js" integrity="sha256-AlTido85uXPlSyyaZNsjJXeCs07eSv3r43kyCVc8ChI=" crossorigin="anonymous"></script>
</head>
<body class="h-screen bg-gray-600">
    <?php include('navbar.php'); ?>
    <?php if (!empty($errors)) : ?>
        <div class="flex flex-col items-center justify-center flex-wrap gap-4">
            <pre>
                <?php foreach ($errors as $error) : ?>
                    <p class="text-red-500"><?= $error ?></p>
                <?php endforeach; ?>
            </pre>
            <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-2 text-center w-1/3 mx-auto mt-4">Go Back</a>
        </div>
    <?php endif ; ?>
    <div class="flex flex-col items-center justify-center flex-wrap gap-4 h-[90%]">
        <form action="<?= base_url('template/'.$id.'/edit') ?>" method="post" class="w-min flex flex-col items-center justify-center flex-wrap gap-4 mt-4">
            <input type="hidden" name="token" value="<?= csrf_token() ?>">
            <p>Type the name of the input fields then type <code class="bg-gray-300 rounded-md">;</code> to input the field type</p>
            <p>For example: name:text;</p>
            <label class="flex flex-col w-full items-center justify-center flex-wrap gap-4 mt-4">
                <textarea class=" h-96 bg-gray-300 rounded-md w-96 text-black resize-none" name="data" id="description" required><?= implode(';'.PHP_EOL, $categories); ?></textarea>
            </label>
            <button type="submit" class="bg-emerald-300 hover:bg-emerald-500 text-white font-bold py-2 px-4 rounded">Create</button>
        </form>
    </div>
</body>
</html>
