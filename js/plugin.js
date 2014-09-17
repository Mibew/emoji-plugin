/*!
 * This file is a part of Mibew Emoji Plugin.
 *
 * Copyright 2005-2014 the original author or authors.
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

(function (Mibew) {
    var module = Mibew.Application.module(
        'Chat.MibewEmojiPlugin',
        {startWithParent: false}
    );

    // Start the module only after the parent one will be initialized.
    Mibew.Application.Chat.on('start', function() {
        module.start();
    });

    module.addInitializer(function() {
        var imagesDir = Mibew.PluginOptions.MibewEmoji.imagesDir;

        // Update message body right after it is added to the messages
        // collection.
        Mibew.Objects.Collections.messages.on('add', function(model) {
            var kind = model.get('kind');

            if (kind == model.KIND_USER || kind == model.KIND_AGENT) {
                model.set(
                    'message',
                    emoji(model.get('message'), imagesDir)
                );
            }
        });
    });
})(Mibew);
