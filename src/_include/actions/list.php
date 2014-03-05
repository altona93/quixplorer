<?php

require_once("./_include/permissions.php");
require_once("./_include/Summary.php");

function do_list_action($dir)
{
    _debug("do_list_action($dir)");

    $dir_f = path_f($dir);
    if (!down_home($dir_f))
        show_error(qx_msg_s("errors.opendir") . ": dir='$dir_f' [not under home]");

	$handle = @opendir($dir_f);
    _debug("listing directory '$dir_f");

	if ($handle === false)
        show_error(qx_msg_s("errors.opendir") . ": $dir_f [error opening directory]");

    global $qx_files;
    global $qx_totals;
    $qx_totals = new Summary();

	// Read directory
	while (($cfile = readdir($handle)) !== false)
    {
        if ($cfile === ".")
            continue;


		$cfile_f = get_abs_item($dir, $cfile);
        $qx_totals->add($cfile_f);
        $fattributes = array();
		$fattributes["type"] = @filetype($cfile_f);
		$fattributes["extension"] = pathinfo($cfile_f, PATHINFO_EXTENSION);
        $fattributes["name"] = $cfile;
        $fattributes["size"] = filesize($cfile_f);
	    $fattributes["modified"] = @filemtime($cfile_f);
	    $fattributes["modified_s"] = parse_file_date(@filemtime($cfile_f));
        $fattributes["permissions"]["text"] = get_file_perms($dir, $cfile);
        $fattributes["permissions_l"]["link"] = NULL;
        $fattributes["download_link"] = qx_link("download", "&selitems[]=" . path_r($cfile_f));
		if (!permissions_grant($dir, NULL, "change"))
            $fattributes["permissions_l"] = html_link(
                qx_link("chmod", "&file=$cfile_f"),
                $fattributes["permissions_l"],
                qx_msg_s("permlink"));
        if (get_is_dir($dir, $cfile))
        {
            // NOT NICE: type and extension management
            $fattributes["extension"] = "dir";
            $fattributes["link"] = qx_link("list", "&dir=" . path_r("$dir_f/$cfile"));
        }
        $qx_files[$cfile] = $fattributes;
	}
	closedir($handle);

    do_list_show();
}

function do_list_show()
{
    global $qx_files;
    global $qx_totals;
	global $smarty;
	$smarty->assign('files', $qx_files);
	$smarty->assign('totals', $qx_totals);
	$smarty->assign('buttons', _get_buttons(".."));
	qx_page('list');
}

function _get_buttons ($dir_up)
{
	$buttons = array
	(
		array
		(
			'id' => 'up',
			'link' => make_link("list", $dir_up, NULL),
			'enabled' => true,
			'alt' => $GLOBALS["messages"]["uplink"]
		),
		array
		(
			'id' => 'home',
			'link' => make_link("list", NULL, NULL),
			'enabled' => true,
			'alt' => $GLOBALS["messages"]["homelink"]
		),
		array
		(
			'id' => 'reload',
			'link' => 'javascript:location.reload()',
			'enabled' => true,
			'alt' => $GLOBALS["messages"]["reloadlink"]
		),
		array
		(
			'id' => 'search',
			'link' => make_link('search', $dir, NULL),
			'enabled' => true,
			'alt' => $GLOBALS["messages"]["searchlink"]
		),
		array ( 'id' => "separator" ),
		array
		(
			'id' => 'copy',
			"link" => "javascript:Copy();",
			'alt' => $GLOBALS["messages"]["copylink"],
			'enabled' => permissions_grant_all($dir, NULL, array("create", "read"))
		),
		array
		(
			'id' => 'move',
		 	'enabled' => permissions_grant($dir, NULL, "change"),
			'link' => "javascript:Move();",
			'alt' => $GLOBALS["messages"]["movelink"]
		),
		array
		(
			'id' => 'delete',
		 	'enabled' => permissions_grant($dir, NULL, "delete"),
			'link' => 'javascript:Delete();',
			'alt' => $GLOBALS["messages"]["dellink"],
		),
		array
		(
			'id' => 'upload',
		 	'enabled' => permissions_grant($dir, NULL, "create") && get_cfg_var("file_uploads"),
			'link' => make_link("upload", $dir, NULL),
			"alt" => $GLOBALS["messages"]["uploadlink"],
		),
		array
		(
			'id' => 'archive',
			'link' => "javascript:Archive();",
			'alt' => $GLOBALS["messages"]["comprlink"],
			'enabled' => permissions_grant_all($dir, NULL, array("create", "read"))
				&& ($GLOBALS["zip"] || $GLOBALS["tar"] || $GLOBALS["tgz"])
		)
	);


	// ADMIN & LOGOUT
	array_push($buttons,
			array ( 'id' => 'separator' ));
	array_push($buttons,
			array
			(
			 	'id' => 'admin',
				'link' => make_link("admin", $dir, NULL),
				"alt" => $GLOBALS["messages"]["adminlink"],
				'enabled' => permissions_grant(NULL, NULL, "admin")
						|| permissions_grant(NULL, NULL, "password"),
			));
	array_push($buttons,
			array
			(
			 	'id' => 'logout',
				'link' => make_link("logout", $dir, NULL),
				"alt" => $GLOBALS["messages"]["logoutlink"],
				'enabled' => qx_var("is_authenticated"),
			));

	// Create File / Dir
	if (permissions_grant($dir, NULL, "create"))
	{
		array_push($buttons,
				array
				(
				 	'id' => 'createdir',
					'link' => make_link("mkitem", $dir, NULL),
					"alt" => $GLOBALS["messages"]["createfile"],
					'enabled' => true,
				));
		array_push($buttons,
				array
				(
				 	'id' => 'createfile',
					'link' => make_link("mkitem", $dir, NULL),
					"alt" => $GLOBALS["messages"]["createdir"],
					'enabled' => true,
				));
	}

	return $buttons;
}
?>

