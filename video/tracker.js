/*jshint esversion: 6 */

//ni: server will crash on any invalid torrent file or invalid server response!
//    need to be a bit more resiliant

// require

const dgram = require('dgram');
const Buffer = require('buffer').Buffer;
const urlParse = require('url').parse;
const crypto = require('crypto');
const torrentParser = require('./torrent-parser');
const EventEmitter = require('events');
const staticVal = require('./staticVal');


// exported functions

function getPeers(torrent, callback) {
  // form rawUrlList
  var rawUrlList;
  // if (torrent.hasOwnProperty('announce-list')) {
  //   rawUrlList = torrent['announce-list'].map(announce => { announce.pop().toString('utf8'); });
  // }
  // else {
  //   rawUrlList = [ torrent.announce.toString('utf8') ];
  // }
  rawUrlList = [ "udp://tracker.coppersurfer.tk:6969" ];
  console.log('rawUrlList: ', rawUrlList);

  const trackerEmitter = new TrackerEmitter();
  trackerInteraction(torrent, rawUrlList, trackerEmitter, callback);
  trackerEmitter.on('error', err => {
    console.log('error: ', err);
  });
}
exports.getPeers = getPeers;


// private functions

class TrackerEmitter extends EventEmitter {}

function trackerInteraction(torrent, rawUrlList, trackerEmitter, callback, timeout=5000) {
  if (rawUrlList.length == 0) {
    trackerEmitter.emit('error', 'No valid tracker');
    return;
  }

  const socket = dgram.createSocket('udp4');
  const rawUrl = rawUrlList.pop();
  console.log('rawUrlList after pop: ', rawUrlList);

  var tiot = setTimeout(() => {
    console.log('timeout!');
    socket.close();
    trackerInteraction(torrent, rawUrlList, trackerEmitter, callback);
  }, timeout);

  console.log('connReq...');
  const connReq = buildConnReq();
  console.log('send...');
  udpSend(socket, connReq, rawUrl, () => {
    console.log('sent to: ', rawUrl);
    // console.log('- socket: ', socket);
    // console.log('- connReq: ', connReq);
    // console.log('- rawUrl: ', rawUrl);
  });

  socket.on('message', response => {
    if (respType(response) === 'connect') {
      console.log('connResp...');
      const connResp = parseConnResp(response);
      console.log('announceReq...');
      const announceReq = buildAnnounceReq(connResp.connectionId, torrent, socket.address().port);
      console.log('send...');
      udpSend(socket, announceReq, rawUrl, () => { console.log('sent: '); });
    }
    else if (respType(response) === 'announce') {
      console.log('announceResp...');
      const announceResp = parseAnnounceResp(response);
      console.log('end...');
      clearTimeout(tiot);
      socket.close();
      callback(announceResp.peers);
    }
  });

  socket.on('listening', () => {
    console.log('listening...');
  });
  socket.on('close', () => {
    console.log('close');
  });
  socket.on('error', err => {
    console.log('error: ', err);
  });
}

function udpSend(socket, message, rawUrl, callback=()=>{}) {
  const url = urlParse(rawUrl);
  socket.send(message, 0, message.length, url.port, url.hostname, callback);
}

function respType(resp) {
  const action = resp.readUInt32BE(0);
  if (action === 0) return 'connect';
  if (action === 1) return 'announce';
}

function buildConnReq() {
  const buf =  Buffer.allocUnsafe(16);

  // setting the first 8 bytes to 0x41727101980 (= connection_id)
  buf.writeUInt32BE(0x417, 0);
  buf.writeUInt32BE(0x27101980, 4);

  // setting the next 4 bytes to 0 (= action)
  buf.writeUInt32BE(0, 8);

  // writing a random number in the last 4 bytes (= transaction_id)
  crypto.randomBytes(4).copy(buf, 12);
  //ni: keep track of transaction_id's in case multiple transactions are ongoing simultaneously.

  return buf;
}

function parseConnResp(resp) {
  return {
    // read first 4 bytes to get action
    action: resp.readUInt32BE(0),
    // read next 4 bytes to get transaction_id
    transactionId: resp.readUInt32BE(4),
    // read last 8 bytes to get connection_id
    connectionId: resp.slice(8)
  };
}

function buildAnnounceReq(connId, torrent, port=6881) {//ni: port for torrent
  const buf = Buffer.allocUnsafe(98);

  // connection id
  connId.copy(buf, 0);
  // action
  buf.writeUInt32BE(1, 8);
  // transaction id
  crypto.randomBytes(4).copy(buf, 12);
  // info hash
  torrentParser.infoHash(torrent).copy(buf, 16);
  // peerId
  staticVal.genId().copy(buf, 36);
  // downloaded
  Buffer.alloc(8).copy(buf, 56);
  // left
  torrentParser.size(torrent).copy(buf, 64);
  // uploaded
  Buffer.alloc(8).copy(buf, 72);
  // event
  buf.writeUInt32BE(0, 80);
  // ip address
  buf.writeUInt32BE(0, 80);
  // key
  crypto.randomBytes(4).copy(buf, 88);
  // num want
  buf.writeInt32BE(-1, 92);
  // port
  buf.writeUInt16BE(port, 96);

  return buf;
}

function parseAnnounceResp(resp) {
  function group(iterable, groupSize) {
    let groups = [];
    for (let i = 0; i < iterable.length; i += groupSize) {
      groups.push(iterable.slice(i, i + groupSize));
    }
    return groups;
  }

  return {
    action: resp.readUInt32BE(0),
    transactionId: resp.readUInt32BE(4),
    leechers: resp.readUInt32BE(8),
    seeders: resp.readUInt32BE(12),
    peers:
      group(resp.slice(20), 6).map(address => {
        return  { ip: address.slice(0, 4).join('.'),
                  port: address.readUInt16BE(4)
                };
      })
  };
}
