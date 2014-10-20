<?php
/*
 * This file is a part of Mibew Emoji Plugin.
 *
 * Copyright 2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @file The main file of Mibew:Emoji plugin.
 */

namespace Mibew\Mibew\Plugin\Emoji;

use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Symfony\Component\HttpFoundation\Request;

/**
 * The main plugin's file definition.
 *
 * It only attaches needed CSS and JS files to chat windows.
 */
class Plugin extends \Mibew\Plugin\AbstractPlugin implements \Mibew\Plugin\PluginInterface
{
    /**
     * List of the plugin configs.
     *
     * @var array
     */
    protected $config;

    /**
     * Class constructor.
     *
     * @param array $config List of the plugin config. The following options are
     * supported:
     *   - 'ignore_emoticons': boolean, if set to true, the plugin only converts
     *     :emoji: and ignore emoticons like :-) and ;D. The default value is
     *     false.
     */
    public function __construct($config)
    {
        $this->config = $config + array('ignore_emoticons' => false);
    }

    /**
     * The plugin does not need extra initialization thus it is always ready to
     * work.
     *
     * @return boolean
     */
    public function initialized()
    {
        return true;
    }

    /**
     * The main entry point of a plugin.
     */
    public function run()
    {
        // Attach CSS and JS files of the plugin to chat window.
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->attachListener(Events::PAGE_ADD_CSS, $this, 'attachCssFiles');
        $dispatcher->attachListener(Events::PAGE_ADD_JS, $this, 'attachJsFiles');
        $dispatcher->attachListener(Events::PAGE_ADD_JS_PLUGIN_OPTIONS, $this, 'attachPluginOptions');
    }

    /**
     * Event handler for "pageAddJS" event.
     *
     * @param array $args
     */
    public function attachJSFiles(&$args)
    {
        if ($this->isAppropriatePage($args['request'])) {
            $base_path = $this->getFilesPath();
            $args['js'][] = $base_path . '/vendor/es5-shim/es5-shim.js';
            $args['js'][] = $base_path . '/vendor/emojify.js/emojify.js';
            $args['js'][] = $base_path . '/js/plugin.js';
        }
    }

    /**
     * Event handler for "pageAddCSS" event.
     *
     * @param array $args
     */
    public function attachCssFiles(&$args)
    {
        if ($this->isAppropriatePage($args['request'])) {
            $args['css'][] = $this->getFilesPath() . '/css/styles.css';
        }
    }

    /**
     * Event handler for "pageAddJSPluginOptions" event.
     *
     * @param array $args
     */
    public function attachPluginOptions(&$args)
    {
        $request = $args['request'];

        if ($this->isAppropriatePage($request)) {
            $args['plugins']['MibewEmoji'] = array(
                'imagesDir' => ($request->getBasePath() . '/' . $this->getFilesPath()
                    . '/vendor/emojify.js/images/emoji'),
                'ignoreEmoticons' => $this->config['ignore_emoticons'],
            );
        }
    }

    /**
     * Specify dependencies of the plugin.
     *
     * @return array List of dependencies
     */
    public static function getDependencies()
    {
        // This plugin does not depend on others so return an empty array.
        return array();
    }

    /**
     * Checks if plugins data should be attached to the page.
     *
     * @param Request $request Incoming request
     * @return boolean
     */
    protected function isAppropriatePage(Request $request)
    {
        $route = $request->attributes->get('_route');

        return in_array(
            $route,
            array(
                'chat_operator',
                'chat_user_start',
                'chat_user',
                'chat_user_invitation',
            )
        );
    }
}
