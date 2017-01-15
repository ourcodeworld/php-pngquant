<?php 
/*
 * (c) Our Code World <dev@ourcodeworld.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ourcodeworld\PNGQuant;

class PNGQuant
{
    private $_binaryPath = "pngquant";

    private $_imagePath = "";

    private $_options = array();

    function __construct(){}

    function setBinaryPath($pathToBinary){
        $this->_binaryPath = escapeshellarg($pathToBinary);
        return $this;
    }

    function setOption($key, $value = null , $space = false, $isPath = false){
        $length_options = count($this->_options);

        // Overwrite an option if it was already set
        for($i = 0; $i < $length_options; ++$i){
            $existent_option = $this->_options[$i];

            if($key == $existent_option["key"]){
                array_splice($this->_options, $i, 1);
            }
        }
    
        array_push($this->_options, array(
            'key' => $key,
            'value' => $value,
            'space' => $space,
            'isPath' => $isPath
        ));

        return $this;
    }

    function buildCommand(){
        $_command = $this->_binaryPath. " ". $this->_imagePath;

        foreach($this->_options as $option){
            $key = $option['key'];
            $value = $option['value'];
            $space = $option['space'];
            $isPath = $option['isPath'];

            if(!$value){
                $_command .= " $key";
                continue;
            }
            
            if($isPath){
                $value = escapeshellarg($value);
            }
            
            if($space){
                $_command .= " $key $value";
            }else{
                $_command .= " $key=$value";
            }
        }

        return $_command;
    }

    /** END console utility wrapper **/

    /** Start wrapper **/

    function disableDithering(){
        return $this->setOption('--nofs');
    }

    function setImage($imagePath){
        $this->_imagePath = $imagePath;
        return $this;
    }

    function setCustomExtension($extension){
        return $this->setOption('--ext',$extension, true, true);
    }

    function setDitheringLevel($level){
        return $this->setOption('--floyd', $level);
    }

    function skipIfLarger(){
        return $this->setOption('--skip-if-larger');
    }

    function setQuality($min_quality = 60, $max_quality = 80){
        return $this->setOption('--quality', "$min_quality-$max_quality", true);
    }

    function setOutputImage($imagePath){
        return $this->setOption('--output', $imagePath, true, true);
    }

    function setSpeed($speed){
        return $this->setOption('--speed', $speed, true);
    }

    function overwriteExistingFile(){
        return $this->setOption('--force');
    }

    function posterize($value){
        return $this->setOption('--posterize', $value);
    }

    function removeMetadata(){
        return $this->setOption('--strip');    
    }

    function execute(){
        $consoleInstruction = $this->buildCommand();
        return shell_exec($consoleInstruction);
    }
}