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

    function addField() {
        console.log("Adding field");
        let name = prompt("Enter field name");
        $("#form").append(`
            <label class="flex flex-col my-6">
                ${name}:
                <input type="text" class="rounded-md bg-gray-200 text-center" name="${name}">
            </label>
        `);
    }



    // $(document).ready(
    // function() {
    //     // console.log(document.getElementById("category").value);
    //     let temp = document.getElementById("category").value;
    //     if (temp !== undefined){
    //         templates = getFilledTemplates(temp);
    //     }
    //    // getFilledTemplates(temp);
    //    }
    // );

    $(document).ready(
        function() {
            getFields($("#category").val());
        }
    );
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
            <button class="bg-red-300 rounded-md flex justify-center items-center m-2 p-2">Delete Images</button>
        </form>
        <form id="form" action="<?= base_url('product/edit/' . $product->id) ?>" enctype="multipart/form-data" method="post" id="form" class="flex flex-col gap-4 bg-teal-100 p-4 rounded-md my-auto">
            <label class="flex flex-col">
                Product Id:
                <input type="number" name="id" id="product-id" value="<?= $product->id ?>" readonly class="rounded-md bg-gray-200 text-center">
            </label>
            <label class="flex flex-col">
                Product Name:
                <input type="text" name="name" id="" value="<?= $product->name ?>" class="rounded-md bg-gray-200 text-center">
            </label>
            <label class="flex flex-col">
                Categories:
                <?php if ($categories) : ?>
                <select id="category" class="rounded-md bg-gray-200 text-center" oninput="addCategory()" onchange="getFields(this)">
                <option value="<?= $current_category->id ?>" selected><?= $current_category->name ?></option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?= $category->id ?>"><?= $category->name ?></option>
                <?php endforeach; ?>
                </select>
                <?php endif; ?>
                <input type="hidden" name="category_id" id="post-categories" value="" class="rounded-md bg-gray-200 text-center">
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
            <?php if(count($dynamic_fields) > 0) : ?>
                <?php foreach(json_decode($dynamic_fields[0]->template) as $field => $value) : ?>
                <label class="flex flex-col">
                    <?= $field ?>:
                    <input type="text" name="<?= $field ?>" value="<?= $value ?>" class="rounded-md bg-gray-200 text-center">
                </label>
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- <div class="bg-emerald-300 rounded-md flex justify-center items-center m-2" onclick="addField()">Add Field</div> -->
            <input id="button" type="submit" class="bg-emerald-300 rounded-md flex justify-center items-center m-2" value="Save Changes">
        </form>
        <?php endif; ?>
        <script type="text/javascript" lang="js">
        // console.log($("#category").val());
        let templates = [];

        // gets the values for the fields
        function getFilledTemplates(data)
        {
            // this should be the product id
            if(data === undefined){
                console.error("Product id is undefined");
                return;
            }
            let url = 'http://localhost:8080/product/'+data+'/template'
            // let url = 'http://localhost:8080/product/'+data+'/template'

            $.ajax({
                url: url,
                type: "GET",
                success: function(data) {
                    // console.log(data);
                    // console.log(JSON.parse(data));
                    templates = JSON.parse(data);
                    return templates;
                    // return JSON.parse(data);
                }
           });

        }

        let values;

        function getValues() {
            // Return promise for better async handling
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: 'http://localhost:8080/template/value-sets',
                    type: "GET",
                    success: function(data) {
                        values = JSON.parse(data);
                        resolve(values);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }

        function getFields(data) {
        if (data === undefined) {
            console.error("Category is undefined");
            return;
        }

        // First ensure we have values
        getValues()
            .then(valueData => {
                values = valueData; // Store values globally
                
                // Now proceed with getting fields
                const url = 'http://localhost:8080/template/' + data + '/fields';
                
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(data) {
                        let res = JSON.parse(data);
                        // console.log(res);
                        $("#button").remove();
                        let i = 0;
                        
                        if(res === undefined){
                            return;
                        }

                        res.forEach(item => {
                            let lastValue;
                            if (item.split(':').length > 2) {
                                lastValue = item.split(':')[2];
                                console.log("Values: " + (lastValue in values));
                            }
                            
                            const [label, type] = item.split(':');
                            const cleanType = type.replace(';', '');
                    
                            // Check if this field should be a select dropdown
                            if (values && values[lastValue]) {
                                console.log("should be select");
                                // Create select element for fields with predefined values
                                let selectHtml = `
                                    <label class="flex flex-col">
                                        ${label.charAt(0).toUpperCase() + label.slice(1)}:
                                        <select id="${label}" name="${label}" class="rounded-md bg-gray-200 text-center">
                                `;
                                

                                console.log("values label: " + values[lastValue]);
                                // Add options from values
                                for (const option of values[lastValue]) {
                                    const selected = templates[i] === option ? 'selected' : '';
                                    selectHtml += `<option value="${option}" ${selected}>${option}</option>`;
                                }
                                
                                selectHtml += `
                                        </select>
                                    </label>
                                `;
                                $('#form').append(selectHtml);
                            } else {
                                // Regular input field
                                $('#form').append(`
                                    <label class="flex flex-col">
                                        ${label.charAt(0).toUpperCase() + label.slice(1)}:
                                        <input type="${cleanType}" 
                                            id="${label}" 
                                            name="${label}" 
                                            value="${templates[i] || ''}" 
                                            class="rounded-md bg-gray-200 text-center">
                                    </label>
                                `);
                            }
                            i++;
                        });
                        
                        $('#form').append(`
                            <button id="button" 
                                    type="submit" 
                                    class="bg-emerald-300 rounded-md px-2">Create</button>
                        `);
                    },
                    error: function(error) {
                        console.error("Error fetching fields:", error);
                    }
                });
            })
            .catch(error => {
                console.error("Error fetching values:", error);
            });
        }

        async function init() {
            const test = $("#product-id").val();
            templates = getFilledTemplates(test); // Assuming this function exists
            try {
                await getFields(test);
            } catch (error) {
                console.error("Error initializing fields:", error);
            }
        }

        $(document).ready(function() {
            // getFields($("#category").val());
            init();
        });

        if($("#category").val()) {
            $("#post-categories").val($("#category").val());
        }

        let categories = [];
        function addCategory() {
            if (!categories.includes($("#category").val())) {
                categories.push($("#category").val());
                $("#post-categories").val(categories.join("|"));
            }else {
                categories.splice(categories.indexOf($("#category").val()), 1);
                $("#post-categories").val(categories.join("|"));
            }
            console.log($("#post-categories").val());
        }

       </script>
</body>
</html>
