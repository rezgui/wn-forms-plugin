<?php namespace Gromit\Forms\Components;

// use Auth; 
use Cms\Classes\ComponentBase;
use Gromit\Forms\Actions\Submissions\Create;
use Gromit\Forms\Models\Form;
use Gromit\Forms\Models\Settings;
use Illuminate\Support\Arr;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Exception\ValidationException;
use Throwable;

class Forms extends ComponentBase
{
    public function componentDetails(): array
    {
        return [
            'name'        => __('gromit.forms::lang.components.forms.details.name'),
            'description' => __('gromit.forms::lang.components.forms.details.description'),
        ];
    }

    public function defineProperties(): array
    {
        return [
            'formKey' => [
                'title'       => __('gromit.forms::lang.components.forms.properties.form_key.title'),
                'description' => __('gromit.forms::lang.components.forms.properties.form_key.description'),
                'type'        => 'dropdown',
            ],
        ];
    }

    public function onRun(): void
    {
        $this->addJs('https://www.google.com/recaptcha/api.js', [
            'async',
            'defer',
        ]);
    }

    /**
     * Lists all forms.
     *
     * @return array
     */
    public function getFormKeyOptions(): array
    {
        $notActive = ' (' . __('gromit.forms::lang.components.forms.form_is_not_active') . ')';

        return Form::all()
            ->mapWithKeys(static function (Form $form) use ($notActive) {
                $name = $form->name;

                if (!$form->is_active) {
                    $name .= $notActive;
                }

                return [$form->key => $name];
            })
            ->all();
    }

    /**
     * Site key for reCAPTCHA
     *
     * @return string
     */
    public function getReCaptchaSiteKey(): ?string
    {
        return Settings::getRecaptchaSiteKey();
    }

    /**
     * Returns form by key.
     *
     * @param string|null $formKey
     *
     * @return Form|null
     */
    public function getForm(?string $formKey = null): ?Form
    {
        $formKey = $formKey ?: $this->property('formKey');

        if (empty($formKey)) {
            throw new ApplicationException('formKey component property or $formKey param is required');
        }

        return Form::whereFormKey($formKey)->first();
    }

    /**
     * Submit form.
     *
     * @throws Throwable
     */
    public function onSubmit(): void
    {
        $formKey = post('form_key');

        if (empty($formKey)) {
            throw new ValidationException(['form_key' => __('gromit.forms::lang.components.forms.unknown_form')]);
        }

        $data = Arr::except(request()->all(), 'form_key');

        Create::make()->execute(
            $formKey,
            $data,
            $this->getRequestData(),
            $this->getUserData()
        );

        $this->page['form'] = $this->getForm($formKey);
    }

    private function getRequestData(): array
    {
        return [
            'ip'         => request()->getClientIp(),
            'user_agent' => request()->userAgent(),
            'cookie'     => request()->cookies->all()
        ];
    }

    /**
     * Returns logged in user data if present.
     *
     * @return array|null
     */
    private function getUserData(): ?array
    {
        if (class_exists('Winter\User\Plugin') === false) {
            return null;
        }

        /** @noinspection PhpUndefinedClassInspection */
        // $user = Auth::user();
        $user = null;

        if ($user === null) {
            return null;
        }

        return $user->toArray();
    }
}
