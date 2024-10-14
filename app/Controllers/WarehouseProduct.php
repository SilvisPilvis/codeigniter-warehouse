<?php

namespace App\Controllers;

class WarehouseProduct extends BaseController
{

    public function index(int $id)
    {
        $warehouseProductModel = new \App\Models\WarehouseProductModel();
        $data['warehouse_product'] = $warehouseProductModel->where('warehouse_id', $id);
        $data['warehouse_product'] = $warehouseProductModel->join('product', 'product.id = warehouse_product.product_id', 'inner');
        $data['warehouse_product'] = $warehouseProductModel->join('warehouse', 'warehouse.id = warehouse_product.warehouse_id', 'inner');
        $data['warehouse_product'] = $warehouseProductModel->select('warehouse_product.*, product.name as product_name, warehouse.name as warehouse_name, warehouse_product.updated_at as updated_at, warehouse.address as address');
        $data['warehouse_product'] = $warehouseProductModel->findAll();

        return view('warehouse_product', $data);
    }

    public function create(int $id)
    {
        $warehouseProductModel = new \App\Models\WarehouseProductModel();
        $warehouseProduct = new \App\Entities\WarehouseProduct();

        $product = new \App\Models\ProductModel();
        $warehouse = new \App\Models\WarehouseModel();
        
        $data = [
            'product_id' => $this->request->getPost('product_id'),
            'product_count' => $this->request->getPost('product_count'),
            'warehouse_id' => $id,
        ];
   
        $rule = [
            'product_id' => 'required|is_natural_no_zero',
            'product_count' => 'required|is_natural_no_zero',
        ];

        $productData = $product->find($data['product_id']);
        $warehouseData = $warehouse->find($id);
        $products = $product->findAll();

        if (!$this->validate($rule)) {
            return view('warehouse_product_show', [
                'errors' => $this->validator->getErrors(),
                'warehouse_id' => $id,
                'warehouse' => $warehouseData,
                'products' => $products
            ]);
        }

        $warehouseProduct->fill($data);
        if ($warehouseProductModel->save($warehouseProduct)) {
            return view('success_message', [
                'message' => $warehouseProduct->product_count . ' ' . $productData['name'] . ' has been added to warehouse '. $warehouseData['name']
            ]);
        }
    }

    public function destroy(int $warehouse_id, int $product_id)
    {
        $warehouseProductModel = new \App\Models\WarehouseProductModel();
        $warehouseProduct = $warehouseProductModel->where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->select('warehouse_product.id')->first();
        print_r($warehouseProduct);
        if ($warehouseProductModel->delete($warehouseProduct['id'])) {
            return redirect()->to("/warehouse/" . $warehouse_id . '/product');
        }

        return view('error_message', [
            // 'message' => 'Product failed to be deleted'
            'message' => $warehouseProduct['id']
        ]);
    }

    public function edit(int $id)
    {
        $warehouseProductModel = new \App\Models\WarehouseProductModel();
        $warehouseProduct = new \App\Entities\WarehouseProduct();

        $product = new \App\Models\ProductModel();
        $warehouse = new \App\Models\WarehouseModel();
        
        $data = [
            'product_id' => $this->request->getPost('product_id'),
            'product_count' => $this->request->getPost('product_count'),
            'warehouse_id' => $id,
        ];
   
        $rule = [
            'product_id' => 'required|is_natural_no_zero',
            'product_count' => 'required|is_natural_no_zero',
        ];

        $productData = $product->find($data['product_id']);
        $warehouseData = $warehouse->find($id);
        $products = $product->findAll();

        if (!$this->validate($rule)) {
            return view('warehouse_product_edit', [
                'errors' => $this->validator->getErrors(),
                'warehouse_id' => $id,
                'warehouse' => $warehouseData,
                'products' => $products
            ]);
        }

        $warehouseProduct->fill($data);
        if ($warehouseProductModel->save($warehouseProduct)) {
            return view('success_message', [
                'message' => 'Edited warehouse ' . $warehouseData['name'] . ' data. Product is: ' . $productData['name'] . ' and amount is: ' . $warehouseProduct->product_count
            ]);
        }
    }

    public function showEdit(int $warehouse_id, int $product_id)
    {
        $warehouseProductModel = new \App\Models\WarehouseProductModel();
        $product = new \App\Models\ProductModel();
        $warehouse = new \App\Models\WarehouseModel();
        $data['products'] = $product->findAll();
        $data['warehouses'] = $warehouse->findAll();
        // $warehouseProduct = $warehouseProductModel->find($id);
        $data['current_warehouse_product'] = $warehouseProductModel->where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->select('warehouse_product.id, warehouse_product.product_count, warehouse_product.warehouse_id')->first();
        $data['current_product'] = $product->find($product_id);
        $data['current_warehouse'] = $warehouse->find($warehouse_id);

        return view('warehouse_product_edit', $data);
    }

    public function show(int $id)
    {
        $product = new \App\Models\ProductModel();
        $data['products'] = $product->findAll();
        $warehouse = new \App\Models\WarehouseModel();
        $data['warehouse'] = $warehouse->find($id);

        return view('warehouse_product_show', $data);
    }
}