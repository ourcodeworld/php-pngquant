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

    /**
     * The error table is based in the pngquant_error enum of the official library
     * @see https://github.com/pornel/pngquant/blob/8eebc5702df6901cff71062fc616c4441bfeb48f/rwpng.h#L47
     */
    private $_errorTable = array(
        "0" => "SUCCESS",
        "1" => "MISSING_ARGUMENT",
        "2" => "READ_ERROR",
        "4" => "INVALID_ARGUMENT",
        "15" => "NOT_OVERWRITING_ERROR",
        "16" => "CANT_WRITE_ERROR",
        "17" => "OUT_OF_MEMORY_ERROR",
        "18" => "WRONG_ARCHITECTURE", // Missing SSE
        "24" => "PNG_OUT_OF_MEMORY_ERROR",
        "25" => "LIBPNG_FATAL_ERROR",
        "26" => "WRONG_INPUT_COLOR_TYPE",
        "35" => "LIBPNG_INIT_ERROR",
        "98" => "TOO_LARGE_FILE",
        "99" => "TOO_LOW_QUALITY"
    );

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

    function getErrorTable(){
        return $this->_errorTable;
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

    /**
     * Run the built command
     */
    function execute(){
        $consoleInstruction = $this->buildCommand();
        $output = null;

        system($consoleInstruction, $output);

        return $output;
    }

    /**
     * Execute PNGQUANT with the providen commands and retrieve the generated image 
     * directly into a variable
     */
    function getRawOutput(){
        // Create a temporal file in the system
        $fileName = uniqid().'.png';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Set the output path the tmp file
        $this->setOutputImage($temp_file);

        $consoleInstruction = $this->buildCommand();
        
        $output = null;

        system($consoleInstruction, $output);
        
        return array(
            'statusCode' => $output,
            'tempFile' => $temp_file,
            'imageData' => file_get_contents($temp_file)
        );
    }
}
