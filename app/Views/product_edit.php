<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit a product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js" integrity="sha256-AlTido85uXPlSyyaZNsjJXeCs07eSv3r43kyCVc8ChI=" crossorigin="anonymous"></script>
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
        if (confirm('Are you sure you want to delete this image?')) {
            $.ajax({
                url: `${window.location.origin}/product/${productId}/images/${productName}`,
                type: 'POST',
                success: function(response) {
                    // Remove the parent div containing both the button and image
                    $(`img[src$="${productName}"]`).parent().remove();
                    alert('Image deleted successfully');
                },
                error: function(xhr, status, error) {
                    console.log(error);
                    alert('Error deleting image: ' + error);
                }
            });
        }
    }

    let tags = [];

    //runs when you add a new tag
    function addTagToList(input) {
        //add tag to array
        tags.push(input.value);
        //set hidden input to all tags
        $("#tags-hidden").val(tags.join("|"));
        //dont allow numbers and if last char | then remove | and if | is typed then remove |
    }

    function removeTagFromList(tag, tagElement) {
        tagElement.remove();
        tags = Array.from($("#tags-hidden").val().split("|"));
        tags = tags.filter(t => t !== tag);  // Assign the filtered result back to tags
        $("#tags-hidden").val(tags.join("|"));
    }

    function submitTags() {
        console.log($("#tags").val(tags.join("|")));
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
        <form action="<?= base_url('product/' . $product->id.'/images/delete') ?>" method="post">
            <button class="bg-red-300 rounded-md flex justify-center items-center m-2">Delete Images</button>
        </form>
        <form action="<?= base_url('product/edit/' . $product->id) ?>" enctype="multipart/form-data" method="post" class="flex flex-col gap-4 bg-teal-100 p-4 rounded-md my-auto">
            <label class="flex flex-col">
                Product Id:
                <input type="number" name="id" id="" value="<?= $product->id ?>" readonly class="rounded-md bg-gray-200 text-center">
            </label>
            <label class="flex flex-col">
                Product Name:
                <input type="text" name="name" id="" value="<?= $product->name ?>" class="rounded-md bg-gray-200 text-center">
            </label>
            <label class="flex flex-col flex-wrap gap-4">
                Tags:
                <div class="flex flex-row flex-wrap gap-4">
                    <?php if($product->tags) : ?>
                        <input type="text" list="tagList" placeholder="Add Tags" id="tags" class="rounded-md bg-gray-200 text-center" onchange="addTagToList(this)">
                        <input type="hidden" name="tags" id="tags-hidden" value="<?= implode('|', json_decode($product->tags)) ?>">
                        <?php foreach(json_decode($product->tags) as $tag) : ?>
                            <p class="text-center text-white bg-indigo-900 px-2 rounded-md" onclick="removeTagFromList(this.innerText, this)"><?= $tag ?></p>
                        <?php endforeach; ?>
                        <datalist id="tagList">
                            <?php foreach(json_decode($product->tags) as $tag) : ?>
                                <option value="<?= $tag ?>"><?= $tag ?></option>
                            <?php endforeach; ?>
                        </datalist>
                    <?php else : ?>
                    <p class="text-center text-gray-400">No Tags</p>
                    <input type="text" list="tagList" placeholder="Add Tags" id="tags" class="rounded-md bg-gray-200 text-center" onchange="addTagToList(this)">
                    <input type="hidden" name="tags" id="tags-hidden" value="">
                    <datalist id="tagList">
                    </datalist>
                    <?php endif; ?>
                </div>
            </label>
            <label class="flex flex-col">
                Images:
                <input type="file" name="image[]" multiple onchange="readURL(this);" id="" class="rounded-md bg-gray-200" accept="image/*" size="10">
                <div class="flex flex-row flex-wrap gap-4">
                    <div id="preview" class="h-max outline outline-2 outline-cyan-300 rounded-md w-fit p-2 mt-4"></div>
                    <?php foreach (json_decode($product->images, true) as $key => $image) : ?>
                        <div class="relative inline-block" id="preview<?= $key ?>">
                        <button 
                            onclick="removeImageFromDb(<?= $product->id ?>, '<?= basename($image) ?>')" 
                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center z-10"
                            type="button">
                            ×
                        </button>
                        <img 
                            src="<?= base_url($image) ?>" 
                            alt="" 
                            class="min-w-26 min-h-26 max-w-52 max-h-52 rounded-md object-cover mx-auto m-2">
                        </div>
                    <?php endforeach; ?>
                </div>
            </label>
            <label class="flex flex-col">
                Manufacturer:
                <input type="text" name="manufacturer" value="<?= $product->manufacturer ?>" class="rounded-md bg-gray-200 text-center">
            </label>
            <label class="flex flex-col">
                Volume in Cm³:
                <input type="number" min="0.01" max="100" step="0.01" name="size" value="<?= $product->size ?>" class="rounded-md bg-gray-200 text-center">
            </label>
            <label class="flex flex-col">
                Weight in Kg:
                <input type="number" name="weight" value="<?= $product->weight ?>" class="rounded-md bg-gray-200 text-center">
            </label>
            <label class="flex flex-col">
                Created at:
                <input type="datetime" name="date" readonly value="<?= $product->created_at ?>" class="rounded-md bg-gray-200 text-center">
            </label>
            <button class="bg-emerald-300 rounded-md flex justify-center items-center m-2">Save Changes</button>
        </form>
        <?php endif; ?>
</body>
</html>
