/*jshint esversion: 6 */

const fs = require('fs');
const bencode = require('bencode');

function open(filePath) {
  const fileContent = fs.readFileSync(filePath);
  const torrent = bencode.decode(fileContent);

  return torrent;
}
exports.open = open;

function size() {

}
exports.size = size;

function infoHash() {

}
exports.infoHash = infoHash;
