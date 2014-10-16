var bower = require('bower'),
    eventStream = require('event-stream'),
    gulp = require('gulp'),
    chmod = require('gulp-chmod'),
    zip = require('gulp-zip'),
    tar = require('gulp-tar'),
    gzip = require('gulp-gzip'),
    rename = require('gulp-rename');

// Installs bower dependencies
gulp.task('bower', function(callback) {
    bower.commands.install([], {}, {})
        .on('error', function(error) {
            callback(error);
        })
        .on('end', function() {
            callback();
        });
});

gulp.task('prepare-release', ['bower'], function() {
    var version = require('./package.json').version;

    return eventStream.merge(
        getSources()
            .pipe(zip('emoji-plugin-' + version + '.zip')),
        getSources()
            .pipe(tar('emoji-plugin-' + version + '.tar'))
            .pipe(gzip())
    )
    .pipe(chmod(0644))
    .pipe(gulp.dest('release'));
});

// Builds and packs plugins sources
gulp.task('default', ['prepare-release'], function() {
    // The "default" task is just an alias for "prepare-release" task.
});

/**
 * Returns files stream with the plugin sources.
 *
 * @returns {Object} Stream with VinylFS files.
 */
var getSources = function() {
    return gulp.src([
            'Plugin.php',
            'README.md',
            'LICENSE',
            'js/*',
            'css/*',
            'vendor/es5-shim/es5-shim.js',
            'vendor/es5-shim/LICENSE',
            'vendor/es5-shim/README.md',
            'vendor/emojify.js/emojify.js',
            'vendor/emojify.js/README.md',
            'vendor/emojify.js/images/emoji/*'
        ],
        {base: './'}
    )
    .pipe(rename(function(path) {
        path.dirname = 'Mibew/Mibew/Plugin/Emoji/' + path.dirname;
    }));
}
