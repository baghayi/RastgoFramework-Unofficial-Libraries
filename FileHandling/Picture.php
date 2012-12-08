<?php
namespace root\library\FileHandling\Picture;

final class Picture extends \SplFileInfo
{
    private $storageDirectory = NULL;
    private $fileReference;
    private $pictureInfo;
    private $pictureName;


    public function setFileReference($fileReference)
    {
        $this->fileReference = $fileReference;
        parent::__construct($this->fileReference['tmp_name']);

        $this->pictureInfo = $this->getInfo();
        return;
    }

    private function getFileExtension()
    {
        return substr($this->fileReference['name'], strrpos($this->fileReference['name'], '.'), strlen($this->fileReference['name'])-1);
    }

    private function getInfo()
    {   
        if(!file_exists($this->fileReference['tmp_name']))
            return False;

        $pictureInfo = getImageSize($this->fileReference['tmp_name']);
        if($pictureInfo === false){
            return false; //It is not an image.
        }
        list($pictureRealWidth, $pictureRealHeight) = $pictureInfo;
        
        return array('width' => $pictureRealWidth, 'height' => $pictureRealHeight, 'mime' => $pictureInfo["mime"]);
    }

    public function checkMaxDimension($allowedMaxWidth, $allowedMaxHeight) 
    {
        $pictureInfo = $this->pictureInfo;
        if(!$pictureInfo)
            return False;

        if ($pictureInfo['width'] > $allowedMaxWidth or $pictureInfo['height'] > $allowedMaxHeight)
            return False;

        return true;
    }

    public function checkMinDimension($allowedMinWidth, $allowedMinHeight) 
    {
        $pictureInfo = $this->pictureInfo;
        if(!$pictureInfo)
            return False;

        if ($pictureInfo['width'] < $allowedMinWidth or $pictureInfo['height'] < $allowedMinHeight)
            return False;

        return true;
    }

    public function checkExactDimension($allowedExactWidth, $allowedExactHeight) 
    {
        $pictureInfo = $this->pictureInfo;
        if(!$pictureInfo)
            return False;

        if ($pictureInfo['width'] != $allowedExactWidth or $pictureInfo['height'] != $allowedExactHeight)
            return False;

        return true;
    }

    public function checkExtension($allowdExtention = array("image/gif", "image/jpeg", "image/pjpeg", "image/png"))
    {
        $pictureInfo = $this->pictureInfo;
        if(!$pictureInfo)
        return False; //It is not an image.

        if(!in_array($pictureInfo["mime"], $allowdExtention)){//depends on the result of getImageSize function.
            return false;//Is not an legal file.
        }
    
        return true;
    }

    public function isValid()
    {
        //picture cant be executable otherwise return false.
        if($this->isExecutable())
            return False;
        
        if(!$this->checkExtension())
            return False;
        
        if($this->checkExactDimension(0, 0))
            return false;#images ought to have a width and height set with them.

        return true;
    }

    public function setStorageDirectory($directoryAddress)
    {
        if(is_dir($directoryAddress))
            $this->storageDirectory = $directoryAddress;
        else
        {
            self::$registry->error->reportError('Storage Directory Does Not Exist.', __LINE__, __METHOD__, true);
            return False;
        }
        
        return;
    }

    public function save($pictureRandom = True, $overwrite = False)
    {
        if($this->storageDirectory === NULL){
            self::$registry->error->reportError('Storage Directory has not been set yet.', __LINE__, __METHOD__, true);
            return False;
        }


        if(!$this->isValid()){
            self::$registry->error->reportError('Uploading file is not a real picture!', __LINE__, __METHOD__, true);
            return False;
        }

        $this->pictureName = $this->getFilename();

        MakeRandomName:
        if ($pictureRandom)
        {
            $this->pictureName = md5($this->pictureName . time() . uniqid()) .$this->getFileExtension();
        }

        $fileFullAddress = $this->storageDirectory . DIRECTORY_SEPARATOR . $this->pictureName; 

        #upload if overwrite is true anyway, even if there is a file with the same name:
        #upload file if there is no any other file with the same name:

        if($overwrite or !file_exists($fileFullAddress))
            return $this->upload($fileFullAddress);


        #if file exists and file cant be overwritten then make a new random file name again:
        if (file_exists($fileFullAddress) and $pictureRandom and !$overwrite)
            goto MakeRandomName;

        #if file exists and it cannot be overwritten and file name can not be changed then throw Exception:
        if(file_exists($fileFullAddress) and !$pictureRandom and !$overwrite){
            self::$registry->error->reportError('While having {$pictureRandom} and {$overwrite} set FALSE, and a file with the same name does exist, It is impossible to upload a file with same name!', __LINE__, __METHOD__, true);
            return False;
        }

        return;
    }

    private function upload($fileFullAddress)
    {
        if(move_uploaded_file($this->getRealPath(), $fileFullAddress))
            return $this->pictureName;
        return False;
    }

}

// if(isset($_POST['ttt'])){
//     try{
//         $obj = new Picture;
//         // $obj -> setFileReference('E:/putty-0.62-installer.exe');
//         $obj -> setFileReference($_FILES['test']);
//         $obj->setStorageDirectory(FILE_PATH . 'public'.DS.'_file'.DS.'uploadsa');
//         var_dump($obj->save());
//         // var_dump($obj -> isValid());
//     }catch(ErrorHandler $e){

//     }
// }
