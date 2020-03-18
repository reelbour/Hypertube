'use strict';

const download = require('./src/download');
const torrentParser = require('./src/torrent-parser');

const torrent = torrentParser.open(process.argv[2]);

var path = './data/';
const files = torrentParser.files(torrent, path);
console.log('list of files: ', files);

download(torrent, torrent.info.name, files);
