/*jshint esversion: 6 */

const fs = require('fs');
const bencode = require('bencode');
const crypto = require('crypto');
const bignum = require('bignum');

function open(filePath) {
  const fileContent = fs.readFileSync(filePath);
  const torrent = bencode.decode(fileContent);

  return torrent;
}
exports.open = open;

function size(torrent) {
  if (torrent.info.hasOwnProperty('files')) {
    const size = torrent.info.files.map(file => file.length).reduce((a, b) => a + b);
  } else {
    const size = torrent.info.length;
  }

  return bignum.toBuffer(size, {size: 8});
}
exports.size = size;

function infoHash(torrent) {
  const info = bencode.encode(torrent.info);
  return crypto.createHash('sha1').update(info).digest();
}
exports.infoHash = infoHash;
