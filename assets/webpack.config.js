const webpack = require( 'webpack' );

module.exports = function ( env ) {
  'use strict';
  return {
    entry: [
      `${__dirname}/src/scss/editor-style.scss`,
      `${__dirname}/src/scss/ie.scss`,
      `${__dirname}/src/scss/style.scss`,
      `${__dirname}/src/scss/login.scss`,
    ],
    output: {
      path: `${__dirname}/build/js`,
      filename: 'main.js'
    },
    watch: env==='watch',
    watchOptions: {
      ignored: [/node_modules/]
    },
    module: {
      rules: [
        {
          test: /\.scss$/,
          exclude: /node_modules/,
          // module chain executes from last to first
          use: [
            {
              loader: 'file-loader',
              options: { name: '[name].css', outputPath: '../css/' }
            },
            { loader: 'extract-loader' },
            { loader: 'css-loader', options: { url:false, sourceMap: true } },
            { loader: 'resolve-url-loader' },
            { loader: 'sass-loader', options: { sourceMap: true } }
          ]
        },
      ]
    },
    stats: {
      colors: true
    },
    devtool: 'source-map'
  }
};