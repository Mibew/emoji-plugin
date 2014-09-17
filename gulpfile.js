var bower = require('bower'),
    eventStream = require('event-stream'),
    gulp = require('gulp'),
    chmod = require('gulp-chmod'),
    zip = require('gulp-zip'),
    tar = require('gulp-tar'),
    gzip = require('gulp-gzip');

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

// Builds and packs plugins sources
gulp.task('default', ['bower'], function() {
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
            'components/emoji-images/emoji-images.js',
            'components/emoji-images/pngs/*'
        ],
        {base: './'}
    );
}
