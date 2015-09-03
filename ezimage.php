<?php
/**
 * Simple image manipulation (supports jpg, png and gif)
 * @author Ben Tomlin (http://tomlin.no)
 * @version 2.0 (2015)
 *
 * Example usage:
 *  $ezimg = new EzImage();
 *  $ezimg->load('filename.png');
 *  $ezimg->resizeToWidth(250);
 *  $ezimg->save('newfile.png', IMAGETYPE_PNG);
 */
class EzImage
{
    /**
     * The loaded image resource
     * @var resource
     */
    private $image;

    /**
     * Image type constant
     * @var int
     */
    private $type;

    /**
     * The width of the image
     * @var int|string
     */
    private $width;

    /**
     * The height of the image
     * @var int|string
     */
    private $height;

    /**
     * Load a specified file from the given filename/path
     * @param string $file  the path to the file to load
     */
    function load($file)
    {
        $info = getimagesize($file);

        $this->width = $info[0];
        $this->height = $info[1];
        $this->type = $info[2];

        if ($this->type == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($file);
        } else if ($this->type == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($file);
        } else if ($this->type == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($file);
        }
    }
    
    /**
     * Save the current image with a specified name
     * @param string $name     new name of the file to save
     * @param int $type        override image file type constant
     * @param int $quality     quality percentage for jpeg images
     * @param int $permissions permissions to set on the newly created file
     */
    function save($name, $type = null, $quality = 90, $permissions = null)
    {
        $type = $type == null ? $this->type : $type;

        if ($type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $name, $quality);
        } else if ($type == IMAGETYPE_GIF) {
            imagegif($this->image, $name);
        } else if ($type == IMAGETYPE_PNG) {
            imagepng($this->image, $name);
        }
        if ($permissions != null) {
            chmod($name, $permissions);
        }
    }
    
    /**
     * Output the image directly
     * @param int $type  override image file type constant
     */
    function output($type = null)
    {
        $type = $type == null ? $this->type : $type;

        if ($type == IMAGETYPE_JPEG) {
            imagejpeg($this->image);
        } else if ($type == IMAGETYPE_GIF) {
            imagegif($this->image);
        } else if ($type == IMAGETYPE_PNG) {
            imagepng($this->image);
        }
    }
    
    /**
     * Get the width of the current image
     * @return string  the image width
     */
    function getWidth()
    {
        return $this->width;
    }
    
    /**
     * Get the height of the current image
     * @return string  the image height
     */
    function getHeight()
    {
        return $this->height;
    }
    
    /**
     * Resize the image to a specified width (while maintaining the ratio)
     * @param int $width  new width of the image
     */
    function resizeToWidth($width)
    {
        $ratio = $width / $this->width;
        $height = $this->height * $ratio;
        $this->resize($width, $height);
    }
    
    /**
     * Resize the image to a specified height (while maintaining the ratio)
     * @param int $height  new height of the image
     */
    function resizeToHeight($height)
    {
        $ratio = $height / $this->height;
        $width = $this->width * $ratio;
        $this->resize($width, $height);
    }

    /**
     * Resize (scale) the image by percent (while maintaining the ratio)
     * @param int $scale  percent to scale new image (1-100)
     */
    function scale($scale)
    {
        $width = $this->width * $scale/100;
        $height = $this->height * $scale/100;
        $this->resize($width, $height);
    }

    /**
     * Does the actual resizing of the current image
     * @param int $width   new width of the image
     * @param int $height  new height of the image
     */
    function resize($width, $height)
    {
        $newimage = imagecreatetruecolor($width, $height);
        imagecopyresampled($newimage, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        $this->image = $newimage;
    }

    /**
     * Resizes and crops the current image to specified size (while maintaining the ratio)
     * @param int $width   new width of the image
     * @param int $height  new height of the image
     */
    function crop($width, $height)
    {
        if ($this->width > $this->height) {
            $y = 0;
            $x = ($this->width - $this->height) / 2;
            $smallest = $this->height;
        } else {
            $x = 0;
            $y = ($this->height - $this->width) / 2;
            $smallest = $this->width;
        }
        $newimage = imagecreatetruecolor($width, $height);
        imagecopyresampled($newimage, $this->image, 0, 0, $x, $y, $width, $height, $smallest, $smallest);
        $this->image = $newimage;
    }
}
?>