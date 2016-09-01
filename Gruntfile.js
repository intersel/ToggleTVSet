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
            dist: {
                options: {
                    position: 'top',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'assets/components/toggletvset/mgr/js/toggletvset.min.js'
                    ]
                }
            }
        },
        uglify: {
            toggletvset: {
                src: [
                    'assets/components/toggletvset/mgr/js/toggletvset.js'
                ],
                dest: 'assets/components/toggletvset/mgr/js/toggletvset.min.js'
            }
        },
        sftp: {
            js: {
                files: {
                    "./": ['assets/components/toggletvset/mgr/js/toggletvset.min.js']
                },
                options: {
                    path: '<%= sshconfig.hostpath %>develop/toggletvset/',
                    srcBasePath: 'develop/toggletvset/',
                    host: '<%= sshconfig.host %>',
                    username: '<%= sshconfig.username %>',
                    privateKey: grunt.file.read("/Users/jako/.ssh/id_dsa"),
                    passphrase: '<%= sshconfig.passphrase %>',
                    showProgress: true
                }
            }
        },
        watch: {
            scripts: {
                files: ['assets/components/toggletvset/mgr/js/**/*.js'],
                tasks: ['uglify', 'usebanner', 'sftp']
            }
        }
    });

    //load the packages
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-banner');
    grunt.loadNpmTasks('grunt-ssh');

    //register the task
    grunt.registerTask('default', ['uglify', 'usebanner', 'sftp']);
};