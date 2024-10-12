<?php

namespace Gromit\Forms\Actions\Submissions;

use Gromit\Forms\Models\Field;
use Gromit\Forms\Models\Form;
use Gromit\Forms\Models\Submission;
use Gromit\Forms\Traits\SelfMakeable;
use InvalidArgumentException;
use League\Csv\Writer;
use SplTempFileObject;
use System\Models\File;

class Export
{
    use SelfMakeable;

    /**
     * @param int $formId
     *
     * @return \League\Csv\Writer
     * @throws \League\Csv\CannotInsertRecord
     * @throws InvalidArgumentException
     */
    public function execute(int $formId): Writer
    {
        $form = Form::query()->find($formId);

        if ($form === null) {
            throw new InvalidArgumentException(__('gromit.forms::lang.messages.form_not_found'));
        }

        $submissions = Submission::whereFormId($formId)->get();

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $headersAdded = true;

        /** @var Submission $submission */
        foreach ($submissions as $submission) {
            $rowData = collect($submission->getSubmissionData());

            if ($submission->form->hasUploadField()) {
                $uploadFields = $submission->form->fields()->where('type', Field::TYPE_UPLOAD)->get();

                /** @var Field $uploadField */
                foreach ($uploadFields as $uploadField) {
                    $rowData[$uploadField->label] = $submission
                        ->uploaded_files
                        ->where('description', $uploadField->label)
                        ->map(function (File $file) {
                            return $file->getPath();
                        })
                        ->implode("\n");
                }
            }

            if ($headersAdded === false) {
                $csv->insertOne($rowData->keys()->all());
                $headersAdded = true;
            }

            $csv->insertOne($rowData->values()->all());
        }

        return $csv;
    }
}
