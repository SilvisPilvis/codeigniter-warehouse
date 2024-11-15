<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Template</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js" integrity="sha256-AlTido85uXPlSyyaZNsjJXeCs07eSv3r43kyCVc8ChI=" crossorigin="anonymous"></script>
</head>
<body class="h-screen bg-gray-600">
<?php include('navbar.php'); ?>
    <div class="flex flex-col items-center justify-center flex-wrap gap-4 h-[90%]">
        <form action="<?= base_url('template/'.$id.'/create') ?>" method="post" class="w-min flex flex-col items-center justify-center flex-wrap gap-4 mt-4">
            <input type="hidden" name="token" value="<?= csrf_token() ?>">
            <!-- <p>+</p> -->
            <p>Type the name of the input fiels then type <code class="bg-gray-300 rounded-md">;</code> to type the input type</p>
            <label class="flex flex-col w-full items-center justify-center flex-wrap gap-4 mt-4">
                <textarea class=" h-96 bg-gray-300 rounded-md w-96 text-black resize-none" name="data" id="description" required><?= implode(';'.PHP_EOL, $categories); ?></textarea>
            </label>
            <button type="submit" class="bg-emerald-300 hover:bg-emerald-500 text-white font-bold py-2 px-4 rounded">Create</button>
        </form>
    </div>
</body>
</html>
