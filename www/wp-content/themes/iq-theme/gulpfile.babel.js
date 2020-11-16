import {src, dest, watch, series, parallel} from 'gulp';
import yargs from 'yargs';
import sass from 'gulp-sass';
import cleanCss from 'gulp-clean-css';
import gulpIf from 'gulp-if';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import autoprefixer from 'autoprefixer';
import imagemin from 'gulp-imagemin';
import del from 'del';
import webpackStream from 'webpack-stream';
import webpack from 'webpack';
import named from 'vinyl-named';
import browserSync from 'browser-sync';
import plumber from 'gulp-plumber';
import notify from 'gulp-notify';
import rev from 'gulp-rev';

const PRODUCTION = yargs.argv.prod;
const server = browserSync.create();
const paths = {
    other: {
        src: ['src/**/*','!src/{images,js,scss}','!src/{images,js,scss}/**/*']
    },
    fonts: [
        'node_modules/@fortawesome/fontawesome-free/webfonts/*',
    ]
}

export const serve = done => {
    server.init({
        proxy: "http://127.0.0.1/indihu/www",
        browser: "chrome"
    });
    done();
}

export const reload = done => {
    server.reload();
    done();
}

export const styles = () => {
    return src(['src/scss/bundle.scss', 'src/scss/admin.scss', 'src/scss/gutenberg.scss'])
        .pipe(plumber({ errorHandler: function(err) {
            notify.onError({
                title: "Gulp error in " + err.plugin,
                message:  err.toString()
            })(err);
        }}))
        .pipe(gulpIf(!PRODUCTION, sourcemaps.init()))
        .pipe(sass().on('error', sass.logError))
        .pipe(gulpIf(PRODUCTION, postcss([autoprefixer])))
        .pipe(gulpIf(PRODUCTION, cleanCss({compatibility: 'ie8'})))
        .pipe(gulpIf(!PRODUCTION, sourcemaps.write()))
        .pipe(dest('dist/css'))
        .pipe(rev())
        .pipe(dest('dist/css'))
        .pipe(rev.manifest('dist/rev-manifest.json', {
            base: process.cwd()+'/dist',
            merge: true
        }))
        .pipe(dest('dist'))
        .pipe(server.stream());
}


export const scripts = () => {
    return src(['src/js/bundle.js', 'src/js/admin.js', 'src/js/gutenberg.js'])
        .pipe(plumber(plumber({ errorHandler: function(err) {
            notify.onError({
                title: "Gulp error in " + err.plugin,
                message:  err.toString()
            })(err);
        }})))
        .pipe(named())
        .pipe(webpackStream({
            module: {
                rules: [
                    {
                        test: /\.(js|jsx)$/,
                        use: {
                            loader: 'babel-loader'
                        }
                    },
                    {
                        test: require.resolve('snapsvg/dist/snap.svg.js'),
                        use: 'imports-loader?this=>window,fix=>module.exports=0',
                    }
                ]
            },
            plugins: [
                new webpack.ProvidePlugin({
                    $: 'jquery',
                    jQuery: 'jquery'
                }) 
            ],
            mode: PRODUCTION ? 'production' : 'development',
            devtool: !PRODUCTION ? 'inline-source-map' : false,
            output: {
                filename: '[name].js'
            },
            externals: {
                'react': 'React',
                'react-dom': 'ReactDOM'
            }
        }))
        .pipe(dest('dist/js'))
        .pipe(rev())
        .pipe(dest('dist/js'))
        .pipe(rev.manifest('dist/rev-manifest.json', {
            base: process.cwd()+'/dist',
            merge: true
        }))
        .pipe(dest('dist'))
        .pipe(server.stream());
}

export const images = () => {
    return src('src/images/**/*.{jpg,jpeg,png,svg,gif,ico}')
        .pipe(plumber(plumber({ errorHandler: function(err) {
            notify.onError({
                title: "Gulp error in " + err.plugin,
                message:  err.toString()
            })(err);
        }})))
        .pipe(gulpIf(PRODUCTION, imagemin()))
        .pipe(dest('dist/images'));
}

export const fonts = () => {
    return src(paths.fonts)
        .pipe(plumber(plumber({ errorHandler: function(err) {
            notify.onError({
                title: "Gulp error in " + err.plugin,
                message:  err.toString()
            })(err);
        }})))
        .pipe(dest('dist/fonts'));
} 

export const copy = () => {
    return src(paths.other.src)
        .pipe(plumber(plumber({ errorHandler: function(err) {
            notify.onError({
                title: "Gulp error in " + err.plugin,
                message:  err.toString()
            })(err);
        }})))
        .pipe(dest('dist'));
}

export const clean = () => del(['dist']);

export const watchForChanges = () => {
    watch('src/scss/**/*.scss', styles);
    watch('src/images/**/*.{jpg,jpeg,png,svg,gif,ico}', series(images, reload));
    watch(paths.other.src, series(copy, reload));
    watch('src/js/**/*.js', series(scripts, reload))
    watch("app/**/*.php", reload);
    watch("app/**/*.latte", reload);
}

export const dev = series(clean, parallel(styles, images, copy, scripts, fonts), serve, watchForChanges)
export const build = series(clean, parallel(styles, images, copy, scripts, fonts))
export default dev;

