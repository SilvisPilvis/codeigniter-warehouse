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

        $allowedTypes = [
            'text' => true,
            'select' => true,
            'checkbox' => true,
            'radio' => true,
            'date' => true,
            'datetime' => true,
            'time' => true,
            'number' => true,
            'email' => true,
            'tel' => true,
            'color' => true,
            'file' => true,
        ];

        $errors = [];

        $pairs = explode(';', trim($template, '; '));
        $result = array();
        foreach ($pairs as $pair) {
            if (!empty($pair)) {
                list($key, $value) = explode(':', trim($pair));
                $result[trim($key)] = trim($value);
                if (!array_key_exists($result[trim($key)], $allowedTypes)) {
                    $errors[] = "Invalid velue type for field $key";
                }
            }
        }

        if (!empty($errors)) {
            $data = [
                'id' => $id,
                'categories' => $templateFields->query("SELECT template FROM category_template")->getResult(),
                'errors' => $errors,
            ];
            // convert to array
            $result = array();
            $test = $data['categories'][0]->template;
            foreach (json_decode($test) as $key => $value) {
                $result[] = "$key:$value";
            }
            $result[count($data['categories'])] .= ';';
            $data['categories'] = $result;
            // return view with data
            return view('template_create', $data);
        } else {
            $templateFields->query("INSERT INTO category_template (template, category_id) VALUES (?, ?)", [json_encode($result), $id]);
            $data['message'] = 'Template created successfully';
            return view('success_message', $data);
        }

    }

    public function showUpdate($id)
    {
        $template = new \App\Models\TemplatefieldsModel();
        $data['id'] = $id;
        $data['categories'] = $template->query("SELECT template FROM category_template WHERE category_id = ?", [$id])->getResult();

        if (!empty($data['categories'])) {
            $test = $data['categories'][0]->template;

            $result = array();
            foreach (json_decode($test) as $key => $value) {
                $result[] = "$key:$value";
                // echo $key.':'.$value;
            }
            $result[count($data['categories'])] .= ';';

            $data['categories'] = $result;
        } else {
            $data['categories'] = [];
        }
        return view('template_edit', $data);

    }

    public function update($id)
    {
        $template = $this->request->getPost('data');
        $templateFields = new \App\Models\TemplatefieldsModel();
        $templateFields->data = $template;
        $templateFields->template_id = $id;

        $allowedTypes = [
            'text' => true,
            'select' => true,
            'checkbox' => true,
            'radio' => true,
            'date' => true,
            'datetime' => true,
            'time' => true,
            'number' => true,
            'email' => true,
            'tel' => true,
            'color' => true,
            'file' => true,
        ];

        $errors = [];

        $pairs = explode(';', trim($template, '; '));
        $result = array();
        foreach ($pairs as $pair) {
            if (!empty($pair)) {
                list($key, $value) = explode(':', trim($pair));
                $result[trim($key)] = trim($value);
                if (!array_key_exists($result[trim($key)], $allowedTypes)) {
                    $errors[] = "Invalid velue type for field $key";
                }
            }
        }

        if (!empty($errors)) {
            $data = [
                'id' => $id,
                'categories' => $templateFields->query("SELECT template FROM category_template")->getResult(),
                'errors' => $errors,
            ];
            // convert to array
            $result = array();
            $test = $data['categories'][0]->template;
            foreach (json_decode($test) as $key => $value) {
                $result[] = "$key:$value";
            }
            $result[count($data['categories'])] .= ';';
            $data['categories'] = $result;
            // return view with data
            return view('template_create', $data);
        } else {
            $templateFields->query("UPDATE category_template SET template = ? WHERE category_id = ?", [json_encode($result), $id]);
            $data['message'] = 'Template updated successfully';
            return view('success_message', $data);
        }
    }

    public function showCreate($id)
    {
        $template = new \App\Models\TemplatefieldsModel();
        $data['id'] = $id;
        $data['categories'] = $template->query("SELECT template FROM category_template WHERE category_id = ?", [$id])->getResult();

        if (!empty($data['categories'])) {
            $test = $data['categories'][0]->template;

            $result = array();
            foreach (json_decode($test) as $key => $value) {
                $result[] = "$key:$value";
                // echo $key.':'.$value;
            }
            $result[count($data['categories'])] .= ';';

            $data['categories'] = $result;
        } else {
            $data['categories'] = [];
        }
        return view('template_create', $data);
    }

    public function getFields($id)
    {
        $template = new \App\Models\TemplatefieldsModel();
        $data['categories'] = $template->query("SELECT template FROM category_template WHERE category_id = ?", [$id])->getResult();

        if (!empty($data['categories'])) {
            $test = $data['categories'][0]->template;

            $result = array();
            foreach (json_decode($test) as $key => $value) {
                $result[] = "$key:$value";
                // echo $key.':'.$value;
            }
            $result[count($data['categories'])] .= ';';

            echo json_encode($result);
            return $result;

        } else {
            echo json_encode(['errors' => 'No categories found']);
            return ['errors' => 'No categories found'];
        }
    }
}
