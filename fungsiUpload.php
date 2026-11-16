<?php
function upload_foto($file, &$error){
    $error = "";
    if($file['error'] === UPLOAD_ERR_NO_FILE){
        return null;
    }
    if($file['error'] !== UPLOAD_ERR_OK){
        $error = "Upload gagal";
        return null;
    }
    if($file['size'] > 2 * 1024 * 1024){
        $error = "Ukuran foto maksimal 2 MB";
        return null;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if(!in_array($ext, ['jpg', 'jpeg', 'png'])){
        $error = "Format foto JPG atau PNG";
        return null;
    }
    if(!@getimagesize($file['tmp_name'])){
        $error = "File tidak valid";
        return null;
    }
    $folder = __DIR__ . '/uploads/';
    if(!is_dir($folder)){
        mkdir($folder, 0755, true);
    }
    $namaBaru = uniqid('mobil_') . '.' . $ext;
    if(!move_uploaded_file($file['tmp_name'], $folder . $namaBaru)){
        $error = "Gagal menyimpan file";
        return null;
    }
    return $namaBaru;
}