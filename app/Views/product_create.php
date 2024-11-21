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

        // function getFields(data) {
        //     let url = 'http://localhost:8080/template/'+data.value+'/fields'
        //     $.ajax({
        //         url: url,
        //         type: "GET",
        //         success: function(data) {
        //             console.log(JSON.parse(data));
        //             let res = JSON.parse(data);
        //             $("#button").remove();
        //             res.forEach(item => {
        //                 const [label, type] = item.split(':');
        //                 const cleanType = type.replace(';', '');

        //                 $('#form').append(`
        //                     <label class="flex flex-col">${label.charAt(0).toUpperCase() + label.slice(1)}:
        //                         <input type="${cleanType}" id="${label}" name="${label}" class="rounded-md bg-gray-200 text-center">
        //                     </label>
        //                 `);
        //             });
        //             $('#form').append(`<button id="button" type="submit" class="bg-emerald-300 rounded-md px-2">Create</button>`);
        //         }
        //     });
        // }
    </script>

    <?php form_open_multipart('product/create') ?>
    <form id="form" action="<?= base_url('product/create') ?>" method="post" enctype="multipart/form-data" class="flex flex-col gap-4 bg-teal-100 p-4 rounded-md my-auto">
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
            Categories:
            <?php if ($categories) : ?>
                <select id="category" class="rounded-md bg-gray-200 text-center" oninput="addCategory()" onchange="getFields(this)">
                <option value="">None</option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?= $category->id ?>"><?= $category->name ?></option>
                <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <input type="hidden" name="category_id" id="post-categories" value="" class="rounded-md bg-gray-200 text-center">
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
        <button id="button" type="submit" class="bg-emerald-300 rounded-md px-2">Create</button>
    </form>
    <script>
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
                const url = 'http://localhost:8080/template/' + data.value + '/fields';
                
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
                                    const selected = option === lastValue ? 'selected' : '';
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
            const test = $("#category").val();
            // templates = getFilledTemplates(test); // Assuming this function exists
            try {
                await getFields(test);
            } catch (error) {
                console.error("Error initializing fields:", error);
            }
        }

        init();
    </script>
</body>
</html>
