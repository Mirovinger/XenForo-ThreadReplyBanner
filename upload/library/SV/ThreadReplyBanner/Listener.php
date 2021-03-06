<?php

class SV_ThreadReplyBanner_Listener
{
    const AddonNameSpace = 'SV_ThreadReplyBanner';

    public static function install($existingAddOn, $addOnData)
    {
        $version = isset($existingAddOn['version_id']) ? $existingAddOn['version_id'] : 0;
        $db = XenForo_Application::getDb();

        $db->query("
            CREATE TABLE IF NOT EXISTS xf_thread_banner (
                thread_id INT UNSIGNED NOT NULL PRIMARY KEY,
                raw_text mediumtext
            ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
        ");

        SV_ThreadReplyBanner_Install::addColumn('xf_thread','has_banner','TINYINT NOT NULL DEFAULT 0');

        $db->query("
            insert ignore into xf_permission_entry (user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
                select distinct user_group_id, user_id, convert(permission_group_id using utf8), 'sv_replybanner_show', permission_value, permission_value_int
                from xf_permission_entry
                where permission_group_id = 'forum' and  permission_id in ('postReply')
        ");

        $db->query("
            insert ignore into xf_permission_entry (user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
                select distinct user_group_id, user_id, convert(permission_group_id using utf8), 'sv_replybanner_manage', permission_value, permission_value_int
                from xf_permission_entry
                where permission_group_id = 'forum' and permission_id in ('warn','editAnyPost','deleteAnyPost')
        ");

        return true;
    }

    public static function uninstall()
    {
        $db = XenForo_Application::get('db');

        $db->query("
            DROP TABLE IF EXISTS xf_thread_banner
        ");

        return true;
    }

    public static function load_class($class, array &$extend)
    {
        $extend[] = self::AddonNameSpace.'_'.$class;
    }
}