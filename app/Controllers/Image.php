<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;

class Image extends BaseController
{

    public function index(string $filepath)
    {

        $path = WRITEPATH . 'uploads/' . $filepath;

        if (!file_exists($path)) {
            return $this->response->setStatusCode(404, 'Image Not Found');
        }
        
        $content = file_get_contents($path);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($path);

        $response = new Response($this->response);
        $response->setHeader('Content-Type', $mimeType);
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-Disposition', 'inline; filename="'.$path.'";');
        $response->setBody($content);
        $response->setStatusCode(200, 'OK');

        return $response;
    }
}
?>