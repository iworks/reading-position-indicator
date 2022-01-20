/*global require*/

/**
 * When grunt command does not execute try these steps:
 *
 * - delete folder 'node_modules' and run command in console:
 *   $ npm install
 *
 * - Run test-command in console, to find syntax errors in script:
 *   $ grunt hello
 */

module.exports = function(grunt) {
    // Show elapsed time at the end.
    require('time-grunt')(grunt);

    // Load all grunt tasks.
    require('load-grunt-tasks')(grunt);

    var buildtime = new Date().toISOString();

    var conf = {

        // Concatenate those JS files into a single file (target: [source, source, ...]).
        js_files_concat: {},

        // SASS files to process. Resulting CSS files will be minified as well.
        css_files_compile: {
            'assets/styles/iworks_position.css': 'assets/scss/iworks_position.scss',
            'assets/styles/iworks_position.admin.css': 'assets/scss/iworks_position.admin.scss',
        },

        // BUILD branches.
        plugin_branches: {
            exclude_release: [
                './README.MD',
                './README.md',
                './readme.txt',
                './Gruntfile.js',
                './package.json',
                './vendor/iworks/rate/README.md',
                './vendor/iworks/options/README.md',
                './vendor/iworks/options/LICENSE',
            ],
            include_files: [
                '**',
                '!css/src/**',
                '!js/src/**',
                '!js/vendor/**',
                '!img/src/**',
                '!node_modules/**',
                '!build/**',
                '!tests/**',
                '!assets/src',
                '!assets/sass',
                '!assets/scss',
                '!assets/src/**',
                '!assets/sass/**',
                '!assets/scss/**',
                '!**/js/vendor/**',
                '!**/img/src/**',
                '!**/node_modules/**',
                '!**/**.po',
                '!**/**.mo',
                '!**/**.log',
                '!**/tests/**',
                '!**/release/*.zip',
                '!release/*.zip',
                '!**/release/**',
                '!release/**',
                '!**/Gruntfile.js',
                '!**/package*',
                '!**/build/**',
                '!.sass-cache/**',
                '!.git/**',
                '!.git',
                '!.log',
                '!./README.MD',
                '!./README.md',
                '!Gruntfile.js',
                '!package.json',
                '!vendor/iworks/rate/README.md',
                '!vendor/iworks/options/README.md',
                '!vendor/iworks/options/LICENSE',
            ],
            base: 'master',
            release: 'reading-position-indicator'
        },

        // BUILD patterns to exclude code for specific builds.
        plugin_patterns: {
            release: [{
                match: /BUILDTIME/g,
                replace: buildtime
            }, {
                match: /IWORKS_OPTIONS_TEXTDOMAIN/g,
                replace: '<%= pkg.name %>'
            }, {
                match: /IWORKS_RATE_TEXTDOMAIN/g,
                replace: '<%= pkg.name %>'
            }, {
                match: /PLUGIN_TAGLINE/g,
                replace: '<%= pkg.tagline %>'
            }, {
                match: /PLUGIN_VERSION/g,
                replace: '<%= pkg.version %>'
            }, ],
            // Files to apply above patterns to (not only php files).
            files: {
                expand: true,
                src: [
                    '**/*.php',
                    '**/*.css',
                    '**/*.js',
                    '**/*.html',
                    '**/*.txt',
                    '!node_modules/**',
                    '!lib/**',
                    '!docs/**',
                    '!release/**',
                    '!Gruntfile.js',
                    '!build/**',
                    '!tests/**',
                    '!.git/**'
                ],
                dest: './release'
            }
        },

        // Regex patterns to exclude from transation.
        translation: {
            ignore_files: [
                'node_modules/.*',
                '(^.php)', // Ignore non-php files.
                'inc/external/.*', // External libraries.
                'release/.*', // Temp release files.
                'tests/.*', // Unit testing.
            ],
            pot_dir: 'languages/', // With trailing slash.
            textdomain: 'reading-position-indicator',
        },

        dev_plugin_file: 'reading-position-indicator.php',
        dev_plugin_dir: 'reading-position-indicator/'
    };

    // Project configuration
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        // JS - Concat .js source files into a single .js file.
        concat: {
            options: {
                stripBanners: true,
                banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
                    ' * <%= pkg.homepage %>\n' +
                    ' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
                    ' * Licensed GPLv2+' +
                    ' */\n'
            },
            scripts: {
                files: conf.js_files_concat
            }
        },


        // JS - Validate .js source code.
        jshint: {
            all: [
                'Gruntfile.js',
                'js/src/**/*.js',
            ],
            options: {
                curly: true,
                eqeqeq: true,
                immed: true,
                latedef: true,
                newcap: true,
                noarg: true,
                sub: true,
                undef: true,
                boss: true,
                eqnull: true,
                globals: {
                    exports: true,
                    module: false
                }
            }
        },


        // JS - Uglyfies the source code of .js files (to make files smaller).
        uglify: {
            all: {
                files: [{
                    expand: true,
                    src: ['*.js', '!*.min.js'],
                    cwd: 'assets/scripts/',
                    dest: 'assets/scripts/',
                    ext: '.min.js',
                    extDot: 'last'
                }],
                options: {
                    banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
                        ' * <%= pkg.homepage %>\n' +
                        ' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
                        ' * Licensed GPLv2+' +
                        ' */\n',
                    mangle: {
                        except: ['jQuery']
                    }
                }
            }
        },


        // TEST - Run the PHPUnit tests.
        /* -- Not used right now...
        phpunit: {
        	classes: {
        		dir: ''
        	},
        	options: {
        		bin: 'phpunit',
        		bootstrap: 'tests/php/bootstrap.php',
        		testsuite: 'default',
        		configuration: 'tests/php/phpunit.xml',
        		colors: true,
        		//tap: true,
        		//testdox: true,
        		//stopOnError: true,
        		staticBackup: false,
        		noGlobalsBackup: false
        	}
        },
        */


        // CSS - Compile a .scss file into a normal .css file.
        sass: {
            all: {
                options: {
                    'sourcemap=none': true, // 'sourcemap': 'none' does not work...
                    unixNewlines: true,
                    style: 'expanded'
                },
                files: conf.css_files_compile
            }
        },


        // CSS - Automaticaly create prefixed attributes in css file if needed.
        //       e.g. add `-webkit-border-radius` if `border-radius` is used.
        autoprefixer: {
            options: {
                browsers: ['last 2 version', 'ie 8', 'ie 9'],
                diff: false
            },
            single_file: {
                files: [{
                    expand: true,
                    src: ['**/*.css', '!**/*.min.css'],
                    cwd: 'css/',
                    dest: 'css/',
                    ext: '.css',
                    extDot: 'last',
                    flatten: false
                }]
            }
        },


        // CSS - Required for CSS-autoprefixer and maybe some SCSS function.
        compass: {
            options: {},
            server: {
                options: {
                    debugInfo: true
                }
            }
        },


        // CSS - Minify all .css files.
        cssmin: {
            options: {
                banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
                    ' * <%= pkg.homepage %>\n' +
                    ' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
                    ' * Licensed GPLv2+' +
                    ' */\n'
            },
            minify: {
                expand: true,
                src: ['*.css', '!*.min.css'],
                cwd: 'assets/styles/',
                dest: 'assets/styles/',
                ext: '.min.css',
                extDot: 'last'
            }
        },


        // WATCH - Watch filesystem for changes during development.
        watch: {
            sass: {
                files: ['assets/scss/**/*.scss'],
                tasks: ['sass', 'autoprefixer'],
                options: {
                    debounceDelay: 500
                }
            },

            scripts: {
                files: ['assets/scripts/**/*.js', 'js/vendor/**/*.js'],
                tasks: ['jshint', 'concat'],
                options: {
                    debounceDelay: 500
                }
            }
        },


        // BUILD - Remove previous build version and temp files.
        clean: {
            temp: {
                src: [
                    '**/*.tmp',
                    '**/.afpDeleted*',
                    '**/.DS_Store',
                ],
                dot: true,
                filter: 'isFile'
            },
            main: {
                src: [
                    'release/<%= pkg.version %>/',
                    'release/<%= pkg.name %>-<%= pkg.version %>.zip',
                ],
            },
            release: {}
            // conf.plugin_branches.exclude_release
        },


        // BUILD - Copy all plugin files to the release subdirectory.
        copy: {
            release: {
                src: conf.plugin_branches.include_files,
                dest: 'release/<%= pkg.version %>/'
            },
        },

        potomo: {
            dist: {
                options: {
                    poDel: false
                },
                files: [{
                    expand: true,
                    cwd: conf.translation.pot_dir,
                    src: ['*.po'],
                    dest: conf.translation.pot_dir,
                    ext: '.mo',
                    nonull: true
                }]
            }
        },

        // BUILD - Create a zip-version of the plugin.
        compress: {
            release: {
                options: {
                    mode: 'zip',
                    archive: './release/<%= pkg.name %>-<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: 'release/<%= pkg.version %>/',
                src: ['**/*'],
                dest: conf.dev_plugin_dir
            }
        },

        // BUILD - update the translation index .po file.
        makepot: {
            target: {
                options: {
                    cwd: '',
                    domainPath: conf.translation.pot_dir,
                    exclude: conf.translation.ignore_files,
                    mainFile: conf.dev_plugin_file,
                    potFilename: conf.translation.textdomain + '.pot',
                    potHeaders: {
                        poedit: true, // Includes common Poedit headers.
                        'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
                    },
                    type: 'wp-plugin' // wp-plugin or wp-theme
                }
            }
        },

        // BUILD: Replace conditional tags in code.
        replace: {
            release: {
                options: {
                    patterns: conf.plugin_patterns.release
                },
                files: [conf.plugin_patterns.files]
            }
        },

        // BUILD: Git control (add files).
        checktextdomain: {
            options: {
                report_missing: true,
                text_domain: ['reading-position-indicator', 'IWORKS_OPTIONS_TEXTDOMAIN', 'IWORKS_RATE_TEXTDOMAIN', ],

                keywords: [ //List keyword specifications
                    '__:1,2d',
                    '_e:1,2d',
                    '_x:1,2c,3d',
                    'esc_html__:1,2d',
                    'esc_html_e:1,2d',
                    'esc_html_x:1,2c,3d',
                    'esc_attr__:1,2d',
                    'esc_attr_e:1,2d',
                    'esc_attr_x:1,2c,3d',
                    '_ex:1,2c,3d',
                    '_n:1,2,4d',
                    '_nx:1,2,4c,5d',
                    '_n_noop:1,2,3d',
                    '_nx_noop:1,2,3c,4d'
                ]
            },
            files: {
                src: ['*.php', 'vendor/**/*.php'], //all php 
                expand: true,
            },
        },

    });

    grunt.registerTask('notes', 'Show release notes', function() {
        grunt.log.subhead('Release notes');
        grunt.log.writeln('  1. Check BITBUCKET for pull-requests');
        grunt.log.writeln('  2. Check ASANA for high-priority bugs');
        grunt.log.writeln('  3. Check EMAILS for high-priority bugs');
        grunt.log.writeln('  4. Check FORUM for open threads');
        grunt.log.writeln('  5. REPLY to forum threads + unsubscribe');
        grunt.log.writeln('  6. Update the TRANSLATION files');
        grunt.log.writeln('  7. Generate ARCHIVE');
        grunt.log.writeln('  8. Check ARCHIVE structure - it should be a folder with plugin name');
        grunt.log.writeln('  9. INSTALL on a clean WordPress installation');
        grunt.log.writeln(' 10. RELEASE the plugin!');
    });

    // Test task.
    grunt.registerTask('hello', 'Test if grunt is working', function() {
        grunt.log.subhead('Hi there :)');
        grunt.log.writeln('Looks like grunt is installed!');
    });

    // Default task.

    grunt.registerTask('default', ['clean:temp', 'jshint', 'concat', 'uglify', 'sass', 'autoprefixer', 'cssmin']);
    grunt.registerTask('js', ['concat', 'uglify']);
    grunt.registerTask('css', ['sass', 'cssmin']);
    // grunt.registerTask( 'i18n', [ 'checktextdomain', 'makepot', 'potomo' ] );
    grunt.registerTask('i18n', ['makepot', ]);

    grunt.registerTask('build', ['default', 'i18n', 'clean', 'copy', 'replace', 'compress', 'notes']);
    //grunt.registerTask( 'test', ['phpunit', 'jshint'] );

    grunt.task.run('clear');
    grunt.util.linefeed = '\n';
};