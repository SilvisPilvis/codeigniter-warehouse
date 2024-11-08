<?php

namespace App\Controllers;

class Product extends BaseController
{
    protected $helpers = ['form'];

    public function index()
    {
        $order = $this->request->getGet('order') ?? "id";
        // ja ir range piemeram weight / size vai id tad $filter vajag but name un >
        // pimeram  $filter = "weight > " un $criteriaMin = "get value AND < $criteriaMax"
        $filter = $this->request->getGet('filter') ?? "id >";
        $criteriaMin = $this->request->getGet('criteriaMin') ?? "0";
        $criteriaMax = $this->request->getGet('criteriaMax') ?? "0";
        $tags = $this->request->getGet('tags') ?? "";

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

        //$tagSearch = "";
        if ($tags) {
            $filter = "";
            foreach (explode("|", $tags) as $index => $tag) {
                // if (count(explode("|", $tags)) === 0) {
                //     $filter .= "(metadata->>'tags')::jsonb @> '\"" . $tag . "\"'::jsonb AND";
                // } else {
                //     $filter .= "(metadata->>'tags')::jsonb @> '\"" . $tag . "\"'::jsonb";
                // }
                if ($index === 0) {
                    $filter = "(metadata->>'tags')::jsonb @> '\"" . $tag . "\"'::jsonb";  // Remove WHERE
                } else {
                    $filter .= " AND (metadata->>'tags')::jsonb @> '\"" . $tag . "\"'::jsonb";
                }
            }
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
        product.created_at ")
        ->where($filter, null, false)
        ->orderBy($order, 'ASC')
        ->get()
        ->getResult();

        $data['names'] = $productModel->select("id, name")->get()->getResult();
        $data['manufacturers'] = $productModel->select("product.id, metadata->>'manufacturer' as name")->get()->getResult();

        return view('product_show', $data);
    }

    public function showCreate()
    {
        return view('product_create');
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
        product.created_at
        FROM product
        WHERE id = ?", [$id])->getRow()];
        $data['manufacturers'] = null;
        $data['names'] = null;
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
        ];

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
        ];

        $data['tags'] = json_encode(explode("|", $data['tags']));
        //print_r($data['tags']);

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
                $image->move(WRITEPATH . 'uploads', $image->getName());
                $productImages[] = "uploads/".$image->getName();
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
        ];


        if (!$this->validate($rule)) {
            return view('product_create', [
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $data['tags'] = json_encode(explode("|", $data['tags']));

        $images = $this->request->getFiles();
        $productImages = [];
        foreach ($images['image'] as $image) {
            if ($image->isValid() && !$image->hasMoved()) {
                $productImages[] = "uploads/".$image->getName();
            }
        }
        $data['image'] = json_encode($productImages);

        $data['metadata'] = json_encode(array_slice($data, 1));
        $product->fill($data);

        if ($productModel->save($product)) {
            return view('success_message', [
                'message' => $product->name . ' has been created'
            ]);
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
}
