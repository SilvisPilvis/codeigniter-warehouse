<?php

namespace App\Controllers;

class Warehouse extends BaseController
{
    public function index()
    {
        $warehouseModel = new \App\Models\WarehouseModel();
        $data['warehouses'] = $warehouseModel->orderBy('updated_at desc')->findAll();
        return view('warehouse_show', $data);

    }

    public function show()
    {
        return view('warehouse_create');
    }

    public function showSingle(int $id)
    {
        $warehouseModel = new \App\Models\WarehouseModel();
        $data['warehouses'] =  [$warehouseModel->find($id)];
        return view('warehouse_show', $data);
    }

    public function showEdit(int $id)
    {
        $warehouseModel = new \App\Models\WarehouseModel();
        $data['warehouse'] = $warehouseModel->find($id);
        return view('warehouse_edit', $data);
    }

    public function saveEdit(int $id)
    {
        $warehouseModel = new \App\Models\WarehouseModel();
        $warehouse = new \App\Entities\Warehouse();
        $data = [
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
        ];
        $rule = [
            'name' => 'required|min_length[5]|max_length[255]',
            'address' => 'required|min_length[10]|max_length[255]',
        ];
        if (!$this->validate($rule)) {
            return view('warehouse_edit', [
                'errors' => $this->validator->getErrors(),
                'warehouse' => $warehouseModel->find($id),
            ]);
        }
        $warehouse->fill($data);
        if ($warehouseModel->update($id, $data)) {
            return view('success_message', [
                'message' => 'warehouse ' . $warehouse->name . ' has been updated'
            ]);
        }
    }

    public function create()
    {
        $warehouseModel = new \App\Models\WarehouseModel();
        $warehouse = new \App\Entities\Warehouse();

        $data = [
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
        ];

        $rule = [
            'name' => 'required|min_length[5]|max_length[255]',
            'address' => 'required|min_length[10]|max_length[255]',
        ];

        if (!$this->validate($rule)) {
            return view('warehouse_create', [
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $warehouse->fill($data);

        if ($warehouseModel->save($warehouse)) {
            return view('success_message', [
                'message' => $warehouse->name . ' has been created'
            ]);
        }
    }

    public function delete(int $id)
    {
        $warehouseModel = new \App\Models\WarehouseModel();
        $name = $warehouseModel->find($id)['name'];
        if ($warehouseModel->delete($id)) {
            return view('success_message', [
                'message' => 'warehouse ' . $name . ' has been deleted'
            ]);
        }
    }

}
?>