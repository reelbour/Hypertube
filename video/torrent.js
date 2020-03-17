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
const download = require('./download').download;
const Pieces = require('./Pieces');

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

//console.log();
// console.log('torrent: ', torrent);
tracker.getPeers(torrent, peers => {
  const pieces = new Pieces(torrent);
  var path = '../data/';
  var peersobj = {p:peers};
  const files = torrentParser.files(torrent, path);
  console.log('list of files: ', files);
  console.log('list of peers: ', peers);
  console.log('info hash: ', torrentParser.infoHash(torrent));
  //peers.forEach(peer => download(peer, torrent, pieces, files));
  for (var i = 0; i < 30 && peers.length > 0; i++) {
    download(peersobj, torrent, pieces, files);
  }
});
