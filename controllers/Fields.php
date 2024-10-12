<?php

namespace Gromit\Forms\Controllers;

use Backend\Behaviors\ReorderController;
use Backend\Classes\Controller;
use Backend\Facades\BackendMenu;
use Gromit\Forms\Classes\Permissions;
use Gromit\Forms\Models\Field;
use Gromit\Forms\Models\Form;
use Winter\Storm\Database\Builder;

/**
 * Class Fields
 */
class Fields extends Controller
{
    public $implement = [
        ReorderController::class
    ];

    public $reorderConfig = 'config_reorder.yaml';

    protected $requiredPermissions = [Permissions::EDIT_FORMS];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Gromit.Forms', 'forms', 'forms');
    }

    public function reorder(int $formId): void
    {
        $this->vars['form'] = Form::query()->find($formId);

        $this->asExtension(ReorderController::class)->reorder();
    }

    /**
     * @param Field|\Winter\Storm\Database\Builder $query
     */
    public function reorderExtendQuery(Builder $query): void
    {
        $query->where('form_id', $this->params[0]);
    }
}
