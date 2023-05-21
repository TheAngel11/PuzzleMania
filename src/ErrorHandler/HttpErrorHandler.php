<?php
declare(strict_types=1);

namespace Salle\PuzzleMania\ErrorHandler;

use Salle\PuzzleMania\Repository\MySQLUserRepository;

class HttpErrorHandler
{
    private array $data;
    private MySQLUserRepository $userRepository;


    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function validateProfile():array
    {
        $formErrors = array();

        $filename = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $mime_type = mime_content_type($_FILES['file']['tmp_name']);

        $fileExtension = substr($mime_type, strpos($mime_type, '/') + 1);
        $fileDimensions = getimagesize($_FILES['file']['tmp_name']);    // [0] = width, [1] = height

        // Check file size
        if ($fileSize >= 1048576) {
            $formErrors['fileSize'] = 'The size of the image must be less than 1MB.';
        }

        // Check file extension
        if ($fileExtension != 'jpg' && $fileExtension != 'png') {
            $formErrors['fileExtension'] = 'Only png and jpg images are allowed.';
        }

        // Check file dimensions
        if ($fileDimensions[0] != 400 || $fileDimensions[1] != 400) {
            $formErrors['fileDimensions'] = 'The image dimensions must be 400x400.';
        }
        return $formErrors;
    }

}