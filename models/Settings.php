<?php

namespace Gromit\Forms\Models;

use Winter\Storm\Database\Model;
use Winter\Storm\Database\Traits\Validation;
use stdClass;
use System\Behaviors\SettingsModel;
use System\Models\MailTemplate;

/**
 * Class Settings
 *
 * @method static mixed get(string $key, ?string $default = null)
 *
 * @property string $recaptcha_sitekey
 * @property string $recaptcha_secret
 * @property string $mail_template
 * @property array  $emails
 */
class Settings extends Model
{
    public $implement = [
        SettingsModel::class
    ];

    public $settingsCode = 'gromit_forms_settings';

    public $settingsFields = 'fields.yaml';

    use Validation;

    public $rules = [
        'emails.*.email' => 'required|email'
    ];

    public function filterFields($fields, ?string $context = null): void
    {
        if ($context === 'update') {
            if ($fields->use_queue) {
                $useQueueCommentText = __('gromit.forms::lang.models.settings.fields.use_queue.comment');
                $fields->use_queue->comment = "<span class='text-danger'>$useQueueCommentText</span>";
            }

            if ($fields->recaptcha_sitekey) {
                $recaptchaSiteKeyComment1 = __('gromit.forms::lang.models.settings.fields.recaptcha_sitekey.comment_1');
                $recaptchaSiteKeyComment2 = __('gromit.forms::lang.models.settings.fields.recaptcha_sitekey.comment_2');

                $fields->recaptcha_sitekey->comment = "{$recaptchaSiteKeyComment1}
    <a target='_blank' href='https://www.google.com/recaptcha/admin'>{$recaptchaSiteKeyComment2} </a>";
            }

            if ($fields->recaptcha_secret) {
                $recaptchaSecretComment1 = __('gromit.forms::lang.models.settings.fields.recaptcha_secret.comment_1');
                $recaptchaSecretComment2 = __('gromit.forms::lang.models.settings.fields.recaptcha_secret.comment_2');

                $fields->recaptcha_secret->comment = "{$recaptchaSecretComment1}
    <a target='_blank' href='https://www.google.com/recaptcha/admin'>{$recaptchaSecretComment2} </a>";
            }
        }
    }

    public function getMailTemplateOptions(): array
    {
        return MailTemplate::listAllTemplates();
    }

    public function initSettingsData(): void
    {
        $this->mail_template = 'gromit.forms::mail.notify';
    }

    public static function getRecaptchaSiteKey(): ?string
    {
        return self::get('recaptcha_sitekey');
    }

    public static function getRecaptchaSecretKey(): ?string
    {
        return self::get('recaptcha_secret');
    }

    public static function getMailTemplate(): ?string
    {
        return self::get('mail_template');
    }

    public static function getEmails(): array
    {
        if (empty(self::get('emails'))) {
            return [];
        }

        return collect(self::get('emails'))
            ->map(function ($email) {
                return trim($email['email']);
            })
            ->all();
    }

    public static function usesQueue(): bool
    {
        return (bool)self::get('use_queue');
    }
}
