<?php

/*
 * Class SmushIt
 *  Compresses images for you using the Yahoo Smush it page
 *  http://www.smush.it/
 *
 * Author:
 *  Frank Broersen
 *  www.pitgroup.nl
 *
 * Usage:
 *  $smush = new SmushIt;
 *  $smush->base = 'http://www.yourdomain.com'; // ( Must be accessible for the Smush It api )
 *  if( !$smush->smush('assets/img/unsmushed/logo.png','assets/smushed/logo.png') ) {
 *    echo $smush->msg;
 *  } else {
 *    echo 'saved: ' . $smush->savings . 'kb (' . $smush->savings_perc . '%)';
 *  }
 */
class SmushIt {

    private $src_image;

    private $res_image;

    private $smush_url;

    private $msg;

    private $base;

    private $savings;

    private $savings_perc;

    public function __construct() {

        $this->src_image = '';

        $this->res_image = '';

        $this->smush_url = 'http://www.smushit.com/ysmush.it/ws.php?img=';

        $this->base = '';

        $this->msg = '';

        $this->savings = 0;

        $this->savings_perc = 0;

    }

    /**
     * smush
     * Smushes an image
     *
     * @param string $src_image the directory and filename of the source image
     * @param string $res_image the directory and filename of the result image
     * @return bool
     */
    public function smush($src_image = '', $res_image = '') {

        /**
         * Data Checks for input and used methods
         */
        if ($src_image != '') {
            $this->src_image = $src_image;
        }
        if ($this->src_image == '') {
            $this->msg = 'The source image cannot be empty.';
            return false;
        }

        if ($res_image != '') {
            $this->res_image = $res_image;
        }
        $this->res_image = $res_image;
        if ($this->res_image == '') {
            $this->msg = 'The result image cannot be empty.';
            return false;
        }

        if (!function_exists('json_decode')) {
            $this->msg = 'Json is not supported.';
            return false;
        }

        if (!function_exists('curl_init')) {
            $this->msg = 'Curl is not supported.';
            return false;
        }

        if (!file_exists($src_image)) {
            $this->msg = 'The source file does not exists.';
            return false;
        }

        /**
         * Open the Smush.it url with the input images and save the result into $result
         */
        $url = $this->smush_url . $this->base . $this->src_image;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);

        if ($result == '') {
            $this->msg = 'An unknown error occured, check the smushit web page.';
            return false;
        }

        /**
         * Parse $result into an object
         */
        $data = json_decode($result);

        /**
         * If the error var isset, return false/
         */
        if (isset($data->error)) {
            $this->msg = 'Image cannot be smushed.';
            return false;
        }

        if ($data->dest_size < $data->src_size) {

            $this->savings = $data->src_size - $data->dest_size;

            $this->savings_perc = $data->percent;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $data->dest);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $image = curl_exec($ch);
            curl_close($ch);

            $fh = @fopen($this->res_image, 'w');
            @fwrite($fh, $image);
            @fclose($fh);

            $this->msg = 'The image has been smushed.';
            return true;
        }

        $this->msg = 'No savings could be made.';
        return true;

    }

    /**
     * Setter
     *
     * @param string $var
     * @param <type> $value
     */
    public function __set($var, $value = '') {
        switch ($var) {

            case 'base':
                $this->base = $value;
                break;

            case 'savings':
                $this->savings = $value;
                break;

            case 'savings_perc':
                $this->savings_perc = $value;
                break;

            case 'msg':
                $this->msg = $value;
                break;

            /*
             * Easy source setting
             */
            case 'src_image':
            case 'source':
            case 'src':
                $this->src_image = $value;
                break;

            /*
             * Easy result setting
             */
            case 'res_image':
            case 'result':
            case 'res':
                $this->res_image = $value;
                break;

            default:
                throw new Exception("You are trying to set an unknown or private variable: " . $var);
                break;
        }
    }

    /**
     * Getter
     *
     * @param string $var
     * @return <type>
     */
    public function __get($var) {
        switch ($var) {

            case 'url':
                return $this->url;
                break;

            case 'savings':
                return $this->savings;
                break;

            case 'savings_perc':
                return $this->savings_perc;
                break;

            case 'base':
                return $this->base;
                break;

            case 'msg':
                return $this->msg;
                break;

            /*
             * Easy source setting
             */
            case 'src_image':
            case 'source':
            case 'src':
                return $this->src_image;
                break;

            /*
             * Easy result setting
             */
            case 'res_image':
            case 'result':
            case 'res':
                return $this->res_image;
                break;

            default:
                throw new Exception("You are trying to get an unknown variable: " . $var);
                break;
        }
    }

}

