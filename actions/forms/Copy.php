<?php

namespace Gromit\Forms\Actions\Forms;

use Gromit\Forms\Models\Field;
use Gromit\Forms\Models\Form;
use Gromit\Forms\Traits\SelfMakeable;
use Illuminate\Support\Facades\DB;

class Copy
{
    use SelfMakeable;

    /**
     * Creates form duplicate.
     *
     * @param \Gromit\Forms\Models\Form $form
     * @param string                    $name
     * @param string                    $key
     *
     * @return \Gromit\Forms\Models\Form
     * @throws \Throwable
     */
    public function execute(Form $form, string $name, string $key): Form
    {
        return DB::transaction(function () use ($key, $name, $form) {
            $newForm                = new Form();
            $newForm->name          = $name;
            $newForm->key           = $key;
            $newForm->is_active     = $form->is_active;
            $newForm->description   = $form->description;
            $newForm->wrapper_class = $form->wrapper_class;
            $newForm->form_class    = $form->form_class;
            $newForm->success_title = $form->success_title;
            $newForm->success_msg   = $form->success_msg;
            $newForm->extra_emails  = $form->extra_emails;
            $newForm->mail_template = $form->mail_template;
            $newForm->save();

            foreach ($form->fields as $field) {
                $fieldCopy          = new Field();
                $fieldCopy->form_id = $newForm->id;
                $fieldCopy->fill($field->only([
                    'label',
                    'key',
                    'comment',
                    'type',
                    'default',
                    'is_required',
                    'required_message',
                    'wrapper_class',
                    'field_class',
                    'options',
                ]));
                $fieldCopy->save();
            }

            return $newForm;
        });
    }
}
