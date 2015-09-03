<?php
/**
 * Functions for parsing directories (albums) for images.
 * @author Ben Tomlin (http://tomlin.no)
 **/

/**
 * Get all albums (directories) and underlying images.
 * @param string $folder  path to directory to read
 * @return array          array containing album data
 */
function getAlbums($folder)
{
    $albums = array();

    if ($handle = opendir($folder))
    {
        while (false !== ($file = readdir($handle)))
        {
            $path = $folder . $file;

            if (is_dir($path) && $file != '..' && $file != '.')
            {
                $images = getImages($path . '/');
                $albums[$file] = array(
                    'name' => ucfirst(substr($file, 2)),
                    'link' => $path,
                    'images' => $images,
                    'size' => count($images)
                );
            }
        }
        closedir($handle);
    }
    ksort($albums);
    return $albums;
}

/**
 * Get all images in specified folder.
 * @param string $folder  path to directory to read
 * @return array          array containing image data
 */
function getImages($folder)
{
    $images = array();

    if ($handle = opendir($folder))
    {
        while (false !== ($file = readdir($handle)))
        {
            $path = $folder . $file;

            if (!is_dir($path) && isImage($file))
            {
                list($width, $height) = getimagesize($path);
                $images[$file] = array(
                    'is_dir' => false,
                    'name' => $file,
                    'short' => strlen($file) > 30  ? substr($file, 0, 20) . '...' . substr($file, -10) : $file,
                    'link' => $path,
                    'thumb' => $file,
                    'width' => $width,
                    'height' => $height,
                    'size' => getSize($path)
                );
            }
        }
        closedir($handle);
    }
    ksort($images);
    return $images;
}

/**
 * Checks whether the file is of a supported image type.
 * @param string $file  filename of the file to check
 * @return bool         true if supported image, false otherwise
 */
function isImage($file)
{
    $ext = substr($file, -4);
    return ($ext === '.jpg' || $ext === 'jpeg' || $ext === '.png' || $ext === '.gif');
}

/**
 * Get formatted size of given file (path).
 * @param string $file  path to file in which to get size
 * @return string       formatted file size
 */
function getSize($file)
{
    $size = filesize($file);

    if ($size < pow(1024, 2))
        return number_format($size/1024, 2) . ' KB';

    return number_format($size/1024/1024, 2) . ' MB';
}

/**
 * Generates thumbnail images for all new images.
 * @param array $albums  array containing albums with images
 * @param string $dir    directory in which to save thumbnails
 */
function generateThumbs($albums, $dir)
{
    require_once('ezimage.php');

    foreach($albums as $album)
    {
        foreach($album['images'] as $image)
        {
            if (!file_exists($dir . $image['name']))
            {
                $ezimg = new EzImage();
                $ezimg->load($image['link']);

                if ($ezimg->getHeight() > $ezimg->getWidth())
                    $ezimg->resizeToHeight(200);
                else
                    $ezimg->resizeToWidth(200);

                $ezimg->save($dir . $image['name']);
            }
        }
    }
}

/**
 * Upload a file or store error message in session on failure.
 * @param string $dir  directory in which to save uploaded files
 */
function upload($dir)
{
    if ($_FILES["file"]["error"] == UPLOAD_ERR_INI_SIZE)
        $_SESSION['error'] = 'Error: The uploaded file exceeds the upload_max_filesize directive in php.ini';
    else if ($_FILES["file"]["error"] == UPLOAD_ERR_FORM_SIZE)
        $_SESSION['error'] = 'Error: The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
    else if ($_FILES["file"]["error"] == UPLOAD_ERR_PARTIAL)
        $_SESSION['error'] = 'Error: The uploaded file was only partially uploaded';
    else if ($_FILES["file"]["error"] == UPLOAD_ERR_NO_FILE)
        $_SESSION['error'] = 'Error: No file was specified';
    else if ($_FILES["file"]["error"] == UPLOAD_ERR_NO_TMP_DIR)
        $_SESSION['error'] = 'Error: Missing a temporary folder';
    else if ($_FILES["file"]["error"] == UPLOAD_ERR_CANT_WRITE)
        $_SESSION['error'] = 'Error: Failed to write file to disk';
    else if ($_FILES["file"]["error"] == UPLOAD_ERR_EXTENSION)
        $_SESSION['error'] = 'Error: A PHP extension stopped the file upload';
    else if (file_exists(realpath($dir) . $_FILES["file"]["name"]))
        $_SESSION['error'] = 'Error: A file with that name already exists';
    else {
        move_uploaded_file($_FILES["file"]["tmp_name"], $dir . $_FILES["file"]["name"]);
    }
    header('Location: /');
    exit;
}

?>