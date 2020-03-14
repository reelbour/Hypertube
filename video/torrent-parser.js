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

module.exports.BLOCK_LEN = Math.pow(2, 14);

function piecelen (torrent, pieceIndex){
  const totalLength = bignum.fromBuffer(this.size(torrent)).toNumber();
  const pieceLength = torrent.info['piece length'];

  const lastPieceLength = totalLength % pieceLength;
  const lastPieceIndex = Math.floor(totalLength / pieceLength);

  return lastPieceIndex === pieceIndex ? lastPieceLength : pieceLength;
}
exports.piecelen = piecelen;

function blocksPerPiece (torrent, pieceIndex){
  const pieceLength = this.pieceLen(torrent, pieceIndex);
  return Math.ceil(pieceLength / this.BLOCK_LEN);
}
exports.blocksPerPiece =blocksPerPiece;

function blocklen (torrent, pieceIndex, blockIndex){
  const pieceLength = this.pieceLen(torrent, pieceIndex);

  const lastPieceLength = pieceLength % this.BLOCK_LEN;
  const lastPieceIndex = Math.floor(pieceLength / this.BLOCK_LEN);

  return blockIndex === lastPieceIndex ? lastPieceLength : this.BLOCK_LEN;
}
exports.blocklen = blocklen;
