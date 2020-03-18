'use strict';

const tracker = require('./tracker');

module.exports = class {
  constructor(peers, torrent) {
    this.list_peers = peers;
    this.ct_peers = [];
    // this.fc_new_peers(torrent);
  }

  fc_new_peers(torrent){
    //let removeDuplicates = newArray => [...new Set(newArray)];
    if (this.ct_peers.length <= 20){
      tracker.getPeers(torrent, peers => {
        this.list_peers = peers;
      })
      return true;
    }
    return false;
  }

  add_ct_peers(val){
    if (!this.ct_peers.find(element => element == val)){
      this.ct_peers.push(val);
      console.log("ok : ", this.ct_peers, "length : ", this.ct_peers.length);
    } else {
      console.log("notok : ", this.ct_peers, "length : ", this.ct_peers.length);
    }
  }

};
