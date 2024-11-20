<?php

namespace App\Controllers;

class Product extends BaseController
{
    protected $helpers = ['form'];

    public function index()
    {
        $order = $this->request->getGet('order') ?? "id";
        $filter = $this->request->getGet('filter') ?? "id >";
        $criteriaMin = $this->request->getGet('criteriaMin') ?? "0";
        $criteriaMax = $this->request->getGet('criteriaMax') ?? "0";
        $tags = $this->request->getGet('tags') ?? "";
        $categories = $this->request->getGet('category') ?? "";

        switch ($filter) {
            case "id":
                $filter = "id BETWEEN ".$criteriaMin." AND ".$criteriaMax;
                break;
            case "weight":
                $filter = "metadata->>'weight' BETWEEN '".$criteriaMin."' AND '".$criteriaMax."'";
                break;
            case "size":
                $filter = "metadata->>'size' BETWEEN '".$criteriaMin."' AND '".$criteriaMax."'";
                break;
            case "name":
                $filter = $filter." = '".$criteriaMin."'";
                break;
            case "manufacturer":
                $filter = "metadata->>'manufacturer' = "."'".$criteriaMin."'";
                break;
            default:
                $filter = "id > 0";
                break;
        }

        if ($tags) {
            $filter = "";
            foreach (explode("|", $tags) as $index => $tag) {
                if ($index === 0) {
                    $filter = "(metadata->>'tags')::jsonb @> '[\"" . $tag . "\"]'";
                } else {
                    $filter .= " AND (metadata->>'tags')::jsonb @> '[\"" . $tag . "\"]'";
                }
            }
        }

        // Add category search
        if ($categories) {
            if ($filter) {
                $filter .= " AND ";
            }
            $filter .= "category_id::text LIKE '%" . $categories . "%'";
        }

        //$filter = `(metadata->>'tags')::jsonb @> '[${filter}]'`;

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

        return view('product_show', $data);
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
