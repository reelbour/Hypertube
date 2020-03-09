/*jshint esversion: 6 */

// require

const dgram = require('dgram');
const Buffer = require('buffer').Buffer;
const urlParse = require('url').parse;


// exported functions

function getPeers(torrent, callback) {
  const socket = dgram.createSocket('udp4');
  const rawUrl = torrent.announce.toString('utf8');

  const connnReq = buildConnReq();
  udpSend(socket, myMsg, rawUrl);

  socket.on('message', response => {
    if (respType(response) === 'connect') {
      const conResp = parseConnResp(response);
      const announceReq = buildAnnounceReq(connResp.connectionId);
      udpSend(socket, announceReq, rawUrl);
    }
    else if (respType(response) === 'announce') {
      const announceResp = parseAnnounceResp(response);
      callback(announceResp.peers);
    }
  });
}
exports.getPeers = getPeers;


// private functions

function udpSend(socket, message, rawUrl, callback=()=>{}) {
  const url = urlParse(rawUrl);
  socket.send(message, 0, message.length, url.port, url.host, callback);
}

function respType() {}
function buildConnReq() {}
function parseConnResp(resp) {}
function buildAnnounceReq(connId) {}
function parseAnnounceResp(resp) {}
