<?php
function string2array($string)
{
    return json_decode($string, true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show products</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js" integrity="sha256-AlTido85uXPlSyyaZNsjJXeCs07eSv3r43kyCVc8ChI=" crossorigin="anonymous"></script>
</head>
<style>
#hidden {
    display: none;
}
</style>
<body class="flex flex-col h-screen bg-gray-800">
    <?php include('navbar.php'); ?>

    <?php if($products == null) : ?>
        <div class="flex flex-col">
            <p class="text-red-500 text-center text-3xl">No products have been created</p>
            <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-2 text-center w-1/3 mx-auto mt-4">Go Back</a>
        </div>
    <?php endif; ?>

    <script>
    function updateURLParameters(params) {
        const url = new URL(window.location.href);
        Object.keys(params).forEach(key => {
            if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
                url.searchParams.set(key, params[key]);
            }
        });
        window.history.replaceState({}, '', url);
        // Optionally reload the page or fetch new data
        window.location.href = url;
    }

    // Order handling
    function order() {
        const orderValue = document.querySelector('#order').value;
        updateURLParameters({ order: orderValue });
    }

    // Tags search handling
    let tagsList = [];

    function addTagToList(tag) {
        if (!tagsList.includes(tag)) {
            tagsList.push(tag);
            document.querySelector('#searched-tags').value = tagsList.join(',');
            // Update the search input to show current tags
            document.querySelector('input[list="tags-search"]').value = tagsList.join(', ');
        }
    }

    function searchTag(input) {
        const tag = input.value.trim();
        if (tag && !tagsList.includes(tag)) {
            addTagToList(tag);
        }
        input.value = ''; // Clear the input after adding
    }

    function tagsSearch() {
        const tags = document.querySelector('#searched-tags').value;
        updateURLParameters({ tags: tags });
    }

    // Category search handling
    function searchCategory() {
        const category = document.querySelector('#category').value;
        updateURLParameters({ category: category });
    }

    function toggleSidebar(){
        $("#sidebar").toggleClass("hidden");
        $("#sidebar-button").toggleClass("ml-[-0.7rem]");
    }

    function clearFilter() {
        document.location = document.location.href.split("?")[0];
    }

    // Initialize values from URL parameters on page load
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Initialize order
        if (urlParams.has('order')) {
            document.querySelector('#order').value = urlParams.get('order');
        }
        
        // Initialize range values
        if (urlParams.has('criteriaMin')) {
            $('#min-value').val(urlParams.get('criteriaMin'));
            $('#min-range').val(urlParams.get('criteriaMin'));
        }
        
        if (urlParams.has('criteriaMax')) {
            $('#max-value').val(urlParams.get('criteriaMax'));
            $('#max-range').val(urlParams.get('criteriaMax'));
        }
        
        // Initialize tags
        if (urlParams.has('tags')) {
            const tags = urlParams.get('tags').split(',');
            tagsList = tags;
            document.querySelector('#searched-tags').value = tags.join(',');
            document.querySelector('input[list="tags-search"]').value = tags.join(', ');
        }
        
        // Initialize category
        if (urlParams.has('category')) {
            document.querySelector('#category').value = urlParams.get('category');
        }

        // Range slider functionality
        const updateRangeBar = () => {
            const min = parseInt($('#min-range').val());
            const max = parseInt($('#max-range').val());
            $('#range-bar').css({
                'left': min + '%',
                'right': (100 - max) + '%'
            });
        }
        
        // Update range bar
        updateRangeBar();
    });

    // Filter handling for ranges
    function filter(minSelector, maxSelector, key) {
        const filterType = key.name || 'id';
        // console.log("Filter key name: " + key.name);
        // console.log("Element: " + key);
        // const filterType = key.name || 'id';
        const minValue = $(minSelector).val();
        const maxValue = $(maxSelector).val();
        
        let filterString = `${filterType}`; // Default filter string
        updateURLParameters({
            filter: filterType,
            criteriaMin: filterString,
        });
        updateURLParameters({
            filter: filterType,
            criteriaMin: minValue,
            criteriaMax: maxValue
        });
    }

    function templateMatch(data) {
        updateURLParameters({
            filter: "template",
            criteriaMin: data.value
        });
    }
    </script>

    <main class="flex flex-row flex-wrap w-full box-border gap-4">
        <aside class="flex flex-col w-56 bg-gray-600 rounded-r-md min-h-screen" id="sidebar">
            <label class="flex flex-col text-black">
                Order By:
                <select name="order" id="order" class="rounded-md p-2 m-2 text-black" onchange="order()">
                    <?php if (array_key_exists("order", $_GET)) : ?>
                        <option value=""><?= $_GET["order"] ?></option>
                    <?php endif; ?>
                    <option value="">Order By:</option>
                    <option value="id">Id</option>
                    <option value="name">Name</option>
                    <option value="manufacturer">Manufacturer</option>
                    <option value="size">Size</option>
                    <option value="weight">Weight</option>
                    <option value="created_at">Created At</option>
                    <option value="updated_at">Updated At</option>
                </select>
            </label>

            <details>
            <summary>Filter Id:</summary>
                 <label class="flex flex-col text-black w-full" id="filter-num">
                    <!-- <button onclick="filter('#criteria-min', '#criteria-max')" class="bg-emerald-300 rounded-md p-2 m-2">Filter</button> -->
                    <?php include_once "doubleslider.php"; ?>
                 </label>
            </details>
            
            <details>
            <summary>Filter Size:</summary>
            <label class="flex flex-col text-black w-full" id="filter-num-detailed">
                <?php include_once "detailed-slider.php"; ?>
            </label>
            </details>
            

            <details>
            <summary>Filter Manufacturer:</summary>
            <label class="flex flex-col" id="filter-str-manufacturer">
                <select id="criteria-str-val-manufcturer" class="rounded-md p-2 m-2" onchange="filter('#criteria-str-val-name', '#criteria-str-val-name', this)" name="manufacturer">
                    <?php if ($manufacturers != null || $manufacturers != "") : ?>
                        <?php foreach ($manufacturers as $manufacturer) : ?>
                            <?php if (array_key_exists("filter", $_GET)) : ?>
                                <option value=""><?= $_GET["filter"] ?></option>
                            <?php endif; ?>
                            <option value="<?= $manufacturer->name ?>"><?= $manufacturer->name ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <!-- <button onclick="filter('#criteria-str-val-manufcturer', '#criteria-str-val-manufcturer')" class="bg-emerald-300 rounded-md p-2 m-2">Filter</button> -->
            </label>
            </details>

            <details>
            <summary>Filter Name:</summary>
            <label class="flex flex-col" id="filter-str-name">
                <select id="criteria-str-val-name" class="rounded-md p-2 m-2" onchange="filter('#criteria-str-val-name', '#criteria-str-val-name', this)" name="name">
                    <?php if ($names != null || $names != "") : ?>
                        <?php foreach ($names as $name) : ?>
                            <?php if (array_key_exists("filter", $_GET)) : ?>
                                <option value=""><?= $_GET["filter"] ?></option>
                            <?php endif; ?>
                            <option value="<?= $name->name ?>"><?= $name->name ?></option>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <option value="">No product names found</option>
                    <?php endif; ?>
                </select>
                <!-- <button onclick="filter('#criteria-str-val-name', '#criteria-str-val-name')" class="bg-emerald-300 rounded-md p-2 m-2">Filter</button> -->
            </label>
            </details>

            <details>
            <summary>Filter Date:</summary>
            <label class="flex flex-col text-black w-full" id="filter-date">
                Filter date:
                <input type="date" name="criteria" class="rounded-md p-2 m-2 text-black">
                <!-- <button onclick="filter('#filter-date', '#filter-date')" class="bg-emerald-300 rounded-md p-2 m-2">Filter</button> -->
            </label>
            </details>

            <p>Filter template:</p>

            <?php foreach ($template as $key => $template) : ?>
                <details>
                <summary><?= $template ?></summary>
                <label class="flex flex-col text-black w-full">
                        <input type="<?= $template_values[$key] ?>" onchange="templateMatch(this)" name="template" class="rounded-md p-2 m-2 text-black">
                </label>
                </details>
            <?php endforeach; ?>

            <button onclick="filter('#criteria-min', '#criteria-max')" class="bg-emerald-300 rounded-md p-2 m-2">Filter</button>

            <div class="flex flex-row flex-wrap gap-2 m-2" id="all-tags">
                <?php foreach ($tags as $tag) : ?>
                    <p class="text-center text-white bg-indigo-900 px-2 rounded-md" onclick="addTagToList(this.innerText)"><?= $tag ?></p>
                <?php endforeach; ?>
            </div>

            <label class="flex flex-col text-white gap-2">
                Search:
                <input type="text" list="tags-search" class="rounded-md p-2 m-2 text-black" placeholder="Search products..." onchange="searchTag(this)" value="<?= $_GET["tags"] ?? "" ?>">
                <input type="hidden" id="searched-tags" name="tags" value="">
                <datalist id="tags-search">
                    <?php foreach ($tags as $tag) : ?>
                        <option value="<?= $tag ?>"><?= $tag ?></option>
                    <?php endforeach; ?>
                </datalist>
                <button onclick="tagsSearch()" class="bg-emerald-300 rounded-md p-2 m-2">Search</button>
            </label>

            <label class="flex flex-col text-white gap-2">
                Category:
                <?php if ($categories) : ?>
                    <select id="category" class="rounded-md p-2 m-2 text-black" oninput="searchCategory()">
                        <?php $test = array_column($categories, 'name', 'id'); ?>
                        <?php if($_GET && array_key_exists("category", $_GET)) : ?>
                            <option value=""><?= $test[$_GET["category"]] ?></option>
                            <?php else : ?>
                            <option value="">Choose a category</option>
                        <?php endif; ?>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?= $category->id ?>"><?= $category->name ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </label>

            <button class="bg-emerald-300 rounded-md p-2 m-2" onclick="clearFilter()">Clear</button>

        </aside>

        <button id="sidebar-button" class="text-center flex justify-center items-center ml-[-0.7rem] mt-[0.3rem] h-4 w-4 p-4 bg-emerald-300 rounded-md" onclick="toggleSidebar()">x</button>

        <script onload>
        // Add event listeners for range inputs to automatically update URL
        $('#min-range, #max-range').on('change', function() {
            console.log("Range changed");
            filter('#min-value', '#max-value');
        });

        $('#min-value, #max-value').on('change', function() {
            console.log("Range changed");
            filter('#min-value', '#max-value');
        });

        // $("#criteria-str-val-manufcturer").on("change", function() {
        //     filter('#criteria-str-val-manufcturer', '#criteria-str-val-manufcturer');
        // })

        // $('#filter-num').hide("drop", { direction: "down" }, "slow");
        // $('#filter-str-name').hide("drop", { direction: "down" }, "slow");
        // $("#filter-str-manufacturer").hide("drop", { direction: "down" }, "slow");
        // $('#filter-num-detailed').hide("drop", { direction: "down" }, "slow");
        // $('#filter-date').hide("drop", { direction: "down" }, "slow");

        // // if tag text is in tags array change color to bg-emerald-300
        // $("#all-tags").children().each(function() {
        //     // console.log("includes: "+ tags.includes($(this).text()));
        //     if(tags.includes($(this).text())) {
        //         $(this).removeClass("bg-indigo-900");
        //         $(this).addClass("bg-lime-800");
        //     }
        // });
        </script>

        <script>
        function showType(filterId) {
            switch($(filterId).val()) {
                case "id" || "size":
                    $('#filter-num').show();
                    $('#filter-str-name').hide("drop", { direction: "down" }, "slow");
                    $("#filter-num-detailed").hide("drop", { direction: "down" }, "slow");
                    $("#filter-date").hide("drop", { direction: "down" }, "slow");
                    $("#filter-str-manufacturer").hide("drop", { direction: "down" }, "slow");
                    break;
                case "name":
                    $('#filter-str-name').show();
                    $('#filter-num').hide("drop", { direction: "down" }, "slow");
                    $("#filter-num-detailed").hide("drop", { direction: "down" }, "slow");
                    $("#filter-date").hide("drop", { direction: "down" }, "slow");
                    $("#filter-str-manufacturer").hide("drop", { direction: "down" }, "slow");
                    break;
                case "manufacturer":
                    $("#filter-str-manufacturer").show();
                    $("#filter-num").hide("drop", { direction: "down" }, "slow");
                    $("#filter-str-name").hide("drop", { direction: "down" }, "slow");
                    $("#filter-num-detailed").hide("drop", { direction: "down" }, "slow");
                    $("#filter-date").hide("drop", { direction: "down" }, "slow");
                    break;
                case "weight":
                    $('#filter-num-detailed').show();
                    $("#filter-num").hide("drop", { direction: "down" }, "slow");
                    $("#filter-str-name").hide("drop", { direction: "down" }, "slow");
                    $("#filter-date").hide("drop", { direction: "down" }, "slow");
                    $("#filter-str-manufacturer").hide("drop", { direction: "down" }, "slow");
                    break;
                case "created_at" || "updated_at":
                    $('#filter-date').show();
                    $("#filter-num").hide("drop", { direction: "down" }, "slow");
                    $("#filter-str-name").hide("drop", { direction: "down" }, "slow");
                    $("#filter-num-detailed").hide("drop", { direction: "down" }, "slow");
                    $("#filter-str-manufacturer").hide("drop", { direction: "down" }, "slow");
                    break;
                default:
                    $('#filter-num').show();
                    $("#filter-num-detailed").hide("drop", { direction: "down" }, "slow");
                    $("#filter-str-name").hide("drop", { direction: "down" }, "slow");
                    $("#filter-date").hide("drop", { direction: "down" }, "slow");
                    $("#filter-str-manufacturer").hide("drop", { direction: "down" }, "slow");
                    break;
            }
        }

        function changeVal(val, e) {
            $(val).text($(e).val());
        }

        // function searchCategory() {
        //     let category = $('#category').val();
        //     let url = document.location.href;
        //     if (url.match(/\?/)) {
        //         if (url.match(/&category/)) {
        //             let filtered = url.split("&category")[0]+"&category="+category;
        //             document.location = filtered;
        //         }
        //         if (!url.match(/&category/)) {
        //             let part = url.split("?")[1]+"&category="+category;
        //             let filtered = url.split("?")[0]+"?"+part;
        //             document.location = filtered;
        //         }
        //     }else{
        //         url = url.split("?")[0];
        //         let filtered = url+"?category="+category;
        //         document.location = filtered;
        //     }
        // }
        </script>

        <div class="flex flex-row flex-wrap gap-4 box-border w-[83%]">
            <?php foreach ($products as $product) : ?>
                <a href="<?= base_url('product/' . $product->id) ?>" class="black decoration-none rounded-md bg-teal-100 p-4 min-h-[10rem] max-h-max">
                    <div class="flex flex-col items-center gap-2">
                        <p>Id: <?= $product->id ?></p>
                        <h1>Name: <?= $product->name ?></h1>
                        <div class="flex flex-row flex-wrap gap-4">
                            <?php foreach (json_decode($product->images, true) as $image) : ?>
                                <img src="<?= base_url($image) ?>" alt="" class="min-w-26 min-h-26 max-w-52 max-h-52 rounded-md object-cover mx-auto m-2">
                            <?php endforeach; ?>
                        </div>
                        <!-- tags -->
                        <div class="flex flex-row gap-4">
                            <?php if ($product->tags) : ?>
                                <?php foreach(json_decode($product->tags) as $tag) : ?>
                                <p class="text-center text-white bg-indigo-900 px-2 rounded-md"><?= $tag ?></p>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <p class="text-center text-gray-400">No Tags</p>
                            <?php endif; ?>
                        </div>
                        <!-- tags -->
                        <!-- categories -->
                        Categories:
                        <div class="flex flex-row gap-4">
                            <?php if ($product->category_id) : ?>
                                <?php foreach(json_decode($product->category_id) as $category) : ?>
                                    <?php $test = array_column($categories, 'name', 'id'); ?>
                                <p class="text-center text-white bg-indigo-900 px-2 rounded-md"><?php echo $test[$category] ?></p>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <p class="text-center text-gray-400">No Categories</p>
                            <?php endif; ?>
                        </div>
                        <!-- categories end -->
                        <label class="flex flex-col">
                            Manufacturer:
                            <input type="text" name="date" readonly value="<?= $product->manufacturer ?>" class="rounded-md w-min text-center">
                        </label>
                        <label class="flex flex-col">
                            Volume in Cm³:
                            <input type="text" min="0.01" max="10000" step="0.01" name="size" readonly value="<?= $product->size ?>" class="rounded-md w-min text-center">
                        </label>
                        <label class="flex flex-col">
                            Weight in Kg:
                            <input type="text" name="date" readonly value="<?= $product->weight ?>" class="rounded-md w-min text-center">
                        </label>
                        <label class="flex flex-col">
                            Created at:
                            <input type="datetime" name="date" readonly value="<?= $product->created_at ?>" class="rounded-md w-min text-center">
                        </label>
                        <div class="flex flex-row gap-4 mt-4 mb-4 justify-center">
                            <form action="<?= base_url('product/delete/' . $product->id) ?>" method="post">
                                <button class="bg-red-300 rounded-md px-2">Delete</button>
                            </form>
                            <form action="<?= base_url('product/edit/' . $product->id) ?>" method="get">
                                <button class="bg-emerald-300 rounded-md px-2">Edit</button>
                            </form>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
            <a href="<?= base_url('product/create') ?>" class="bg-orange-300 rounded-md h-full flex justify-center items-center p-4 h-max">Create A Product</a>
        </div>
    </main>
</body>
</html>
