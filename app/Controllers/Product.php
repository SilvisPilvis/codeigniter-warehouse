<?php

namespace App\Controllers;

function updateAvailabilityCounts($productModel, $available, $filterParams, $filter)
{
    if (empty($filter)) {
        return $available;
    }

    $filterKeys = explode('|', $filter);
    $conditions = [];
    $bindings = [];

    // Table columns that are not in metadata
    $tableColumns = ['id', 'name', 'created_at', 'updated_at'];

    // Build filter conditions
    foreach ($filterKeys as $key) {
        if (isset($filterParams[$key])) {
            $value = $filterParams[$key];
            if (empty($value)) {
                continue;
            }

            if (in_array($key, $tableColumns)) {
                // Direct table columns
                $values = explode('|', $value);
                if (count($values) > 1) {
                    $orParts = [];
                    foreach ($values as $val) {
                        $orParts[] = "$key = ?";
                        $bindings[] = $val;
                    }
                    $conditions[] = '(' . implode(' OR ', $orParts) . ')';
                } else {
                    $conditions[] = "$key = ?";
                    $bindings[] = $value;
                }
            } elseif ($key === 'size') {
                // Size handling
                $sizes = explode('|', $value);
                if (count($sizes) === 2) {
                    $conditions[] = "CAST(metadata->>'size' AS FLOAT) BETWEEN ? AND ?";
                    $bindings[] = min($sizes);
                    $bindings[] = max($sizes);
                } else {
                    $conditions[] = "CAST(metadata->>'size' AS FLOAT) = ?";
                    $bindings[] = $sizes[0];
                }
            } else {
                // Metadata fields
                $values = explode('|', $value);
                if (count($values) > 1) {
                    $orParts = [];
                    foreach ($values as $val) {
                        $orParts[] = "metadata->>'$key' = ?";
                        $bindings[] = $val;
                    }
                    $conditions[] = '(' . implode(' OR ', $orParts) . ')';
                } else {
                    $conditions[] = "metadata->>'$key' = ?";
                    $bindings[] = $value;
                }
            }
        }
    }

    $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

    $query = "SELECT 
        unnest(array['id', 'name', 'created_at', 'updated_at'] || 
              array(SELECT DISTINCT jsonb_object_keys(metadata) FROM product)) as column_name,
        COUNT(*) as filtered_count
    FROM product
    $whereClause
    GROUP BY 1";

    try {
        $result = $productModel->query($query, $bindings)->getResult();

        $updatedAvailable = $available;
        foreach ($result as $row) {
            $row = (array)$row;
            $columnName = $row['column_name'];
            $filteredCount = (int)$row['filtered_count'];

            if (isset($updatedAvailable[$columnName])) {
                $updatedAvailable[$columnName] = $filteredCount;
            }
        }

        return $updatedAvailable;

    } catch (Exception $e) {
        log_message('error', 'Query error: ' . $e->getMessage());
        log_message('error', 'Query: ' . $query);
        log_message('error', 'Bindings: ' . print_r($bindings, true));
        return $available;
    }
}

class FilterQueryBuilder
{
    private $conditions = [];
    private $queryParts = [];