/**
 * Class DBImageOpt
 *
 * Class to optimize image for magento
 *
 * @author Martin Beukman <martin@digitalbits.nl>
 *
 * @todo   use an admin value for cron schedule
 * @todo   clean up this file and move classes to their own model
 *
 */
class DBImageOpt {

    /**
     * @var string
     */
    private $_sMediaDir = '';

    /**
     * @var int
     */
    private $_ImgTargetHeight = 1000;

    /**
     * @var int
     */
    private $_ImgTargetWidth = 1000;

    /**
     * @var int
     */
    private $_ImgTargetQuality = 60;

    /**
     * @var bool
     */
    private $_DebugInfo = false;

    /**
     * @var array
     */
    private $_ImageOptInfo = array();

    /**
     * @var bool
     */
    private $_bTestmode = false;

    /**
     * @var string
     */
    private $_sBaseUrl = '';

    /**
     * @var string
     */
    private $_sRootDir = '';

    /**
     * @var string
     */
    private $_sProcessDir = '';

    /**
     * @var string
     */
    private $_sWork_file = '';

    /**
     * @var string
     */
    private $_sLog_file = '';

    /**
     * @var int
     */
    private $_sProcessedImages = 0;

    /**
     * @var array
     */
    private $_wImages = array();

    /**
     *
     */
    public function __construct() {
        //make the smush it class available
        $this->_oSmush = new SmushIt;
    }

    /**
     * @param null $url
     */
    public function setBaseUrl($url = null) {
        $this->_sBaseUrl     = $url;
        $this->_oSmush->base = $url;
    }

    /**
     * @param null $dir
     */
    public function setRootDir($dir = null) {
        $this->_sRootDir = $dir;
    }

    /**
     * @param null $dir
     */
    public function setProcessDir($dir = null) {
        $this->_sProcessDir = $dir;
    }

    /**
     * @param null $mediadir
     */
    public function setMediaDir($mediadir = null) {
        $this->_sMediaDir = $mediadir;
    }

    /**
     * @param bool $test
     */
    public function setTestMode($test = false) {
        $this->_bTestmode = $test;
    }

    /**
     * @param int $height
     */
    public function setImageTargetHeight($height = 1000) {
        $this->_ImgTargetHeight = $height;
    }

    /**
     * @param int $width
     */
    public function setImageTargetWidth($width = 1000) {
        $this->_ImgTargetWidth = $width;
    }

    /**
     * @param int $quality
     */
    public function setImageTargetQuality($quality = 51) {
        $this->_ImgTargetQuality = $quality;
    }

    /**
     * @param bool $debug
     */
    public function setImageInfo($debug = false) {
        $this->_DebugInfo = $debug;
    }


    /**
     * @return int
     */
    public function OptimiseImages() {
        $this->_sWork_file = $this->_sRootDir . $this->_sProcessDir . "work_list";
        $update_list       = true;

        if (is_dir($this->_sRootDir . $this->_sProcessDir) && !is_writable($this->_sRootDir . $this->_sProcessDir)) {
            die('Error: folder ' . $this->_sRootDir . $this->_sProcessDir . ' should be writeable');
        }
        if ($fh = fopen($this->_sWork_file, "rb")) {
            $data           = file_get_contents($this->_sWork_file);
            $this->_wImages = explode("\n", $data);
            if (count($this->_wImages) > 0 && strlen($this->_wImages[0]) > 0) {
                $update_list = false;
            }
        }
        if ($update_list) {
            $this->ReadDirToArray($this->_sRootDir . $this->_sMediaDir);
            $fh = fopen($this->_sWork_file, "w");
            fwrite($fh, implode("\n", $this->_wImages));
        }
        fclose($fh);
        $this->DoOptimise();
        $this->SmushImages();
        $this->writeImageLog();
        return $this->_sProcessedImages;
    }

