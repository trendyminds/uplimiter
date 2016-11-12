<?php
/**
 * Uplimiter plugin for Craft CMS
 *
 * Uplimiter Variable.php
 *
 * @author    TrendyMinds
 * @copyright Copyright (c) 2016 TrendyMinds
 * @link      http://trendyminds.com
 * @package   Uplimiter
 * @since     1.0.0
 */

namespace Craft;

class UplimiterVariable
{
    public function getUserGroups()
    {
        return craft()->userGroups->getAllGroups();
    }
}
