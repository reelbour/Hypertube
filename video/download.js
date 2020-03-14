/*jshint esversion: 6 */

const net = require('net');
const Buffer = require('buffer').Buffer;
const tracker = require('./tracker');
const message = require('./message');
const pieces = require('./pieces');


function download(peer, torrent, pieces) {
  const socket = new net.Socket();
  socket.on('error', console.log);

  socket.connect(peer.port, peer.ip, () => {
    console.log("Connected !", peer);
    socket.write(message.buildHandshake(torrent));
  });
  const queue = {choked: true, queue: []};
  onWholeMsg(socket, msg => msgHandler(msg, socket, requested, queue));
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

function msgHandler(msg, socket, requested, queue) {
  if (isHandshake(msg)) {
    socket.write(message.buildInterested());
  } else {
    const m = message.parse(msg);

    // if (m.id === 0) chokeHandler(socket);
    // if (m.id === 1) unchokeHandler(socket, pieces, queue);
    // if (m.id === 4) haveHandler(m.payload, socket, requested, queue);
    // if (m.id === 5) bitfieldHandler(m.payload);
    // if (m.id === 7) pieceHandler(m.payload, socket, requested, queue);
  }
}

function isHandshake(msg) {
  return msg.length === msg.readUInt8(0) + 49 &&
         msg.toString('utf8', 1) === 'BitTorrent protocol';
}


function haveHandler(payload, socket, requested, queue) {
   // ...
  const pieceIndex = payload.readUInt32BE(0);
  queue.push(pieceIndex);
  if (queue.length === 1) {
    requestPiece(socket, requested, queue);
  }
}

function pieceHandler(payload, socket, requested, queue) {
  // ...
  queue.shift();
  requestPiece(socket, requested, queue);
}

function requestPiece(socket, pieces, queue) {
  if (queue.choked) return null;

  while (queue.queue.length) {
    const pieceIndex = queue.shift();
    if (pieces.needed(pieceIndex)) {
      // need to fix this
      socket.write(message.buildRequest(pieceIndex));
      pieces.addRequested(pieceIndex);
      break;
    }
  }
}
function chokeHandler(socket) {
  socket.end();
}


function unchokeHandler(socket, pieces, queue) {
  queue.choked = false;

  requestPiece(socket, pieces, queue);
}
