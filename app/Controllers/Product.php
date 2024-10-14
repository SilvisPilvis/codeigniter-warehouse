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
            'image' => $this->request->getPost('image'),
            'manufacturer' => $this->request->getPost('manufacturer'),
            'weight' => $this->request->getPost('weight'),
            'size' => $this->request->getPost('size'),
        ];

        switch ($data):
            case !str_contains($data['image'], 'http'):
                // Return error message if image is not a url
                return view('product_edit', [
                    'errors' => 'Image must be a valid url'
                ]);
            case !str_contains($data['weight'], 'Kg'):
                $data['weight'] = $data['weight'] . 'Kg';
            case !str_contains($data['size'], 'Cm³'):
                $data['size'] = $data['size'] . 'Cm³';
        endswitch;

        $rule = [
            'name' => 'required|min_length[3]|max_length[255]',
            'image' => 'required|min_length[10]|max_length[255]',
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
        $data['metadata'] = json_encode(array_slice($data, 1));
        $product->fill($data);
        if ($productModel->update($id, $data)) {
            return view('success_message', [
                'message' => 'product ' . $product->name . ' has been updated'
            ]);
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
        $config = \Config\Services::config();

        // $file = new \CodeIgniter\Files\File('uploads');

        // $config['upload_path'] = WRITEPATH . 'uploads';
        // $config['allowed_types'] = 'jpg|jpeg|png';
        // $config['max_size'] = 1024;
        // $config['max_width'] = 1024;
        // $config['max_height'] = 768;

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
            // case !str_contains($data['image'], 'http'):
                // Return error message if image is not a url
                // return view('product_edit', [
                //     'errors' => 'Image must be a valid url'
                // ]);
                // $errors[] = 'Image must be a valid url';
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
                // if(!$image->hasMoved()) {
                $newPath = $image->move(WRITEPATH . 'uploads', $image->getName());
                // $productImages[] = $newPath->getRealPath();
                $productImages[] = "uploads/".$image->getName();
            }
        }
        $data['image'] = json_encode($productImages);
        // $data['image'] = $images;

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