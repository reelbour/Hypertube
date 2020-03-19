'use strict';

const tracker = require('./tracker');

module.exports = class {
  constructor(peers, torrent) {
    this.time = 60000;
    this.list_peers = peers;
    this.ct_peers = [];
    this.ck_peers = [];
    this.unck_peers = [];
    this.new_peers = [];
  }

  fc_new_peers(torrent){
    if (this.unck_peers.length <= 1){
      this.time = 30000;
    } else if (this.unck_peers.length <= 5) {
      this.time = 60000;
    } else {
      this.time += 15000;
    }
    if (this.unck_peers.length <= 25){
      tracker.getPeers(torrent, peers => {
        this.new_peers = [];
        this.add_new_peers(peers);
        this.list_peers = peers;
      })
      return true;
    }
    return false;
  }

  add_new_peers(peers){
    for (let i = 0; i < peers.length; i++) {
      if(!this.list_peers.find(element => element == peers[i])){
        this.new_peers.push(peers[i]);
      }
      if(this.new_peers.length > 100)
        break;
    }
  }

  add_ct_peers(peer){
    if (!this.is_inside_list(peer, this.ct_peers)){
      this.ct_peers.push(peer);
      //console.log("ok : ", this.ct_peers, "length : ", this.ct_peers.length);
    }
  }

  is_inside_list(peer, list){
    if(list.find(element => element == peer)){
      return true;
    }
    return false;
  }

  add_ck_peers(peer){
    if(this.is_inside_list(peer, this.unck_peers)){
      this.unck_peers = this.unck_peers.filter(elem => elem != peer);
    }
    if (!this.is_inside_list(peer, this.ck_peers))
      this.ck_peers.push(peer);
  }

  add_unck_peers(peer){
    if(this.is_inside_list(peer, this.ck_peers)){
      this.ck_peers = this.ck_peers.filter(elem => elem != peer);
    }
    if (!this.is_inside_list(peer, this.unck_peers))
      this.unck_peers.push(peer);
  }

};
