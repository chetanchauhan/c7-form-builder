/* jshint node:true */

module.exports = function (grunt) {
	'use strict';

	// Load multiple grunt tasks using globbing patterns.
	require('load-grunt-tasks')(grunt);

	// Show elapsed time.
	require('time-grunt')(grunt);

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// Watch source files.
		watch: {
			options: {
				livereload: true
			},
			php: {
				files: ['**/*.php', '!node_modules/**', '!build/**'],
				tasks: ['checktextdomain']
			},
			css: {
				files: ['assets/css/**/*.css'],
				tasks: ['dev:css']
			},
			js: {
				files: ['Gruntfile.js', 'assets/js/**/*.js'],
				tasks: ['dev:js', 'jshint']
			}
		},

		// Check for textdomain errors.
		checktextdomain: {
			options: {
				text_domain: 'c7-form-builder',
				keywords: [
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
				src: [
					'**/*.php',
					'!build/**',
					'!node_modules/**'
				],
				expand: true
			}
		},

		// Generate POT files.
		makepot: {
			plugin: {
				options: {
					type: 'wp-plugin',
					domainPath: '/languages/',
					exclude: ['build/.*'],
					mainFile: 'c7-form-builder.php',
					potFilename: 'c7-form-builder.pot',
					potHeaders: {
						poedit: true,
						'report-msgid-bugs-to': 'https://github.com/chetanchauhan/c7-form-builder/issues'
					}
				}
			}
		},

		// Autoprefix CSS
		autoprefixer: {
			options: {
				browsers: ['Android >= 2.1', 'Chrome >= 21', 'Explorer >= 7', 'Firefox >= 17', 'Opera >= 12.1', 'Safari >= 6.0']
			},
			all: {
				src: ['assets/css/**/*.css', '!assets/css/**/*.min.css']
			}
		},

		// Format CSS files according to WordPress CSS coding standards.
		wpcss: {
			all: {
				expand: true,
				src: ['assets/css/**/*.css', '!assets/css/**/*.min.css']
			}
		},

		// Minify all CSS files.
		cssmin: {
			minify: {
				expand: true,
				cwd: 'assets/css/',
				src: ['**/*.css', '!**/*.min.css'],
				dest: 'assets/css/',
				ext: '.min.css'
			}
		},

		// Lint all JS files with JSHint.
		jshint: {
			options: {
				reporter: require('jshint-stylish'),
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'assets/js/**/*.js',
				'!assets/js/**/*.min.js'
			]
		},

		// Minify all JS files.
		uglify: {
			options: {
				preserveComments: 'some'
			},
			all: {
				files: [{
					expand: true,
					cwd: 'assets/js/',
					src: ['**/*.js', '!**/*.min.js'],
					dest: 'assets/js/',
					ext: '.min.js'
				}]
			}
		},

		// Clean up build directory
		clean: {
			main: ['build/<%= pkg.name %>']
		},

		// Copy source files to build directory for release or deploy.
		copy: {
			main: {
				src: [
					'**/*',
					'!node_modules/**',
					'!**/.**',
					'!build/**',
					'!Gruntfile.js',
					'!package.json',
					'!README.md',
					'!apigen.neon',
					'!docs/**',
					'!**/*~'
				],
				dest: 'build/<%= pkg.name %>/'
			}
		},

		// Compress build directory into <name>.zip
		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './build/<%= pkg.name %>.zip'
				},
				expand: true,
				cwd: 'build/<%= pkg.name %>/',
				src: ['**/*'],
				dest: '<%= pkg.name %>/'
			}
		}

	});

	grunt.registerTask('check', ['checktextdomain', 'jshint']);
	grunt.registerTask('dev:css', ['autoprefixer', 'wpcss', 'cssmin']);
	grunt.registerTask('dev:js', ['uglify']);
	grunt.registerTask('dev:i18n', ['makepot']);
	grunt.registerTask('dev', ['dev:css', 'dev:js', 'dev:i18n']);
	grunt.registerTask('build', ['clean', 'copy', 'compress']);
	grunt.registerTask('default', ['dev']);

};