    public function buildFilter($params)
    {
        $order = $params['order'] ?? 'id';
        $filters = $this->parseFilters($params);

        foreach ($filters as $filterType => $values) {
            if (empty($values)) {
                continue;
            }

            $subConditions = [];

            switch ($filterType) {
                case 'id':
                    foreach ($values as $range) {
                        list($min, $max) = explode('|', $range . '|' . $range);
                        $subConditions[] = "id BETWEEN {$min} AND {$max}";
                    }
                    break;

                case 'weight':
                case 'size':
                    foreach ($values as $range) {
                        list($min, $max) = explode('|', $range . '|' . $range);
                        $subConditions[] = "metadata->>'$filterType' BETWEEN '$min' AND '$max'";
                    }
                    break;

                case 'name':
                    foreach ($values as $value) {
                        $subConditions[] = "name = '$value'";
                    }
                    break;

                case 'manufacturer':
                    foreach ($values as $value) {
                        $subConditions[] = "metadata->>'manufacturer' = '$value'";
                    }
                    break;

                case 'template':
                    foreach ($values as $value) {
                        $subConditions[] = "metadata->>'template' ILIKE '%$value%'";
                    }
                    break;

                case 'tags':
                    foreach ($values as $tag) {
                        $subConditions[] = "(metadata->>'tags')::jsonb @> '[\"$tag\"]'";
                    }
                    break;

                case 'category':
                    foreach ($values as $category) {
                        $subConditions[] = "category_id::text LIKE '%$category%'";
                    }
                    break;

                case 'date':
                    foreach ($values as $date) {
                        $subConditions[] = "product.created_at::date = '$date'::date";
                    }
                    break;
            }

            if (!empty($subConditions)) {
                // Join same-type conditions with OR
                $this->queryParts[] = '(' . implode(' OR ', $subConditions) . ')';
            }
        }

        // Join different filter types with AND
        return empty($this->queryParts) ? 'id > 0' : implode(' AND ', $this->queryParts);
    }

    public function parseFilters($params)
    {
        $filters = [];

        // Parse each filter type
        if (!empty($params['id'])) {
            $filters['id'] = explode('|', $params['id']);
        }
        if (!empty($params['weight'])) {
            $filters['weight'] = explode('|', $params['weight']);
        }
        if (!empty($params['size'])) {
            $filters['size'] = explode('|', $params['size']);
        }
        if (!empty($params['name'])) {
            $filters['name'] = explode('|', $params['name']);
        }
        if (!empty($params['manufacturer'])) {
            $filters['manufacturer'] = explode('|', $params['manufacturer']);
        }
        if (!empty($params['template'])) {
            $filters['template'] = explode('|', $params['template']);
        }
        if (!empty($params['tags'])) {
            $filters['tags'] = explode('|', $params['tags']);
        }
        if (!empty($params['category'])) {
            $filters['category'] = explode('|', $params['category']);
        }
        if (!empty($params['date'])) {
            $filters['date'] = explode('|', $params['date']);
        }

        return $filters;
    }

    /**
     * Returns all GET parameters as an associative array
     *
     * @return array
     */
    public function getQueryParams(): array
    {
        $queryParams = [];

        // Get all GET parameters
        $params = $_GET;

        // Filter out empty values and create clean array
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $queryParams[$key] = $value;
            }
        }

        return $queryParams;
    }

    /**
     * Counts matches for each parameter in the database
     *
     * @param PDO $pdo Database connection
     * @return array Associative array of parameter counts
     */
    public function getParameterMatchCounts($model): array
    {
        $params = $this->getQueryParams();
        $counts = [];

        foreach ($params as $param => $value) {
            // Skip certain parameters that shouldn't be counted
            if (in_array($param, ['order', 'page', 'limit'])) {
                continue;
            }

            $query = $this->buildCountQuery($param, $value);

            if ($query) {
                // $stmt = $pdo->prepare($query);
                // $stmt->execute();
                $counts = $model->query($query)->getResult();
                // $counts[$param] = (int)$stmt->fetchColumn();
            }
        }

        return $counts;
    }

    /**
     * Builds the appropriate COUNT query based on parameter type
     *
     * @param string $param Parameter name
     * @param string $value Parameter value
     * @return string|null SQL query or null if parameter type is not supported
     */
    private function buildCountQuery(string $param, string $value): ?string
    {
        switch ($param) {
            case 'id':
                list($min, $max) = explode('|', $value . '|' . $value);
                return "SELECT COUNT(*) as ".$value." FROM product WHERE id BETWEEN ".$min." AND ".$max;

            case 'weight':
            case 'size':
                list($min, $max) = explode('|', $value . '|' . $value);
                return "SELECT COUNT(*) ".$value." FROM product WHERE metadata->>'$param' BETWEEN ".$min." AND ".$max;

            case 'name':
                return "SELECT COUNT(*) FROM product WHERE name = '".$value."'";

            case 'manufacturer':
                return "SELECT COUNT(*) FROM product WHERE metadata->>'manufacturer' = '".$value."'";

            case 'template':
                return "SELECT COUNT(*) FROM product WHERE metadata->>'template' ILIKE "."'%".$value."%'";

            case 'tags':
                return "SELECT COUNT(*) FROM product WHERE (metadata->>'tags')::jsonb @> '[\"".$value."\"]'";

            case 'category':
                return "SELECT COUNT(*) FROM product WHERE category_id::text LIKE '%".$value."%'";

            case 'date':
                return "SELECT COUNT(*) FROM product WHERE created_at::date = ".$value."::date";

            default:
                return null;
        }
    }

}

