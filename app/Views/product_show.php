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
<body class="flex flex-col items-center h-screen bg-gray-800">
    <?php include('navbar.php'); ?>

    <?php if($products == null) : ?>
        <div class="flex flex-col">
            <p class="text-red-500 text-center text-3xl">No products have been created</p>
            <a href="javascript:history.go(-1)" class="bg-emerald-300 rounded-md p-2 text-center w-1/3 mx-auto mt-4">Go Back</a>
        </div>
    <?php endif; ?>

    <script>
    function order() {
        let url = document.location.href;
        // if order exists replace it if no order then append
        if (url.match(/\?/)) {
            // If a get parameter exists
            // if order exists and is not 1st get param
            if (url.match(/&order/)) {
                let filtered = url.split("&order")[0]+"&order="+encodeURI($("#order").val());
                document.location = filtered;
            }
            // if 1st get param replace
            if (!url.match(/&order/)) {
                let part = url.split("?")[1]+"&order="+encodeURI($("#order").val());
                let filtered = url.split("?")[0]+"?"+part;
                document.location = filtered;
            }
        }else{
            url = url.split("?")[0];
            // If no get parameter
            let filtered = url+"?order="+encodeURI($("#order").val());
            document.location = filtered;
        }
    }

    function filter(minVal, maxVal) {
        // if a filter exists replace it if no filter then append
        let url = document.location.href;
        if (url.match(/\?/)) {
            if (url.match(/&filter/)) {
                let filtered = url.split("&filter")[0]+"&filter="+encodeURI(document.getElementById("filter").value)+"&criteriaMin="+encodeURI($(minVal).val())+"&criteriaMax="+encodeURI($(maxVal).val());
                document.location = filtered;
            }tagSearch
            if (!url.match(/&filter/)) {
                //error here
                let part = url.split("?")[1]+"&filter="+encodeURI(document.getElementById("filter").value)+"&criteriaMin="+encodeURI($(minVal).val())+"&criteriaMax="+encodeURI($(maxVal).val());
                let filtered = url.split("?")[0]+"?"+part;
                document.location = filtered;
            }
        }else{
            //error here
            url = url.split("?")[0];
            let filtered = url+"?filter="+encodeURI(document.getElementById("filter").value)+"&criteriaMin="+encodeURI($(minVal).val())+"&criteriaMax="+encodeURI($(maxVal).val());
            document.location = filtered;
        }
    }

    let tags = [];

    function searchTag(input) {
        tags.push(input.value);
        //console.log("searching tags:", input.value);
        $("#searched-tags").val(tags.join("|"));
        console.log($("#searched-tags").val());
    }

    function tagsSearch() {
        // console.log("searching tags:", $("#tags-search").val());
        let url = document.location.href;
        // if order exists replace it if no order then append
        if (url.match(/\?/)) {
            // If a get parameter exists
            // if order exists and is not 1st get param
            if (url.match(/&tags/)) {
                let filtered = url.split("&tags")[0]+"&tags="+encodeURI(tags.join("|"));
                document.location = filtered;
            }
            // if 1st get param replace
            if (!url.match(/&tags/)) {
                let part = url.split("?")[1]+"&tags="+encodeURI(tags.join("|"));
                let filtered = url.split("?")[0]+"?"+part;
                document.location = filtered;
            }
        }else{
            url = url.split("?")[0];
            // If no get parameter
            let filtered = url+"?tags="+encodeURI(tags.join("|"));
            document.location = filtered;
        }
    }
    </script>

    <label class="flex flex-col text-white">
    Order By:
    <select name="order" id="order" class="rounded-md p-2 m-2 text-black" onchange="order()">
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

    <label class="flex flex-col text-white">
    Filter:
    <select name="filter" id="filter" class="rounded-md p-2 m-2 text-black" oninput="showType(this)">
        <option value="">Filter By:</option>
        <option value="id">Id</option>
        <option value="name">Name</option>
        <option value="manufacturer">Manufacturer</option>
        <option value="size">Size</option>
        <option value="weight">Weight</option>
        <option value="created_at">Created At</option>
        <option value="updated_at">Updated At</option>
    </select>
    </label>

    <label class="flex flex-col text-white" id="filter-num">
        <div class="flex flex-row gap-2">
            Min:
            <input type="range" min="1" max="100" step="1" value="0" id="criteria-min" oninput="changeVal('#criteria-min-val', this)">
            <p id="criteria-min-val" class="text-white">0</p>
        </div>
        <div class="flex flex-row gap-2">
            Max:
            <input type="range" min="0" max="100" step="1" value="10" id="criteria-max" oninput="changeVal('#criteria-max-val', this)">
            <p id="criteria-max-val" class="text-white">10</p>
        </div>
        <button onclick="filter('#criteria-min', '#criteria-max')" class="bg-emerald-300 rounded-md p-2">Filter</button>
    </label>

    <label class="flex flex-col text-white" id="filter-num-detailed">
        <div class="flex flex-row gap-2">
            Min:
            <input type="range" min="0" max="1000" step="0.01" value="0.1" id="criteria-min" oninput="changeVal('#criteria-min-val', this)">
            <p id="criteria-min-val" class="text-white">0</p>
        </div>
        <div class="flex flex-row gap-2">
            Max:
            <input type="range" min="0" max="10000" step="0.01" value="1000" id="criteria-max" oninput="changeVal('#criteria-max-val', this)">
            <p id="criteria-max-val" class="text-white">10</p>
            </div>
            <button onclick="filter('#criteria-min-val', '#criteria-max-val')" class="bg-emerald-300 rounded-md p-2">Filter</button>
    </label>
 
    <label class="flex flex-col" id="filter-str-manufacturer">
        <select id="criteria-str-val-manufcturer" class="rounded-md p-2 m-2">
        <?php if ($manufacturers != null || $manufacturers != "") : ?>
            <?php foreach ($manufacturers as $manufacturer) : ?>
                <option value="<?= $manufacturer->name ?>"><?= $manufacturer->name ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
        </select>
        <button onclick="filter('#criteria-str-val-manufcturer', '#criteria-str-val-manufcturer')" class="bg-emerald-300 rounded-md p-2">Filter</button>
    </label>

    <label class="flex flex-col" id="filter-str-name">
        <select id="criteria-str-val-name" class="rounded-md p-2 m-2">
        <?php if ($names != null || $names != "") : ?>
            <?php foreach ($names as $name) : ?>
                <option value="<?= $name->name ?>"><?= $name->name ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
        </select>
        <button onclick="filter('#criteria-str-val-name', '#criteria-str-val-name')" class="bg-emerald-300 rounded-md p-2">Filter</button>
    </label>

    <label class="flex flex-col text-white" id="filter-date">
        <input type="datetime" name="criteria" class="rounded-md">
        <button onclick="filter('#filter-date', '#filter-date')" class="bg-emerald-300 rounded-md p-2">Filter</button>
    </label>

    <label class="flex flex-col text-white gap-2">
        Search:
        <input type="text" class="rounded-md p-2 text-black" placeholder="Search tags..." onchange="searchTag(this)">
        <input type="hidden" name="tags" id="tags-search" value="">
        <button onclick="tagsSearch()" class="bg-emerald-300 rounded-md p-2">Search</button>
    </label>

    <script onload>
    $('#filter-num').hide("drop", { direction: "down" }, "slow");
    $('#filter-str-name').hide("drop", { direction: "down" }, "slow");
    $("#filter-str-manufacturer").hide("drop", { direction: "down" }, "slow");
    $('#filter-num-detailed').hide("drop", { direction: "down" }, "slow");
    $('#filter-date').hide("drop", { direction: "down" }, "slow");
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
    </script>

    <div class="flex flex-row flex-wrap gap-4 w-full box-border p-4">
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
</body>
</html>
