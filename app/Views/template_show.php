<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show categories</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js" integrity="sha256-AlTido85uXPlSyyaZNsjJXeCs07eSv3r43kyCVc8ChI=" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-600 h-screen">
<?php include('navbar.php'); ?>
<?php if ($categories): ?>
    <div class="flex flex-col items-center justify-evenly h-[90%]">
        <?php foreach($categories as $category): ?>
            <a class="flex justify-center items-center cursor-pointer text-black no-underline min-w-24 py-4 bg-sky-400 rounded-md" href="<?= base_url('template/'.$category->id.'/create') ?>"><?= $category->name ?></a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</body>
</html>
