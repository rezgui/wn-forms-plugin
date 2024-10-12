<?php namespace Gromit\Forms;

use Backend\Facades\Backend;
use Backend\Facades\BackendAuth;
use Gromit\Forms\Classes\Permissions;
use Gromit\Forms\Components\Forms;
use Gromit\Forms\Models\Settings;
use System\Classes\PluginBase;

/**
 * Forms Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'gromit.forms::lang.plugin.name',
            'description' => 'gromit.forms::lang.plugin.description',
            'author'      => 'GromIT',
            'icon'        => 'icon-address-card'
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents(): array
    {
        return [
            Forms::class => 'gromitForms',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions(): array
    {
        return Permissions::lists();
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation(): array
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var \Backend\Models\User $user */
        $user = BackendAuth::getUser();

        $canEditForms       = $user->hasAccess(Permissions::EDIT_FORMS);
        $canShowSubmissions = $user->hasAccess(Permissions::SHOW_SUBMISSIONS);

        if ($canEditForms) {
            return [
                'forms' => [
                    'label'       => 'gromit.forms::lang.plugin.menu.forms.label',
                    'icon'        => 'icon-address-card',
                    'url'         => Backend::url('gromit/forms/forms'),
                    'permissions' => [Permissions::EDIT_FORMS],
                ],
            ];
        }

        if ($canShowSubmissions) {
            return [
                'forms' => [
                    'label'       => 'gromit.forms::lang.plugin.menu.forms.label',
                    'icon'        => 'icon-address-card',
                    'url'         => Backend::url('gromit/forms/submissions'),
                    'permissions' => [Permissions::SHOW_SUBMISSIONS],
                ],
            ];
        }

        return [];
    }

    /**
     * @return array
     */
    public function registerSettings(): array
    {
        return [
            'settings' => [
                'label'       => 'gromit.forms::lang.plugin.menu.settings.label',
                'description' => 'gromit.forms::lang.plugin.menu.settings.description',
                'category'    => 'gromit.forms::lang.plugin.menu.settings.category',
                'icon'        => 'icon-cogs',
                'class'       => Settings::class,
                'order'       => 500,
                'keywords'    => 'forms',
                'permissions' => [Permissions::EDIT_FORMS]
            ],
        ];
    }

    public function registerMailTemplates(): array
    {
        return [
            'gromit.forms::mail.notify',
        ];
    }
}
