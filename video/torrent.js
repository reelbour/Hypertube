/*jshint esversion: 6 */

var die = function() {
  process.kill(process.pid);
};

if (process.argv.length != 4) die();

const https = require('https');
const fs = require('fs');

const file = fs.createWriteStream(process.argv[2] + ".torrent");
const request = https.get(process.argv[3], function(response) {
  response.pipe(file);
});
