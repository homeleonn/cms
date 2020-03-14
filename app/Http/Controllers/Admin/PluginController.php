<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PluginController extends Controller
{
	const PLUGIN = '/^[ \t\/*#@]*needle:(.*)$/mi';
	
	private $folder;
	private $activePlugins;
	
	public function __construct()
	{
		$this->folder = public_path() . '/plugins/';
		$this->activePlugins = unserialize(\Options::get('plugins_activated'));
	}
	
	/**
	 *  Show plugins
	 */
	public function actionIndex()
	{
		$needles 			= ['Plugin Name', 'Plugin URI', 'Description', 'Version', 'Author', 'Author URI', 'License'];
		$plugins 			= plugins($this->activePlugins);
		$data['plugins'] 	= [];
		$data['title'] 		= 'Плагины';
		
		if(!$plugins) return $data;
		
		foreach ($plugins as $plugin) {
			$fileData = file_get_contents($plugin['src']);
			
			foreach ($needles as $needle) {
				$plugin[$needle] = preg_match(str_replace('needle', $needle, self::PLUGIN), $fileData, $matches) ? 
					trim($matches[1]):
					'none';
			}
			
			if ($plugin['Plugin Name'] == 'none') continue;
			
			$data['plugins'][] 	= $plugin;
		}
		
		return view('plugin.index', compact('data'));
	}
	
	/**
	 *  Plugins toggle
	 *  
	 *  @param type $pluginFolder
	 *  @param type $pluginFile
	 */
	public function actionToggle($pluginFolder, $pluginFile)
	{
		if (isset($this->activePlugins[$pluginFolder])) {
			unset($this->activePlugins[$pluginFolder]);
		} else {
			$this->activePlugins[$pluginFolder] = $pluginFolder . '/' . $pluginFile;
		}
		
		\Options::get('plugins_activated', serialize($this->activePlugins));
		redir(route('plugin.index'));
	}
	
	public function actionSettings($pluginFolder)
	{
		// dd($pluginFolder);
		ob_start();
		doAction('plugin_settings');
		$data['content'] = ob_get_clean();
		
		return view('plugin.settings', ['data' => $data]);
		return ['content' => $content];
	}
	
	public function actionSettingsSave()
	{
		doAction('plugin_settings_save');
	}
}