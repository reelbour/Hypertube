/*jshint esversion: 6 */

const fs = require('fs');
const net = require('net');
const Buffer = require('buffer').Buffer;
const tracker = require('./tracker');
const message = require('./message');
const Pieces = require('./Pieces');
const Queue = require('./Queue');
const EventEmitter = require('events');


function download(peers, torrent, pieces, files) {
  if (peers.p.length <= 0) { return; }
  var peer = peers.p.pop();
  console.log('start download with peer: ', peer);

  function keepGoing() {download(peers, torrent, pieces, files);}

  const socket = new net.Socket();
  socket.on('error', err => {
    console.log(err);
    keepGoing();
  });

  socket.connect(peer.port, peer.ip, () => {
    console.log("Connected !", peer);
    socket.write(message.buildHandshake(torrent));
  });
  const queue = new Queue(torrent);
  onWholeMsg(socket, msg => msgHandler(msg, socket, pieces, queue, torrent, files, keepGoing));
}
exports.download = download;


// private functions

function onWholeMsg(socket, callback) {
  let savedBuf = Buffer.alloc(0);
  let handshake = true;

  socket.on('data', recvBuf => {
    // msgLen calculates the length of a whole message
    const msgLen = () => handshake ? savedBuf.readUInt8(0) + 49 : savedBuf.readInt32BE(0) + 4;
    savedBuf = Buffer.concat([savedBuf, recvBuf]);

    while (savedBuf.length >= 4 && savedBuf.length >= msgLen()) {
      callback(savedBuf.slice(0, msgLen()));
      savedBuf = savedBuf.slice(msgLen());
      handshake = false;
    }
  });
}

function msgHandler(msg, socket, pieces, queue, torrent, files, keepGoing) {
  if (isHandshake(msg)) {
    console.log('isHandshake!');
    socket.write(message.buildInterested());
  } else {
    const m = message.parse(msg);
    console.log('msgHandler!', m.id);

    if (m.id === 0 || m.id === 84) chokeHandler(socket, keepGoing);
    // if (m.id === null) chokeHandler(socket);
    if (m.id === 1) unchokeHandler(socket, pieces, queue);
    if (m.id === 4) haveHandler(socket, pieces, queue, m.payload);
    if (m.id === 5) bitfieldHandler(socket, pieces, queue, m.payload);
    if (m.id === 7) pieceHandler(socket, pieces, queue, torrent, files, m.payload);

  }
}

function isHandshake(msg) {
  return msg.length === msg.readUInt8(0) + 49 &&
         msg.toString('utf8', 1) === 'BitTorrent protocol';
}

function chokeHandler(socket, keepGoing) {
  console.log('chokeHandler!');
  socket.end();
  keepGoing();
}


function unchokeHandler(socket, pieces, queue) {
  queue.choked = false;
  console.log('unchokeHandler!');

  requestPiece(socket, pieces, queue);
}

function haveHandler(socket, pieces, queue, payload) {
  const pieceIndex = payload.readUInt32BE(0);
  console.log('queue.length: ', queue.length());
  const queueEmpty = queue.length() === 0;
  queue.queue(pieceIndex);
  console.log('haveHandler!');
  if (queueEmpty) requestPiece(socket, pieces, queue);
}

function bitfieldHandler(socket, pieces, queue, payload) {
  console.log('queue.length: ', queue.length());
  const queueEmpty = queue.length() === 0;
  payload.forEach((byte, i) => {
    for (let j = 0; j < 8; j++) {
      if (byte % 2) queue.queue(i * 8 + 7 - j);
      byte = Math.floor(byte / 2);
    }
  });
  console.log('bitfieldHandler!');
  if (queueEmpty) requestPiece(socket, pieces, queue);
}

function pieceHandler(socket, pieces, queue, torrent, files, pieceResp) {
  console.log(pieceResp);
  pieces.printPercentDone();

  pieces.addReceived(pieceResp);

  const start = pieceResp.index * torrent.info['piece length'] + pieceResp.begin;
  const end = start + pieceResp.block.length;

  var countoffset = 0;
  files.forEach((file, index) => {
    var writeLength;
    var offset;
    if (start >= countoffset && start < countoffset + file.length &&
        end >= countoffset && end < countoffset + file.length) {
      writeLength = pieceResp.block.length;
      fs.write(file.fs, pieceResp.block, 0, writeLength, start - countoffset, () => {});
    }
    else if (start >= countoffset && start < countoffset + file.length) {
      writeLength = countoffset + file.length - start;
      fs.write(file.fs, pieceResp.block, 0, writeLength, start - countoffset, () => {});
    }
    else if (end >= countoffset && end < countoffset + file.length) {
      writeLength = end - countoffset;
      offset = countoffset - start;
      fs.write(file.fs, pieceResp.block, offset, writeLength, start - countoffset, () => {});
    }
    else if (start < countoffset && end >= countoffset + file.length) {
      writeLength = file.length;
      offset = countoffset - start;
      fs.write(file.fs, pieceResp.block, offset, writeLength, start - countoffset, () => {});
    }
    countoffset += file.length;
  });

  if (pieces.isDone()) {
    console.log('DONE!');
    socket.end();
    try { fs.closeSync(file); } catch(e) {}
  } else {
    requestPiece(socket,pieces, queue);
  }
}

function requestPiece(socket, pieces, queue) {
  if (queue.choked) return null;

  while (queue.length()) {
    const pieceBlock = queue.deque();
    if (pieces.needed(pieceBlock)) {
      socket.write(message.buildRequest(pieceBlock));
      console.log('requestPiece!');
      pieces.addRequested(pieceBlock);
      break;
    }
  }
}
