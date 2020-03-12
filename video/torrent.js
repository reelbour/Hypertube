/*jshint esversion: 6 */

var die = msg => {
  console.log(msg);
  process.kill(process.pid);
};

// requires
const https = require('https');
const fs = require('fs');
const bencode = require('bencode');
const tracker = require('./tracker');
const torrentParser = require('./torrent-parser');

// die if wrong amout of param
if (process.argv.length != 3) die("Invalid param amount.");

// dl torrent file if not exist
var fileName = `${process.argv[2]}.torrent`;
// try {
//   fs.accessSync(fileName, fs.constants.F_OK);
// } catch (err) {
//   const wfile = fs.createWriteStream(fileName);
//   const url = `https://yts.mx/torrent/download/${process.argv[2]}`;
//   const request = https.get(url, (response) => {
//     response.pipe(wfile);
//   });
// }
//ni: make `get` syncrone to be sure the torrent file
//    has been completely dl before doing anything.

// parse torrent
const torrent = torrentParser.open(fileName);
//console.log('torrent: ', torrent);
tracker.getPeers(torrent, peers => {
  console.log('list of peers: ', peers);
  console.log('info hash: ', torrentParser.infoHash(torrent));
});
