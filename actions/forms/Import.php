<?php

namespace Gromit\Forms\Actions\Forms;

use Gromit\Forms\Models\Field;
use Gromit\Forms\Models\Form;
use Gromit\Forms\Traits\SelfMakeable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Winter\Storm\Exception\ValidationException;
use Winter\Storm\Support\Arr;
use Winter\Storm\Support\Str;
use System\Models\File;

class Import
{
    use SelfMakeable;

    /**
     * Creates form from json created by Export action.
     *
     * @see \Gromit\Forms\Actions\Forms\Export
     *
     * @param \System\Models\File $file
     *
     * @return \Gromit\Forms\Models\Form
     * @throws \Winter\Storm\Exception\ValidationException
     * @throws \Throwable
     */
    public function execute(File $file): Form
    {
        $formConfig = $this->decodeFile($file);

        $this->validateFileContent($formConfig);

        return $this->importForm($formConfig);
    }

    /**
     * @noinspection PhpComposerExtensionStubsInspection
     */
    private function decodeFile(File $file): array
    {
        $formConfig = json_decode($file->getContents(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(
                'json_decode error: ' . json_last_error_msg()
            );
        }

        return $formConfig;
    }

    /**
     * @param $formConfig
     *
     * @throws \Winter\Storm\Exception\ValidationException
     */
    private function validateFileContent($formConfig): void
    {
        $isFormConfig = Arr::get($formConfig, '_meta.form_config');

        if (!$isFormConfig) {
            throw new ValidationException(['' => __('gromit.forms::lang.actions.forms.import.validate_file_content')]);
        }
    }

    /**
     * @param $formConfig
     *
     * @return \Gromit\Forms\Models\Form
     * @throws \Throwable
     */
    private function importForm($formConfig): Form
    {
        $formConfig = $formConfig['form'];

        return DB::transaction(function () use ($formConfig) {
            $alreadyExists = Form::query()
                ->where('key', $formConfig['key'])
                ->exists();

            if ($alreadyExists) {
                $formConfig['key'] .= '-' . Str::random();
            }

            $form = Form::create(Arr::except($formConfig, 'fields'));

            foreach ($formConfig['fields'] as $fieldConfig) {
                $fieldConfig['form_id'] = $form->id;

                Field::create($fieldConfig);
            }

            return $form;
        });
    }
}