function consolidateArrayAllArrays($inputArray)
{
    $result = [];

    foreach ($inputArray as $item) {
        foreach ($item as $key => $value) {
            if (!isset($result[$key])) {
                $result[$key] = [$value];  // Always start with an array
            } elseif (!in_array($value, $result[$key])) {
                $result[$key][] = $value;
            }
        }
    }

    return $result;
}

class Product extends BaseController
{
    protected $helpers = ['form'];

    public function index()
    {
        $order = $this->request->getGet('order') ?? "id";
        $filter = $this->request->getGet('filter') ?? "id >";
        $criteriaMin = $this->request->getGet('criteriaMin') ?? "";
        $criteriaMax = $this->request->getGet('criteriaMax') ?? "";
        $tags = $this->request->getGet('tags') ?? "";
        $categories = $this->request->getGet('category') ?? "";
        $template = $this->request->getGet('template') ?? "";
        $dateCreated = $this->request->getGet('date') ?? "";
        $weight = $this->request->getGet('weight') ?? "";
        $size = $this->request->getGet('size') ?? "";
        $name = $this->request->getGet('name') ?? "";
        $manufacturer = $this->request->getGet('manufacturer') ?? "";

        $params = [
            'order' => $order,
            'id' => $criteriaMin && $criteriaMax ?
            $criteriaMin . '|' . $criteriaMax : '',
            'filter' => $filter ?? 'id >',
            'weight' => $weight,
            'size' => $size,
            'name' => $name,
            'manufacturer' => $manufacturer,
            'template' => $template,
            'tags' => $tags,
            'category' => $categories,
            'date' => $dateCreated,
            // $criteriaMin,
            // $criteriaMax,
        ];

        $filterBuilder = new FilterQueryBuilder();
        $filter = $filterBuilder->buildFilter($params);

        // switch ($filter) {
        //     case "id":
        //         $filter = "id BETWEEN ".$criteriaMin." AND ".$criteriaMax;
        //         break;
        //     case "weight":
        //         $filter = "metadata->>'weight' BETWEEN '".$criteriaMin."' AND '".$criteriaMax."'";
        //         break;
        //     case "size":
        //         $filter = "metadata->>'size' BETWEEN '".$criteriaMin."' AND '".$criteriaMax."'";
        //         break;
        //     case "name":
        //         $filter = $filter." = '".$criteriaMin."'";
        //         $criteriaMax = "";
        //         $criteriaMin = "";
        //         break;
        //     case "manufacturer":
        //         $filter = "metadata->>'manufacturer' = "."'".$criteriaMin."'";
        //         $criteriaMax = "";
        //         $criteriaMin = "";
        //         break;
        //     case "template":
        //         $filter = "metadata->>'template' ILIKE "."'%".$criteriaMin."%'";
        //         $criteriaMax = "";
        //         $criteriaMin = "";
        //         break;
        //     default:
        //         $filter = "id > 0";
        //         break;
        // }

        // if ($tags) {
        //     $filter = "";
        //     foreach (explode("|", $tags) as $index => $tag) {
        //         if ($index === 0) {
        //             $filter = "(metadata->>'tags')::jsonb @> '[\"" . $tag . "\"]'";
        //         } else {
        //             $filter .= " AND (metadata->>'tags')::jsonb @> '[\"" . $tag . "\"]'";
        //         }
        //     }
        // }

        // // Add category search
        // if ($categories) {
        //     if ($filter) {
        //         $filter .= " AND ";
        //     }
        //     $filter .= "category_id::text LIKE '%" . $categories . "%'";
        // }

        // echo $filter;

        $productModel = new \App\Models\ProductModel();
        $data['products'] = $productModel
        ->select("product.id,
        product.name,
        metadata->>'tags' as tags,
        metadata->>'image' as images,
        metadata->>'manufacturer' as manufacturer,
        metadata->>'size' as size,
        metadata->>'weight' as weight,
        product.created_at,
        product.category_id ")
        ->where($filter, null, false)
        ->orderBy($order, 'ASC')
        ->get()
        ->getResult();

        $data['names'] = $productModel->select("id, name")->get()->getResult();
        $data['manufacturers'] = $productModel->select("product.id, metadata->>'manufacturer' as name")->get()->getResult();
        $data['categories'] = $productModel->query("SELECT category.id, category.name FROM category;")->getResult();

        // --- all tags
        $productTags = $productModel->select("metadata->>'tags' as tags")->get()->getResult();
        $allTags = [];
        foreach ($productTags as $tag) {
            foreach (json_decode($tag->tags) as $test) {
                array_push($allTags, $test);
            }
        }
        $data['tags'] = $allTags;
        // --- end all tags

        // --- get all template fields
        $template = new \App\Models\TemplatefieldsModel();
        $fields = $template->query("SELECT template FROM category_template;")->getResult();

        foreach ($fields as $field) {
            $result[] = array_keys(json_decode($field->template, true));
        }

        $result = array_merge(...$result);
        $data['template'] = array_unique($result);
        // --- end all template fields

        // --- template field types
        $fields = $template->query("SELECT template FROM category_template;")->getResult();

        $padded = [];
        foreach ($fields as $f) {
            $padded[] = array_values(json_decode($f->template, true));
        }

        $padded = array_merge(...$padded);

        // instead sheck if  has more than 1 ":" and if so then leave fieldtype:values else leave fieldtype
        foreach ($padded as $key => $value) {
            if (explode(":", $padded[$key]) > 1) {
                if (explode(':', $padded[$key])[0] == "checkbox" || explode(':', $padded[$key])[0] == "radio" || explode(':', $padded[$key])[0] == "select") {
                    // $padded[$key] = explode(':', $padded[$key])[1];
                    $padded[$key] = $padded[$key];
                }
            } else {
                if (explode(':', $padded[$key])[1] == "checkbox" || explode(':', $padded[$key])[1] == "radio" || explode(':', $padded[$key])[1] == "select") {
                    $padded[$key] = explode(':', $padded[$key])[1];
                }
            }
        }

        $padded = array_unique($padded);
        foreach ($padded as $key => $value) {
            if (count(explode(":", $value)) > 1) {
                $padded[$key] = explode(':', $value)[count(explode(':', $value)) - 1];
            } else {
                $padded[$key] = 0;
            }
        }
        // print_r($padded);
        $data['padded'] = $padded;

        foreach ($fields as $field) {
            $res[] = array_values(json_decode($field->template, true));
        }

        $res = array_merge(...$res);
        // print_r($result);
        $temp = [];
        foreach ($res as $r) {
            $temp[] = explode(':', $r)[0];
        }
        $temp = array_unique($temp);

        $data['template_values'] = $temp;
        // $data['template_values'] = get_object_vars($temp);

        // --- end template field types
        $data['test'] = $template->query("SELECT metadata->>'template' template FROM product WHERE metadata->>'template' IS NOT NULL;")->getResult();
        foreach ($data['test'] as $key => $value) {
            $tmp = (array)$value;
            $data['test'][$key] = json_decode($tmp['template'], true);
        }
        $data['test'] = consolidateArrayAllArrays($data['test']);
        // $data['test'] = array_merge(...$data['test']);
        // --- template field values
        $values = $template->query("SELECT value_sets FROM template_values;")->getResult();
        $data['value_sets'] = json_decode($values[0]->value_sets);
        // --- end template field values
        // padded values if not select or checkbox or radio then pad array with 0

        // --- get all product dates
        $dates = $productModel->query("SELECT
        product.created_at
            FROM product")->getResult();
        $data['dates'] = [];
        foreach ($dates as $key => $value) {
            array_push($data['dates'], $value->created_at);
            // $data['dates'] = $value->created_at;
        }
        // remove duplicates and convert to date format
        $data['dates'] = array_unique(
            array_map(
                function ($date) {
                    return date('Y-m-d', strtotime($date));
                },
                $data['dates']
            )
        );
        // --- end get all product dates

        // --- count all availible fields
        $data['availible'] = $productModel->query("WITH base AS (
    -- Table columns
    SELECT 
        a.attname as column_name,
        (SELECT COUNT(*) FROM product) as record_count
    FROM pg_catalog.pg_attribute a
    WHERE a.attrelid = 'product'::regclass
        AND a.attnum > 0 
        AND NOT a.attisdropped

    UNION

    -- First level JSON keys
    SELECT 
        jsonb_object_keys(metadata) as column_name,
        COUNT(*) as record_count
    FROM product
    GROUP BY jsonb_object_keys(metadata)

    UNION

    -- Nested JSON keys
    SELECT 
        jsonb_object_keys(value::jsonb) as column_name,
        COUNT(*) as record_count
    FROM product, 
    jsonb_each(metadata)
    WHERE jsonb_typeof(value) = 'object'
    GROUP BY jsonb_object_keys(value::jsonb)
)
SELECT 
    column_name,
    record_count
FROM base
            ORDER BY column_name;")->getResult();
        $availible = [];
        foreach ($data['availible'] as $value) {
            $value = (array)$value;
            if (!array_key_exists($value['column_name'], $availible)) {
                $availible[$value['column_name']] = $value['record_count'];
            } else {
                // $availible[$value['column_name']] += $value['record_count'];
            }
        }
        $data['availible'] = $availible;

        $data['sigma'] = updateAvailabilityCounts($productModel, $data['availible'], $params, $params['filter']);

        print_r($data['sigma']);

        // $data['availible'] = $data['sigma'];
        // $data['params'] = $filterBuilder->getParameterMatchCounts($productModel);

        // return view('product_show', $data);
    }

    public function getAllTags()
    {
        $productModel = new \App\Models\ProductModel();
        $tags = $productModel->select("metadata->>'tags' as tags")->get()->getResult();
        $allTags = [];
        foreach ($tags as $tag) {
            foreach (json_decode($tag->tags) as $test) {
                array_push($allTags, $test);
            }
        }
        print_r($allTags);
    }

    public function showCreate()
    {
        $productModel = new \App\Models\ProductModel();
        $data['categories'] = $productModel->query("SELECT category.id, category.name FROM category")->getResult();
        // $data['dynamic_fields'] = $productModel->query("SELECT * FROM category_template WHERE category_id = ?", [$data['categories'][0]->id])->getResult();
        return view('product_create', $data);
    }

    public function showSingle(int $id)
    {
        $productModel = new \App\Models\ProductModel();
        $data['products'] =  [$productModel->query("SELECT product.id, 
        product.name,
        metadata->>'tags' as tags,
        metadata->>'image' as images,
        metadata->>'manufacturer' as manufacturer,
        metadata->>'size' as size,
        metadata->>'weight' as weight,
        product.category_id,
        product.created_at
        FROM product
        WHERE id = ?", [$id])->getRow()];
        $data['manufacturers'] = null;
        //$data['names'] = null;

        // --- all tags
        $productTags = $productModel->select("metadata->>'tags' as tags")->get()->getResult();
        $allTags = [];
        foreach ($productTags as $tag) {
            foreach (json_decode($tag->tags) as $test) {
                array_push($allTags, $test);
            }
        }
        $data['tags'] = $allTags;
        $data['categories'] = $productModel->query("SELECT category.id, category.name FROM category")->getResult();
        $data['names'] = $productModel->select("id, name")->get()->getResult();
        // --- end all tags

        return view('product_show', $data);
    }

    public function showEdit(int $id)
    {
        $productModel = new \App\Models\ProductModel();
        $data['product'] =  $productModel->query("SELECT product.id, 
        product.name,
        metadata->>'image' as images,
        metadata->>'manufacturer' as manufacturer,
        metadata->>'size' as size,
        metadata->>'weight' as weight,
        metadata->>'tags' as tags,
        product.created_at
        FROM product
        WHERE id = ?", [$id])->getRow();
        $data['categories'] = $productModel->query("SELECT category.id, category.name FROM category")->getResult();
        // should be where id = product.category_id
        $data['current_category'] = $productModel->query("SELECT metadata->>'category_id' category_id from product WHERE product.id = ?", [$id])->getRow();
        $data['current_category'] = json_decode($data['current_category']->category_id)[0];
        $data['current_category'] = $productModel->query("SELECT id, name FROM category WHERE id = ?", [$data['current_category']])->getRow();
        $data['dynamic_fields'] = [];
        // we can comment this and just use js
        // $data['dynamic_fields'] = $productModel->query("SELECT * FROM category_template WHERE category_id = ?", [$data['current_category']->id])->getResult();
        return view('product_edit', $data);
    }

    public function saveEdit(int $id)
    {
        $productModel = new \App\Models\ProductModel();
        $product = new \App\Entities\Product();
        $data = [
            'name' => $this->request->getPost('name'),
            'image' => $this->request->getFiles('image'),
            'manufacturer' => $this->request->getPost('manufacturer'),
            'weight' => $this->request->getPost('weight'),
            'size' => $this->request->getPost('size'),
            'tags' => $this->request->getPost('tags'),
            'category_id' => $this->request->getPost('category_id'),
            'test' => $this->request->getPost('test'),
        ];

        // get post data except all predefined inputs and bind it to the category_id
        $productModel->query("INSERT INTO category_template (template, category_id) VALUES(?, ?)", [json_encode(array_slice($_POST, 8, count($_POST) - 8)), $id]);
        // echo json_encode(array_slice($_POST, 8, count($_POST) - 8));
        // save the array to db category_id => array(input_name, value)

        $rule = [
            'name' => 'required|min_length[3]|max_length[255]',
            'image' => [
                'label' => 'Image',
                'rules' => 'max_size[image,1024]|max_dims[image,1024,1024]|mime_in[image,image/jpg,image/jpeg,image/png]',
            ],
            'manufacturer' => 'required|min_length[3]|max_length[255]',
            'weight' => 'required|numeric|greater_than_equal_to[0.01]',
            'size' => 'required|numeric|greater_than_equal_to[1]',
            'tags' => 'required|min_length[1]|max_length[255]',
            'category_id' => 'required|min_length[1]|max_length[255]',
        ];

        // echo print_r($data['category_id']);

        $data['tags'] = json_encode(explode("|", $data['tags']));
        $data['category_id'] = json_encode(explode("|", $data['category_id']));

        if (!$this->validate($rule)) {
            return view('product_edit', [
                'errors' => $this->validator->getErrors(),
                'product' => $productModel->find($id),
            ]);
        }

        $images = $this->request->getFiles();
        $productImages = [];
        foreach ($images['image'] as $image) {
            if ($image->isValid() && !$image->hasMoved()) {
                // upload image and replace all spaces with underscores
                $image->move(WRITEPATH . 'uploads', str_replace(" ", "_", $image->getName()));
                $productImages[] = "uploads/".str_replace(" ", "_", $image->getName());
                // $productImages[] = "uploads/".$image->getName();
            }
        }

        // TEST add existing images from db

        $existingPics = $productModel->query("SELECT
        metadata->>'image' as images
        FROM product
        WHERE id = ?", [$id])->getRow();
        $existingPics = json_decode($existingPics->images, true);

        if (gettype($existingPics) == 'array') {
            if (count($existingPics) > 0) {
                foreach ($existingPics as $pic) {
                    if (substr_count($pic, "uploads/") <= 0) {
                        // break;
                        $productImages[] = "uploads/".$pic;
                    } else {
                        $productImages[] = $pic;
                    }
                }
            }
        }

        $data['image'] = json_encode($productImages);

        $data['metadata'] = json_encode(array_slice($data, 1));
        $product->fill($data);

        // use this query
        // UPDATE product SET metadata = jsonb_set(metadata, '{manufacturer}', '"Sigma"') WHERE id = 1;

        if ($productModel->update($id, $data)) {
            return view('success_message', [
                'message' => 'product ' . $product->name . ' has been updated'
            ]);
        }
    }

    public function deleteImages(int $id)
    {
        $productModel = new \App\Models\ProductModel();

        $existingPics = $productModel->query("SELECT 
        metadata->>'image' as images
        FROM product
        WHERE id = ?", [$id])->getRow();
        $existingPics = json_decode($existingPics->images, true);

        $errors = [];

        if (count($existingPics) > 0) {
            foreach ($existingPics as $pic) {
                if (!unlink(WRITEPATH.$pic)) {
                    return view('product_edit', $errors);
                }
            }
        }

        $query = $productModel->query("UPDATE product SET metadata = jsonb_set(metadata, '{image}', '".json_encode([])."') WHERE id = ?", [$id]);

        $data['product'] =  $productModel->query("SELECT product.id, 
        product.name,
        metadata->>'image' as images,
        metadata->>'manufacturer' as manufacturer,
        metadata->>'size' as size,
        metadata->>'weight' as weight,
        product.created_at
        FROM product
        WHERE id = ?", [$id])->getRow();

        if ($query) {
            return view('product_edit', $data);
        }

    }

    public function deleteSingleImage(int $id, string $name)
    {
        $productModel = new \App\Models\ProductModel();

        $existingPics = $productModel->query("SELECT 
        metadata->>'image' as images
        FROM product
        WHERE id = ?", [$id])->getRow();
        $existingPics = json_decode($existingPics->images, true);

        for ($x = 0; $x < count($existingPics); $x++) {
            if ($existingPics[$x] == "uploads/".$name) {
                unset($existingPics[$x]);
                if (!unlink(WRITEPATH ."uploads/".$name)) {
                    $errors = ["Failed to delete image(s)."];
                    return view('product_edit', $errors);
                }
                break;
            }
        }

        $query = $productModel->query("UPDATE product SET metadata = jsonb_set(metadata, '{image}', '".json_encode($existingPics)."') WHERE id = ?", [$id]);

        $data['product'] =  $productModel->query("SELECT product.id,
        product.name,
        metadata->>'tags' as tags,
        metadata->>'image' as images,
        metadata->>'manufacturer' as manufacturer,
        metadata->>'size' as size,
        metadata->>'weight' as weight,
        product.created_at
        FROM product
        WHERE id = ?", [$id])->getRow();

        if ($query) {
            return view('product_edit', $data);
        }
    }


    public function delete(int $id)
    {
        $productModel = new \App\Models\ProductModel();
        $name = $productModel->find($id)['name'];
        if ($productModel->delete($id)) {
            return view('success_message', [
                'message' => 'product ' . $name . ' has been deleted'
            ]);
        }
    }


    public function create()
    {
        $productModel = new \App\Models\ProductModel();
        $product = new \App\Entities\Product();

        $data = [
            'name' => $this->request->getPost('name'),
            'image' => $this->request->getFiles('image'),
            'manufacturer' => $this->request->getPost('manufacturer'),
            'weight' => $this->request->getPost('weight'),
            'size' => $this->request->getPost('size'),
            'tags' => $this->request->getPost('tags'),
            'category_id' => $this->request->getPost('category_id'),

        ];

        $rule = [
            'name' => 'required|min_length[3]|max_length[255]',
            'image' => [
                'label' => 'Image',
                'rules' => 'uploaded[image]|max_size[image,1024]|max_dims[image,1024,1024]|mime_in[image,image/jpg,image/jpeg,image/png]',
            ],
            'manufacturer' => 'required|min_length[3]|max_length[255]',
            'weight' => 'required|numeric|greater_than_equal_to[0.01]',
            'size' => 'required|numeric|greater_than_equal_to[1]',
            'tags' => 'required|min_length[1]|max_length[255]',
            'category_id' => 'required|greater_than_equal_to[1]',
        ];

        $data['template'] = array_slice($_POST, 6, count($_POST) - 6);

        if (!$this->validate($rule)) {
            $data['categories'] = $productModel->query("SELECT category.id, category.name FROM category")->getResult();
            return view('product_create', [
                'errors' => $this->validator->getErrors(),
                'categories' => $data['categories']
            ]);
        }

        $data['tags'] = json_encode(explode("|", $data['tags']));
        $data['category_id'] = json_encode(explode("|", $data['category_id']));

        $images = $this->request->getFiles();
        $productImages = [];
        foreach ($images['image'] as $image) {
            if ($image->isValid() && !$image->hasMoved()) {
                // upload image and replace all spaces with underscores
                $image->move(WRITEPATH . 'uploads', str_replace(" ", "_", $image->getName()));
                $productImages[] = "uploads/".str_replace(" ", "_", $image->getName());
            }
        }
        $data['image'] = json_encode($productImages);

        $data['metadata'] = json_encode(array_slice($data, 1));
        // echo json_encode(array_slice($data, 1));
        $product->fill($data);

        if ($productModel->save($product)) {
            return view('success_message', [
                'message' => $product->name . ' has been created'
            ]);
        }
    }

    public function getProductDate()
    {
        $productModel = new \App\Models\ProductModel();
        $data['products'] = $productModel->query("SELECT
        product.created_at
            FROM product")->getResult();
        $res = [];
        foreach ($data['products'] as $key => $value) {
            $res[] = $value->created_at;
        }
        print_r($res);
    }

    public function getTemplateValues(int $id)
    {
        $productModel = new \App\Models\ProductModel();
        $values = $productModel->query("SELECT metadata->>'template' template FROM product WHERE product.id = ?", [$id])->getResult();
        if (!$values) {
            print_r(['errors' => 'No template']);
        } else {
            $res = [];
            foreach (json_decode($values[0]->template) as $key => $value) {
                $res[] = $value;
            }
            print_r(json_encode($res));
        }

    }

    public function testSuccess()
    {
        return view('success_message', [
            'message' => 'This is the success message'
        ]);
    }

    public function testError()
    {
        return view('error_message', [
            'errors' => 'This is the error message'
        ]);
    }

    public function testTemplate()
    {
        $productModel = new \App\Models\ProductModel();
        $values["colors"] = ["red", "blue", "green", "yellow", "orange", "purple", "black", "white", "gray", "brown", "cornflowerblue"];
        $values["colors_hex"] = ["#ff0000", "#0000ff", "#00ff00", "#ffff00", "#ff8000", "#800080", "#000000", "#ffffff", "#808080", "#a52a2a", "#6495ed"];
        $values['materials'] = ["wood", "metal", "plastic"];
        $values["sizes"] = ["small", "medium", "large"];
        $values["gender"] = ["male", "female"];
        $values["months"] = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        $productModel->query("INSERT INTO template_values (value_sets, updated_at) VALUES(?, NOW())", [json_encode($values)]);
        return view('success_message', [
            'message' => 'This is the success message'
        ]);
    }
}
