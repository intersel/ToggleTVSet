module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        modx: grunt.file.readJSON('_build/config.json'),
        sshconfig: grunt.file.readJSON('/Users/jako/Documents/MODx/partout.json'),
        banner: '/*!\n' +
        ' * <%= modx.name %> - <%= modx.description %>\n' +
        ' * Version: <%= modx.version %>\n' +
        ' * Build date: <%= grunt.template.today("yyyy-mm-dd") %>\n' +
        ' */\n',
        usebanner: {
            js: {
                options: {
                    position: 'top',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'assets/components/toggletvset/js/mgr/toggletvset.min.js'
                    ]
                }
            }
        },
        uglify: {
            mgr: {
                src: [
                    'source/js/mgr/toggletvset.js'
                ],
                dest: 'assets/components/toggletvset/js/mgr/toggletvset.min.js'
            }
        },
        sftp: {
            js: {
                files: {
                    "./": ['assets/components/toggletvset/js/mgr/toggletvset.min.js']
                },
                options: {
                    path: '<%= sshconfig.hostpath %>develop/toggletvset/',
                    srcBasePath: 'develop/toggletvset/',
                    host: '<%= sshconfig.host %>',
                    username: '<%= sshconfig.username %>',
                    privateKey: '<%= sshconfig.privateKey %>',
                    passphrase: '<%= sshconfig.passphrase %>',
                    showProgress: true
                }
            }
        },
        watch: {
            js: {
                files: [
                    'source/**/*.js'
                ],
                tasks: ['uglify', 'usebanner:js', 'sftp:js']
            },
            config: {
                files: [
                    '_build/config.json'
                ],
                tasks: ['default']
            }
        },
        bump: {
            copyright: {
                files: [{
                    src: 'core/components/toggletvset/model/toggletvset/toggletvset.class.php',
                    dest: 'core/components/toggletvset/model/toggletvset/toggletvset.class.php'
                }],
                options: {
                    replacements: [{
                        pattern: /Copyright 2015-\d{4}? by/g,
                        replacement: 'Copyright ' + (new Date().getFullYear() > 2015 ? '2015-' : '') + new Date().getFullYear() + ' by'
                    }]
                }
            },
            version: {
                files: [{
                    src: 'core/components/toggletvset/model/toggletvset/toggletvset.class.php',
                    dest: 'core/components/toggletvset/model/toggletvset/toggletvset.class.php'
                }],
                options: {
                    replacements: [{
                        pattern: /version = '\d+.\d+.\d+[-a-z0-9]*'/ig,
                        replacement: 'version = \'' + '<%= modx.version %>' + '\''
                    }]
                }
            },
            docs: {
                files: [{
                    src: 'mkdocs.yml',
                    dest: 'mkdocs.yml'
                }],
                options: {
                    replacements: [{
                        pattern: /&copy; \d{4}(-\d{4})?/g,
                        replacement: '&copy; ' + (new Date().getFullYear() > 2015 ? '2015-' : '') + new Date().getFullYear()
                    }]
                }
            }
        }
    });

    //load the packages
    grunt.loadNpmTasks('grunt-banner');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-ssh');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.renameTask('string-replace', 'bump');

    //register the task
    grunt.registerTask('default', ['bump', 'uglify', 'usebanner', 'sftp']);
};
