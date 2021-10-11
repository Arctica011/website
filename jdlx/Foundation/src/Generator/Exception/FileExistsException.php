<?php

namespace Jdlx\Generator\Exception;


use Throwable;

class FileExistsException extends \Exception
{
   public function __construct($file, $code = 0, Throwable $previous = null)
   {
       $message= "{$file} already exists. Set overwrite to true to overwrite the contents";
       parent::__construct($message, $code, $previous);
   }
}
