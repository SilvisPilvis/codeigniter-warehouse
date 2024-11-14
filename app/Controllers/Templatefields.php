<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Templatefields extends BaseController
{
    public function index()
    {
        // $templateFields = new \App\Models\TemplatefieldsModel();
        // $templateFields->findAll();
        // return view('templatefields/index');
        $template = $this->request->getPost('data');
        $templateFields = new \App\Models\TemplatefieldsModel();
        echo $template;
        $productModel = new \App\Models\ProductModel();
        $data['categories'] = $productModel->query("SELECT category.id, category.name FROM category")->getResult();
        return view('template_show', $data);
    }

    public function create($id)
    {
        $template = $this->request->getPost('data');
        $templateFields = new \App\Models\TemplatefieldsModel();
        $templateFields->data = $template;
        $templateFields->template_id = $id;
        $template->query("INSERT INTO templatefields (data, category_id) VALUES (:data, :category_id)", ['data' => $template['data'], 'category_id' => $id]);
        // $templateFields->save();
        // echo $template;
        $data['message'] = 'Template created successfully';
        return view('success_message');
    }

    public function showCreate()
    {
        return view('template_create');
    }
}
