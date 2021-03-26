<?php

class ShowFolderDiff
{
    private $firstFile;

    private $secondFile;

    private $f1Array;

    private $f2Array;

    public $result;

    /**
     * @param string $firstFile
     * @param string $secondFile
     */
    public function __construct(string $firstFile, string $secondFile)
    {
        $this->firstFile = $firstFile;
        $this->secondFile = $secondFile;

        $this->f1Array = $this->SearchInFile($firstFile);
        $this->f2Array = $this->SearchInFile($secondFile);

        $rootLessArr1 = $this->HideRootFolder($this->firstFile, $this->f1Array);
        $rootLessArr2 = $this->HideRootFolder($this->secondFile, $this->f2Array);

        $getDiff1 = $this->GetDiff($rootLessArr1, $rootLessArr2);
        $getDiff2 = $this->GetDiff($rootLessArr2, $rootLessArr1);

        $this->ShowDiff($getDiff1, $this->f1Array);
        $this->ShowDiff($getDiff2, $this->f2Array);
    }

    /**
     * @param array $keys
     * @param array $array
     */
    private function ShowDiff(array $keys, array $array)
    {
        foreach($keys as $key)
        {
            echo $array[$key]."<br>";
        }
    }

    /**
     * @param string $path
     */
    private function SearchInFile(string $path)
    {
        $array = array();
        $content = scandir($path);

        $folders = array_filter(glob($path.'/*'), 'is_dir');

        if ($handle = opendir($path)) {

            while (false !== ($entry = readdir($handle))) {
        
                //ignor folders
                if ($entry != "." && $entry != ".." && $entry != "themes"
                    && $entry != "plugins" && $entry != "uploads" && $entry != "languages") {

                    array_push($array, $path."/".$entry);

                    if(is_dir($path."/".$entry))
                    {
                        if($entry != "themes" || $entry != "plugins" || $entry != "uploads") {
                            $newPath = $path."/".$entry;
                            $dummy = $this->SearchInFile($newPath);
    
                            foreach($dummy as $e)
                            {
                                array_push($array, $e);
                            }
                        }
                    }
        
                }
            }
        
            closedir($handle);
        }

        return $array;
    }

    /**
     * @param array $arr1
     * @param array $arr2
     */
    private function GetDiff(array $arr1, array $arr2)
    {
        $result = array();
        foreach($arr1 as $e)
        {
            if (!(in_array($e, $arr2))) {
                $key = array_search($e, $arr1);
                array_push($result, $key);
            }
        }
        return $result;
    }

    /**
     * @param string $root
     * @param array $array
     */
    private function HideRootFolder(string $root , array $array)
    {
        foreach($array as $e)
        {
            $key = array_search($e, $array);
            $replace = str_replace($root, "", $e);
            $array[$key] = $replace;
        }

        return $array;
    }

}

$diff = new ShowFolderDiff('./Designs', './Designs1');
