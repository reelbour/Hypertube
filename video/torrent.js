/*jshint esversion: 6 */

var die = msg => {
  console.log(msg);
  process.kill(process.pid);
};

var readTorrent = fileName => {
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

// dl torrent file if not exist
var fileName = process.argv[2] + ".torrent";
fs.access(fileName, fs.constants.F_OK, (err) => {
  if (err) {
    const wfile = fs.createWriteStream(fileName);
    const request = https.get(process.argv[3], (response) => {
      response.pipe(wfile);
    });
  }
  else {
    /*
    ** request to the tracker
    */

    torrent = readTorrent(fileName);

    // require
    const dgram = require('dgram');
    const Buffer = require('buffer').Buffer;
    const urlParse = require('url').parse;

    // prepare
    const url = urlParse(torrent.announce.toString('utf8'));
    const socket = dgram.createSocket('udp4');
    const myMsg = Buffer.from('hello?', 'utf8');

    // send request
    socket.send(myMsg, 0, myMsg.length, url.port, url.host, () => {});
    socket.on('message', msg => {
      console.log('message is', msg);
    });
  }
});
