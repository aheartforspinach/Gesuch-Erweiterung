<?php

if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function wanted_info()
{
    return array(
        "name"            => "Gesuch-Erweiterung",
        "description"    => "Fügt verschiedene Funktionen zur Verwaltung von Gesuchen hinzu",
        "author"        => "aheartforspinach",
        "authorsite"    => "https://github.com/aheartforspinach",
        "version"        => "1.1",
        "compatibility" => "18*"
    );
}

function wanted_install()
{
    global $db;

    //Task
    $date = new DateTime(date("d.m.Y", strtotime('+1 day')));
    $wantedTask = array(
        'title' => 'Wanted Cleanup',
        'description' => 'A task to cleanup old thread in the table wanted',
        'file' => 'wanted',
        'minute' => 0,
        'hour' => 0,
        'day' => '*',
        'month' => '*',
        'weekday' => '*',
        'nextrun' => $date->getTimestamp(),
        'logging' => 1,
        'locked' => 0
    );
    $db->insert_query('tasks', $wantedTask);

    if ($db->engine == 'mysql' || $db->engine == 'mysqli') {
        $db->query("CREATE TABLE `" . TABLE_PREFIX . "wanted` (
        `tid` int(10) unsigned NOT NULL,
        `kind` VARCHAR(64),
        `age` VARCHAR(24),
        `avatar` VARCHAR(64),
        `sg` VARCHAR(10),
        `csb` VARCHAR(10),
        `status` VARCHAR(64),
        PRIMARY KEY (`tid`)
        )" . $db->build_create_table_collation());
    }

    //Einstellungen 
    $setting_group = array(
        'name' => 'wanted',
        'title' => 'Gesuch-Erweiterung',
        'description' => 'Einstellungen für das Gesuch-Plugin',
        'isdefault' => 0
    );
    $gid = $db->insert_query("settinggroups", $setting_group);

    $setting_array = array(
        'wanted_area' => array(
            'title' => 'Gesuchbereich',
            'description' => 'In welchem Bereich werden Gesuche gepostet? (Nicht das Oberforum auswählen, sondern die einzelnen Bereiche, wo das Plugin greifen soll)',
            'optionscode' => 'forumselect',
            'value' => '0', // Default
            'disporder' => 1
        ),
        'wanted_kinds' => array(
            'title' => 'Gesucharten',
            'description' => 'Welche Arten von Gesuchen gibt es?',
            'optionscode' => 'text',
            'value' => 'Familie,Arbeit,xx', // Default
            'disporder' => 2
        ),
        'wanted_free' => array(
            'title' => 'Style für "Frei"-Tag',
            'description' => 'Gib unten das CSS ein, welches den Tag stylen soll',
            'optionscode' => 'text',
            'value' => 'style="color:green;"', // Default
            'disporder' => 3
        ),
        'wanted_reserved' => array(
            'title' => 'Style für "Reserviert"-Tag',
            'description' => 'Gib unten das CSS ein, welches den Tag stylen soll',
            'optionscode' => 'text',
            'value' => 'style="color:yellow;"', // Default
            'disporder' => 4
        ),
        'wanted_halftaken' => array(
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

    // create templates
    $templategroup = array(
        "prefix" => 'wanted',
        "title" => $db->escape_string("Gesucherweiterung"),
    );

    $db->insert_query("templategroups", $templategroup);

    //Template misc_wanted_changePrefix bauen
    $insert_array = array(
        'title'        => 'wanted_misc_changePrefix',
        'template'    => $db->escape_string('<html>
        <head>
        <title>{$mybb->settings[\'bbname\']} - {$lang->wanted_changePrefix_title}</title>
        {$headerinclude}
        </head>
        <body>
        {$header}
        <form action="misc.php?action=changePrefix" method="post">
        <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
        <tr>
        <td class="thead" colspan="2"><strong>{$lang->wanted_changePrefix_title}</strong></td>
        </tr>
            <tr><td>
                {$lang->wanted_changePrefix_text}</td><td>
                {$radiobuttons}
                </td>
                </tr>
        </table>
        <br />
        <div align="center"><input type="submit" class="button" name="submit" value="{$lang->wanted_changePrefix_submit}" /></div>
        <input type="hidden" name="action" value="changePrefix" />
        <input type="hidden" name="tid" value="{$tid}" />
        </form>
        {$footer}
        </body>
        </html>'),
        'sid'        => '-2',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template misc_wanted_team bauen
    $insert_array = array(
        'title'        => 'wanted_misc_team',
        'template'    => $db->escape_string('<html>

<head>
    <title>{$mybb->settings[\'bbname\']} - {$lang->wanted_team_title}</title>
    {$headerinclude}
</head>

<body>
    {$header}
    <form action="misc.php?action=team" method="post">
        <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
            <tr>
                <td class="thead" colspan="2"><strong>{$lang->wanted_team_title}</strong></td>
            </tr>
            <tr>
                <td>
                    {$lang->wanted_team_text_sg}</td>
                <td>
                    <input class="textbox" type="text" name="sg" style="width:600px;" placeholder="dd.mm.yyyy"
                        value="{$sg}">
                </td>
            </tr>
            <tr>
                <td>
                    {$lang->wanted_team_text_csb}</td>
                <td>
                    <input class="textbox" type="text" name="csb" style="width:600px;" placeholder="dd.mm.yyyy"
                        value="{$csb}">
                </td>
            </tr>
        </table>
        <br />
        <div align="center"><input type="submit" class="button" name="submit" value="{$lang->wanted_team_submit}" />
        </div>
        <input type="hidden" name="action" value="team" />
        <input type="hidden" name="tid" value="{$tid}" />
    </form>
    {$footer}
</body>
</html>'),
        'sid'        => '-2',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template forumdisplay_thread_wanted bauen
    $insert_array = array(
        'title'        => 'wanted_forumdisplay_thread',
        'template'    => $db->escape_string('<b>{$lang->wanted_forumdisplay_thread_kind}:</b> {$kind}<br>
<b>{$lang->wanted_forumdisplay_thread_age}:</b> {$age} | <b>{$lang->wanted_forumdisplay_thread_ava}:</b>
{$ava}<br>{$links}{$team}'),
        'sid'        => '-2',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template forumdisplay_thread_wanted_Prefix bauen
    $insert_array = array(
        'title'        => 'wanted_forumdisplay_thread_prefix',
        'template'    => $db->escape_string('<span {$css}>[{$status}]</span>'),
        'sid'        => '-2',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template forumdisplay_thread_wanted_PrefixOwner bauen
    $insert_array = array(
        'title'        => 'wanted_forumdisplay_thread_prefixOwner',
        'template'    => $db->escape_string('<a href="misc.php?action=changePrefix&tid={$tid}" title="Status ändern"><span {$css}>[{$status}]</span></a>'),
        'sid'        => '-2',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template newthread_wanted bauen
    $insert_array = array(
        'title'        => 'wanted_newthread',
        'template'    => $db->escape_string('<tr>
    <td class="trow1" width="20%"><strong>{$lang->wanted_status}</strong></td>
    <td class="trow1"><span class="smalltext"><select name="wanted_status">{$wanted_status}</select></span></td>
</tr>
<tr>
    <td class="trow1" width="20%"><strong>{$lang->wanted_kind}</strong></td>
    <td class="trow1"><span class="smalltext"><select name="wanted_kind">{$wanted_kind}</select></span></td>
</tr>

<tr>
    <td class="trow1" width="20%"><strong>{$lang->wanted_age}</strong></td>
    <td class="trow1"><span class="smalltext"><input type="text" class="textbox" name="wanted_age" value="{$wanted_age}" /></span></td>
</tr>

<tr>
    <td class="trow1" width="20%"><strong>{$lang->wanted_ava}</strong></td>
    <td class="trow1"><span class="smalltext"><input type="text" class="textbox" name="wanted_ava" value="{$wanted_ava}" /></span></td>
</tr>'),
        'sid'        => '-2',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template misc_wanted bauen
    $insert_array = array(
        'title'        => 'wanted_misc',
        'template'    => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->wanted_misc_title}</title>
{$headerinclude}
</head>
<body>
{$header}
<table class="tborder" cellspacing="0" cellpadding="5" border="0" style="text-align:center;">
<tr>
<td class="thead" colspan="5"><strong>{$lang->wanted_misc_title}</strong></td>
</tr>
    <tr><td class="tcat">
        {$lang->wanted_misc_status}</td>
        <td class="tcat">
        {$lang->wanted_misc_category}</td>
        <td class="tcat">
        {$lang->wanted_misc_avatar}</td>
        <td class="tcat">
        {$lang->wanted_misc_age}
        </td>
        <td class="tcat">
        {$lang->wanted_misc_link}
        </td>
        </tr>
    {$wanted_bit}
</table>
{$footer}
</body>
</html>'),
        'sid'        => '-2',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template misc_wanted_bit bauen
    $insert_array = array(
        'title'        => 'wanted_misc_bit',
        'template'    => $db->escape_string('<tr>
    <td class="trow1" align="center">{$status}</td>
    <td class="trow1" align="center">{$cat}</td>
    <td class="trow1" align="center">{$avatar}</td>
    <td class="trow1" align="center">{$age} Jahre</td>
    <td class="trow1" align="center">{$link}</td>
</tr>'),
        'sid'        => '-2',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //Template showthread_wanted bauen
    $insert_array = array(
        'title'        => 'wanted_showthread',
        'template'    => $db->escape_string('<tr>
<td>
    <table cellpadding="5" cellspacing="5" width="100%">
        <tr>
            <td class="tcat" colspan="2">
                {$lang->wanted_showthread_info}
            </td>
        </tr>
        <tr>
            <td class="trow2" style="width: 20% !important;">
                <strong>{$lang->wanted_kind}</strong>
            </td>
            <td class="trow2">
                {$kind}
            </td>
        </tr>
        <tr>
            <td class="trow2" style="width: 20% !important;">
                <strong>{$lang->wanted_age}</strong>
            </td>
            <td class="trow2">
                {$age}
            </td>
        </tr>
        <tr>
            <td class="trow2" style="width: 20% !important;">
                <strong>{$lang->wanted_ava}</strong>
            </td>
            <td class="trow2">
                {$ava}
            </td>
        </tr>
    </table>
</td>
</tr>'),
        'sid'        => '-2',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'wanted_export_button',
        'template'    => $db->escape_string('<a href="misc.php?action=wanted_export&tid={$tid}" title="{$lang->wanted_export_button_title}"><i class="fas fa-share"></i></a>'),
        'sid'        => '-2',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'wanted_shortfacts',
        'template'    => $db->escape_string('Deine Shortfacts'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'wanted_export_overview',
        'template'    => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->wanted_export_title}</title>
{$headerinclude}
</head>
<body>
{$header}
<div class="panel" id="panel">
    <div id="panel">$menu</div>
    <h1>{$lang->wanted_export_title}</h1>
    
    <div style="display:flex;justify-content:center;margin-bottom:20pt;">
        <button id="copyTextBtn">{$lang->wanted_export_copy_text}</button><br>
    </div>
    <div style="display:flex;justify-content:center;">
        <textarea id="copytextarea" style="width:90%; height:250pt;">{$wanted}</textarea>
    </div>
</div>
{$footer}
</body>
</html>

<script>
copyTextBtn = document.querySelector(\'#copyTextBtn\');
copyTextBtn.addEventListener(\'click\', function(event) {
let copyTextarea = document.querySelector(\'#copytextarea\');
copyTextarea.focus();
copyTextarea.select();
try {
    let successful = document.execCommand(\'copy\');
    let msg = successful ? \'successful\' : \'unsuccessful\';
    document.querySelector(\'#copyTextBtn\').innerHTML = \'Kopiert!\';
} catch(err) {
    console.error(\'Unable to copy\');
}
});
</script>'),
        'sid'        => '-2',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    rebuild_settings();
}

function wanted_is_installed()
{
    global $db;
    return $db->table_exists('wanted');
}

function wanted_uninstall()
{
    global $db;
    $db->delete_query("templategroups", 'prefix = "wanted"');
    $db->delete_query("templates", "title like 'wanted%'");
    $db->delete_query('settings', "name LIKE 'wanted_%'");
    $db->delete_query('settinggroups', "name = 'wanted'");
    if ($db->table_exists("wanted")) $db->drop_table("wanted");

    rebuild_settings();
}

function wanted_activate()
{
    global $db, $mybb, $lang;
    include MYBB_ROOT . "/inc/adminfunctions_templates.php";
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('<span class="author smalltext">') . "#i", '{$wanted} <span class="author smalltext">');
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('{$gotounread}') . "#i", '{$wantedPrefix} {$gotounread}');
    find_replace_templatesets("newthread", "#" . preg_quote('{$posticons}') . "#i", '{$posticons} {$wanted}');
    find_replace_templatesets("editpost", "#" . preg_quote('{$posticons}') . "#i", '{$posticons} {$wanted}');
    find_replace_templatesets("showthread", "#" . preg_quote('<tr><td id="posts_container">') . "#i", '{$wanted}<tr><td id="posts_container">');
    find_replace_templatesets("showthread", "#" . preg_quote('{$thread[\'displayprefix\']}') . "#i", '{$wantedPrefix}{$thread[\'displayprefix\']}');
    find_replace_templatesets("showthread", "#" . preg_quote('<div class="float_right">{$newreply}') . "#i", '<div class="float_right">{$shortfacts_wanted} {$newreply}');
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('{$thread[\'multipage\']}') . "#i", '{$thread[\'multipage\']} {$shortfacts_wanted}');
}

function wanted_deactivate()
{
    global $db, $mybb;
    include MYBB_ROOT . "/inc/adminfunctions_templates.php";
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('{$wanted}') . "#i", '', 0);
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('{$wantedPrefix}') . "#i", '', 0);
    find_replace_templatesets("newthread", "#" . preg_quote('{$wanted}') . "#i", '', 0);
    find_replace_templatesets("editpost", "#" . preg_quote('{$wanted}') . "#i", '', 0);
    find_replace_templatesets("showthread", "#" . preg_quote('{$wanted}') . "#i", '', 0);
    find_replace_templatesets("showthread", "#" . preg_quote('{$wantedPrefix}') . "#i", '', 0);
    find_replace_templatesets("showthread", "#" . preg_quote('{$shortfacts_wanted}') . "#i", '', 0);
    find_replace_templatesets("forumdisplay_thread", "#" . preg_quote('{$shortfacts_wanted}') . "#i", '', 0);
}

// 
// display information in forumdisplay
// 
$plugins->add_hook('forumdisplay_thread', 'wanted_forumdisplay_thread');
function wanted_forumdisplay_thread()
{
    global $mybb, $templates, $thread, $lang, $db, $wanted, $wantedPrefix, $shortfacts_wanted, $thread, $fid;
    $lang->load('wanted');
    $areas = explode(',', $mybb->settings['wanted_area']);
    $tid = $thread['tid'];

    // return when it isnt wanted area
    if (!in_array($fid, $areas)) return;

    $shortfacts_wanted = wanted_setButton($thread['tid']);

    // get information
    $information = $db->fetch_array($db->simple_select('wanted', '*', 'tid = ' . $tid));
    $kind = $information['kind'];
    $age = $information['age'];
    $ava = $information['avatar'];

    if ($information['status'] == $lang->wanted_free) {
        $css = $mybb->settings['wanted_free'];
        $status = $lang->wanted_free;
    } elseif ($information['status'] == $lang->wanted_reserved) {
        $css = $mybb->settings['wanted_reserved'];
        $status = $lang->wanted_reserved;
    } else {
        $css = $mybb->settings['wanted_halftaken'];
        $status = $lang->wanted_halftaken;
    }

    if (wanted_isAllowToEdit($thread['uid'])) {
        eval("\$wantedPrefix = \"" . $templates->get("wanted_forumdisplay_thread_prefixOwner") . "\";");
    } else {
        eval("\$wantedPrefix = \"" . $templates->get("wanted_forumdisplay_thread_prefix") . "\";");
    }

    // display if thread is posted in SG/CSB
    if (!$information['sg'] == '' && !$information['csb'] == '') {
        $links = 'SG seit: ' . $information['sg'] . ' | CSB seit: ' . $information['csb'] . '<br>';
    } elseif (!$information['sg'] == '') {
        $links = 'SG seit: ' . $information['sg'] . '<br>';
    } elseif (!$information['csb'] == '') {
        $links = 'CSB seit: ' . $information['csb'] . '<br>';
    } else {
        $links = '';
    }

    //teamies
    if ($mybb->usergroup['canmodcp'] == 1) {
        $team = '<a href="misc.php?action=team&tid=' . $tid . '" title="Links hinzufügen"><i class="fas fa-plus"></i> SG & CSB</a>';
    } else {
        $team = '';
    }

    eval("\$wanted = \"" . $templates->get("wanted_forumdisplay_thread") . "\";");
}

$plugins->add_hook('showthread_start', 'wanted_showthread_start');
function wanted_showthread_start()
{
    global $db, $lang, $thread, $templates, $wanted, $shortfacts_wanted, $wantedPrefix, $mybb, $fid, $tid;
    $lang->load('wanted');
    $areas = explode(',', $mybb->settings['wanted_area']);

    // return when it isnt wanted area
    if (!in_array($fid, $areas)) return;

    $shortfacts_wanted = wanted_setButton();

    $infos = $db->fetch_array($db->simple_select('wanted', '*', 'tid = ' . $tid));
    $kind = $infos['kind'];
    $age = $infos['age'];
    $ava = $infos['avatar'];
    if ($infos['status'] == $lang->wanted_free) {
        $css = $mybb->settings['wanted_free'];
        $status = $lang->wanted_free;
    } elseif ($infos['status'] == $lang->wanted_reserved) {
        $css = $mybb->settings['wanted_reserved'];
        $status = $lang->wanted_reserved;
    } else {
        $css = $mybb->settings['wanted_halftaken'];
        $status = $lang->wanted_halftaken;
    }
    $wantedPrefix = '<span ' . $css . '>[' . $status . ']</span> ';
    eval("\$wanted = \"" . $templates->get("wanted_showthread") . "\";");
}

// 
// show option (new thread)
// 
$plugins->add_hook('newthread_start', 'wanted_newthread_start');
function wanted_newthread_start()
{
    global $templates, $mybb, $lang, $wanted, $fid, $post_errors;
    $areas = explode(',', $mybb->settings['wanted_area']);
    $lang->load('wanted');

    // return when it isnt wanted area
    if (!in_array($fid, $areas)) return;

    $kinds = explode(',', $mybb->settings['wanted_kinds']);
    $stati = array($lang->wanted_free, $lang->wanted_reserved, $lang->wanted_halftaken);
    $wanted_kind = wanted_buildOptionForSelect($kinds);
    $wanted_status = wanted_buildOptionForSelect($stati);

    // previewing new thread?
    if (isset($mybb->input['previewpost']) || $post_errors) {
        $wanted_age = htmlspecialchars_uni($mybb->get_input('wanted_age'));
        $wanted_ava = htmlspecialchars_uni($mybb->get_input('wanted_ava'));
        $kindValue = htmlspecialchars_uni($mybb->get_input('wanted_kind'));
        $statusValue = htmlspecialchars_uni($mybb->get_input('wanted_status'));

        $wanted_kind = wanted_buildOptionForSelect($kinds, $kindValue);
        $wanted_status = wanted_buildOptionForSelect($stati, $statusValue);
    }

    eval("\$wanted = \"" . $templates->get("wanted_newthread") . "\";");
}

$plugins->add_hook('newthread_do_newthread_end', 'wanted_do_newthread');
function wanted_do_newthread()
{
    global $mybb, $db, $fid, $tid, $lang;
    $areas = explode(',', $mybb->settings['wanted_area']);
    $lang->load('wanted');

    // return when it isnt wanted area
    if (!in_array($fid, $areas)) return;

    $insert = array(
        'tid' => $tid,
        'kind' => $mybb->input['wanted_kind'],
        'age' => $db->escape_string($mybb->input['wanted_age']),
        'avatar' => $db->escape_string($mybb->input['wanted_ava']),
        'status' => $db->escape_string($mybb->input['wanted_status'])
    );
    $db->insert_query('wanted', $insert);
}

// 
// show option (edit post)
// 
$plugins->add_hook('editpost_end', 'wanted_editpost');
function wanted_editpost()
{
    global $templates, $mybb, $lang, $wanted, $forum, $db, $thread, $pid, $post_errors;
    $areas = explode(',', $mybb->settings['wanted_area']);
    $lang->load('wanted');

    // return when it isnt wanted area
    if (!in_array($forum['fid'], $areas)) return;

    // post isnt the first post in thread
    if ($thread['firstpost'] != $pid) return;

    $kinds = explode(',', $mybb->settings['wanted_kinds']);
    $stati = array($lang->wanted_free, $lang->wanted_reserved, $lang->wanted_halftaken);
    $information = $db->fetch_array($db->simple_select('wanted', '*', 'tid = ' . $thread['tid']));

    $wanted_kind = wanted_buildOptionForSelect($kinds, $information['kind']);
    $wanted_status = wanted_buildOptionForSelect($stati, $information['status']);
    $wanted_age = $information['age'];
    $wanted_ava = $information['avatar'];

    // previewing new thread?
    if (isset($mybb->input['previewpost']) || $post_errors) {
        $wanted_age = htmlspecialchars_uni($mybb->get_input('wanted_age'));
        $wanted_ava = htmlspecialchars_uni($mybb->get_input('wanted_ava'));
        $kindValue = htmlspecialchars_uni($mybb->get_input('wanted_kind'));
        $statusValue = htmlspecialchars_uni($mybb->get_input('wanted_status'));

        $wanted_kind = wanted_buildOptionForSelect($kinds, $kindValue);
        $wanted_status = wanted_buildOptionForSelect($stati, $statusValue);
    }

    eval("\$wanted = \"" . $templates->get("wanted_newthread") . "\";");
}

$plugins->add_hook('editpost_do_editpost_end', 'wanted_do_editpost');
function wanted_do_editpost()
{
    global $mybb, $db, $forum, $thread, $lang, $pid;
    $areas = explode(',', $mybb->settings['wanted_area']);
    $lang->load('wanted');

    // return when it isnt wanted area
    if (!in_array($forum['fid'], $areas)) return;

    // post isnt the first post in thread
    if ($thread['firstpost'] != $pid) return;

    $update = array(
        'kind' => $mybb->input['wanted_kind'],
        'age' => $db->escape_string($mybb->input['wanted_age']),
        'avatar' => $db->escape_string($mybb->input['wanted_ava']),
        'status' => $db->escape_string($mybb->input['wanted_status'])
    );

    $insert = array(
        'tid' => $thread['tid'],
        'kind' => $mybb->input['wanted_kind'],
        'age' => $db->escape_string($mybb->input['wanted_age']),
        'avatar' => $db->escape_string($mybb->input['wanted_ava']),
        'status' => $db->escape_string($mybb->input['wanted_status'])
    );

    if ($db->fetch_array($db->simple_select('wanted', 'tid',  'tid = ' . $thread['tid']))['tid'] != null) {
        $db->update_query('wanted', $update, 'tid = ' . $thread['tid']);
    } else {
        $db->insert_query('wanted', $insert);
    }
}

// 
// functions for misc
// 
$plugins->add_hook('misc_start', 'wanted_misc');
function wanted_misc()
{
    global $mybb, $db, $lang, $templates, $headerinclude, $header, $footer;
    $lang->load('wanted');

    $tid = $mybb->get_input('tid');
    if ($mybb->get_input('action') == 'changePrefix') {
        if ($tid == null) $tid = $_POST['tid'];
        $thread = get_thread($tid);
        if (!wanted_isAllowToEdit($thread['uid'])) error_no_permission();

        if ($_POST['action'] == 'changePrefix') {
            $update = array('status' => $db->escape_string($_POST['status']));
            $db->update_query('wanted', $update, 'tid = ' . $thread['tid']);
            redirect('forumdisplay.php?fid=' . $thread['fid'], $lang->wanted_redirect);
        }

        $wantedStatus = $db->fetch_array($db->simple_select('wanted', 'status', 'tid = ' . $thread['tid']))['status'];
        $items = array($lang->wanted_free, $lang->wanted_reserved, $lang->wanted_halftaken);
        foreach ($items as $item) {
            $checked = '';
            if ($wantedStatus == $item) {
                $radiobuttons .= '<input type="radio" id="' . $item . '" name="status" value="' . $item . '" checked="" ><label for="' . $item . '">' . $item . '</label><br>';
            } else {
                $radiobuttons .= '<input type="radio" id="' . $item . '" name="status" value="' . $item . '"><label for="' . $item . '">' . $item . '</label><br>';
            }
        }

        eval("\$page = \"" . $templates->get("wanted_misc_changePrefix") . "\";");
        output_page($page);
    }

    // 
    // insert dates (team function)
    // 
    if ($mybb->get_input('action') == 'team') {
        if (!($mybb->usergroup['canmodcp'] == 1)) error_no_permission();

        if ($_POST['action'] == 'team') {
            $thread = get_thread($tid);
            $update = array('sg' => $db->escape_string($_POST['sg']), 'csb' => $db->escape_string($_POST['csb']));
            $db->update_query('wanted', $update, 'tid = ' . $tid);
            redirect('forumdisplay.php?fid=' . $thread['fid'], $lang->wanted_redirect);
        }

        $dates = $db->fetch_array($db->simple_select('wanted', 'sg, csb', 'tid = ' . $tid));
        $sg = $dates['sg'];
        $csb = $dates['csb'];

        eval("\$page = \"" . $templates->get("wanted_misc_team") . "\";");
        output_page($page);
    }

    if ($mybb->get_input('action') == 'wanted') {
        $wanted = $db->simple_select('wanted', '*');

        while ($want = $db->fetch_array($wanted)) {
            $thread = get_thread($want['tid']);
            if ($thread['fid'] != 75 && $thread['visible'] == 1) {
                $threadtitle = $thread['subject'];
                $status = $want['status'];
                $cat = $want['kind'];
                $avatar =  $want['avatar'];
                $age = $want['age'];
                $link = '<a href="showthread.php?tid=' . $want['tid'] . '" title="Zum Gesuch">' . $threadtitle . '</a>';
                eval("\$wanted_bit .= \"" . $templates->get("wanted_misc_bit") . "\";");
            }
        }

        eval("\$page = \"" . $templates->get("wanted_misc") . "\";");
        output_page($page);
    }

    if ($mybb->get_input('action') == 'wanted_export') {
        if ($mybb->usergroup['canmodcp'] == 0) error_no_permission();

        $wanted = $db->fetch_array($db->simple_select('posts', 'message, tid, pid', 'tid = ' . $tid, array('order_by' => 'dateline', 'order_dir' => 'asc', 'limit' => 1)));
        $linkWanted = $mybb->settings['bburl'] . '/showthread.php?tid=' . $wanted['tid'] . '&pid=' . $wanted['pid'] . '#' . $wanted['pid'] . '';
        $linkForum = $mybb->settings['bburl'];
        $helper = eval($templates->render('wanted_shortfacts'));
        $shortfacts = substr($helper, 33, -31);
        $wanted = $shortfacts . PHP_EOL . $wanted['message'];

        eval("\$page = \"" . $templates->get('wanted_export_overview') . "\";");
        output_page($page);
    }
}

$plugins->add_hook('admin_config_settings_change_commit', 'wanted_admin_config_settings_change_commit');
function wanted_admin_config_settings_change_commit() {
    global $mybb, $db;

    if(!key_exists('wanted_area', $mybb->input['upsetting'])) return;

    $selectedOptions = $mybb->input['select']['wanted_area'];

    // insert all wanted threads in the table
    $query = $db->simple_select(
        'threads t join ' . TABLE_PREFIX . 'forums f on f.fid = t.fid',
        'tid',
        'find_in_set(f.fid, "' . implode(',', $selectedOptions) . '") and tid not in (select tid from ' . TABLE_PREFIX . 'wanted)'
    );
    while ($row = $db->fetch_array($query)) {
        $db->insert_query('wanted', array('tid' => $row['tid'], 'status' => 'Frei'));
    }
}

// helper
function wanted_isAllowToEdit($threadOwner)
{
    global $db, $mybb;
    $allUids = array();
    $main =  $mybb->user['as_uid'] == 0 ? $mybb->user['uid'] : $mybb->user['as_uid'];
    array_push($allUids, $main);
    $other_uids = $db->simple_select('users', 'uid', 'as_uid = ' . $main);
    while ($other = $db->fetch_array($other_uids)) array_push($allUids, $other['uid']);
    return in_array($threadOwner, $allUids);
}

function wanted_buildOptionForSelect($data, $selectedValue = null)
{
    $optionString = '';
    foreach ($data as $item) {
        $selected = $selectedValue == $item ? 'selected' : '';
        $optionString .= '<option value="' . $item . '" ' . $selected . '>' . $item . '</option>';
    }
    return $optionString;
}

function wanted_setButton()
{
    global $mybb, $templates, $thread;
    $wanted_area = explode(',', $mybb->settings['wanted_area']);
    $tid = $thread['tid'];

    if (in_array($thread['fid'], $wanted_area) && $mybb->usergroup['canmodcp'] == 1) {
        return eval($templates->render('wanted_export_button'));
    } else {
        return '';
    }
}
