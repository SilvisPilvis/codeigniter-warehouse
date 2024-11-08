<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js" integrity="sha256-AlTido85uXPlSyyaZNsjJXeCs07eSv3r43kyCVc8ChI=" crossorigin="anonymous"></script>
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

    <script>
        let tags = [];

        //runs when you add a new tag
        function addTagToList(input) {
            //add tag to array
            tags.push(input.value);
            //set hidden input to all tags
            $("#tags-hidden").val(tags.join("|"));
            //dont allow numbers and if last char | then remove | and if | is typed then remove |
        }

        function removeTagFromList(tag) {
            $("#tags").val(tags.filter(t => t !== tag));
            $("#tags-hidden").val(tags.filter(t => t !== tag).join("|"));
            console.log($("#tags-hidden").val());
        }

        function submitTags() {
            console.log($("#tags").val(tags.join("|")));
        }
    </script>

    <?php form_open_multipart('product/create') ?>
    <form action="<?= base_url('product/create') ?>" method="post" enctype="multipart/form-data" class="flex flex-col gap-4 bg-teal-100 p-4 rounded-md my-auto">
        <label class="flex flex-col">
            Product Name:
            <input type="text" name="name" class="rounded-md bg-gray-200 text-center">
        </label>
        <label class="flex flex-col">
            Tags:
            <div class="flex flex-row flex-wrap gap-4">
                <input type="text" list="tagList" placeholder="Add Tags" id="tags" class="rounded-md bg-gray-200 text-center" onchange="addTagToList(this)">
                <input type="hidden" name="tags" id="tags-hidden" value="">
            </div>
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
