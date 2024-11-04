<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit a product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link class="jsbin" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
    <script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js"></script>
</head>
<body class="flex flex-col items-center h-screen w-screen bg-gray-800">
    <?php include('navbar.php'); ?>

<script type="text/javascript" lang="js">
    function readURL(input) {
        // console.log(input.files);
        if (input.files && input.files.length > 0) {
            // Clear existing previews
            $('#preview').empty();
            
            // Loop through each file
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    addPreviewImage(e.target.result, index, file.name);
                };
                
                reader.readAsDataURL(file);
            });
        }
    }

    // function addPreviewImage(src, index, fileName) {
    //     $('#preview').append(`
    //         <img 
    //             id="preview${index}" 
    //             src="${src}" 
    //             alt="Preview of ${fileName}" 
    //             class="min-w-26 min-h-26 max-w-52 max-h-52 rounded-md object-cover mx-auto m-2"
    //         />
    //     `);
    // }

    function addPreviewImage(src, index, fileName) {
    $('#preview').append(`
        <div class="relative inline-block">
            <button 
                onclick="removeFromPreview(${index}, self)" 
                class="absolute top-2 right-2 bg-red-500 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center z-10"
                type="button"
            >
                ×
            </button>
            <img 
                id="preview${index}"
                src="${src}"
                alt="Preview of ${fileName}"
                class="min-w-26 min-h-26 max-w-52 max-h-52 rounded-md object-cover mx-auto m-2"
            />
        </div>
    `);
    }

    function removeFromPreview(id, input) {

        // delete input.files[id];
        const fileInput = document.querySelector('input[type="file"]');
        // Create a new FileList without the removed file
        const dt = new DataTransfer();
        Array.from(fileInput.files).forEach((file, index) => {
            if (index !== id) {
                dt.items.add(file);
            }
        });
        fileInput.files = dt.files;
        
        $(`#preview${id}`).parent().remove();
    }

    function removeImageFromDb(productId, productName) {
        delete input.files[id];
        $('#preview').remove(`#preview${id}`)
        // send post req to /product/(:num)/images/(:str)
        $.post(`http://localhost:8000/product/${productId}/images/${productName}`,
        {
            // name: "Donald Duck",
            // city: "Duckburg"
        },
        function(data, status){
            alert("Data: " + data + "\nStatus: " + status);
        });
    }

    function removeImageFromDb(productId, productName) {
        if (confirm('Are you sure you want to delete this image?')) {
            $.ajax({
                url: `${window.location.origin}/product/${productId}/images/${productName}`,
                type: 'POST',
                success: function(response) {
                    // Remove the parent div containing both the button and image
                    // $(`img[src$="${productName}"]`).parent().remove();
                    $('#preview').remove(`#preview${id}`)
                    alert('Image deleted successfully');
                },
                error: function(xhr, status, error) {
                    console.log(error);
                    alert('Error deleting image: ' + error);
                }
            });
        }
    }
</script>

    
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
        <form action="<?= base_url('product/' . $product['id'].'/images/delete') ?>" method="post">
            <button class="bg-red-300 rounded-md flex justify-center items-center m-2">Delete Images</button>
        </form>
        <form action="<?= base_url('product/edit/' . $product['id']) ?>" enctype="multipart/form-data" method="post" class="flex flex-col gap-4 bg-teal-100 p-4 rounded-md my-auto">
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
                    <p>No Image</p>
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
                <?php foreach (json_decode($product['metadata'], true) as $key => $metadata) : ?>
                    <label class="flex flex-col justify-center items-center">
                        <?= ucfirst($key) ?>:
                        Metadata: <?= gettype($metadata) ?>

                        <?php if($key == 'image' && $metadata != null) : ?>
                            <?php if(gettype($metadata) == 'string' && $metadata[0] != "["): ?>
                                <input type="file" name="<?= $key ?>[]" multiple onchange="readURL(this);" id="" class="rounded-md bg-gray-200" accept="image/*" size="10">
                                <div id="preview" class="h-max outline outline-2 outline-cyan-300 rounded-md w-fit p-2 mt-4">
                                </div>
                            <?php elseif (gettype($metadata) == 'string' && $metadata[0] == "[") : ?>
                                <input type="file" name="<?= $key ?>[]" multiple onchange="readURL(this);" id="" class="rounded-md bg-gray-200" accept="image/*" size="10">
                                <div id="preview" class="h-max outline outline-2 outline-cyan-300 rounded-md w-fit p-2 mt-4">
                                </div>
                                <div class="w-full flex flex-rows">
                                    <?php foreach (json_decode($metadata, true) as $image) : ?>
                                        <?php if (gettype($metadata) == 'string' && $metadata[0] == "[") : ?>
                                                <div class="relative inline-block">
                                                    <button 
                                                        onclick="removeImageFromDb(<?= $product['id'] ?>, '<?= basename($image) ?>')" 
                                                        class="absolute top-2 right-2 bg-red-500 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center z-10"
                                                        type="button"
                                                    >
                                                        ×
                                                    </button>
                                                    <img 
                                                        src="<?= base_url($image) ?>" 
                                                        alt="" 
                                                        class="min-w-26 min-h-26 max-w-52 max-h-52 rounded-md object-cover mx-auto m-2"
                                                    >
                                                </div>
                                        <?php endif ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif ?>

                        <?php if($key == 'image' && $metadata == null) : ?>
                            <input type="file" name="<?= $key ?>[]" multiple onchange="readURL(this);" id="" class="rounded-md bg-gray-200" accept="image/*" size="10">
                            <div id="preview" class="h-max outline outline-2 outline-cyan-300 rounded-md w-fit p-2 mt-4"></div>
                        <?php endif; ?>
                        
                        <?php if($key == 'weight' || $key == 'size') : ?>
                            <input type="text" id="" name="<?= $key ?>" value="<?= preg_replace("/[^0-9]/", '', $metadata) ?>" class="rounded-md bg-gray-200 text-center">
                        <?php else : ?>
                            <?php if($key != "image"): ?>
                                <input type="text" name="<?= $key ?>" value="<?= $metadata ?>" class="rounded-md bg-gray-200 text-center">
                            <?php endif; ?>
                        <?php endif; ?>


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