/*!
 * This file is a part of Mibew Emoji Plugin.
 *
 * Copyright 2014 Dmitriy Simushev <simushevds@gmail.com>.
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

(function (Mibew, emojify) {
    // Initialize separated Marionette.js module for the plugin.
    var module = Mibew.Application.module(
        'MibewEmojiPlugin',
        {startWithParent: false}
    );

    // Make the plugin works together with "Chat" and "Invitation" modules.
    var eventsMap = {
        'start': function() {
            module.start();
        },
        'stop': function() {
            module.stop();
        }
    }

    Mibew.Application.Chat.on(eventsMap);
    Mibew.Application.Invitation.on(eventsMap);

    module.addInitializer(function() {
        emojify.setConfig({
            img_dir: Mibew.PluginOptions.MibewEmoji.imagesDir,
            ignore_emoticons: Mibew.PluginOptions.MibewEmoji.ignoreEmoticons
        });

        // Update message body right after it is added to the messages
        // collection.
        Mibew.Objects.Collections.messages.on('add', function(model) {
            var kind = model.get('kind');

            if (kind == model.KIND_USER || kind == model.KIND_AGENT) {
                model.set(
                    'message',
                    emojify.replace(model.get('message'))
                );
            }
        });
    });
})(Mibew, emojify);
