<?php

if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function wantedExtension_info()
{
    return array(
        "name"            => "Gesuch-Erweiterung",
        "description"    => "Fügt verschiedene Funktionen zur Verwaltung von Gesuchen hinzu",
        "author"        => "aheartforspinach",
        "authorsite"    => "https://github.com/aheartforspinach",
        "version"        => "1.0",
        "compatibility" => "18*"
    );
}

function wantedExtension_install()
{
    global $db;

    //Task
    $date = new DateTime(date("d.m.Y", strtotime('+1 day')));
    $wantedExtensionTask = array(
        'title' => 'WantedExtension Cleanup',
        'description' => 'A task to cleanup old thread in the table wantedExtensions',
        'file' => 'wantedExtension',
        'minute' => 0,
        'hour' => 0,
        'day' => '*',
        'month' => '*',
        'weekday' => '*',
        'nextrun' => $date->getTimestamp(),
        'logging' => 1,
        'locked' => 0
    );
    $db->insert_query('tasks', $wantedExtensionTask);

    if ($db->engine == 'mysql' || $db->engine == 'mysqli') {
        $db->query("CREATE TABLE `" . TABLE_PREFIX . "wantedExtension` (
        `tid` int(10) unsigned NOT NULL,
        `kind` VARCHAR(64),
        `age` VARCHAR(24),
        `avatar` VARCHAR(64),
        `link_SG` VARCHAR(124) DEFAULT NULL,
        `link_CSB` VARCHAR(124) DEFAULT NULL,
        `status` VARCHAR(64),
        PRIMARY KEY (`tid`)
        )" . $db->build_create_table_collation());
    }

    //Einstellungen 
    $setting_group = array(
        'name' => 'wantedExtension',
        'title' => 'Gesuch-Erweiterung',
        'description' => 'Einstellungen für das Gesuch-Erweiterung-Plugin',
        'isdefault' => 0
    );
    $gid = $db->insert_query("settinggroups", $setting_group);

    $setting_array = array(
        'wantedExtension_area' => array(
            'title' => 'Gesuchbereich',
            'description' => 'In welchem Bereich werden Gesuche gepostet? (Nicht das Oberforum auswählen, sondern die einzelnen Bereiche, wo das Plugin greifen soll)',
            'optionscode' => 'forumselect',
            'value' => '0', // Default
            'disporder' => 1
        ),
        'wantedExtension_kinds' => array(
            'title' => 'Gesucharten',
            'description' => 'Welche Arten von Gesuchen gibt es?',
            'optionscode' => 'text',
            'value' => 'Familie,Arbeit,xx', // Default
            'disporder' => 2
        ),
        'wantedExtension_free' => array(
            'title' => 'Style für "Frei"-Tag',
            'description' => 'Gib unten das CSS ein, welches den Tag stylen soll',
            'optionscode' => 'text',
            'value' => 'style="color:green;"', // Default
            'disporder' => 3
        ),
        'wantedExtension_reserved' => array(
            'title' => 'Style für "Reserviert"-Tag',
            'description' => 'Gib unten das CSS ein, welches den Tag stylen soll',
            'optionscode' => 'text',
            'value' => 'style="color:yellow;"', // Default
            'disporder' => 4
        ),
        'wantedExtension_halftaken' => array(
            'title' => 'Style für "Teilvergeben"-Tag',
            'description' => 'Gib unten das CSS ein, welches den Tag stylen soll',
            'optionscode' => 'text',
            'value' => 'style="color:red;"', // Default
            'disporder' => 5
        )
    );

    foreach ($setting_array as $name => $setting) {
        $setting['name'] = $name;
        $setting['gid'] = $gid;

        $db->insert_query('settings', $setting);
    }

    //Template misc_wantedExtension_changePrefix bauen
    $insert_array = array(
        'title'        => 'misc_wantedExtension_changePrefix',
        'template'    => $db->escape_string('<html>
        <head>
        <title>{$mybb->settings[\'bbname\']} - {$lang->wantedExtension_changePrefix_title}</title>
        {$headerinclude}
        </head>
        <body>
        {$header}
        <form action="misc.php?action=changePrefix" method="post">
        <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
        <tr>
        <td class="thead" colspan="2"><strong>{$lang->wantedExtension_changePrefix_title}</strong></td>
        </tr>
            <tr><td>
                {$lang->wantedExtension_changePrefix_text}</td><td>
                {$radiobuttons}
                </td>
                </tr>
        </table>
        <br />
        <div align="center"><input type="submit" class="button" name="submit" value="{$lang->wantedExtension_changePrefix_submit}" /></div>
        <input type="hidden" name="action" value="changePrefix" />
        <input type="hidden" name="tid" value="{$tid}" />
        </form>
        {$footer}
        </body>
        </html>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template misc_wantedExtension_team bauen
    $insert_array = array(
        'title'        => 'misc_wantedExtension_team',
        'template'    => $db->escape_string('<html>
        <head>
        <title>{$mybb->settings[\'bbname\']} - {$lang->wantedExtension_team_title}</title>
        {$headerinclude}
        </head>
        <body>
        {$header}
        <form action="misc.php?action=team" method="post">
        <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
        <tr>
        <td class="thead" colspan="2"><strong>{$lang->wantedExtension_team_title}</strong></td>
        </tr>
            <tr><td>
                {$lang->wantedExtension_team_text_sg}</td>
                <td>
                {$textfield_sg}
                </td>
                </tr>
            <tr><td>
                {$lang->wantedExtension_team_text_csb}</td>
                <td>
                {$textfield_csb}
                </td>
                </tr>
        </table>
        <br />
        <div align="center"><input type="submit" class="button" name="submit" value="{$lang->wantedExtension_team_submit}" /></div>
        <input type="hidden" name="action" value="team" />
            <input type="hidden" name="tid" value="{$tid}" />
        </form>
        {$footer}
        </body>
        </html>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template forumdisplay_thread_wantedExtension bauen
    $insert_array = array(
        'title'        => 'forumdisplay_thread_wantedExtension',
        'template'    => $db->escape_string('<br><b>{$lang->wantedExtension_forumdisplay_thread_kind}:</b> {$kind}<br>
        <b>{$lang->wantedExtension_forumdisplay_thread_age}:</b> {$age} | <b>{$lang->wantedExtension_forumdisplay_thread_ava}:</b> {$ava}<br>{$links}{$team}'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template forumdisplay_thread_wantedExtension_Prefix bauen
    $insert_array = array(
        'title'        => 'forumdisplay_thread_wantedExtension_Prefix',
        'template'    => $db->escape_string('<span {$css}>[{$status}]</span>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template forumdisplay_thread_wantedExtension_PrefixOwner bauen
    $insert_array = array(
        'title'        => 'forumdisplay_thread_wantedExtension_PrefixOwner',
        'template'    => $db->escape_string('<a href="misc.php?action=changePrefix&tid={$tid}" title="Status ändern"><span {$css}>[{$status}]</span></a>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template newthread_wantedExtension bauen
    $insert_array = array(
        'title'        => 'newthread_wantedExtension',
        'template'    => $db->escape_string('<tr>
        <td class="trow1" width="20%"><strong>{$lang->wantedExtension_kind}</strong></td>
            <td class="trow1"><span class="smalltext"><select name="wanted_kind">{$wanted_kind}</select></span></td>
        </tr>
        
        <tr>
        <td class="trow1" width="20%"><strong>{$lang->wantedExtension_age}</strong></td>
            <td class="trow1"><span class="smalltext"><input type="text" class="textbox" name="wanted_age" value="{$wanted_age}" /></span></td>
        </tr>
        
        <tr>
        <td class="trow1" width="20%"><strong>{$lang->wantedExtension_ava}</strong></td>
            <td class="trow1"><span class="smalltext"><input type="text" class="textbox" name="wanted_ava" value="{$wanted_ava}" /></span></td>
        </tr>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template misc_wantedExtension bauen
    $insert_array = array(
        'title'        => 'misc_wantedExtension',
        'template'    => $db->escape_string('<html>
        <head>
        <title>{$mybb->settings[\'bbname\']} - {$lang->wantedExtension_misc_title}</title>
        {$headerinclude}
        </head>
        <body>
        {$header}
        <table class="tborder" cellspacing="0" cellpadding="5" border="0">
        <tr>
        <td class="thead" colspan="5"><strong>{$lang->wantedExtension_misc_title}</strong></td>
        </tr>
            <tr><td class="tcat">
                {$lang->wantedExtension_misc_status}</td>
				<td class="tcat">
                {$lang->wantedExtension_misc_category}</td>
				<td class="tcat">
                {$lang->wantedExtension_misc_avatar}</td>
                <td class="tcat">
                {$lang->wantedExtension_misc_age}
                </td>
                <td class="tcat">
                {$lang->wantedExtension_misc_link}
                </td>
                </tr>
            {$wantedExtension_bit}
        </table>
        {$footer}
        </body>
        </html>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template misc_wantedExtension_bit bauen
    $insert_array = array(
        'title'        => 'misc_wantedExtension_bit',
        'template'    => $db->escape_string('<tr>
        <td class="trow1" align="center">{$status}</td>
        <td class="trow1" align="center">{$cat}</td>
           <td class="trow1" align="center">{$avatar}</td>
           <td class="trow1" align="center">{$age} Jahre</td>
           <td class="trow1" align="center">{$link}</td>
       </tr>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template showthread_wantedExtension bauen
    $insert_array = array(
        'title'        => 'showthread_wantedExtension',
        'template'    => $db->escape_string('<tr>
        <td>
<table cellpadding="5" cellspacing="5" width="100%">
<tr>
<td class="tcat" colspan="2">
{$lang->wantedExtension_showthread_info}
</td>
</tr>
<tr>
<td class="trow2" style="width: 20% !important;">
<strong>{$lang->wantedExtension_kind}</strong>
</td>
<td class="trow2">
{$kind}
</td>
</tr>
	<tr>
<td class="trow2" style="width: 20% !important;">
<strong>{$lang->wantedExtension_age}</strong>
</td>
<td class="trow2">
{$age}
</td>
</tr>
	<tr>
<td class="trow2" style="width: 20% !important;">
<strong>{$lang->wantedExtension_ava}</strong>
</td>
<td class="trow2">
{$ava}
</td>
</tr>
</table>
</td>
</tr>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    rebuild_settings();
}

function wantedExtension_is_installed()
{
    global $db;
    if ($db->table_exists('wantedExtension')) {
        return true;
    }
    return false;
}

function wantedExtension_uninstall()
{
    global $db, $cache;
    $db->delete_query('settings', "name LIKE '%wantedExtension%'");
    $db->delete_query('settinggroups', "name = 'wantedExtension'");
    $db->delete_query("templates", "title LIKE '%wantedExtension%'");

    if ($db->table_exists('wantedExtension')) {
        $db->drop_table('wantedExtension');
    }

    rebuild_settings();
}

function wantedExtension_activate()
{
    global $db, $mybb;
    include MYBB_ROOT . "/inc/adminfunctions_templates.php";
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('<div class="author smalltext">') . "#i", '{$wantedExtension} <div class="author smalltext">');
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('{$gotounread}') . "#i", '{$wantedExtensionPrefix} {$gotounread}');
    find_replace_templatesets("newthread", "#" . preg_quote('{$posticons}') . "#i", '{$posticons} {$wantedExtension}');
    find_replace_templatesets("editpost", "#" . preg_quote('{$posticons}') . "#i", '{$posticons} {$wantedExtension}');
    find_replace_templatesets("showthread", "#" . preg_quote('<tr><td id="posts_container">') . "#i", '{$wantedExtension}<tr><td id="posts_container">');
    find_replace_templatesets("editpost", "#" . preg_quote('{$thread[\'subject\']}') . "#i", '{$wantedExtensionPrefix} {$thread[\'subject\']}');
}

function wantedExtension_deactivate()
{
    global $db, $mybb;
    include MYBB_ROOT . "/inc/adminfunctions_templates.php";
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('{$wantedExtension}') . "#i", '', 0);
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('{$wantedExtensionPrefix}') . "#i", '', 0);
    find_replace_templatesets("newthread", "#" . preg_quote('{$wantedExtension}') . "#i", '', 0);
    find_replace_templatesets("editpost", "#" . preg_quote('{$wantedExtension}') . "#i", '', 0);
    find_replace_templatesets("showthread", "#" . preg_quote('{$wantedExtension}') . "#i", '', 0);
    find_replace_templatesets("showthread", "#" . preg_quote('{$wantedExtensionPrefix}') . "#i", '', 0);
}

$plugins->add_hook('forumdisplay_thread', 'wantedExtension_forumdisplay_thread');
function wantedExtension_forumdisplay_thread()
{
    global $mybb, $templates, $thread, $lang, $db, $wantedExtension, $wantedExtensionPrefix, $thread, $fid;
    $lang->load('wantedExtension');
    $areas = explode(',', $mybb->settings['wantedExtension_area']);
    $tid = $thread['tid'];

    foreach ($areas as $area) {
        if ($area == $fid) {
            //normale Infos
            $informations = $db->fetch_array($db->simple_select('wantedExtension', '*', 'tid = ' . $tid));
            $kind = $informations['kind'];
            $age = $informations['age'];
            $ava = $informations['avatar'];

            if ($informations['status'] == $lang->wantedExtension_free) {
                $css = $mybb->settings['wantedExtension_free'];
                $status = $lang->wantedExtension_free;
            } elseif ($informations['status'] == $lang->wantedExtension_reserved) {
                $css = $mybb->settings['wantedExtension_reserved'];
                $status = $lang->wantedExtension_reserved;
            } else {
                $css = $mybb->settings['wantedExtension_halftaken'];
                $status = $lang->wantedExtension_halftaken;
            }

            if ($thread['uid'] == $mybb->user['uid'] || $thread['uid'] == $mybb->user['as_uid']) {
                eval("\$wantedExtensionPrefix = \"" . $templates->get("forumdisplay_thread_wantedExtension_PrefixOwner") . "\";");
            } else {
                eval("\$wantedExtensionPrefix = \"" . $templates->get("forumdisplay_thread_wantedExtension_Prefix") . "\";");
            }

            //links
            if (!$informations['link_SG'] == '' && !$informations['link_CSB'] == '') {
                $links = '<a href="' . $informations['link_SG'] . '" target="_blank">SG</a> | <a href="' . $informations['link_CSB'] . '" target="_blank">CSB</a><br>';
            } elseif (!$informations['link_SG'] == '') {
                $links = '<a href="' . $informations['link_SG'] . '" target="_blank">SG</a><br>';
            } elseif (!$informations['link_CSB'] == '') {
                $links = '<a href="' . $informations['link_CSB'] . '" target="_blank">CSB</a><br>';
            } else {
                $links = '';
            }

            //teamies
            if ($mybb->usergroup['canmodcp'] == 1) {
                $team = '<a href="misc.php?action=team&tid=' . $tid . '" title="Links hinzufügen"><i class="fas fa-plus"></i> SG & CSB</a>';
            } else {
                $team = '';
            }

            eval("\$wantedExtension = \"" . $templates->get("forumdisplay_thread_wantedExtension") . "\";");
        }
    }
}

$plugins->add_hook('showthread_start', 'wantedExtension_showthread_start');
function wantedExtension_showthread_start()
{
    global $db, $lang, $thread, $templates, $wantedExtension, $wantedExtensionPrefix, $mybb;
    $lang->load('wantedExtension');

    $infos = $db->fetch_array($db->simple_select('wantedExtension', '*', 'tid = ' . $thread['tid']));
    $kind = $infos['kind'];
    $age = $infos['age'];
    $ava = $infos['avatar'];
    if ($infos['status'] == $lang->wantedExtension_free) {
        $css = $mybb->settings['wantedExtension_free'];
        $status = $lang->wantedExtension_free;
    } elseif ($infos['status'] == $lang->wantedExtension_reserved) {
        $css = $mybb->settings['wantedExtension_reserved'];
        $status = $lang->wantedExtension_reserved;
    } else {
        $css = $mybb->settings['wantedExtension_halftaken'];
        $status = $lang->wantedExtension_halftaken;
    }
    $wantedExtensionPrefix = '<span ' . $css . '>[' . $status . ']</span> ';
    eval("\$wantedExtension = \"" . $templates->get("showthread_wantedExtension") . "\";");
}

$plugins->add_hook('newthread_start', 'wantedExtension_newthread_start');
function wantedExtension_newthread_start()
{
    global $templates, $mybb, $lang, $wantedExtension, $fid;
    $areas = explode(',', $mybb->settings['wantedExtension_area']);
    $lang->load('wantedExtension');

    foreach ($areas as $area) {
        if ($area == $fid) {
            $kinds = explode(',', $mybb->settings['wantedExtension_kinds']);
            $wanted_kind = '';
            foreach ($kinds as $kind) {
                $wanted_kind .= '<option value="' . $kind . '">' . $kind . '</option>';
            }

            eval("\$wantedExtension = \"" . $templates->get("newthread_wantedExtension") . "\";");
        }
    }
}

$plugins->add_hook('newthread_do_newthread_end', 'wantedExtension_do_newthread');
function wantedExtension_do_newthread()
{
    global $mybb, $db, $fid, $tid, $lang;
    $areas = explode(',', $mybb->settings['wantedExtension_area']);
    $lang->load('wantedExtension');

    foreach ($areas as $area) {
        if ($area == $fid) {
            $insert = array(
                'tid' => $tid,
                'kind' => $mybb->input['wanted_kind'],
                'age' => $db->escape_string($mybb->input['wanted_age']),
                'avatar' => $db->escape_string($mybb->input['wanted_ava']),
                'status' => $lang->wantedExtension_free,
            );
            $db->insert_query('wantedExtension', $insert);
        }
    }
}

$plugins->add_hook('editpost_end', 'wantedExtension_editpost');
function wantedExtension_editpost()
{
    global $templates, $mybb, $lang, $wantedExtension, $forum, $db, $tid;
    $areas = explode(',', $mybb->settings['wantedExtension_area']);
    $lang->load('wantedExtension');

    foreach ($areas as $area) {
        if ($area == $forum['fid']) {
            $kinds = explode(',', $mybb->settings['wantedExtension_kinds']);
            $wanted_kind = '';
            $informations = $db->fetch_array($db->simple_select('wantedExtension', '*', 'tid = ' . $tid));
            foreach ($kinds as $kind) {
                if ($informations['kind'] == $kind) {
                    $wanted_kind .= '<option value="' . $kind . '" selected>' . $kind . '</option>';
                } else {
                    $wanted_kind .= '<option value="' . $kind . '">' . $kind . '</option>';
                }
                $wanted_age = $informations['age'];
                $wanted_ava = $informations['avatar'];
            }

            eval("\$wantedExtension = \"" . $templates->get("newthread_wantedExtension") . "\";");
        }
    }
}

$plugins->add_hook('editpost_do_editpost_end', 'wantedExtension_do_editpost');
function wantedExtension_do_editpost()
{
    global $mybb, $db, $forum, $tid, $lang;
    $areas = explode(',', $mybb->settings['wantedExtension_area']);
    $lang->load('wantedExtension');

    foreach ($areas as $area) {
        if ($area == $forum['fid']) {
            $update = array(
                'kind' => $mybb->input['wanted_kind'],
                'age' => $db->escape_string($mybb->input['wanted_age']),
                'avatar' => $db->escape_string($mybb->input['wanted_ava']),
            );

            $insert = array(
                'tid' => $tid,
                'kind' => $mybb->input['wanted_kind'],
                'age' => $db->escape_string($mybb->input['wanted_age']),
                'avatar' => $db->escape_string($mybb->input['wanted_ava']),
                'status' => $lang->wantedExtension_free,
            );

            if ($db->fetch_array($db->simple_select('wantedExtension', 'tid',  'tid = ' . $tid))['tid'] != null) {
                $db->update_query('wantedExtension', $update, 'tid = ' . $tid);
            } else {
                $db->insert_query('wantedExtension', $insert);
            }
        }
    }
}

$plugins->add_hook('misc_start', 'wantedExtension_misc');
function wantedExtension_misc()
{
    global $mybb, $db, $lang, $templates, $headerinclude, $header, $footer;
    $lang->load('wantedExtension');

    $tid = $mybb->get_input('tid');
    if ($mybb->get_input('action') == 'changePrefix') {
        if ($tid == null)
            $tid = $_POST['tid'];
        $thread = get_thread($tid);
        if ($thread['uid'] != $mybb->user['uid']) {
            error_no_permission();
        }

        if ($_POST['action'] == 'changePrefix') {
            $update = array('status' => $db->escape_string($_POST['status']));
            $db->update_query('wantedExtension', $update, 'tid = ' . $thread['tid']);
            redirect('forumdisplay.php?fid=' . $thread['fid'], $lang->wantedExtension_redirect);
        }

        $wantedStatus = $db->fetch_array($db->simple_select('wantedExtension', 'status', 'tid = ' . $thread['tid']))['status'];
        $items = array($lang->wantedExtension_free, $lang->wantedExtension_reserved, $lang->wantedExtension_halftaken);
        foreach ($items as $item) {
            $checked = '';
            if ($wantedStatus == $item) {
                $radiobuttons .= '<input type="radio" id="' . $item . '" name="status" value="' . $item . '" checked="" ><label for="' . $item . '">' . $item . '</label><br>';
            } else {
                $radiobuttons .= '<input type="radio" id="' . $item . '" name="status" value="' . $item . '"><label for="' . $item . '">' . $item . '</label><br>';
            }
        }

        eval("\$page = \"" . $templates->get("misc_wantedExtension_changePrefix") . "\";");
        output_page($page);
    }

    if ($mybb->get_input('action') == 'team') {
        if (!($mybb->usergroup['canmodcp'] == 1)) {
            error_no_permission();
        }

        if ($_POST['action'] == 'team') {
            $thread = get_thread($tid);
            $update = array('link_SG' => $db->escape_string($_POST['sg']), 'link_CSB' => $db->escape_string($_POST['csb']));
            $db->update_query('wantedExtension', $update, 'tid = ' . $tid);
            redirect('forumdisplay.php?fid=' . $thread['fid'], $lang->wantedExtension_redirect);
        }

        $links = $db->fetch_array($db->simple_select('wantedExtension', 'link_SG, link_CSB', 'tid = ' . $tid));
        $sg = $links['link_SG'];
        $csb = $links['link_CSB'];

        $textfield_sg = '<input class="textbox" type="text" name="sg" style="width:600px;" value="' . $sg . '">';
        $textfield_csb = '<input class="textbox" type="text" name="csb" style="width:600px;" value="' . $csb . '">';

        eval("\$page = \"" . $templates->get("misc_wantedExtension_team") . "\";");
        output_page($page);
    }

    if ($mybb->get_input('action') == 'wantedExtension') {
        $wanted = $db->simple_select('wantedExtension', '*');

        while ($want = $db->fetch_array($wanted)) {
            $threadtitle = get_thread($want['tid'])['subject'];
            $status = $want['status'];
            $cat = $want['kind'];
            $avatar =  $want['avatar'];
            $age = $want['age'];
            $link = '<a href="showthread.php?tid=' . $want['tid'] . '" title="Zum Gesuch">' . $threadtitle . '</a>';
            eval("\$wantedExtension_bit .= \"" . $templates->get("misc_wantedExtension_bit") . "\";");
        }

        eval("\$page = \"" . $templates->get("misc_wantedExtension") . "\";");
        output_page($page);
    }
}
