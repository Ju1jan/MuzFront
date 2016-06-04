var gulp = require('gulp');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var less = require('gulp-less');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var env = process.env.GULP_ENV;
var babel = require('gulp-babel');

var path = {
    src: {
        //js: 'app/Resources/web/js/**/*.js',
        jsx: 'app/Resources/web/js/**/*.jsx',
        less: 'app/Resources/web/less/**/*.less'
    },
    dist: {
        js: 'web/js/',
        css: 'web/css/'
    }
};

gulp.task('js:libs:build', function () {
    return gulp.src([
        'bower_components/jquery/dist/jquery.js',
        'bower_components/bootstrap/dist/js/bootstrap.js',
        'bower_components/react/react.js',
        'bower_components/react/react-dom.js',
        'bower_components/react/react-with-addons.js'
    ])
        .pipe(concat('libs.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(path.dist.js));
});

gulp.task('js:babel:build', function () {
    return gulp.src(path.src.jsx)
        .pipe(babel({
            presets: ['es2015', 'react']
        }))
        .pipe(gulp.dest(path.dist.js));
});

gulp.task('js:build', ['js:libs:build', 'js:babel:build']);

gulp.task('css:build', function () {
    return gulp.src([
        'bower_components/bootstrap/dist/css/bootstrap.css',
        path.src.less])
        .pipe(gulpif(/[.]less/, less()))
        .pipe(concat('styles.css'))
        .pipe(gulpif(env === 'prod', uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(path.dist.css));
});

gulp.task('js:watch', function () {
    return gulp.watch([path.src.jsx], ['js:build']);
});

gulp.task('css:watch', function () {
    return gulp.watch([path.src.less], ['css:build']);
});

gulp.task('default', ['js:build', 'css:build']);

gulp.task('watch', ['js:watch', 'css:watch']);
