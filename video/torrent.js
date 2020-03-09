/*jshint esversion: 6 */

var die = function(msg) {
  console.log(msg);
  process.kill(process.pid);
};

var readTorrent = function(fileName) {
  const fileContent = fs.readFileSync(fileName);
  const torrent = bencode.decode(fileContent);

  return torrent;
};

// die if wrong amout of param
if (process.argv.length != 4) die("Invalid param amount.");

// requires
const https = require('https');
const fs = require('fs');
const bencode = require('bencode');

// torent file name
var fileName = process.argv[2] + ".torrent";

// dl file if not exist
fs.access(fileName, fs.constants.F_OK, (err) => {
  if (err) {
    const wfile = fs.createWriteStream(fileName);
    const request = https.get(process.argv[3], (response) => {
      response.pipe(wfile);
    });
  }
  else {
    torrent = readTorrent(fileName);
    console.log(torrent.announce.toString('utf8'));
  }

});
