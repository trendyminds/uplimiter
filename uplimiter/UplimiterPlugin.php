<?php
/**
 * Uplimiter plugin for Craft CMS
 *
 * Allows you to define the maximum upload size per user group
 *
 * @author    TrendyMinds
 * @copyright Copyright (c) 2016 TrendyMinds
 * @link      http://trendyminds.com
 * @package   Uplimiter
 * @since     1.0.0
 */

namespace Craft;

class UplimiterPlugin extends BasePlugin
{
    /**
     * Called after the plugin class is instantiated; do any one-time initialization here such as hooks and events:
     *
     * craft()->on('entries.saveEntry', function(Event $event) {
     *    // ...
     * });
     *
     * or loading any third party Composer packages via:
     *
     * require_once __DIR__ . '/vendor/autoload.php';
     *
     * @return mixed
     */
    public function init()
    {
        if ( craft()->request->isCpRequest() && craft()->userSession->isLoggedIn() )
        {
            // Get the currently logged in user
            $userId = craft()->userSession->id;

            // Get the user groups the user belongs to
            $userGroups = craft()->userGroups->getGroupsByUserId($userId);

            // If the user belongs to a group, prepare to check their upload limit
            if (!empty($userGroups))
            {
                craft()->on('assets.onBeforeUploadAsset', function(Event $event) {
                    // Clear the file cache to get the correct file size of the attempted file
                    clearstatcache();

                    // Get the currently logged in user
                    $userId = craft()->userSession->id;

                    // Get the user groups the user belongs to
                    $userGroups = craft()->userGroups->getGroupsByUserId($userId);

                    // Create an array of the groups the user is in and the upload limits
                    $groups = array();

                    // Get the value for each user group in the plugin settings. If a user group does not have a value assigned to it, use the default maxUploadFileSize variable.
                    foreach($userGroups as $group) {
                        $groups[] = ($this->getSettings()['gid' . $group->id]) ? $this->getSettings()['gid' . $group->id] : craft()->config->get('maxUploadFileSize');
                    }

                    // Find the largest value of the groups this user belongs to
                    $maxGroupFileSize = max($groups);

                    // Get the size of the uploaded file
                    $getFileSize = filesize($event->params['path']);

                    // Determine if the user can upload a file of this size
                    if ($maxGroupFileSize < $getFileSize)
                    {
                        throw new Exception(Craft::t('The file you attempted to upload was too large. Please ensure your upload is smaller than ' . $maxGroupFileSize . ' bytes.'));
                    }
                });
            }
        }
    }

    /**
     * Returns the user-facing name.
     *
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Uplimiter');
    }

    /**
     * Plugins can have descriptions of themselves displayed on the Plugins page by adding a getDescription() method
     * on the primary plugin class:
     *
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Easily define the maximum file upload size per user group.');
    }

    /**
     * Plugins can have links to their documentation on the Plugins page by adding a getDocumentationUrl() method on
     * the primary plugin class:
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/trendyminds/uplimiter/blob/master/README.md';
    }

    /**
     * Plugins can now take part in Craft’s update notifications, and display release notes on the Updates page, by
     * providing a JSON feed that describes new releases, and adding a getReleaseFeedUrl() method on the primary
     * plugin class.
     *
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/trendyminds/uplimiter/master/releases.json';
    }

    /**
     * Returns the version number.
     *
     * @return string
     */
    public function getVersion()
    {
        return '1.0.1';
    }

    /**
     * As of Craft 2.5, Craft no longer takes the whole site down every time a plugin’s version number changes, in
     * case there are any new migrations that need to be run. Instead plugins must explicitly tell Craft that they
     * have new migrations by returning a new (higher) schema version number with a getSchemaVersion() method on
     * their primary plugin class:
     *
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.1';
    }

    /**
     * Returns the developer’s name.
     *
     * @return string
     */
    public function getDeveloper()
    {
        return 'TrendyMinds';
    }

    /**
     * Returns the developer’s website URL.
     *
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'http://trendyminds.com';
    }

    /**
     * Returns whether the plugin should get its own tab in the CP header.
     *
     * @return bool
     */
    public function hasCpSection()
    {
        return false;
    }

    /**
     * Called right before your plugin’s row gets stored in the plugins database table, and tables have been created
     * for it based on its records.
     */
    public function onBeforeInstall()
    {
    }

    /**
     * Called right after your plugin’s row has been stored in the plugins database table, and tables have been
     * created for it based on its records.
     */
    public function onAfterInstall()
    {
    }

    /**
     * Called right before your plugin’s record-based tables have been deleted, and its row in the plugins table
     * has been deleted.
     */
    public function onBeforeUninstall()
    {
    }

    /**
     * Called right after your plugin’s record-based tables have been deleted, and its row in the plugins table
     * has been deleted.
     */
    public function onAfterUninstall()
    {
    }

    /**
     * Defines the attributes that model your plugin’s available settings.
     *
     * @return array
     */
    protected function defineSettings()
    {

        $groupRow = [];
        $getUserGroups = craft()->userGroups->getAllGroups();

        foreach($getUserGroups as $group)
        {
            $groupRow['gid' . $group['id']] = array(AttributeType::String, 'label' => $group['name'], 'default' => '');
        }

        return $groupRow;
    }

    /**
     * Returns the HTML that displays your plugin’s settings.
     *
     * @return mixed
     */
    public function getSettingsHtml()
    {
       return craft()->templates->render('uplimiter/Uplimiter_Settings', array(
           'settings' => $this->getSettings()
       ));
    }

    /**
     * If you need to do any processing on your settings’ post data before they’re saved to the database, you can
     * do it with the prepSettings() method:
     *
     * @param mixed $settings  The Widget's settings
     *
     * @return mixed
     */
    public function prepSettings($settings)
    {
        // Modify $settings here...

        return $settings;
    }

}