    /**
     *
     * @return void
     *
     * @todo leave the logging to magento
     */
    private function writeImageLog() {
        $this->_sLog_file = $this->_sRootDir . $this->_sProcessDir . date('Ymd');
        if (file_exists($this->_sLog_file)) {
            $fh = fopen($this->_sLog_file, 'a');
            fwrite($fh, print_r($this->_ImageOptInfo, true));
            fclose($fh);
        } else {
            $fh = fopen($this->_sLog_file, "w");
            fwrite($fh, print_r($this->_ImageOptInfo, true));
            fclose($fh);
        }
    }

    /**
     * @param string $dir
     * @return void
     *
     * @todo reverse logic with skipping files to include only mentioned file formats use magento settings?
     */
    private function ReadDirToArray($dir = null) {
        $cdir       = scandir($dir);
        $skip_files = array('.htaccess');
        foreach ($cdir as $v) {
            if (!in_array($v, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $v)) {
                    $this->ReadDirToArray($dir . DIRECTORY_SEPARATOR . $v);
                } else {
                    if (!in_array($v, $skip_files)) {
                        $dir              = str_replace($this->_sRootDir, '', $dir);
                        $this->_wImages[] = str_replace('//', '/', $dir) . DIRECTORY_SEPARATOR . $v;
                    }
                }
            }
        }

    }

    /**
     * @return void
     */
    private function DoOptimise() {
        if (count($this->_wImages) > 0) {
            foreach ($this->_wImages as $v) {
                $use_temp   = explode('/', $v);
                $_sFileName = end($use_temp);
                $_sFileDir  = str_replace($_sFileName, '', $v);

                if (file_exists($_sFileDir . $_sFileName)) {
                    $_Imageimg = new Imagick($_sFileDir . $_sFileName);

                    $img_info = array(
                        'source'                     => $this->_sRootDir . $_sFileDir . $_sFileName,
                        'source compression quality' => $_Imageimg->getImageCompressionQuality(),
                        'source file size'           => $this->byteFormat($_Imageimg->getImageLength(), 'KB'),
                        'source file width'          => $_Imageimg->getImageWidth(),
                        'source file height'         => $_Imageimg->getImageHeight()
                    );

                    $_iImageheight = $_Imageimg->getImageHeight();
                    $_iImagewidth  = $_Imageimg->getImageWidth();

                    if (($_iImagewidth > $this->_ImgTargetWidth) && ($_iImageheight > $this->_ImgTargetHeight)) {
                        $_Imageimg->resizeImage($this->_ImgTargetWidth, $this->_ImgTargetHeight, null, 1);
                    }

                    $_Imageimg->setImageCompression(imagick::COMPRESSION_JPEG);
                    $_Imageimg->setImageCompressionQuality($this->_ImgTargetQuality);

                    $_Imageimg->stripImage();

                    if ($this->_bTestmode) {
                        $_sDestinationFileTemp    = explode('.', $_sFileName);
                        $_sDestinationFileTemp[0] = $_sDestinationFileTemp[0] . '-test';
                        $_sDestinationFile        = $this->_sRootDir . $_sFileDir . implode('.', $_sDestinationFileTemp);
                    } else {
                        $_sDestinationFile = $this->_sRootDir . $_sFileDir . $_sFileName;
                    }

                    if ($_Imageimg->writeimage($_sDestinationFile)) {
                        $_ImageimgCreated                       = new Imagick($_sDestinationFile);
                        $img_info['target']                     = $_sDestinationFile;
                        $img_info['target compression quality'] = $_ImageimgCreated->getImageCompressionQuality();
                        $img_info['target file size']           = $this->byteFormat($_ImageimgCreated->getImageLength(), 'KB');
                        $img_info['target file width']          = $_ImageimgCreated->getImageWidth();
                        $img_info['target file height']         = $_ImageimgCreated->getImageHeight();
                        $img_info['imagick savings kb']         = $this->byteFormat($img_info['source file size'] - $img_info['target file size'], 'KB');
                        $img_info['imagick savings perc']       = round((($img_info['source file size'] - $img_info['target file size']) / $img_info['source file size'] * 100), 2) . '%';
                        $_ImageimgCreated->destroy();
                    }

                    $_Imageimg->destroy();
                    $this->_ImageOptInfo[] = $img_info;
                }

            }
        }
    }

    /**
     *
     * @return void
     */
    private function SmushImages() {
        if (count($this->_wImages) > 0) {
            $iC = 0;
            foreach ($this->_wImages as $v) {
                $use_temp   = explode('/', $v);
                $_sFileName = end($use_temp);
                $_sFileDir  = str_replace($_sFileName, '', $v);

                if ($this->_bTestmode) {
                    $_sDestinationFileTemp    = explode('.', $_sFileName);
                    $_sDestinationFileTemp[0] = $_sDestinationFileTemp[0] . '-test';
                    $_sDestinationSmush       = $_sFileDir . implode('.', $_sDestinationFileTemp);
                } else {
                    $_sDestinationSmush = $_sFileDir . $_sFileName;
                }

                $_SourceSmush = $_sFileDir . $_sFileName;

                if (!$this->_oSmush->smush($_SourceSmush, $_sDestinationSmush)) {
                    $this->ImageOptInfo[] = 'image ' . $_SourceSmush . ' can not be smushed<br/>';
                } else {
                    $this->_ImageOptInfo[$iC]['smush savings kb']   = $this->_oSmush->savings . ' KB';
                    $this->_ImageOptInfo[$iC]['smush savings perc'] = $this->_oSmush->savings_perc . '%';
                }
                $iC++;
                $this->ReadAndDeleteFirstLine($this->_sWork_file);
                $this->_sProcessedImages++;
            }
        }
    }

    /**
     * @param string $filename
     * @return mixed
     */
    private function ReadAndDeleteFirstLine($filename = '') {
        $file   = file($filename);
        $output = $file[0];
        unset($file[0]);
        file_put_contents($filename, $file);
        return $output;
    }

    /**
     * @param        $bytes
     * @param string $unit
     * @param int    $decimals
     * @return string
     */
    private function byteFormat($bytes, $unit = "", $decimals = 2) {
        $units = array('B'  => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4,
                       'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);

        $value = 0;
        if ($bytes > 0) {
            // Generate automatic prefix by bytes
            // If wrong prefix given
            if (!array_key_exists($unit, $units)) {
                $pow  = floor(log($bytes) / log(1024));
                $unit = array_search($pow, $units);
            }

            // Calculate byte value by prefix
            $value = ($bytes / pow(1024, floor($units[$unit])));
        }

        // If decimals is not numeric or decimals is less than 0
        // then set default value
        if (!is_numeric($decimals) || $decimals < 0) {
            $decimals = 2;
        }

        // Format output
        return sprintf('%.' . $decimals . 'f ' . $unit, $value);
    }
}

class Digitalbits_Imageoptimiser_Model_Observer {


    public function cronOptimiseImages() {
        $_oImageopt = new DBImageOpt();

        //set required options for class
        $_oImageopt->setMediaDir('media/');
        $_oImageopt->setImageTargetHeight(Mage::getStoreConfig('imageopt/general/img_height'));
        $_oImageopt->setImageTargetWidth(Mage::getStoreConfig('imageopt/general/img_width'));
        $_oImageopt->setImageTargetQuality(Mage::getStoreConfig('imageopt/general/img_qual'));
        $_oImageopt->setImageInfo(false);
        $_oImageopt->setTestMode(false);
        $_oImageopt->setBaseUrl(str_replace('/index.php', '', Mage::getBaseUrl()));
        $_oImageopt->setRootDir(Mage::getBaseDir() . '/');
        $_oImageopt->setProcessDir('/var/log/');

        //run the script
        //print $_oImageopt->OptimiseImages() . ' images processed';
        //die('arrow to the knee');
        return $this;
    }
}
