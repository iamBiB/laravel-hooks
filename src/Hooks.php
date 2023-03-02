<?php

namespace iAmBiB\Hooks;

use Cache;
use File;

\define('PLUGINS_FOLDER', config('hooks.plugins_folder') . '/');

class Hooks
{
    /**
     * DB Table.
     * @var string
     */
    protected $plugins_table;

    /**
     * All active plugins.
     * @var array
     */
    public $active_plugins = null;

    /**
     * All plugins header information.
     * @var array
     */
    public $plugins_header = [];

    /**
     * All hooks.
     * @var array
     */
    public $hooks = [];

    /**
     * Hooks Construction.
     */
    public function __construct()
    {
        $this->plugins_table = config('hooks.table');
        if (!config('hooks'))
        {
            throw(new \Exception('You need to run the config before doing anything with this pack. Did you php artisan vendor:publish --tag=iambib-hooks ?'));
        }
        $this->set_hooks(config('hooks.hook_tags'));
        Cache::forget('active_plugins');
        $results = Cache::remember('active_plugins', 1440, function ()
        {
            $active_plugins = \DB::table($this->plugins_table)->select('filename')->whereStatus(1)->get();

            return $active_plugins;
        });

        $active_plugins = [];
        foreach ($results as $result)
        {
            $active_plugins[] = $result->filename;
        }
        $this->active_plugins = $active_plugins;
        $this->load_plugins();
    }

    /**
     * Hook tag registration.
     * @param string $tag name of the tag
     */
    public function set_hook($tag): void
    {
        $this->hooks[$tag] = [];
    }

    /**
     * Register multiple hook tags.
     *
     * @param array $tags. Name of the tags array.
     */
    public function set_hooks($tags): void
    {
        foreach ($tags as $tag)
        {
            $this->set_hook($tag);
        }
    }

    /**
     * Unset a hook.
     * @param string $tag. Name of the tag.
     */
    public function unset_hook($tag): void
    {
        unset($this->hooks[$tag]);
    }

    /**
     * Unset multiple hooks.
     * @param array $tags. Name of the tags.
     */
    public function unset_hooks($tags): void
    {
        foreach ($tags as $tag)
        {
            $this->developer_unset_hook($tag);
        }
    }

    /**
     * Load plugins from folder recursive.
     * @param string $from_folder optional.
     */
    public function load_plugins($from_folder = PLUGINS_FOLDER): void
    {
        foreach (\File::allFiles($from_folder) as $file)
        {
            if (($this->active_plugins == null || \in_array($file->getFileName(), $this->active_plugins)) && strpos($file->getPath() . $file->getFileName(), '.plugin.php'))
            {
                include_once $file->getPathName();
                if (\in_array($file->getFileName(), $this->active_plugins))
                {
                    foreach ($hooks as $hook)
                    {
                        $this->add_hook($hook['hook'], $hook['function'], $hook['priority']);
                    }
                }
            }
        }
    }

    /**
     * Get all plugins header info.
     *
     * @param string $from_folder
     * @return array. return all plugins.
     */
    public function get_plugins_header($from_folder = PLUGINS_FOLDER): array
    {
        foreach (File::allFiles($from_folder) as $file)
        {
            if (strpos($file->getPathName(), '.plugin.php'))
            {
                $plugin_data = File::get($file->getPathName());
                preg_match('|Plugin Name:(.*)$|mi', $plugin_data, $name);
                preg_match('|Plugin URI:(.*)$|mi', $plugin_data, $uri);
                preg_match('|Version:(.*)|i', $plugin_data, $version);
                preg_match('|Description:(.*)$|mi', $plugin_data, $description);
                preg_match('|Author:(.*)$|mi', $plugin_data, $author_name);
                preg_match('|Author URI:(.*)$|mi', $plugin_data, $author_uri);

                foreach (['name', 'uri', 'version', 'description', 'author_name', 'author_uri'] as $field)
                {
                    if (!empty(${$field}))
                    {
                        ${$field} = trim(${$field}[1]);
                    }
                    else
                    {
                        ${$field} = '';
                    }
                }
                $plugin_data = [
                    'filename' => $file->getFileName(),
                    'Name' => $name,
                    'Title' => $name,
                    'PluginURI' => $uri,
                    'Description' => $description,
                    'Author' => $author_name,
                    'AuthorURI' => $author_uri,
                    'Version' => $version,
                ];
                $this->plugins_header[] = $plugin_data;
            }
        }

        return $this->plugins_header;
    }

    /**
     * Attach action to a hook.
     * @param string $tag.      Hook name.
     * @param string $function. Function to be called.
     * @param int    $priority
     */
    public function add_hook($tag, $function, $priority = 10): void
    {
        if (!$this->hook_exist($tag))
        {
            throw(new \Exception("No such tag available ($tag). Maybe add it to config?"));
        }
        else
        {
            $this->hooks[$tag] = [];
            $this->hooks[$tag][$priority] = [];
            $this->hooks[$tag][$priority][] = $function;
        }
    }

    /**
     * Check if hook exists.
     * @param string $tag The name of the hook.
     */
    public function hook_exist($tag): bool
    {
        if (!isset($this->hooks[$tag]))
        {
            return false;
        }
        if (\is_array($this->hooks[$tag]))
        {
            if (\count($this->hooks[$tag]) > 0)
            {
                return true;
            }
        }
        else
        {
            return (trim($this->hooks[$tag]) == '') ? false : true;
        }

        return true;
    }

    /**
     * Execute the function attach to the hook.
     * @param  string $tag. Hook name
     * @param  mix    $args optional args for the function
     * @return mixed  $result optional.
     */
    public function execute_hook($tag, $args = ''): mixed
    {
        $result = $args;
        if (!$this->hook_exist($tag))
        {
            throw(new \Exception("No such tag available ($tag). Maybe add it to config?"));
        }
        $these_hooks = $this->hooks[$tag];
        for ($i = 0; $i <= \count($these_hooks); $i++)
        {
            if (isset($these_hooks[$i]))
            {
                foreach ($these_hooks[$i] as $hook)
                {
                    $result = \call_user_func($hook, $args);
                }
            }
        }

        return $result;
    }

    /**
     * Filter $args and modify.
     * @param string $tag. The name of the hook.
     * @param mix    $args optional.The arguments the function accept to filter(default none)
     * @return mixed. The $args filter result.
     */
    public function filter_hook($tag, $args = ''): mixed
    {
        if (!$this->hook_exist($tag))
        {
            throw(new \Exception("No such tag available ($tag). Maybe add it to config?"));
        }
        $result = $args;
        $these_hooks = $this->hooks[$tag];
        for ($i = 0; $i <= 20; $i++)
        {
            if (isset($these_hooks[$i]))
            {
                foreach ($these_hooks[$i] as $hook)
                {
                    $args = $result;
                    $result = \call_user_func($hook, $args);
                }
            }
        }

        return $result;
    }
}
