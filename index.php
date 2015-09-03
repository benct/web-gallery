<?php
/**
 * Dynamically display images with external js module.
 * @author Ben Tomlin (http://tomlin.no)
 */

define('ALBUMS', 'images/');
define('THUMBS', 'thumbs/');
define('UPLOAD', 'uploads/');

define('TITLE', 'My Albums');

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
session_regenerate_id(true);

require_once('func.php');

if (isset($_POST['upload']))
    upload(UPLOAD);

$albums = getAlbums(ALBUMS);

if (isset($_GET['generate']))
    generateThumbs($albums, THUMBS);

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo TITLE; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />

    <link rel="shortcut icon" type="image/png" href="favicon.png" />
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="photoswipe/default-skin/default-skin.css" />
    <link rel="stylesheet" href="photoswipe/photoswipe.css" />

    <script src="photoswipe/photoswipe.js" type="text/javascript"></script>
    <script src="photoswipe/photoswipe-ui-default.js" type="text/javascript"></script>
    <script src="photoswipe/photoswipe-dom.js" type="text/javascript"></script>
</head>
<body id="top">

    <div class="nav">
        <?php if (isset($_SESSION['error'])): ?>
        <span style="color:#900"><?php echo $_SESSION['error']; ?></span><br/><br/>
        <?php unset($_SESSION['error']); endif; ?>
        <?php foreach($albums as $album): ?>
        <a href="#<?php echo $album['name']; ?>"><?php echo $album['name']; ?> (<?php echo $album['size']; ?>)</a> |
        <?php endforeach; ?>
        <a href="#upload">Upload</a>
    </div>

    <?php foreach($albums as $album): ?>
    <div class="wrapper">
        <div class="container" id="<?php echo $album['name']; ?>">
            <a class="top" href="#top" title="Go to top"><img src="top.png"/></a><h2><?php echo $album['name']; ?></h2>
            <div class="gallery" data-pswp-uid="1" itemscope itemtype="http://schema.org/ImageGallery">
                <?php foreach($album['images'] as $image): ?>
                <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                    <a href="<?php echo $image['link']; ?>" itemprop="contentUrl" data-size="<?php echo $image['width'] . 'x' . $image['height']; ?>">
                        <img src="<?php echo THUMBS . $image['thumb']; ?>" itemprop="thumbnail" alt="<?php echo $image['name']; ?>" />
                    </a>
                    <figcaption itemprop="caption description"><?php echo $image['short'] . ' - ' . $image['size']; ?></figcaption>
                </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="wrapper">
        <div class="container" id="upload">
            <a class="top" href="#top" title="Go to top"><img src="top.png"/></a>
            <form class="upload" action="/" method="post" enctype="multipart/form-data">
                <input name="file" type="file" />
                <input name="upload" type="submit" value="Upload" />
            </form>
        </div>
    </div>

    <div class="pswp" id="pswp" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="pswp__bg"></div>
        <div class="pswp__scroll-wrap">
            <div class="pswp__container">
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
            </div>
            <div class="pswp__ui pswp__ui--hidden">
                <div class="pswp__top-bar">
                    <div class="pswp__counter"></div>
                    <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                    <button class="pswp__button pswp__button--share" title="Share"></button>
                    <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                    <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                    <div class="pswp__preloader">
                        <div class="pswp__preloader__icn">
                          <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                    <div class="pswp__share-tooltip"></div>
                </div>
                <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
                <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
                <div class="pswp__caption">
                    <div class="pswp__caption__center"></div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        initPhotoSwipeFromDOM('.gallery');
    </script>

</body>
</html>
