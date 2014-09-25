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

use Symfony\Component\HttpFoundation\Request;

/**
 * The main plugin's file definition.
 *
 * It only attaches needed CSS and JS files to chat windows.
 */
class Plugin extends \Mibew\Plugin\AbstractPlugin implements \Mibew\Plugin\PluginInterface
{
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
        $dispatcher = \Mibew\EventDispatcher::getInstance();
        $dispatcher->attachListener('pageAddCSS', $this, 'attachCssFiles');
        $dispatcher->attachListener('pageAddJS', $this, 'attachJsFiles');
        $dispatcher->attachListener('pageAddJSPluginOptions', $this, 'attachPluginOptions');
    }

    /**
     * Event handler for "pageAddJS" event.
     *
     * @param array $args
     */
    public function attachJSFiles(&$args)
    {
        $request = $args['request'];

        if ($this->isAppropriatePage($request)) {
            $base_path = $request->getBasePath() . '/' . $this->getFilesPath();
            $args['js'][] = $base_path . '/components/es5-shim/es5-shim.js';
            $args['js'][] = $base_path . '/components/emojify.js/emojify.js';
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
        $request = $args['request'];

        if ($this->isAppropriatePage($request)) {
            $args['css'][] = $request->getBasePath() . '/' . $this->getFilesPath()
                . '/css/styles.css';
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
                    . '/components/emojify.js/images/emoji'),
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
