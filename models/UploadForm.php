<?php

namespace Gromit\Forms\Models;

use Winter\Storm\Database\Model;
use System\Models\File;

class UploadForm extends Model
{
    public $attachOne = [
        'file' => File::class
    ];
}
