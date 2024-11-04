<?php

namespace App\Controllers;

class Product extends BaseController
{

    protected $helpers = ['form'];

    public function index()
    {
        $productModel = new \App\Models\ProductModel();
        $data['products'] = $productModel->orderBy('updated_at desc')->findAll();
        return view('product_show', $data);
    }

    public function show(int $id)
    {
        $productModel = new \App\Models\ProductModel();
        $data['product'] = $productModel->find($id);
        return view('product_show', $data);
    }

    public function showCreate()
    {
        return view('product_create');
    }

    public function showSingle(int $id)
    {
        $productModel = new \App\Models\ProductModel();
        $data['products'] =  [$productModel->find($id)];
        return view('product_show', $data);   
    }

    public function showEdit(int $id)
    {
        $productModel = new \App\Models\ProductModel();
        $data['product'] = $productModel->find($id);
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
        ];

        switch ($data):
            case !str_contains($data['weight'], 'Kg'):
                $data['weight'] = $data['weight'] . 'Kg';
            case !str_contains($data['size'], 'Cm³'):
                $data['size'] = $data['size'] . 'Cm³';
        endswitch;

        $rule = [
            'name' => 'required|min_length[3]|max_length[255]',
            'image' => [
                'label' => 'Image',
                'rules' => 'max_size[image,1024]|max_dims[image,1024,768]|mime_in[image,image/jpg,image/jpeg,image/png]',
            ],
            'manufacturer' => 'required|min_length[3]|max_length[255]',
            'weight' => 'required|numeric',
            'size' => 'required|numeric',
        ];

        if (!$this->validate($rule)) {
            return view('product_edit', [
                'errors' => $this->validator->getErrors(),
                'product' => $productModel->find($id),
            ]);
        }

        $images = $this->request->getFiles();
        $productImages = [];
        foreach ($images['image'] as $image) {
            if($image->isValid() && !$image->hasMoved()) {
                $newPath = $image->move(WRITEPATH . 'uploads', $image->getName());
                $productImages[] = "uploads/".$image->getName();
            }
        }

        // TEST add existing images from db
        $existingMetadata = $productModel->find($id);
        $existingPics = json_decode($existingMetadata["metadata"], true)["image"];
        if (gettype($existingPics) == 'array'){
            if(count($existingPics) > 0){
                foreach(json_decode($existingPics) as $pic) {
                    if(substr_count($pic, "uploads/") <= 0){
                        // break;
                        $productImages[] = "uploads/".$pic;
                    }else{
                        $productImages[] = $pic;
                    }
                }
            }
        }
        // print_r($productImages);
        
        $data['image'] = json_encode($productImages);

        $data['metadata'] = json_encode(array_slice($data, 1));
        $product->fill($data);

        if ($productModel->update($id, $data)) {
            return view('success_message', [
                'message' => 'product ' . $product->name . ' has been updated'
            ]);
        }
    }

    public function deleteImages(int $id)
    {
        $productModel = new \App\Models\ProductModel();
        $existingProduct = $productModel->find($id);
        $existingRawMetadata = $existingProduct["metadata"];
        $existingMetadata = json_decode($existingRawMetadata, true);

        foreach(json_decode($existingMetadata["image"]) as $image) {
            // execute this if images failed to delete
            $errors = ["Failed to delete image(s)."];
            if (!unlink(WRITEPATH.$image)) {
                return view('product_edit', $errors);
            }
        }

        $existingMetadata["image"] = [];
        $existingProduct["metadata"] = json_encode($existingMetadata);
        $data['product'] = $existingProduct;
        if ($productModel->update($id, $existingProduct)) {
            return view('product_edit', $data);
        }
    }

    public function deleteSingleImage(int $id, string $name)
    {
        $productModel = new \App\Models\ProductModel();
        $existingProduct = $productModel->find($id);
        $existingRawMetadata = $existingProduct["metadata"];
        $existingMetadata = json_decode($existingRawMetadata, true);

        $images = json_decode($existingMetadata["image"], true);

        for($x = 0; $x < count($images); $x++) {
            if ($images[$x] == "uploads/".$name) {
                unset($images[$x]);
                if (!unlink(WRITEPATH ."uploads/".$name)) {
                    $errors = ["Failed to delete image(s)."];
                    return view('product_edit', $errors);
                }
                break;
            }
        }
  
        $existingMetadata["image"] = json_encode($images);

        $existingProduct["metadata"] = json_encode($existingMetadata);
        
        print_r($existingMetadata);
        $data['product'] = $existingProduct;

        if ($productModel->update($id, $existingProduct)) {
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
        ];

        // $errors = [];

        switch ($data):
            case !str_contains($data['weight'], 'Kg'):
                $data['weight'] = $data['weight'] . 'Kg';
            case !str_contains($data['size'], 'Cm³'):
                $data['size'] = $data['size'] . 'Cm³';
        endswitch;

        

        $rule = [
            'name' => 'required|min_length[3]|max_length[255]',
            'image' => [
                'label' => 'Image',
                'rules' => 'uploaded[image]|max_size[image,1024]|max_dims[image,1024,768]|mime_in[image,image/jpg,image/jpeg,image/png]',
            ],
            'manufacturer' => 'required|min_length[3]|max_length[255]',
            'weight' => 'required|numeric',
            'size' => 'required|numeric',
        ];

        if (!$this->validate($rule)) {
            return view('product_create', [
                'errors' => $this->validator->getErrors(),
            ]);
        }

        
        $images = $this->request->getFiles();
        $productImages = [];
        foreach ($images['image'] as $image) {
            if($image->isValid() && !$image->hasMoved()) {
                $newPath = $image->move(WRITEPATH . 'uploads', $image->getName());
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