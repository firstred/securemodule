module.exports = function(grunt) {

    grunt.initConfig({
        compress: {
            main: {
                options: {
                    archive: 'securemodule.zip'
                },
                files: [
                    {src: ['controllers/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['classes/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['docs/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['override/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['logs/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['vendor/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['mails/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['translations/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['upgrade/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['optionaloverride/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['oldoverride/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['sql/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['lib/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['defaultoverride/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: ['views/**'], dest: 'securemodule/', filter: 'isFile'},
                    {src: 'config.xml', dest: 'securemodule/'},
                    {src: 'index.php', dest: 'securemodule/'},
                    {src: 'securemodule.php', dest: 'securemodule/'},
                    {src: 'securemodule_ajax.php', dest: 'securemodule/'},
                    {src: 'logo.png', dest: 'securemodule/'},
                    {src: 'logo.gif', dest: 'securemodule/'},
                    {src: 'LICENSE.md', dest: 'securemodule/'},
                    {src: 'README.md', dest: 'securemodule/'},
                    {src: '.htaccess', dest: 'securemodule/'}
                ]
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-compress');

    grunt.registerTask('default', ['compress']);
};