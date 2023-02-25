var elixir = require('laravel-elixir');
require('laravel-elixir-scss-lint');
require('laravel-elixir-artisan-serve');
var gulp = require('gulp');
var cleanCSS = require('gulp-clean-css');
var uglify = require('gulp-uglify');
var pump = require('pump');
// var BrowserSync = require('laravel-elixir-browsersync2');
//require('./elixir-extensions');

var Task = elixir.Task;

elixir.extend('minJs', function() {
    new Task('compress', function (cb) {
        pump([
                gulp.src('public/bin/*.js'),
                uglify(),
                gulp.dest('public/bin/js')
            ],
            cb
        );

    });
});

elixir(function(mix) {
    // BrowserSync.init();
 mix
    //  .phpUnit()
    // .sass([
    //     'app.scss',
    //     'modules/_basics.scss',
    //     'modules/_colors.scss',
    //     'partials/_responsive.scss'
    // ], 'resources/assets/combine/app-sass.css')

     // .sass([
     //     'styles.scss'
     // ], 'resources/assets/combine/app-sass.css')

     // .sass('materialize/materialize.scss', 'public/css/business/materialize.css')

  //   .styles([
  //       'vendor/animate.css',
  //       'vendor/jquery.mCustomScrollbar.css',
  //       'vendor/owl.carousel.css',
  //       'vendor/owl.transitions.css'
  //   ], 'resources/assets/combine/app-css.css')
  //
  //
  //   .styles([
  //     './resources/assets/combine/app-sass.css',
  //     './resources/assets/combine/app-css.css'
  // ],'public/css/app.css')
     
     // Sass para estilos mobile
     // .sass([
     //     'styles/jquery-ui.scss',
     //     'styles/fontawesome.scss',
     //     'styles/bootstrap.scss',
     //     'styles/styles.scss',
     //     'styles/media/max-768.scss'
     // ], 'resources/assets/combine/app-mobile.css')
     //
     // .sass([
     //     'styles/jquery-ui.scss',
     //     'styles/fontawesome.scss',
     //     'styles/bootstrap.scss',
     //     'styles/styles.scss',
     //     'styles/media/max-768.scss'
     // ], 'resources/assets/combine/app-desktop.css')

     // .styles([
     //     './public/css/app-old.css',
     //     './public/css/bootstrap-plain.css',
     //     './public/css/jquery-ui-plain.css',
     //     './public/css/font-awesome-plain.css'
     // ],'public/css/app-plain.css')

     .styles([
         './public/css/bootstrap.css',
         './public/css/font-awesome.css',
         './public/css/app.css',
         './public/css/stores.css',
         './public/css/app-max-768.css',
         './public/css/app-min-768.css',
         './public/css/jquery-ui.css'
     ],'public/css/citystock.css')

     // .scripts([
     //     './public/js/i18next.js',
     //     './public/js/pwstrngth/pwstrength-bootstrap.min.js',
     //     './public/js/pwstrngth/pwstrength.js'
     // ],'public/js/dist/pwstrength.js')
     //
     // .scripts([
     //     './public/js/vendor/jquery.min.js',
     //     './public/js/vendor/jquery-ui.min.js',
     //     './public/js/vendor/bootstrap.min.js'
     // ],'public/js/dist/jquery-bootstrap.js')


     .version([
         "public/css/app.css",
         "public/css/citystock.css",
         "public/css/stores.css",
         "public/css/business/materialize.css",
         "public/css/business/business.css",
         "public/js/dist/jquery-bootstrap.js",
         "public/js/dist/pwstrength.js",
         "public/js/blog/blog.js",
         "public/js/web/envio.js",
         "public/js/web/login-registro.js",
         "public/js/web/pago.js",
         "public/js/drivers/viaje.js",
         "public/js/viaje2.js",
         "public/js/drivers/viaje-seleccion.js",
         "public/js/drivers/viaje-pagar.js",
         "public/js/web/alerta.js",
         "public/js/business.js",
         "public/js/web/imagen.js"
     ])

     // .artisanServe({
     //     php_path: 'C:\\xampp\\php\\php', // Path to PHP
     //     artisan_path: './artisan', // Relative path from gulpfile to the artisan file
     //     host: '192.168.1.102', // Host to pass to artisan serve
     //     port: 8000, // Port to pass to artisan serve
     //     show_requests: true // Show requests in the output
     // });

     // .BrowserSync({
     //     files: ['app/**/*',
     //         // 'public/**/*',
     //         'resources/views/**/*'],
     //     server: {
     //         baseDir: "app",
     //         directory: true
     //     },
     //     proxy: "192.168.1.104:8000"
     // });
});

gulp.task('minjs', function (cb) {
    pump([
            gulp.src('public/js/dist/*.js'),
            uglify(),
            gulp.dest('public/js/dist/')
        ],
        cb
    );

});

gulp.task('minjs', function (cb) {
    pump([
            gulp.src('public/js/dist/*.js'),
            uglify(),
            gulp.dest('public/js/dist/')
        ],
        cb
    );

});

gulp.task('minintltelinput', function (cb) {
    pump([
            gulp.src('public/js/vendor/intltelinput/intlTelInput.min.js'),
            uglify(),
            gulp.dest('public/js/dist/')
        ],
        cb
    );

});

gulp.task('minify-css-transporter', function() {
    return gulp.src('public/css/citystock.css')
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest('public/css'));
});

gulp.task('minify-css', function() {
    return gulp.src('resources/assets/combine/*.css')
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest('public/css'));
});

gulp.task('minify-css-plain', function() {
    return gulp.src('public/css/app-plain.css')
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest('public/css'));
});