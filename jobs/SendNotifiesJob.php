<?php

namespace Gromit\Forms\Jobs;

use Exception;
use Gromit\Forms\Actions\Submissions\Notify;
use Gromit\Forms\Models\Submission;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\Log;

class SendNotifiesJob
{
    /**
     * @param \Illuminate\Contracts\Queue\Job $job
     * @param array                           $data [submission_id]
     */
    public function fire(Job $job, array $data): void
    {
        try {
            /** @var Submission $submission */
            $submission = Submission::query()->find($data['submission_id']);

            Notify::make()->execute($submission);
        } catch (Exception $exception) {
            Log::error($exception);
        }

        $job->delete();
    }
}
