<form action="/" method="POST" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="media-upload">
    <input type="submit" value="Upload Image">
</form>

<?php
// note: if building a system for uploading other types of files, see -> finfo_file
var_dump($_FILES);


// source
$source_path = $_FILES["media-upload"]["name"];
$source_name = pathinfo($source_path, PATHINFO_FILENAME);
$ext = pathinfo($source_path, PATHINFO_EXTENSION);
$mime = $_FILES["media-upload"]["type"];
$size = $_FILES["media-upload"]["size"];

list($file_width, $file_height, $file_type, $file_attr) = getimagesize($_FILES["media-upload"]["tmp_name"]);


// destination
$target_path = DATA_PATH."images/";
$target_filename = get_random_filename(59 -  strlen($ext)).".$ext";
$target_path .= $target_filename;


// verification
if (substr($mime, 0, 6) !== "image/") {
    // todo error: not image
    return;
}

if ($size === 0) {
    // todo error: corrupt?
    return;
}

if (($file_width <= 0) || ($file_height <= 0)) {
    // todo error: wtf
    return;
}

if (count($ext) === 0) {
    // todo error: be nice
    return;
}

if ($size > 8000000) { // circa 7-8 MB 
    // todo error: file too large
    return;
}


// handling
// todo
?>