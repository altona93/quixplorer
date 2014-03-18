<?php

Qx::useModule("Config");
require_once "_lib/smarty/Smarty.class.php";

class QxSmarty
{
    public static function init ()
    {
        // Set up smarty
        $smarty = new Smarty;
        QxSmarty::$_smarty = $smarty;

        $template_dir = Config::get('template_directory');
        QxLog::debug("setting template dir to " . $template_dir);

        // Smarty directories
        $smarty->template_dir = $template_dir;
        $smarty->compile_dir  = Config::get('compile_dir', Config::get('temp_directory')."/smarty/compile",   "smarty");
        $smarty->cache_dir    = Config::get('cache_dir',   Config::get('temp_directory')."/smarty/cache_dir", "smarty");
        $smarty->config_dir   = Config::get('config_dir', "_config",                                          "smarty");

        // Assign the homepage to smarty
        global $lang;
        $smarty->assign('themedir', $template_dir);
        $themeplugin_dir = "$template_dir/plugins";
        if (is_dir($themeplugin_dir))
        {
            QxLog::debug("adding $themeplugin_dir to smarty plugins dir");
            $smarty->addPluginsDir($themeplugin_dir);
        }
        $qx_plugin_dir = "_templates/plugins";
        if (is_dir($qx_plugin_dir))
        {
            QxLog::debug("adding $qx_plugin_dir to smarty plugins dir");
            $smarty->addPluginsDir($qx_plugin_dir);
        }
        $smarty->assign('logon_user', qx_user_s());

        // Register language translation filter
        $smarty->registerFilter("output", smarty_outputfilter_lang);
    }

    public function assign(string $variable, $value)
    {
        QxSmarty::$_smarty->assign($variable, $value);
    }

    public function display(string $pagename)
    {
        $smarty = QxSmarty::$_smarty;
        $smarty->assign('homepage', Config::get('homepage', "Quixplorer", "site"));
        $smarty->assign('site_name', Config::get('site_name', "Quixplorer Home", "site"));
        $smarty->display('list.tpl');
    }

    private static $_smarty;
}



