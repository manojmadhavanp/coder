module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    
    sass: {
      dist: {
        options: {
          style: 'compressed'
        },
        files: [{
          expand: true,
          cwd: 'assets/scss',
          src: ['**/*.scss'],
          dest: 'public/assets/css',
          ext: '.min.css'
        }]
      }
    },
    
    uglify: {
      options: {
        mangle: false
      },
      app: {
        files: [{
          expand: true,
          cwd: 'assets/js',
          src: ['**/*.js', '!**/*.min.js'],
          dest: 'public/assets/js',
          ext: '.min.js'
        }]
      }
    },
    
    cssmin: {
      site: {
        files: [{
          expand: true,
          cwd: 'assets/css',
          src: ['**/*.css', '!**/*.min.css'],
          dest: 'public/assets/css',
          ext: '.min.css'
        }]
      }
    },
    
    copy: {
      main: {
        files: [
          {expand: true, src: ['assets/images/**'], dest: 'public/'},
          {expand: true, src: ['assets/css/**'], dest: 'public/'},
          {src: ['assets/sw.js'], dest: 'public/sw.js'},
          {src: ['assets/manifest.json'], dest: 'public/manifest.json'}
        ],
      },
    },
    
    watch: {
      css: {
        files: ['assets/scss/**/*.scss'],
        tasks: ['sass']
      },
      js: {
        files: ['assets/js/**/*.js'],
        tasks: ['uglify']
      },
      other: {
        files: ['assets/css/**/*.css', 'assets/images/**/*', 'assets/sw.js', 'assets/manifest.json'],
        tasks: ['cssmin', 'copy']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-copy');

  grunt.registerTask('default', ['sass', 'uglify', 'cssmin', 'copy', 'watch']);
};
