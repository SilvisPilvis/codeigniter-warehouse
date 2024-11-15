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

        $pairs = explode(';', trim($template, '; '));
        $result = array();
        foreach ($pairs as $pair) {
            if (!empty($pair)) {
                list($key, $value) = explode(':', trim($pair));
                $result[trim($key)] = trim($value);
            }
        }

        // echo json_encode($result);
        $templateFields->query("INSERT INTO category_template (template, category_id) VALUES (?, ?)", [json_encode($result), $id]);
        $data['message'] = 'Template created successfully';
        return view('success_message', $data);
    }

    public function showCreate($id)
    {
        $template = new \App\Models\TemplatefieldsModel();
        $data['id'] = $id;
        $data['categories'] = $template->query("SELECT template FROM category_template")->getResult();
        $test = $data['categories'][0]->template;

        $result = array();
        foreach (json_decode($test) as $key => $value) {
            $result[] = "$key:$value";
            // echo $key.':'.$value;
        }
        $result[count($data['categories'])] .= ';';

        $data['categories'] = $result;
        return view('template_create', $data);
    }
}
